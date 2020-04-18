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


    require_once("../../connector/pdo_connect_main.php");
    $con = pdo_con();
    $con->beginTransaction();


    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }


    $details = json_decode($_POST["details"], true); //+

    $hotelfk = $details["hotelfk"];
    $rooms_ids = $details["rooms_ids"];
    $to_ids = trim($details["to_ids"]);
    $market_countries_ids = $details["market_countries_ids"];
    $specific_to = $details["specific_to"];
    $date_from = date("Y-m-d", strtotime($details["date_from"]));
    $date_to = date("Y-m-d", strtotime($details["date_to"]));



    //split room wise
    $arr_room_ids = explode(",", $rooms_ids);



    $date_from = new DateTime($date_from);
    $date_to = new DateTime($date_to);
    $date_to->modify('+1 day');

    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($date_from, $interval, $date_to);

    foreach ($period as $dt) {
        $inventory_date = $dt->format("Y-m-d");

        for ($i = 0; $i < count($arr_room_ids); $i++) {
            $roomid = $arr_room_ids[$i]; //<------------------- room id
            
            //=================================================
            if ($to_ids == "") {
                
                //TOUR OPERATOR IS NULL here

                //delete the inventory status saved for that TO,ROOM,HOTEL,DATE,SPECIFIC_TO
                $sql = "UPDATE tblinventory_dates SET deleted=1 WHERE 
                        inventory_date=:inventory_date AND roomfk=:roomfk AND 
                        hotelfk=:hotelfk AND to_fk=:to_fk AND specific_to=:specific_to";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventory_date" => $inventory_date, ":roomfk" => $roomid,
                    ":hotelfk" => $hotelfk, ":to_fk" => NULL,
                    ":specific_to" => $specific_to));
            } else {


                //split TO wise
                $arr_to_ids = explode(",", $to_ids);

                for ($t = 0; $t < count($arr_to_ids); $t++) {

                    $tofk = $arr_to_ids[$t]; //<------------------- TO ID
                    //delete the inventory status saved for that TO,ROOM,HOTEL,DATE,SPECIFIC_TO
                    $sql = "UPDATE tblinventory_dates SET deleted=1 WHERE 
                            inventory_date=:inventory_date AND roomfk=:roomfk AND 
                            hotelfk=:hotelfk AND to_fk=:to_fk AND specific_to=:specific_to";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":inventory_date" => $inventory_date, ":roomfk" => $roomid,
                        ":hotelfk" => $hotelfk, ":to_fk" => $tofk,
                        ":specific_to" => $specific_to));
                }
            }
            //=================================================
        }
    }

    $con->commit();

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
