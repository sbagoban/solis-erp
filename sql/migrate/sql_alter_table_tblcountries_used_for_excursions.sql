ALTER TABLE `dbsolis`.`tblcountries` 
ADD COLUMN `used_for_excursions` INT NOT NULL DEFAULT 0 AFTER `used_for_hotels`,
ADD INDEX `index8` (`used_for_excursions` ASC);