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

$sql = "SELECT * FROM tblcurrency ORDER BY currency_code; ";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "currency_code,currency_name,use_for_costprice,use_for_base_conversions");
?>