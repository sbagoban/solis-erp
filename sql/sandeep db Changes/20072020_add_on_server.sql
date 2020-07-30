CREATE TABLE `product_service_paxbreak` (
  `id_product_service_pax_break_claim` int(11) NOT NULL AUTO_INCREMENT,
  `id_product_service_claim` int(11) NOT NULL,
  `id_product_service_cost` int(11) NOT NULL COMMENT 'id_product_service_cost from product_service_cost',
  `id_product_service` int(11) NOT NULL COMMENT 'id_product_service from product_service',
  `pax_from` int(11) NOT NULL,
  `pax_to` int(11) NOT NULL,
  `charge` varchar(30) NOT NULL DEFAULT 'PAX',
  `ps_adult_claim_break` decimal(15,2) DEFAULT NULL,
  `ps_teen_claim_break` decimal(15,2) DEFAULT NULL,
  `ps_child_claim_break` decimal(15,2) DEFAULT NULL,
  `ps_infant_claim_break` decimal(15,2) DEFAULT NULL,
  `ps_infant_claim_rollover` varchar(45) NOT NULL DEFAULT '0',
  `ps_child_claim_rollover` varchar(45) NOT NULL DEFAULT '0',
  `ps_teen_claim_rollover` varchar(45) NOT NULL DEFAULT '0',
  `ps_adult_claim_rollover` varchar(45) NOT NULL DEFAULT '0',
  `rollover_value` varchar(45) NOT NULL DEFAULT '0',
  `rollover_type` varchar(45) NOT NULL DEFAULT '0',
  `active` smallint(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id_product_service_pax_break_claim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `product_service_paxbreak_cost` (
  `id_product_service_pax_break_cost` int(11) NOT NULL AUTO_INCREMENT,
  `id_product_service_claim` int(11) NOT NULL,
  `id_product_service_cost` int(11) NOT NULL,
  `id_product_service` int(11) NOT NULL COMMENT 'id_product_service from product_service',
  `pax_from` int(11) NOT NULL,
  `pax_to` int(11) NOT NULL,
  `charge` varchar(30) NOT NULL DEFAULT 'PAX',
  `ps_adult_cost_break` decimal(15,2) DEFAULT NULL,
  `ps_teen_cost_break` decimal(15,2) DEFAULT NULL,
  `ps_child_cost_break` decimal(15,2) DEFAULT NULL,
  `ps_infant_cost_break` decimal(15,2) DEFAULT NULL,
  `active` smallint(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id_product_service_pax_break_cost`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;