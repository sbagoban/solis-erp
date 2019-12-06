<?php

function _hotel_details($con, $hotelids) {

    $arr_hotel = array();

    $sql = "SELECT * FROM tblhotels WHERE id IN ($hotelids) ORDER BY hotelname ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

        
        $id = $rw["id"];
        $hotelname = $rw["hotelname"];
        $hoteltypefk = $rw["hoteltypefk"];
        $groupfk = $rw["groupfk"];
        $description = $rw["description"];
        $phy_address = $rw["phy_address"];
        $phy_address2 = $rw["phy_address2"];
        $phy_city = $rw["phy_city"];
        $phy_postcode = $rw["phy_postcode"];
        $phy_countryfk = $rw["phy_countryfk"];
        $areafk = $rw["areafk"];
        $coastfk = $rw["coastfk"];
        $id_transfer_coast = $rw["id_transfer_coast"];
        $website = $rw["website"];
        $ratecode = $rw["ratecode"];
        $specialratecode = $rw["specialratecode"];
        $lat = $rw["lat"];
        $lon = $rw["lon"];
        $active = $rw["active"];
        $mail_address = $rw["mail_address"];
        $mail_address2 = $rw["mail_address2"];
        $mail_city = $rw["mail_city"];
        $mail_postcode = $rw["mail_postcode"];
        $mail_countryfk = $rw["mail_countryfk"];
        $deleted = $rw["deleted"];
        $rating = $rw["rating"];
        $property_name = $rw["property_name"];
        $company_name = $rw["company_name"];


        $arr_hotel[] = array("id" => $id,
            "hotelname" => $hotelname,
            "hoteltypefk" => $hoteltypefk,
            "groupfk" => $groupfk,
            "description" => $description,
            "phy_address" => $phy_address,
            "phy_address2" => $phy_address2,
            "phy_city" => $phy_city,
            "phy_postcode" => $phy_postcode,
            "phy_countryfk" => $phy_countryfk,
            "areafk" => $areafk,
            "id_transfer_coast" => $id_transfer_coast,
            "coastfk" => $coastfk,
            "website" => $website,
            "ratecode" => $ratecode,
            "specialratecode" => $specialratecode,
            "lat" => $lat,
            "lon" => $lon,
            "active" => $active,
            "mail_address" => $mail_address,
            "mail_address2" => $mail_address2,
            "mail_city" => $mail_city,
            "mail_postcode" => $mail_postcode,
            "mail_countryfk" => $mail_countryfk,
            "deleted" => $deleted,
            "rating" => $rating,
            "property_name" => $property_name,
            "company_name" => $company_name,
            "images" => _hotel_getHotelImages($con, $id),
            "commission" => _hotel_getHotelCommission($con, $id),
            "currencies" => _hotel_getHotelCurrencies($con, $id),
            "rooms" => _hotel_getHotelRooms($con, $id));
    }


    return $arr_hotel;
}

function _hotel_getHotelImages($con, $hotelid) {
    $arr_images = array();

    $sql = "SELECT * FROM tblhotel_images WHERE hotelfk=:hotelfk ORDER BY image_name ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hotelfk" => $hotelid));
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arr_images[] = array("id" => $rw["id"],
            "image_name" => $rw["image_name"],
            "image_description" => $rw["image_description"],
            "isdefault" => $rw["isdefault"]);
    }


    return $arr_images;
}

function _hotel_getHotelCommission($con, $hotelid) {
    $arr_commission = array();

    $sql = "SELECT * FROM tblhotel_commission WHERE hotelfk=:hotelfk ORDER BY dtfrom DESC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hotelfk" => $hotelid));
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arr_commission[] = array("id" => $rw["id"],
            "taxcode_fk" => $rw["taxcode_fk"],
            "taxamt" => $rw["taxamt"],
            "commission" => $rw["commission"],
            "markup" => $rw["markup"],
            "dtfrom" => $rw["dtfrom"],
            "dtto" => $rw["dtto"]);
    }


    return $arr_commission;
}

function _hotel_getHotelCurrencies($con, $hotelid) {
    $arr_currency = array();

    $sql = "SELECT * FROM tblhotel_currency WHERE hotelfk=:hotelfk ORDER BY currencyid ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hotelfk" => $hotelid));
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arr_currency[] = array("id" => $rw["id"],
            "currencyid" => $rw["currencyid"],
            "tax_code" => $rw["tax_code"],
            "terms_code" => $rw["terms_code"],
            "terms_value" => $rw["terms_value"],
            "use_default" => $rw["use_default"],
            "bankfk" => $rw["bankfk"],
            "bankaccount" => $rw["bankaccount"],
            "accountname" => $rw["accountname"]);
    }


    return $arr_currency;
}

function _hotel_getHotelRooms($con, $hotelid) {
    $arr_rooms = array();

    $sql = "SELECT * FROM tblhotel_rooms WHERE hotelfk=:hotelfk ORDER BY roomname ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hotelfk" => $hotelid));
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arr_rooms[] = array("id" => $rw["id"],
            "roomname" => $rw["roomname"],
            "description" => $rw["description"],
            "numbedrooms" => $rw["numbedrooms"],
            "images" => _hotel_getHotelRoomImages($con, $rw["id"]));
    }


    return $arr_rooms;
}

function _hotel_getHotelRoomImages($con, $roomid) {
    $arr_rooms_images = array();

    $sql = "SELECT * FROM tblhotel_room_images WHERE roomfk=:roomfk ORDER BY image_name ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":roomfk" => $roomid));
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $arr_rooms_images[] = array("id" => $rw["id"],
            "image_name" => $rw["image_name"],
            "image_description" => $rw["image_description"],
            "isdefault" => $rw["isdefault"]);
    }


    return $arr_rooms_images;
}

?>
