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

if (!isset($_GET["cid"])) {
    die("INVALID RATE ID");
}

$ratefk = $_GET["cid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "SELECT id,ratefk,
        DATE_FORMAT(dtfrom,'%d-%m-%Y') AS dtfrom,
        DATE_FORMAT(dtto,'%d-%m-%Y') AS dtto,
        DATE_FORMAT(dtfrom,'%d %M %Y') AS dtfrom_disp,
        DATE_FORMAT(dtto,'%d %M %Y') AS dtto_disp,
        exchange_rate
        FROM tblexchangerates_periods WHERE ratefk=$ratefk ORDER BY dtto DESC; ";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "ratefk,dtfrom,dtto,exchange_rate,dtfrom_disp,dtto_disp");
?>