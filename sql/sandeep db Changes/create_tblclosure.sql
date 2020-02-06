CREATE TABLE `product_service_closure_date` (
  `id_product_service_closure_date` int(11) NOT NULL,
  `id_product_service` int(11) NOT NULL COMMENT 'id_product_service from product_service',
  `closure_date` date NOT NULL COMMENT 'date of closure',
  `closure_date_description` varchar(255) NOT NULL,
  `active` smallint(1) NOT NULL DEFAULT 1 COMMENT '1 for active closure date no/ 0 for deleted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;