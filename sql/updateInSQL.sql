DROP TABLE IF EXISTS `id_creditor_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `creditor_log` (
  `id_creditor_log` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto increment field',
  `id_creditor` int(11) NOT NULL COMMENT 'id_creditor from creditor',
  `code` varchar(25) NOT NULL DEFAULT 'NONE' COMMENT 'creditor code from tour plan',
  `creditor_name` varchar(250) NOT NULL DEFAULT 'NONE' COMMENT 'creditor name',
  `contact_person` varchar(100) DEFAULT NULL COMMENT 'contact name of supplier',
  `email` varchar(100) DEFAULT NULL COMMENT 'email address of supplier',
  `tel_no` varchar(100) DEFAULT NULL COMMENT 'telephone of supplier',
  `mobile_no` varchar(100) DEFAULT NULL COMMENT 'mobile no of supplier',
  `fax_no` varchar(100) DEFAULT NULL COMMENT 'fax no of supplier',
  `website` varchar(100) DEFAULT NULL COMMENT 'website',
  `address_1` varchar(100) DEFAULT NULL COMMENT 'Coporate building name/street address',
  `address_2` varchar(100) DEFAULT NULL COMMENT 'street address',
  `locality` varchar(100) DEFAULT NULL COMMENT 'locality address',
  `city` varchar(100) DEFAULT NULL COMMENT 'city address',
  `id_country` int(11) NOT NULL DEFAULT '0' COMMENT 'id from tblcountries',
  `country_name` varchar(100) DEFAULT NULL COMMENT 'country name',
  `vat_reg_no` varchar(30) NOT NULL DEFAULT 'NONE' COMMENT 'if yes, the vat registration no',
  `vat_flag` char(1) NOT NULL DEFAULT 'Y' COMMENT 'if the creditor is taxable or not (Y/N)',
  `business_reg_no` varchar(30) NOT NULL DEFAULT 'NONE' COMMENT 'Business registration no',
  `remarks` varchar(200) NOT NULL DEFAULT 'NONE',
  `id_user` int(11) NOT NULL COMMENT 'id from tbluser',
  `uname` varchar(45) NOT NULL DEFAULT 'NONE',
  `log_date` datetime NOT NULL DEFAULT '9999-12-31 00:00:01',
  `log_status` varchar(15) NOT NULL COMMENT 'CREATE, UPDATE, DELETE',
  PRIMARY KEY (`id_creditor_log`),
  KEY `id_creditorfk` (`id_creditor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UNLOCK TABLES;
