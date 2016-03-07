<?php
/**
 * eCommerce Watch
 *
 * Copyright (c) 2016 Atelier Disko - All rights reserved.
 *
 * Licensed under the AD General Software License v1.
 *
 * This software is proprietary and confidential. Redistribution
 * not permitted. Unless required by applicable law or agreed to
 * in writing, software distributed on an "AS IS" BASIS, WITHOUT-
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *
 * You should have received a copy of the AD General Software
 * License. If not, see http://atelierdisko.de/licenses.
 */

use lithium\g11n\Message;
use base_core\extensions\cms\Widgets;
use ecommerce_watch\models\Watches;

extract(Message::aliases());

Widgets::register('watches', function() use ($t) {
	$total = Watches::find('count');
	$users = Watches::find('count', [
		'fields' => [
			'DISTINCT(user_id)'
		]
	]);

	return [
		'title' => $t('Watches', ['scope' => 'ecommerce_watch']),
		'url' => [
			'controller' => 'Watches', 'action' => 'index', 'library' => 'ecommerce_watches'
		],
		'data' => [
			$t('Total', ['scope' => 'ecommerce_watch']) => $total,
			$t('Ø per user', ['scope' => 'ecommerce_watch']) => round($total / $users, 0)
		]
	];
}, [
	'type' => Widgets::TYPE_COUNTER,
	'group' => Widgets::GROUP_DASHBOARD,
]);

?>