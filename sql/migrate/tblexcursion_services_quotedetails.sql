CREATE TABLE `dbsolis`.`tblexcursion_services_quotedetails` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `idservicesfk` INT NULL DEFAULT NULL,
    `extraname` VARCHAR(255) NULL DEFAULT NULL,
    `extradescription` VARCHAR(255) NULL DEFAULT NULL,
    `chargeper` VARCHAR(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id`))