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
    
    $value = "";
    
    //get the value of hte userid + portal + key combination
    $sql = "select * from tbllastusersettings where userid = :userid AND portal=:portal AND valkey=:valkey";
    $query_cls = $con->prepare($sql);
    $query_cls->execute(array(":userid" => $userid,":portal" => $portal,":valkey" => $key));
    if ($rw_cls = $query_cls->fetch(PDO::FETCH_ASSOC)) {

        $value = $rw_cls["value"];
    }
    
    echo json_encode(array("OUTCOME" => "OK", "VALUE"=>$value));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage() . " on " . $ex->getLine())));
}
?>
