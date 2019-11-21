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

namespace ecommerce_watch\models;

use ecommerce_core\models\Products;

class Watches extends \base_core\models\Base {

	protected $_meta = [
		'source' => 'ecommerce_watches'
	];

	public $belongsTo = [
		'User' => [
			'to' => 'base_core\models\Users',
			'key' => 'user_id'
		],
		'Product' => [
			'to' => 'ecommerce_core\models\Products',
			'key' => 'ecommerce_product_id'
		]
	];

	protected $_actsAs = [
		'base_core\extensions\data\behavior\RelationsPlus',
		'base_core\extensions\data\behavior\Timestamp',
		'base_core\extensions\data\behavior\Searchable' => [
			'fields' => [
				'Product.title',
				'User.number',
				'modified'
			]
		]
	];
}

?>