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
ADD COLUMN `locality_costdetails` VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `invoice_desciption_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `duration_costdetails` time(5),
ADD COLUMN `taxbasis_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `charged_unit_children_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `min_children_costdetails`  VARCHAR(255) NULL DEFAULT NULL,    
ADD COLUMN `max_children_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `charged_unit_adults_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `min_adults_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `max_adults_costdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `services_notes` text character set utf8 collate utf8_general_ci,
ADD COLUMN `address_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `country_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `state_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `postcode_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `vouchercreation_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `printvoucher_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `vouchertext1_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `vouchertext2_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `vouchertext3_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `vouchertext4_voucherdetails`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `settingapplyto_policies`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `pickoffdropoff_policies`  BOOLEAN DEFAULT false,
ADD COLUMN `crossseasonsrates_policies`  VARCHAR(255) NULL DEFAULT NULL,
ADD COLUMN `infantmin_policies`  int NULL DEFAULT NULL,
ADD COLUMN `infantmax_policies`  int NULL DEFAULT NULL,
ADD COLUMN `childmin_policies`  int NULL DEFAULT NULL,
ADD COLUMN `childmax_policies`  int NULL DEFAULT NULL,
ADD COLUMN `teenmin_policies`  int NULL DEFAULT NULL,
ADD COLUMN `teenmax_policies`  int NULL DEFAULT NULL,
ADD COLUMN `adultmin_policies`  int NULL DEFAULT NULL,
ADD COLUMN `adultmax_policies`  int NULL DEFAULT NULL,
ADD COLUMN `starton_monday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_tuesday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_wednesday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_thursday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_friday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_saturday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `starton_sunday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_monday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_tuesday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_wednesday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_thursday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_friday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_saturday_policies`  BOOLEAN DEFAULT NULL,
ADD COLUMN `mustinclude_sunday_policies`  BOOLEAN DEFAULT NULL
