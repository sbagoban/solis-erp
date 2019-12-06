<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if (!isset($_GET["t"])) {
    die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) {
    die("INVALID TOKEN");
}

if (!isset($_GET["id_booking"])) {
    throw new Exception("INVALID ID". $_GET["id_booking"]);
}

$id_booking = $_GET["id_booking"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = $con->prepare("SELECT 
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
							FROM booking_client BC, CLIENT C
							WHERE BC.id_client = C.id_client
							AND BC.id_booking = :id_booking
							AND BC.active = 1
							AND C.active = 1");
$sql->execute(array(":id_booking"=>$id_booking));
$row_count_c = $sql->rowCount();

if ($row_count_c > 0) {
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $clientDetails[] = array(
            'id_booking_client'	=> $row['id_booking_client'],
            'id_client'			=> $row['id_client'],
            'type'				=> $row['type'],
            'is_vip'			=> $row['is_vip'],
            'title'				=> $row['title'],
            'surname'    		=> $row['surname'],
            'other_name'    	=> $row['other_name'],
            'client_dob'    	=> $row['client_dob'],
            'age'    			=> $row['age'],
            'yearMonth'    		=> $row['yearMonth'],
            'passport_no'    	=> $row['passport_no'],
            'id_booking'    	=> $row['id_booking'],
            'id_quote'    		=> $row['id_quote'],
            'remarks'    		=> $row['remarks'],
        );
    }    $myData = $clientDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $clientDetails[] = array(
            'id_booking_client'	=> '-',
            'id_client'			=> '-',
            'type'				=> '-',
            'is_vip'			=> '-',
            'title'				=> '-',
            'surname'    		=> '-',
            'other_name'    	=> '-',
            'client_dob'    	=> '-',
            'age'    			=> '-',
            'yearMonth'    		=> '-',
            'passport_no'    	=> '-',
            'id_booking'    	=> '-',
            'id_quote'    		=> '-',
            'remarks'    		=> '-',
    );
    $myData = $clientDetails;
    echo json_encode($myData);
}
