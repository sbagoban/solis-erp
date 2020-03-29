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
    
    if (!isset($_POST["id"])) {
        throw new Exception("INVALID ID");
    }
    
    $id = $_POST["id"];
    $token = $_POST["t"];
    $ip_add = $_POST["ip"];
    

    require_once("../utils/utilities.php");
    require_once("../connector/pdo_connect_main_login_internet.php");

    $con = connect_login_pdo();
    $stmt = $con->prepare("UPDATE tbluser SET date_lastlogin_success=:date_lastlogin_success WHERE id = :id");
    $stmt->execute(array(":id"=>$id,":date_lastlogin_success"=>date("Y-m-d H:i:s")));
    
    // SESSION DEATILS
    $stmt = $con->prepare("INSERT INTO login_session (id_user,session_token,session_ip) 
            VALUES (:id_user, :session_token,:session_ip)");

	$stmt->execute(array(":id_user"=>$id,":session_token"=>$token, ":session_ip"=>$ip_add));

    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>
