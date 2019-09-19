(1) INSERT INTO `product` (`id_product`, `id_service_type`,id_product_type,product_name)
VALUES
                (1001,2,1,'TOURS'),(1002,2,1,'GOLF'),(1003,2,1,'QUAD'),(1004,2,1,'HIKING'),(1005,2,2,'DEEP SEA FISHING');


(2) INSERT INTO `tblservicetype` (`id`, `servicecode`, `servicetype`, `isaccomodation`, `isexcursions`, `istransfer`)
VALUES
                (4,'OTH', 'OTHERS', 0, 0, 0)