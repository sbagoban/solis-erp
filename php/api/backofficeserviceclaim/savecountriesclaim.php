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
    $idrates_fk = trim($_POST["idrates_fk"]);
    $country_id = trim($_POST["country_id"]);
    
    $con = pdo_con();

    // check duplicates for services
    $sql = "SELECT * FROM product_services_claim_countries WHERE id = :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES !");
    }

    if ($id == "-1") {
        $sql = "INSERT INTO product_services_claim_countries (id_product_service_claim, id_countries) 
                VALUES (:id_product_service_claim, :id_countries)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_claim" => $id_product_service_claim, 
            ":id_countries" => $id_countries));
        
        $id = $con->lastInsertId();
        echo $id;
    } else {
        $sql = "UPDATE product_services_claim_countries SET 
                idservicesfk=:idservicesfk, 
                idrates_fk=:idrates_fk,
                country_id=:country_id
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":idservicesfk" => $idservicesfk, 
            ":idrates_fk" => $idrates_fk,
            ":country_id" => $country_id));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
