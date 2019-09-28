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
    
    $id_product_services_extra = $_POST["id_product_services_extra"];
    $id_services_extra = trim($_POST["id_services_extra"]);
    $extra_name = trim($_POST["extra_name"]);
    $id_product_services = trim($_POST["id_product_services"]);
    $extra_description = trim($_POST["extra_description"]);
    $charges = trim($_POST["charges"]);
    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product_services_extra WHERE id_product_services_extra = :id_product_services_extra ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_services_extra" => $id_product_services_extra));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_services_extra == "-1") {
        $sql = "INSERT INTO product_services_extra (id_services_extra, extra_name, id_product_services, extra_description, charges) 
                VALUES (:id_services_extra, :extra_name, :id_product_services, :extra_description, :charges)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_services_extra" => $id_services_extra, 
            ":extra_name" => $extra_name,
            ":id_product_services" => $id_product_services,
            ":extra_description" => $extra_description,
            ":charges" => $charges));
        
        $id_product_services_extra = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_services_extra SET 
                id_services_extra=:id_services_extra, 
                extra_name=:extra_name, 
                id_product_services=:id_product_services,
                extra_description=:extra_description,
                charges=:charges
                WHERE id_product_services_extra=:id_product_services_extra";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_services_extra" => $id_product_services_extra,
            ":id_services_extra" => $id_services_extra,
            ":extra_name" => $extra_name, 
            ":id_product_services" => $id_product_services,
            ":extra_description" => $extra_description, 
            ":charges" => $charges));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_services_extra"=>$id_product_services_extra));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
