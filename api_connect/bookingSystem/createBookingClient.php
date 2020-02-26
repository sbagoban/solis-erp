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
    
// TO BE UPDATED
    $_SESSION["solis_userid"] = 1;
    $_SESSION["id_tour_operator"] = 1;
    $_SESSION["id_country"] = 979;
// TO BE UPDATED
    
    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    require_once("../../php/connector/pdo_connect_main.php");
    
    $id_booking = $_POST["id_booking"];
    $client_type = trim($_POST["client_type"]);
    $client_vip = 0;
    $client_title = trim($_POST["client_title"]);
    $client_surname = trim($_POST["client_surname"]);    
    $client_forename = trim($_POST["client_forename"]);
    $client_dob = trim($_POST["client_dob"]);
    $client_years = trim($_POST["client_years"]);
    $client_yearMonth = trim($_POST["client_yearMonth"]);
    $client_passport = trim($_POST["client_passport"]);
    $client_remarks = trim($_POST["client_remarks"]);
	$id_user = $_SESSION["solis_userid"];
	$uname = $_SESSION["solis_username"];
	$log_status = "CREATE";
	
	if ($client_years == "") 
	{
		$client_years = null;
	}

	if ($client_dob == "") 
	{
		$client_dob = null;
	}
	
    $con = pdo_con();
	
	
	$stmt = $con->prepare("SELECT * FROM booking WHERE id_booking = :id_booking AND active = 1");
	$stmt->execute(array(":id_booking"=>$id_booking));
	
	$row_count_c = $stmt->rowCount();

    if ($row_count_c > 0) 
	{
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$id_quote = $row["id_quote"];
		}
		//CLIENT TABLE

		$sqlClient = "INSERT INTO client
				(
				type,
				is_vip,
				title,
				surname,
				other_name,
				client_dob,
				passport_no,
				remarks)
				VALUES
				(:type,
				:is_vip,
				:title,
				:surname,
				:other_name,
				:client_dob,
				:passport_no,
				:remarks)";

		$stmt = $con->prepare($sqlClient);
					$stmt->execute(array(
					":type" => $client_type,
					":is_vip" => $client_vip,
					":title" => $client_title,
					":surname" => $client_surname,
					":other_name" => $client_forename,
					":client_dob" => $client_dob,
					":passport_no" => $client_passport,
					":remarks" => $client_remarks
				));

		$id_client = $con->lastInsertId();   

		// CLIENT LOG

		$sqlClientLog = "INSERT INTO client_log
				(id_client,
				type,
				is_vip,
				title,
				surname,
				other_name,
				client_dob,
				passport_no,
				remarks,
				id_user,
				uname,
				log_status) 
				VALUES (
				:id_client,
				:type,
				:is_vip,
				:title,
				:surname,
				:other_name,
				:client_dob,
				:passport_no,
				:remarks,
				:id_user,
				:uname,
				:log_status)";

		$stmt = $con->prepare($sqlClientLog);
					$stmt->execute(array(
					":id_client" => $id_client,
					":type" => $client_type,
					":is_vip" => $client_vip,
					":title" => $client_title,
					":surname" => $client_surname,
					":other_name" => $client_forename,
					":client_dob" => $client_dob,
					":passport_no" => $client_passport,
					":remarks" => $client_remarks,
					":id_user" => $id_user,
					":uname" => $uname,
					":log_status" => $log_status
				));

		// BOOKING CLIENT

		$sqlBookingClient = "INSERT INTO booking_client
				(id_client,
				id_booking,
				id_quote,
				age,
				yearMonth,
				remarks) 
				VALUES (
				:id_client,
				:id_booking,
				:id_quote,
				:age,
				:yearMonth,
				:remarks)";

		$stmt = $con->prepare($sqlBookingClient);
					$stmt->execute(array(
					":id_client" => $id_client,
					":id_booking" => $id_booking,
					":id_quote" => $id_quote,
					":age" => $client_years,
					":yearMonth" => $client_yearMonth,
					":remarks" => $client_remarks
				));

		$id_booking_client = $con->lastInsertId();    

		// BOOKING CLIENT LOG

		$sqlLog = "INSERT INTO booking_client_log
				(id_booking_client,
				id_client,
				id_booking,
				id_quote,
				age,
				yearMonth,
				remarks,
				id_user,
				uname,
				log_status) 
				VALUES (
				:id_booking_client,
				:id_client,
				:id_booking,
				:id_quote,
				:age,
				:yearMonth,
				:remarks,
				:id_user,
				:uname,
				:log_status
				)";


		$stmt = $con->prepare($sqlLog);
					$stmt->execute(array(
					":id_booking_client" => $id_booking_client,
					":id_client" => $id_client,
					":id_booking" => $id_booking,
					":id_quote" => $id_quote,
					":age" => $client_years,
					":yearMonth" => $client_yearMonth,
					":remarks" => $client_remarks,
					":id_user" => $id_user,
					":uname" => $uname,
					":log_status" => $log_status
				));

		$client_result= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_client"=>$id_client,  "id_booking_client"=>$id_booking_client);
		echo json_encode($client_result);
	}
	
	else
	{
        throw new Exception("BOOKING NOT FOUND");
	}
	
	
	} 
	catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>
