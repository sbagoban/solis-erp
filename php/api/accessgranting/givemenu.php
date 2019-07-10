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
    
    $ugrpid = $_POST["ugrpid"];
    $menuid = $_POST["menuid"];

    $arrmenuid = explode(",", $menuid);
    
    for($i = 0; $i < count($arrmenuid); $i++)
    {
        $menuid = $arrmenuid[$i];
        
        $sql = "SELECT * FROM tblgrpmenurights WHERE groupfk =:groupfk AND menufk=:menufk";
        $query = $con->prepare($sql);
        $query->execute(array(":groupfk"=>$ugrpid, ":menufk"=>$menuid));
        if(!$rw = $query->fetch(PDO::FETCH_ASSOC))
        {
            //menu is not assigned, need to insert it
            $sql = "INSERT INTO tblgrpmenurights (groupfk,menufk) VALUES (:groupfk,:menufk)";
            $query = $con->prepare($sql);
            $query->execute(array(":groupfk"=>$ugrpid, ":menufk"=>$menuid));
        }
    }
    
    $con->commit();
    
    echo json_encode(array("OUTCOME" => "OK"));
    
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
