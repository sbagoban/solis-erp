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
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $con->beginTransaction();

    //get all the spos
    //for each spo, get the seasons and date periods of that hotel
    //for each date per validity period, update the season it falls into
    //update to null, it it is a custom season

    $sql = "select * from tblspecial_offer where deleted = 0";
    $stmtdates = $con->prepare($sql);
    $stmtdates->execute();
    while ($rw = $stmtdates->fetch(PDO::FETCH_ASSOC)) {

        $spoid = $rw["id"];
        $hotelfk = $rw["hotel_fk"];

        //for that spo get the season periods
        $arr_seasons = loaddateseasons($con, $hotelfk);

        //now the the validity dates for that spo
        $arr_dates = loadvaliditydates($con, $spoid);

        //for each dates in arr_dates, check in which season they fall from arr_seasons
        //get the id and stamp it into tblspecial_offer_validityperiods

        for ($i = 0; $i < count($arr_dates); $i++) {
            $dtfrom = $arr_dates[$i]["valid_from"];
            $dtto = $arr_dates[$i]["valid_to"];
            $rwid = $arr_dates[$i]["id"];

            $seasonid = lookupseason($arr_seasons, $dtfrom, $dtto);

            //update that season in the record 
            if (!is_null($seasonid)) {
                $sql = "UPDATE tblspecial_offer_validityperiods SET season_fk = :seasonid 
                        WHERE id=:id";

                $stmt_exec = $con->prepare($sql);
                $stmt_exec->execute(array(":seasonid" => $seasonid, ":id" => $rwid));
            }
        }
    }


    $con->commit();

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function lookupseason($arr_seasons, $dtfrom, $dtto) {

    for ($s = 0; $s < count($arr_seasons); $s++) {
        $season = $arr_seasons[$s]["season"];
        $seasonid = $arr_seasons[$s]["seasonfk"];
        $seasondtfrom = $arr_seasons[$s]["checkin"];
        $seasondtto = $arr_seasons[$s]["checkout"];

        if ($seasondtfrom <= $dtfrom &&
                $dtto <= $seasondtto) {
            return $seasonid;
        }
    }

    //no seasons found for the date
    //return custom season
    return null;
}

function loadvaliditydates($con, $cid) {
    $sql = "select * from tblspecial_offer_validityperiods 
            where spo_fk = $cid order by valid_from, valid_to";

    $stmtdates = $con->prepare($sql);
    $stmtdates->execute();
    $arr = array();

    while ($rw = $stmtdates->fetch(PDO::FETCH_ASSOC)) {
        $arr[] = $rw;
    }

    return $arr;
}

function loaddateseasons($con, $hotelid) {
    $sql = "select dp.*,s.season,s.scode,
            DATE_FORMAT(dp.checkin,'%d-%m-%Y') AS checkin_dmy,
            DATE_FORMAT(dp.checkout,'%d-%m-%Y') AS checkout_dmy
            from tbldateperiods dp 
            INNER JOIN tblseasons s on dp.seasonfk = s.id
            where dp.hotelfk = $hotelid and active = 1 
            order by dp.checkin ASC";


    $stmtdates = $con->prepare($sql);
    $stmtdates->execute();
    $arr = array();

    while ($rw = $stmtdates->fetch(PDO::FETCH_ASSOC)) {
        $arr[] = $rw;
    }

    return $arr;
}

?>
