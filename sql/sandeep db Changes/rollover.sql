
ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `rollover_type` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;

ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `rollover_value` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;


ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `ps_adult_claim_rollover` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;

ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `ps_teen_claim_rollover` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;

ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `ps_child_claim_rollover` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;

ALTER TABLE `dbsolis`.`product_service_claim`
ADD COLUMN `ps_infant_claim_rollover` VARCHAR(45) NOT NULL DEFAULT 0 AFTER `ex_sunday`;
