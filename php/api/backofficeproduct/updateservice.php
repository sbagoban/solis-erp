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
        $id_product = $_POST["id_product"];
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
        $max_adult = trim($_POST["max_adult"]);
        $is_pakage = trim($_POST["is_pakage"]);
        $special_name = strtoupper(trim($_POST["special_name"]));
        $servicetype = $_POST["servicetype"];
        $id_user = $_SESSION["solis_userid"];
        $uname = $_SESSION["solis_username"];
        $log_status = "UPDATE";
        $on_api = trim($_POST["on_api"]);
        $on_approved = trim($_POST["on_approved"]);

        if ($servicetype == 'TRANSFER') {
            $id_coast = 0;
            $duration = '00:00:00.00000';
            $id_creditor = 0; //id_creditor name should be Solis planning - to set in db 
            $min_age = 0;
            $max_age = 0;
        }

        if ($servicetype != 'TRANSFER') {
            $max_adult = 0;
        }

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
                id_product =:id_product,
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
                for_adult =:for_adult,
                is_pakage =:is_pakage, 
                special_name =:special_name,
                max_adult =:max_adult, 
                on_api =:on_api,
                on_approved =:on_approved
                WHERE id_product_service=:id_product_service";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product_service" => $id_product_service,
                ":id_product" => $id_product,
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
                ":for_adult" => $for_adult,
                ":is_pakage" => $is_pakage, 
                ":special_name" => $special_name, 
                ":max_adult" => $max_adult, 
                ":on_api" => $on_api, 
                ":on_approved" => $on_approved));
                
        if ($servicetype == 'TRANSFER') {            
            $sqlUpdateCostTransfer = "UPDATE product_service_cost SET 
                valid_from =:valid_from,
                valid_to =:valid_to,
                charge =:charge
                WHERE id_product_service=:id_product_service";
            $stmt2 = $con->prepare($sqlUpdateCostTransfer); 
            $stmt2->execute(array(
                ":id_product_service" => $id_product_service,
                ":valid_from" => $valid_from,
                ":valid_to" => $valid_to,
                ":charge" => $charge
            ));
        }
        
// Start Product Log
$sqlLog = "INSERT INTO product_service_log ( 
    id_product,
    valid_from,
    valid_to,
    id_dept,
    id_country,
    id_coast,
    service_name,
    id_tax,
    charge,
    duration,
    transfer_included,
    description,
    comments,
    on_monday,
    on_tuesday,
    on_wednesday,
    on_thursday,
    on_friday,
    on_saturday,
    on_sunday,
    cancellation,
    age_inf_to,
    age_child_to,
    age_teen_to,
    age_inf_from,
    age_child_from,
    age_teen_from,
    min_pax,
    max_pax,
    id_creditor,
    for_infant,
    for_child,
    for_teen,
    min_age,
    max_age,
    is_pakage,
    special_name,
    id_user,
    uname,
    log_status
    ) 
        VALUES (
            :id_product,
            :valid_from,
            :valid_to,
            :id_dept,
            :id_country,
            :id_coast,
            :service_name,
            :id_tax,
            :charge,
            :duration,
            :transfer_included,
            :description,
            :comments,
            :on_monday,
            :on_tuesday,
            :on_wednesday,
            :on_thursday,
            :on_friday,
            :on_saturday,
            :on_sunday,
            :cancellation,
            :age_inf_to,
            :age_child_to,
            :age_teen_to,
            :age_inf_from,
            :age_child_from,
            :age_teen_from,
            :min_pax,
            :max_pax,
            :id_creditor,
            :for_infant,
            :for_child,
            :for_teen,
            :min_age,
            :max_age,
            :is_pakage,
            :special_name,
            :id_user,
            :uname,
            :log_status
            )";

$stmt = $con->prepare($sqlLog);
            $stmt->execute(array(
                ":id_product" => $id_product,
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
                ":age_inf_from" => $age_inf_from,
                ":age_child_from" => $age_child_from,
                ":age_teen_from" => $age_teen_from,
                ":min_pax" => $min_pax,
                ":max_pax" => $max_pax,
                ":id_creditor" => $id_creditor,
                ":for_infant" => $for_infant,
                ":for_child" => $for_child,
                ":for_teen" => $for_teen,
                ":min_age" => $min_age,
                ":max_age" => $max_age,
                ":is_pakage" => $is_pakage,
                ":special_name" => $special_name,
                ":id_user" => $id_user,
                ":uname" => $uname,
                ":log_status" => $log_status
        ));

// End Of Log
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
