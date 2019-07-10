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
    die("INVALID COMPANY ID");
}

$cid = $_GET["cid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "SELECT C.*, U.currency_code, B.bankname
        FROM tblcompanyaccounts C
        LEFT JOIN tblcurrency U ON C.currencyfk = U.id
        LEFT JOIN tblbanks B ON C.bankfk = B.id
        WHERE C.companyfk = $cid ORDER BY C.accountno ";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "accountno,iban,bankname,currency_code,bankfk,currencyfk,companyfk");
?>