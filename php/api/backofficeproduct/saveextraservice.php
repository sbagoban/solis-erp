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
    
    $id_product_service_extra = $_POST["id_product_service_extra"];
    $id_service_extra = trim($_POST["id_service_extra"]);
    $extra_name = trim($_POST["extra_name"]);
    $id_product_service = trim($_POST["id_product_service"]);
    $extra_description = trim($_POST["extra_description"]);
    $charge = trim($_POST["charge"]);
    $con = pdo_con();

    //check duplicates for service
    $sql = "SELECT * FROM product_service_extra WHERE id_product_service_extra = :id_product_service_extra ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service_extra" => $id_product_service_extra));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_service_extra == "-1") {
        $sql = "INSERT INTO product_service_extra (id_service_extra, extra_name, id_product_service, extra_description, charge) 
                VALUES (:id_service_extra, :extra_name, :id_product_service, :extra_description, :charge)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_service_extra" => $id_service_extra, 
            ":extra_name" => $extra_name,
            ":id_product_service" => $id_product_service,
            ":extra_description" => $extra_description,
            ":charge" => $charge));
        
        $id_product_service_extra = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_service_extra SET 
                id_service_extra=:id_service_extra, 
                extra_name=:extra_name, 
                id_product_service=:id_product_service,
                extra_description=:extra_description,
                charge=:charge
                WHERE id_product_service_extra=:id_product_service_extra";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_extra" => $id_product_service_extra,
            ":id_service_extra" => $id_service_extra,
            ":extra_name" => $extra_name, 
            ":id_product_service" => $id_product_service,
            ":extra_description" => $extra_description, 
            ":charge" => $charge));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service_extra"=>$id_product_service_extra));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
