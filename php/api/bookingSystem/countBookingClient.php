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
    
    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }
    
    if (!isset($_GET["t"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_GET["t"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }
    
    if (!isset($_GET["id_booking"])) {
        throw new Exception("INVALID ID". $_GET["id_booking"]);
    }
    
    $id_booking = $_GET["id_booking"];
    
    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");
    
    $con = pdo_con();
	$dossier= array();
	
	$stmt = $con->prepare("SELECT * FROM booking WHERE id_booking = :id_booking AND active = 1");
	$stmt->execute(array(":id_booking"=>$id_booking));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		$dossier['id_booking'] = $row["id_booking"];
		$dossier['adult_amt'] = $row["adult_amt"];
		$dossier['teen_amt'] = $row["teen_amt"];
		$dossier['child_amt'] = $row["child_amt"];
		$dossier['infant_amt'] = $row["infant_amt"];
	}   
	
   $stmt = $con->prepare("	SELECT COUNT(*) AS client_adult
							FROM booking_client BC, client C
							WHERE BC.id_client = C.id_client
							AND id_booking = :id_booking
							AND C.type = 'ADULT' 
							AND BC.active = 1");
    $stmt->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $stmt->rowCount();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$dossier['client_adult'] = $row['client_adult'];
	}    
	
    $stmt = $con->prepare("	SELECT COUNT(*) AS client_teen
							FROM booking_client BC, client C
							WHERE BC.id_client = C.id_client
							AND id_booking = :id_booking
							AND C.type = 'TEEN' 
							AND BC.active = 1");
    $stmt->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $stmt->rowCount();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$dossier['client_teen'] = $row['client_teen'];
	}     
	
    $stmt = $con->prepare("	SELECT COUNT(*) AS client_child
							FROM booking_client BC, client C
							WHERE BC.id_client = C.id_client
							AND id_booking = :id_booking
							AND C.type = 'CHILD' 
							AND BC.active = 1");
    $stmt->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $stmt->rowCount();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$dossier['client_child'] = $row['client_child'];
	} 
	 
    $stmt = $con->prepare("	SELECT COUNT(*) AS client_infant
							FROM booking_client BC, client C
							WHERE BC.id_client = C.id_client
							AND id_booking = :id_booking
							AND C.type = 'INFANT' 
							AND BC.active = 1");
    $stmt->execute(array(":id_booking"=>$id_booking));
    $row_count_c = $stmt->rowCount();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$dossier['client_infant'] = $row['client_infant'];
	}   
	
	$myData = $dossier;
	echo json_encode($myData);
   
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

