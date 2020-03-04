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
    $service_name = trim($_POST["transfer_type"]);
    $special_name = trim($_POST["transfer_port"]);

    $con = pdo_con();
	
    // Booking Details
    $qry_bookingDetails = $con->prepare("
		SELECT * 
        FROM booking 
        WHERE id_booking = :id_booking 
        AND id_tour_operator = :id_tour_operator
        AND active =1");

	$qry_bookingDetails->execute(array(":id_booking"=>$id_booking, ":id_tour_operator"=>$_SESSION["id_tour_operator"]));

	$row_count_bookingDetails = $qry_bookingDetails->rowCount();
	
	if ($row_count_bookingDetails > 0) 
	{
		while ($row = $qry_bookingDetails->fetch(PDO::FETCH_ASSOC))
		{
			$booking_from = $row["booking_from"];
		}
	}
    
    if($service_name != 'INTER HOTEL')
    {
        $qry_transferDetails = $con->prepare("
            SELECT DISTINCT P.*
            FROM 
                product P,
                product_service PS
            WHERE P.id_product = PS.id_product
            AND P.id_service_type = 3
            AND PS.special_name = :special_name
            AND PS.valid_to >= :booking_from
            AND P.active =1
            AND PS.active =1");

        $qry_transferDetails->execute(array(":special_name"=>$special_name,":booking_from"=>$booking_from));
    }
    else
    {
        $qry_transferDetails = $con->prepare("
            SELECT DISTINCT P.*
            FROM 
                product P,
                product_service PS
            WHERE P.id_product = PS.id_product
            AND P.id_service_type = 3
            AND PS.service_name = :service_name
            AND PS.valid_to >= :booking_from
            AND P.active =1
            AND PS.active =1");

        $qry_transferDetails->execute(array(":service_name"=>$service_name,":booking_from"=>$booking_from));
        
    }

	$row_count_transferDetails = $qry_transferDetails->rowCount();
	
	if ($row_count_transferDetails > 0) 
	{
		while ($row = $qry_transferDetails->fetch(PDO::FETCH_ASSOC)) {
			$transferList[] = array(
				'id_product'	=> $row['id_product'],
				'product_name'	=> $row['product_name']
			);
        }    
        $myData = $transferList;
        echo json_encode($myData);
    } 
	else 
	{
		$transferList[] = array(
				'id_product'	=> '0',
				'product_name'	=> 'NONE'
			);
        $myData = $transferList;
        echo json_encode($myData);
    }
	
	
	
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

