<?php
/**
 * eCommerce Watch
 *
 * Copyright (c) 2014 Atelier Disko - All rights reserved.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 */

use base_core\extensions\cms\Jobs;
use base_core\models\Users;
use ecommerce_watch\models\Watchers;
use lithium\util\Set;

Jobs::recur('ecommerce_watch:notify', function() {

	// 1. Check which products need notifications.
	// Assumes the set of watched product id will
	// be always less then the total number of products.
	//
	// FIXME Set doesn't work with missing zero keys.
	$nowInStock = Watchers::find('all', [
		'fields' => ['ecommerce_product_id'],
		'group' => ['ecommerce_product_id']
	])->find(function($item) {
		return $item->product()->stock() > 0;
	});
	$nowInStock = Set::extract(array_values($nowInStock->data(), '/ecommerce_product_id'));

	// 2. Get all watchers
	$watchers = Watchers::find('all', [
		'conditions' => [
			'ecommerce_product_id' => $nowInStock
		],
		'order' => ['user_id'] // Preorder for perfance reasons.
	]);

	// 3. Key watchers by user.
	$users = [];
	foreach ($watchers as $watcher) {
		$user = $watcher->user();

		if (!isset($users[$user->id])) {
			$user->watchers = [];
			$users[$user->id] = $user;
 		}
		$users[$user->id]->watchers[] = $watcher;
	}

	// 4. Now mail each user list of watched products.
	foreach ($users as $user) {
		if (!$user->is_notified) {
			continue;
		}
		Watchers::pdo()->beginTransaction();

		$products = [];
		foreach ($user->watchers as $watcher) {
			$products[] = $watcher->product();
		}
		$result = Mailer::deliver('watch', [
			'to' => $user->email,
			'subject' => $t('Updates for watched products.'),
			'data' => [
				'user' => $user,
				'products' => $products
			]
		]);

		// Prevent renotifications.
		foreach ($user->watchers as $watcher) {
			$result = $result && $watcher->delete();
		}
		if (!$result) {
			Watchers::pdo()->rollback();
			return false;
		}
		Watchers::pdo()->commit();
	}
}, [
	'frequency' => Jobs::FREQUENCY_LOW
]);

?>