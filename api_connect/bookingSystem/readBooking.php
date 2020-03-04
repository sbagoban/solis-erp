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
    
// // TO BE UPDATED
     $_SESSION["solis_userid"] = 1;
     $_SESSION["id_tour_operator"] = 1;
     $_SESSION["id_country"] = 979;
// // TO BE UPDATED
    
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
    
	require_once("../../php/connector/pdo_connect_main.php");
	require_once("../../php/connector/db_pdo.php");
	require_once("../../php/connector/data_connector.php");

    $con = pdo_con();
    $stmt = $con->prepare("SELECT 
		B.id_booking,
		B.id_quote,
		B.to_ref,
		B.id_tour_operator,
		TOP.toname,
		B.id_country,
		C.country_name,
		B.id_dept,
		DEPT.deptname,
		B.booking_for,
		B.pax_type,
		B.booking_from,
		B.booking_to,
		B.adult_amt,
		B.teen_amt,
		B.child_amt,
		B.infant_amt,
		B.opt_date,
		B.created_by,
		B.created_name,
		B.remarks,
		B.status,
		B.invoice_no,
		B.invoiced_date,
		B.close,
		B.close_on
	FROM booking B, tbltouroperator TOP, tblcountries C, tbldepartments DEPT
	WHERE B.id_tour_operator = TOP.id
	AND B.id_country = C.id
	AND B.id_dept = DEPT .id
	AND B.id_booking = :id_booking
	AND B.id_tour_operator = :id_tour_operator
	AND B.id_country = :id_country
	AND B.active = 1");
    $stmt->execute(array(":id_booking"=>$id_booking, ":id_tour_operator" => $_SESSION["id_tour_operator"], ":id_country" =>$_SESSION["id_country"]));
    $row_count_c = $stmt->rowCount();

    if ($row_count_c > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dossier[] = array(
				"id_booking" => $row['id_booking'],
				"id_quote" => $row["id_quote"],
				"to_ref" => $row["to_ref"],
				"id_tour_operator" => $row["id_tour_operator"],
				"to_name" => $row["toname"],
				"id_country" => $row["id_country"],
				"country_name" => $row["country_name"],
				"id_dept" => $row["id_dept"],
				"deptname" => $row["deptname"],
				"booking_for" => $row["booking_for"],
				"pax_type" =>  $row["pax_type"],
				"booking_from" => $row["booking_from"],
				"booking_to" => $row["booking_to"],
				"adult_amt" => $row["adult_amt"],
				"teen_amt" => $row["teen_amt"],
				"child_amt" => $row["child_amt"],
				"infant_amt" => $row["infant_amt"],
				"opt_date" => $row["opt_date"],
				"created_by" => $row["created_by"],
				"created_name" => $row["created_name"],
				"remarks" => $row["remarks"],
				"status" => $row["status"],
				"invoice_no" => $row["invoice_no"],
				"invoiced_date" => $row["invoiced_date"],
				"close" => $row["close"],
				"close_on" => $row["close_on"],
                "OUTCOME" => 'OK'
            );
        }    
        $myData = $dossier;
        echo json_encode($myData);
    } else {
        //echo "NO DATA";    
        $dossier[] = array(
				"id_booking" => '0',
				"id_quote" =>  '0',
				"to_ref" =>  '0',
				"id_tour_operator" =>  '-',
				"to_name" =>  '-',
				"id_country" =>  '-',
				"country_name" =>  '-',
				"id_dept" =>  '-',
				"deptname" =>  '-',
				"booking_for" =>  '-',
				"pax_type" =>   '-',
				"booking_from" =>  '-',
				"booking_to" =>  '-',
				"adult_amt" =>  '-',
				"teen_amt" =>  '-',
				"child_amt" =>  '-',
				"infant_amt" =>  '-',
				"opt_date" =>  '-',
				"created_by" =>  '-',
				"created_name" =>  '-',
				"remarks" =>  '-',
				"status" =>  '-',
				"invoice_no" =>  '-',
				"invoiced_date" =>  '-',
				"close" =>  '-',
				"close_on" =>  '-',
                "OUTCOME" => 'NO DATA'
            );
        $myData = $dossier;
        echo json_encode($myData);
    }
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>

