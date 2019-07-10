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

$countryfk = $_GET["countryfk"];

$query_c = $con->prepare("SELECT id, toname FROM tbltouroperator WHERE active=1 AND id IN (SELECT tofk FROM tblto_countries WHERE countryfk = :countryfk)  ORDER BY toname ASC");
$query_c->execute(array(":countryfk"=>$countryfk));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'value' => $row['id'],
            'text' => $row['toname']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>