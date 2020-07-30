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
    //================================================================================


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

    if (!isset($_POST["itemids"])) {
        throw new Exception("INVALID ID");
    }

    if (!isset($_POST["hotelid"])) {
        throw new Exception("INVALID HOTEL ID");
    }

    $itemids = $_POST["itemids"];
    $hotelid = $_POST["hotelid"];


    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $con->beginTransaction();

    $arrids = explode(",", $itemids);

    for ($i = 0; $i < count($arrids); $i++) {
        $itemid = $arrids[$i];
        //check if this item is already attached to the hotel

        $sql = "SELECT * FROM tblhotel_facilities WHERE facilityfk=:facilityfk AND 
                hotelfk=:hotelfk";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotelfk" => $hotelid,
            ":facilityfk" => $itemid));
        if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
            $sql = "INSERT INTO tblhotel_facilities (facilityfk,hotelfk)
                    VALUES (:facilityfk,:hotelfk)";
            
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":hotelfk" => $hotelid,
                                 ":facilityfk" => $itemid));
        }
    }


    
    $con->commit();

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
