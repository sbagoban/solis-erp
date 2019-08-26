INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Company Types', '13', 'Y', '26', 'fa-object-group', 'company_type', 'Company Types', 'O');

CREATE TABLE `dbsolis`.`tblservice_contract_settings_profile` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `profile_name` VARCHAR(45) NULL,
  `profile_description` VARCHAR(1000) NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `dbsolis`.`tblservice_contract_settings_profile_details` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `profile_fk` INT NULL,
  `buy_sell` VARCHAR(3) NULL,
  `item_fk` INT NULL,
  `rounding` VARCHAR(45) NULL,
  `basis` VARCHAR(45) NULL,
  `formula` VARCHAR(1000) NULL,
  `row_index` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `fk_tblservice_contract_settings_profile_details_1_idx` (`profile_fk` ASC),
  INDEX `fk_tblservice_contract_settings_profile_details_2_idx` (`item_fk` ASC),
  INDEX `idx_buy_sell` (`buy_sell` ASC),
  INDEX `idx_row_index` (`row_index` ASC),
  CONSTRAINT `fk_tblservice_contract_settings_profile_details_1`
    FOREIGN KEY (`profile_fk`)
    REFERENCES `dbsolis`.`tblservice_contract_settings_profile` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblservice_contract_settings_profile_details_2`
    FOREIGN KEY (`item_fk`)
    REFERENCES `dbsolis`.`tbltaxcomm_items` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION);

ALTER TABLE `dbsolis`.`tblservice_contract_settings_profile_details` 
CHANGE COLUMN `buy_sell` `buy_sell` VARCHAR(30) NULL DEFAULT NULL ;

