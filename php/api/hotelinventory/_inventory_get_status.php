<?php

function _inventory_get_inventory_status($con, $toid, $roomid, $hotelid, $thedate) {

    /**
     * Summary.
     *
     * returns the status for the given Date, Tour Operator, Room and Hotel
     *
     *
     * @param PDOConnection  $con   PDO Connection Object
     * @param Integer $toid         Tour Operator ID
     * @param Integer $roomid       Room ID
     * @param Integer $hotelid      Hotel ID
     * @param Date $thedate         Date Search YYYY-mm-dd
     * 
     * @return status of given date
     * 
     */
    
    $sql = "SELECT * FROM tblinventory_dates WHERE inventory_date=:thedate
            AND hotelfk=:hotelfk AND roomfk=:roomfk AND to_fk=:to_fk AND deleted=0";

    $query = $con->prepare($sql);
    $query->execute(array(":thedate" => $thedate,
                           ":hotelfk" => $hotelid, ":roomfk" => $roomid, ":to_fk" => $toid));

    $arr_status = array();
    
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $arr_status[] = $rw;
    }
    
    return $arr_status;
}

function _inventory_get_allotment_units($con, $toid, $roomid, $hotelid, $thedate, $priority) {

    /**
     * Summary.
     *
     * returns the allotment status for the given Date, Tour Operator, Room, Hotel and Priority
     *
     *
     * @param PDOConnection  $con   PDO Connection Object
     * @param Integer $toid         Tour Operator ID
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
            AND ia.id IN (SELECT allotmentfk FROM tblinventory_allotment_rooms WHERE roomfk = :roomfk)
            AND ia.id IN (SELECT allotmentfk FROM tblinventory_allotment_to WHERE tofk = :tofk)";

    $query = $con->prepare($sql);
    $query->execute(array(":hotel_fk" => $hotelid, ":thedate" => $thedate,
        ":priority" => $priority, ":roomfk" => $roomid, ":tofk" => $toid));

   $count = 0;
    
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {
        $count = $rw["myunits"];
    }
    
    return $count;
}

?>
