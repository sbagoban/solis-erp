CREATE TABLE `dbsolis`.`tblexcursion_services_rates_to` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL,
    `idrates_fk` INT NULL,
    `to_id` INT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_4_idx` (`idservicesfk` ASC),
    INDEX `fk_tblexcursion_services_rates_insertrates_3_idx` (`idrates_fk` ASC),
    CONSTRAINT `fk_tblexcursion_services_rates_insertrates_3`
        FOREIGN KEY (`idrates_fk`)
        REFERENCES `dbsolis`.`tblexcursion_services_rates_insertrates` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_tblexcursion_services_4`
        FOREIGN KEY (`idservicesfk`)
        REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION);