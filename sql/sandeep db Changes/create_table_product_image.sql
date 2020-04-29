CREATE TABLE `product_service_images` (
  `id_product_service_images` int(11) NOT NULL,
  `id_product_service` int(11) NOT NULL COMMENT 'id_product_service from product_service',
  `product_service_images_path` varchar(255) NOT NULL,
  `product_service_images_datetime` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `active` smallint(1) NOT NULL DEFAULT 1 COMMENT '1 for active closure date no/ 0 for deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;