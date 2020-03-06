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


    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    $hotelfk = $_POST["hid"];
    $data = json_decode($_POST["data"], true); //+
    $active = $data["active"];
    $description = $data["description"];

    //===========================================================================


    $sql = "INSERT INTO tblspecial_offer_link
                (description,active,deleted,hotel_fk,created_on) 
                VALUES (:description,:active,0,:hotel_fk,NOW())";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":description"=>$description,":active"=>$active,":hotel_fk"=>$hotelfk));
    $id = $con->lastInsertId();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
