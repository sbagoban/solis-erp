<?php
/*Date : 2019, 2 October
Use : Get all tour operator with operating country
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
$query_tourOperator = $con->prepare("
	SELECT TOP.id, 
	toname,
	country_name
	FROM tbltouroperator TOP, tblcountries C
	WHERE TOP.phy_countryfk = C.id
	AND TOP.active = 1
	");
$query_tourOperator->execute();
$row_count_tourOperator = $query_tourOperator->rowCount();

if ($row_count_tourOperator > 0) {
    while ($row = $query_tourOperator->fetch(PDO::FETCH_ASSOC)) {
        $tourOperator[] = array(
            'id_to' => $row['id'],
            'to_name' => $row['toname'],
            'country_name' => $row['country_name']
			
        );
    }
    $myData = $tourOperator;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>