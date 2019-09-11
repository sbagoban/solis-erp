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

if (!isset($_GET["phy_countryfk"])) {
    throw new Exception("INVALID ID". $_GET["phy_countryfk"]);
}

$phy_countryfk = $_GET["phy_countryfk"];

require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();
// To add Where Active 1 in sql query ???
$query_c = $con->prepare("SELECT id, toname
FROM tbltouroperator where phy_countryfk IN ( " . $phy_countryfk . "  )");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $countries[] = array(
            'id' => $row['id'],
            'toname' => $row['toname']
        );
    }
    $myData = $countries;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>