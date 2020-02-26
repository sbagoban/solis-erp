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
    
    if (!isset($_GET["id_booking_client"])) {
        throw new Exception("INVALID ID". $_GET["id_booking_client"]);
    }
    
    $id_booking_client = $_GET["id_booking_client"];
    
    require_once("../../php/connector/pdo_connect_main.php");

    $con = pdo_con();
    $stmt = $con->prepare("SELECT 
								BC.id_booking_client,
								C.id_client,
								C.type,
								C.is_vip,
								C.title,
								C.surname,
								C.other_name,
								C.client_dob,
								BC.age,
								BC.yearMonth,
								C.passport_no,
								BC.id_booking,
								BC.id_quote,
								BC.remarks
							FROM booking_client BC, CLIENT C
							WHERE BC.id_client = C.id_client
							AND BC.id_booking_client = :id_booking_client
							AND BC.active = 1
							AND C.active = 1");
    $stmt->execute(array(":id_booking_client"=>$id_booking_client));
    $row_count_c = $stmt->rowCount();

    if ($row_count_c > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dossier[] = array(
				"id_booking_client" => $row['id_booking_client'],
				"id_client" => $row["id_client"],
				"type" => $row["type"],
				"is_vip" => $row["is_vip"],
				"title" => $row["title"],
				"surname" => $row["surname"],
				"other_name" =>  $row["other_name"],
				"client_dob" => $row["client_dob"],
				"age" => $row["age"],
				"yearMonth" => $row["yearMonth"],
				"passport_no" => $row["passport_no"],
				"id_booking" => $row["id_booking"],
				"id_quote" => $row["id_quote"],
				"remarks" => $row["remarks"],
                "OUTCOME" => 'OK'
            );
        }    
        $myData = $dossier;
        echo json_encode($myData);
    } else {
        //echo "NO DATA";    
        $dossier[] = array(
				"id_booking_client" => "-",
				"id_client" => "-",
				"type" => "-",
				"is_vip" => "-",
				"title" => "-",
				"surname" => "-",
				"other_name" => "-",
				"client_dob" => "-",
				"age" => "-",
				"yearMonth" => "-",
				"passport_no" => "-",
				"id_booking" => "-",
				"id_quote" => "-",
				"remarks" => "-",
                "OUTCOME" => 'ERROR'
        );
    }
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>