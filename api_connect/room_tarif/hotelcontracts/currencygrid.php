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

if (!isset($_GET["hid"])) {
    die("INVALID HOTEL ID");
}

$hid = $_GET["hid"];


require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "
        SELECT A.id, A.X, A.currency_code as value from
        (
        select c.id, 0 AS X, currency_code 
        from tblhotel_currency hc
        inner join tblcurrency c on hc.currencyid = c.id
        where hc.hotelfk = $hid


        UNION 

        select c.id, 0 AS X, currency_code 
        from tblcurrency c WHERE use_for_costprice = 1
        )
        A
        order by value";

        
$data = new JSONDataConnector($con, "PDO");

$data->render_complex_sql($sql, "id", "X,value");
?>