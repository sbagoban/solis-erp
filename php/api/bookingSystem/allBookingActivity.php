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

$sqlBookingActivity = $con->prepare("SELECT 
                                                                BA.id_booking_activity,
                                                                BA.id_booking,
                                                                BA.id_product,
                                                                P.product_name,
                                                                BA.id_product_service,
                                                                PS.service_name,
                                                                BA.activity_date,
                                                                BA.activity_rebate_type,
                                                                BA.activity_total_claim_after_disc,
                                                                C.currency_code
                                                            FROM 
                                                                booking_activity BA,
                                                                product P,
                                                                product_service PS,
                                                                tblcurrency C
                                                            WHERE BA.id_product = P.id_product
                                                            AND BA.id_product_service = PS.id_product_service
                                                            AND BA.id_product_service_claim_cur = C.id
                                                            AND BA.id_booking = :id_booking
                                                            AND BA.active = 1");
$sqlBookingActivity->execute(array(":id_booking"=>$id_booking));
$row_count_c = $sqlBookingActivity->rowCount();

if ($row_count_c > 0) {
    while ($row = $sqlBookingActivity->fetch(PDO::FETCH_ASSOC)) {
        $bookingActivityDetails[] = array(
            'id_booking_activity'   => $row['id_booking_activity'],
            'id_booking'   => $row['id_booking'],
            'id_product'    => $row['id_product'],
            'product_name'  => $row['product_name'],
            'id_product_service'    => $row['id_product_service'],
            'service_name'  => $row['service_name'],
            'activity_date' => $row['activity_date'],
            'activity_rebate_type'  => $row['activity_rebate_type'],
            'activity_total_claim_after_disc'   => $row['activity_total_claim_after_disc'],
            'currency_code' => $row['currency_code']
        );
    }    $myData = $bookingActivityDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $bookingActivityDetails[] = array(
            'id_booking_activity'   => '-',
            'id_booking'   =>  '-',
            'id_product'    => '-',
            'product_name'  => '-',
            'id_product_service'    => '-',
            'service_name'  => '-',
            'activity_date' => '-',
            'activity_rebate_type'  => '-',
            'activity_total_claim_after_disc'   => '-',
            'currency_code' => '-'
    );
    $myData = $bookingActivityDetails;
    echo json_encode($myData);
}
