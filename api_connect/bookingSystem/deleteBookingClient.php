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
    
    if (!isset($_GET["id_booking"])) {
        throw new Exception("INVALID ID". $_GET["id_booking"]);
    }
    
    $id_booking = $_POST["id_booking"];
    $id_client = $_POST["id_client"];
    $id_booking_client = $_POST["id_booking_client"];
	$id_user = $_SESSION["solis_userid"];
	$uname = $_SESSION["solis_username"];
	$log_status = "DELETE";
    
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
            IFNULL(BC.age, '-') AS age,
            BC.yearMonth,
            C.passport_no,
            BC.id_booking,
            BC.id_quote,
            BC.remarks
        FROM booking_client BC, CLIENT C, booking B
        WHERE BC.id_client = C.id_client
        AND BC.id_booking = B.id_booking
        AND BC.id_booking_client = :id_booking_client
        AND C.id_client = :id_client
        AND BC.id_booking = :id_booking
        AND B.id_tour_operator = :id_tour_operator
        AND BC.active = 1
        AND C.active = 1");
	$stmt->execute(array(":id_booking_client"=>$id_booking_client, ":id_client"=>$id_client,":id_booking"=>$id_booking, ":id_tour_operator"=>$_SESSION["id_tour_operator"]));
	
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
            $id_booking_client = $row["id_booking_client"];
            $id_client = $row["id_client"];
            $type = $row["type"];
            $is_vip = $row["is_vip"];
            $title = $row["title"];
            $surname = $row["surname"];
            $other_name = $row["other_name"];
            $client_dob = $row["client_dob"];
            $age = $row["age"];
            $yearMonth = $row["yearMonth"];
            $passport_no = $row["passport_no"];
            $id_booking = $row["id_booking"];
            $id_quote = $row["id_quote"];
            $remarks = $row["remarks"];
	}  
	
    $stmt = $con->prepare("UPDATE booking_client SET active=0 WHERE id_booking_client = :id_booking_client");
    $stmt->execute(array(":id_booking_client"=>$id_booking_client));

    // BOOKING CLIENT LOG

    $sqlLog = "INSERT INTO booking_client_log
            (id_booking_client,
            id_client,
            id_booking,
            id_quote,
            age,
            yearMonth,
            remarks,
            id_user,
            uname,
            log_status) 
            VALUES (
            :id_booking_client,
            :id_client,
            :id_booking,
            :id_quote,
            :age,
            :yearMonth,
            :remarks,
            :id_user,
            :uname,
            :log_status
            )";


    $stmt = $con->prepare($sqlLog);
                $stmt->execute(array(
                ":id_booking_client" => $id_booking_client,
                ":id_client" => $id_client,
                ":id_booking" => $id_booking,
                ":id_quote" => $id_quote,
                ":age" => $age,
                ":yearMonth" => $yearMonth,
                ":remarks" => $remarks,
                ":id_user" => $id_user,
                ":uname" => $uname,
                ":log_status" => $log_status
            ));

    echo json_encode(array("OUTCOME" => "OK", "id_booking" => $id_booking));
    
} catch (Exception $ex) {
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

?>

