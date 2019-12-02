<?php
/*Date : 2019, 2 October
Use : Get all country
Developer : slouis@solis360.com*/

session_start();

if (!isset($_SESSION["solis_userid"])) 
{
	die("NO LOG IN!");
}

if (!isset($_GET["t"])) 
{
	die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) 
{
	die("INVALID TOKEN");
}

require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();
$query_country = $con->prepare("
	SELECT *
	FROM tblcountries
");
$query_country->execute();
$row_count_country = $query_country->rowCount();

if ($row_count_country > 0) {
    while ($row = $query_country->fetch(PDO::FETCH_ASSOC)) {
        $country[] = array(
            'id_country' => $row['id'],
            'country_name' => $row['country_name']
        );
    }
    $myData = $country;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>