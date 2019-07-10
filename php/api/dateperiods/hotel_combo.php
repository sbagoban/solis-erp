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

if (!isset($_GET["hid"])) {
    die("INVALID HOTEL ID");
}
$id = $_GET["hid"];

require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();

$query_c = $con->prepare("SELECT h.id, h.hotelname, 
                          IFNULL(h.groupfk,-1) AS groupfk, gh.grpname
                          FROM tblhotels h left join
                          tblgrouphotels gh on h.groupfk = gh.id 
                          WHERE h.active = 1 AND h.id=:id
                          ORDER BY h.hotelname ASC");

$query_c->execute(array(":id"=>$id));

$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'value' => $row['id'],
            'text' => $row['hotelname'],
            'groupfk' => $row['groupfk'],
            'grpname' => $row['grpname']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>