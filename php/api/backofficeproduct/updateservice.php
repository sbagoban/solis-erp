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
        
        if (!isset($_GET["id_product_services"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id_product_services = $_GET["id_product_services"];
        $valid_from = trim($_POST["valid_from"]);
        $valid_to = trim($_POST["valid_to"]);
        $id_dept = trim($_POST["id_dept"]);
        $id_countries = trim($_POST["id_countries"]);
        $id_coasts = trim($_POST["id_coasts"]);
        $service_name = trim($_POST["service_name"]);
        $id_tax = trim($_POST["id_tax"]);
        $charges = trim($_POST["charges"]);
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

        $con = pdo_con();
        $sql = "UPDATE product_services SET 
                valid_from =:valid_from,
                valid_to =:valid_to,
                id_dept =:id_dept,
                id_countries =:id_countries,
                id_coasts =:id_coasts,
                service_name =:service_name,
                id_tax =:id_tax,
                charges =:charges,
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
                max_pax =:max_pax
                WHERE id_product_services=:id_product_services";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id_product_services" => $id_product_services,
                ":valid_from" => $valid_from,
                ":valid_to" => $valid_to,
                ":id_dept" => $id_dept,
                ":id_countries" => $id_countries,
                ":id_coasts" => $id_coasts,
                ":service_name" => $service_name,
                ":id_tax" => $id_tax,
                ":charges" => $charges,
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
                ":max_pax" => $max_pax));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
