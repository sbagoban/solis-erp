<?php

try {

//=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
    set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
// error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        throw new Exception($errstr . " " . $errno);
    });


    session_start();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    if (!isset($_POST["invid"])) {
        throw new Exception("INVALID INVENTORY ID");
    }

    if (!isset($_POST["hid"])) {
        throw new Exception("INVALID HOTEL ID");
    }

    require_once("../../connector/pdo_connect_main.php");

    $invid = $_POST["invid"];
    $hotel_fk = $_POST["hid"];

    $con = pdo_con();
    
    //to prevent mysql from truncating group_concat values
    $sql = "SET SESSION group_concat_max_len=10000;";
    $stmt = $con->prepare($sql);
    $stmt->execute();



    $arr_inv = array();
    
    $arr_inv["DETAILS"] = _load_inv_general($con,$invid);
    $arr_inv["DATES"] = _load_dates($con,$invid);
    
    echo json_encode(array("OUTCOME" => "OK", "INVENTORY" => $arr_inv));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function _load_dates($con,$invid)
{
    $arr = array();
    $sql = "SELECT inventory_date "
            . "FROM tblinventory_dates WHERE inventoryfk=:inventoryfk "
            . "ORDER BY inventory_date ASC";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":inventoryfk" => $invid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw["inventory_date"];
    }
    
    return $arr;
    
}


function _load_inv_general($con,$invid)
{
    $sql = "SELECT *,
            DATE_FORMAT(release_date_value,'%d-%m-%Y') AS release_date_value,
            DATE_FORMAT(autho_reserve_date_from,'%d-%m-%Y') AS autho_reserve_date_from,
            DATE_FORMAT(autho_reserve_date_to,'%d-%m-%Y') AS autho_reserve_date_to,
            DATE_FORMAT(autho_reserve_time_from,'%H:%i') AS autho_reserve_time_from,
            DATE_FORMAT(autho_reserve_time_to,'%H:%i') AS autho_reserve_time_to,
            
            (
            SELECT group_concat(countryfk SEPARATOR ',')
            FROM tblinventory_countries soc 
            INNER JOIN tblcountries c on soc.countryfk = c.id
            WHERE inventoryfk=:id 
            group by inventoryfk ) as market_countries_ids,

            (SELECT group_concat(country_name SEPARATOR ' , ')
            FROM tblinventory_countries soc 
            INNER JOIN tblcountries c on soc.countryfk = c.id
            WHERE inventoryfk=:id
            group by inventoryfk ) as market_countries_display,


            (SELECT group_concat(roomfk SEPARATOR ',')
            FROM tblinventory_rooms sor
            INNER JOIN tblhotel_rooms hr on sor.roomfk = hr.id
            WHERE inventory_fk=:id 
            group by inventory_fk) as rooms_ids,

            (SELECT group_concat(roomname SEPARATOR ' , ')
            FROM tblinventory_rooms sor
            INNER JOIN tblhotel_rooms hr on sor.roomfk = hr.id
            WHERE sor.inventory_fk=:id group by inventory_fk) as rooms_display,
            


            (SELECT group_concat(tofk SEPARATOR ',')
            FROM tblinventory_touroperators tor
            INNER JOIN tbltouroperator tc on tor.tofk = tc.id
            WHERE inventoryfk=:id 
            group by inventoryfk) as to_ids,

            (SELECT group_concat(toname SEPARATOR ' , ')
            FROM tblinventory_touroperators tor
            INNER JOIN tbltouroperator tc on tor.tofk = tc.id
            WHERE tor.inventoryfk=:id group by inventoryfk) as to_display
            

            FROM tblinventory WHERE id=:id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $invid));

    if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        return $rw;
    }
    
    return [];

}


?>
