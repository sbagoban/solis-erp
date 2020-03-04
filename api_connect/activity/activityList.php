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
    $paid_by = 'TO';
    $payer = $_SESSION["id_tour_operator"];
    $activity_date = trim($_POST["activity_date"]);
    $activity_day = trim($_POST["activity_day"]);    

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
	
	if ($paid_by == 'TO') 
	{
		$qry_Product_List = " 
		SELECT DISTINCT id_product,product_name FROM
		(
			(
				SELECT 
					P.id_product,
					PS.id_product_service,
					PSC.id_product_service_claim,
					PSC.id_product_service_cost,
					P.id_service_type,
					P.id_product_type,
					P.product_name,
					CONCAT(PS.service_name,' - ', PS.special_name) AS service_name,
					PS.valid_from AS service_from,
					PS.valid_to AS service_to,
					PSC.valid_from AS claim_from,
					PSC.valid_to AS claim_to,
					PSC.specific_to,
					PS.id_dept,
					PS.id_country AS service_country,
					PS.id_coast,
					PS.id_creditor,
					PS.id_tax,
					PS.charge,
					PS.duration,
					PS.transfer_included,
					PS.on_approved,
					PS.on_api,
					PS.description,
					PS.comments,
					PS.is_pakage,
					PS.on_monday,
					PSC.ex_monday,
					PS.on_tuesday,
					PSC.ex_tuesday,
					PS.on_wednesday,
					PSC.ex_wednesday,
					PS.on_thursday,
					PSC.ex_thursday,
					PS.on_friday,
					PSC.ex_friday,
					PS.on_saturday,
					PSC.ex_saturday,
					PS.on_sunday,
					PSC.ex_sunday,
					PS.cancellation,
					PS.for_infant,
					PS.age_inf_from,
					PS.age_inf_to,
					PSC.ps_infant_claim,
					PS.for_child,
					PS.age_child_from,
					PSC.ps_child_claim,
					PS.age_child_to, 
					PS.for_teen,
					PS.age_teen_from,
					PS.age_teen_to,
					PSC.ps_teen_claim,
					PSC.ps_adult_claim,
					PS.min_pax,
					PS.max_pax
				FROM 
					product P,
					product_service PS,
					product_service_claim PSC,
					product_service_claim_to PSC_TO
				WHERE P.id_product = PS.id_product
				AND PS.id_product_service = PSC.id_product_service
				AND PSC.id_product_service_claim = PSC_TO.id_product_service_claim
				AND :activity_date BETWEEN PSC.valid_from AND PSC.valid_to
				AND PS.id_dept = :id_dept
				AND PSC.specific_to = 'A'
				AND PSC_TO.id_tour_operator = :payer
				AND P.active = 1
				AND PS.active = 1
				AND PSC.active = 1
				AND PS.on_approved = 1
			)
			UNION
			(
				SELECT 
					P.id_product,
					PS.id_product_service,
					PSC.id_product_service_claim,
					PSC.id_product_service_cost,
					P.id_service_type,
					P.id_product_type,
					P.product_name,
					CONCAT(PS.service_name,' - ', PS.special_name) AS service_name,
					PS.valid_from AS service_from,
					PS.valid_to AS service_to,
					PSC.valid_from AS claim_from,
					PSC.valid_to AS claim_to,
					PSC.specific_to,
					PS.id_dept,
					PS.id_country AS service_country,
					PS.id_coast,
					PS.id_creditor,
					PS.id_tax,
					PS.charge,
					PS.duration,
					PS.transfer_included,
					PS.on_approved,
					PS.on_api,
					PS.description,
					PS.comments,
					PS.is_pakage,
					PS.on_monday,
					PSC.ex_monday,
					PS.on_tuesday,
					PSC.ex_tuesday,
					PS.on_wednesday,
					PSC.ex_wednesday,
					PS.on_thursday,
					PSC.ex_thursday,
					PS.on_friday,
					PSC.ex_friday,
					PS.on_saturday,
					PSC.ex_saturday,
					PS.on_sunday,
					PSC.ex_sunday,
					PS.cancellation,
					PS.for_infant,
					PS.age_inf_from,
					PS.age_inf_to,
					PSC.ps_infant_claim,
					PS.for_child,
					PS.age_child_from,
					PSC.ps_child_claim,
					PS.age_child_to, 
					PS.for_teen,
					PS.age_teen_from,
					PS.age_teen_to,
					PSC.ps_teen_claim,
					PSC.ps_adult_claim,
					PS.min_pax,
					PS.max_pax
				FROM 
					product P,
					product_service PS,
					product_service_claim PSC,
					product_service_claim_country PSC_C
				WHERE P.id_product = PS.id_product
				AND PS.id_product_service = PSC.id_product_service
				AND PSC.id_product_service_claim = PSC_C.id_product_service_claim
				AND :activity_date BETWEEN PSC.valid_from AND PSC.valid_to
				AND PS.id_dept = :id_dept
				AND PSC.specific_to = 'C'
				AND PSC_C.id_country = :id_country
				AND P.active = 1
				AND PS.active = 1
				AND PSC.active = 1
				AND PS.on_approved = 1
			)
			UNION
			(
				SELECT 
					P.id_product,
					PS.id_product_service,
					PSC.id_product_service_claim,
					PSC.id_product_service_cost,
					P.id_service_type,
					P.id_product_type,
					P.product_name,
					CONCAT(PS.service_name,' - ', PS.special_name) AS service_name,
					PS.valid_from AS service_from,
					PS.valid_to AS service_to,
					PSC.valid_from AS claim_from,
					PSC.valid_to AS claim_to,
					PSC.specific_to,
					PS.id_dept,
					PS.id_country AS service_country,
					PS.id_coast,
					PS.id_creditor,
					PS.id_tax,
					PS.charge,
					PS.duration,
					PS.transfer_included,
					PS.on_approved,
					PS.on_api,
					PS.description,
					PS.comments,
					PS.is_pakage,
					PS.on_monday,
					PSC.ex_monday,
					PS.on_tuesday,
					PSC.ex_tuesday,
					PS.on_wednesday,
					PSC.ex_wednesday,
					PS.on_thursday,
					PSC.ex_thursday,
					PS.on_friday,
					PSC.ex_friday,
					PS.on_saturday,
					PSC.ex_saturday,
					PS.on_sunday,
					PSC.ex_sunday,
					PS.cancellation,
					PS.for_infant,
					PS.age_inf_from,
					PS.age_inf_to,
					PSC.ps_infant_claim,
					PS.for_child,
					PS.age_child_from,
					PSC.ps_child_claim,
					PS.age_child_to, 
					PS.for_teen,
					PS.age_teen_from,
					PS.age_teen_to,
					PSC.ps_teen_claim,
					PSC.ps_adult_claim,
					PS.min_pax,
					PS.max_pax
				FROM 
					product P,
					product_service PS,
					product_service_claim PSC
				WHERE P.id_product = PS.id_product
				AND PS.id_product_service = PSC.id_product_service
				AND :activity_date BETWEEN PSC.valid_from AND PSC.valid_to
				AND PS.id_dept = :id_dept
				AND PSC.specific_to = 'B'
				AND P.active = 1
				AND PS.active = 1
				AND PSC.active = 1
				AND PS.on_approved = 1
			)
		) AS product_list
		";
	}
	
	else if ($paid_by == 'Client') 
	{
		$qry_Product_List = " 
		SELECT DISTINCT id_product,product_name FROM
		(
			(
				SELECT 
					P.id_product,
					PS.id_product_service,
					PSC.id_product_service_claim,
					PSC.id_product_service_cost,
					P.id_service_type,
					P.id_product_type,
					P.product_name,
					CONCAT(PS.service_name,' - ', PS.special_name) AS service_name,
					PS.valid_from AS service_from,
					PS.valid_to AS service_to,
					PSC.valid_from AS claim_from,
					PSC.valid_to AS claim_to,
					PSC.specific_to,
					PS.id_dept,
					PS.id_country AS service_country,
					PS.id_coast,
					PS.id_creditor,
					PS.id_tax,
					PS.charge,
					PS.duration,
					PS.transfer_included,
					PS.on_approved,
					PS.on_api,
					PS.description,
					PS.comments,
					PS.is_pakage,
					PS.on_monday,
					PSC.ex_monday,
					PS.on_tuesday,
					PSC.ex_tuesday,
					PS.on_wednesday,
					PSC.ex_wednesday,
					PS.on_thursday,
					PSC.ex_thursday,
					PS.on_friday,
					PSC.ex_friday,
					PS.on_saturday,
					PSC.ex_saturday,
					PS.on_sunday,
					PSC.ex_sunday,
					PS.cancellation,
					PS.for_infant,
					PS.age_inf_from,
					PS.age_inf_to,
					PSC.ps_infant_claim,
					PS.for_child,
					PS.age_child_from,
					PSC.ps_child_claim,
					PS.age_child_to, 
					PS.for_teen,
					PS.age_teen_from,
					PS.age_teen_to,
					PSC.ps_teen_claim,
					PSC.ps_adult_claim,
					PS.min_pax,
					PS.max_pax
				FROM 
					product P,
					product_service PS,
					product_service_claim PSC
				WHERE P.id_product = PS.id_product
				AND PS.id_product_service = PSC.id_product_service
				AND :activity_date BETWEEN PSC.valid_from AND PSC.valid_to
				AND PS.id_dept = :id_dept
				AND PSC.specific_to = 'B'
				AND P.active = 1
				AND PS.active = 1
				AND PSC.active = 1
				AND PS.on_approved = 1
			)
		) AS product_list";
	}
   //$qry_Product_List .= " "."WHERE on_thursday = 1 AND ex_thursday = 0"; 

	if ($activity_day == 'Sun')
	{
		$qry_Product_List .= " "."WHERE on_sunday = 1 AND ex_sunday = 0"; 
	}
	else if ($activity_day == 'Mon')
	{
		$qry_Product_List .= " "."WHERE on_monday = 1 AND ex_monday = 0"; 
	}
	else if ($activity_day == 'Tue')
	{
		$qry_Product_List .= " "."WHERE on_tuesday = 1 AND ex_tuesday = 0"; 
	}
	else if ($activity_day == 'Wed')
	{
		$qry_Product_List .= " "."WHERE on_wednesday = 1 AND ex_wednesday = 0"; 
	}
	else if ($activity_day == 'Thu')
	{
		$qry_Product_List .= " "."WHERE on_thursday = 1 AND ex_thursday = 0"; 
	}
	else if ($activity_day == 'Fri')
	{
		$qry_Product_List .= " "."WHERE on_friday = 1 AND ex_friday = 0"; 
	}
	else if ($activity_day == 'Sat')
	{
		$qry_Product_List .= " "."WHERE on_saturday = 1 AND ex_saturday = 0"; 
	}
	
	$qry_activityProduct = $con->prepare($qry_Product_List);
	
	if ($paid_by == 'TO') 
	{
		$qry_activityProduct->execute(array(":activity_date"=>$activity_date, ":id_dept"=>$id_dept, ":id_country"=>$id_country, ":payer"=>$payer));
	}
	else if ($paid_by == 'Client') 
	{
		$qry_activityProduct->execute(array(":activity_date"=>$activity_date, ":id_dept"=>$id_dept));
	} 
	
	$row_count_p = $qry_activityProduct->rowCount();

	if ($row_count_p > 0) 
	{
		while ($row = $qry_activityProduct->fetch(PDO::FETCH_ASSOC)) {
			$productList[] = array(
				'id_product'	=> $row['id_product'],
				'product_name'	=> $row['product_name']
			);
        }    
        $myData = $productList;
        echo json_encode($myData);
    } 
	else 
	{
		$productList[] = array(
				'id_product'	=> '0',
				'product_name'	=> 'NONE'
			);
		
        $myData = $productList;
        echo json_encode($myData);
    }
	
	
	
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

