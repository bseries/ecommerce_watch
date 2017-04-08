<?php
/**
 * eCommerce Watch
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * Licensed under the AD General Software License v1.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * You should have received a copy of the AD General Software
 * License. If not, see https://atelierdisko.de/licenses.
 */

use base_core\async\Jobs;
use base_core\models\Users;
use ecommerce_watch\models\Watches;
use lithium\util\Set;
use lithium\analysis\Logger;
use li3_mailer\action\Mailer;
use lithium\g11n\Message;

Jobs::recur('ecommerce_watch:notify', function() {
	extract(Message::aliases());

	// 1. Check which products need notifications.
	// Assumes the set of watched product id will
	// be always less then the total number of products.
	//
	// FIXME Set doesn't work with missing zero keys.
	$nowInStock = Watches::find('all', [
		'fields' => ['ecommerce_product_id'],
		'group' => ['ecommerce_product_id'],
		'with' => ['Product'],
		'conditions' => [
			'Product.is_published' => true,
			'(Product.stock - Product.stock_reserved)' => ['>' => 0]
		]
	]);
	if (!$nowInStock->count()) {
		Logger::debug('No products again in stock; skipping.');
		return true;
	}
	$nowInStock = Set::extract(array_values($nowInStock->data()), '/ecommerce_product_id');

	// 2. Get all watches
	$watches = Watches::find('all', [
		'conditions' => [
			'ecommerce_product_id' => $nowInStock
		],
		'order' => ['user_id'] // Preorder for perfance reasons.
	]);

	// 3. Key watches by user.
	$users = [];
	foreach ($watches as $watch) {
		$user = $watch->user();

		if (!$user) {
			Logger::debug("User on watch has gone away; removing watch.");
			$watch->delete();
			continue;
		}

		if (!$user->is_active) {
			Logger::debug("User on watch is not active; removing watch.");
			$watch->delete();
			continue;
		}

		if (!isset($users[$user->id])) {
			$user->watches = [];
			$users[$user->id] = $user;
 		}
		$users[$user->id]->watches[] = $watch;
	}

	// 4. Now mail each user list of watched products.
	foreach ($users as $user) {
		if (!$user->is_notified) {
			Logger::write('debug', "Skipping user {$user->id}, is not notified.");
			continue;
		}
		Watches::pdo()->beginTransaction();

		$products = [];
		foreach ($user->watches as $watch) {
			$products[] = $watch->product();
		}
		$result = Mailer::deliver('watch_update', [
			'library' => 'ecommerce_watch',
			'to' => $user->email,
			'subject' => $t('Updates for watched products.', ['scope' => 'ecommerce_watch']),
			'data' => [
				'user' => $user,
				'products' => $products
			]
		]);

		// Prevent renotifications.
		foreach ($user->watches as $watch) {
			$result = $result && $watch->delete();
		}
		if (!$result) {
			Logger::write('notice', 'Failed notify about watches for user: ' . $user->id);

			Watches::pdo()->rollback();
			return false;
		}
		Logger::write('debug', "Notified user {$user->id} about watches.");
		Watches::pdo()->commit();
		return true;
	}
}, [
	'frequency' => Jobs::FREQUENCY_LOW
]);

?>