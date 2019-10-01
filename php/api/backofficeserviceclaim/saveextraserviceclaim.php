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
    
    $id_product_services_extra_claim = $_POST["id_product_services_extra_claim"];
    $id_product_services_cost = trim($_POST["id_product_services_cost"]);
    $id_product_services = trim($_POST["id_product_services"]);
    $valid_from = trim($_POST["valid_from"]);
    $valid_to = $_POST["valid_to"];
    $ps_adult_claim = trim($_POST["ps_adult_claim"]);
    $ps_teen_claim = trim($_POST["ps_teen_claim"]);
    $ps_child_claim = trim($_POST["ps_child_claim"]);
    $ps_infant_claim = trim($_POST["ps_infant_claim"]);
    $charges = trim($_POST["charges"]);
    $id_currency = trim($_POST["id_currency"]);
    $currency = trim($_POST["currency"]);
    $id_product_services_extra_cost = trim($_POST["id_product_services_extra_cost"]);
    $id_product_services_claim = trim($_POST["id_product_services_claim"]);
    $id_dept = trim($_POST["id_dept"]);
    $specific_to = trim($_POST["specific_to"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product_services_extra_claim WHERE id_product_services_extra_claim = :id_product_services_extra_claim";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_services_extra_claim" => $id_product_services_extra_claim));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_services_extra_claim == "-1") {
        $sql = "INSERT INTO product_services_extra_claim 
        (
            id_product_services_extra_cost,
            id_product_services_claim,
            id_product_services_cost,
            id_product_services,
            valid_from,
            valid_to,
            id_dept,
            specific_to,
            charges,
            ps_adult_claim,
            ps_teen_claim,
            ps_child_claim,
            ps_infant_claim,
            id_currency,
            currency
        ) 
                VALUES (
                    :id_product_services_extra_cost,
                    :id_product_services_claim,
                    :id_product_services_cost,
                    :id_product_services,
                    :valid_from,
                    :valid_to,
                    :id_dept,
                    :specific_to,
                    :charges,
                    :ps_adult_claim,
                    :ps_teen_claim,
                    :ps_child_claim,
                    :ps_infant_claim,
                    :id_currency,
                    :currency
                )";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_services_extra_cost" => $id_product_services_extra_cost,
            ":id_product_services_claim" => $id_product_services_claim,
            ":id_product_services_cost" => $id_product_services_cost,
            ":id_product_services" => $id_product_services,
            ":valid_from" => $valid_from,
            ":valid_to" => $valid_to,
            ":id_dept" => $id_dept,
            ":specific_to" => $specific_to,
            ":charges" => $charges,
            ":ps_adult_claim" => $ps_adult_claim,
            ":ps_teen_claim" => $ps_teen_claim,
            ":ps_child_claim" => $ps_child_claim,
            ":ps_infant_claim" => $ps_infant_claim,
            ":id_currency" => $id_currency,
            ":currency" => $currency));
        
        $id_product_services_extra_claim = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_services_extra_claim SET 
                id_product_services_extra_cost = :id_product_services_extra_cost,
                id_product_services_claim = :id_product_services_claim,
                id_product_services_cost = :id_product_services_cost,
                id_product_services = :id_product_services,
                valid_from = :valid_from,
                valid_to = :valid_to,
                id_dept = :id_dept,
                specific_to = :specific_to,
                charges = :charges,
                ps_adult_claim = :ps_adult_claim,
                ps_teen_claim = :ps_teen_claim,
                ps_child_claim = :ps_child_claim,
                ps_infant_claim = :ps_infant_claim,
                id_currency = :id_currency,
                currency = :currency
                WHERE id_product_services_extra_claim=:id_product_services_extra_claim";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_services_extra_claim" => $id_product_services_extra_claim,
            ":id_product_services_extra_cost" => $id_product_services_extra_cost,
            ":id_product_services_claim" => $id_product_services_claim,
            ":id_product_services_cost" => $id_product_services_cost,
            ":id_product_services" => $id_product_services,
            ":valid_from" => $valid_from,
            ":valid_to" => $valid_to,
            ":id_dept" => $id_dept,
            ":specific_to" => $specific_to,
            ":charges" => $charges,
            ":ps_adult_claim" => $ps_adult_claim,
            ":ps_teen_claim" => $ps_teen_claim,
            ":ps_child_claim" => $ps_child_claim,
            ":ps_infant_claim" => $ps_infant_claim,
            ":id_currency" => $id_currency,
            ":currency" => $currency));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_services_extra_claim"=>$id_product_services_extra_claim));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
