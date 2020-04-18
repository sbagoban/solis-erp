DROP TABLE `dbsolis`.`tblinventory`;
DROP TABLE `dbsolis`.`tblinventory_countries`;
DROP TABLE `dbsolis`.`tblinventory_dates`;
DROP TABLE `dbsolis`.`tblinventory_rooms`;
DROP TABLE `dbsolis`.`tblinventory_touroperators`;

CREATE TABLE `tblinventory_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_date` date DEFAULT NULL,
  `roomfk` int(11) DEFAULT NULL,
  `hotelfk` int(11) DEFAULT NULL,
  `inventory_status` varchar(45) DEFAULT NULL,
  `release_days_value` int(11) DEFAULT NULL,
  `release_date_value` date DEFAULT NULL,
  `autho_reserve_days_to` int(11) DEFAULT NULL,
  `autho_reserve_date_from` date DEFAULT NULL,
  `autho_reserve_date_to` date DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `autho_reserve_time_from` time DEFAULT NULL,
  `autho_reserve_time_to` time DEFAULT NULL,
  `specific_to` varchar(45) DEFAULT NULL,
  `autho_reserve_days_from` int(11) DEFAULT NULL,
  `to_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index2` (`inventory_status`),
  KEY `index3` (`release_days_value`,`release_date_value`,`autho_reserve_days_to`,`autho_reserve_date_from`,`autho_reserve_date_to`),
  KEY `index4` (`date_created`,`deleted`),
  KEY `fk_tblinventory_dates_1_idx` (`roomfk`),
  KEY `fk_tblinventory_dates_4_idx` (`hotelfk`),
  KEY `index7` (`specific_to`),
  KEY `fk_tblinventory_dates_3_idx` (`to_fk`),
  CONSTRAINT `fk_tblinventory_dates_1` FOREIGN KEY (`roomfk`) REFERENCES `tblhotel_rooms` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_dates_3` FOREIGN KEY (`to_fk`) REFERENCES `tbltouroperator` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_dates_4` FOREIGN KEY (`hotelfk`) REFERENCES `tblhotels` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=latin1;

CREATE TABLE `tblinventory_dates_countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_date_fk` int(11) DEFAULT NULL,
  `country_fk` int(11) DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_tblinventory_dates_countries_1_idx` (`inventory_date_fk`),
  KEY `fk_tblinventory_dates_countries_2_idx` (`country_fk`),
  CONSTRAINT `fk_tblinventory_dates_countries_1` FOREIGN KEY (`inventory_date_fk`) REFERENCES `tblinventory_dates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_dates_countries_2` FOREIGN KEY (`country_fk`) REFERENCES `tblcountries` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tblinventory_dates_to` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_date_fk` int(11) DEFAULT NULL,
  `to_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tblinventory_dates_to_1_idx` (`inventory_date_fk`),
  KEY `fk_tblinventory_dates_to_2_idx` (`to_fk`),
  CONSTRAINT `fk_tblinventory_dates_to_1` FOREIGN KEY (`inventory_date_fk`) REFERENCES `tblinventory_dates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_dates_to_2` FOREIGN KEY (`to_fk`) REFERENCES `tbltouroperator` (`id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tblinventory_allotment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_fk` int(11) DEFAULT NULL,
  `room_fk` int(11) DEFAULT NULL,
  `allotment_date` date DEFAULT NULL,
  `release_type` varchar(45) DEFAULT NULL,
  `specific_no_days` int(11) DEFAULT NULL,
  `specific_date` date DEFAULT NULL,
  `comment` varchar(2000) DEFAULT NULL,
  `units` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `deleted` int(11) NOT NULL DEFAULT '0',
  `to_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `index3` (`units`),
  KEY `index4` (`created_on`),
  KEY `index5` (`deleted`),
  KEY `fk_tblinventory_allotment_1_idx` (`hotel_fk`),
  KEY `fk_tblinventory_allotment_2_idx` (`room_fk`),
  KEY `fk_tblinventory_allotment_3_idx` (`created_by`),
  KEY `index2` (`allotment_date`),
  KEY `fk_tblinventory_allotment_4_idx` (`to_fk`),
  CONSTRAINT `fk_tblinventory_allotment_1` FOREIGN KEY (`hotel_fk`) REFERENCES `tblhotels` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_2` FOREIGN KEY (`room_fk`) REFERENCES `tblhotel_rooms` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_3` FOREIGN KEY (`created_by`) REFERENCES `tbluser` (`id`) ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_4` FOREIGN KEY (`to_fk`) REFERENCES `tbltouroperator` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=latin1;

ALTER TABLE `dbsolis_test`.`tblservice_contract_extrasupplement` 
ADD COLUMN `spo_deductable` INT NOT NULL DEFAULT 0 AFTER `service_contract_roomcapacity_dates_fk`;



