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

    
    $id_booking_room = $_POST["id_booking_room"];
    $room_remarks = $_POST["room_remarks"];
    $room_internal_remarks = $_POST["room_internal_remarks"];
    $room_status = trim($_POST["room_status"]);
    $created_by = $_SESSION["solis_userid"];
    $created_name = $_SESSION["solis_username"];
    $id_user = $_SESSION["solis_userid"];
    $uname = $_SESSION["solis_username"];
    $log_status = "CREATE";
    $room_clients = $_POST["room_clients"];
    
    //BOOKING ROOM 
    $sqlRoom = "SELECT * FROM booking_room WHERE id_booking_room = :id_booking_room";
    $stmt = $con->prepare($sqlRoom);
    $stmt->execute(array(":id_booking_room" => $id_booking_room));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE SERVICES!");
    }
    
    $sqlSaveRoom= "
        INSERT INTO booking_room
            (
                id_booking,
                stay_from,
                stay_to,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_status,
                created_by,
                created_name
            )
        VALUES
            (
                :id_booking,
                :stay_from,
                :stay_to,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_status,
                :created_by,
                :created_name
            )
    ";
    
    $stmt = $con->prepare($sqlSaveRoom);
    $stmt->execute(array(
        ":id_booking"                     =>$id_booking,
        ":stay_from"                     =>$stay_from,
        ":stay_to"                         =>$stay_to,
        ":room_adult_amt"           =>$room_adult_amt,
        ":room_teen_amt"            =>$room_teen_amt,
        ":room_child_amt"            =>$room_child_amt,
        ":room_infant_amt"          =>$room_infant_amt,
        ":room_status"                  =>$room_status,
        ":created_by"                    =>$created_by,
        ":created_name"               =>$created_name
    ));
    
    $id_booking_room = $con->lastInsertId();    
    
    $sqlSaveRoomLog= "
        INSERT INTO booking_room
            (
                id_booking_room,
                id_booking,
                stay_from,
                stay_to,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_status,
                created_by,
                created_name
            )
        VALUES
            (
                :id_booking_room,
                :id_booking,
                :stay_from,
                :stay_to,
                :room_adult_amt,
                :room_teen_amt,
                :room_child_amt,
                :room_infant_amt,
                :room_status,
                :created_by,
                :created_name
            )
    ";
    
    $stmt = $con->prepare($sqlSaveRoomLog);
    $stmt->execute(array(
        ":id_booking_room"         =>$id_booking_room,             
        ":id_booking"         =>$id_booking,             
        ":stay_from"                     =>$stay_from,
        ":stay_to"                         =>$stay_to,
        ":room_adult_amt"           =>$room_adult_amt,
        ":room_teen_amt"            =>$room_teen_amt,
        ":room_child_amt"            =>$room_child_amt,
        ":room_infant_amt"          =>$room_infant_amt,
        ":room_status"                  =>$room_status,
        ":created_by"                    =>$created_by,
        ":created_name"               =>$created_name
    ));

	// $id_booking_room_claim = $_POST["id_booking_room_claim"];
    $id_booking = $_POST["id_booking"];
    $room_service_paid_by = trim($_POST["room_service_paid_by"]);
    $id_tour_operator = trim($_POST["id_tour_operator"]);
    $id_client = $_POST["id_client"];
    $room_stay_from = trim($_POST["id_client"]);
    $room_stay_to = trim($_POST["room_stay_to"]);
    $room_booking_date = trim($_POST["room_booking_date"]);
	$id_contract = $_POST["id_contract"];
	$id_hotel = $_POST["id_hotel"];
    $qry_hotelDetails = $con->prepare("
		SELECT * FROM tblhotels WHERE id = :id_hotel AND active =1");
	$qry_hotelDetails->execute(array(":id_hotel"=>$id_hotel));
	$row_count_hotelDetails = $qry_hotelDetails->rowCount();
	if ($row_count_hotelDetails > 0) 
	{
		while ($rowHotel = $qry_hotelDetails->fetch(PDO::FETCH_ASSOC))
		{
			$hotelname = $rowHotel["hotelname"];
		}
	}
    else
    {
			$hotelname = " ";
    }
	$id_room = $_POST["id_room"];
    $qry_roomDetails = $con->prepare("
		SELECT * FROM tblhotel_rooms WHERE id = :id_room AND active =1");
	$qry_roomDetails->execute(array(":id_room"=>$id_room));
	$row_count_roomDetails = $qry_roomDetails->rowCount();
	if ($row_count_roomDetails > 0) 
	{
		while ($rowRoom = $qry_roomDetails->fetch(PDO::FETCH_ASSOC))
		{
			$room_details = $rowRoom["roomname"];
		}
	}
    else
    {
			$roomname = " ";
    }
    $room_claim_calcultation = trim($_POST["room_claim_calcultation"]);
    $room_cost_calcultation = trim($_POST["room_cost_calcultation"]);
    $room_adult_amt = trim($_POST["room_adult_amt"]);
    $room_teen_amt = trim($_POST["room_teen_amt"]);
    $room_child_amt = trim($_POST["room_child_amt"]);
    $room_infant_amt = trim($_POST["room_infant_amt"]);
    $room_total_pax = trim($_POST["room_total_pax"]); 
    
    $room_rebate_cost_type = trim($_POST["room_rebate_cost_type"]);
    $room_rebate_cost_approve_by = trim($_POST["room_rebate_cost_type"]);
    $room_rebate_claim_type = trim($_POST["room_rebate_claim_type"]);
    $room_rebate_claim_approve_by = trim($_POST["room_rebate_claim_approve_by"]);
    
    $qry_bookingDetails = $con->prepare("
		SELECT * FROM booking WHERE id_booking = :id_booking AND active =1");
	$qry_bookingDetails->execute(array(":id_booking"=>$id_booking));
	$row_count_bookingDetails = $qry_bookingDetails->rowCount();
	if ($row_count_bookingDetails > 0) 
	{
		while ($rowDept = $qry_bookingDetails->fetch(PDO::FETCH_ASSOC))
		{
			$id_dept = $rowDept["id_dept"];
		}
	}
    $room_charge = trim($_POST["room_charge"]); 
    $id_service_tax = trim($_POST["id_service_tax"]); 
    $tax_value = trim($_POST["tax_value"]); 
    if($room_service_paid_by == "TO")
    {
        $qry_tourOperator = $con->prepare("SELECT * FROM tbltouroperator WHERE id = :id_tour_operator AND active = 1");
        $qry_tourOperator->execute(array(":id_tour_operator"=>$id_tour_operator));

        $row_count_tourOperator = $qry_tourOperator->rowCount();

        if ($row_count_tourOperator > 0) 
        {
            while ($row = $qry_tourOperator->fetch(PDO::FETCH_ASSOC))
            {
                $id_tax_TO = $row["id_vat"];
            }
        }

    }
    else
    {
        $id_tax_TO = 3;
    }
    
    if ($id_tax_TO ==1 && $id_service_tax == '3' )
    {
        //remove vat on claim
        if ($room_charge == "PAX")
        {
            // ROOM PER PERSON
            if($room_adult_amt== 0 || $room_adult_amt== null || $room_adult_amt== "")
            {
                $room_adult_cost_exTAX= 0 ;
                $room_adult_cost= 0 ;
                $room_adult_claim_exTAX= 0 ;
                $room_adult_claim= 0 ;
                
                $room_adult_cost_after_rebate_exTAX= 0 ;
                $room_adult_cost_after_rebate= 0 ;
                $room_adult_claim_after_rebate_exTAX= 0 ;
                $room_adult_claim_after_rebate= 0 ;
                
            }
            else
            {
                $room_adult_cost_exTAX= $_POST["adult_cost"]* ((100 - $tax_value)/100);
                $room_adult_cost= $_POST["adult_cost"];
                $room_adult_claim_exTAX= $_POST["adult_claim"]* ((100 - $tax_value)/100);
                $room_adult_claim= $room_adult_claim_exTAX;
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_adult_claim_rebate = $room_adult_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $_POST["room_adult_claim_rebate"]* ((100 - $tax_value)/100); 
                    $room_adult_claim_rebate = $room_adult_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_adult_claim_after_rebate_exTAX = 0; 
                    $room_adult_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                    $room_adult_claim_rebate =$room_adult_claim;
                }
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_adult_cost_rebate = $room_adult_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"]* ((100 - $tax_value)/100); 
                    $room_adult_cost_rebate = $room_adult_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_adult_cost_after_rebate_exTAX = 0; 
                    $room_adult_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                    $room_adult_cost_rebate =$room_adult_cost;
                }
            }
            
            if($room_teen_amt== 0 || $room_teen_amt== null || $room_teen_amt== "")
            {
                $room_teen_cost_exTAX= 0 ;
                $room_teen_cost= 0 ;
                $room_teen_claim_exTAX= 0 ;
                $room_teen_claim= 0 ;
                $room_teen_cost_after_rebate_exTAX= 0 ;
                $room_teen_cost_after_rebate= 0 ;
                $room_teen_claim_after_rebate_exTAX= 0 ;
                $room_teen_claim_after_rebate= 0 ;
            }
            else
            {
                $room_teen_cost_exTAX= $_POST["teen_cost"]* ((100 - $tax_value)/100);
                $room_teen_cost= $_POST["teen_cost"];
                $room_teen_claim_exTAX= $_POST["teen_claim"]* ((100 - $tax_value)/100);
                $room_teen_claim= $room_adult_claim_exTAX;
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_teen_claim_rebate = $room_teen_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $_POST["room_teen_claim_rebate"]* ((100 - $tax_value)/100); 
                    $room_teen_claim_rebate = $room_teen_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_teen_claim_after_rebate_exTAX = 0; 
                    $room_teen_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX; 
                    $room_teen_claim_rebate =$room_teen_claim;
                }
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_teen_cost_rebate = $room_teen_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $_POST["room_teen_cost_rebate"]* ((100 - $tax_value)/100); 
                    $room_teen_cost_rebate = $room_teen_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_teen_cost_after_rebate_exTAX = 0; 
                    $room_teen_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX; 
                    $room_teen_cost_rebate =$room_teen_cost;
                }
            }
            
            if($room_child_amt== 0 || $room_child_amt== null || $room_child_amt== "")
            {
                $room_child_cost_exTAX= 0 ;
                $room_child_cost= 0 ;
                $room_child_claim_exTAX= 0 ;
                $room_child_claim= 0 ;
                $room_child_cost_after_rebate_exTAX= 0 ;
                $room_child_cost_after_rebate= 0 ;
                $room_child_claim_after_rebate_exTAX= 0 ;
                $room_child_claim_after_rebate= 0 ;
            }
            else
            {
                $room_child_cost_exTAX= $_POST["child_cost"]* ((100 - $tax_value)/100);
                $room_child_cost= $_POST["child_cost"];
                $room_child_claim_exTAX= $_POST["child_claim"]* ((100 - $tax_value)/100);
                $room_child_claim= $room_child_claim_exTAX;
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_child_claim_rebate = $room_child_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $_POST["room_child_claim_rebate"]* ((100 - $tax_value)/100); 
                    $room_child_claim_rebate = $room_child_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_child_claim_after_rebate_exTAX = 0; 
                    $room_child_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX; 
                    $room_child_claim_rebate =$room_child_claim;
                }
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_child_cost_rebate = $room_child_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $_POST["room_child_cost_rebate"]* ((100 - $tax_value)/100); 
                    $room_child_cost_rebate = $room_child_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_child_cost_after_rebate_exTAX = 0; 
                    $room_child_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX; 
                    $room_child_cost_rebate =$room_child_cost;
                }
            }
            
            if($room_infant_amt== 0 || $room_infant_amt== null || $room_infant_amt== "")
            {
                $room_infant_cost_exTAX= 0 ;
                $room_infant_cost= 0 ;
                $room_infant_claim_exTAX= 0 ;
                $room_infant_claim= 0 ;
                $room_infant_cost_after_rebate_exTAX= 0 ;
                $room_infant_cost_after_rebate= 0 ;
                $room_infant_claim_after_rebate_exTAX= 0 ;
                $room_infant_claim_after_rebate= 0 ;
            }
            else
            {
                $room_infant_cost_exTAX= $_POST["infant_cost"]* ((100 - $tax_value)/100);
                $room_infant_cost= $_POST["infant_cost"];
                $room_infant_claim_exTAX= $_POST["infant_claim"]* ((100 - $tax_value)/100);
                $room_infant_claim= $room_infant_claim_exTAX;
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_infant_claim_rebate = $room_infant_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $_POST["room_infant_claim_rebate"]* ((100 - $tax_value)/100); 
                    $room_infant_claim_rebate = $room_infant_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_infant_claim_after_rebate_exTAX = 0; 
                    $room_infant_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX; 
                    $room_infant_claim_rebate =$room_infant_claim;
                }
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_infant_cost_rebate = $room_infant_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $_POST["room_infant_cost_rebate"]* ((100 - $tax_value)/100); 
                    $room_infant_cost_rebate = $room_infant_cost_after_rebate_exTAX;
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_infant_cost_after_rebate_exTAX = 0; 
                    $room_infant_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX; 
                    $room_infant_cost_rebate =$room_infant_cost;
                }
            }
            
            $room_total_cost_exTAX = (($room_adult_amt * $room_adult_cost_exTAX) +($room_teen_amt * $room_teen_cost_exTAX) + ($room_child_amt * $room_child_cost_exTAX) +($room_infant_amt * $room_infant_cost_exTAX));
            $room_total_cost = (($room_adult_amt * $room_adult_cost) +($room_teen_amt * $room_teen_cost) + ($room_child_amt * $room_child_cost) +($room_infant_amt * $room_infant_cost));
            
            $room_total_claim_exTAX = (($room_adult_amt * $room_adult_claim_exTAX) +($room_teen_amt * $room_teen_claim_exTAX) + ($room_child_amt * $room_child_claim_exTAX) +($room_infant_amt * $room_infant_claim_exTAX));
            $room_total_claim = (($room_adult_amt * $room_adult_claim) +($room_teen_amt * $room_teen_claim) + ($room_child_amt * $room_child_claim) +($room_infant_amt * $room_infant_claim));
            
            $room_total_cost_after_rebate_exTAX = (($room_adult_amt * $room_adult_cost_after_rebate_exTAX) +($room_teen_amt * $room_teen_cost_after_rebate_exTAX) + ($room_child_amt * $room_child_cost_after_rebate_exTAX) +($room_infant_amt * $room_infant_cost_after_rebate_exTAX));
            $room_total_cost_after_rebate = (($room_adult_amt * $room_adult_cost_rebate) +($room_teen_amt * $room_teen_cos_rebatet) + ($room_child_amt * $room_child_cost_rebate) +($room_infant_amt * $room_infant_cost_rebate));
            
            $room_total_claim_after_rebate_exTAX = (($room_adult_amt * $room_adult_claim_after_rebate_exTAX) +($room_teen_amt * $room_teen_claim_after_rebate_exTAX) + ($room_child_amt * $room_child_claim_after_rebate_exTAX) +($room_infant_amt * $room_infant_claim_after_rebate_exTAX));
            $room_total_claim_after_rebate = (($room_adult_amt * $room_adult_claim_rebate) +($room_teen_amt * $room_teen_claim_rebate) + ($room_child_amt * $room_child_claim_rebate) +($room_infant_amt * $room_infant_claim_rebate));
            
        }
        else
        {
            // ROOM PER UNIT
            $room_adult_cost_exTAX=  $_POST["unit_cost"]* ((100 - $tax_value)/100);
            $room_adult_cost= $_POST["unit_cost"];
            $room_adult_claim_exTAX= $_POST["unit_claim"]* ((100 - $tax_value)/100);
            $room_adult_claim= $room_adult_claim_exTAX;
            
            $room_teen_cost_exTAX= 0 ;
            $room_teen_cost= 0 ;
            $room_teen_claim_exTAX= 0 ;
            $room_teen_claim= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim= 0 ;
            $room_child_cost_exTAX= 0 ;
            $room_child_cost= 0 ;
            $room_infant_claim_exTAX= 0 ;
            $room_infant_claim= 0 ;
            $room_infant_cost_exTAX= 0 ;
            $room_infant_cost= 0 ;
            $room_teen_cost_after_rebate_exTAX= 0 ;
            $room_teen_cost_after_rebate= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim_rebate= 0 ;
            $room_child_cost_after_rebate_exTAX= 0 ;
            $room_child_cost_after_rebate= 0 ;
            $room_infant_claim_after_rebate_exTAX= 0 ;
            $room_infant_claim_rebate= 0 ;
            $room_infant_cost_after_rebate_exTAX= 0 ;
            $room_infant_cost_after_rebate= 0 ;
            
            $room_total_claim_exTAX = $room_adult_claim_exTAX;
            $room_total_claim = $room_adult_claim;
            
            $room_total_cost_exTAX = $room_adult_cost_exTAX;
            $room_total_cost = $room_adult_cost;   
            
            $room_total_claim_after_rebate_exTAX = $room_adult_claim_after_rebate_exTAX;
            $room_total_claim_after_rebate = $room_adult_claim_after_rebate;
            
            $room_total_cost_after_rebate_exTAX = $room_adult_cost_after_rebate_exTAX;
            $room_total_cost_after_rebate = $room_adult_cost_after_rebate;   
        }
          
    }
    else if  ($id_tax_TO ==1 && $id_service_tax != '3' )
    {
        //remove vat on markup
        if ($room_charge == "PAX")
        {
            // ROOM PER PERSON
            if($room_adult_amt== 0 || $room_adult_amt== null || $room_adult_amt== "")
            {
                $room_adult_claim_exTAX= 0 ;
                $room_adult_claim= 0 ;
                $room_adult_cost_exTAX= 0 ;
                $room_adult_cost= 0 ;
                
                $room_adult_cost_after_rebate_exTAX= 0 ;
                $room_adult_cost_after_rebate= 0 ;
                $room_adult_claim_after_rebate_exTAX= 0 ;
                $room_adult_claim_after_rebate= 0 ;
                
            }
            else
            {
                $room_adult_cost_exTAX= $_POST["adult_cost"]* ((100 - $tax_value)/100);
                $room_adult_cost= $_POST["adult_cost"];
                $adult_markup_exTAX= ($_POST["adult_claim"] - $room_adult_cost) * ((100 - $tax_value)/100);
                $room_adult_claim_exTAX= $adult_markup_exTAX + $room_adult_cost;
                $room_adult_claim= $room_adult_claim_exTAX;
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_adult_cost_after_rebate_exTAX = 0; 
                    $room_adult_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                    $room_adult_cost_rebate =$room_adult_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $adult_markup_after_rebate_exTAX= ($_POST["room_adult_claim_rebate"] - $room_adult_cost) * ((100 - $tax_value)/100);
                    $room_adult_claim_after_rebate_exTAX = $adult_markup_after_rebate_exTAX + $room_adult_cost;
                    $room_adult_claim_rebate = $room_adult_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_adult_claim_after_rebate_exTAX = 0; 
                    $room_adult_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                    $room_adult_claim_rebate =$room_adult_claim_after_rebate_exTAX;
                }
            }
            
            if($room_teen_amt== 0 || $room_teen_amt== null || $room_teen_amt== "")
            {
                $room_teen_claim_exTAX= 0 ;
                $room_teen_claim= 0 ;
                $room_teen_cost_exTAX= 0 ;
                $room_teen_cost= 0 ;
                
                $room_teen_cost_after_rebate_exTAX= 0 ;
                $room_teen_cost_after_rebate= 0 ;
                $room_teen_claim_after_rebate_exTAX= 0 ;
                $room_teen_claim_after_rebate= 0 ;
            }
            else
            {
                $room_teen_claim_exTAX= $_POST["teen_claim"]* ((100 - $tax_value)/100);
                $room_teen_claim= $room_teen_claim_exTAX;
                $teen_markup_exTAX= ($_POST["teen_claim"] - $room_teen_cost) * ((100 - $tax_value)/100);
                $room_teen_claim_exTAX= $teen_markup_exTAX + $room_teen_cost;
                $room_teent_claim= $room_teen_claim_exTAX;
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_teen_cost_rebate = $room_teen_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $_POST["room_teen_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_teen_cost_rebate = $_POST["room_teen_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_teen_cost_after_rebate_exTAX = 0; 
                    $room_teen_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX; 
                    $room_teen_cost_rebate =$room_teen_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_teen_claim_rebate = $room_teen_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $teen_markup_after_rebate_exTAX= ($_POST["room_teen_claim_rebate"] - $room_teen_cost) * ((100 - $tax_value)/100);
                    $room_teen_claim_after_rebate_exTAX = $teen_markup_after_rebate_exTAX + $room_teen_cost;
                    $room_teen_claim_rebate = $room_teen_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_teen_claim_after_rebate_exTAX = 0; 
                    $room_teen_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX; 
                    $room_teen_claim_rebate =$room_teen_claim_after_rebate_exTAX;
                }
            }
            
            if($room_child_amt== 0 || $room_child_amt== null || $room_child_amt== "")
            {
                $room_child_claim_exTAX= 0 ;
                $room_child_claim= 0 ;
                $room_child_cost_exTAX= 0 ;
                $room_child_cost= 0 ;
                
                $room_child_cost_after_rebate_exTAX= 0 ;
                $room_child_cost_after_rebate= 0 ;
                $room_child_claim_after_rebate_exTAX= 0 ;
                $room_child_claim_after_rebate= 0 ;
            }
            else
            {
                $room_child_claim_exTAX= $_POST["child_claim"]* ((100 - $tax_value)/100);
                $room_child_claim= $room_child_claim_exTAX;
                $child_markup_exTAX= ($_POST["child_claim"] - $room_child_cost) * ((100 - $tax_value)/100);
                $room_child_claim_exTAX= $child_markup_exTAX + $room_child_cost;
                $room_child_claim= $room_child_claim_exTAX;
                
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_child_cost_rebate = $room_child_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $_POST["room_child_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_child_cost_rebate = $_POST["room_child_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_child_cost_after_rebate_exTAX = 0; 
                    $room_child_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX; 
                    $room_child_cost_rebate =$room_child_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_child_claim_rebate = $room_child_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $child_markup_after_rebate_exTAX= ($_POST["room_child_claim_rebate"] - $room_child_cost) * ((100 - $tax_value)/100);
                    $room_child_claim_after_rebate_exTAX = $child_markup_after_rebate_exTAX + $room_child_cost;
                    $room_child_claim_rebate = $room_child_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_child_claim_after_rebate_exTAX = 0; 
                    $room_child_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX; 
                    $room_child_claim_rebate =$room_child_claim_after_rebate_exTAX;
                }
            }
            
            if($room_infant_amt== 0 || $room_infant_amt== null || $room_infant_amt== "")
            {
                $room_infant_claim_exTAX= 0 ;
                $room_infant_claim= 0 ;
                $room_infant_cost_exTAX= 0 ;
                $room_infant_cost= 0 ;
                
                $room_infant_cost_after_rebate_exTAX= 0 ;
                $room_infant_cost_after_rebate= 0 ;
                $room_infant_claim_after_rebate_exTAX= 0 ;
                $room_infant_claim_after_rebate= 0 ;
            }
            else
            {
                $room_infant_claim_exTAX= $_POST["infant_claim"]* ((100 - $tax_value)/100);
                $room_infant_claim= $room_infant_claim_exTAX;
                $infant_markup_exTAX= ($_POST["infant_claim"] - $room_infant_cost) * ((100 - $tax_value)/100);
                $room_infant_claim_exTAX= $infant_markup_exTAX + $room_infant_cost;
                $room_infant_claim= $room_child_claim_exTAX;
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_infant_cost_rebate = $room_infant_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $_POST["room_infant_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_infant_cost_rebate = $_POST["room_infant_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_infant_cost_after_rebate_exTAX = 0; 
                    $room_infant_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX; 
                    $room_infant_cost_rebate =$room_infant_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_infant_claim_rebate = $room_infant_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $infant_markup_after_rebate_exTAX= ($_POST["room_infant_claim_rebate"] - $room_infant_cost) * ((100 - $tax_value)/100);
                    $room_infant_claim_after_rebate_exTAX = $infant_markup_after_rebate_exTAX + $room_infant_cost;
                    $room_infant_claim_rebate = $room_infant_claim_after_rebate_exTAX;
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_infant_claim_after_rebate_exTAX = 0; 
                    $room_infant_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX; 
                    $room_infant_claim_rebate =$room_infant_claim_after_rebate_exTAX;
                }
            }
            
            $room_total_cost_exTAX = (($room_adult_amt * $room_adult_cost_exTAX) +($room_teen_amt * $room_teen_cost_exTAX) + ($room_child_amt * $room_child_cost_exTAX) +($room_infant_amt * $room_infant_cost_exTAX));
            $room_total_cost = (($room_adult_amt * $room_adult_cost) +($room_teen_amt * $room_teen_cost) + ($room_child_amt * $room_child_cost) +($room_infant_amt * $room_infant_cost));
            
            $room_total_claim_exTAX = (($room_adult_amt * $room_adult_claim_exTAX) +($room_teen_amt * $room_teen_claim_exTAX) + ($room_child_amt * $room_child_claim_exTAX) +($room_infant_amt * $room_infant_claim_exTAX));
            $room_total_claim = (($room_adult_amt * $room_adult_claim) +($room_teen_amt * $room_teen_claim) + ($room_child_amt * $room_child_claim) +($room_infant_amt * $room_infant_claim));
            
            $room_total_cost_after_rebate_exTAX = (($room_adult_amt * $room_adult_cost_after_rebate_exTAX) +($room_teen_amt * $room_teen_cost_after_rebate_exTAX) + ($room_child_amt * $room_child_cost_after_rebate_exTAX) +($room_infant_amt * $room_infant_cost_after_rebate_exTAX));
            $room_total_cost_after_rebate = (($room_adult_amt * $room_adult_cost_rebate) +($room_teen_amt * $room_teen_cos_rebatet) + ($room_child_amt * $room_child_cost_rebate) +($room_infant_amt * $room_infant_cost_rebate));
            
            $room_total_claim_after_rebate_exTAX = (($room_adult_amt * $room_adult_claim_after_rebate_exTAX) +($room_teen_amt * $room_teen_claim_after_rebate_exTAX) + ($room_child_amt * $room_child_claim_after_rebate_exTAX) +($room_infant_amt * $room_infant_claim_after_rebate_exTAX));
            $room_total_claim_after_rebate = (($room_adult_amt * $room_adult_claim_rebate) +($room_teen_amt * $room_teen_claim_rebate) + ($room_child_amt * $room_child_claim_rebate) +($room_infant_amt * $room_infant_claim_rebate));
                
        }
        else
        {
            // ROOM PER UNIT
            $room_adult_cost_exTAX= $_POST["unit_cost"]* ((100 - $tax_value)/100);
            $room_adult_cost= $_POST["unit_cost"];
            $adult_markup_exTAX= ($_POST["unit_claim"] - $room_adult_cost) * ((100 - $tax_value)/100);
            $room_adult_claim_exTAX= $adult_markup_exTAX + $room_adult_cost;
            $room_adult_claim= $room_adult_claim_exTAX;
            
            $room_teen_cost_exTAX= 0 ;
            $room_teen_cost= 0 ;
            $room_teen_claim_exTAX= 0 ;
            $room_teen_claim= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim= 0 ;
            $room_child_cost_exTAX= 0 ;
            $room_child_cost= 0 ;
            $room_infant_claim_exTAX= 0 ;
            $room_infant_claim= 0 ;
            $room_infant_cost_exTAX= 0 ;
            $room_infant_cost= 0 ;
            $room_teen_cost_after_rebate_exTAX= 0 ;
            $room_teen_cost_after_rebate= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim_rebate= 0 ;
            $room_child_cost_after_rebate_exTAX= 0 ;
            $room_child_cost_after_rebate= 0 ;
            $room_infant_claim_after_rebate_exTAX= 0 ;
            $room_infant_claim_rebate= 0 ;
            $room_infant_cost_after_rebate_exTAX= 0 ;
            $room_infant_cost_after_rebate= 0 ;

            if ($room_rebate_cost_type == "Percenatge")
            {
                $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
            }
            else if ($room_rebate_cost_type == "Fixed Tariff")
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
            }
            else if ($room_rebate_cost_type == "FOC")
            {
                $room_rebate_cost_percentage = 100;
                $room_adult_cost_after_rebate_exTAX = 0; 
                $room_adult_cost_rebate = 0;
            }
            else
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                $room_adult_cost_rebate =$room_adult_cost;
            }

            if ($room_rebate_claim_type == "Percenatge")
            {
                $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
            }
            else if ($room_rebate_claim_type == "Fixed Tariff")
            {
                $room_rebate_claim_percentage = 0;
                $adult_markup_after_rebate_exTAX= ($_POST["room_unit_claim_rebate"] - $room_adult_cost) * ((100 - $tax_value)/100);
                $room_adult_claim_after_rebate_exTAX = $adult_markup_after_rebate_exTAX + $room_adult_cost;
                $room_adult_claim_rebate = $room_adult_claim_after_rebate_exTAX;
            }
            else if ($room_rebate_claim_type == "FOC")
            {
                $room_rebate_claim_percentage = 100;
                $room_adult_claim_after_rebate_exTAX = 0; 
                $room_adult_claim_rebate = 0;
            }
            else
            {
                $room_rebate_claim_percentage = 0;
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                $room_adult_claim_rebate =$room_adult_claim_after_rebate_exTAX;
            }
            
            $room_total_claim_exTAX = $room_adult_claim_exTAX;
            $room_total_claim = $room_total_claim_exTAX;
            
            $room_total_cost_exTAX = $room_adult_cost;
            $room_total_cost = $room_adult_cost;   
            
            $room_total_claim_after_rebate_exTAX = $room_adult_claim_after_rebate_exTAX;
            $room_total_claim_after_rebate = $room_adult_claim_after_rebate;
            
            $room_total_cost_after_rebate_exTAX = $room_adult_cost_after_rebate_exTAX;
            $room_total_cost_after_rebate = $room_adult_cost_after_rebate; 
        }
    }
    else if  ($id_tax_TO !=1 && $id_service_tax == '3' )
    {
        //vat on markup
        if ($room_charge == "PAX")
        {
            // ROOM PER PERSON
            if($room_adult_amt== 0 || $room_adult_amt== null || $room_adult_amt== "")
            {
                $room_adult_claim_exTAX= 0 ;
                $room_adult_claim= 0 ;
                $room_adult_cost_exTAX= 0 ;
                $room_adult_cost= 0 ;
                
                $room_adult_cost_after_rebate_exTAX= 0 ;
                $room_adult_cost_after_rebate= 0 ;
                $room_adult_claim_after_rebate_exTAX= 0 ;
                $room_adult_claim_after_rebate= 0 ;
                
            }
            else
            {
                $room_adult_cost_exTAX= $_POST["adult_cost"];
                $room_adult_cost= $_POST["adult_cost"];
                $adult_markup= ($_POST["adult_claim"] - $room_adult_cost);
                $adult_markup_exTAX= $adult_markup * ((100 - $tax_value)/100);
                $room_adult_claim_exTAX= $adult_markup_exTAX + $room_adult_cost;
                $room_adult_claim= $_POST["adult_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_adult_cost_after_rebate_exTAX = 0; 
                    $room_adult_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                    $room_adult_cost_rebate =$room_adult_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $adult_rebate_markup = ($_POST["room_adult_claim_rebate"] - $room_adult_cost);
                    $adult_rebate_markup_exTAX = $adult_rebate_markup * ((100 - $tax_value)/100);
                    $room_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $room_adult_cost;
                    $room_adult_claim_rebate = $_POST["room_adult_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_adult_claim_after_rebate_exTAX = 0; 
                    $room_adult_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                    $room_adult_claim_rebate =$room_adult_claim;
                }
            }
            
            if($room_teen_amt== 0 || $room_teen_amt== null || $room_teen_amt== "")
            {
                $room_teen_claim_exTAX= 0 ;
                $room_teen_claim= 0 ;
                $room_teen_cost_exTAX= 0 ;
                $room_teen_cost= 0 ;
                
                $room_teen_cost_after_rebate_exTAX= 0 ;
                $room_teen_cost_after_rebate= 0 ;
                $room_teen_claim_after_rebate_exTAX= 0 ;
                $room_teen_claim_after_rebate= 0 ;
            }
            else
            {
                $room_teen_cost_exTAX= $_POST["teen_cost"];
                $room_teen_cost= $_POST["teen_cost"];
                $teen_markup= ($_POST["teen_claim"] - $room_teen_cost);
                $teen_markup_exTAX= $teen_markup * ((100 - $tax_value)/100);
                $room_teen_claim_exTAX= $teen_markup_exTAX + $room_teen_cost;
                $room_teen_claim= $_POST["teen_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_teen_cost_rebate = $room_teen_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $_POST["room_teen_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_teen_cost_rebate = $_POST["room_teen_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_teen_cost_after_rebate_exTAX = 0; 
                    $room_teen_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX; 
                    $room_teen_cost_rebate =$room_teen_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_teen_claim_rebate = $room_teen_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $teen_rebate_markup = ($_POST["room_teen_claim_rebate"] - $room_teen_cost);
                    $teen_rebate_markup_exTAX = $teen_rebate_markup * ((100 - $tax_value)/100);
                    $room_teen_claim_after_rebate_exTAX = $teen_rebate_markup_exTAX + $room_teen_cost;
                    $room_teen_claim_rebate = $_POST["room_teen_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_teen_claim_after_rebate_exTAX = 0; 
                    $room_teen_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX; 
                    $room_teen_claim_rebate =$room_teen_claim;
                }
            }
            
            if($room_child_amt== 0 || $room_child_amt== null || $room_child_amt== "")
            {
                $room_child_claim_exTAX= 0 ;
                $room_child_claim= 0 ;
                $room_child_cost_exTAX= 0 ;
                $room_child_cost= 0 ;
                
                $room_child_cost_after_rebate_exTAX= 0 ;
                $room_child_cost_after_rebate= 0 ;
                $room_child_claim_after_rebate_exTAX= 0 ;
                $room_child_claim_after_rebate= 0 ;
            }
            else
            {
                $room_child_cost_exTAX= $_POST["child_cost"];
                $room_child_cost= $_POST["child_cost"];
                $child_markup= ($_POST["child_claim"] - $room_child_cost);
                $child_markup_exTAX= $child_markup * ((100 - $tax_value)/100);
                $room_child_claim_exTAX= $child_markup_exTAX + $room_child_cost;
                $room_child_claim= $_POST["child_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_child_cost_rebate = $room_child_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $_POST["room_child_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_child_cost_rebate = $_POST["room_child_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_child_cost_after_rebate_exTAX = 0; 
                    $room_child_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX; 
                    $room_child_cost_rebate =$room_child_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_child_claim_rebate = $room_child_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $child_rebate_markup = ($_POST["room_child_claim_rebate"] - $room_child_cost);
                    $child_rebate_markup_exTAX = $child_rebate_markup * ((100 - $tax_value)/100);
                    $room_child_claim_after_rebate_exTAX = $child_rebate_markup_exTAX + $room_child_cost;
                    $room_child_claim_rebate = $_POST["room_child_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_child_claim_after_rebate_exTAX = 0; 
                    $room_child_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX; 
                    $room_child_claim_rebate =$room_child_claim;
                }
            }
            
            if($room_infant_amt== 0 || $room_infant_amt== null || $room_infant_amt== "")
            {
                $room_infant_claim_exTAX= 0 ;
                $room_infant_claim= 0 ;
                $room_infant_cost_exTAX= 0 ;
                $room_infant_cost= 0 ;
                
                $room_infant_cost_after_rebate_exTAX= 0 ;
                $room_infant_cost_after_rebate= 0 ;
                $room_infant_claim_after_rebate_exTAX= 0 ;
                $room_infant_claim_after_rebate= 0 ;
            }
            else
            {
                $room_infant_cost_exTAX= $_POST["infant_cost"];
                $room_infant_cost= $_POST["infant_cost"];
                $infant_markup= ($_POST["infant_claim"] - $room_infant_cost);
                $infant_markup_exTAX= $infant_markup * ((100 - $tax_value)/100);
                $room_infant_claim_exTAX= $infant_markup_exTAX + $room_infant_cost;
                $room_infant_claim= $_POST["infant_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_infant_cost_rebate = $room_infant_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $_POST["room_infant_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_infant_cost_rebate = $_POST["room_infant_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_infant_cost_after_rebate_exTAX = 0; 
                    $room_infant_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX; 
                    $room_infant_cost_rebate =$room_infant_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_infant_claim_rebate = $room_infant_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $infant_rebate_markup = ($_POST["room_infant_claim_rebate"] - $room_infant_cost);
                    $infant_rebate_markup_exTAX = $infant_rebate_markup * ((100 - $tax_value)/100);
                    $room_infant_claim_after_rebate_exTAX = $infant_rebate_markup_exTAX + $room_infant_cost;
                    $room_infant_claim_rebate = $_POST["room_infant_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_infant_claim_after_rebate_exTAX = 0; 
                    $room_infant_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX; 
                    $room_infant_claim_rebate =$room_infant_claim;
                }
            }
            
            $room_total_cost_exTAX = (($room_adult_amt * $room_adult_cost_exTAX) +($room_teen_amt * $room_teen_cost_exTAX) + ($room_child_amt * $room_child_cost_exTAX) +($room_infant_amt * $room_infant_cost_exTAX));
            $room_total_cost = (($room_adult_amt * $room_adult_cost) +($room_teen_amt * $room_teen_cost) + ($room_child_amt * $room_child_cost) +($room_infant_amt * $room_infant_cost));
            
            $room_total_claim_exTAX = (($room_adult_amt * $room_adult_claim_exTAX) +($room_teen_amt * $room_teen_claim_exTAX) + ($room_child_amt * $room_child_claim_exTAX) +($room_infant_amt * $room_infant_claim_exTAX));
            $room_total_claim = (($room_adult_amt * $room_adult_claim) +($room_teen_amt * $room_teen_claim) + ($room_child_amt * $room_child_claim) +($room_infant_amt * $room_infant_claim));
            
            $room_total_cost_after_rebate_exTAX= (($room_adult_amt * $room_adult_cost_after_rebate_exTAX) +($room_teen_amt * $room_teen_cost_after_rebate_exTAX) + ($room_child_amt * $room_child_cost_after_rebate_exTAX) +($room_infant_amt * $room_infant_cost_after_rebate_exTAX));
            $room_total_cost_after_rebate = (($room_adult_amt * $room_adult_cost_rebate) +($room_teen_amt * $room_teen_cost_rebate) + ($room_child_amt * $room_child_cost_rebate) +($room_infant_amt * $room_infant_cost_rebate));
            
            $room_total_claim_after_rebate_exTAX = (($room_adult_amt * $room_adult_claim_after_rebate_exTAX) +($room_teen_amt * $room_teen_claim_after_rebate_exTAX) + ($room_child_amt * $room_child_claim_after_rebate_exTAX) +($room_infant_amt * $room_infant_claim_after_rebate_exTAX));
            $room_total_claim_after_rebate = (($room_adult_amt * $room_adult_claim_rebate) +($room_teen_amt * $room_teen_claim_rebate) + ($room_child_amt * $room_child_claim_rebate) +($room_infant_amt * $room_infant_claim_rebate));
                
        }
        else
        {
            // ROOM PER UINT
            $room_adult_claim_exTAX= $_POST["unit_claim"]* ((100 - $tax_value)/100);
            $room_adult_claim= $room_adult_claim_exTAX;
            $adult_markup_exTAX= ($_POST["unit_claim"] - $room_adult_cost) * ((100 - $tax_value)/100);
            $room_adult_claim_exTAX= $adult_markup_exTAX + $room_adult_cost;
            $room_adult_claim= $room_adult_claim_exTAX;
            
            $room_teen_cost_exTAX= 0 ;
            $room_teen_cost= 0 ;
            $room_teen_claim_exTAX= 0 ;
            $room_teen_claim= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim= 0 ;
            $room_child_cost_exTAX= 0 ;
            $room_child_cost= 0 ;
            $room_infant_claim_exTAX= 0 ;
            $room_infant_claim= 0 ;
            $room_infant_cost_exTAX= 0 ;
            $room_infant_cost= 0 ;
            $room_teen_cost_after_rebate_exTAX= 0 ;
            $room_teen_cost_after_rebate= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim_rebate= 0 ;
            $room_child_cost_after_rebate_exTAX= 0 ;
            $room_child_cost_after_rebate= 0 ;
            $room_infant_claim_after_rebate_exTAX= 0 ;
            $room_infant_claim_rebate= 0 ;
            $room_infant_cost_after_rebate_exTAX= 0 ;
            $room_infant_cost_after_rebate= 0 ;
            
            if ($room_rebate_cost_type == "Percenatge")
            {
                $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
            }
            else if ($room_rebate_cost_type == "Fixed Tariff")
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
            }
            else if ($room_rebate_cost_type == "FOC")
            {
                $room_rebate_cost_percentage = 100;
                $room_adult_cost_after_rebate_exTAX = 0; 
                $room_adult_cost_rebate = 0;
            }
            else
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                $room_adult_cost_rebate =$room_adult_cost;
            }

            if ($room_rebate_claim_type == "Percenatge")
            {
                $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
            }
            else if ($room_rebate_claim_type == "Fixed Tariff")
            {
                $room_rebate_claim_percentage = 0;
                $adult_rebate_markup = ($_POST["unit_claim"] - $room_adult_cost);
                $adult_rebate_markup_exTAX = $adult_rebate_markup * ((100 - $tax_value)/100);
                $room_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $room_adult_cost;
                $room_adult_claim_rebate = $_POST["unit_claim"];
            }
            else if ($room_rebate_claim_type == "FOC")
            {
                $room_rebate_claim_percentage = 100;
                $room_adult_claim_after_rebate_exTAX = 0; 
                $room_adult_claim_rebate = 0;
            }
            else
            {
                $room_rebate_claim_percentage = 0;
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                $room_adult_claim_rebate =$room_adult_claim;
            }
            
            $room_total_cost_exTAX = $room_adult_cost;
            $room_total_cost = $room_total_cost_exTAX;   
            $room_total_cost_after_rebate_exTAX = $room_adult_cost_after_rebate_exTAX;
            $room_total_cost_after_rebate = $room_total_cost_after_rebate_exTAX;   
            
            $room_total_claim_exTAX = $room_adult_claim_exTAX;
            $room_total_claim = $room_total_claim_exTAX;
            $room_total_claim_after_rebate_exTAX = $room_adult_claim_after_rebate_exTAX;
            $room_total_claim_after_rebate = $room_total_claim_after_rebate_exTAX;
        }
        
    }
    else if  ($id_tax_TO !=1 && $id_service_tax != '3' )
    {
        //vat applied
        if ($room_charge == "PAX")
        {
            // ROOM PER PERSON
            if($room_adult_amt== 0 || $room_adult_amt== null || $room_adult_amt== "")
            {
                $room_adult_claim_exTAX= 0 ;
                $room_adult_claim= 0 ;
                $room_adult_cost_exTAX= 0 ;
                $room_adult_cost= 0 ;
                
                $room_adult_cost_after_rebate_exTAX= 0 ;
                $room_adult_cost_after_rebate= 0 ;
                $room_adult_claim_after_rebate_exTAX= 0 ;
                $room_adult_claim_after_rebate= 0 ;
            }
            else
            {
                $room_adult_cost_exTAX= $_POST["adult_cost"] * ((100 - $tax_value)/100);
                $room_adult_cost= $_POST["adult_cost"];
                $room_adult_claim_exTAX= $_POST["adult_claim"] * ((100 - $tax_value)/100);
                $room_adult_claim= $_POST["adult_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_adult_cost_after_rebate_exTAX = 0; 
                    $room_adult_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                    $room_adult_cost_rebate =$room_adult_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $_POST["room_adult_claim_rebate"] * ((100 - $tax_value)/100);
                    $room_adult_claim_rebate = $_POST["room_adult_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_adult_claim_after_rebate_exTAX = 0; 
                    $room_adult_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                    $room_adult_claim_rebate =$room_adult_claim;
                }
            }
            
            if($room_teen_amt== 0 || $room_teen_amt== null || $room_teen_amt== "")
            {
                $room_teen_claim_exTAX= 0 ;
                $room_teen_claim= 0 ;
                $room_teen_cost_exTAX= 0 ;
                $room_teen_cost= 0 ;
                
                $room_teen_cost_after_rebate_exTAX= 0 ;
                $room_teen_cost_after_rebate= 0 ;
                $room_teen_claim_after_rebate_exTAX= 0 ;
                $room_teen_claim_after_rebate= 0 ;
            }
            else
            {
                $room_teen_cost_exTAX= $_POST["teen_cost"] * ((100 - $tax_value)/100);
                $room_teen_cost= $_POST["teen_cost"];
                $room_teen_claim_exTAX= $_POST["teen_claim"] * ((100 - $tax_value)/100);
                $room_teen_claim= $_POST["teen_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_teen_cost_rebate = $room_teen_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $_POST["room_teen_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_teen_cost_rebate = $_POST["room_teen_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_teen_cost_after_rebate_exTAX = 0; 
                    $room_teen_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_teen_cost_after_rebate_exTAX = $room_teen_cost_exTAX; 
                    $room_teen_cost_rebate =$room_teen_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_teen_claim_rebate = $room_teen_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $_POST["room_teen_claim_rebate"] * ((100 - $tax_value)/100);
                    $room_teen_claim_rebate = $_POST["room_teen_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_teen_claim_after_rebate_exTAX = 0; 
                    $room_teen_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_teen_claim_after_rebate_exTAX = $room_teen_claim_exTAX; 
                    $room_teen_claim_rebate =$room_teen_claim;
                }
            }
            
            if($room_child_amt== 0 || $room_child_amt== null || $room_child_amt== "")
            {
                $room_child_claim_exTAX= 0 ;
                $room_child_claim= 0 ;
                $room_child_cost_exTAX= 0 ;
                $room_child_cost= 0 ;
                
                $room_child_cost_after_rebate_exTAX= 0 ;
                $room_child_cost_after_rebate= 0 ;
                $room_child_claim_after_rebate_exTAX= 0 ;
                $room_child_claim_after_rebate= 0 ;
            }
            else
            {
                $room_child_cost_exTAX= $_POST["child_cost"] * ((100 - $tax_value)/100);
                $room_child_cost= $_POST["child_cost"];
                $room_child_claim_exTAX= $_POST["child_claim"] * ((100 - $tax_value)/100);
                $room_child_claim= $_POST["child_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_child_cost_rebate = $room_child_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $_POST["room_child_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_child_cost_rebate = $_POST["room_child_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_child_cost_after_rebate_exTAX = 0; 
                    $room_child_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_child_cost_after_rebate_exTAX = $room_child_cost_exTAX; 
                    $room_child_cost_rebate =$room_child_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_child_claim_rebate = $room_child_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $_POST["room_child_claim_rebate"] * ((100 - $tax_value)/100);
                    $room_child_claim_rebate = $_POST["room_child_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_child_claim_after_rebate_exTAX = 0; 
                    $room_child_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_child_claim_after_rebate_exTAX = $room_child_claim_exTAX; 
                    $room_child_claim_rebate =$room_child_claim;
                }
            }
            
            if($room_infant_amt== 0 || $room_infant_amt== null || $room_infant_amt== "")
            {
                $room_infant_claim_exTAX= 0 ;
                $room_infant_claim= 0 ;
                $room_infant_cost_exTAX= 0 ;
                $room_infant_cost= 0 ;
                
                $room_infant_cost_after_rebate_exTAX= 0 ;
                $room_infant_cost_after_rebate= 0 ;
                $room_infant_claim_after_rebate_exTAX= 0 ;
                $room_infant_claim_after_rebate= 0 ;
            }
            else
            {
                $room_infant_cost_exTAX= $_POST["infant_cost"] * ((100 - $tax_value)/100);
                $room_infant_cost= $_POST["infant_cost"];
                $room_infant_claim_exTAX= $_POST["infant_claim"] * ((100 - $tax_value)/100);
                $room_infant_claim= $_POST["infant_claim"];
                
                if ($room_rebate_cost_type == "Percenatge")
                {
                    $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                    $room_infant_cost_rebate = $room_infant_cost* ((100 - $room_rebate_cost_percentage)/100); 
                }
                else if ($room_rebate_cost_type == "Fixed Tariff")
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $_POST["room_infant_cost_rebate"] * ((100 - $tax_value)/100);
                    $room_infant_cost_rebate = $_POST["room_infant_cost_rebate"];
                }
                else if ($room_rebate_cost_type == "FOC")
                {
                    $room_rebate_cost_percentage = 100;
                    $room_infant_cost_after_rebate_exTAX = 0; 
                    $room_infant_cost_rebate = 0;
                }
                else
                {
                    $room_rebate_cost_percentage = 0;
                    $room_infant_cost_after_rebate_exTAX = $room_infant_cost_exTAX; 
                    $room_infant_cost_rebate =$room_infant_cost;
                }
                
                if ($room_rebate_claim_type == "Percenatge")
                {
                    $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                    $room_infant_claim_rebate = $room_infant_claim* ((100 - $room_rebate_claim_percentage)/100); 
                }
                else if ($room_rebate_claim_type == "Fixed Tariff")
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $_POST["room_infant_claim_rebate"] * ((100 - $tax_value)/100);
                    $room_infant_claim_rebate = $_POST["room_infant_claim_rebate"];
                }
                else if ($room_rebate_claim_type == "FOC")
                {
                    $room_rebate_claim_percentage = 100;
                    $room_infant_claim_after_rebate_exTAX = 0; 
                    $room_infant_claim_rebate = 0;
                }
                else
                {
                    $room_rebate_claim_percentage = 0;
                    $room_infant_claim_after_rebate_exTAX = $room_infant_claim_exTAX; 
                    $room_infant_claim_rebate =$room_infant_claim;
                }
            }
            
            $room_total_cost_exTAX = (($room_adult_amt * $room_adult_cost_exTAX) +($room_teen_amt * $room_teen_cost_exTAX) + ($room_child_amt * $room_child_cost_exTAX) +($room_infant_amt * $room_infant_cost_exTAX));
            $room_total_cost = (($room_adult_amt * $room_adult_cost) +($room_teen_amt * $room_teen_cost) + ($room_child_amt * $room_child_cost) +($room_infant_amt * $room_infant_cost));
            $room_total_cost_after_rebate_exTAX = (($room_adult_amt * $room_adult_cost_after_rebate_exTAX) +($room_teen_amt * $room_teen_cost_after_rebate_exTAX) + ($room_child_amt * $room_child_cost_after_rebate_exTAX) +($room_infant_amt * $room_infant_cost_after_rebate_exTAX));
            $room_total_cost_after_rebate = (($room_adult_amt * $room_adult_cost_rebate) +($room_teen_amt * $room_teen_cost_rebate) + ($room_child_amt * $room_child_cost_rebate) +($room_infant_amt * $room_infant_cost_after_rebate_exTAX));
            
            $room_total_claim_exTAX = (($room_adult_amt * $room_adult_claim_exTAX) +($room_teen_amt * $room_teen_claim_exTAX) + ($room_child_amt * $room_child_claim_exTAX) +($room_infant_amt * $room_infant_claim_exTAX));
            $room_total_claim = (($room_adult_amt * $room_adult_claim) +($room_teen_amt * $room_teen_claim) + ($room_child_amt * $room_child_claim) +($room_infant_amt * $room_infant_claim));
            $room_total_claim_after_rebate_exTAX = (($room_adult_amt * $room_adult_claim_after_rebate_exTAX) +($room_teen_amt * $room_teen_claim_after_rebate_exTAX) + ($room_child_amt * $room_child_claim_after_rebate_exTAX) +($room_infant_amt * $room_infant_claim_after_rebate_exTAX));
            $room_total_claim_after_rebate = (($room_adult_amt * $room_adult_claim_rebate) +($room_teen_amt * $room_teen_claim_rebate) + ($room_child_amt * $room_child_claim_rebate) +($room_infant_amt * $room_infant_claim_rebate));
                
        }
        else
        {
            // ROOM PER UINT
            $room_adult_cost_exTAX= $_POST["unit_cost"] * ((100 - $tax_value)/100);
            $room_adult_cost= $_POST["unit_cost"];
            $room_adult_claim_exTAX= $_POST["unit_claim"] * ((100 - $tax_value)/100);
            $room_adult_claim= $_POST["unit_claim"];
            $room_teen_cost_exTAX= 0 ;
            $room_teen_cost= 0 ;
            $room_teen_claim_exTAX= 0 ;
            $room_teen_claim= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim= 0 ;
            $room_child_cost_exTAX= 0 ;
            $room_child_cost= 0 ;
            $room_infant_claim_exTAX= 0 ;
            $room_infant_claim= 0 ;
            $room_infant_cost_exTAX= 0 ;
            $room_infant_cost= 0 ;
            $room_teen_cost_after_rebate_exTAX= 0 ;
            $room_teen_cost_after_rebate= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_teen_claim_after_rebate_exTAX= 0 ;
            $room_child_claim_exTAX= 0 ;
            $room_child_claim_rebate= 0 ;
            $room_child_cost_after_rebate_exTAX= 0 ;
            $room_child_cost_after_rebate= 0 ;
            $room_infant_claim_after_rebate_exTAX= 0 ;
            $room_infant_claim_rebate= 0 ;
            $room_infant_cost_after_rebate_exTAX= 0 ;
            $room_infant_cost_after_rebate= 0 ;
                
            if ($room_rebate_cost_type == "Percenatge")
            {
                $room_rebate_cost_percentage = trim($_POST["room_rebate_cost_percentage"]);
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX* ((100 - $room_rebate_cost_percentage)/100); 
                $room_adult_cost_rebate = $room_adult_cost* ((100 - $room_rebate_cost_percentage)/100); 
            }
            else if ($room_rebate_cost_type == "Fixed Tariff")
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $_POST["room_adult_cost_rebate"] * ((100 - $tax_value)/100);
                $room_adult_cost_rebate = $_POST["room_adult_cost_rebate"];
            }
            else if ($room_rebate_cost_type == "FOC")
            {
                $room_rebate_cost_percentage = 100;
                $room_adult_cost_after_rebate_exTAX = 0; 
                $room_adult_cost_rebate = 0;
            }
            else
            {
                $room_rebate_cost_percentage = 0;
                $room_adult_cost_after_rebate_exTAX = $room_adult_cost_exTAX; 
                $room_adult_cost_rebate =$room_adult_cost;
            }

            if ($room_rebate_claim_type == "Percenatge")
            {
                $room_rebate_claim_percentage = trim($_POST["room_rebate_claim_percentage"]);
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX* ((100 - $room_rebate_claim_percentage)/100); 
                $room_adult_claim_rebate = $room_adult_claim* ((100 - $room_rebate_claim_percentage)/100); 
            }
            else if ($room_rebate_claim_type == "Fixed Tariff")
            {
                $room_rebate_claim_percentage = 0;
                $room_adult_claim_after_rebate_exTAX = $_POST["unit_claim"] * ((100 - $tax_value)/100);
                $room_adult_claim_rebate = $_POST["unit_claim"];
            }
            else if ($room_rebate_claim_type == "FOC")
            {
                $room_rebate_claim_percentage = 100;
                $room_adult_claim_after_rebate_exTAX = 0; 
                $room_adult_claim_rebate = 0;
            }
            else
            {
                $room_rebate_claim_percentage = 0;
                $room_adult_claim_after_rebate_exTAX = $room_adult_claim_exTAX; 
                $room_adult_claim_rebate =$room_adult_claim;
            }
            
            $room_total_claim_exTAX = $room_adult_claim_exTAX;
            $room_total_claim = $room_total_claim_exTAX;
            $room_total_claim_after_rebate_exTAX = $room_adult_claim_after_rebate_exTAX;
            $room_total_claim_after_rebate = $room_total_claim_after_rebate;
            
            $room_total_cost_exTAX = $room_adult_cost;
            $room_total_cost = $room_adult_cost;   
            $room_total_cost_after_rebate_exTAX = $room_adult_cost_after_rebate_exTAX;
            $room_total_cost_after_rebate = $room_adult_cost_rebate;   
        }
    }
    
    
    $sqlSaveRoomClaim= "
    INSERT INTO booking_room_claim
        (
            id_booking,
            id_booking_room,
            room_service_paid_by,
            id_tour_operator,
            id_client,
            room_stay_from,
            room_stay_to,
            room_booking_date,
            id_contract,
            id_hotel,
            hotelname,
            id_room,
            room_details,
            room_claim_calcultation,
            room_adult_amt,
            room_teen_amt,
            room_child_amt,
            room_infant_amt,
            room_total_pax,
            id_dept,
            room_charge,
            id_service_tax,
            tax_value,
            room_adult_claim_exTAX,
            room_adult_claim,
            room_teen_claim_exTAX,
            room_teen_claim,
            room_child_claim_exTAX,
            room_child_claim,
            room_infant_claim_exTAX,
            room_infant_claim,
            room_total_claim_exTAX,
            room_total_claim,
            room_rebate_claim_type,
            room_rebate_claim_approve_by,
            room_rebate_claim_percentage,
            room_adult_claim_rebate,
            room_adult_claim_after_rebate_exTAX,
            room_adult_claim_after_rebate,
            room_teen_claim_rebate,
            room_teen_claim_after_rebate,
            room_teen_claim_after_rebate_exTAX,
            room_child_claim_rebate,
            room_child_claim_after_rebate,
            room_child_claim_after_rebate_exTAX,
            room_infant_claim_rebate,
            room_infant_claim_after_rebate,
            room_infant_claim_after_rebate_exTAX,
            room_total_claim_after_rebate_exTAX,
            room_total_claim_after_rebate,
            room_remarks,
            room_internal_remarks,
            room_status,
            created_by,
            created_name
        )
    VALUES
        (
            :id_booking,
            :id_booking_room.
            :room_service_paid_by.
            :id_tour_operator.
            :id_client.
            :room_stay_from.
            :room_stay_to.
            :room_booking_date.
            :id_contract.
            :id_hotel.
            :hotelname.
            :id_room.
            :room_details.
            :room_claim_calcultation.
            :room_adult_amt.
            :room_teen_amt.
            :room_child_amt.
            :room_infant_amt.
            :room_total_pax.
            :id_dept.
            :room_charge.
            :id_service_tax.
            :tax_value,
            :room_adult_claim_exTAX.
            :room_adult_claim.
            :room_teen_claim_exTAX.
            :room_teen_claim.
            :room_child_claim_exTAX.
            :room_child_claim.
            :room_infant_claim_exTAX.
            :room_infant_claim.
            :room_total_claim_exTAX.
            :room_total_claim.
            :room_rebate_claim_type.
            :room_rebate_claim_approve_by.
            :room_rebate_claim_percentage.
            :room_adult_claim_rebate.
            :room_adult_claim_after_rebate_exTAX.
            :room_adult_claim_after_rebate.
            :room_teen_claim_rebate.
            :room_teen_claim_after_rebate.
            :room_teen_claim_after_rebate_exTAX.
            :room_child_claim_rebate.
            :room_child_claim_after_rebate.
            :room_child_claim_after_rebate_exTAX.
            :room_infant_claim_rebate.
            :room_infant_claim_after_rebate.
            :room_infant_claim_after_rebate_exTAX.
            :room_total_claim_after_rebate_exTAX.
            :room_total_claim_after_rebate.
            :room_remarks.
            :room_internal_remarks.
            :room_status,
            :created_by.
            :created_name
          )";
    
    
    $stmt = $con->prepare($sqlSaveRoomClaim);
    $stmt->execute(array(
        ":id_booking"                                          =>$id_booking,
        ":id_booking_room"                                =>$id_booking_room,
        ":room_service_paid_by"                        =>$room_service_paid_by,
        ":id_tour_operator"                                 =>$id_tour_operator,
        ":id_client"                                              =>$id_client,
        ":room_stay_from"                                  =>$room_stay_from,
        ":room_stay_to"                                      =>$room_stay_to,
        ":room_booking_date"                            =>$room_booking_date,
        ":id_contract"                                          =>$id_contract,
        ":id_hotel"                                               =>$id_hotel,
        ":hotelname"                                           =>$hotelname,
        ":id_room"                                               =>$id_room,
        ":room_details"                                        =>$room_details,
        ":room_claim_calcultation"                      =>$room_claim_calcultation,
        ":room_adult_amt"                                   =>$room_adult_amt,
        ":room_teen_amt"                                    =>$room_teen_amt,
        ":room_child_amt"                                   =>$room_child_amt,
        ":room_infant_amt"                                  =>$room_infant_amt,
        ":room_total_pax"                                     =>$room_total_pax,
        ":id_dept"                                                  =>$id_dept,
        ":room_charge"                                          =>$room_charge,
        ":id_service_tax"                                        =>$id_service_tax,
        ":tax_value"                                                =>$tax_value,
        ":room_adult_claim_exTAX"                        =>$room_adult_claim_exTAX,
        ":room_adult_claim"                                    =>$room_adult_claim,
        ":room_teen_claim_exTAX"                         =>$room_teen_claim_exTAX,
        ":room_teen_claim"                                     =>$room_teen_claim,
        ":room_child_claim_exTAX"                         =>$room_child_claim_exTAX,
        ":room_child_claim"                                     =>$room_child_claim,
        ":room_infant_claim_exTAX"                        =>$room_infant_claim_exTAX,
        ":room_infant_claim"                                    =>$room_infant_claim,
        ":room_total_claim_exTAX"                           =>$room_total_claim_exTAX,
        ":room_total_claim"                                      =>$room_total_claim,
        ":room_rebate_claim_type"                           =>$room_rebate_claim_type,
        ":room_rebate_claim_approve_by"                =>$room_rebate_claim_approve_by,
        ":room_rebate_claim_percentage"                 =>$room_rebate_claim_percentage,
        ":room_adult_claim_rebate"                          =>$room_adult_claim_rebate,
        ":room_adult_claim_after_rebate_exTAX"      =>$room_adult_claim_after_rebate_exTAX,
        ":room_adult_claim_after_rebate"                  =>$room_adult_claim_after_rebate,
        ":room_teen_claim_rebate"                            =>$room_teen_claim_rebate,
        ":room_teen_claim_after_rebate"                   =>$room_teen_claim_after_rebate,
        ":room_teen_claim_after_rebate_exTAX"        =>$room_teen_claim_after_rebate_exTAX,
        ":room_child_claim_rebate"                            =>$room_child_claim_rebate,
        ":room_child_claim_after_rebate"                   =>$room_child_claim_after_rebate,
        ":room_child_claim_after_rebate_exTAX"       =>$room_child_claim_after_rebate_exTAX,
        ":room_infant_claim_rebate"                          =>$room_infant_claim_rebate,
        ":room_infant_claim_after_rebate"                 =>$room_infant_claim_after_rebate,
        ":room_infant_claim_after_rebate_exTAX"      =>$room_infant_claim_after_rebate_exTAX,
        ":room_total_claim_after_rebate_exTAX"        =>$room_total_claim_after_rebate_exTAX,
        ":room_total_claim_after_rebate"                    =>$room_total_claim_after_rebate,
        ":room_remarks"                                             =>$room_remarks,
        ":room_internal_remarks"                                =>$room_internal_remarks,
        ":room_status"                                                 =>$room_status,
        ":created_by"                                                   =>$created_by,
        ":created_name"                                              =>$created_name
    ));
    
    $id_booking_room_claim = $con->lastInsertId();    
      
    $sqlSaveRoomCost= "
    INSERT INTO booking_room_cost
        (
            id_booking_room_claim,
            id_booking,
            id_booking_room,
            room_service_paid_by,
            id_tour_operator,
            id_client,
            room_stay_from,
            room_stay_to,
            room_booking_date,
            id_contract,
            id_hotel,
            hotelname,
            id_room,
            room_details,
            room_cost_calcultation,
            room_adult_amt,
            room_teen_amt,
            room_child_amt,
            room_infant_amt,
            room_total_pax,
            id_dept,
            room_charge,
            id_service_tax,
            tax_value,
            room_adult_cost_exTAX,
            room_adult_cost,
            room_teen_cost_exTAX,
            room_teen_cost,
            room_child_cost_exTAX,
            room_child_cost,
            room_infant_cost_exTAX,
            room_infant_cost,
            room_total_cost_exTAX,
            room_total_cost,
            room_rebate_cost_type,
            room_rebate_cost_approve_by,
            room_rebate_cost_percentage,
            room_adult_cost_rebate,
            room_adult_cost_after_rebate_exTAX,
            room_adult_cost_after_rebate,
            room_teen_cost_rebate,
            room_teen_cost_after_rebate,
            room_teen_cost_after_rebate_exTAX,
            room_child_cost_rebate,
            room_child_cost_after_rebate,
            room_child_cost_after_rebate_exTAX,
            room_infant_cost_rebate,
            room_infant_cost_after_rebate,
            room_infant_cost_after_rebate_exTAX,
            room_total_cost_after_rebate_exTAX,
            room_total_cost_after_rebate,
            created_by,
            created_name
        )
    VALUES
        (
            :id_booking_room_claim,
            :id_booking,
            :id_booking_room,
            :room_service_paid_by.
            :id_tour_operator.
            :id_client.
            :room_stay_from.
            :room_stay_to.
            :room_booking_date.
            :id_contract.
            :id_hotel.
            :hotelname.
            :id_room.
            :room_details.
            :room_cost_calcultation.
            :room_adult_amt.
            :room_teen_amt.
            :room_child_amt.
            :room_infant_amt.
            :room_total_pax.
            :id_dept.
            :room_charge.
            :id_service_tax.
            :tax_value,
            :room_adult_cost_exTAX.
            :room_adult_cost.
            :room_teen_cost_exTAX.
            :room_teen_cost.
            :room_child_cost_exTAX.
            :room_child_cost.
            :room_infant_cost_exTAX.
            :room_infant_cost.
            :room_total_cost_exTAX.
            :room_total_cost.
            :room_rebate_cost_type.
            :room_rebate_cost_approve_by.
            :room_rebate_cost_percentage.
            :room_adult_cost_rebate.
            :room_adult_cost_after_rebate_exTAX.
            :room_adult_cost_after_rebate.
            :room_teen_cost_rebate.
            :room_teen_cost_after_rebate.
            :room_teen_cost_after_rebate_exTAX.
            :room_child_cost_rebate.
            :room_child_cost_after_rebate.
            :room_child_cost_after_rebate_exTAX.
            :room_infant_cost_rebate.
            :room_infant_cost_after_rebate.
            :room_infant_cost_after_rebate_exTAX.
            :room_total_cost_after_rebate_exTAX.
            :room_total_cost_after_rebate.
            :created_by.
            :created_name
          )";

    $stmt = $con->prepare($sqlSaveRoomCost);
    $stmt->execute(array(
            ":id_booking_room_claim"                      =>$id_booking_room_claim,
            ":id_booking"                                          =>$id_booking,
            ":id_booking_room"                                =>$id_booking_room,
            ":room_service_paid_by"                        =>$room_service_paid_by,
            ":id_tour_operator"                                 =>$id_tour_operator,
            ":id_client"                                              =>$id_client,
            ":room_stay_from"                                  =>$room_stay_from,
            ":room_stay_to"                                      =>$room_stay_to,
            ":room_booking_date"                            =>$room_booking_date,
            ":id_contract"                                          =>$id_contract,
            ":id_hotel"                                               =>$id_hotel,
            ":hotelname"                                           =>$hotelname,
            ":id_room"                                               =>$id_room,
            ":room_details"                                        =>$room_details,
            ":room_cost_calcultation"                       =>$room_cost_calcultation,
            ":room_adult_amt"                                   =>$room_adult_amt,
            ":room_teen_amt"                                    =>$room_teen_amt,
            ":room_child_amt"                                   =>$room_child_amt,
            ":room_infant_amt"                                  =>$room_infant_amt,
            ":room_total_pax"                                     =>$room_total_pax,
            ":id_dept"                                                  =>$id_dept,
            ":room_charge"                                          =>$room_charge,
            ":id_service_tax"                                        =>$id_service_tax,
            ":tax_value"                                                =>$tax_value,
            ":room_adult_cost_exTAX"                        =>$room_adult_cost_exTAX,
            ":room_adult_cost"                                    =>$room_adult_cost,
            ":room_teen_cost_exTAX"                         =>$room_teen_cost_exTAX,
            ":room_teen_cost"                                     =>$room_teen_cost,
            ":room_child_cost_exTAX"                         =>$room_child_cost_exTAX,
            ":room_child_cost"                                     =>$room_child_cost,
            ":room_infant_cost_exTAX"                        =>$room_infant_cost_exTAX,
            ":room_infant_cost"                                    =>$room_infant_cost,
            ":room_total_cost_exTAX"                           =>$room_total_cost_exTAX,
            ":room_total_cost"                                      =>$room_total_cost,
            ":room_rebate_cost_type"                           =>$room_rebate_cost_type,
            ":room_rebate_cost_approve_by"                =>$room_rebate_cost_approve_by,
            ":room_rebate_cost_percentage"                 =>$room_rebate_cost_percentage,
            ":room_adult_cost_rebate"                          =>$room_adult_cost_rebate,
            ":room_adult_cost_after_rebate_exTAX"      =>$room_adult_cost_after_rebate_exTAX,
            ":room_adult_cost_after_rebate"                  =>$room_adult_cost_after_rebate,
            ":room_teen_cost_rebate"                            =>$room_teen_cost_rebate,
            ":room_teen_cost_after_rebate"                   =>$room_teen_cost_after_rebate,
            ":room_teen_cost_after_rebate_exTAX"        =>$room_teen_cost_after_rebate_exTAX,
            ":room_child_cost_rebate"                            =>$room_child_cost_rebate,
            ":room_child_cost_after_rebate"                   =>$room_child_cost_after_rebate,
            ":room_child_cost_after_rebate_exTAX"       =>$room_child_cost_after_rebate_exTAX,
            ":room_infant_cost_rebate"                          =>$room_infant_cost_rebate,
            ":room_infant_cost_after_rebate"                 =>$room_infant_cost_after_rebate,
            ":room_infant_cost_after_rebate_exTAX"      =>$room_infant_cost_after_rebate_exTAX,
            ":room_total_cost_after_rebate_exTAX"        =>$room_total_cost_after_rebate_exTAX,
            ":room_total_cost_after_rebate"                    =>$room_total_cost_after_rebate,
            ":created_by"                                                 =>$created_by,
            ":created_name"                                            =>$created_name
        ));

    $id_booking_room_cost = $con->lastInsertId();    

     // CLIENT ROOM
    $sqlClientRoom = "INSERT INTO booking_room_client (id_client, id_booking_room_claim,id_booking) 
    VALUES (:booking_client, :id_booking_room_claim,:id_booking)";

    $stmt = $con->prepare($sqlClientRoom);
    $data = $room_clients;

    foreach($data as $d) {
        $stmt->execute(array( ':booking_client' => $d,':id_booking_room_claim' => $id_booking_room_claim,':id_booking' => $id_booking));
    }

    // CLIENT ROOM
    $sqlSaveRoomClaimLog= "
    INSERT INTO booking_room_claim_log
        (
            id_booking_room_claim,
            id_booking,
            id_booking_room,
            room_service_paid_by,
            id_tour_operator,
            id_client,
            room_stay_from,
            room_stay_to,
            room_booking_date,
            id_contract,
            id_hotel,
            hotelname,
            id_room,
            room_details,
            room_clients,
            room_claim_calcultation,
            room_adult_amt,
            room_teen_amt,
            room_child_amt,
            room_infant_amt,
            room_total_pax,
            id_dept,
            room_charge,
            id_service_tax,
            tax_value,
            room_adult_claim_exTAX,
            room_adult_claim,
            room_teen_claim_exTAX,
            room_teen_claim,
            room_child_claim_exTAX,
            room_child_claim,
            room_infant_claim_exTAX,
            room_infant_claim,
            room_total_claim_exTAX,
            room_total_claim,
            room_rebate_claim_type,
            room_rebate_claim_approve_by,
            room_rebate_claim_percentage,
            room_adult_claim_rebate,
            room_adult_claim_after_rebate_exTAX,
            room_adult_claim_after_rebate,
            room_teen_claim_rebate,
            room_teen_claim_after_rebate,
            room_teen_claim_after_rebate_exTAX,
            room_child_claim_rebate,
            room_child_claim_after_rebate,
            room_child_claim_after_rebate_exTAX,
            room_infant_claim_rebate,
            room_infant_claim_after_rebate,
            room_infant_claim_after_rebate_exTAX,
            room_total_claim_after_rebate_exTAX,
            room_total_claim_after_rebate,
            room_remarks,
            room_internal_remarks,
            room_status,
            id_user,
            uname,
            log_status
        )
    VALUES
        (
            :id_booking_room_claim,
            :id_booking,
            :id_booking_room,
            :room_service_paid_by.
            :id_tour_operator.
            :id_client.
            :room_stay_from.
            :room_stay_to.
            :room_booking_date.
            :id_contract.
            :id_hotel.
            :hotelname.
            :id_room.
            :room_details.
            :room_clients.
            :room_claim_calcultation.
            :room_adult_amt.
            :room_teen_amt.
            :room_child_amt.
            :room_infant_amt.
            :room_total_pax.
            :id_dept.
            :room_charge.
            :id_service_tax.
            :tax_value,
            :room_adult_claim_exTAX.
            :room_adult_claim.
            :room_teen_claim_exTAX.
            :room_teen_claim.
            :room_child_claim_exTAX.
            :room_child_claim.
            :room_infant_claim_exTAX.
            :room_infant_claim.
            :room_total_claim_exTAX.
            :room_total_claim.
            :room_rebate_claim_type.
            :room_rebate_claim_approve_by.
            :room_rebate_claim_percentage.
            :room_adult_claim_rebate.
            :room_adult_claim_after_rebate_exTAX.
            :room_adult_claim_after_rebate.
            :room_teen_claim_rebate.
            :room_teen_claim_after_rebate.
            :room_teen_claim_after_rebate_exTAX.
            :room_child_claim_rebate.
            :room_child_claim_after_rebate.
            :room_child_claim_after_rebate_exTAX.
            :room_infant_claim_rebate.
            :room_infant_claim_after_rebate.
            :room_infant_claim_after_rebate_exTAX.
            :room_total_claim_after_rebate_exTAX.
            :room_total_claim_after_rebate.
            :room_remarks.
            :room_internal_remarks.
            :room_status,
            :id_user,
            :uname,
            :log_status
          )";

    $stmt = $con->prepare($sqlSaveRoomClaim);
    $stmt->execute(array(
            ":id_booking_room_claim"                      =>$id_booking_room_claim,
            ":id_booking"                                          =>$id_booking,
            ":id_booking_room"                                =>$id_booking_room,
            ":room_service_paid_by"                        =>$room_service_paid_by,
            ":id_tour_operator"                                 =>$id_tour_operator,
            ":id_client"                                              =>$id_client,
            ":room_stay_from"                                  =>$room_stay_from,
            ":room_stay_to"                                      =>$room_stay_to,
            ":room_booking_date"                            =>$room_booking_date,
            ":id_contract"                                          =>$id_contract,
            ":id_hotel"                                               =>$id_hotel,
            ":hotelname"                                           =>$hotelname,
            ":id_room"                                               =>$id_room,
            ":room_details"                                        =>$room_details,
            ":room_clients"                                        =>implode( ", ", $room_clients ),
            ":room_claim_calcultation"                      =>$room_claim_calcultation,
            ":room_adult_amt"                                   =>$room_adult_amt,
            ":room_teen_amt"                                    =>$room_teen_amt,
            ":room_child_amt"                                   =>$room_child_amt,
            ":room_infant_amt"                                  =>$room_infant_amt,
            ":room_total_pax"                                     =>$room_total_pax,
            ":id_dept"                                                  =>$id_dept,
            ":room_charge"                                          =>$room_charge,
            ":id_service_tax"                                        =>$id_service_tax,
            ":tax_value"                                                =>$tax_value,
            ":room_adult_claim_exTAX"                        =>$room_adult_claim_exTAX,
            ":room_adult_claim"                                    =>$room_adult_claim,
            ":room_teen_claim_exTAX"                         =>$room_teen_claim_exTAX,
            ":room_teen_claim"                                     =>$room_teen_claim,
            ":room_child_claim_exTAX"                         =>$room_child_claim_exTAX,
            ":room_child_claim"                                     =>$room_child_claim,
            ":room_infant_claim_exTAX"                        =>$room_infant_claim_exTAX,
            ":room_infant_claim"                                    =>$room_infant_claim,
            ":room_total_claim_exTAX"                           =>$room_total_claim_exTAX,
            ":room_total_claim"                                      =>$room_total_claim,
            ":room_rebate_claim_type"                         =>$room_rebate_claim_type,
            ":room_rebate_claim_approve_by"                =>$room_rebate_claim_approve_by,
            ":room_rebate_claim_percentage"                 =>$room_rebate_claim_percentage,
            ":room_adult_claim_rebate"                          =>$room_adult_claim_rebate,
            ":room_adult_claim_after_rebate_exTAX"      =>$room_adult_claim_after_rebate_exTAX,
            ":room_adult_claim_after_rebate"                  =>$room_adult_claim_after_rebate,
            ":room_teen_claim_rebate"                            =>$room_teen_claim_rebate,
            ":room_teen_claim_after_rebate"                   =>$room_teen_claim_after_rebate,
            ":room_teen_claim_after_rebate_exTAX"        =>$room_teen_claim_after_rebate_exTAX,
            ":room_child_claim_rebate"                            =>$room_child_claim_rebate,
            ":room_child_claim_after_rebate"                   =>$room_child_claim_after_rebate,
            ":room_child_claim_after_rebate_exTAX"       =>$room_child_claim_after_rebate_exTAX,
            ":room_infant_claim_rebate"                          =>$room_infant_claim_rebate,
            ":room_infant_claim_after_rebate"                 =>$room_infant_claim_after_rebate,
            ":room_infant_claim_after_rebate_exTAX"      =>$room_infant_claim_after_rebate_exTAX,
            ":room_total_claim_after_rebate_exTAX"        =>$room_total_claim_after_rebate_exTAX,
            ":room_total_claim_after_rebate"                    =>$room_total_claim_after_rebate,
            ":room_remarks"                                             =>$room_remarks,
            ":room_internal_remarks"                                =>$room_internal_remarks,
            ":room_status"                                                 =>$room_status,
             ":id_user"                                                        => $id_user,
             ":uname"                                                         => $uname,
             ":log_status"                                                    => $log_status
        ));


        $sqlSaveRoomCost= "
        INSERT INTO booking_room_cost_log
            (
                id_booking_room_cost,
                id_booking_room_claim,
                id_booking,
                id_booking_room,
                room_service_paid_by,
                id_tour_operator,
                id_client,
                room_stay_from,
                room_stay_to,
                room_booking_date,
                id_contract,
                id_hotel,
                hotelname,
                id_room,
                room_details,
                room_cost_calcultation,
                room_adult_amt,
                room_teen_amt,
                room_child_amt,
                room_infant_amt,
                room_total_pax,
                id_dept,
                room_charge,
                id_service_tax,
                tax_value,
                room_adult_cost_exTAX,
                room_adult_cost,
                room_teen_cost_exTAX,
                room_teen_cost,
                room_child_cost_exTAX,
                room_child_cost,
                room_infant_cost_exTAX,
                room_infant_cost,
                room_total_cost_exTAX,
                room_total_cost,
                room_rebate_cost_type,
                room_rebate_cost_approve_by,
                room_rebate_cost_percentage,
                room_adult_cost_rebate,
                room_adult_cost_after_rebate_exTAX,
                room_adult_cost_after_rebate,
                room_teen_cost_rebate,
                room_teen_cost_after_rebate,
                room_teen_cost_after_rebate_exTAX,
                room_child_cost_rebate,
                room_child_cost_after_rebate,
                room_child_cost_after_rebate_exTAX,
                room_infant_cost_rebate,
                room_infant_cost_after_rebate,
                room_infant_cost_after_rebate_exTAX,
                room_total_cost_after_rebate_exTAX,
                room_total_cost_after_rebate,
                id_user,
                uname,
                log_status
            )
        VALUES
            (
                :id_booking_room_cost,
                :id_booking_room_claim,
                :id_booking,
                :id_booking_room,
                :room_service_paid_by.
                :id_tour_operator.
                :id_client.
                :room_stay_from.
                :room_stay_to.
                :room_booking_date.
                :id_contract.
                :id_hotel.
                :hotelname.
                :id_room.
                :room_details.
                :room_cost_calcultation.
                :room_adult_amt.
                :room_teen_amt.
                :room_child_amt.
                :room_infant_amt.
                :room_total_pax.
                :id_dept.
                :room_charge.
                :id_service_tax.
                :tax_value,
                :room_adult_cost_exTAX.
                :room_adult_cost.
                :room_teen_cost_exTAX.
                :room_teen_cost.
                :room_child_cost_exTAX.
                :room_child_cost.
                :room_infant_cost_exTAX.
                :room_infant_cost.
                :room_total_cost_exTAX.
                :room_total_cost.
                :room_rebate_cost_type.
                :room_rebate_cost_approve_by.
                :room_rebate_cost_percentage.
                :room_adult_cost_rebate.
                :room_adult_cost_after_rebate_exTAX.
                :room_adult_cost_after_rebate.
                :room_teen_cost_rebate.
                :room_teen_cost_after_rebate.
                :room_teen_cost_after_rebate_exTAX.
                :room_child_cost_rebate.
                :room_child_cost_after_rebate.
                :room_child_cost_after_rebate_exTAX.
                :room_infant_cost_rebate.
                :room_infant_cost_after_rebate.
                :room_infant_cost_after_rebate_exTAX.
                :room_total_cost_after_rebate_exTAX.
                :room_total_cost_after_rebate.
                :id_user,
                :uname,
                :log_status
              )";

        $stmt = $con->prepare($sqlSaveRoomCost);
        $stmt->execute(array(
                ":id_booking_room_cost"                      =>$id_booking_room_cost,
                ":id_booking_room_claim"                      =>$id_booking_room_claim,
                ":id_booking"                                          =>$id_booking,
                ":id_booking_room"                                =>$id_booking_room,
                ":room_service_paid_by"                        =>$room_service_paid_by,
                ":id_tour_operator"                                 =>$id_tour_operator,
                ":id_client"                                              =>$id_client,
                ":room_stay_from"                                  =>$room_stay_from,
                ":room_stay_to"                                      =>$room_stay_to,
                ":room_booking_date"                            =>$room_booking_date,
                ":id_contract"                                          =>$id_contract,
                ":id_hotel"                                               =>$id_hotel,
                ":hotelname"                                           =>$hotelname,
                ":id_room"                                               =>$id_room,
                ":room_details"                                        =>$room_details,
                ":room_cost_calcultation"                       =>$room_cost_calcultation,
                ":room_adult_amt"                                   =>$room_adult_amt,
                ":room_teen_amt"                                    =>$room_teen_amt,
                ":room_child_amt"                                   =>$room_child_amt,
                ":room_infant_amt"                                  =>$room_infant_amt,
                ":room_total_pax"                                     =>$room_total_pax,
                ":id_dept"                                                  =>$id_dept,
                ":room_charge"                                          =>$room_charge,
                ":id_service_tax"                                        =>$id_service_tax,
                ":tax_value"                                                =>$tax_value,
                ":room_adult_cost_exTAX"                        =>$room_adult_cost_exTAX,
                ":room_adult_cost"                                    =>$room_adult_cost,
                ":room_teen_cost_exTAX"                         =>$room_teen_cost_exTAX,
                ":room_teen_cost"                                     =>$room_teen_cost,
                ":room_child_cost_exTAX"                         =>$room_child_cost_exTAX,
                ":room_child_cost"                                     =>$room_child_cost,
                ":room_infant_cost_exTAX"                        =>$room_infant_cost_exTAX,
                ":room_infant_cost"                                    =>$room_infant_cost,
                ":room_total_cost_exTAX"                           =>$room_total_cost_exTAX,
                ":room_total_cost"                                      =>$room_total_cost,
                ":room_rebate_cost_type"                          =>$room_rebate_cost_type,
                ":room_rebate_cost_approve_by"                =>$room_rebate_cost_approve_by,
                ":room_rebate_cost_percentage"                 =>$room_rebate_cost_percentage,
                ":room_adult_cost_rebate"                          =>$room_adult_cost_rebate,
                ":room_adult_cost_after_rebate_exTAX"      =>$room_adult_cost_after_rebate_exTAX,
                ":room_adult_cost_after_rebate"                  =>$room_adult_cost_after_rebate,
                ":room_teen_cost_rebate"                            =>$room_teen_cost_rebate,
                ":room_teen_cost_after_rebate"                   =>$room_teen_cost_after_rebate,
                ":room_teen_cost_after_rebate_exTAX"        =>$room_teen_cost_after_rebate_exTAX,
                ":room_child_cost_rebate"                            =>$room_child_cost_rebate,
                ":room_child_cost_after_rebate"                   =>$room_child_cost_after_rebate,
                ":room_child_cost_after_rebate_exTAX"       =>$room_child_cost_after_rebate_exTAX,
                ":room_infant_cost_rebate"                          =>$room_infant_cost_rebate,
                ":room_infant_cost_after_rebate"                 =>$room_infant_cost_after_rebate,
                ":room_infant_cost_after_rebate_exTAX"      =>$room_infant_cost_after_rebate_exTAX,
                ":room_total_cost_after_rebate_exTAX"        =>$room_total_cost_after_rebate_exTAX,
                ":room_total_cost_after_rebate"                    =>$room_total_cost_after_rebate,
                 ":id_user"                                                      => $id_user,
                 ":uname"                                                       => $uname,
                 ":log_status"                                                  => $log_status
            ));

            $bookingRoom_result= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_room_claim"=>$id_booking_room_claim, "created_by" =>$created_name);
            echo json_encode($bookingRoom_result); 

} catch (Exception $ex) {
die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

