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

if (!isset($_GET["selected_id"])) {
    die("MISSING TO ID");
}

require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();

$sql = "SELECT id, toname FROM tbltouroperator WHERE active=1 "
     . "OR id=:id ORDER BY toname ASC";
$query_c = $con->prepare($sql);
$query_c->execute(array(":id"=>$_GET["selected_id"]));
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