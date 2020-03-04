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

    require_once("../../php/connector/pdo_connect_main.php");
    
    $id_booking = $_POST["id_booking"];
    $bookingDate = trim($_POST["transfer_bookingDate"]);
    $paid_by = 'TO';
    $payer = $_SESSION["id_tour_operator"];
    $transfer_type = trim($_POST["transfer_type"]);    
    $special_name = trim($_POST["transfer_port"]);    
    $transfer_vehicle = trim($_POST["transfer_vehicle"]);    
    $transfer_destination_from = trim($_POST["transfer_destination_from"]);  
    $transfer_destination_to = trim($_POST["transfer_destination_to"]);  
    $arrivalDate = trim($_POST["transfer_arrivalDate"]);  
    $departureDate = trim($_POST["transfer_departureDate"]);  
    
    $con = pdo_con();
    // Booking Details
    $qry_bookingDetails = $con->prepare("
            SELECT * 
            FROM booking 
            WHERE id_booking = :id_booking
            AND id_tour_operator = :id_tour_operator
            AND active = 1");
    
	$qry_bookingDetails->execute(array(":id_booking"=>$id_booking,":id_tour_operator"=>$payer));

	$row_count_bookingDetails = $qry_bookingDetails->rowCount();
	
	if ($row_count_bookingDetails > 0) 
	{
		while ($row = $qry_bookingDetails->fetch(PDO::FETCH_ASSOC))
		{
			$id_dept = $row["id_dept"];
		}
	}
    
	// Country Details
	if ($paid_by == 'TO') 
	{
		$qry_country = $con->prepare("SELECT * FROM tbltouroperator WHERE id = :id_tour_operator AND active = 1");
		$qry_country->execute(array(":id_tour_operator"=>$payer));

		$row_count_c = $qry_country->rowCount();

		if ($row_count_c > 0) 
		{
			while ($row = $qry_country->fetch(PDO::FETCH_ASSOC))
			{
				$id_country = $row["phy_countryfk"];
			}
		}

	}
	else
	{
		$id_country = 0;
	}
    
    // Service Type
	if ($transfer_type == 'ARR')
    { 
         // Hotel Details
        $qry_hotelDetails = $con->prepare("
           SELECT * FROM tblhotels WHERE id = :id_destination AND active =1");

        $qry_hotelDetails->execute(array(":id_destination"=>$transfer_destination_to));
        $row_count_hotelDetails = $qry_hotelDetails->rowCount();

        if ($row_count_hotelDetails > 0) 
        {
            while ($row = $qry_hotelDetails->fetch(PDO::FETCH_ASSOC))
            {
                if($row["id_transfer_coast"] != 8)
                {
                    $service_name = "OTHER COAST";
                }
                else
                {
                    $service_name = "SOUTH EAST";
                }
            }
        }
        else
        {
            $service_name = "NONE";
        }
    }
    else if ($transfer_type == 'DEP') 
    { 
         // Hotel Details
        $qry_hotelDetails = $con->prepare("
           SELECT * FROM tblhotels WHERE id = :id_destination AND active =1");

        $qry_hotelDetails->execute(array(":id_destination"=>$transfer_destination_from));
        $row_count_hotelDetails = $qry_hotelDetails->rowCount();

        if ($row_count_hotelDetails > 0) 
        {
            while ($row = $qry_hotelDetails->fetch(PDO::FETCH_ASSOC))
            {
                if($row["id_transfer_coast"] != 8)
                {
                    $service_name = "OTHER COAST";
                }
                else
                {
                    $service_name = "SOUTH EAST";
                }
            }
        }
        else
        {
            $service_name = "NONE";
        }
        
    }
    else if ($transfer_type == 'INTER HOTEL') 
    {
        $service_name = "INTER HOTEL";
    }
    else if ($transfer_type == 'ACTIVITY') 
    {
        $service_name = "ACTIVITY";
    }
    
	if ($paid_by == 'TO') 
	{
		$qry_serviceClaimAgency ="
		SELECT * FROM
		(
            SELECT
                    PS.id_product_service,
                    PS_CLAIM.id_product_service_claim,
                    PS_CLAIM.id_product_service_cost,
                    PS.service_name,
                    PS.special_name,
                    PS.valid_from AS service_from,
                    PS.valid_to AS service_to,
                    PS_CLAIM.valid_from AS claim_from,
                    PS_CLAIM.valid_to AS claim_to,
                    PS_CLAIM.specific_to,
                    PS.id_dept,
                    PS.id_country AS service_country,
                    PS.id_coast,
                    PS.id_creditor,
                    PS.id_tax,
                    PS.charge,
                    PS_CLAIM.id_currency AS id_claim_curr,
                    CUR.currency_code AS claim_curr,
                    PS_COST.currency AS cost_curr,
                    PS.duration,
                    PS.on_approved,
                    PS.on_api,
                    PS.description,
                    PS.comments,
                    PS.cancellation,
                    PS.for_infant,
                    PS.age_inf_from,
                    PS.age_inf_to,
                    PS_CLAIM.ps_infant_claim,
                    PS_COST.ps_infant_cost,
                    PS.for_child,
                    PS.age_child_from,
                    PS.age_child_to, 
                    PS_CLAIM.ps_child_claim,
                    PS_COST.ps_child_cost,
                    PS.for_adult,
                    PS_CLAIM.ps_adult_claim,
                    PS_COST.ps_adult_cost,
                    PS.min_pax,
                    PS.max_adult,
                    PS.max_pax
                FROM 
                    product_service PS,
                    product_service_claim PS_CLAIM,
                    product_service_cost PS_COST,
                    tblcurrency CUR,
                    product_service_claim_to PS_CLAIM_TO
                WHERE PS.id_product_service = PS_CLAIM.id_product_service
                AND PS.id_product_service = PS_CLAIM.id_product_service
                AND PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
                AND PS_CLAIM.id_product_service_claim = PS_CLAIM_TO.id_product_service_claim
                AND PS_CLAIM.id_currency = CUR.id
                AND PS.id_product = :transfer_vehicle
                AND PS.service_name = :service_name
                AND PS.id_dept = :id_dept
                AND PS_CLAIM.specific_to = 'A'
                AND PS_CLAIM_TO.id_tour_operator = :id_tour_operator
                AND PS.on_api = 1
                AND PS.on_approved = 1
                AND PS.active = 1
                AND PS_CLAIM.active = 1
		) AS product_service_claim ";
        
        if ($transfer_type == 'ARR')
        {
            $qryTransferArrDate = " WHERE  :arrivalDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
            $qry_serviceClaimAgency .= $qryTransferArrDate;	
            $qry_transferClaim = $con->prepare($qry_serviceClaimAgency);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name,  ":arrivalDate"=>$arrivalDate, ":id_dept"=>$id_dept, ":id_tour_operator"=>$payer));
        }
        else if ($transfer_type == 'DEP')
        {
            $qry_serviceClaimAgency .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
            $qry_transferClaim = $con->prepare($qry_serviceClaimAgency);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":special_name"=>$special_name,  ":departureDate"=>$departureDate, ":id_dept"=>$id_dept, ":id_tour_operator"=>$payer));
        }
        else if ($transfer_type == 'INTER HOTEL') 
        {
            $qry_serviceClaimAgency .= " WHERE  :departureDate BETWEEN claim_from AND claim_to";
            $qry_transferClaim = $con->prepare($qry_serviceClaimAgency);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":departureDate"=>$departureDate, ":id_dept"=>$id_dept, ":id_tour_operator"=>$payer));
        }
        else if ($transfer_type == 'ACTIVITY')
        {
            $qry_serviceClaimAgency .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
            $qry_transferClaim = $con->prepare($qry_serviceClaimAgency);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":special_name"=>$special_name,  ":departureDate"=>$departureDate, ":id_dept"=>$id_dept, ":id_tour_operator"=>$payer));
        }

		$row_count_p = $qry_transferClaim->rowCount();

		if ($row_count_p > 0) 
		{
			while ($row = $qry_transferClaim->fetch(PDO::FETCH_ASSOC)) {
				$productServiceClaim[] = array(
					'id_product_service'           => $row['id_product_service'],
					'id_product_service_claim'  => $row['id_product_service_claim'],
					'id_product_service_cost'    => $row['id_product_service_cost'],
					'service_name'                    => $row['service_name'],
					'special_name'				      => $row['special_name'],
					'service_from'				       => $row['service_from'],
					'service_to'				         => $row['service_to'],
					'claim_from'				        => $row['claim_from'],
					'claim_to'					          => $row['claim_to'],
					'specific_to'				         => $row['specific_to'],
					'id_dept'					          => $row['id_dept'],
					'service_country'			     => $row['service_country'],
					'id_coast'					          => $row['id_coast'],
					'id_creditor'				         => $row['id_creditor'],
					'id_tax'					            => $row['id_tax'],
					'charge'					           => $row['charge'],
					'claim_curr'				         => $row['claim_curr'],
					'cost_curr'					          => $row['cost_curr'],
					'duration'					           => $row['duration'],
					'description'				          => $row['description'],
					'comments'					        => $row['comments'],
					'cancellation'				          => $row['cancellation'],
					'for_infant'				            => $row['for_infant'],
					'age_inf_from'				         => $row['age_inf_from'],
					'age_inf_to'				           => $row['age_inf_to'],
					'ps_infant_claim'			        => $row['ps_infant_claim'],
					'ps_infant_cost'			         => $row['ps_infant_cost'],
					'for_child'					             => $row['for_child'],
					'age_child_from'			        => $row['age_child_from'],
					'age_child_to'				          => $row['age_child_to'],
					'ps_child_claim'			         => $row['ps_child_claim'],
					'ps_child_cost'				         => $row['ps_child_cost'],
					'for_adult'					            => $row['for_adult'],
					'ps_adult_claim'			        => $row['ps_adult_claim'],
					'ps_adult_cost'				         => $row['ps_adult_cost'],
					'min_pax'					            => $row['min_pax'],
					'max_pax'					           => $row['max_pax'],
					'max_adult'					           => $row['max_adult'],
                    'OUTCOME'                          => "OK"
				);
			}    
			$myData = $productServiceClaim;
			echo json_encode($myData);
		} 
		else 
		{
			$qry_serviceClaimCountry ="
				SELECT * FROM
				(
                    SELECT 
                        PS.id_product_service,
                        PS_CLAIM.id_product_service_claim,
                        PS_CLAIM.id_product_service_cost,
                        PS.service_name,
                        PS.special_name,
                        PS.valid_from AS service_from,
                        PS.valid_to AS service_to,
                        PS_CLAIM.valid_from AS claim_from,
                        PS_CLAIM.valid_to AS claim_to,
                        PS_CLAIM.specific_to,
                        PS.id_dept,
                        PS.id_country AS service_country,
                        PS.id_coast,
                        PS.id_creditor,
                        PS.id_tax,
                        PS.charge,
                        PS_CLAIM.id_currency AS id_claim_curr,
                        CUR.currency_code AS claim_curr,
                        PS_COST.currency AS cost_curr,
                        PS.duration,
                        PS.on_approved,
                        PS.on_api,
                        PS.description,
                        PS.comments,
                        PS.cancellation,
                        PS.for_infant,
                        PS.age_inf_from,
                        PS.age_inf_to,
                        PS_CLAIM.ps_infant_claim,
                        PS_COST.ps_infant_cost,
                        PS.for_child,
                        PS.age_child_from,
                        PS.age_child_to, 
                        PS_CLAIM.ps_child_claim,
                        PS_COST.ps_child_cost,
                        PS.for_adult,
                        PS_CLAIM.ps_adult_claim,
                        PS_COST.ps_adult_cost,
                        PS.min_pax,
                        PS.max_adult,
                        PS.max_pax
                    FROM 
                        product_service PS,
                        product_service_claim PS_CLAIM,
                        product_service_cost PS_COST,
                        tblcurrency CUR,
						product_service_claim_country PS_CLAIM_C
                    WHERE PS.id_product_service = PS_CLAIM.id_product_service
                    AND PS.id_product_service = PS_CLAIM.id_product_service
                    AND PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
					AND PS_CLAIM.id_product_service_claim = PS_CLAIM_C.id_product_service_claim
                    AND PS_CLAIM.id_currency = CUR.id
                    AND PS.id_product = :transfer_vehicle
                    AND PS.service_name = :service_name
                    AND PS.id_dept = :id_dept
					AND PS_CLAIM.specific_to = 'C'
					AND PS_CLAIM_C.id_country = :id_country
                    AND PS.on_api = 1
                    AND PS.on_approved = 1
                    AND PS.active = 1
                    AND PS_CLAIM.active = 1
				) AS product_service_claim ";
        
            if ($transfer_type == 'ARR')
            {
                $qryTransferArrDate = " WHERE  :arrivalDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
                $qry_serviceClaimCountry .= $qryTransferArrDate;	
                $qry_transferClaim = $con->prepare($qry_serviceClaimCountry);
                $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name,  ":arrivalDate"=>$arrivalDate,  ":id_dept"=>$id_dept, ":id_country"=>$id_country));
            }
            else if ($transfer_type == 'DEP')
            {
                $qry_serviceClaimCountry .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
                $qry_transferClaim = $con->prepare($qry_serviceClaimCountry);
                $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":special_name"=>$special_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept, ":id_country"=>$id_country));
            }
            else if ($transfer_type == 'INTER HOTEL') 
            {
                $qry_serviceClaimCountry .= " WHERE  :departureDate BETWEEN claim_from AND claim_to";
                $qry_transferClaim = $con->prepare($qry_serviceClaimCountry);
                $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept, ":id_country"=>$id_country));
            }
            else if ($transfer_type == 'ACTIVITY')
            {
                $qry_serviceClaimCountry .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
                $qry_transferClaim = $con->prepare($qry_serviceClaimCountry);
                $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":special_name"=>$special_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept, ":id_country"=>$id_country));
            }
			$row_count_p = $qry_transferClaim->rowCount();

			if ($row_count_p > 0) 
			{
				while ($row = $qry_transferClaim->fetch(PDO::FETCH_ASSOC)) {
					$productServiceClaim[] = array(
					'id_product_service'		     => $row['id_product_service'],
					'id_product_service_claim'	  => $row['id_product_service_claim'],
					'id_product_service_cost'	   => $row['id_product_service_cost'],
					'service_name'				         => $row['service_name'],
					'special_name'				          => $row['special_name'],
					'service_from'				           => $row['service_from'],
					'service_to'				             => $row['service_to'],
					'claim_from'				            => $row['claim_from'],
					'claim_to'					              => $row['claim_to'],
					'specific_to'				             => $row['specific_to'],
					'id_dept'					              => $row['id_dept'],
					'service_country'			         => $row['service_country'],
					'id_coast'					              => $row['id_coast'],
					'id_creditor'				             => $row['id_creditor'],
					'id_tax'					               => $row['id_tax'],
					'charge'					              => $row['charge'],
					'claim_curr'				            => $row['claim_curr'],
					'cost_curr'					             => $row['cost_curr'],
					'duration'					             => $row['duration'],
					'description'				            => $row['description'],
					'comments'					          => $row['comments'],
					'cancellation'				            => $row['cancellation'],
					'for_infant'				             => $row['for_infant'],
					'age_inf_from'				          => $row['age_inf_from'],
					'age_inf_to'				            => $row['age_inf_to'],
					'ps_infant_claim'			         => $row['ps_infant_claim'],
					'ps_infant_cost'			         => $row['ps_infant_cost'],
					'for_child'					             => $row['for_child'],
					'age_child_from'			        => $row['age_child_from'],
					'age_child_to'				          => $row['age_child_to'],
					'ps_child_claim'			         => $row['ps_child_claim'],
					'ps_child_cost'				          => $row['ps_child_cost'],
					'for_adult'					             => $row['for_adult'],
					'ps_adult_claim'			         => $row['ps_adult_claim'],
					'ps_adult_cost'				         => $row['ps_adult_cost'],
					'min_pax'					            => $row['min_pax'],
					'max_pax'					           => $row['max_pax'],
					'max_adult'					           => $row['max_adult'],
                    'OUTCOME'                          => "OK"
					);
				}    
				$myData = $productServiceClaim;
				echo json_encode($myData);
			} 
			else	
			{
				$qry_serviceClaimPublic ="
					SELECT * FROM
					(
						SELECT 
                            PS.id_product_service,
                            PS_CLAIM.id_product_service_claim,
                            PS_CLAIM.id_product_service_cost,
                            PS.service_name,
                            PS.special_name,
                            PS.valid_from AS service_from,
                            PS.valid_to AS service_to,
                            PS_CLAIM.valid_from AS claim_from,
                            PS_CLAIM.valid_to AS claim_to,
                            PS_CLAIM.specific_to,
                            PS.id_dept,
                            PS.id_country AS service_country,
                            PS.id_coast,
                            PS.id_creditor,
                            PS.id_tax,
                            PS.charge,
                            PS_CLAIM.id_currency AS id_claim_curr,
                            CUR.currency_code AS claim_curr,
                            PS_COST.currency AS cost_curr,
                            PS.duration,
                            PS.on_approved,
                            PS.on_api,
                            PS.description,
                            PS.comments,
                            PS.cancellation,
                            PS.for_infant,
                            PS.age_inf_from,
                            PS.age_inf_to,
                            PS_CLAIM.ps_infant_claim,
                            PS_COST.ps_infant_cost,
                            PS.for_child,
                            PS.age_child_from,
                            PS.age_child_to, 
                            PS_CLAIM.ps_child_claim,
                            PS_COST.ps_child_cost,
                            PS.for_adult,
                            PS_CLAIM.ps_adult_claim,
                            PS_COST.ps_adult_cost,
                            PS.min_pax,
                            PS.max_adult,
                            PS.max_pax
                        FROM 
                            product_service PS,
                            product_service_claim PS_CLAIM,
                            product_service_cost PS_COST,
                            tblcurrency CUR
                        WHERE PS.id_product_service = PS_CLAIM.id_product_service
                        AND PS.id_product_service = PS_CLAIM.id_product_service
                        AND PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
                        AND PS_CLAIM.id_currency = CUR.id
                        AND PS.id_product = :transfer_vehicle
                        AND PS.service_name = :service_name
                        AND PS.id_dept = :id_dept
                        AND PS_CLAIM.specific_to = 'B'
                        AND PS.on_api = 1
                        AND PS.on_approved = 1
                        AND PS.active = 1
                        AND PS_CLAIM.active = 1
					) AS product_service_claim";
                
                
                    if ($transfer_type == 'ARR')
                    {
                        $qryTransferArrDate = " WHERE  :arrivalDate BETWEEN claim_from AND claim_to 
                        AND special_name = :special_name";
                        $qry_serviceClaimPublic .= $qryTransferArrDate;	
                        $qry_transferClaim = $con->prepare($qry_serviceClaimPublic);
                        $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":arrivalDate"=>$arrivalDate,  ":id_dept"=>$id_dept));
                    }
                    else if ($transfer_type == 'DEP')
                    {
                        $qry_serviceClaimPublic .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                        AND special_name = :special_name";
                        $qry_transferClaim = $con->prepare($qry_serviceClaimPublic);
                        $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept));
                    }
                    else if ($transfer_type == 'INTER HOTEL') 
                    {
                        $qry_serviceClaimPublic .= " WHERE  :departureDate BETWEEN claim_from AND claim_to";
                        $qry_transferClaim = $con->prepare($qry_serviceClaimPublic);
                        $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept));
                    }
                    else if ($transfer_type == 'ACTIVITY')
                    {
                        $qry_serviceClaimPublic .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                        AND special_name = :special_name";
                        $qry_transferClaim = $con->prepare($qry_serviceClaimPublic);
                        $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept));
                    }
					$row_count_p = $qry_transferClaim->rowCount();

					if ($row_count_p > 0) 
					{
						while ($row = $qry_transferClaim->fetch(PDO::FETCH_ASSOC)) {
							$productServiceClaim[] = array(
                                'id_product_service'		      => $row['id_product_service'],
                                'id_product_service_claim'	  => $row['id_product_service_claim'],
                                'id_product_service_cost'	   => $row['id_product_service_cost'],
                                'service_name'				         => $row['service_name'],
                                'special_name'				          => $row['special_name'],
                                'service_from'				           => $row['service_from'],
                                'service_to'				             => $row['service_to'],
                                'claim_from'				            => $row['claim_from'],
                                'claim_to'					              => $row['claim_to'],
                                'specific_to'				             => $row['specific_to'],
                                'id_dept'					              => $row['id_dept'],
                                'service_country'			         => $row['service_country'],
                                'id_coast'					              => $row['id_coast'],
                                'id_creditor'				             => $row['id_creditor'],
                                'id_tax'					                => $row['id_tax'],
                                'charge'					               => $row['charge'],
                                'claim_curr'				             => $row['claim_curr'],
                                'cost_curr'					             => $row['cost_curr'],
                                'duration'					             => $row['duration'],
                                'description'				           => $row['description'],
                                'comments'					         => $row['comments'],
                                'cancellation'				           => $row['cancellation'],
                                'for_infant'				            => $row['for_infant'],
                                'age_inf_from'				         => $row['age_inf_from'],
                                'age_inf_to'				           => $row['age_inf_to'],
                                'ps_infant_claim'			        => $row['ps_infant_claim'],
                                'ps_infant_cost'			        => $row['ps_infant_cost'],
                                'for_child'					            => $row['for_child'],
                                'age_child_from'			       => $row['age_child_from'],
                                'age_child_to'				         => $row['age_child_to'],
                                'ps_child_claim'			        => $row['ps_child_claim'],
                                'ps_child_cost'				        => $row['ps_child_cost'],
                                'for_adult'					           => $row['for_adult'],
                                'ps_adult_claim'			       => $row['ps_adult_claim'],
                                'ps_adult_cost'				       => $row['ps_adult_cost'],
                                'min_pax'					          => $row['min_pax'],
                                'max_pax'					          => $row['max_pax'],
                                'max_adult'					          => $row['max_adult'],
                                'OUTCOME'                         => "OK"
							);
						}    
						$myData = $productServiceClaim;
						echo json_encode($myData);
					} 
					else
					{
                        $productServiceClaim[] = array("OUTCOME" => "NO DATA", 'id_product_service' => 3, 'service_name' => 0);
                        $myData = $productServiceClaim;
                        echo json_encode($myData);
					}
			}
		}
		
	}
	else if ($paid_by == 'Client') 
	{
		$qry_serviceClaimClient="
			SELECT * FROM
			(
				SELECT 
                    PS.id_product_service,
                    PS_CLAIM.id_product_service_claim,
                    PS_CLAIM.id_product_service_cost,
                    PS.service_name,
                    PS.special_name,
                    PS.valid_from AS service_from,
                    PS.valid_to AS service_to,
                    PS_CLAIM.valid_from AS claim_from,
                    PS_CLAIM.valid_to AS claim_to,
                    PS_CLAIM.specific_to,
                    PS.id_dept,
                    PS.id_country AS service_country,
                    PS.id_coast,
                    PS.id_creditor,
                    PS.id_tax,
                    PS.charge,
                    PS_CLAIM.id_currency AS id_claim_curr,
                    CUR.currency_code AS claim_curr,
                    PS_COST.currency AS cost_curr,
                    PS.duration,
                    PS.on_approved,
                    PS.on_api,
                    PS.description,
                    PS.comments,
                    PS.cancellation,
                    PS.for_infant,
                    PS.age_inf_from,
                    PS.age_inf_to,
                    PS_CLAIM.ps_infant_claim,
                    PS_COST.ps_infant_cost,
                    PS.for_child,
                    PS.age_child_from,
                    PS.age_child_to, 
                    PS_CLAIM.ps_child_claim,
                    PS_COST.ps_child_cost,
                    PS.for_adult,
                    PS_CLAIM.ps_adult_claim,
                    PS_COST.ps_adult_cost,
                    PS.min_pax,
                    PS.max_adult,
                    PS.max_pax
                FROM 
                    product_service PS,
                    product_service_claim PS_CLAIM,
                    product_service_cost PS_COST,
                    tblcurrency CUR
                WHERE PS.id_product_service = PS_CLAIM.id_product_service
                AND PS.id_product_service = PS_CLAIM.id_product_service
                AND PS_CLAIM.id_product_service_cost = PS_COST.id_product_service_cost
                AND PS_CLAIM.id_currency = CUR.id
                AND PS.id_product = :transfer_vehicle
                AND PS.service_name = :service_name
                AND PS.special_name = :special_name
                AND :bookingDate BETWEEN PS_CLAIM.valid_from AND PS_CLAIM.valid_to
                AND PS.id_dept = :id_dept
                AND PS_CLAIM.specific_to = 'D'
                AND PS.on_api = 1
                AND PS.on_approved = 1
                AND PS.active = 1
                AND PS_CLAIM.active = 1
			) AS product_service_claim";
        
		$qry_serviceClaimClient .= $qryTransferDate;	
		$qry_transferClaim = $con->prepare($qry_serviceClaimClient);
		$qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":transferDate"=>$transferDate, ":id_dept"=>$id_dept));
        
        
        if ($transfer_type == 'ARR')
        {
            $qryTransferArrDate = " WHERE  :arrivalDate BETWEEN claim_from AND claim_to 
            AND special_name = :special_name";
            $qry_serviceClaimClient .= $qryTransferArrDate;	
            $qry_transferClaim = $con->prepare($qry_serviceClaimClient);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":arrivalDate"=>$arrivalDate,  ":id_dept"=>$id_dept));
        }
        else if ($transfer_type == 'DEP')
        {
            $qry_serviceClaimClient .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
            AND special_name = :special_name";
            $qry_transferClaim = $con->prepare($qry_serviceClaimClient);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,  ":special_name"=>$special_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept));
        }
        else if ($transfer_type == 'INTER HOTEL') 
        {
            $qry_serviceClaimClient .= " WHERE  :departureDate BETWEEN claim_from AND claim_to";
            $qry_transferClaim = $con->prepare($qry_serviceClaimClient);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name, ":departureDate"=>$departureDate,  ":id_dept"=>$id_dept));
        }
        else if ($transfer_type == 'ACTIVITY')
        {
            $qry_serviceClaimClient .= " WHERE  :departureDate BETWEEN claim_from AND claim_to 
                AND special_name = :special_name";
            $qry_transferClaim = $con->prepare($qry_serviceClaimClient);
            $qry_transferClaim->execute(array(":transfer_vehicle"=>$transfer_vehicle, ":service_name"=>$service_name,   ":special_name"=>$special_name,  ":departureDate"=>$departureDate, ":id_dept"=>$id_dept, ":payer"=>$payer));
        }

		$row_count_p = $qry_transferClaim->rowCount();

		if ($row_count_p > 0) 
		{
			while ($row = $qry_transferClaim->fetch(PDO::FETCH_ASSOC)) {
				$productServiceClaim[] = array(
                    'id_product_service'		      => $row['id_product_service'],
                    'id_product_service_claim'	  => $row['id_product_service_claim'],
                    'id_product_service_cost'	   => $row['id_product_service_cost'],
                    'service_name'				         => $row['service_name'],
                    'special_name'				          => $row['special_name'],
                    'service_from'				           => $row['service_from'],
                    'service_to'				             => $row['service_to'],
                    'claim_from'				            => $row['claim_from'],
                    'claim_to'					              => $row['claim_to'],
                    'specific_to'				             => $row['specific_to'],
                    'id_dept'					              => $row['id_dept'],
                    'service_country'			         => $row['service_country'],
                    'id_coast'					              => $row['id_coast'],
                    'id_creditor'				             => $row['id_creditor'],
                    'id_tax'					                => $row['id_tax'],
                    'charge'					               => $row['charge'],
                    'claim_curr'				             => $row['claim_curr'],
                    'cost_curr'					             => $row['cost_curr'],
                    'duration'					             => $row['duration'],
                    'description'				           => $row['description'],
                    'comments'					         => $row['comments'],
                    'cancellation'				           => $row['cancellation'],
                    'for_infant'				            => $row['for_infant'],
                    'age_inf_from'				         => $row['age_inf_from'],
                    'age_inf_to'				           => $row['age_inf_to'],
                    'ps_infant_claim'			        => $row['ps_infant_claim'],
                    'ps_infant_cost'			        => $row['ps_infant_cost'],
                    'for_child'					            => $row['for_child'],
                    'age_child_from'			       => $row['age_child_from'],
                    'age_child_to'				         => $row['age_child_to'],
                    'ps_child_claim'			        => $row['ps_child_claim'],
                    'ps_child_cost'				        => $row['ps_child_cost'],
                    'for_adult'					           => $row['for_adult'],
                    'ps_adult_claim'			       => $row['ps_adult_claim'],
                    'ps_adult_cost'				       => $row['ps_adult_cost'],
                    'min_pax'					          => $row['min_pax'],
                    'max_pax'					          => $row['max_pax'],
                    'max_adult'					          => $row['max_adult'],
                    'OUTCOME'                         => "OK"
				);
			}    
			$myData = $productServiceClaim;
			echo json_encode($myData);
		} 
		else
		{
			$productServiceClaim[] = array("OUTCOME" => "NO DATA", 'id_product_service' => 3, 'service_name' => 0);
			$myData = $productServiceClaim;
			echo json_encode($myData);
		}
	}
    
	
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

