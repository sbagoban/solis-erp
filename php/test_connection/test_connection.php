<?php

session_start();
if (!isset($_SESSION["solis_userid"])) {
    die(json_encode(array("OUTCOME" => "ERR_NO_LOG_IN")));
}

require_once("../connector/pdo_connect_main.php");
require_once("../connector/db_pdo.php");
require_once("../connector/data_connector.php");


//if here, then means connected to server
//then test connection to database

$con = pdo_con();
if (!$con) {
    die(json_encode(array("OUTCOME" => "ERR_NO_DB_CONNECTION")));
}

die(json_encode(array("OUTCOME" => "OK")));
?>