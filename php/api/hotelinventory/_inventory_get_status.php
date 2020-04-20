<?php

function _inventory_get_inventory_status($con, $toid, $countryid, $roomid, $hotelid, $thedate, $specific_to) {

    /**
     * Summary.
     *
     * returns the status for the given Date, Tour Operator, Country, Room and Hotel
     *
     *
     * @param PDOConnection  $con   PDO Connection Object
     * @param Integer $toid         Tour Operator ID - MUST BE NULL if $specific_to is WORLDWIDE (B) or MARKET (C)
     * @param Integer $countryid    Country ID - MUST BE NULL if $specific_to is WORLDWIDE (B)
     * @param Integer $roomid       Room ID
     * @param Integer $hotelid      Hotel ID
     * @param Date $thedate         Date Search YYYY-mm-dd
     * @param String $specific_to   Tour Operator (A), World Wide (B), Market (C)
     * 
     * @return status of given date
     * 
     */
    
    $sql = "SELECT * FROM tblinventory_dates 
            WHERE inventory_date=:thedate
            AND hotelfk=:hotelfk 
            AND roomfk=:roomfk 
            AND specific_to=:specific_to 
            AND to_fk=:to_fk 
            AND country_fk=:country_fk
            AND deleted=0";

    $query = $con->prepare($sql);
    $query->execute(array(":thedate" => $thedate,
                           ":hotelfk" => $hotelid, ":roomfk" => $roomid, 
                           ":to_fk" => $toid, 
                           ":country_fk" => $countryid,
                           ":specific_to" => $specific_to));

    $arr_status = array();
    
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr_status[] = $rw;
    }
    
    return $arr_status;
}

function _inventory_get_allotment_units($con, $toid, $countryid, $roomid, $hotelid, $thedate, $priority) {

    /**
     * Summary.
     *
     * returns the allotment status for the given Date, Tour Operator, Room, Hotel and Priority
     *
     *
     * @param PDOConnection  $con   PDO Connection Object
     * @param Integer $toid         Tour Operator ID - MUST BE NULL if $specific_to is WORLDWIDE (B) or MARKET (C)
     * @param Integer $countryid    Country ID - MUST BE NULL if $priority is WORLDWIDE (B)
     * @param Integer $roomid       Room ID
     * @param Integer $hotelid      Hotel ID
     * @param Date $thedate         Date Search YYYY-mm-dd
     * @param Date $priority        priority of search {touroperator,market,company}
     * 
     * @return number of units for the above parameters
     * 
     */
    
    $sql = "SELECT IFNULL(SUM(units),0) as myunits 
            FROM tblinventory_allotment ia 
            WHERE ia.hotel_fk = :hotel_fk AND ia.deleted = 0 
            AND :thedate BETWEEN ia.date_from and ia.date_to
            AND ia.priority = :priority
            AND ia.id IN (SELECT allotmentfk FROM tblinventory_allotment_rooms WHERE roomfk = :roomfk)";
    
    if($priority == "A" && !is_null($toid))
    {
        $sql .= "AND ia.id IN (SELECT allotmentfk FROM tblinventory_allotment_to WHERE tofk = $toid)";
    }
    else if($priority == "C" && !is_null($countryid))
    {
        $sql .= "AND ia.id IN (SELECT allotmentfk FROM tblinventory_allotment_countries WHERE countryfk = $countryid)";
    }

    $query = $con->prepare($sql);
    $query->execute(array(":hotel_fk" => $hotelid, ":thedate" => $thedate,
        ":priority" => $priority, ":roomfk" => $roomid));

   $count = 0;
    
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $count = $rw["myunits"];
    }
    
    return $count;
}

?>
