ALTER TABLE `dbsolis`.`tblhotels` 
ADD COLUMN `id_transfer_coast` INT NULL AFTER `company_name`,
ADD INDEX `fk_tblhotels_8_idx` (`id_transfer_coast` ASC);
;
ALTER TABLE `dbsolis`.`tblhotels` 
ADD CONSTRAINT `fk_tblhotels_8`
  FOREIGN KEY (`id_transfer_coast`)
  REFERENCES `dbsolis`.`tblcoasts` (`id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;
