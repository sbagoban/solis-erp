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
    
    $id_product_services = $_POST['id_product_services'];
    $id_product_services_cost = $_POST["id_product_services_cost"];
    $valid_from = $_POST["valid_from"];
    $valid_to = trim($_POST["valid_to"]);
    $ps_adult_cost = trim($_POST["ps_adult_cost"]);
    $ps_teen_cost = trim($_POST["ps_teen_cost"]);
    $ps_child_cost = trim($_POST["ps_child_cost"]);
    $ps_infant_cost = trim($_POST["ps_infant_cost"]);
    $id_currency = trim($_POST["id_currency"]);
    $id_dept = trim($_POST["id_dept"]);

    $con = pdo_con();

    //check duplicates for services
    $sql = "SELECT * FROM product_services_cost WHERE id_product_services_cost = :id_product_services_cost ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_services_cost" => $id_product_services_cost));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_services_cost == "-1") {
        $sql = "INSERT INTO product_services_cost (id_product_services, valid_from, valid_to, ps_adult_cost, ps_teen_cost, ps_child_cost, ps_infant_cost, id_currency, id_dept) 
                VALUES (:id_product_services, :valid_from, :valid_to, :ps_adult_cost, :ps_teen_cost, :ps_child_cost, :ps_infant_cost, :id_currency, :id_dept)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_services" => $id_product_services,
            ":valid_from" => $valid_from, 
            ":valid_to" => $valid_to,
            ":ps_adult_cost" => $ps_adult_cost,
            ":ps_teen_cost" => $ps_teen_cost,
            ":ps_child_cost" => $ps_child_cost,
            ":ps_infant_cost" => $ps_infant_cost,
            ":id_currency" => $id_currency,
            ":id_dept" => $id_dept));
        
        $id_product_services_cost = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_services_cost SET 
                id_product_services_cost=:id_product_services_cost, 
                id_product_services=:id_product_services,
                valid_to=:valid_to, 
                ps_adult_cost=:ps_adult_cost,
                ps_teen_cost=:ps_teen_cost,
                ps_child_cost=:ps_child_cost,
                ps_infant_cost=:ps_infant_cost,
                id_currency=:id_currency,
                id_dept=:id_dept,
                WHERE id_product_services_cost=:id_product_services_cost";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_services_cost" => $id_product_services_cost,
            ":id_product_services" => $id_product_services,
            ":valid_from" => $valid_from, 
            ":valid_to" => $valid_to,
            ":ps_adult_cost" => $ps_adult_cost, 
            ":ps_teen_cost" => $ps_teen_cost, 
            ":ps_child_cost" => $ps_child_cost, 
            ":ps_infant_cost" => $ps_infant_cost, 
            ":id_currency" => $id_currency,
            ":id_dept" => $id_dept));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_services_cost"=>$id_product_services_cost));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
