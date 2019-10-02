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
    //$id_product_service_claim = trim($_POST["id_product_service_claim"]);
    $id_countries = trim($_POST["id_countries"]);
    
    $con = pdo_con();

    $sql_id = "SELECT id_product_service_claim FROM product_service_claim ORDER BY id_product_service_claim DESC LIMIT 1";
    $stmt_id = $con->prepare($sql_id);
    $stmt_id->execute(array(":id_product_service_claim" => $id_product_service_claim));

    // check duplicates for services
    $sql = "SELECT * FROM product_services_claim_countries WHERE id_product_services_claim_countries = :id_product_services_claim_countries ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_services_claim_countries" => $id_product_services_claim_countries));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES !");
    }

    if ($id_product_services_claim_countries == "-1") {
        $sql = "INSERT INTO product_services_claim_countries (id_product_service_claim, id_countries) 
                VALUES (:id_product_service_claim, :id_countries)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_claim" => $id_product_service_claim, 
            ":id_countries" => $id_countries));
        
        $id_product_services_claim_countries = $con->lastInsertId();
        echo $id_product_services_claim_countries;
    } else {
        $sql = "UPDATE product_services_claim_countries SET 
                id_product_service_claim=:id_product_service_claim, 
                id_countries=:id_countries
                WHERE id_product_services_claim_countries=:id_product_services_claim_countries";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_claim" => $id_product_service_claim, 
            ":id_countries" => $id_countries));
    }
    echo json_encode(array("OUTCOME" => "OK", "ID"=>$id_product_services_claim_countries));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
