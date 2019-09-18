ALTER TABLE `dbsolis`.`tblspecial_offer_validityperiods` 
ADD COLUMN `season_fk` INT NULL AFTER `valid_to`,
ADD INDEX `fk_tblspecial_offer_validityperiods_2_idx` (`season_fk` ASC);
;
ALTER TABLE `dbsolis`.`tblspecial_offer_validityperiods` 
ADD CONSTRAINT `fk_tblspecial_offer_validityperiods_2`
  FOREIGN KEY (`season_fk`)
  REFERENCES `dbsolis`.`tblseasons` (`id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;
