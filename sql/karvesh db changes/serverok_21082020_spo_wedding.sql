CREATE TABLE `dbsolis`.`tblspecial_offer_templates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `template_code` VARCHAR(100) NULL,
  `priority` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `index2` (`template_code` ASC),
  INDEX `index3` (`priority` ASC));


ALTER TABLE `dbsolis`.`tblspecial_offer_templates` 
ADD COLUMN `description` VARCHAR(100) NULL AFTER `priority`;


INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('early_booking', '1', 'Early Booking');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('long_stay', '1', 'Long Stay');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('honeymoon', '500', 'Honeymoon');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('free_nights', '2000', 'Free Nights');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('flat_rate', '1000', 'Flat Rate');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('free_upgrade', '1', 'Free Upgrade');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('wedding_anniversary', '500', 'Wedding Anniversary');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('family_offer', '1', 'Family Offer');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('wedding_party', '500', 'Wedding Party');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('senior_offer', '1', 'Senior Offer');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('meals_upgrade', '1', 'Meals Upgrade');
INSERT INTO `dbsolis`.`tblspecial_offer_templates` (`template_code`, `priority`, `description`) VALUES ('discount', '1', 'Discount');

ALTER TABLE `dbsolis`.`tblspecial_offer_templates` 
ADD COLUMN `tabs` VARCHAR(1000) NULL AFTER `description`;

UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,discounts' WHERE (`id` = '1');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,discounts' WHERE (`id` = '12');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,discounts' WHERE (`id` = '2');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,wedding_discounts' WHERE (`id` = '3');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,free_nights' WHERE (`id` = '4');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,flat_rate_periods,flat_rate_policies,flat_rate_currency,flat_rate_commission,flat_rate_rates' WHERE (`id` = '5');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,upgrade' WHERE (`id` = '6');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,wedding_anniversary' WHERE (`id` = '7');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,family_discount' WHERE (`id` = '8');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,wedding_party' WHERE (`id` = '9');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,senior' WHERE (`id` = '10');
UPDATE `dbsolis`.`tblspecial_offer_templates` SET `tabs` = 'name,periods,conditions,applicable,meals_upgrade' WHERE (`id` = '11');
