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

$query_c = $con->prepare("SELECT tmc.id, tmc.marketfk, tmc.countryfk, tc.country_name, tm.market_name, tc.id, tc.continent, tci.continent 
FROM tblmarket_countries tmc 
join tblcountries tc on tmc.countryfk = tc.id 
join tblmarkets tm on tmc.marketfk = tm.id 
join tblcontinents tci on tc.continent = tci.continent_code
ORDER BY `tmc`.`marketfk` ASC");
$query_c->execute();
$row_count_c = $query_c->rowCount();

if ($row_count_c > 0) {
    while ($row = $query_c->fetch(PDO::FETCH_ASSOC)) {
        $market[] = array(
            'marketfk' => $row['marketfk'],
            'market_name' => $row['market_name'],
            'id' => $row['id'],
            'country_name' => $row['country_name'], 
            'countryfk' => $row['countryfk'], 
            'countryId' => $row['id'],
            'continent' => $row['continent']
        );
    }
    $myData = $market;
    echo json_encode($myData);
} 

else {
    echo "NO DATA";
}
?>