CREATE TABLE `ecommerce_watches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `ecommerce_product_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poly` (`ecommerce_product_id`),
  KEY `media_file` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
