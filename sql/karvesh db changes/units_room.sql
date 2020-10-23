ALTER TABLE `dbsolis`.`tblservice_contract_adultpolicy_room_dates_rules` 
ADD COLUMN `ruleageranges` VARCHAR(1000) NULL AFTER `rulecategory`,
ADD INDEX `index4` (`ruleageranges` ASC);
;


ALTER TABLE `dbsolis`.`tblspecial_offer_flatrate_ad_rm_dt_rules` 
ADD COLUMN `ruleageranges` VARCHAR(1000) NULL AFTER `rulecategory`,
ADD INDEX `index5` (`ruleageranges` ASC);
;
