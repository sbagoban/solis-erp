
CREATE TABLE `dbsolis`.`tblexcursion_services_rates_insertrates` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL DEFAULT NULL,
    `servicedatefrom` DATE NULL DEFAULT NULL,
    `servicedateto` DATE NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_quotedetails_2_idx` (`idservicesfk` ASC),
    CONSTRAINT `fk_tblexcursion_services_quotedetails_2`
    FOREIGN KEY (`idservicesfk`)
    REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION);
