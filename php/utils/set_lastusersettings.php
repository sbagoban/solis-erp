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

    require_once("../connector/pdo_connect_main.php");
    require_once("../connector/db_pdo.php");
    require_once("../connector/data_connector.php");


    $con = pdo_con();


    $userid = $_POST["userid"];
    $portal = $_POST["portal"];
    $key = $_POST["key"];
    $value = $_POST["value"];
    
    //first check if key value pair exist for userid and portal
    $sql = "select * from tbllastusersettings where userid = :userid AND portal=:portal AND valkey=:key";
    $query_cls = $con->prepare($sql);
    $query_cls->execute(array(":userid" => $userid,":portal" => $portal,":key" => $key));
    if ($rw_cls = $query_cls->fetch(PDO::FETCH_ASSOC)) {

        //update the value of the key
        $sql = "UPDATE tbllastusersettings SET value=:value WHERE id=:id";
        $query = $con->prepare($sql);
        $query->execute(array(":value" => $value,":id" => $rw_cls["id"]));
    }
    else
    {
        //insert the new last record
        $sql = "INSERT INTO tbllastusersettings (userid,portal,valkey,value)
                 VALUES (:userid,:portal,:valkey,:value)";
        $query = $con->prepare($sql);
        $query->execute(array(":userid"=>$userid,":portal"=>$portal,":valkey"=>$key,":value"=>$value));
    }

    echo json_encode(array("OUTCOME" => "OK"));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage() . " on " . $ex->getLine())));
}
?>
