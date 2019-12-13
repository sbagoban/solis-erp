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
    
    if (!isset($_GET["id_product_service"])) {
        throw new Exception("INVALID ID". $_GET["id_product_service"]);
    }
    
    $id_product_service = $_GET["id_product_service"];
    
    $id_user = $_SESSION["solis_userid"];
    $uname = $_SESSION["solis_username"];
    $log_status = "DELETE";
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $stmt = $con->prepare("SELECT * FROM product_service WHERE id_product_service = :id_product_service AND active = 1");
	$stmt->execute(array(":id_product_service"=>$id_product_service));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$id_product = $row["id_product"];
        $valid_from = $row["valid_from"];
        $valid_to = $row["valid_to"];
        $id_dept = $row["id_dept"];
        $id_country = $row["id_country"];
        $id_coast = $row["id_coast"];
        $service_name = $row["service_name"];
        $id_tax = $row["id_tax"];
        $charge = $row["charge"];
        $duration = $row["duration"];
        $transfer_included = $row["transfer_included"];
        $description = $row["description"];
        $comments = $row["comments"];
        $on_monday = $row["on_monday"];
        $on_tuesday = $row["on_tuesday"];
        $on_wednesday = $row["on_wednesday"];
        $on_thursday = $row["on_thursday"];
        $on_friday = $row["on_friday"];
        $on_saturday = $row["on_saturday"];
        $on_sunday = $row["on_sunday"];
        $cancellation = $row["cancellation"];
        $age_inf_to = $row["age_inf_to"];
        $age_child_to = $row["age_child_to"];
        $age_teen_to = $row["age_teen_to"];
        $age_inf_from = $row["age_inf_from"];
        $age_child_from = $row["age_child_from"];
        $age_teen_from = $row["age_teen_from"];
        $min_pax = $row["min_pax"];
        $max_pax = $row["max_pax"];
        $id_creditor = $row["id_creditor"];
        $for_infant = $row["for_infant"];
        $for_child = $row["for_child"];
        $for_teen = $row["for_teen"];
        $min_age = $row["min_age"];
        $max_age = $row["max_age"];
        $is_pakage = $row["is_pakage"]; 
        $special_name = $row["special_name"];
    }

    $stmt = $con->prepare("UPDATE product_service SET active=0 WHERE id_product_service = :id_product_service");
    $stmt->execute(array(":id_product_service"=>$id_product_service));

    $stmt2 = $con->prepare("UPDATE product_service_cost SET active=0 WHERE id_product_service = :id_product_service");
    $stmt2->execute(array(":id_product_service"=>$id_product_service));

    $stmt3 = $con->prepare("UPDATE product_service_claim SET active=0 WHERE id_product_service = :id_product_service");
    $stmt3->execute(array(":id_product_service"=>$id_product_service));

    
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
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME" => "ERROR"));
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>

