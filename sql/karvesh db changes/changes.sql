ALTER TABLE `dbsolis`.`tblspecial_offer` 
ADD COLUMN `adult_max_category` VARCHAR(45) NULL AFTER `adult_max`,
ADD COLUMN `children_max_category` VARCHAR(45) NULL AFTER `children_max`,
CHANGE COLUMN `family_offer_adult_min` `adult_min` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `family_offer_adult_max` `adult_max` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `family_offer_children_min` `children_min` INT(11) NULL DEFAULT NULL ,
CHANGE COLUMN `family_offer_children_max` `children_max` INT(11) NULL DEFAULT NULL ;
