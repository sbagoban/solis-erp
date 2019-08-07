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
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    require_once("../../connector/pdo_connect_main.php");
    
    $id = $_POST["id"];
    $idservicesfk = trim($_POST["idservicesfk"]);
    $extraname = trim($_POST["extraname"]);
    $extradescription = trim($_POST["extradescription"]);
    $chargeper = trim($_POST["chargeper"]);

    $con = pdo_con();

    // //check duplicates for services
    $sql = "SELECT * FROM tblexcursion_services_quotedetails WHERE  idservicesfk <> :idservicesfk AND id = :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":idservicesfk" => $idservicesfk, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES QUOTE DETAILS!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblexcursion_services_quotedetails (idservicesfk, extraname, extradescription, chargeper) 
                VALUES (:idservicesfk, :extraname, :extradescription, :chargeper)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":idservicesfk" => $idservicesfk, 
            ":extraname" => $extraname,
            ":extradescription" => $extradescription,
            ":chargeper" => $chargeper));
        
        $id = $con->lastInsertId();
        echo $id;
    } else {
        $sql = "UPDATE tblexcursion_services SET 
                idservicesfk=:idservicesfk, 
                extraname=:extraname, 
                extradescription=:extradescription, 
                chargeper=:chargeper,
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":idservicesfk" => $idservicesfk, 
            ":extraname" => $extraname,
            ":extradescription" => $extradescription,
            ":chargeper" => $chargeper));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
