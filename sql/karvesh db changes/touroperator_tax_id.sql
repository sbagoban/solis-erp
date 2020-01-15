ALTER TABLE `dbsolis`.`tbltouroperator` 
ADD COLUMN `id_vat` INT NULL AFTER `iata_code`,
ADD INDEX `fk_tbltouroperator_5_idx` (`id_vat` ASC);

ALTER TABLE `dbsolis`.`tbltouroperator` 
ADD CONSTRAINT `fk_tbltouroperator_5`
  FOREIGN KEY (`id_vat`)
  REFERENCES `dbsolis`.`tbltaxcodes` (`id`)
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;
