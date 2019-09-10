



-- RUN 1
drop table `dbsolis`.`tblexcursion_services_rates_insertclosedate`






-- Run 2
CREATE TABLE `dbsolis`.`tblexcursion_services_rates_insertclosedate` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL,
    `idrates_fk` INT NULL,
    `serviceclosedstartdate` date,
    `serviceclosedenddate` date,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_6_idx` (`idservicesfk` ASC),
    INDEX `fk_tblexcursion_services_rates_insertrates_4_idx` (`idrates_fk` ASC),
    CONSTRAINT `fk_tblexcursion_services_rates_insertrates_4`
        FOREIGN KEY (`idrates_fk`)
        REFERENCES `dbsolis`.`tblexcursion_services_rates_insertrates` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION,
    CONSTRAINT `fk_tblexcursion_services_6`
        FOREIGN KEY (`idservicesfk`)
        REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
        ON DELETE CASCADE
        ON UPDATE NO ACTION);