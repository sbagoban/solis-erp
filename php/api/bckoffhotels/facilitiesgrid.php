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

if (!isset($_GET["hoid"])) {
    die("INVALID HOTEL ID");
}

$hotelfk = $_GET["hoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$data = new JSONDataConnector($con, "PDO");

$sql = "select hf.*, f.facility,f.ordering,f.description from 
        tblhotel_facilities hf
        inner join tblfacilities f on hf.facilityfk = f.id
        where hf.hotelfk = $hotelfk and f.deleted = 0
        order by f.ordering, f.facility";

$data->render_sql($sql, "id", "hotelfk,facilityfk,ordering,description,facility");

?>
