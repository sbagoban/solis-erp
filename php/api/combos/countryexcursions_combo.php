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

$con = pdo_con();

$query_c = $con->prepare("SELECT * FROM tblcountries where used_for_excursions = 1  ORDER BY country_name ASC");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'value' => $row['id'],
            'text' => $row['country_name'],
            'length_two' => $row['countrycode_2'],
            'length_three' => $row['countrycode_3']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>