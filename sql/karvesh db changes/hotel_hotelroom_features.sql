ALTER TABLE `dbsolis`.`tblfacilities` 
ADD COLUMN `category` VARCHAR(45) NULL AFTER `ordering`,
ADD INDEX `index5` (`category` ASC) VISIBLE;
;

ALTER TABLE `dbsolis`.`tblfacilities` 
DROP COLUMN `active`,
DROP INDEX `index3` ;
;


CREATE TABLE `dbsolis`.`tblhotel_room_facilities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `roomfk` INT NULL,
  `facilityfk` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tblhotel_room_facilities_1_idx` (`roomfk` ASC) VISIBLE,
  INDEX `fk_tblhotel_room_facilities_2_idx` (`facilityfk` ASC) VISIBLE,
  CONSTRAINT `fk_tblhotel_room_facilities_1`
    FOREIGN KEY (`roomfk`)
    REFERENCES `dbsolis`.`tblhotel_rooms` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tblhotel_room_facilities_2`
    FOREIGN KEY (`facilityfk`)
    REFERENCES `dbsolis`.`tblfacilities` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION);
