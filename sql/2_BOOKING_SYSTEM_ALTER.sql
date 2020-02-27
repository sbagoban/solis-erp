ALTER TABLE `dbsolis`.`booking_room`
ADD COLUMN `id_booking` INT(11) NOT NULL DEFAULT 0 COMMENT 'id_booking from booking table' AFTER `id_booking_room`,
ADD KEY `id_bookingfk` (`id_booking`);

ALTER TABLE `dbsolis`.`booking_room_log`
ADD COLUMN `id_booking` INT(11) NOT NULL DEFAULT 0 COMMENT 'id_booking from booking table' AFTER `id_booking_room`;
