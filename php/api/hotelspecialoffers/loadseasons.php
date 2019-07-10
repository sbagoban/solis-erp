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

if (!isset($_GET["hid"])) {
    die("INVALID HOTEL ID");
}

$hotelid = $_GET["hid"];


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "select dp.*,s.season,s.scode,
        DATE_FORMAT(dp.checkin,'%d-%m-%Y') AS checkin_dmy,
        DATE_FORMAT(dp.checkout,'%d-%m-%Y') AS checkout_dmy
        from tbldateperiods dp 
        INNER JOIN tblseasons s on dp.seasonfk = s.id
        where dp.hotelfk = $hotelid and active = 1 
        order by dp.checkin DESC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "checkin,checkout,season,scode,seasonfk,checkin_dmy,checkout_dmy");
?>