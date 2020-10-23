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

$sql = "select *, 0 as X
        from tblfacilities where category = 'HOTEL' and 
        deleted = 0 and id not in (select facilityfk from tblhotel_facilities where hotelfk = $hotelfk)
        order by ordering asc, facility asc;";

$data->render_sql($sql, "id", "X,category,facility,description");
?>
