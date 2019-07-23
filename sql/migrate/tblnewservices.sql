CREATE TABLE  `tblnewservices` 
(    
   `id` INTEGER NOT NULL AUTO_INCREMENT ,
   `locationservice` VARCHAR( 255 ) ,
   `servicetype` VARCHAR( 255 ) ,
   `supplier` VARCHAR( 255 ) ,
   `optioncode` VARCHAR( 255 ) ,
   `descriptionservice` VARCHAR( 255 ) ,
    `comments` VARCHAR( 255 ) ,
   PRIMARY KEY `id`(`id`)
)