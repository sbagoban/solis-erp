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
	
    if (!isset($_GET["id_service"])) {
        throw new Exception("INVALID ID". $_GET["id_service"]);
    }
    
    $id_service = $_GET["id_service"];
	
    require_once("../../php/connector/pdo_connect_main.php"); 

    $con = pdo_con();

	$qry_productService = $con->prepare("
		SELECT PS.*
		FROM product_service PS
		WHERE PS.id_product_service =:id_service
		AND active = 1");
	$qry_productService->execute(array(":id_service"=>$id_service));

		$row_count_p = $qry_productService->rowCount();

		if ($row_count_p > 0) 
		{
			while ($row = $qry_productService->fetch(PDO::FETCH_ASSOC)) {
				$productService[] = array(
					'id_product_service'		=> $row['id_product_service'],
					'id_service_type'			=> $row['id_service_type'],
					'id_product_type'			=> $row['id_product_type'],
					'valid_from'				=> $row['valid_from'],
					'valid_to'					=> $row['valid_to'],
					'id_dept'					=> $row['id_dept'],
					'deptname'					=> $row['deptname'],
					'id_country'				=> $row['id_country'],
					'id_coast'					=> $row['id_coast'],
					'service_name'				=> $row['service_name'],
					'special_name'				=> $row['special_name'],
					'id_creditor'				=> $row['id_creditor'],
					'id_tax'					=> $row['id_tax'],
					'charge'					=> $row['charge'],
					'duration'					=> $row['duration'],
					'transfer_included'			=> $row['transfer_included'],
					'description'				=> $row['description'],
					'for_infant'				=> $row['for_infant'],
					'age_inf_from'				=> $row['age_inf_from'],
					'age_inf_to'				=> $row['age_inf_to'],
					'for_child'					=> $row['for_child'],
					'age_child_from'			=> $row['age_child_from'],
					'age_child_to'				=> $row['age_child_to'],
					'for_teen'					=> $row['for_teen'],
					'age_teen_from'				=> $row['age_teen_from'],
					'age_teen_to'				=> $row['age_teen_to'],
					'for_adult'					=> $row['for_adult'],
					'min_age'					=> $row['min_age'],
					'max_age'					=> $row['max_age'],
					'min_pax'					=> $row['min_pax'],
					'max_pax'					=> $row['max_pax']
				);
			}    
			$myData = $productService;
			echo json_encode($myData);
		} 
		else
		{
			$productService[] = array('id_product_service' => 0,'service_name' => 0);
			$myData = $productService;
			echo json_encode($myData);
		}
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

