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
    
    $id_product = $_POST["id_product"];
    $id_product_type = trim($_POST["id_product_type"]);
    $id_service_type = trim($_POST["id_service_type"]);
    $product_name = trim($_POST["product_name"]);
    $active = trim($_POST["active"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product WHERE id_product = :id_product ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product" => $id_product));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product == "-1") {
        $sql = "INSERT INTO product (id_product_type, id_service_type, product_name, active) 
                VALUES (:id_product_type, :id_service_type, :product_name, :active)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_type" => $id_product_type, 
            ":id_service_type" => $id_service_type,
            ":product_name" => $product_name,
            ":active" => $active));
        
        $id_product = $con->lastInsertId();
    } else {
        $sql = "UPDATE product SET 
                id_product_type=:id_product_type, 
                id_service_type=:id_service_type, 
                product_name=:product_name,
                active=:active,
                WHERE id_product=:id_product";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product" => $id_product,
            ":id_product_type" => $id_product_type, 
            ":id_service_type" => $id_service_type,
            ":product_name" => $product_name, 
            "active" => $active));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product"=>$id_product));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
