-- KARVESH ADD COLUMN STAY AND REMOVE COLUMN FREE NIGHTS

ALTER TABLE `dbsolis`.`tblspecial_offer_freenights` 
DROP COLUMN `free_nights`,
ADD COLUMN `pay_nights` INT NULL AFTER `stay_nights`;

ALTER TABLE `dbsolis`.`tblspecial_offer` 
DROP COLUMN `free_nights_deducted_lowest_rate`;
