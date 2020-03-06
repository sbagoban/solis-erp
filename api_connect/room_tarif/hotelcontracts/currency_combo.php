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

$hid = $_GET["hid"];


require_once("../../connector/pdo_connect_main.php");

$con = pdo_con();

$query_c = $con->prepare("select hc.currencyid, c.currency_code, c.currency_name, c.id 
                          from tblhotel_currency hc
                          inner join tblcurrency c on hc.currencyid = c.id
                          where hc.hotelfk = :hotelfk
                          order by currency_code ASC");

$query_c->execute(array(":hotelfk"=>$hid));
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $ug[] = array(
            'value' => $row['id'],
            'text' => $row['currency_code']
        );
    }
    $myData = $ug;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>