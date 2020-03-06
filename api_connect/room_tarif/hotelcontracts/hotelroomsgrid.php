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

$sql = "select *, 0 AS X, roomname AS value from tblhotel_rooms where hotelfk = $hotelid order by roomname asc";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "X,value,roomname,description,numbedrooms");
?>