<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if(!isset($_GET["t"]))
{
    die("INVALID TOKEN");
}

if($_GET["t"] != $_SESSION["token"])
{
    die("INVALID TOKEN");
}

    
require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con=pdo_con();

$menuid = $_GET["menuid"];
$gprid = $_GET["gprid"];

$sql = "SELECT gp.prcsfk AS id, 0 AS X, fp.processname, fp.processdescription, fp.menuid
        FROM tblgrpprcsrights gp 
        INNER JOIN tblmenuprocess fp on gp.prcsfk = fp.id
        WHERE fp.menuid= $menuid AND gp.gprfk = $gprid
        order by fp.processname ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql,"id","X,menuid,processname,processdescription");


?>