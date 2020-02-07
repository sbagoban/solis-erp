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

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sqlDiscountUser = $con->prepare("SELECT *
											FROM tbluser");
$sqlDiscountUser->execute();
$row_count_DiscountUser = $sqlDiscountUser->rowCount();

if ($row_count_DiscountUser > 0) {
    while ($row = $sqlDiscountUser->fetch(PDO::FETCH_ASSOC)) {
        $discountUserDetails[] = array(
            'id_user'	=> $row['id'],
            'full_name'	=> $row['ufullname']
        );
    }    $myData = $discountUserDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $discountUserDetails[] = array(
            'id_user'	=> '-',
            'full_name'	=> '-'
        );
    $myData = $discountUserDetails;
    echo json_encode($myData);
}
