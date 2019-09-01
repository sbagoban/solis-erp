
CREATE TABLE `dbsolis`.`tblexcursion_services_rates_insertclosedate` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL DEFAULT NULL,
    `serviceclosedstartdate` DATE NULL DEFAULT NULL,
    `serviceclosedenddate` DATE NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_quotedetails_3_idx` (`idservicesfk` ASC),
    CONSTRAINT `fk_tblexcursion_services_quotedetails_3`
    FOREIGN KEY (`idservicesfk`)
    REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION);
