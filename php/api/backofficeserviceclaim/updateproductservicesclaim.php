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
            throw new Exception("INVALID TOKEN1");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            throw new Exception("INVALID TOKEN2");
        }
        
        if (!isset($_GET["id_product_service_claim"])) {
            throw new Exception("INVALID ID");
        }
          
        if (!isset($_GET["id_product_service_claim"])) {
            throw new Exception("INVALID ID". $_GET["id_product_service_claim"]);
        }
        
        $id_product_service_claim = $_GET["id_product_service_claim"];
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $valid_from = trim($_POST["valid_from"]);
        $valid_to = trim($_POST["valid_to"]);
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
        
        $rollover_type = $_POST["rollover_type"];
        $rollover_value = $_POST["rollover_value"];

        $ps_adult_claim_rollover = trim($_POST["ps_adult_claim_rollover"]);
        $ps_teen_claim_rollover = trim($_POST["ps_teen_claim_rollover"]);
        $ps_child_claim_rollover = trim($_POST["ps_child_claim_rollover"]);
        $ps_infant_claim_rollover = trim($_POST["ps_infant_claim_rollover"]);
		
		
		if ($ps_teen_claim == "") 
		{
			$ps_teen_claim = 0;
		}

		if ($ps_child_claim == "") 
		{
			$ps_child_claim = 0;
		}
		if ($ps_infant_claim == "") 
		{
			$ps_infant_claim = 0;
		}


        $con = pdo_con();
        $sql = "UPDATE product_service_claim SET 
                valid_from=:valid_from,
                valid_to=:valid_to,
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
                specific_to_name=:specific_to_name,
                rollover_type=:rollover_type,
                rollover_value=:rollover_value,                
                ps_adult_claim_rollover=:ps_adult_claim_rollover,
                ps_teen_claim_rollover=:ps_teen_claim_rollover,
                ps_child_claim_rollover=:ps_child_claim_rollover,
                ps_infant_claim_rollover=:ps_infant_claim_rollover
                WHERE id_product_service_claim=:id_product_service_claim";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
            ":id_product_service_claim" => $id_product_service_claim,
            ":valid_from" => $valid_from,
            ":valid_to" => $valid_to,
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
            ":specific_to_name" => $specific_to_name,
            ":rollover_type" => $rollover_type,
            ":rollover_value" => $rollover_value,
            ":ps_adult_claim_rollover" => $ps_adult_claim_rollover,
            ":ps_teen_claim_rollover" => $ps_teen_claim_rollover,
            ":ps_child_claim_rollover" => $ps_child_claim_rollover,
            ":ps_infant_claim_rollover" => $ps_infant_claim_rollover));

            if ($specific_to == 'A' || $specific_to == 'C') {
                $sqlToDelete = $con->prepare("DELETE FROM product_service_claim_to WHERE id_product_service_claim=:id_product_service_claim");
                $sqlToDelete->execute(array(":id_product_service_claim"=>$id_product_service_claim));
                $sqlMarketDelete = $con->prepare("DELETE FROM product_service_claim_country WHERE id_product_service_claim=:id_product_service_claim");
                $sqlMarketDelete->execute(array(":id_product_service_claim"=>$id_product_service_claim));
            }

            if ($specific_to == 'A') {
                $sqlTo = "INSERT INTO product_service_claim_to (id_product_service_claim,id_tour_operator) 
                VALUES (:id_product_service_claim,:id_tour_operator)";

                $stmt = $con->prepare($sqlTo);
                $data = $id_tour_operator;
                
                foreach($data as $to) {
                    $stmt->execute(array(':id_product_service_claim' => $id_product_service_claim, ':id_tour_operator' => $to));
                }
            } 
            if($specific_to == 'C') {
                $sqlMarket = "INSERT INTO product_service_claim_country (id_product_service_claim,id_country) 
                VALUES (:id_product_service_claim,:id_country)";

                $stmt = $con->prepare($sqlMarket);
                $data = $id_country;
                
                foreach($data as $d) {
                    $stmt->execute(array(':id_product_service_claim' => $id_product_service_claim, ':id_country' => $d));
                }
            }
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
