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

if (!isset($_GET["roomids"])) {
    die("INVALID ROOMS ID");
}

$roomids = $_GET["roomids"];
if($roomids == "")
{
    $roomids = "-1";
}



require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "select * from tblhotel_rooms WHERE id IN ($roomids) order by roomname asc";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "roomname,description,numbedrooms");
?>