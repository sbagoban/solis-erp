CREATE TABLE `dbsolis`.`tblexcursion_services` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `countryfk` INT NULL DEFAULT NULL,
    `servicetypefk` INT NULL DEFAULT NULL,
    `supplierfk` INT NULL DEFAULT NULL,
    `optioncode` VARCHAR(255) NULL DEFAULT NULL,
    `descriptionservice` VARCHAR(255) NULL DEFAULT NULL,
    `comments` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `fk_tblexcursion_services_1_idx` (`countryfk` ASC),
    INDEX `fk_tblexcursion_services_2_idx` (`servicetypefk` ASC),
    INDEX `fk_tblexcursion_services_3_idx` (`supplierfk` ASC),
    INDEX `index5` (`optioncode` ASC),
    CONSTRAINT `fk_tblexcursion_services_1`
    FOREIGN KEY (`countryfk`)
    REFERENCES `dbsolis`.`tblcountries` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
    CONSTRAINT `fk_tblexcursion_services_2`
    FOREIGN KEY (`servicetypefk`)
    REFERENCES `dbsolis`.`tblservicetype` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION,
    CONSTRAINT `fk_tblexcursion_services_3`
    FOREIGN KEY (`supplierfk`)
    REFERENCES `dbsolis`.`tblsuppliesexcursions` (`id`)
    ON DELETE RESTRICT
    ON UPDATE NO ACTION);
