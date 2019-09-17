ALTER TABLE `dbsolis`.`tblservice_contract_roomcapacity_dates` 
ADD COLUMN `season_fk` INT NULL AFTER `service_contract_roomcapacity_fk`,
ADD INDEX `fk_tblservice_contract_roomcapacity_dates_2_idx` (`season_fk` ASC);
;
ALTER TABLE `dbsolis`.`tblservice_contract_roomcapacity_dates` 
ADD CONSTRAINT `fk_tblservice_contract_roomcapacity_dates_2`
  FOREIGN KEY (`season_fk`)
  REFERENCES `dbsolis`.`tblseasons` (`id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;
