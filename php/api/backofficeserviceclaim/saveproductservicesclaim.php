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
    
    $id_product_service_claim = $_POST["id_product_service_claim"];
    $id_product_service_cost = trim($_POST["id_product_service_cost"]);
    $id_product_service = trim($_POST["id_product_service"]);
    $valid_from = trim($_POST["valid_from"]);
    $valid_to = trim($_POST["valid_to"]);
    $id_dept = trim($_POST["id_dept"]);
    $specific_to = trim($_POST["specific_to"]);
    $charge = trim($_POST["charge"]);
    $ps_adult_claim = trim($_POST["ps_adult_claim"]);
    $ps_teen_claim = trim($_POST["ps_teen_claim"]);
    $ps_child_claim = trim($_POST["ps_child_claim"]);
    $ps_infant_claim = trim($_POST["ps_infant_claim"]);
    $id_currency = trim($_POST["id_currency"]);
    $currency = trim($_POST["currency"]);
    $ex_monday = trim($_POST["ex_monday"]);
    $ex_tuesday = trim($_POST["ex_tuesday"]);
    $ex_wednesday = trim($_POST["ex_wednesday"]);
    $ex_thursday = trim($_POST["ex_thursday"]);
    $ex_friday = trim($_POST["ex_friday"]);
    $ex_saturday = trim($_POST["ex_saturday"]);
    $ex_sunday = trim($_POST["ex_sunday"]);
    $id_country = $_POST["id_country"];
    $id_tour_operator = $_POST["id_tour_operator"];
    $specific_to_name = $_POST["specific_to_name"];

	
	if ($ps_adult_claim == "") 
	{
		$ps_adult_claim = NULL;
	}
	
	if ($ps_teen_claim == "") 
	{
		$ps_teen_claim = NULL;
	}
	if ($ps_child_claim == "") 
	{
		$ps_child_claim = NULL;
	}
	if ($ps_infant_claim == "") 
	{
		$ps_infant_claim = NULL;
	}
	
	
    $con = pdo_con();

    //check duplicates for service
    $sql = "SELECT * FROM product_service_claim WHERE id_product_service_claim = :id_product_service_claim";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_product_service_claim" => $id_product_service_claim));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

    if ($id_product_service_claim == "-1") {
        $sql = "INSERT INTO product_service_claim (
            id_product_service_cost, 
            id_product_service, 
            valid_from, 
            valid_to,
            id_dept,
            specific_to,
            charge,
            ps_adult_claim,
            ps_teen_claim,
            ps_child_claim,
            ps_infant_claim,
            id_currency,
            currency,
            ex_monday,
            ex_tuesday,
            ex_wednesday,
            ex_thursday,
            ex_friday,
            ex_saturday,
            ex_sunday,
            specific_to_name
            ) 
                VALUES (
                    :id_product_service_cost, 
                    :id_product_service, 
                    :valid_from, 
                    :valid_to,
                    :id_dept,
                    :specific_to,
                    :charge,
                    :ps_adult_claim,
                    :ps_teen_claim,
                    :ps_child_claim,
                    :ps_infant_claim,
                    :id_currency,
                    :currency,
                    :ex_monday,
                    :ex_tuesday,
                    :ex_wednesday,
                    :ex_thursday,
                    :ex_friday,
                    :ex_saturday,
                    :ex_sunday,
                    :specific_to_name)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_cost" => $id_product_service_cost, 
            ":id_product_service" => $id_product_service, 
            ":valid_from" => $valid_from, 
            ":valid_to" => $valid_to,
            ":id_dept" => $id_dept,
            ":specific_to" => $specific_to,
            ":charge" => $charge,
            ":ps_adult_claim" => $ps_adult_claim,
            ":ps_teen_claim" => $ps_teen_claim,
            ":ps_child_claim" => $ps_child_claim,
            ":ps_infant_claim" => $ps_infant_claim,
            ":id_currency" => $id_currency,
            ":currency" => $currency,
            ":ex_monday" => $ex_monday,
            ":ex_tuesday" => $ex_tuesday,
            ":ex_wednesday" => $ex_wednesday,
            ":ex_thursday" => $ex_thursday,
            ":ex_friday" => $ex_friday,
            ":ex_saturday" => $ex_saturday,
            ":ex_sunday" => $ex_sunday,
            ":specific_to_name" => $specific_to_name));
        
            $id_product_service_claim = $con->lastInsertId();

            if ($specific_to == 'A') {
                $sqlTo = "INSERT INTO product_service_claim_to (id_product_service_claim,id_tour_operator) 
                VALUES (:id_product_service_claim,:id_tour_operator)";

                $stmt = $con->prepare($sqlTo);
                $data = $id_tour_operator;
                
                foreach($data as $to) {
                    $stmt->execute(array(':id_product_service_claim' => $id_product_service_claim, ':id_tour_operator' => $to));
                }
            } if($specific_to == 'C') {
                $sqlMarket = "INSERT INTO product_service_claim_country (id_product_service_claim,id_country) 
                VALUES (:id_product_service_claim,:id_country)";

                $stmt = $con->prepare($sqlMarket);
                $data = $id_country;
                
                foreach($data as $d) {
                    $stmt->execute(array(':id_product_service_claim' => $id_product_service_claim, ':id_country' => $d));
                }
            }
            
    } 
    else {
        $sql = "UPDATE product_service_claim SET 
                id_product_service_cost=:id_product_service_cost, 
                id_product_service=:id_product_service, 
                valid_from=:valid_from, 
                valid_to=:valid_to,
                id_dept=:id_dept,
                specific_to=:specific_to,
                charge=:charge,
                ps_adult_claim=:ps_adult_claim,
                ps_teen_claim=:ps_teen_claim,
                ps_child_claim=:ps_child_claim,
                ps_infant_claim=:ps_infant_claim,
                id_currency=:id_currency,
                currency=:currency,
                ex_monday=:ex_monday,
                ex_tuesday=:ex_tuesday,
                ex_wednesday=:ex_wednesday,
                ex_thursday=:ex_thursday,
                ex_friday=:ex_friday,
                ex_saturday=:ex_saturday,
                ex_sunday=:ex_sunday,
                specific_to_name=:specific_to_name
                WHERE id_product_service_claim=:id_product_service_claim";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(
            ":id_product_service_cost" => $id_product_service_cost, 
            ":id_product_service" => $id_product_service, 
            ":valid_from" => $valid_from, 
            ":valid_to" => $valid_to,
            ":id_dept" => $id_dept,
            ":specific_to" => $specific_to,
            ":charge" => $charge,
            ":ps_adult_claim" => $ps_adult_claim,
            ":ps_teen_claim" => $ps_teen_claim,
            ":ps_child_claim" => $ps_child_claim,
            ":ps_infant_claim" => $ps_infant_claim,
            ":id_currency" => $id_currency,
            ":currency" => $currency,
            ":ex_monday" => $ex_monday,
            ":ex_tuesday" => $ex_tuesday,
            ":ex_wednesday" => $ex_wednesday,
            ":ex_thursday" => $ex_thursday,
            ":ex_friday" => $ex_friday,
            ":ex_saturday" => $ex_saturday,
            ":ex_sunday" => $ex_sunday,
            ":specific_to_name" => $specific_to_name));
    }
    echo json_encode(array("OUTCOME" => "OK", "id_product_service_claim"=>$id_product_service_claim));
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>
