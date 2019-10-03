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
    
    $id_product_services_claim_countries = $_POST["id_product_services_claim_countries"];
    $id_country = $_POST["id_country"];
    $id_product_service_claim = $_POST["id_product_service_claim"];

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product_services_claim_countries WHERE id_product_services_claim_countries = :id_product_services_claim_countries";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_services_claim_countries" => $id_product_services_claim_countries));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_services_claim_countries == "-1") {
        $sql = "INSERT INTO product_services_claim_countries 
        (
            id_country,
            id_product_service_claim
        ) 
                VALUES (
                    :id_country,
                    :id_product_service_claim
                )";

        $stmt = $con->prepare($sql);
        $data = $id_country;
        foreach($data as $d) {
            $stmt->execute(array(
                ":id_country" => $d,
                ":id_product_service_claim" => $id_product_service_claim));
        }
        
        $id_product_services_claim_countries = $con->lastInsertId();
        
    } else {
        $sql = "UPDATE product_services_claim_countries SET 
                id_country = :id_country,
                id_product_service_claim = :id_product_service_claim
                WHERE id_product_services_claim_countries=:id_product_services_claim_countries";

        $stmt = $con->prepare($sql);
        $data = $id_country;
        foreach($data as $d) {
            $stmt->execute(array(
                ":id_product_services_claim_countries" => $id_product_services_claim_countries,
                ":id_country" => $d,
                ":id_product_service_claim" => $id_product_service_claim)); 
        }
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_services_claim_countries"=>$id_product_services_claim_countries));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
