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
$grpid = $_GET["grpid"];

//display all processes for that menu that are not already allocated to grpid

$sql = "SELECT *, 0 AS X 
        FROM tblmenuprocess 
        WHERE menuid= $menuid 
        AND id NOT IN (SELECT prcsfk FROM tblgrpprcsrights WHERE gprfk=$grpid) 
        order by processname ASC";

$data = new JSONDataConnector($con, "PDO");

$data->render_sql($sql,"id","X,menuid,processname,processdescription");


?>