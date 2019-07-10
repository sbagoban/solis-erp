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

    if (!isset($_POST["spoid"])) {
        throw new Exception("INVALID SPECIAL OFFER ID");
    }
    
    if (!isset($_POST["linkid"])) {
        throw new Exception("INVALID LINK ID");
    }
    
    if (!isset($_POST["linklineid"])) {
        throw new Exception("INVALID LINK LINE ID");
    }
    
    
    require_once("../../connector/pdo_connect_main.php");

    $spoid = $_POST["spoid"];
    $linkid = $_POST["linkid"];
    $linklineid = $_POST["linklineid"];
    $nValue = $_POST["nValue"];
    $colid = $_POST["colid"];
    
    $con = pdo_con();
    
    if($colid == "description" || $colid == "active")
    {
        $sql = "UPDATE tblspecial_offer_link SET $colid=:nValue WHERE id=:id";
         $stmt = $con->prepare($sql);
         $stmt->execute(array(":nValue"=>$nValue, ":id" => $linkid));
    }
    else if($colid == "cumulative")
    {
        $sql = "UPDATE tblspecial_offer_link_spos SET cumulative=:nValue WHERE id=:id";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":nValue"=>$nValue, ":id" => $linklineid));
    }
    

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {

    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
