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
    
    if (!isset($_GET["id_booking"])) {
        throw new Exception("INVALID ID". $_GET["id_booking"]);
    }
    
    $id_booking = $_GET["id_booking"];
	$id_user = $_SESSION["solis_userid"];
	$uname = $_SESSION["solis_username"];
	$log_status = "DELETE";
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();
	
	$stmt = $con->prepare("SELECT * FROM booking WHERE id_booking = :id_booking AND active = 1");
	$stmt->execute(array(":id_booking"=>$id_booking));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$to_ref = $row["to_ref"];
		$id_quote = $row["id_quote"];
		$id_tour_operator = $row["id_tour_operator"];
		$id_country = $row["id_country"];
		$id_dept = $row["id_dept"]; 
		$booking_for = $row["booking_for"];
		$pax_type = $row["pax_type"];
		$booking_from = $row["booking_from"];
		$booking_to = $row["booking_to"];
		$adult_amt = $row["adult_amt"];
		$teen_amt = $row["teen_amt"];
		$child_amt = $row["child_amt"];
		$infant_amt = $row["infant_amt"];
		$status = $row["status"];
		$opt_date = $row["opt_date"];
		$remarks = $row["remarks"];
		$invoice_no = $row["invoice_no"];
		$invoiced_date = $row["invoiced_date"];
		$close = $row["close"];
		$close_on = $row["close_on"];
		$created_by = $row["created_by"];
		$created_name = $row["created_name"];
	}  
	
    $stmt = $con->prepare("UPDATE booking SET active=0 WHERE id_booking = :id_booking");
    $stmt->execute(array(":id_booking"=>$id_booking));
	
	$sqlLog = "INSERT INTO booking_log ( 
		id_booking,
		id_quote,
		to_ref,
		id_tour_operator,
		id_country,
		id_dept,
		booking_for,
		pax_type,
		booking_from,
		booking_to,
		adult_amt,
		teen_amt,
		child_amt,
		infant_amt,
		opt_date,
		created_by,
		created_name,
		remarks,
		status,
		invoice_no,
		invoiced_date,
		close,
		close_on,
		id_user,
		uname,
		log_status
		) 
			VALUES (
				:id_booking,
				:id_quote,
				:to_ref,
				:id_tour_operator,
				:id_country,
				:id_dept,
				:booking_for,
				:pax_type,
				:booking_from,
				:booking_to,
				:adult_amt,
				:teen_amt,
				:child_amt,
				:infant_amt,
				:opt_date,
				:created_by,
				:created_name,
				:remarks,
				:status,
				:invoice_no,
				:invoiced_date,
				:close,
				:close_on,
				:id_user,
				:uname,
				:log_status
				)";

	
	$stmt = $con->prepare($sqlLog);
				$stmt->execute(array(
				":id_booking" => $id_booking,
				":id_quote" => $id_quote,
				":to_ref" => $to_ref,
				":id_tour_operator" => $id_tour_operator,
				":id_country" => $id_country,
				":id_dept" => $id_dept,
				":booking_for" => $booking_for,
				":pax_type" => $pax_type,
				":booking_from" => $booking_from,
				":booking_to" => $booking_to,
				":adult_amt" => $adult_amt,
				":teen_amt" => $teen_amt,
				":child_amt" => $child_amt,
				":infant_amt" => $infant_amt,
				":opt_date" => $opt_date,
				":created_by" => $created_by,
				":created_name" => $created_name,
				":remarks" => $remarks,
				":status" => $status,
				":invoice_no" => $invoice_no,
				":invoiced_date" => $invoiced_date,
				":close" => $close,
				":close_on" => $close_on,
				":id_user" => $id_user,
				":uname" => $uname,
				":log_status" => $log_status
			));
    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

echo json_encode(array("OUTCOME" => "OK"));
?>

