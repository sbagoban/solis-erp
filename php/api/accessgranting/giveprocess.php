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
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");
    
    $con = pdo_con();
    $con->beginTransaction();
    
    $ugrpid = $_POST["grpid"];
    $prcsids = $_POST["prcsids"];

    $arrprcsids = explode(",", $prcsids);
    
    for($i = 0; $i < count($arrprcsids); $i++)
    {
        $pid = $arrprcsids[$i];
        
        $sql = "SELECT * FROM tblgrpprcsrights WHERE gprfk =:grpfk AND prcsfk=:prcsfk";
        $query = $con->prepare($sql);
        $query->execute(array(":grpfk"=>$ugrpid, ":prcsfk"=>$pid));
        if(!$rw = $query->fetch(PDO::FETCH_ASSOC))
        {
            //menu is not assigned, need to insert it
            $sql = "INSERT INTO tblgrpprcsrights (gprfk,prcsfk) VALUES (:grpfk,:prcsfk)";
            $query = $con->prepare($sql);
            $query->execute(array(":grpfk"=>$ugrpid, ":prcsfk"=>$pid));
        }
    }
    
    $con->commit();
    
    echo json_encode(array("OUTCOME" => "OK"));
    
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
