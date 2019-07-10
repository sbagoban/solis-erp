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

$sql = "SELECT tc.*, cd.deptcode, cd.deptdescription
        FROM tblhotel_contacts tc
        LEFT JOIN tblcontact_departments cd on tc.deptfk = cd.id
        WHERE hotelfk=$hotelfk
        ORDER BY cd.deptcode ASC";

$data->render_sql($sql, "id", "deptcode,deptdescription,contact_name,contact_phone,
                               contact_mobile,contact_fax,contact_email,
                               contact_webaddress,dept_default,deptfk");
?>
