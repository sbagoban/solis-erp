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
        
        if (!isset($_GET["id_product_service"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product_service = $_GET["id_product_service"];
        $valid_from = trim($_POST["valid_from"]);
        $valid_to = trim($_POST["valid_to"]);
        $id_dept = trim($_POST["id_dept"]);
        $id_country = trim($_POST["id_country"]);
        $id_coast = trim($_POST["id_coast"]);
        $service_name = trim($_POST["service_name"]);
        $id_tax = trim($_POST["id_tax"]);
        $charge = trim($_POST["charge"]);
        $duration = trim($_POST["duration"]);
        $transfer_included = trim($_POST["transfer_included"]);
        $description = trim($_POST["description"]);
        $comments = trim($_POST["comments"]);
        $on_monday = trim($_POST["on_monday"]);
        $on_tuesday = trim($_POST["on_tuesday"]);
        $on_wednesday = trim($_POST["on_wednesday"]);
        $on_thursday = trim($_POST["on_thursday"]);
        $on_friday = trim($_POST["on_friday"]);
        $on_saturday = trim($_POST["on_saturday"]);
        $on_sunday = trim($_POST["on_sunday"]);
        $cancellation = trim($_POST["cancellation"]);
        $age_inf_to = trim($_POST["age_inf_to"]);
        $age_child_to = trim($_POST["age_child_to"]);
        $age_teen_to = trim($_POST["age_teen_to"]);
        $min_pax = trim($_POST["min_pax"]);
        $max_pax = trim($_POST["max_pax"]);
        $id_creditor = trim($_POST["id_creditor"]);        
        $age_inf_from = trim($_POST["age_inf_from"]);
        $age_child_from = trim($_POST["age_child_from"]);
        $age_teen_from = trim($_POST["age_teen_from"]);
        $for_infant = trim($_POST["for_infant"]);
        $for_child = trim($_POST["for_child"]);
        $for_teen = trim($_POST["for_teen"]);
        $for_adult = trim($_POST["for_adult"]);
		
        $min_age = trim($_POST["min_age"]);
        $max_age = trim($_POST["max_age"]);

		if ($age_inf_to == "") 
		{
			$age_inf_from = NULL;
			$age_inf_to = NULL;
		}

		if ($age_child_to == "") 
		{
			$age_child_from = NULL;
			$age_child_to = NULL;
		}
		if ($age_teen_to == "") 
		{
			$age_teen_from = NULL;
			$age_teen_to = NULL;
		}
        if ($min_pax == "") 
        {
            $min_pax = NULL;
        }
        if ($max_pax == "") 
        {
            $max_pax = NULL;
        }
        $con = pdo_con();
        $sql = "UPDATE product_service SET 
                valid_from =:valid_from,
                valid_to =:valid_to,
                id_dept =:id_dept,
                id_country =:id_country,
                id_coast =:id_coast,
                service_name =:service_name,
                id_tax =:id_tax,
                charge =:charge,
                duration =:duration,
                transfer_included =:transfer_included,
                description =:description,
                comments =:comments,
                on_monday =:on_monday,
                on_tuesday =:on_tuesday,
                on_wednesday =:on_wednesday,
                on_thursday =:on_thursday,
                on_friday =:on_friday,
                on_saturday =:on_saturday,
                on_sunday =:on_sunday,
                cancellation =:cancellation,
                age_inf_to =:age_inf_to,
                age_child_to =:age_child_to,
                age_teen_to =:age_teen_to,
                min_pax =:min_pax,
                max_pax =:max_pax,
                id_creditor =:id_creditor,
                for_infant =:for_infant,
                for_child =:for_child,
                for_teen =:for_teen,
                age_inf_from =:age_inf_from,
                age_child_from =:age_child_from,
                age_teen_from =:age_teen_from,
                min_age =:min_age,
                max_age =:max_age,
                for_adult =:for_adult
                WHERE id_product_service=:id_product_service";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product_service" => $id_product_service,
                ":valid_from" => $valid_from,
                ":valid_to" => $valid_to,
                ":id_dept" => $id_dept,
                ":id_country" => $id_country,
                ":id_coast" => $id_coast,
                ":service_name" => $service_name,
                ":id_tax" => $id_tax,
                ":charge" => $charge,
                ":duration" => $duration,
                ":transfer_included" => $transfer_included,
                ":description" => $description,
                ":comments" => $comments,
                ":on_monday" => $on_monday,
                ":on_tuesday" => $on_tuesday,
                ":on_wednesday" => $on_wednesday,
                ":on_thursday" => $on_thursday,
                ":on_friday" => $on_friday,
                ":on_saturday" => $on_saturday,
                ":on_sunday" => $on_sunday,
                ":cancellation" => $cancellation,
                ":age_inf_to" => $age_inf_to,
                ":age_child_to" => $age_child_to,
                ":age_teen_to" => $age_teen_to,
                ":min_pax" => $min_pax,
                ":max_pax" => $max_pax,
                ":id_creditor" => $id_creditor,
                ":for_infant" => $for_infant,
                ":for_child" => $for_child,
                ":for_teen" => $for_teen,
                ":age_inf_from" => $age_inf_from,
                ":age_child_from" => $age_child_from,
                ":age_teen_from" => $age_teen_from,
                ":min_age" => $min_age,
                ":max_age" => $max_age,
                ":for_adult" => $for_adult));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
