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

$sqlMeal = $con->prepare("SELECT * 
                                            FROM tblmealplans
                                            WHERE usedasboardbasis = 1
                                            ORDER BY meal");
$sqlMeal->execute();
$row_count_meal = $sqlMeal->rowCount();

if ($row_count_meal > 0) {
    while ($row = $sqlMeal->fetch(PDO::FETCH_ASSOC)) {
        $mealDetails[] = array(
            'id'	=> $row['id'],
            'meal'	=> $row['meal'],
            'meal_full_name'	=> $row['mealfullname']
        );
    }    $myData = $mealDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $mealDetails[] = array(
            'id'	=> '-',
            'meal'	=> '-',
            'meal_full_name'	=> '-'
        );
    $myData = $mealDetails;
    echo json_encode($myData);
}
