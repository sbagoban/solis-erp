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

    //get all the contract
    //for each contract, get the seasons and date periods of that hotel
    //for each date per room, update the season it falls into
    //update to null, it it is a custom season

    $sql = "select * from tblservice_contract where deleted = 0";
    $stmtdates = $con->prepare($sql);
    $stmtdates->execute();
    while ($rw = $stmtdates->fetch(PDO::FETCH_ASSOC)) {

        $contractid = $rw["id"];
        $hotelfk = $rw["hotelfk"];
        $contract_from = $rw["active_from"];
        $contract_to = $rw["active_to"];
        
        echo $contractid . "<br>";
            
        //for that contract get the season periods
        $arr_seasons = loaddateseasons($con, $hotelfk, $contract_from, $contract_to);

        //now the the dates for that contract
        $arr_dates = loadcontractdates($con, $contractid);

        //for each dates in arr_dates, check in which season they fall from arr_seasons
        //get the id and stamp it into tblservice_contract_roomcapacity_dates

        for ($i = 0; $i < count($arr_dates); $i++) {
            $dtfrom = $arr_dates[$i]["override_dtfrom"];
            $dtto = $arr_dates[$i]["override_dtto"];
            $rwid = $arr_dates[$i]["id"];

            $seasonid = lookupseason($arr_seasons, $dtfrom, $dtto, $contract_from, $contract_to);
            
            //update that season in the record 
            if(!is_null($seasonid))
            {
                $sql = "UPDATE tblservice_contract_roomcapacity_dates SET season_fk = :seasonid 
                        WHERE id=:id";

                $stmt_exec = $con->prepare($sql);
                $stmt_exec->execute(array(":seasonid"=>$seasonid, ":id"=>$rwid));     
            }
            
            
        }
    }





    $con->commit();

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function lookupseason($arr_seasons, $dtfrom, $dtto, $contract_from, $contract_to) {
    if ($dtfrom == "")
        {
            $dtfrom = $contract_from;
        }

        if ($dtto == "")
        {
            $dtto = $contract_to;
        }


        for ($s = 0; $s < count($arr_seasons); $s++)
        {
            $season = $arr_seasons[$s]["season"];
            $seasonid = $arr_seasons[$s]["seasonfk"];
            $seasondtfrom = $arr_seasons[$s]["checkin"];
            $seasondtto = $arr_seasons[$s]["checkout"];
    
            if ($seasondtfrom <= $dtfrom &&
                $dtto <=  $seasondtto)
            {
                return $seasonid;
            }
            
        }

        //no seasons found for the date
        //return custom season
        return null;
}

function loadcontractdates($con, $cid) {
    $sql = "select * from tblservice_contract_roomcapacity_dates
            where service_contract_roomcapacity_fk in 
            (
            select id from tblservice_contract_rooms where servicecontractfk = $cid
            )
            order by override_dtfrom, override_dtto";

    $stmtdates = $con->prepare($sql);
    $stmtdates->execute();
    $arr = array();

    while ($rw = $stmtdates->fetch(PDO::FETCH_ASSOC)) {
        $arr[] = $rw;
    }

    return $arr;
}

function loaddateseasons($con, $hotelid, $contract_from, $contract_to) {
    $sql = "select dp.*,s.season,s.scode,
            DATE_FORMAT(dp.checkin,'%d-%m-%Y') AS checkin_dmy,
            DATE_FORMAT(dp.checkout,'%d-%m-%Y') AS checkout_dmy
            from tbldateperiods dp 
            INNER JOIN tblseasons s on dp.seasonfk = s.id
            where dp.hotelfk = $hotelid and active = 1 
            AND 
            (
                dp.checkin BETWEEN '$contract_from' AND '$contract_to' 
                OR
                dp.checkout BETWEEN '$contract_from' AND '$contract_to'
                OR 
                dp.checkin <= '$contract_from' AND dp.checkout >= '$contract_to'
            )
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
