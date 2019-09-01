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
    $serviceclosedstartdate = trim($_POST["serviceclosedstartdate"]);
    $serviceclosedenddate = trim($_POST["serviceclosedenddate"]);

    $con = pdo_con();

    // check duplicates for services
    $sql = "SELECT * FROM tblexcursion_services_rates_insertclosedate WHERE id = :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES RATE CLOSED DATE!");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO tblexcursion_services_rates_insertclosedate (idservicesfk, serviceclosedstartdate, serviceclosedenddate) 
                VALUES (:idservicesfk, :serviceclosedstartdate, :serviceclosedenddate)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":idservicesfk" => $idservicesfk, 
            ":serviceclosedstartdate" => $serviceclosedstartdate,
            ":serviceclosedenddate" => $serviceclosedenddate));
        
        $id = $con->lastInsertId();
        echo $id;
    } else {
        $sql = "UPDATE tblexcursion_services_rates_insertclosedate SET 
                idservicesfk=:idservicesfk, 
                serviceclosedstartdate=:serviceclosedstartdate, 
                serviceclosedenddate=:serviceclosedenddate,
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id" => $id,
            ":idservicesfk" => $idservicesfk, 
            ":serviceclosedstartdate" => $serviceclosedstartdate,
            ":serviceclosedenddate" => $serviceclosedenddate));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
