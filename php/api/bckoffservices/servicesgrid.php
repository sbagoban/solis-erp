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

$query_c = $con->prepare("
select es.id, es.optioncode, es.descriptionservice, es.comments, es.services_notes, c.country_name, s.suppliername, st.servicetype
from tblexcursion_services es 
join tblcountries c on es.countryfk = c.id
join tblservicetype st on es.servicetypefk = st.id
join tblsuppliesexcursions s on es.supplierfk = s.id
order by es.id desc
");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $excursionservices[] = array(
            'id'                 => $row['id'],
            'countryfk'          => $row['country_name'],
            'servicetypefk'      => $row['servicetype'],
            'supplierfk'         => $row['suppliername'],
            'optioncode'         => $row['optioncode'],
            'descriptionservice' => $row['descriptionservice'],
            'comments'           => $row['comments'],
            'services_notes'     => $row['services_notes']
        );
    }
    $myData = $excursionservices;
    echo json_encode($myData);
} else {
    echo "NO DATA";
}
?>
