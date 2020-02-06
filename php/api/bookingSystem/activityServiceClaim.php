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
    
    $id_booking = $_POST["id_booking"];
    $paid_by = trim($_POST["paid_by"]);
    $payer = trim($_POST["payer"]);
    $activity_date = trim($_POST["activity_date"]);
    $activity_day = trim($_POST["activity_day"]);    
    $id_product = trim($_POST["activity_product"]);    
    $id_service = trim($_POST["activity_service"]);    

    $con = pdo_con();
    
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
	
	if ($paid_by == 'TO') 
	{
		$qry_country = $con->prepare("SELECT * FROM tbltouroperator WHERE id = :payer AND active = 1");
		$qry_country->execute(array(":payer"=>$payer));

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
	
	
	if ($activity_day == 'Sun')
	{
		$qry_servicedDay = " "."WHERE on_sunday = 1 AND ex_sunday = 0"; 
	}
	else if ($activity_day == 'Mon')
	{
		$qry_servicedDay = " "."WHERE on_monday = 1 AND ex_monday = 0"; 
	}
	else if ($activity_day == 'Tue')
	{
		$qry_servicedDay = " "."WHERE on_tuesday = 1 AND ex_tuesday = 0"; 
	}
	else if ($activity_day == 'Wed')
	{
		$qry_servicedDay = " "."WHERE on_wednesday = 1 AND ex_wednesday = 0"; 
	}
	else if ($activity_day == 'Thu')
	{
		$qry_servicedDay = " "."WHERE on_thursday = 1 AND ex_thursday = 0"; 
	}
	else if ($activity_day == 'Fri')
	{
		$qry_servicedDay = " "."WHERE on_friday = 1 AND ex_friday = 0"; 
	}
	else if ($activity_day == 'Sat')
	{
		$qry_servicedDay = " "."WHERE on_saturday = 1 AND ex_saturday = 0"; 
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
				PS.transfer_included,
				PS.on_approved,
				PS.on_api,
				PS.description,
				PS.comments,
				PS.is_pakage,
				PS.on_monday,
				PS_CLAIM.ex_monday,
				PS.on_tuesday,
				PS_CLAIM.ex_tuesday,
				PS.on_wednesday,
				PS_CLAIM.ex_wednesday,
				PS.on_thursday,
				PS_CLAIM.ex_thursday,
				PS.on_friday,
				PS_CLAIM.ex_friday,
				PS.on_saturday,
				PS_CLAIM.ex_saturday,
				PS.on_sunday,
				PS_CLAIM.ex_sunday,
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
				PS.for_teen,
				PS.age_teen_from,
				PS.age_teen_to,
				PS_CLAIM.ps_teen_claim,
				PS_COST.ps_teen_cost,
				PS.for_adult,
				PS_CLAIM.ps_adult_claim,
				PS_COST.ps_adult_cost,
				PS.min_pax,
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
			AND PS.id_product = :id_product
			AND PS.id_product_service = :id_service
			AND :activity_date BETWEEN PS_CLAIM.valid_from AND PS_CLAIM.valid_to
			AND PS.id_dept = :id_dept
			AND PS_CLAIM.specific_to = 'A'
			AND PS_CLAIM_TO.id_tour_operator = :payer
			AND PS.active = 1
			AND PS_CLAIM.active = 1
            AND PS.on_approved = 1
		) AS product_service_claim";
		
		$qry_serviceClaimAgency .= $qry_servicedDay;	
		$qry_activityClaim = $con->prepare($qry_serviceClaimAgency);
		$qry_activityClaim->execute(array(":id_product"=>$id_product, ":id_service"=>$id_service,":activity_date"=>$activity_date, ":id_dept"=>$id_dept, ":payer"=>$payer));
		
		$row_count_p = $qry_activityClaim->rowCount();

		if ($row_count_p > 0) 
		{
			while ($row = $qry_activityClaim->fetch(PDO::FETCH_ASSOC)) {
				$productServiceClaim[] = array(
					'id_product_service'		=> $row['id_product_service'],
					'id_product_service_claim'	=> $row['id_product_service_claim'],
					'id_product_service_cost'	=> $row['id_product_service_cost'],
					'service_name'				=> $row['service_name'],
					'service_from'				=> $row['service_from'],
					'service_to'				=> $row['service_to'],
					'claim_from'				=> $row['claim_from'],
					'claim_to'					=> $row['claim_to'],
					'specific_to'				=> $row['specific_to'],
					'id_dept'					=> $row['id_dept'],
					'service_country'			=> $row['service_country'],
					'id_coast'					=> $row['id_coast'],
					'id_creditor'				=> $row['id_creditor'],
					'id_tax'					=> $row['id_tax'],
					'charge'					=> $row['charge'],
					'claim_curr'				=> $row['claim_curr'],
					'cost_curr'					=> $row['cost_curr'],
					'duration'					=> $row['duration'],
					'transfer_included'			=> $row['transfer_included'],
					'description'				=> $row['description'],
					'comments'					=> $row['comments'],
					'is_pakage'					=> $row['is_pakage'],
					'on_monday'					=> $row['on_monday'],
					'ex_monday'					=> $row['ex_monday'],
					'on_tuesday'				=> $row['on_tuesday'],
					'ex_tuesday'				=> $row['ex_tuesday'],
					'on_wednesday'				=> $row['on_wednesday'],
					'ex_wednesday'				=> $row['ex_wednesday'],
					'on_thursday'				=> $row['on_thursday'],
					'ex_thursday'				=> $row['ex_thursday'],
					'on_friday'					=> $row['on_friday'],
					'ex_friday'					=> $row['ex_friday'],
					'on_saturday'				=> $row['on_saturday'],
					'ex_saturday'				=> $row['ex_saturday'],
					'on_sunday'					=> $row['on_sunday'],
					'ex_sunday'					=> $row['ex_sunday'],
					'cancellation'				=> $row['cancellation'],
					'for_infant'				=> $row['for_infant'],
					'age_inf_from'				=> $row['age_inf_from'],
					'age_inf_to'				=> $row['age_inf_to'],
					'ps_infant_claim'			=> $row['ps_infant_claim'],
					'ps_infant_cost'			=> $row['ps_infant_cost'],
					'for_child'					=> $row['for_child'],
					'age_child_from'			=> $row['age_child_from'],
					'age_child_to'				=> $row['age_child_to'],
					'ps_child_claim'			=> $row['ps_child_claim'],
					'ps_child_cost'				=> $row['ps_child_cost'],
					'for_teen'					=> $row['for_teen'],
					'age_teen_from'				=> $row['age_teen_from'],
					'age_teen_to'				=> $row['age_teen_to'],
					'ps_teen_claim'				=> $row['ps_teen_claim'],
					'ps_teen_cost'				=> $row['ps_teen_cost'],
					'for_adult'					=> $row['for_adult'],
					'ps_adult_claim'			=> $row['ps_adult_claim'],
					'ps_adult_cost'				=> $row['ps_adult_cost'],
					'min_pax'					=> $row['min_pax'],
					'max_pax'					=> $row['max_pax'],
                    'OUTCOME'               => "OK"
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
						PS.transfer_included,
						PS.on_approved,
						PS.on_api,
						PS.description,
						PS.comments,
						PS.is_pakage,
						PS.on_monday,
						PS_CLAIM.ex_monday,
						PS.on_tuesday,
						PS_CLAIM.ex_tuesday,
						PS.on_wednesday,
						PS_CLAIM.ex_wednesday,
						PS.on_thursday,
						PS_CLAIM.ex_thursday,
						PS.on_friday,
						PS_CLAIM.ex_friday,
						PS.on_saturday,
						PS_CLAIM.ex_saturday,
						PS.on_sunday,
						PS_CLAIM.ex_sunday,
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
						PS.for_teen,
						PS.age_teen_from,
						PS.age_teen_to,
						PS_CLAIM.ps_teen_claim,
						PS_COST.ps_teen_cost,
						PS.for_adult,
						PS_CLAIM.ps_adult_claim,
						PS_COST.ps_adult_cost,
						PS.min_pax,
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
					AND PS.id_product = :id_product
					AND PS.id_product_service = :id_service
					AND :activity_date BETWEEN PS_CLAIM.valid_from AND PS_CLAIM.valid_to
					AND PS.id_dept = :id_dept
					AND PS_CLAIM.specific_to = 'C'
					AND PS_CLAIM_C.id_country = :id_country
					AND PS.active = 1
					AND PS_CLAIM.active = 1
                    AND PS.on_approved = 1
				) AS product_service_claim";

			$qry_serviceClaimCountry .= $qry_servicedDay;	
			$qry_activityClaim = $con->prepare($qry_serviceClaimCountry);
			$qry_activityClaim->execute(array(":id_product"=>$id_product, ":id_service"=>$id_service,":activity_date"=>$activity_date, ":id_dept"=>$id_dept, ":id_country"=>$id_country));

			$row_count_p = $qry_activityClaim->rowCount();

			if ($row_count_p > 0) 
			{
				while ($row = $qry_activityClaim->fetch(PDO::FETCH_ASSOC)) {
					$productServiceClaim[] = array(
						'id_product_service'		=> $row['id_product_service'],
						'id_product_service_claim'	=> $row['id_product_service_claim'],
						'id_product_service_cost'	=> $row['id_product_service_cost'],
						'service_name'				=> $row['service_name'],
						'service_from'				=> $row['service_from'],
						'service_to'				=> $row['service_to'],
						'claim_from'				=> $row['claim_from'],
						'claim_to'					=> $row['claim_to'],
						'specific_to'				=> $row['specific_to'],
						'id_dept'					=> $row['id_dept'],
						'service_country'			=> $row['service_country'],
						'id_coast'					=> $row['id_coast'],
						'id_creditor'				=> $row['id_creditor'],
						'id_tax'					=> $row['id_tax'],
						'charge'					=> $row['charge'],
						'claim_curr'				=> $row['claim_curr'],
						'cost_curr'					=> $row['cost_curr'],
						'duration'					=> $row['duration'],
						'transfer_included'			=> $row['transfer_included'],
						'description'				=> $row['description'],
						'comments'					=> $row['comments'],
						'is_pakage'					=> $row['is_pakage'],
						'on_monday'					=> $row['on_monday'],
						'ex_monday'					=> $row['ex_monday'],
						'on_tuesday'				=> $row['on_tuesday'],
						'ex_tuesday'				=> $row['ex_tuesday'],
						'on_wednesday'				=> $row['on_wednesday'],
						'ex_wednesday'				=> $row['ex_wednesday'],
						'on_thursday'				=> $row['on_thursday'],
						'ex_thursday'				=> $row['ex_thursday'],
						'on_friday'					=> $row['on_friday'],
						'ex_friday'					=> $row['ex_friday'],
						'on_saturday'				=> $row['on_saturday'],
						'ex_saturday'				=> $row['ex_saturday'],
						'on_sunday'					=> $row['on_sunday'],
						'ex_sunday'					=> $row['ex_sunday'],
						'cancellation'				=> $row['cancellation'],
						'for_infant'				=> $row['for_infant'],
						'age_inf_from'				=> $row['age_inf_from'],
						'age_inf_to'				=> $row['age_inf_to'],
						'ps_infant_claim'			=> $row['ps_infant_claim'],
						'ps_infant_cost'			=> $row['ps_infant_cost'],
						'for_child'					=> $row['for_child'],
						'age_child_from'			=> $row['age_child_from'],
						'age_child_to'				=> $row['age_child_to'],
						'ps_child_claim'			=> $row['ps_child_claim'],
						'ps_child_cost'				=> $row['ps_child_cost'],
						'for_teen'					=> $row['for_teen'],
						'age_teen_from'				=> $row['age_teen_from'],
						'age_teen_to'				=> $row['age_teen_to'],
						'ps_teen_claim'				=> $row['ps_teen_claim'],
						'ps_teen_cost'				=> $row['ps_teen_cost'],
						'for_adult'					=> $row['for_adult'],
						'ps_adult_claim'			=> $row['ps_adult_claim'],
						'ps_adult_cost'				=> $row['ps_adult_cost'],
						'min_pax'					=> $row['min_pax'],
						'max_pax'					=> $row['max_pax'],
                        'OUTCOME'               => "OK"
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
							PS.transfer_included,
							PS.on_approved,
							PS.on_api,
							PS.description,
							PS.comments,
							PS.is_pakage,
							PS.on_monday,
							PS_CLAIM.ex_monday,
							PS.on_tuesday,
							PS_CLAIM.ex_tuesday,
							PS.on_wednesday,
							PS_CLAIM.ex_wednesday,
							PS.on_thursday,
							PS_CLAIM.ex_thursday,
							PS.on_friday,
							PS_CLAIM.ex_friday,
							PS.on_saturday,
							PS_CLAIM.ex_saturday,
							PS.on_sunday,
							PS_CLAIM.ex_sunday,
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
							PS.for_teen,
							PS.age_teen_from,
							PS.age_teen_to,
							PS_CLAIM.ps_teen_claim,
							PS_COST.ps_teen_cost,
							PS.for_adult,
							PS_CLAIM.ps_adult_claim,
							PS_COST.ps_adult_cost,
							PS.min_pax,
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
						AND PS.id_product = :id_product
						AND PS.id_product_service = :id_service
						AND :activity_date BETWEEN PS_CLAIM.valid_from AND PS_CLAIM.valid_to
						AND PS.id_dept = :id_dept
						AND PS_CLAIM.specific_to = 'B'
						AND PS.active = 1
						AND PS_CLAIM.active = 1
                        AND PS.on_approved = 1
					) AS product_service_claim";

					$qry_serviceClaimPublic .= $qry_servicedDay;	
					$qry_activityClaim = $con->prepare($qry_serviceClaimPublic);
					$qry_activityClaim->execute(array(":id_product"=>$id_product, ":id_service"=>$id_service,":activity_date"=>$activity_date, ":id_dept"=>$id_dept));

					$row_count_p = $qry_activityClaim->rowCount();

					if ($row_count_p > 0) 
					{
						while ($row = $qry_activityClaim->fetch(PDO::FETCH_ASSOC)) {
							$productServiceClaim[] = array(
								'id_product_service'		=> $row['id_product_service'],
								'id_product_service_claim'	=> $row['id_product_service_claim'],
								'id_product_service_cost'	=> $row['id_product_service_cost'],
								'service_name'				=> $row['service_name'],
								'service_from'				=> $row['service_from'],
								'service_to'				=> $row['service_to'],
								'claim_from'				=> $row['claim_from'],
								'claim_to'					=> $row['claim_to'],
								'specific_to'				=> $row['specific_to'],
								'id_dept'					=> $row['id_dept'],
								'service_country'			=> $row['service_country'],
								'id_coast'					=> $row['id_coast'],
								'id_creditor'				=> $row['id_creditor'],
								'id_tax'					=> $row['id_tax'],
								'charge'					=> $row['charge'],
								'claim_curr'				=> $row['claim_curr'],
								'cost_curr'					=> $row['cost_curr'],
								'duration'					=> $row['duration'],
								'transfer_included'			=> $row['transfer_included'],
								'description'				=> $row['description'],
								'comments'					=> $row['comments'],
								'is_pakage'					=> $row['is_pakage'],
								'on_monday'					=> $row['on_monday'],
								'ex_monday'					=> $row['ex_monday'],
								'on_tuesday'				=> $row['on_tuesday'],
								'ex_tuesday'				=> $row['ex_tuesday'],
								'on_wednesday'				=> $row['on_wednesday'],
								'ex_wednesday'				=> $row['ex_wednesday'],
								'on_thursday'				=> $row['on_thursday'],
								'ex_thursday'				=> $row['ex_thursday'],
								'on_friday'					=> $row['on_friday'],
								'ex_friday'					=> $row['ex_friday'],
								'on_saturday'				=> $row['on_saturday'],
								'ex_saturday'				=> $row['ex_saturday'],
								'on_sunday'					=> $row['on_sunday'],
								'ex_sunday'					=> $row['ex_sunday'],
								'cancellation'				=> $row['cancellation'],
								'for_infant'				=> $row['for_infant'],
								'age_inf_from'				=> $row['age_inf_from'],
								'age_inf_to'				=> $row['age_inf_to'],
								'ps_infant_claim'			=> $row['ps_infant_claim'],
								'ps_infant_cost'			=> $row['ps_infant_cost'],
								'for_child'					=> $row['for_child'],
								'age_child_from'			=> $row['age_child_from'],
								'age_child_to'				=> $row['age_child_to'],
								'ps_child_claim'			=> $row['ps_child_claim'],
								'ps_child_cost'				=> $row['ps_child_cost'],
								'for_teen'					=> $row['for_teen'],
								'age_teen_from'				=> $row['age_teen_from'],
								'age_teen_to'				=> $row['age_teen_to'],
								'ps_teen_claim'				=> $row['ps_teen_claim'],
								'ps_teen_cost'				=> $row['ps_teen_cost'],
								'for_adult'					=> $row['for_adult'],
								'ps_adult_claim'			=> $row['ps_adult_claim'],
								'ps_adult_cost'				=> $row['ps_adult_cost'],
								'min_pax'					=> $row['min_pax'],
								'max_pax'					=> $row['max_pax'],
                                'OUTCOME'               => "OK"
							);
						}    
						$myData = $productServiceClaim;
						echo json_encode($myData);
					} 
					 
					else
					{
						$productServiceClaim[] = array('id_product_service'		=> 0);
						$myData = $productServiceClaim;
						echo json_encode($myData);
					}
			}
		}
		
	}
	else if ($paid_by == 'Client') 
	{
		$qry_serviceClaimPublic ="
			SELECT * FROM
			(
				SELECT 
					PS.id_product_service,
					PS_CLAIM.id_product_service_claim,
					PS_CLAIM.id_product_service_cost,
					PS.service_name,
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
					PS.transfer_included,
					PS.on_approved,
					PS.on_api,
					PS.description,
					PS.comments,
					PS.is_pakage,
					PS.on_monday,
					PS_CLAIM.ex_monday,
					PS.on_tuesday,
					PS_CLAIM.ex_tuesday,
					PS.on_wednesday,
					PS_CLAIM.ex_wednesday,
					PS.on_thursday,
					PS_CLAIM.ex_thursday,
					PS.on_friday,
					PS_CLAIM.ex_friday,
					PS.on_saturday,
					PS_CLAIM.ex_saturday,
					PS.on_sunday,
					PS_CLAIM.ex_sunday,
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
					PS.for_teen,
					PS.age_teen_from,
					PS.age_teen_to,
					PS_CLAIM.ps_teen_claim,
					PS_COST.ps_teen_cost,
					PS.for_adult,
					PS_CLAIM.ps_adult_claim,
					PS_COST.ps_adult_cost,
					PS.min_pax,
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
				AND PS.id_product = :id_product
				AND PS.id_product_service = :id_service
				AND :activity_date BETWEEN PS_CLAIM.valid_from AND PS_CLAIM.valid_to
				AND PS.id_dept = :id_dept
				AND PS_CLAIM.specific_to = 'B'
				AND PS.active = 1
				AND PS_CLAIM.active = 1
                AND PS.on_approved = 1
			) AS product_service_claim";

		$qry_serviceClaimPublic .= $qry_servicedDay;	
		$qry_activityClaim = $con->prepare($qry_serviceClaimPublic);
		$qry_activityClaim->execute(array(":id_product"=>$id_product, ":id_service"=>$id_service,":activity_date"=>$activity_date, ":id_dept"=>$id_dept));

		$row_count_p = $qry_activityClaim->rowCount();

		if ($row_count_p > 0) 
		{
			while ($row = $qry_activityClaim->fetch(PDO::FETCH_ASSOC)) {
				$productServiceClaim[] = array(
					'id_product_service'		=> $row['id_product_service'],
					'id_product_service_claim'	=> $row['id_product_service_claim'],
					'id_product_service_cost'	=> $row['id_product_service_cost'],
					'service_name'				=> $row['service_name'],
					'service_from'				=> $row['service_from'],
					'service_to'				=> $row['service_to'],
					'claim_from'				=> $row['claim_from'],
					'claim_to'					=> $row['claim_to'],
					'specific_to'				=> $row['specific_to'],
					'id_dept'					=> $row['id_dept'],
					'service_country'			=> $row['service_country'],
					'id_coast'					=> $row['id_coast'],
					'id_creditor'				=> $row['id_creditor'],
					'id_tax'					=> $row['id_tax'],
					'charge'					=> $row['charge'],
					'claim_curr'				=> $row['claim_curr'],
					'cost_curr'					=> $row['cost_curr'],
					'duration'					=> $row['duration'],
					'transfer_included'			=> $row['transfer_included'],
					'description'				=> $row['description'],
					'comments'					=> $row['comments'],
					'is_pakage'					=> $row['is_pakage'],
					'on_monday'					=> $row['on_monday'],
					'ex_monday'					=> $row['ex_monday'],
					'on_tuesday'				=> $row['on_tuesday'],
					'ex_tuesday'				=> $row['ex_tuesday'],
					'on_wednesday'				=> $row['on_wednesday'],
					'ex_wednesday'				=> $row['ex_wednesday'],
					'on_thursday'				=> $row['on_thursday'],
					'ex_thursday'				=> $row['ex_thursday'],
					'on_friday'					=> $row['on_friday'],
					'ex_friday'					=> $row['ex_friday'],
					'on_saturday'				=> $row['on_saturday'],
					'ex_saturday'				=> $row['ex_saturday'],
					'on_sunday'					=> $row['on_sunday'],
					'ex_sunday'					=> $row['ex_sunday'],
					'cancellation'				=> $row['cancellation'],
					'for_infant'				=> $row['for_infant'],
					'age_inf_from'				=> $row['age_inf_from'],
					'age_inf_to'				=> $row['age_inf_to'],
					'ps_infant_claim'			=> $row['ps_infant_claim'],
					'ps_infant_cost'			=> $row['ps_infant_cost'],
					'for_child'					=> $row['for_child'],
					'age_child_from'			=> $row['age_child_from'],
					'age_child_to'				=> $row['age_child_to'],
					'ps_child_claim'			=> $row['ps_child_claim'],
					'ps_child_cost'				=> $row['ps_child_cost'],
					'for_teen'					=> $row['for_teen'],
					'age_teen_from'				=> $row['age_teen_from'],
					'age_teen_to'				=> $row['age_teen_to'],
					'ps_teen_claim'				=> $row['ps_teen_claim'],
					'ps_teen_cost'				=> $row['ps_teen_cost'],
					'for_adult'					=> $row['for_adult'],
					'ps_adult_claim'			=> $row['ps_adult_claim'],
					'ps_adult_cost'				=> $row['ps_adult_cost'],
					'min_pax'					=> $row['min_pax'],
					'max_pax'					=> $row['max_pax'],
                    'OUTCOME'               => "OK"
				);
			}    
			$myData = $productServiceClaim;
			echo json_encode($myData);
		} 
		else
		{
			$productServiceClaim[] = array("OUTCOME" => "ERROR", 'id_product_service' => 0,'service_name' => 0);
			$myData = $productServiceClaim;
			echo json_encode($myData);
		}
	}
	
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

