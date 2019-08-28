
CREATE TABLE `dbsolis`.`tblexcursion_services_quotedetails_paxbreaks` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL DEFAULT NULL,
    `paxfrom` INT NULL DEFAULT NULL,
    `paxto` INT NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_quotedetails_1_idx` (`idservicesfk` ASC),
    CONSTRAINT `fk_tblexcursion_services_quotedetails_1`
    FOREIGN KEY (`idservicesfk`)
    REFERENCES `dbsolis`.`tblexcursion_services` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION);
