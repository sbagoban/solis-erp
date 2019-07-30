-- Add column  to table tblexcursion_services 
-- 1. Cost Charged - Adults/Unit 
-- 2. Min Adults Max Adults
-- 3. Cost Charged - Children/Unit
-- 4. Invoice Description
-- 5. Duration
-- 6. Tax Basis - Inclusive/Exclusive
-- 7. Service Class -- X
-- 8. Locality
-- 9. Department -- X
ALTER TABLE `dbsolis`.`tblexcursion_services`
ADD COLUMN 
(
    `charged_unit_children_costdetails`  INT NULL DEFAULT NULL,
    `locality_costdetails` VARCHAR(255) NULL DEFAULT NULL,
    `invoice_desciption_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
    `duration_costdetails` time(5),
    `taxbasis_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
);
