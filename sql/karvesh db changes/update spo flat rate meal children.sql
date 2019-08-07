ALTER TABLE `dbsolis`.`tblspecial_offer_flatrate_mealsupp_children_ages` 
CHANGE COLUMN `child_age_from` `child_age_from` INT(11) NOT NULL DEFAULT 0 ,
CHANGE COLUMN `child_age_to` `child_age_to` INT(11) NOT NULL DEFAULT 0 ,
CHANGE COLUMN `child_count` `child_count` INT(11) NOT NULL DEFAULT 0 ;
