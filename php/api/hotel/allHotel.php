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

$sqlHotel = $con->prepare("SELECT 
										H.id,
										H.hotelname,
										HG.grpname,
										H.description,
										H.phy_address, 
										H.phy_address2, 
										H.phy_city, 
										H.phy_postcode, 
										C.country_name,
										A.areaname,
										COAST.coast,
										H.website,
										H.ratecode,
										H.specialratecode,
										H.property_name,
										H.company_name
									FROM 
										tblhotels H,
										tblgrouphotels HG,
										tblcountries C,
										tblareas A,
										tblcoasts COAST
									WHERE H.groupfk = HG.id 
									AND H.phy_countryfk = C.id
									AND H.areafk = A.id
									AND H.coastfk = COAST.id
									AND H.deleted = 0");
$sqlHotel->execute();
$row_count_hotel = $sqlHotel->rowCount();

if ($row_count_hotel > 0) {
    while ($row = $sqlHotel->fetch(PDO::FETCH_ASSOC)) {
        $hotelDetails[] = array(
            'id'	=> $row['id'],
            'hotelname'	=> $row['hotelname'],
            'grpname'	=> $row['grpname'],
            'description'	=> $row['description'],
            'phy_address'	=> $row['phy_address'],
            'phy_address2'	=> $row['phy_address2'],
            'phy_city'	=> $row['phy_city'],
            'phy_postcode'	=> $row['phy_postcode'],
            'country_name'	=> $row['country_name'],
            'areaname'	=> $row['areaname'],
            'coast'	=> $row['coast'],
            'website'	=> $row['website'],
            'ratecode'	=> $row['ratecode'],
            'specialratecode'	=> $row['specialratecode'],
            'property_name'	=> $row['property_name'],
            'company_name'	=> $row['company_name']
        );
    }    $myData = $hotelDetails;
    echo json_encode($myData);
} else {
    //echo "NO DATA";    
    $hotelDetails[] = array(
            'id'	=> '-',
            'hotelname'	=> '-',
            'grpname'	=> '-',
            'description'	=> '-',
            'phy_address'	=> '-',
            'phy_address2'	=> '-',
            'phy_city'	=> '-',
            'phy_postcode'	=> '-',
            'country_name'	=> '-',
            'areaname'	=> '-',
            'coast'	=> '-',
            'website'	=> '-',
            'ratecode'	=> '-',
            'specialratecode'	=> '-',
            'property_name'	=> '-',
            'company_name'	=> '-'
        );
    $myData = $hotelDetails;
    echo json_encode($myData);
}
