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

namespace ecommerce_watch\models;

use ecommerce_core\models\Products;

class Watchers extends \base_core\models\Base {

	use \base_core\models\UserTrait;

	protected $_meta = [
		'source' => 'ecommerce_watchers'
	];

	protected static $_actsAs = [
		'base_core\extensions\data\behavior\Timestamp'
	];

	public function product($entity) {
		return Products::find('first', [
			'conditions' => [
				'id' => $entity->ecommerce_product_id
			]
		]);
	}
}

?>