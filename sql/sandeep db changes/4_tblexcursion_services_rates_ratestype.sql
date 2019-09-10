CREATE TABLE `dbsolis`.`tblexcursion_services_rates_ratestype` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL,
    `idrates_fk` INT NULL,
    `ratestype_id` INT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_5_idx` (`idservicesfk` ASC),
    INDEX `fk_tblexcursion_services_rates_insertrates_4_idx` (`idrates_fk` ASC),
    CONSTRAINT `fk_tblexcursion_services_rates_insertrates_4`
        FOREIGN KEY (`idrates_fk`)
        REFERENCES `dbsolis`.`tblexcursion_services_rates_insertrates` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_tblexcursion_services_5`
        FOREIGN KEY (`idservicesfk`)
        REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION);