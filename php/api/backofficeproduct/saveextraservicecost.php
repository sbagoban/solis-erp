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
    
    $id_product_service_extra_cost = $_POST["id_product_service_extra_cost"];
    $id_product_service_extra = trim($_POST["id_product_service_extra"]);
    $id_product_service_cost = trim($_POST["id_product_service_cost"]);
    $id_product_service = trim($_POST["id_product_service"]);
    $valid_from = trim($_POST["valid_from"]);
    $valid_to = $_POST["valid_to"];
    $ps_adult_cost = trim($_POST["ps_adult_cost"]);
    $ps_teen_cost = trim($_POST["ps_teen_cost"]);
    $ps_child_cost = trim($_POST["ps_child_cost"]);
    $ps_infant_cost = trim($_POST["ps_infant_cost"]);
    $charge = trim($_POST["charge"]);
    $id_currency = trim($_POST["id_currency"]);
    $currency = trim($_POST["currency"]);
    $extra_name = trim($_POST["extra_name"]);

	if ($ps_teen_cost == "") 
	{
		$ps_teen_cost = 0;
	}
	
	if ($ps_child_cost == "") 
	{
		$ps_child_cost = 0;
	}
	if ($ps_infant_cost == "") 
	{
		$ps_infant_cost = 0;
	}

	
    $con = pdo_con();

    //check duplicates for service
    $sql = "SELECT * FROM product_service_extra_cost WHERE id_product_service_extra_cost = :id_product_service_extra_cost";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service_extra_cost" => $id_product_service_extra_cost));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_service_extra_cost == "-1") {
        $sql = "INSERT INTO product_service_extra_cost 
        (id_product_service_cost, id_product_service, id_product_service_extra, extra_name, valid_from, valid_to, ps_adult_cost, ps_teen_cost, ps_child_cost, ps_infant_cost, charge, id_currency, currency) 
                VALUES (:id_product_service_cost, :id_product_service, :id_product_service_extra, :extra_name, :valid_from, :valid_to, :ps_adult_cost, :ps_teen_cost, :ps_child_cost, :ps_infant_cost, :charge, :id_currency, :currency)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_cost" => $id_product_service_cost,
            ":id_product_service" => $id_product_service,
            ":id_product_service_extra" => $id_product_service_extra,
            ":extra_name" => $extra_name,
            ":valid_from" => $valid_from,
            ":valid_to" => $valid_to,
            ":ps_adult_cost" => $ps_adult_cost,
            ":ps_teen_cost" => $ps_teen_cost, 
            ":ps_child_cost" => $ps_child_cost,
            ":ps_infant_cost" => $ps_infant_cost, 
            ":charge" => $charge, 
            ":id_currency" => $id_currency,
            ":currency" => $currency));
        
        $id_product_service_extra_cost = $con->lastInsertId();
    } else {
        $sql = "UPDATE product_service_extra_cost SET 
                id_product_service_cost=:id_product_service_cost,
                id_product_service_extra=:id_product_service_extra,
                extra_name=:extra_name,
                id_product_service=:id_product_service,
                valid_from=:valid_from,
                valid_to=:valid_to,
                ps_adult_cost=:ps_adult_cost,
                ps_teen_cost=:ps_teen_cost,
                ps_child_cost=:ps_child_cost,
                ps_infant_cost=:ps_infant_cost,
                charge=:charge,
                id_currency=:id_currency, 
                currency=:currency
                WHERE id_product=:id_product";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_extra_cost" => $id_product_service_extra_cost,
            ":id_product_service_cost" => $id_product_service_cost,
            ":id_product_service_extra" => $id_product_service_extra,
            ":extra_name" => $extra_name,
            ":id_product_service" => $id_product_service, 
            ":valid_from" => $valid_from,            
            ":valid_to" => $valid_to,
            ":ps_adult_cost" => $ps_adult_cost,
            ":ps_teen_cost" => $ps_teen_cost, 
            ":ps_child_cost" => $ps_child_cost,
            ":ps_infant_cost" => $ps_infant_cost, 
            ":charge" => $charge, 
            ":id_currency" => $id_currency,
            ":currency" => $currency));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service_extra_cost"=>$id_product_service_extra_cost));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
