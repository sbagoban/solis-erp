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

if (!isset($_GET["vid"])) {
    die("INVALID VEHICLE ID");
}

$vid = $_GET["vid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$data = new JSONDataConnector($con, "PDO");

$sql = "SELECT *
        FROM tblvehicletype_luggage WHERE vehtypefk=$vid
        ORDER BY suitcase ASC, handbag ASC, golfbag ASC";

$data->render_sql($sql, "id", "vehtypefk,suitcase,handbag,golfbag");
?>
