DROP TABLE `dbsolis`.`tblinventory_dates_countries`;
DROP TABLE `dbsolis`.`tblinventory_dates_to`;


ALTER TABLE `dbsolis`.`tblinventory_dates` 
ADD COLUMN `country_fk` INT NULL AFTER `to_fk`,
ADD INDEX `fk_tblinventory_dates_2_idx` (`country_fk` ASC);
;
ALTER TABLE `dbsolis`.`tblinventory_dates` 
ADD CONSTRAINT `fk_tblinventory_dates_2`
  FOREIGN KEY (`country_fk`)
  REFERENCES `dbsolis`.`tblcountries` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
