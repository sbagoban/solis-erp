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
                                                                BA_CLAIM.id_booking_activity_claim,
                                                                BA_CLAIM.id_booking,
                                                                BA_CLAIM.id_product,
                                                                P.product_name,
                                                                BA_CLAIM.id_product_service,
                                                                PS.service_name,
                                                                BA_CLAIM.activity_date,
                                                                BA_CLAIM.activity_rebate_type,
                                                                BA_CLAIM.activity_total_claim_after_rebate,
                                                                C.currency_code
                                                            FROM 
                                                                booking_activity_claim BA_CLAIM,
                                                                product P,
                                                                product_service PS,
                                                                tblcurrency C
                                                            WHERE BA_CLAIM.id_product = P.id_product
                                                            AND BA_CLAIM.id_product_service = PS.id_product_service
                                                            AND BA_CLAIM.id_product_service_claim_cur = C.id
                                                            AND BA_CLAIM.id_booking = :id_booking
                                                            AND BA_CLAIM.active = 1");
$sqlBookingActivity->execute(array(":id_booking"=>$id_booking));
$row_count_c = $sqlBookingActivity->rowCount();

if ($row_count_c > 0) {
    while ($row = $sqlBookingActivity->fetch(PDO::FETCH_ASSOC)) {
        $bookingActivityDetails[] = array(
            'id_booking_activity_claim'   => $row['id_booking_activity_claim'],
            'id_booking'   => $row['id_booking'],
            'id_product'    => $row['id_product'],
            'product_name'  => $row['product_name'],
            'id_product_service'    => $row['id_product_service'],
            'service_name'  => $row['service_name'],
            'activity_date' => $row['activity_date'],
            'activity_rebate_type'  => $row['activity_rebate_type'],
            'activity_total_claim_after_rebate'   => $row['activity_total_claim_after_rebate'],
            'currency_code' => $row['currency_code']
        );
    }    $myData = $bookingActivityDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $bookingActivityDetails[] = array(
            'id_booking_activity_claim'   => '-',
            'id_booking'   =>  '-',
            'id_product'    => '-',
            'product_name'  => '-',
            'id_product_service'    => '-',
            'service_name'  => '-',
            'activity_date' => '-',
            'activity_rebate_type'  => '-',
            'activity_total_claim_after_rebate'   => '-',
            'currency_code' => '-'
    );
    $myData = $bookingActivityDetails;
    echo json_encode($myData);
}
