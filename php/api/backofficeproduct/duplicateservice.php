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
   
    if (!isset($_GET["id_product_service"])) {
        throw new Exception("INVALID ID". $_GET["id_product_service"]);
    }
    
    $id_product_service = $_GET["id_product_service"];
    
    require_once("../../connector/pdo_connect_main.php");
    
    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product_service_cost WHERE id_product_service = :id_product_service ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service" => $id_product_service));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product == "-1") {
        $sql = "INSERT INTO product_service_cost (id_product_service,valid_from, valid_to, id_dept, charge, ps_adult_cost, ps_teen_cost, ps_child_cost, ps_infant_cost, id_currency, currency) 
SELECT :id_product_service, :valid_from, :valid_to, :id_dept, :charge, :ps_adult_cost, 
:ps_teen_cost, :ps_child_cost, :ps_infant_cost, :id_currency, :currency from product_service 
WHERE id_product_service = :id_product_service";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service" => $id_product_service, 
            ":valid_from" => $valid_from,
            ":valid_to" => $valid_to,
            ":id_dept" => $id_dept,
            ":charge" => $charge, 
            ":ps_adult_cost" => $ps_adult_cost,
            ":ps_teen_cost" => $ps_teen_cost,
            ":ps_child_cost" => $ps_child_cost,
            ":ps_infant_cost" => $ps_infant_cost,
            ":id_currency" => $id_currency,
            ":currency" => $currency));
        
        $id_product = $con->lastInsertId();
    } 
    // else {
    //     $sql = "UPDATE product SET 
    //             id_product_type=:id_product_type, 
    //             id_service_type=:id_service_type, 
    //             product_name=:product_name,
    //             active=:active,
    //             WHERE id_product=:id_product";

    //     $stmt = $con->prepare($sql);
    //     $stmt->execute(array(
    //         ":id_product" => $id_product,
    //         ":id_product_type" => $id_product_type, 
    //         ":id_service_type" => $id_service_type,
    //         ":product_name" => $product_name, 
    //         ":active" => $active));
    // }
    echo json_encode(array("OUTCOME" => "OK", "id_product"=>$id_product));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
