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
	
    $con = pdo_con();
   
	$id_booking_activity = $_POST["id_booking_activity"];
    $id_booking = $_POST["id_booking"];
    $activity_service_paid_by = trim($_POST["activity_service_paid_by"]);
    $id_tour_operator = trim($_POST["id_tour_operator"]);
    $id_client = $_POST["id_client"];
    $activity_date = trim($_POST["activity_date"]);
    $activity_time = trim($_POST["activity_time"]);    
    $activity_booking_date = trim($_POST["activity_booking_date"]);
    $id_product = trim($_POST["id_product"]);
    $id_product_service = trim($_POST["id_product_service"]);
	$id_product_service_claim = trim($_POST["id_product_service_claim"]);
    $booking_client = $_POST["booking_client"];
	
	$qry_bookingDetails = $con->prepare("
		SELECT * FROM booking WHERE id_booking = :id_booking AND active =1");

	$qry_bookingDetails->execute(array(":id_booking"=>$id_booking));

	$row_count_bookingDetails = $qry_bookingDetails->rowCount();
	
	if ($row_count_bookingDetails > 0) 
	{
		while ($row = $qry_bookingDetails->fetch(PDO::FETCH_ASSOC))
		{
			$id_dept = $row["id_dept"];
		}
	}
	
	$qry_activityDetails = $con->prepare("
		SELECT 
			PS.service_name,
			PS.duration,
			PS_CLAIM.id_currency AS id_product_service_claim_cur,
			PS_CLAIM.id_dept,
			PS_CLAIM.charge,
			PS_CLAIM.ps_adult_claim,
			PS_CLAIM.ps_teen_claim,
			PS_CLAIM.ps_child_claim,
			PS_CLAIM.ps_infant_claim,
			PS_COST.id_product_service_cost,
			PS_COST.id_currency AS id_product_service_cost_cur,
			PS_COST.ps_adult_cost,
			PS_COST.ps_teen_cost,
			PS_COST.ps_child_cost,
			PS_COST.ps_infant_cost
		FROM
			product_service_claim PS_CLAIM,
			product_service_cost PS_COST,
			product_service PS
		WHERE PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
		AND PS_CLAIM.id_product_service = PS.id_product_service
		AND PS_CLAIM.id_product_service_claim = :id_product_service_claim 
		AND PS_CLAIM.active = 1
		AND PS_COST.active = 1
		AND PS.active =1");
	
		$qry_activityDetails->execute(array(":id_product_service_claim"=>$id_product_service_claim));

		$row_count_activityDetails = $qry_activityDetails->rowCount();

		if ($row_count_activityDetails > 0) 
		{
			while ($row = $qry_activityDetails->fetch(PDO::FETCH_ASSOC))
			{
				$activity_name = $row["service_name"];
				$activity_duration = $row["duration"];
                if($activity_duration == ' ' || $activity_duration == '')
                {
                    $activity_duration = null;
                }
				$activity_adult_amt = trim($_POST["activity_adult_amt"]);
				$activity_teen_amt = trim($_POST["activity_teen_amt"]);
				$activity_child_amt = trim($_POST["activity_child_amt"]);
				$activity_infant_amt = trim($_POST["activity_infant_amt"]);
				$activity_total_pax = trim($_POST["activity_total_pax"]);
				$id_product_service_claim_cur = $row["id_product_service_claim_cur"];
				$activity_claim_dept = $row["id_product_service_claim_cur"];
				$activity_charge = $row["charge"];
				if ($activity_charge == "PAX")
				{
                    if($row["ps_adult_claim"] == null)
                    {
                        $activity_adult_claim = 0;
                    }
                    else
                    {
                        $activity_adult_claim = $row["ps_adult_claim"];
                    }
                    
                    if($row["ps_teen_claim"] == null)
                    {
                        $activity_teen_claim = 0;
                    }
                    else
                    {
                        $activity_teen_claim = $row["ps_teen_claim"];
                    }
                    
                    if($row["ps_child_claim"] == null)
                    {
                        $activity_child_claim = 0;
                    }
                    else
                    {
                        $activity_child_claim = $row["ps_child_claim"];
                    }
                    
                    if($row["ps_infant_claim"] == null)
                    {
                        $activity_infant_claim = 0;
                    }
                    else
                    {
                        $activity_infant_claim = $row["ps_infant_claim"];
                    }
                    
					$activity_total_claim = (($activity_adult_amt * $activity_adult_claim) +($activity_teen_amt * $activity_teen_claim) + ($activity_child_amt * $activity_child_claim) +($activity_infant_amt * $activity_infant_claim));
                    
                    if($row["ps_adult_cost"] == null)
                    {
                        $activity_adult_cost = 0;
                    }
                    else
                    {
                        $activity_adult_cost = $row["ps_adult_cost"];
                    }
                    
                    if($row["ps_teen_claim"] == null)
                    {
                        $activity_teen_cost = 0;
                    }
                    else
                    {
                        $activity_teen_cost = $row["ps_teen_cost"];
                    }
                    
                    if($row["ps_child_cost"] == null)
                    {
                        $activity_child_cost = 0;
                    }
                    else
                    {
                        $activity_child_cost = $row["ps_child_cost"];
                    }
                    
                    if($row["ps_infant_cost"] == null)
                    {
                        $activity_infant_cost = 0;
                    }
                    else
                    {
                        $activity_infant_cost = $row["ps_infant_cost"];
                    }
                    
					$activity_total_cost = (($activity_adult_amt * $activity_adult_cost) +($activity_teen_amt * $activity_teen_cost) + ($activity_child_amt * $activity_child_cost) +($activity_infant_amt * $activity_infant_cost));
				}
				else
				{
					$activity_adult_claim = $row["ps_adult_claim"];
					$activity_teen_claim = 0;
					$activity_child_claim = 0;
					$activity_infant_claim = 0;
					$activity_total_claim = $row["ps_adult_claim"];
					$activity_adult_cost = $row["ps_adult_cost"];
					$activity_teen_cost = 0;
					$activity_child_cost = 0;
					$activity_infant_cost = 0;
					$activity_total_cost = $row["ps_adult_cost"];
				}
				$id_product_service_cost = $row["id_product_service_cost"];
				$id_product_service_cost_cur = $row["id_product_service_cost_cur"]; 
				$activity_rebate_type = trim($_POST["activity_rebate_type"]);
				$activity_rebate_approve_by = trim($_POST["activity_rebate_approve_by"]);
				$activity_discount_percentage = trim($_POST["activity_discount_percentage"]);
				$activity_adult_claim_after_disc = trim($_POST["activity_adult_claim_after_disc"]);
				$activity_teen_claim_after_disc = trim($_POST["activity_teen_claim_after_disc"]);
				$activity_child_claim_after_disc = trim($_POST["activity_child_claim_after_disc"]);
				$activity_infant_claim_after_disc = trim($_POST["activity_infant_claim_after_disc"]);
				if ($activity_rebate_type == 'Percentage')
				{
					$activity_total_claim_after_disc = $activity_total_claim * ((100-$activity_discount_percentage)/100);
				}
				else if ($activity_rebate_type == 'Fixed Tariff')
				{
					$activity_total_claim_after_disc = (($activity_adult_amt * $activity_adult_claim_after_disc) +($activity_teen_amt * $activity_teen_claim_after_disc) + ($activity_child_amt * $activity_child_claim_after_disc) +($activity_infant_amt * $activity_infant_claim_after_disc));
				}
				else if ($activity_rebate_type == 'FOC')
				{
					$activity_total_claim_after_disc = 0;
				}
				else 
				{
					$activity_total_claim_after_disc = $activity_total_claim;
				}
			}
		}
	$activity_remarks = trim($_POST["activity_remarks"]);
    $activity_internal_remarks = trim($_POST["activity_internal_remarks"]);
    $activity_status = trim($_POST["activity_status"]);
	$created_by = $_SESSION["solis_userid"];
	$created_name = $_SESSION["solis_username"];
	$id_user = $_SESSION["solis_userid"];
	$uname = $_SESSION["solis_username"];
	$log_status = "CREATE";
	
	
	if ($activity_booking_date == "") 
	{
		$activity_booking_date = date("Y-m-d");
	}
		
	if ($activity_time == '')
	{
		$activity_time = null;
	}	
	
    $con = pdo_con();

    //check duplicates for booking
    $sql = "SELECT * FROM booking_activity WHERE id_booking_activity = :id_booking_activity";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id_booking_activity" => $id_booking_activity));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }

	$sqlSaveActivity= "INSERT INTO booking_activity
			(
				id_booking,
				activity_service_paid_by,
				id_tour_operator,
				id_client,
				activity_date,
				activity_time,
				activity_booking_date,
				id_product,
				id_product_service,
				activity_name,
				activity_duration,
				activity_adult_amt,
				activity_teen_amt,
				activity_child_amt,
				activity_infant_amt,
				activity_total_pax,
				id_product_service_claim,
				id_product_service_claim_cur,
				id_dept,
				activity_claim_dept,
				activity_charge,
				activity_adult_claim,
				activity_teen_claim,
				activity_child_claim,
				activity_infant_claim,
				activity_total_claim,
				id_product_service_cost,
				id_product_service_cost_cur,
				activity_adult_cost,
				activity_teen_cost,
				activity_child_cost,
				activity_infant_cost,
				activity_total_cost,
				activity_rebate_type,
				activity_rebate_approve_by,
				activity_discount_percentage,
				activity_adult_claim_after_disc,
				activity_teen_claim_after_disc,
				activity_child_claim_after_disc,
				activity_infant_claim_after_disc,
				activity_total_claim_after_disc,
				activity_remarks,
				activity_internal_remarks,
				activity_status,
				created_by,
				created_name
			)
			VALUES
			(
				:id_booking,
				:activity_service_paid_by,
				:id_tour_operator,
				:id_client,
				:activity_date,
				:activity_time,
				:activity_booking_date,
				:id_product,
				:id_product_service,
				:activity_name,
				:activity_duration,
				:activity_adult_amt,
				:activity_teen_amt,
				:activity_child_amt,
				:activity_infant_amt,
				:activity_total_pax,
				:id_product_service_claim,
				:id_product_service_claim_cur,
				:id_dept,
				:activity_claim_dept,
				:activity_charge,
				:activity_adult_claim,
				:activity_teen_claim,
				:activity_child_claim,
				:activity_infant_claim,
				:activity_total_claim,
				:id_product_service_cost,
				:id_product_service_cost_cur,
				:activity_adult_cost,
				:activity_teen_cost,
				:activity_child_cost,
				:activity_infant_cost,
				:activity_total_cost,
				:activity_rebate_type,
				:activity_rebate_approve_by,
				:activity_discount_percentage,
				:activity_adult_claim_after_disc,
				:activity_teen_claim_after_disc,
				:activity_child_claim_after_disc,
				:activity_infant_claim_after_disc,
				:activity_total_claim_after_disc,
				:activity_remarks,
				:activity_internal_remarks,
				:activity_status,
				:created_by,
				:created_name
			)";

	$stmt = $con->prepare($sqlSaveActivity);
				$stmt->execute(array(
				":id_booking" => $id_booking,
				":activity_service_paid_by" => $activity_service_paid_by,
				":id_tour_operator" => $id_tour_operator,
				":id_client" => $id_client,
				":activity_date" => $activity_date,
				":activity_time" => $activity_time,
				":activity_booking_date" => $activity_booking_date,
				":id_product" => $id_product,
				":id_product_service" => $id_product_service,
				":activity_name" => $activity_name,
				":activity_duration" => $activity_duration,
				":activity_adult_amt" => $activity_adult_amt,
				":activity_teen_amt" => $activity_teen_amt,
				":activity_child_amt" => $activity_child_amt,
				":activity_infant_amt" => $activity_infant_amt,
				":activity_total_pax" => $activity_total_pax,
				":id_product_service_claim" => $id_product_service_claim,
				":id_product_service_claim_cur" => $id_product_service_claim_cur,
				":id_dept" => $id_dept,
				":activity_claim_dept" => $activity_claim_dept,
				":activity_charge" => $activity_charge,
				":activity_adult_claim" => $activity_adult_claim,
				":activity_teen_claim" => $activity_teen_claim,
				":activity_child_claim" => $activity_child_claim,
				":activity_infant_claim" => $activity_infant_claim,
				":activity_total_claim" => $activity_total_claim,
				":id_product_service_cost" => $id_product_service_cost,
				":id_product_service_cost_cur" => $id_product_service_cost_cur,
				":activity_adult_cost" => $activity_adult_cost,
				":activity_teen_cost" => $activity_teen_cost,
				":activity_child_cost" => $activity_child_cost,
				":activity_infant_cost" => $activity_infant_cost,
				":activity_total_cost" => $activity_total_cost,
				":activity_rebate_type" => $activity_rebate_type,
				":activity_rebate_approve_by" => $activity_rebate_approve_by,
				":activity_discount_percentage" => $activity_discount_percentage,
				":activity_adult_claim_after_disc" => $activity_adult_claim_after_disc,
				":activity_teen_claim_after_disc" => $activity_teen_claim_after_disc,
				":activity_child_claim_after_disc" => $activity_child_claim_after_disc,
				":activity_infant_claim_after_disc" => $activity_infant_claim_after_disc,
				":activity_total_claim_after_disc" => $activity_total_claim_after_disc,
				":activity_remarks" => $activity_remarks,
				":activity_internal_remarks" => $activity_internal_remarks,
				":activity_status" => $activity_status,
				":created_by" => $created_by,
				":created_name" => $created_name
			));

	$id_booking_activity = $con->lastInsertId();    

    // CLIENT ACTIVITY
    $sqlClientActivity = "INSERT INTO booking_activity_client (id_client, id_booking_activity,id_booking) 
    VALUES (:booking_client, :id_booking_activity,:id_booking)";

    $stmt = $con->prepare($sqlClientActivity);
    $data = $booking_client;

    foreach($data as $d) {
        $stmt->execute(array(':id_booking_activity' => $id_booking_activity,':id_booking' => $id_booking, ':booking_client' => $d));
    }
    
	// BOOKING ACTIVITY LOG

	$sqlSaveActivity= "INSERT INTO booking_activity_log
			(
				id_booking_activity,
				id_booking,
				activity_service_paid_by,
				id_tour_operator,
				id_client,
				activity_date,
				activity_time,
				activity_booking_date,
				id_product,
				id_product_service,
				activity_name,
				activity_duration,
                activity_clients,
				activity_adult_amt,
				activity_teen_amt,
				activity_child_amt,
				activity_infant_amt,
				activity_total_pax,
				id_product_service_claim,
				id_product_service_claim_cur,
				id_dept,
				activity_claim_dept,
				activity_charge,
				activity_adult_claim,
				activity_teen_claim,
				activity_child_claim,
				activity_infant_claim,
				activity_total_claim,
				id_product_service_cost,
				id_product_service_cost_cur,
				activity_adult_cost,
				activity_teen_cost,
				activity_child_cost,
				activity_infant_cost,
				activity_total_cost,
				activity_rebate_type,
				activity_rebate_approve_by,
				activity_discount_percentage,
				activity_adult_claim_after_disc,
				activity_teen_claim_after_disc,
				activity_child_claim_after_disc,
				activity_infant_claim_after_disc,
				activity_total_claim_after_disc,
				activity_remarks,
				activity_internal_remarks,
				activity_status,
				id_user,
				uname,
				log_status
			)
			VALUES
			(
				:id_booking_activity,
				:id_booking,
				:activity_service_paid_by,
				:id_tour_operator,
				:id_client,
				:activity_date,
				:activity_time,
				:activity_booking_date,
				:id_product,
				:id_product_service,
				:activity_name,
				:activity_duration,
				:activity_clients,
				:activity_adult_amt,
				:activity_teen_amt,
				:activity_child_amt,
				:activity_infant_amt,
				:activity_total_pax,
				:id_product_service_claim,
				:id_product_service_claim_cur,
				:id_dept,
				:activity_claim_dept,
				:activity_charge,
				:activity_adult_claim,
				:activity_teen_claim,
				:activity_child_claim,
				:activity_infant_claim,
				:activity_total_claim,
				:id_product_service_cost,
				:id_product_service_cost_cur,
				:activity_adult_cost,
				:activity_teen_cost,
				:activity_child_cost,
				:activity_infant_cost,
				:activity_total_cost,
				:activity_rebate_type,
				:activity_rebate_approve_by,
				:activity_discount_percentage,
				:activity_adult_claim_after_disc,
				:activity_teen_claim_after_disc,
				:activity_child_claim_after_disc,
				:activity_infant_claim_after_disc,
				:activity_total_claim_after_disc,
				:activity_remarks,
				:activity_internal_remarks,
				:activity_status,
				:id_user,
				:uname,
				:log_status
			)";

	$stmt = $con->prepare($sqlSaveActivity);
				$stmt->execute(array(
				":id_booking_activity" => $id_booking_activity,
				":id_booking" => $id_booking,
				":activity_service_paid_by" => $activity_service_paid_by,
				":id_tour_operator" => $id_tour_operator,
				":id_client" => $id_client,
				":activity_date" => $activity_date,
				":activity_time" => $activity_time,
				":activity_booking_date" => $activity_booking_date,
				":id_product" => $id_product,
				":id_product_service" => $id_product_service,
				":activity_name" => $activity_name,
				":activity_duration" => $activity_duration,
				":activity_clients" => implode( ", ", $booking_client ),
				":activity_adult_amt" => $activity_adult_amt,
				":activity_teen_amt" => $activity_teen_amt,
				":activity_child_amt" => $activity_child_amt,
				":activity_infant_amt" => $activity_infant_amt,
				":activity_total_pax" => $activity_total_pax,
				":id_product_service_claim" => $id_product_service_claim,
				":id_product_service_claim_cur" => $id_product_service_claim_cur,
				":id_dept" => $id_dept,
				":activity_claim_dept" => $activity_claim_dept,
				":activity_charge" => $activity_charge,
				":activity_adult_claim" => $activity_adult_claim,
				":activity_teen_claim" => $activity_teen_claim,
				":activity_child_claim" => $activity_child_claim,
				":activity_infant_claim" => $activity_infant_claim,
				":activity_total_claim" => $activity_total_claim,
				":id_product_service_cost" => $id_product_service_cost,
				":id_product_service_cost_cur" => $id_product_service_cost_cur,
				":activity_adult_cost" => $activity_adult_cost,
				":activity_teen_cost" => $activity_teen_cost,
				":activity_child_cost" => $activity_child_cost,
				":activity_infant_cost" => $activity_infant_cost,
				":activity_total_cost" => $activity_total_cost,
				":activity_rebate_type" => $activity_rebate_type,
				":activity_rebate_approve_by" => $activity_rebate_approve_by,
				":activity_discount_percentage" => $activity_discount_percentage,
				":activity_adult_claim_after_disc" => $activity_adult_claim_after_disc,
				":activity_teen_claim_after_disc" => $activity_teen_claim_after_disc,
				":activity_child_claim_after_disc" => $activity_child_claim_after_disc,
				":activity_infant_claim_after_disc" => $activity_infant_claim_after_disc,
				":activity_total_claim_after_disc" => $activity_total_claim_after_disc,
				":activity_remarks" => $activity_remarks,
				":activity_internal_remarks" => $activity_internal_remarks,
				":activity_status" => $activity_status,
				":id_user" => $id_user,
				":uname" => $uname,
				":log_status" => $log_status
			));	
	$bookingActivity_result= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_activity"=>$id_booking_activity, "created_by" =>$created_name);
    echo json_encode($bookingActivity_result);
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>
