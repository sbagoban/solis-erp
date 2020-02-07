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
    
    $id_booking_transfer_claim = $_POST["id_booking_transfer_claim"];
    $id_booking = $_POST["id_booking"];
    $transfer_service_paid_by = trim($_POST["transfer_service_paid_by"]);
    $id_tour_operator = $_POST["id_tour_operator"];
    $id_client = $_POST["id_client"];
    $transfer_arrivalDate = trim($_POST["transfer_arrivalDate"]);
    $transfer_arrivalFlight = trim($_POST["transfer_arrivalFlight"]);
    $transfer_arrivalTime = trim($_POST["transfer_arrivalTime"]);
    $transfer_departureDate = trim($_POST["transfer_departureDate"]);
    $transfer_departureFlight = trim($_POST["transfer_departureFlight"]);
    $transfer_departureTime = trim($_POST["transfer_departureTime"]);
    
    $transfer_port = trim($_POST["transfer_port"]);  //special Name
    
    $transfer_destination_from = $_POST["transfer_destination_from"]; // hotel id //service_name
    $qry_hotelDetails = $con->prepare("
		SELECT * FROM tblhotels WHERE id = :transfer_destination_from AND active =1");

	$qry_hotelDetails->execute(array(":transfer_destination_from"=>$transfer_destination_from));

	$row_count_hotelDetails = $qry_hotelDetails->rowCount();
    
	if ($row_count_hotelDetails > 0) 
	{
		while ($rowHotel = $qry_hotelDetails->fetch(PDO::FETCH_ASSOC))
		{
			$id_destination_from = $rowHotel["id"];
			$destination_from_name = $rowHotel["hotelname"];
			$id_transfer_coast_from = $rowHotel["id_transfer_coast"];
		}
	}
    else
    {
			$id_destination_from = $transfer_destination_from;
			$destination_from_name = " ";
			$id_transfer_coast_from = 0;
    }
    
    $transfer_destination_to = $_POST["transfer_destination_to"]; // hotel id //service_name
    $qry_hotelDetails = $con->prepare("
		SELECT * FROM tblhotels WHERE id = :transfer_destination_to AND active =1");

	$qry_hotelDetails->execute(array(":transfer_destination_to"=>$transfer_destination_to));

	$row_count_hotelDetails = $qry_hotelDetails->rowCount();
    
	if ($row_count_hotelDetails > 0) 
	{
		while ($rowHotel = $qry_hotelDetails->fetch(PDO::FETCH_ASSOC))
		{
			$id_destination_to = $rowHotel["id"];
			$destination_to_name = $rowHotel["hotelname"];
			$id_transfer_coast_to = $rowHotel["id_transfer_coast"];
		}
	}
    else
    {
			$id_destination_to = $transfer_destination_to;
			$destination_to_name = " ";
			$id_transfer_coast_to = 0;
    }
    
    $transfer_booking_date = trim($_POST["transfer_booking_date"]);
    $transfer_option = trim($_POST["transfer_type"]);
    $transfer_vehicle = $_POST["transfer_vehicle"]; // id_product
    
    $transfer_adult_amt = trim($_POST["transfer_adultAmt"]);
    $transfer_child_amt = trim($_POST["transfer_childAmt"]);
    $transfer_infant_amt = trim($_POST["transfer_infantAmt"]);
    $transfer_total_pax = trim($_POST["transfer_total_pax"]);
    $id_product_service_arr_claim = trim($_POST["id_product_service_arr_claim"]);
    $id_product_service_dep_claim = trim($_POST["id_product_service_dep_claim"]);
    
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
    
    $qry_TAXDetails = $con->prepare("
        SELECT * 
        FROM tax_rate
        WHERE tax_dateFrom < :transfer_booking_date
        AND id_tax_code = 3
        AND active = 1
        ORDER BY tax_dateFrom DESC
        LIMIT 1");
    $qry_TAXDetails->execute(array(":transfer_booking_date"=>$transfer_booking_date));

    $row_count_TAXDetails = $qry_TAXDetails->rowCount();
    
    if ($row_count_TAXDetails > 0) 
    {
        while ($rowTAX = $qry_TAXDetails->fetch(PDO::FETCH_ASSOC))
        {
            $tax_value = $rowTAX["tax_value"];
        }
    }
    
    $transfer_rebate_claim_type = trim($_POST["transfer_rebateClaim"]);
    $transfer_rebate_claim_approve_by = trim($_POST["transfer_rebateClaimApproveBy"]);
    $transfer_rebate_claim_percentage = trim($_POST["transfer_claimPercentageRebate"]);
    
    $transfer_remarks = trim($_POST["transfer_remarks"]);
    $transfer_internal_remarks = trim($_POST["transfer_internal_remarks"]);
    $transfer_status = trim($_POST["transfer_status"]);
    $transfer_client = $_POST["transfer_client"]; 
    
     if($transfer_service_paid_by == "TO")
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
    
    if ( $transfer_option =="BOTH" ||  $transfer_option =="ARR")
    {
         $qry_transferDetails = $con->prepare("
            SELECT 
                PS.id_product_service,
                PS.id_product,
                P.product_name,                
                PS.service_name,
                PS.special_name,
                PS_CLAIM.id_currency AS id_product_service_claim_cur,
                PS_CLAIM.id_dept,
                PS_CLAIM.charge,
                PS.id_tax AS id_service_tax,
                C.vat_flag AS creditor_vat_flag,
                PS_CLAIM.ps_adult_claim,
                PS_CLAIM.ps_child_claim,
                PS_CLAIM.ps_infant_claim,
                PS_COST.id_product_service_cost,
                PS_COST.id_currency AS id_product_service_cost_cur,
                PS_COST.ps_adult_cost,
                PS_COST.ps_child_cost,
                PS_COST.ps_infant_cost
            FROM
                product_service_claim PS_CLAIM,
                product_service_cost PS_COST,
                product_service PS,
                product P,
                creditor C
            WHERE PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
            AND PS_CLAIM.id_product_service = PS.id_product_service
            AND PS.id_product = P.id_product
            AND PS.id_creditor = C.id_creditor
            AND PS_CLAIM.id_product_service_claim = :id_product_service_arr_claim
            AND PS_CLAIM.active = 1
            AND PS_COST.active = 1
            AND PS.active =1");
        
        $qry_transferDetails->execute(array(":id_product_service_arr_claim"=>$id_product_service_arr_claim));
		$row_count_transferDetails = $qry_transferDetails->rowCount();
        if ($row_count_transferDetails > 0) 
		{
            while ($rowTransfer = $qry_transferDetails->fetch(PDO::FETCH_ASSOC))
			{
				$transfer_date = $transfer_arrivalDate;
				$transfer_flight_no = $transfer_arrivalFlight;
				$transfer_time = $transfer_arrivalTime;
				$id_transfer_from = 1000; //to update to transfer from place id (hotel)
				$transfer_from_name = $transfer_port; 
				$id_transfer_to = $id_destination_to;
				$transfer_to_name = $destination_to_name; 
				$transfer_type = "ARRIVAL"; 
				$id_product = $transfer_vehicle; 
				$id_product_service = $rowTransfer["id_product_service"];
				$transfer_special_name = $rowTransfer["special_name"];
				$transfer_name = $transfer_type.' - '. $rowTransfer["product_name"].' - '.$transfer_special_name. ' - '.$transfer_to_name;
				$id_product_service_claim = $id_product_service_arr_claim; 
				$id_product_service_claim_cur = $rowTransfer["id_product_service_claim_cur"];
                
				$transfer_claim_dept = $rowTransfer["id_dept"];
				$transfer_charge = $rowTransfer["charge"];
                $id_service_tax = $rowTransfer["id_service_tax"];
                $creditor_vat_flag = $rowTransfer["creditor_vat_flag"];
                if ($id_tax_TO ==1)
                {
                    if($creditor_vat_flag == 'Y' && $id_service_tax == '3')
                    {
                        // remove VAT on claim
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                               
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }

                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX  = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX  = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                            
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX)  + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                    }
                    else
                    {
                        // remove VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                           
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                             
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($child_claim - $transfer_child_cost) * ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX =   $child_markup_exTAX + $transfer_child_cost;
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($infant_claim - $transfer_infant_cost) * ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX =   $infant_markup_exTAX + $transfer_infant_cost;
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim = $transfer_adult_claim;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_childt_cost) * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infantt_cost) * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                        }
                    }
                }
                else
                {
                    if ($creditor_vat_flag == "Y" && $id_service_tax == 3)
                    {
                        // VAT inlucded 
                        if ($transfer_charge == "PAX")
                        {
                            
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = (($transfer_adult_amt * $transfer_adult_claim) + ($transfer_child_amt * $transfer_child_claim) +($transfer_infant_amt * $transfer_infant_claim));
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $rowTransfer["ps_adult_claim"];
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_rebate;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_rebate;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_rebate;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                    else
                    {
                        // VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($transfer_child_claim - $transfer_child_cost)* ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX = $child_markup_exTAX + $transfer_child_cost;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($transfer_infant_claim - $transfer_infant_cost)* ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX = $infant_markup_exTAX + $transfer_infant_cost;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                            
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate= trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate= trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_child_cost)* ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $child_rebate_markup_exTAX + $transfer_child_cost;
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate= trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infant_cost)* ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $infant_rebate_markup_exTAX + $transfer_infant_cost;
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) +  ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                }
                
            }
            
            $created_by = $_SESSION["solis_userid"];
            $created_name = $_SESSION["solis_username"];
            $id_user = $_SESSION["solis_userid"];
            $uname = $_SESSION["solis_username"];
            $log_status = "CREATE";
            if ($transfer_booking_date == "") 
            {
                $transfer_booking_date = date("Y-m-d");
            }

            if ($transfer_time == '')
            {
                $transfer_time = null;
            }	
            //BOOKING TRANSFER CLAIM
            
            $sqlSaveTransferClaim = "
                INSERT INTO booking_transfer_claim
                (
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    created_by,
                    created_name
                )
                VALUES
                (
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :created_by,
                    :created_name
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaim);
            $stmt->execute(array(
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
                ":created_by"                                                       =>$created_by,
                ":created_name"                                                  =>$created_name
            ));

            $id_booking_transfer_claim = $con->lastInsertId();    
            
            // CLIENT TRANSFER
            $sqlClientTransfer = "INSERT INTO booking_transfer_client (id_client, id_booking_transfer_claim,id_booking) 
            VALUES (:booking_client, :id_booking_transfer_claim,:id_booking)";

            $stmt = $con->prepare($sqlClientTransfer);
            $data = $transfer_client;

            foreach($data as $d) {
            $stmt->execute(array(':id_booking_transfer_claim' => $id_booking_transfer_claim,':id_booking' => $id_booking, ':booking_client' => $d));
            }
            
            // BOOKING TRANSFER CLAIM LOG
            $sqlSaveTransferClaimLog = "
                INSERT INTO booking_transfer_claim_log
                (
                    id_booking_transfer_claim,
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_clients,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    id_user,
                    uname,
                    log_status
                )
                VALUES
                (
                    :id_booking_transfer_claim,
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_clients,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :id_user,
                    :uname,
                    :log_status
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaimLog);
            $stmt->execute(array(
                ":id_booking_transfer_claim"                              =>$id_booking_transfer_claim,
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_clients"                                             =>implode( ", ", $transfer_client ),
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
				 ":id_user"                                                             => $id_user,
				 ":uname"                                                              => $uname,
				 ":log_status"                                                         => $log_status
            ));
            $bookingTransfer_Arrival= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim, "created_by" =>$created_name);
        }
        else
        {
            $bookingTransfer_Arrival= array("OUTCOME" => "FAIL", "id_booking"=>0, "id_booking_transfer_claim"=>0, "created_by" =>0);
        }
    }
   
    if ( $transfer_option =="BOTH" ||  $transfer_option =="DEP")
    {
        $qry_transferDetails = $con->prepare("
            SELECT 
                PS.id_product_service,
                PS.id_product,
                P.product_name,                
                PS.service_name,
                PS.special_name,
                PS_CLAIM.id_currency AS id_product_service_claim_cur,
                PS_CLAIM.id_dept,
                PS_CLAIM.charge,
                PS.id_tax AS id_service_tax,
                C.vat_flag AS creditor_vat_flag,
                PS_CLAIM.ps_adult_claim,
                PS_CLAIM.ps_child_claim,
                PS_CLAIM.ps_infant_claim,
                PS_COST.id_product_service_cost,
                PS_COST.id_currency AS id_product_service_cost_cur,
                PS_COST.ps_adult_cost,
                PS_COST.ps_child_cost,
                PS_COST.ps_infant_cost
            FROM
                product_service_claim PS_CLAIM,
                product_service_cost PS_COST,
                product_service PS,
                product P,
                creditor C
            WHERE PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
            AND PS_CLAIM.id_product_service = PS.id_product_service
            AND PS.id_product = P.id_product
            AND PS.id_creditor = C.id_creditor
            AND PS_CLAIM.id_product_service_claim = :id_product_service_dep_claim
            AND PS_CLAIM.active = 1
            AND PS_COST.active = 1
            AND PS.active =1");
        
        $qry_transferDetails->execute(array(":id_product_service_dep_claim"=>$id_product_service_dep_claim));
		$row_count_transferDetails = $qry_transferDetails->rowCount();
        if ($row_count_transferDetails > 0) 
		{
            while ($rowTransfer = $qry_transferDetails->fetch(PDO::FETCH_ASSOC))
			{
				$transfer_date = $transfer_departureDate;
				$transfer_flight_no = $transfer_departureFlight;
				$transfer_time = $transfer_departureTime;
				$id_transfer_from = $id_destination_from; //to update to transfer from place id (hotel)
				$transfer_from_name = $destination_from_name; 
				$id_transfer_to = 1000; //to update to transfer from place id (hotel)
				$transfer_to_name = $transfer_port; 
				$transfer_type = "DEPARTURE"; 
				$id_product = $transfer_vehicle; 
				$id_product_service = $rowTransfer["id_product_service"];
				$transfer_special_name = $rowTransfer["special_name"];
				$transfer_name = $transfer_type.' - '. $rowTransfer["product_name"]. ' - '.$transfer_from_name.' - '.$transfer_special_name;
				$id_product_service_claim = $id_product_service_dep_claim; 
				$id_product_service_claim_cur = $rowTransfer["id_product_service_claim_cur"];
                
				$transfer_claim_dept = $rowTransfer["id_dept"];
				$transfer_charge = $rowTransfer["charge"];
                $id_service_tax = $rowTransfer["id_service_tax"];
                $creditor_vat_flag = $rowTransfer["creditor_vat_flag"];
                if ($id_tax_TO ==1)
                {
                    if($creditor_vat_flag == 'Y' && $id_service_tax == '3')
                    {
                        // remove VAT on claim
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                               
                           }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }

                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX  = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX  = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                            
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX)  + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                    }
                    else
                    {
                        // remove VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                           
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                             
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($child_claim - $transfer_child_cost) * ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX =   $child_markup_exTAX + $transfer_child_cost;
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($infant_claim - $transfer_infant_cost) * ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX =   $infant_markup_exTAX + $transfer_infant_cost;
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim = $transfer_adult_claim;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_childt_cost) * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infantt_cost) * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                        }
                    }
                }
                else
                {
                    if ($creditor_vat_flag == "Y" && $id_service_tax == 3)
                    {
                        // VAT inlucded 
                        if ($transfer_charge == "PAX")
                        {
                            
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = (($transfer_adult_amt * $transfer_adult_claim) + ($transfer_child_amt * $transfer_child_claim) +($transfer_infant_amt * $transfer_infant_claim));
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $rowTransfer["ps_adult_claim"];
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_rebate;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_rebate;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_rebate;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                    else
                    {
                        // VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($transfer_child_claim - $transfer_child_cost)* ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX = $child_markup_exTAX + $transfer_child_cost;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($transfer_infant_claim - $transfer_infant_cost)* ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX = $infant_markup_exTAX + $transfer_infant_cost;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                            
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate= trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate= trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_child_cost)* ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $child_rebate_markup_exTAX + $transfer_child_cost;
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate= trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infant_cost)* ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $infant_rebate_markup_exTAX + $transfer_infant_cost;
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) +  ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                }
                
            }
            
            $created_by = $_SESSION["solis_userid"];
            $created_name = $_SESSION["solis_username"];
            $id_user = $_SESSION["solis_userid"];
            $uname = $_SESSION["solis_username"];
            $log_status = "CREATE";
            if ($transfer_booking_date == "") 
            {
                $transfer_booking_date = date("Y-m-d");
            }

            if ($transfer_time == '')
            {
                $transfer_time = null;
            }	
            //BOOKING TRANSFER CLAIM
            
            $sqlSaveTransferClaim = "
                INSERT INTO booking_transfer_claim
                (
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    created_by,
                    created_name
                )
                VALUES
                (
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :created_by,
                    :created_name
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaim);
            $stmt->execute(array(
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
                ":created_by"                                                       =>$created_by,
                ":created_name"                                                  =>$created_name
            ));

            $id_booking_transfer_claim = $con->lastInsertId();    
            
            // CLIENT TRANSFER
            $sqlClientTransfer = "INSERT INTO booking_transfer_client (id_client, id_booking_transfer_claim,id_booking) 
            VALUES (:booking_client, :id_booking_transfer_claim,:id_booking)";

            $stmt = $con->prepare($sqlClientTransfer);
            $data = $transfer_client;

            foreach($data as $d) {
            $stmt->execute(array(':id_booking_transfer_claim' => $id_booking_transfer_claim,':id_booking' => $id_booking, ':booking_client' => $d));
            }
            
            // BOOKING TRANSFER CLAIM LOG
            $sqlSaveTransferClaimLog = "
                INSERT INTO booking_transfer_claim_log
                (
                    id_booking_transfer_claim,
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_clients,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    id_user,
                    uname,
                    log_status
                )
                VALUES
                (
                    :id_booking_transfer_claim,
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_clients,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :id_user,
                    :uname,
                    :log_status
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaimLog);
            $stmt->execute(array(
                ":id_booking_transfer_claim"                              =>$id_booking_transfer_claim,
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_clients"                                             =>implode( ", ", $transfer_client ),
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
				 ":id_user"                                                             => $id_user,
				 ":uname"                                                              => $uname,
				 ":log_status"                                                         => $log_status
            ));
            $bookingTransfer_Departure= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim, "created_by" =>$created_name);
        }
        else
        {
            $bookingTransfer_Departure= array("OUTCOME" => "FAIL", "id_booking"=>0, "id_booking_transfer_claim"=>0, "created_by" =>0);
            //echo json_encode($bookingTransfer_Departure); 
        }
    }
    
    if ($transfer_option =="INTER HOTEL")
    {
        $qry_transferDetails = $con->prepare("
            SELECT 
                PS.id_product_service,
                PS.id_product,
                P.product_name,                
                PS.service_name,
                PS.special_name,
                PS_CLAIM.id_currency AS id_product_service_claim_cur,
                PS_CLAIM.id_dept,
                PS_CLAIM.charge,
                PS.id_tax AS id_service_tax,
                C.vat_flag AS creditor_vat_flag,
                PS_CLAIM.ps_adult_claim,
                PS_CLAIM.ps_child_claim,
                PS_CLAIM.ps_infant_claim,
                PS_COST.id_product_service_cost,
                PS_COST.id_currency AS id_product_service_cost_cur,
                PS_COST.ps_adult_cost,
                PS_COST.ps_child_cost,
                PS_COST.ps_infant_cost
            FROM
                product_service_claim PS_CLAIM,
                product_service_cost PS_COST,
                product_service PS,
                product P,
                creditor C
            WHERE PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
            AND PS_CLAIM.id_product_service = PS.id_product_service
            AND PS.id_product = P.id_product
            AND PS.id_creditor = C.id_creditor
            AND PS_CLAIM.id_product_service_claim = :id_product_service_dep_claim
            AND PS_CLAIM.active = 1
            AND PS_COST.active = 1
            AND PS.active =1");
        
        $qry_transferDetails->execute(array(":id_product_service_dep_claim"=>$id_product_service_dep_claim));
		$row_count_transferDetails = $qry_transferDetails->rowCount();
        if ($row_count_transferDetails > 0) 
		{
            while ($rowTransfer = $qry_transferDetails->fetch(PDO::FETCH_ASSOC))
			{
				$transfer_date = $transfer_departureDate;
				$transfer_flight_no = '-';
				$transfer_time = $transfer_arrivalTime;
				$id_transfer_from = $id_destination_from; //to update to transfer from place id (hotel)
				$transfer_from_name = $destination_from_name; 
				$id_transfer_to =  $id_destination_to; //to update to transfer from place id (hotel)
				$transfer_to_name =  $destination_to_name; 
				$transfer_type = "INTER HOTEL"; 
				$id_product = $transfer_vehicle; 
				$id_product_service = $rowTransfer["id_product_service"];
				$transfer_special_name = $rowTransfer["special_name"];
				$transfer_name = $transfer_type.' - '. $rowTransfer["product_name"].' - '.$transfer_from_name. ' - '.$transfer_to_name;
				$id_product_service_claim = $id_product_service_dep_claim; 
				$id_product_service_claim_cur = $rowTransfer["id_product_service_claim_cur"];
                
				$transfer_claim_dept = $rowTransfer["id_dept"];
				$transfer_charge = $rowTransfer["charge"];
                $id_service_tax = $rowTransfer["id_service_tax"];
                $creditor_vat_flag = $rowTransfer["creditor_vat_flag"];
                if ($id_tax_TO ==1)
                {
                    if($creditor_vat_flag == 'Y' && $id_service_tax == '3')
                    {
                        // remove VAT on claim
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                               
                           }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }

                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX  = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX  = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                            
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX)  + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                    }
                    else
                    {
                        // remove VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                           
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                             
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($child_claim - $transfer_child_cost) * ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX =   $child_markup_exTAX + $transfer_child_cost;
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($infant_claim - $transfer_infant_cost) * ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX =   $infant_markup_exTAX + $transfer_infant_cost;
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim = $transfer_adult_claim;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_childt_cost) * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infantt_cost) * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                        }
                    }
                }
                else
                {
                    if ($creditor_vat_flag == "Y" && $id_service_tax == 3)
                    {
                        // VAT inlucded 
                        if ($transfer_charge == "PAX")
                        {
                            
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = (($transfer_adult_amt * $transfer_adult_claim) + ($transfer_child_amt * $transfer_child_claim) +($transfer_infant_amt * $transfer_infant_claim));
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $rowTransfer["ps_adult_claim"];
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_rebate;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_rebate;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_rebate;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                    else
                    {
                        // VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($transfer_child_claim - $transfer_child_cost)* ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX = $child_markup_exTAX + $transfer_child_cost;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($transfer_infant_claim - $transfer_infant_cost)* ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX = $infant_markup_exTAX + $transfer_infant_cost;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                            
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate= trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate= trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_child_cost)* ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $child_rebate_markup_exTAX + $transfer_child_cost;
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate= trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infant_cost)* ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $infant_rebate_markup_exTAX + $transfer_infant_cost;
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) +  ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                }
                
            }
            
            $created_by = $_SESSION["solis_userid"];
            $created_name = $_SESSION["solis_username"];
            $id_user = $_SESSION["solis_userid"];
            $uname = $_SESSION["solis_username"];
            $log_status = "CREATE";
            if ($transfer_booking_date == "") 
            {
                $transfer_booking_date = date("Y-m-d");
            }

            if ($transfer_time == '')
            {
                $transfer_time = null;
            }	
            //BOOKING TRANSFER CLAIM
            
            $sqlSaveTransferClaim = "
                INSERT INTO booking_transfer_claim
                (
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    created_by,
                    created_name
                )
                VALUES
                (
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :created_by,
                    :created_name
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaim);
            $stmt->execute(array(
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
                ":created_by"                                                       =>$created_by,
                ":created_name"                                                  =>$created_name
            ));

            $id_booking_transfer_claim = $con->lastInsertId();    
            
            // CLIENT TRANSFER
            $sqlClientTransfer = "INSERT INTO booking_transfer_client (id_client, id_booking_transfer_claim,id_booking) 
            VALUES (:booking_client, :id_booking_transfer_claim,:id_booking)";

            $stmt = $con->prepare($sqlClientTransfer);
            $data = $transfer_client;

            foreach($data as $d) {
            $stmt->execute(array(':id_booking_transfer_claim' => $id_booking_transfer_claim,':id_booking' => $id_booking, ':booking_client' => $d));
            }
            
            // BOOKING TRANSFER CLAIM LOG
            $sqlSaveTransferClaimLog = "
                INSERT INTO booking_transfer_claim_log
                (
                    id_booking_transfer_claim,
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_clients,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    id_user,
                    uname,
                    log_status
                )
                VALUES
                (
                    :id_booking_transfer_claim,
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_clients,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :id_user,
                    :uname,
                    :log_status
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaimLog);
            $stmt->execute(array(
                ":id_booking_transfer_claim"                              =>$id_booking_transfer_claim,
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_clients"                                             =>implode( ", ", $transfer_client ),
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
				 ":id_user"                                                             => $id_user,
				 ":uname"                                                              => $uname,
				 ":log_status"                                                         => $log_status
            ));
            $bookingTransfer_Departure= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim, "created_by" =>$created_name);
        }
        else
        {
            $bookingTransfer_Departure= array("OUTCOME" => "FAIL", "id_booking"=>0, "id_booking_transfer_claim"=>0, "created_by" =>0);
            //echo json_encode($bookingTransfer_Departure); 
        }
    }
    
    if ($transfer_option =="ACTIVITY")
    {
        $qry_transferDetails = $con->prepare("
            SELECT 
                PS.id_product_service,
                PS.id_product,
                P.product_name,                
                PS.service_name,
                PS.special_name,
                PS_CLAIM.id_currency AS id_product_service_claim_cur,
                PS_CLAIM.id_dept,
                PS_CLAIM.charge,
                PS.id_tax AS id_service_tax,
                C.vat_flag AS creditor_vat_flag,
                PS_CLAIM.ps_adult_claim,
                PS_CLAIM.ps_child_claim,
                PS_CLAIM.ps_infant_claim,
                PS_COST.id_product_service_cost,
                PS_COST.id_currency AS id_product_service_cost_cur,
                PS_COST.ps_adult_cost,
                PS_COST.ps_child_cost,
                PS_COST.ps_infant_cost
            FROM
                product_service_claim PS_CLAIM,
                product_service_cost PS_COST,
                product_service PS,
                product P,
                creditor C
            WHERE PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
            AND PS_CLAIM.id_product_service = PS.id_product_service
            AND PS.id_product = P.id_product
            AND PS.id_creditor = C.id_creditor
            AND PS_CLAIM.id_product_service_claim = :id_product_service_dep_claim
            AND PS_CLAIM.active = 1
            AND PS_COST.active = 1
            AND PS.active =1");
        
        $qry_transferDetails->execute(array(":id_product_service_dep_claim"=>$id_product_service_dep_claim));
		$row_count_transferDetails = $qry_transferDetails->rowCount();
        if ($row_count_transferDetails > 0) 
		{
            while ($rowTransfer = $qry_transferDetails->fetch(PDO::FETCH_ASSOC))
			{
				$transfer_date = $transfer_departureDate;
				$transfer_flight_no = '-';
				$transfer_time = $transfer_arrivalTime;
				$id_transfer_from = $id_destination_from; //to update to transfer from place id (hotel)
				$transfer_from_name = $destination_from_name; 
				$id_transfer_to =  $id_destination_to; //to update to transfer from place id (hotel)
				$transfer_to_name =  $destination_to_name; 
				$transfer_type = "ACTIVITY";
				$id_product = $transfer_vehicle; 
				$id_product_service = $rowTransfer["id_product_service"];
				$transfer_special_name = $rowTransfer["special_name"];
				$transfer_name = $transfer_type.' '.$transfer_port.' - '. $rowTransfer["product_name"].' - '.$transfer_from_name. ' - '.$transfer_to_name;
				$id_product_service_claim = $id_product_service_dep_claim; 
				$id_product_service_claim_cur = $rowTransfer["id_product_service_claim_cur"];
                
				$transfer_claim_dept = $rowTransfer["id_dept"];
				$transfer_charge = $rowTransfer["charge"];
                $id_service_tax = $rowTransfer["id_service_tax"];
                $creditor_vat_flag = $rowTransfer["creditor_vat_flag"];
                if ($id_tax_TO ==1)
                {
                    if($creditor_vat_flag == 'Y' && $id_service_tax == '3')
                    {
                        // remove VAT on claim
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                               
                           }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }

                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX  = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX  = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                            
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX)  + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim_after_rebate_exTAX;
                        }
                    }
                    else
                    {
                        // remove VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                           
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                             
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                                $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($child_claim - $transfer_child_cost) * ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX =   $child_markup_exTAX + $transfer_child_cost;
                                $transfer_child_claim = $transfer_child_claim_exTAX;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($infant_claim - $transfer_infant_cost) * ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX =   $infant_markup_exTAX + $transfer_infant_cost;
                                $transfer_infant_claim = $transfer_infant_claim_exTAX;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($adult_claim - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX =   $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim = $transfer_adult_claim_exTAX;
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim = $transfer_adult_claim;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost) * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_childt_cost) * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infantt_cost) * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate = 0;
                            $transfer_total_claim_after_rebate_exTAX = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                        }
                    }
                }
                else
                {
                    if ($creditor_vat_flag == "Y" && $id_service_tax == 3)
                    {
                        // VAT inlucded 
                        if ($transfer_charge == "PAX")
                        {
                            
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;

                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim_exTAX = $rowTransfer["ps_child_claim"] * ((100 - $tax_value)/100);
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim_exTAX = $rowTransfer["ps_infant_claim"] * ((100 - $tax_value)/100);
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = (($transfer_adult_amt * $transfer_adult_claim) + ($transfer_child_amt * $transfer_child_claim) +($transfer_infant_amt * $transfer_infant_claim));
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $transfer_child_claim_exTAX = 0;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_infant_claim = 0;
                            $transfer_total_claim_exTAX = $rowTransfer["ps_adult_claim"] * ((100 - $tax_value)/100);
                            $transfer_total_claim = $rowTransfer["ps_adult_claim"];
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate = trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $transfer_adult_claim_after_rebate_exTAX = $transfer_adult_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_rebate;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate = trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $transfer_child_claim_after_rebate_exTAX = $transfer_child_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate = $transfer_child_claim_rebate;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate = trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_infant_claim_rebate !=0)
                        {
                            $transfer_infant_claim_after_rebate_exTAX = $transfer_infant_claim_rebate * ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_rebate;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) + ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                    else
                    {
                        // VAT on markup
                        if ($transfer_charge == "PAX")
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            
                            if($rowTransfer["ps_adult_claim"] == null)
                            {
                                $transfer_adult_claim_exTAX = 0;
                                $transfer_adult_claim = 0;
                            }
                            else
                            {
                                $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                                $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                                $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            }

                            if($rowTransfer["ps_child_claim"] == null)
                            {
                                $transfer_child_claim_exTAX = 0;
                                $transfer_child_claim = 0;
                            }
                            else
                            {
                                $transfer_child_claim = $rowTransfer["ps_child_claim"];
                                $child_markup_exTAX = ($transfer_child_claim - $transfer_child_cost)* ((100 - $tax_value)/100);
                                $transfer_child_claim_exTAX = $child_markup_exTAX + $transfer_child_cost;
                            }

                            if($rowTransfer["ps_infant_claim"] == null)
                            {
                                $transfer_infant_claim_exTAX = 0;
                                $transfer_infant_claim = 0;
                            }
                            else
                            {
                                $transfer_infant_claim = $rowTransfer["ps_infant_claim"];
                                $infant_markup_exTAX = ($transfer_infant_claim - $transfer_infant_cost)* ((100 - $tax_value)/100);
                                $transfer_infant_claim_exTAX = $infant_markup_exTAX + $transfer_infant_cost;
                            }

                            $transfer_total_claim_exTAX = (($transfer_adult_amt * $transfer_adult_claim_exTAX) + ($transfer_child_amt * $transfer_child_claim_exTAX) +($transfer_infant_amt * $transfer_infant_claim_exTAX));
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                            
                        }
                        else
                        {
                            $transfer_adult_cost = 0;
                            $transfer_child_cost = 0;
                            $transfer_infant_cost = 0;
                            $transfer_total_cost = 0;
                            $transfer_adult_claim = $rowTransfer["ps_adult_claim"];
                            $adult_markup_exTAX = ($transfer_adult_claim - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_exTAX = $adult_markup_exTAX + $transfer_adult_cost;
                            $transfer_child_claim = 0;
                            $transfer_infant_claim_exTAX = 0;
                            $transfer_total_claim_exTAX = $transfer_adult_claim_exTAX;
                            $transfer_total_claim = $transfer_total_claim_exTAX;
                        }
                        $id_product_service_cost = $rowTransfer["id_product_service_cost"];
                        $id_product_service_cost_cur = $rowTransfer["id_product_service_cost_cur"]; 
                        $transfer_adult_claim_rebate= trim($_POST["transfer_adult_claim_after_rebate"]);
                        if($transfer_adult_claim_rebate !=0)
                        {
                            $adult_rebate_markup_exTAX = ($transfer_adult_claim_rebate - $transfer_adult_cost)* ((100 - $tax_value)/100);
                            $transfer_adult_claim_after_rebate_exTAX = $adult_rebate_markup_exTAX + $transfer_adult_cost;
                            $transfer_adult_claim_after_rebate = $transfer_adult_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_adult_claim_after_rebate_exTAX = 0;
                            $transfer_adult_claim_after_rebate = 0;
                        }
                        $transfer_child_claim_rebate= trim($_POST["transfer_child_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $child_rebate_markup_exTAX = ($transfer_child_claim_rebate - $transfer_child_cost)* ((100 - $tax_value)/100);
                            $transfer_child_claim_after_rebate_exTAX = $child_rebate_markup_exTAX + $transfer_child_cost;
                            $transfer_child_claim_after_rebate = $transfer_child_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_child_claim_after_rebate_exTAX = 0;
                            $transfer_child_claim_after_rebate = 0;
                        }
                        $transfer_infant_claim_rebate= trim($_POST["transfer_infant_claim_after_rebate"]);
                        if($transfer_child_claim_rebate !=0)
                        {
                            $infant_rebate_markup_exTAX = ($transfer_infant_claim_rebate - $transfer_infant_cost)* ((100 - $tax_value)/100);
                            $transfer_infant_claim_after_rebate_exTAX = $infant_rebate_markup_exTAX + $transfer_infant_cost;
                            $transfer_infant_claim_after_rebate = $transfer_infant_claim_after_rebate_exTAX;
                        }
                        else
                        {
                            $transfer_infant_claim_after_rebate_exTAX = 0;
                            $transfer_infant_claim_after_rebate = 0;
                        }
                        if ($transfer_rebate_claim_type == 'Percentage')
                        {
                            $transfer_total_claim_after_rebate = $transfer_total_claim * ((100-$transfer_rebate_claim_percentage)/100);
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX * ((100-$transfer_rebate_claim_percentage)/100);
                        }
                        else if ($transfer_rebate_claim_type == 'Fixed Tariff')
                        {
                            $transfer_total_claim_after_rebate_exTAX = (($transfer_adult_amt * $transfer_adult_claim_after_rebate_exTAX) +  ($transfer_child_amt * $transfer_child_claim_after_rebate_exTAX) + ($transfer_infant_amt * $transfer_infant_claim_after_rebate_exTAX));
                            $transfer_total_claim_after_rebate = (($transfer_adult_amt * $transfer_adult_claim_after_rebate) + ($transfer_child_amt * $transfer_child_claim_after_rebate) +($transfer_infant_amt * $transfer_infant_claim_after_rebate));
                        }
                        else if ($transfer_rebate_claim_type == 'FOC')
                        {
                            $transfer_total_claim_after_rebate_exTAX = 0;
                            $transfer_total_claim_after_rebate = 0;
                        }
                        else 
                        {
                            $transfer_total_claim_after_rebate_exTAX = $transfer_total_claim_exTAX;
                            $transfer_total_claim_after_rebate = $transfer_total_claim;
                        }
                    }
                }
                
            }
            
            $created_by = $_SESSION["solis_userid"];
            $created_name = $_SESSION["solis_username"];
            $id_user = $_SESSION["solis_userid"];
            $uname = $_SESSION["solis_username"];
            $log_status = "CREATE";
            if ($transfer_booking_date == "") 
            {
                $transfer_booking_date = date("Y-m-d");
            }

            if ($transfer_time == '')
            {
                $transfer_time = null;
            }	
            //BOOKING TRANSFER CLAIM
            
            $sqlSaveTransferClaim = "
                INSERT INTO booking_transfer_claim
                (
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    created_by,
                    created_name
                )
                VALUES
                (
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :created_by,
                    :created_name
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaim);
            $stmt->execute(array(
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
                ":created_by"                                                       =>$created_by,
                ":created_name"                                                  =>$created_name
            ));

            $id_booking_transfer_claim = $con->lastInsertId();    
            
            // CLIENT TRANSFER
            $sqlClientTransfer = "INSERT INTO booking_transfer_client (id_client, id_booking_transfer_claim,id_booking) 
            VALUES (:booking_client, :id_booking_transfer_claim,:id_booking)";

            $stmt = $con->prepare($sqlClientTransfer);
            $data = $transfer_client;

            foreach($data as $d) {
            $stmt->execute(array(':id_booking_transfer_claim' => $id_booking_transfer_claim,':id_booking' => $id_booking, ':booking_client' => $d));
            }
            
            // BOOKING TRANSFER CLAIM LOG
            $sqlSaveTransferClaimLog = "
                INSERT INTO booking_transfer_claim_log
                (
                    id_booking_transfer_claim,
                    id_booking,
                    transfer_service_paid_by,
                    id_tour_operator,
                    id_client,
                    transfer_date,
                    transfer_flight_no,
                    transfer_time,
                    id_transfer_from,
                    transfer_from_name,
                    id_transfer_to,
                    transfer_to_name,
                    transfer_booking_date,
                    transfer_type,
                    id_product,
                    id_product_service,
                    transfer_special_name,
                    transfer_name,
                    transfer_clients,
                    transfer_adult_amt,
                    transfer_child_amt,
                    transfer_infant_amt,
                    transfer_total_pax,
                    id_product_service_claim,
                    id_product_service_claim_cur,
                    id_dept,
                    transfer_claim_dept,
                    transfer_charge,
                    id_service_tax,
                    tax_value,
                    transfer_adult_claim_exTAX,
                    transfer_adult_claim,
                    transfer_child_claim_exTAX,
                    transfer_child_claim,
                    transfer_infant_claim_exTAX,
                    transfer_infant_claim,
                    transfer_total_claim_exTAX,
                    transfer_total_claim,
                    transfer_rebate_claim_type,
                    transfer_rebate_claim_approve_by,
                    transfer_rebate_claim_percentage,
                    transfer_adult_claim_rebate,
                    transfer_adult_claim_after_rebate_exTAX,
                    transfer_adult_claim_after_rebate,
                    transfer_child_claim_rebate,
                    transfer_child_claim_after_rebate,
                    transfer_child_claim_after_rebate_exTAX,
                    transfer_infant_claim_rebate,
                    transfer_infant_claim_after_rebate,
                    transfer_infant_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate_exTAX,
                    transfer_total_claim_after_rebate,
                    transfer_remarks,
                    transfer_internal_remarks,
                    transfer_status,
                    id_user,
                    uname,
                    log_status
                )
                VALUES
                (
                    :id_booking_transfer_claim,
                    :id_booking,
                    :transfer_service_paid_by,
                    :id_tour_operator,
                    :id_client,
                    :transfer_date,
                    :transfer_flight_no,
                    :transfer_time,
                    :id_transfer_from,
                    :transfer_from_name,
                    :id_transfer_to,
                    :transfer_to_name,
                    :transfer_booking_date,
                    :transfer_type,
                    :id_product,
                    :id_product_service,
                    :transfer_special_name,
                    :transfer_name,
                    :transfer_clients,
                    :transfer_adult_amt,
                    :transfer_child_amt,
                    :transfer_infant_amt,
                    :transfer_total_pax,
                    :id_product_service_claim,
                    :id_product_service_claim_cur,
                    :id_dept,
                    :transfer_claim_dept,
                    :transfer_charge,
                    :id_service_tax,
                    :tax_value,
                    :transfer_adult_claim_exTAX,
                    :transfer_adult_claim,
                    :transfer_child_claim_exTAX,
                    :transfer_child_claim,
                    :transfer_infant_claim_exTAX,
                    :transfer_infant_claim,
                    :transfer_total_claim_exTAX,
                    :transfer_total_claim,
                    :transfer_rebate_claim_type,
                    :transfer_rebate_claim_approve_by,
                    :transfer_rebate_claim_percentage,
                    :transfer_adult_claim_rebate,
                    :transfer_adult_claim_after_rebate_exTAX,
                    :transfer_adult_claim_after_rebate,
                    :transfer_child_claim_rebate,
                    :transfer_child_claim_after_rebate,
                    :transfer_child_claim_after_rebate_exTAX,
                    :transfer_infant_claim_rebate,
                    :transfer_infant_claim_after_rebate,
                    :transfer_infant_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate_exTAX,
                    :transfer_total_claim_after_rebate,
                    :transfer_remarks,
                    :transfer_internal_remarks,
                    :transfer_status,
                    :id_user,
                    :uname,
                    :log_status
                )
            ";

            $stmt = $con->prepare($sqlSaveTransferClaimLog);
            $stmt->execute(array(
                ":id_booking_transfer_claim"                              =>$id_booking_transfer_claim,
                ":id_booking"                                                      =>$id_booking,
                ":transfer_service_paid_by"                                =>$transfer_service_paid_by,
                ":id_tour_operator"                                            =>$id_tour_operator,
                ":id_client"                                                         =>$id_client,
                ":transfer_date"                                                 =>$transfer_date,
                ":transfer_flight_no"                                          =>$transfer_flight_no,
                ":transfer_time"                                                 =>$transfer_time,
                ":id_transfer_from"                                            =>$id_transfer_from,
                ":transfer_from_name"                                      =>$transfer_from_name,
                ":id_transfer_to"                                                =>$id_transfer_to,
                ":transfer_to_name"                                          =>$transfer_to_name,
                ":transfer_booking_date"                                  =>$transfer_booking_date,
                ":transfer_type"                                                =>$transfer_type,
                ":id_product"                                                    =>$id_product,
                ":id_product_service"                                       =>$id_product_service,
                ":transfer_special_name"                                  =>$transfer_special_name,
                ":transfer_name"                                              =>$transfer_name,
                ":transfer_clients"                                             =>implode( ", ", $transfer_client ),
                ":transfer_adult_amt"                                       =>$transfer_adult_amt,
                ":transfer_child_amt"                                        =>$transfer_child_amt,
                ":transfer_infant_amt"                                      =>$transfer_infant_amt,
                ":transfer_total_pax"                                        =>$transfer_total_pax,
                ":id_product_service_claim"                             =>$id_product_service_claim,
                ":id_product_service_claim_cur"                       =>$id_product_service_claim_cur,
                ":id_dept"                                                         =>$id_dept,
                ":transfer_claim_dept"                                      =>$transfer_claim_dept,
                ":transfer_charge"                                            =>$transfer_charge,
                ":id_service_tax"                                               =>$id_service_tax,
                ":tax_value"                                                      =>$tax_value,
                ":transfer_adult_claim_exTAX"                         =>$transfer_adult_claim_exTAX,
                ":transfer_adult_claim"                                     =>$transfer_adult_claim,
                ":transfer_child_claim_exTAX"                          =>$transfer_child_claim_exTAX,
                ":transfer_child_claim"                                      =>$transfer_child_claim,
                ":transfer_infant_claim_exTAX"                         =>$transfer_infant_claim_exTAX,
                ":transfer_infant_claim"                                     =>$transfer_infant_claim,
                ":transfer_total_claim_exTAX"                           =>$transfer_total_claim_exTAX,
                ":transfer_total_claim"                                       =>$transfer_total_claim,
                ":transfer_rebate_claim_type"                           =>$transfer_rebate_claim_type,
                ":transfer_rebate_claim_approve_by"                =>$transfer_rebate_claim_approve_by,
                ":transfer_rebate_claim_percentage"                 =>$transfer_rebate_claim_percentage,
                ":transfer_adult_claim_rebate"                           =>$transfer_adult_claim_rebate,
                ":transfer_adult_claim_after_rebate_exTAX"      =>$transfer_adult_claim_after_rebate_exTAX,
                ":transfer_adult_claim_after_rebate"                  =>$transfer_adult_claim_after_rebate,
                ":transfer_child_claim_rebate"                            =>$transfer_child_claim_rebate,
                ":transfer_child_claim_after_rebate"                   =>$transfer_child_claim_after_rebate,
                ":transfer_child_claim_after_rebate_exTAX"        =>$transfer_child_claim_after_rebate_exTAX,
                ":transfer_infant_claim_rebate"                           =>$transfer_infant_claim_rebate,
                ":transfer_infant_claim_after_rebate"                  =>$transfer_infant_claim_after_rebate,
                ":transfer_infant_claim_after_rebate_exTAX"       =>$transfer_infant_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate_exTAX"         =>$transfer_total_claim_after_rebate_exTAX,
                ":transfer_total_claim_after_rebate"                     =>$transfer_total_claim_after_rebate,
                ":transfer_remarks"                                              =>$transfer_remarks,
                ":transfer_internal_remarks"                                 =>$transfer_internal_remarks,
                ":transfer_status"                                                  =>$transfer_status,
				 ":id_user"                                                             => $id_user,
				 ":uname"                                                              => $uname,
				 ":log_status"                                                         => $log_status
            ));
            $bookingTransfer_Departure= array("OUTCOME" => "OK", "id_booking"=>$id_booking, "id_booking_transfer_claim"=>$id_booking_transfer_claim, "created_by" =>$created_name);
        }
        else
        {
            $bookingTransfer_Departure= array("OUTCOME" => "FAIL", "id_booking"=>0, "id_booking_transfer_claim"=>0, "created_by" =>0);
            //echo json_encode($bookingTransfer_Departure); 
        }
    }

    if ( $transfer_option =="BOTH")
    {
        if (($bookingTransfer_Arrival["OUTCOME"] == "OK") && ($bookingTransfer_Departure["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ALL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else if (($bookingTransfer_Arrival["OUTCOME"] == "OK") && ($bookingTransfer_Departure["OUTCOME"] == "FAIL"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ARRIVAL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else if (($bookingTransfer_Arrival["OUTCOME"] == "FAIL") && ($bookingTransfer_Departure["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "DEPARTURE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else if (($bookingTransfer_Arrival["OUTCOME"] == "FAIL") && ($bookingTransfer_Departure["OUTCOME"] == "FAIL"))
        {
            $bookingTransfer_result= array("OUTCOME" => "FAIL", "MESSAGE" => "NONE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
    }
    else if ( $transfer_option =="ARR")
    { 
        if (($bookingTransfer_Arrival["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ALL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else
        {
            $bookingTransfer_result= array("OUTCOME" => "FAIL", "MESSAGE" => "NONE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
    }
    else if ( $transfer_option =="DEP")
    {
        if (($bookingTransfer_Departure["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ALL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else
        {
            $bookingTransfer_result= array("OUTCOME" => "FAIL", "MESSAGE" => "NONE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
    }
    else if ( $transfer_option =="INTER HOTEL")
    {
        if (($bookingTransfer_Departure["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ALL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else
        {
            $bookingTransfer_result= array("OUTCOME" => "FAIL", "MESSAGE" => "NONE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
    }
    else if ( $transfer_option =="ACTIVITY")
    {
        if (($bookingTransfer_Departure["OUTCOME"] == "OK"))
        {
            $bookingTransfer_result= array("OUTCOME" => "OK", "MESSAGE" => "ALL", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
        else
        {
            $bookingTransfer_result= array("OUTCOME" => "FAIL", "MESSAGE" => "NONE", "id_booking"=>$id_booking);
            echo json_encode($bookingTransfer_result); 
        }
    }
    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
    
    