ALTER TABLE `dbsolis`.`tblinventory_allotment` 
DROP FOREIGN KEY `fk_tblinventory_allotment_4`,
DROP FOREIGN KEY `fk_tblinventory_allotment_2`;
ALTER TABLE `dbsolis`.`tblinventory_allotment` 
DROP COLUMN `to_fk`,
DROP COLUMN `allotment_date`,
DROP COLUMN `room_fk`,
ADD COLUMN `date_from` DATE NULL AFTER `deleted`,
ADD COLUMN `date_to` DATE NULL AFTER `date_from`,
DROP INDEX `fk_tblinventory_allotment_4_idx` ,
DROP INDEX `index2` ,
DROP INDEX `fk_tblinventory_allotment_2_idx` ;
;


CREATE TABLE `dbsolis`.`tblinventory_allotment_rooms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `roomfk` INT NULL,
  `allotmentfk` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tblinventory_allotment_rooms_1_idx` (`roomfk` ASC),
  INDEX `fk_tblinventory_allotment_rooms_2_idx` (`allotmentfk` ASC),
  CONSTRAINT `fk_tblinventory_allotment_rooms_1`
    FOREIGN KEY (`roomfk`)
    REFERENCES `dbsolis`.`tblhotel_rooms` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_rooms_2`
    FOREIGN KEY (`allotmentfk`)
    REFERENCES `dbsolis`.`tblinventory_allotment` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION);

CREATE TABLE `dbsolis`.`tblinventory_allotment_to` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tofk` INT NULL,
  `allotmentfk` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tblinventory_allotment_to_1_idx` (`tofk` ASC),
  INDEX `fk_tblinventory_allotment_to_2_idx` (`allotmentfk` ASC),
  CONSTRAINT `fk_tblinventory_allotment_to_1`
    FOREIGN KEY (`tofk`)
    REFERENCES `dbsolis`.`tbltouroperator` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_to_2`
    FOREIGN KEY (`allotmentfk`)
    REFERENCES `dbsolis`.`tblinventory_allotment` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE `dbsolis`.`tblinventory_allotment_countries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `allotmentfk` INT NULL,
  `countryfk` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tblinventory_allotment_countries_1_idx` (`allotmentfk` ASC),
  INDEX `fk_tblinventory_allotment_countries_2_idx` (`countryfk` ASC),
  CONSTRAINT `fk_tblinventory_allotment_countries_1`
    FOREIGN KEY (`allotmentfk`)
    REFERENCES `dbsolis`.`tblinventory_allotment` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblinventory_allotment_countries_2`
    FOREIGN KEY (`countryfk`)
    REFERENCES `dbsolis`.`tblcountries` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION);

ALTER TABLE `dbsolis`.`tblinventory_allotment` 
ADD COLUMN `priority` VARCHAR(45) NULL AFTER `date_to`,
ADD INDEX `priority` (`priority` ASC);
;

ALTER TABLE `dbsolis`.`tblinventory_allotment` 
ADD INDEX `index8` (`date_from` ASC, `date_to` ASC);
;


INSERT INTO `dbsolis`.`tblmenuprocess` (`menuid`, `processname`, `processdescription`) VALUES ('31', 'MODIFY ALLOTMENTS', 'MODIFY ALLOTMENTS');

ALTER TABLE `dbsolis`.`tblinventory_allotment_to` 
DROP FOREIGN KEY `fk_tblinventory_allotment_to_2`;
ALTER TABLE `dbsolis`.`tblinventory_allotment_to` 
ADD CONSTRAINT `fk_tblinventory_allotment_to_2`
  FOREIGN KEY (`allotmentfk`)
  REFERENCES `dbsolis`.`tblinventory_allotment` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;
