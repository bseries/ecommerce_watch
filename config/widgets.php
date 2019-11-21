<?php
/**
 * eCommerce Watch
 *
 * Copyright (c) 2014 David Persson - All rights reserved.
 * Copyright (c) 2016 Atelier Disko - All rights reserved.
 *
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
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