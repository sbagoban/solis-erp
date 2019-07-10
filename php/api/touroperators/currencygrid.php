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

if (!isset($_GET["toid"])) {
    die("INVALID TO ID");
}

$toid = $_GET["toid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$data = new JSONDataConnector($con, "PDO");

$sql = "SELECT tc.id,c.currency_code,tc.currencyid,tc.tax_code,
        tc.terms_code,tc.terms_value,tc.use_default,tc.credit_limit,
        IFNULL(tc.bankfk,'') AS bankfk,
        tc.bankaccount,tc.accountname, IFNULL(b.bankname,'') AS bankname,
        c.currency_code
        FROM tblto_currency tc
        LEFT JOIN tblbanks b on tc.bankfk = b.id
        LEFT JOIN tblcurrency c on tc.currencyid = c.id
        WHERE tofk=$toid
        ORDER BY c.currency_code ASC";

$data->render_sql($sql, "id", "bankname,currency_code,currencyid,tax_code,"
        . "terms_code,terms_value,use_default,credit_limit,bankfk,"
        . "bankaccount,accountname");
?>
