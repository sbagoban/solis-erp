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

$sql = "SELECT tc.*, t.txcode, t.txdescription
        FROM tblhotel_commission tc
        LEFT JOIN tbltaxcodes t on tc.taxcode_fk = t.id
        WHERE hotelfk=$hotelfk
        ORDER BY tc.dtto desc, tc.dtfrom desc";

$data->render_sql($sql, "id", "hotelfk,taxcode_fk,taxamt,commission,markup,dtfrom,dtto");
?>
