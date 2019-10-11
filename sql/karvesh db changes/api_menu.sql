INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('API', '0', 'N', '100', 'fa-tree', 'api', 'external apis', 'I');

-- get id from above query and then use it into the inserts below

INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Children Ages', '55', 'Y', '1', 'fa-tree', 'api_children_ages', 'external api children ages', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Hotels', '55', 'Y', '2', 'fa-tree', 'api_hotels', 'external api hotels', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Hotel Rooms', '55', 'Y', '3', 'fa-tree', 'api_hotel_rooms', 'external api hotel rooms', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Hotel Types', '55', 'Y', '4', 'fa-tree', 'api_hotel_types', 'external api hotel types', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Hotel Groups', '55', 'Y', '5', 'fa-tree', 'api_hotel_groups', 'external api hotel groups', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Areas', '55', 'Y', '6', 'fa-tree', 'api_areas', 'external api areas', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Coasts', '55', 'Y', '7', 'fa-tree', 'api_coasts', 'external api coasts', 'I');
INSERT INTO `dbsolis`.`tblmenu` (`menuname`, `parentfk`, `leaf`, `ordering`, `icon`, `menusysid`, `description`, `inout`) VALUES ('Countries', '55', 'Y', '8', 'fa-tree', 'api_countries', 'external api countries', 'I');
