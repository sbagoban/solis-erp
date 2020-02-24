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
    
    $id_product_service_package = $_POST["id_product_service_package"];
    $id_product_service  = $_POST["id_product_service"];
    $id_product  = $_POST["id_product"];
    $id_service_type  = $_POST["id_service_type"];
    $id_product_type  = $_POST["id_product_type"];
    $id_product_service_included = $_POST["id_product_service_included"];

    $con = pdo_con();

    //check duplicates for service
    $sql = "SELECT * FROM product_service_package WHERE id_product_service_package = :id_product_service_package";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service_package" => $id_product_service_package));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_service_package == "-1") {
        $sql = "INSERT INTO product_service_package 
        (
            id_product_service,
            id_product,
            id_service_type,
            id_product_type,
            id_product_service_included
        ) 
                VALUES (
                    :id_product_service,
                    :id_product,
                    :id_service_type,
                    :id_product_type,
                    :id_product_service_included
                )";

        $stmt = $con->prepare($sql);
        $data = $id_product_service_included;
        foreach($data as $d) {
            $stmt->execute(array(
                ":id_product_service" => $id_product_service,
                ":id_product" => $id_product,
                ":id_service_type" => $id_service_type,
                ":id_product_type" => $id_product_type,
                ":id_product_service_included" => $d));
        }
        
        $id_product_service_package = $con->lastInsertId();
        
    } else {
        $sql = "UPDATE product_service_package SET 
                id_product_service = :id_product_service,
                id_product = :id_product,
                id_service_type = :id_service_type,
                id_product_type = :id_product_type,
                id_product_service_included = :id_product_service_included
                WHERE id_product_service_package=:id_product_service_package";

        $stmt = $con->prepare($sql);
        $data = $id_product_service_included;
        foreach($data as $d) {
            $stmt->execute(array(
                ":id_product_service_package" => $id_product_service_package,
                ":id_product_service" => $id_product_service,
                ":id_product" => $id_product,
                ":id_service_type" => $id_service_type,
                ":id_product_type" => $id_product_type,
                ":id_product_service_included" => $d));
        }
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service_package"=>$id_product_service_package));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
