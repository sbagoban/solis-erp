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
    
    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    require_once("../../connector/pdo_connect_main.php");

    $id = $_POST["id"];
    $agefrom = trim($_POST["agefrom"]);
    $ageto = trim($_POST["ageto"]);    
    

    $con = pdo_con();

    //check duplicates for age range
    $sql = "SELECT * FROM tblchildrenagerange WHERE agefrom = :agefrom AND ageto=:ageto AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":agefrom" => $agefrom, ":ageto"=>$ageto, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE AGE RANGE!");
    }

    

    if ($id == "-1") {
        $sql = "INSERT INTO tblchildrenagerange (agefrom,ageto) 
                VALUES (:agefrom,:ageto) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":agefrom" => $agefrom, 
            ":ageto" => $ageto));
        
        $id = $con->lastInsertId();
    } else {
        $sql = "UPDATE tblchildrenagerange SET agefrom=:agefrom, ageto=:ageto
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":agefrom" => $agefrom, 
            ":ageto" => $ageto));
    }

    
    
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
