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

$sql = "SELECT C.*, O.countrycode_3,U.currency_code
        FROM tblcompanies C
        LEFT JOIN tblcountries O on C.countryfk = O.id
        LEFT JOIN tblcurrency U on C.defaultcurrencyfk = U.id
        ORDER BY C.companyname ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql, "id", "companyname,countryfk,defaultcurrencyfk,email,address,brn,vatno,website,phone,fax,description,countrycode_3,currency_code");
?>