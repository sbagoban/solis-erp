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


    require_once("../../connector/pdo_connect_main.php");

    $id = $_POST["id"];
    $arr_buy = array();
    $arr_sell = array();

    $con = pdo_con();

    $sql = "SELECT scspd.*, tci.item_name, tci.code, tci.core_addon, tci.abbrv
            FROM tblservice_contract_settings_profile_details scspd
            INNER JOIN tbltaxcomm_items tci on scspd.item_fk = tci.id
            WHERE scspd.profile_fk = :id
            ORDER BY scspd.buy_sell, scspd.row_index ASC";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id));

    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($rw["buy_sell"] == "BUY") {
            $arr_buy[] = $rw;
        } else if ($rw["buy_sell"] == "SELL") {
            $arr_sell[] = $rw;
        }
    }

    echo json_encode(array("OUTCOME" => "OK", "BUY" => $arr_buy, "SELL" => $arr_sell));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
