ALTER TABLE booking_room ADD id_booking int(11);
ALTER TABLE booking_room_log ADD id_booking int(11);

ALTER TABLE booking_room_client ADD id_booking_room int(11);