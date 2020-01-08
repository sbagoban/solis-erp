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
    //================================================================================


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
    
    if (!isset($_GET["id_product_service_cost"])) {
        throw new Exception("INVALID ID". $_GET["id_product_service_cost"]);
    }
    
    $id_product_service_cost = $_GET["id_product_service_cost"];
    $id_user = $_SESSION["solis_userid"];
    $uname = $_SESSION["solis_username"];
    $log_status = "DELETE";
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
    $stmt = $con->prepare("SELECT * FROM product_service_cost WHERE id_product_service_cost = :id_product_service_cost AND active = 1");
	$stmt->execute(array(":id_product_service_cost"=>$id_product_service_cost));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id_product_service_cost = $row["id_product_service_cost"];
        $id_product_service = $row["id_product_service"]; 
        $valid_from = $row["valid_from"]; 
        $valid_to = $row["valid_to"]; 
        $id_dept = $row["id_dept"];
        $ps_adult_cost = $row["ps_adult_cost"]; 
        $ps_teen_cost = $row["ps_teen_cost"]; 
        $ps_child_cost = $row["ps_child_cost"]; 
        $ps_infant_cost = $row["ps_infant_cost"]; 
        $id_currency = $row["id_currency"]; 
        $currency = $row["currency"];
    }

    $stmt = $con->prepare("UPDATE product_service_cost SET active=0 WHERE id_product_service_cost = :id_product_service_cost");
    $stmt->execute(array(":id_product_service_cost"=>$id_product_service_cost));

    $stmt3 = $con->prepare("UPDATE product_service_claim SET active=0 WHERE id_product_service_cost = :id_product_service_cost");
    $stmt3->execute(array(":id_product_service_cost"=>$id_product_service_cost));

    $stmt4 = $con->prepare("UPDATE product_service_extra_cost SET active=0 WHERE id_product_service_cost = :id_product_service_cost");
    $stmt4->execute(array(":id_product_service_cost"=>$id_product_service_cost));

    $stmt5 = $con->prepare("UPDATE product_service_extra_claim SET active=0 WHERE id_product_service_cost = :id_product_service_cost");
    $stmt5->execute(array(":id_product_service_cost"=>$id_product_service_cost));
    
    $sqlLog = "INSERT INTO product_service_cost_log ( 
        id_product_service_cost,
        id_product_service, 
        valid_from, 
        valid_to, 
        id_dept,
        ps_adult_cost, 
        ps_teen_cost, 
        ps_child_cost, 
        ps_infant_cost, 
        id_currency, 
        currency, 
        id_user,
        uname,
        log_status
        ) 
            VALUES (
                :id_product_service_cost,
                :id_product_service, 
                :valid_from, 
                :valid_to, 
                :id_dept,
                :ps_adult_cost, 
                :ps_teen_cost, 
                :ps_child_cost, 
                :ps_infant_cost, 
                :id_currency, 
                :currency, 
                :id_user,
                :uname,
                :log_status
                )";
                
    $stmt = $con->prepare($sqlLog);
    $stmt->execute(array(
        ":id_product_service_cost" => $id_product_service_cost,
        ":id_product_service" => $id_product_service, 
        ":valid_from" => $valid_from, 
        ":valid_to" => $valid_to, 
        ":id_dept" => $id_dept,
        ":ps_adult_cost" => $ps_adult_cost, 
        ":ps_teen_cost" => $ps_teen_cost, 
        ":ps_child_cost" => $ps_child_cost, 
        ":ps_infant_cost" => $ps_infant_cost, 
        ":id_currency" => $id_currency, 
        ":currency" => $currency, 
        ":id_user" => $id_user,
        ":uname" => $uname,
        ":log_status" => $log_status
));
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME" => "ERROR"));
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>

