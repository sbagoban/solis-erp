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
        die("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        die("INVALID TOKEN");
    }

    if ($_POST["t"] != $_SESSION["token"]) {
        die("INVALID TOKEN");
    }

    if (!isset($_POST["hid"])) {
        die("INVALID HOTEL ID");
    }

    $hid = $_POST["hid"];
    $activefrom = $_POST["activefrom"];
    
    $taxamt = "";
    $commission = "";
    $markup = "";
    $taxcode_fk = "";

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../connector/db_pdo.php");
    require_once("../../connector/data_connector.php");

    $con = pdo_con();

    $arr_checkinout = array();

    $sql = "SELECT * FROM tblhotel_commission WHERE hotelfk = :hotelid 
            AND :active_from BETWEEN dtfrom AND dtto ORDER BY dtfrom DESC LIMIT 1";

    $query = $con->prepare($sql);
    $query->execute(array(":hotelid" => $hid, ":active_from"=>$activefrom));
    if ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $taxamt = $rw["taxamt"];
        $commission = $rw["commission"];
        $markup = $rw["markup"];
        $taxcode_fk = $rw["taxcode_fk"];
    }
    
    $arr_results = array("TAXAMT"=>$taxamt, "COMMI"=>$commission,"MKUP"=>$markup,
                         "TAXCODE_FK"=>$taxcode_fk);

    echo json_encode(array("OUTCOME" => "OK", "DEFAULT_COMMISSION" => $arr_results));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>