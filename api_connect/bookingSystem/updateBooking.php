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
    
        // TO BE UPDATED
        $_SESSION["solis_userid"] = 1;
        $_SESSION["id_tour_operator"] = 1;
        $_SESSION["id_country"] = 979;
        // TO BE UPDATED
        
        if (!isset($_SESSION["solis_userid"])) {
            throw new Exception("NO LOG IN!");
        }
        
        if (!isset($_GET["t"])) {
            throw new Exception("INVALID TOKEN1");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            throw new Exception("INVALID TOKEN2");
        }
        
        if (!isset($_GET["id_booking"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../php/connector/pdo_connect_main.php");

		$id_booking = $_POST["id_booking"];
        $to_ref = trim($_POST["booking_toRef"]);
        $id_tour_operator = $_SESSION["id_tour_operator"];
        $id_country = $_SESSION["id_country"];
		$booking_for = trim($_POST["booking_dossierName"]);
        $pax_type = trim($_POST["booking_clientType"]);
        $booking_from = trim($_POST["booking_from"]);
        $booking_to = trim($_POST["booking_to"]);
		$adult_amt = trim($_POST["booking_adultAmt"]);
        $teen_amt = trim($_POST["booking_teenAmt"]);
        $child_amt = trim($_POST["booking_childAmt"]);
        $infant_amt = trim($_POST["booking_infantAmt"]);
		$remarks = trim($_POST["booking_remarks"]);
        $id_user = $_SESSION["solis_userid"];
        $uname = $_SESSION["solis_username"];
		$log_status = "UPDATE";

		if ($teen_amt == "") 
		{
			$teen_amt = "0";
		}
		if ($child_amt == "") 
		{
			$child_amt = 0;
		}
		if ($infant_amt == "") 
		{
			$infant_amt = 0;
		}

        $con = pdo_con();
        
		$stmt = $con->prepare("
            SELECT * 
            FROM booking 
            WHERE id_booking = :id_booking
            AND id_tour_operator = :id_tour_operator
            AND active = 1");
		$stmt->execute(array(":id_booking"=>$id_booking,":id_tour_operator"=>$id_tour_operator));
		$row_count_c = $stmt->rowCount();
	
		if ($row_count_c > 0) {
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$created_by = $row["created_by"];
				$created_name = $row["created_name"];
                $status = $row["status"];
                $opt_date = $row["opt_date"];
                $id_dept = $row["id_dept"];    
			}  
		}
		else
		{
			$created_by = "";
			$created_name = "";
		}
        $sqlUpdate = "UPDATE booking SET
					to_ref=:to_ref,
					id_tour_operator=:id_tour_operator,
                    id_country=:id_country,
                    id_dept=:id_dept,
                    booking_for=:booking_for,
                    pax_type=:pax_type,
                    booking_from=:booking_from,
                    booking_to=:booking_to,
                    adult_amt=:adult_amt,
                    teen_amt=:teen_amt,
                    child_amt=:child_amt,
                    infant_amt=:infant_amt,
					status=:status,
					opt_date=:opt_date,
					remarks=:remarks
                WHERE id_booking=:id_booking";

        $stmt = $con->prepare($sqlUpdate);                        
        $stmt->execute(array(
				":id_booking" => $id_booking,
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
				":status" => $status,
				":opt_date" => $opt_date,
				":remarks" => $remarks
			));
		
		$sqlLog = "INSERT INTO booking_log ( 
		id_booking,
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
		status,
		opt_date,
		created_by,
		created_name,
		remarks,
		id_user,
		uname,
		log_status
		) 
			VALUES (
				:id_booking,
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
				:status,
				:opt_date,
				:created_by,
				:created_name,
				:remarks,
				:id_user,
				:uname,
				:log_status
				)";

	
	$stmt = $con->prepare($sqlLog);
				$stmt->execute(array(
				":id_booking" => $id_booking,
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
				":status" => $status,
				":opt_date" => $opt_date,
				":created_by" => $created_by,
				":created_name" => $created_name,
				":remarks" => $remarks,
				":id_user" => $id_user,
				":uname" => $uname,
				":log_status" => $log_status
			));

    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
