CREATE TABLE `dbsolis`.`tblexcursion_services_rates_countries` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL,
    `idrates_fk` INT NULL,
    `country_id` INT NULL,
    `country_name` VARCHAR(45) NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_rates_insertrates_1_idx` (`idrates_fk` ASC),
    CONSTRAINT `fk_tblexcursion_services_rates_insertrates_1`
        FOREIGN KEY (`idrates_fk`)
        REFERENCES `dbsolis`.`tblexcursion_services_rates_insertrates` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION);