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
select es.id, es.optioncode, es.descriptionservice, es.comments, es.services_notes,
es.locality_costdetails, es.charged_unit_adults_costdetails, es.min_adults_costdetails, es.max_adults_costdetails, es.charged_unit_children_costdetails, 
es.min_children_costdetails, es.max_children_costdetails, es.taxbasis_costdetails, es.duration_costdetails, es.invoice_desciption_costdetails,
es.address_voucherdetails, es.country_voucherdetails, es.state_voucherdetails, es.postcode_voucherdetails, es.vouchercreation_voucherdetails, 
es.printvoucher_voucherdetails, es.vouchertext1_voucherdetails, es.vouchertext2_voucherdetails, es.vouchertext3_voucherdetails, es.vouchertext4_voucherdetails,
es.settingapplyto_policies, es.pickoffdropoff_policies, es.crossseasonsrates_policies, es.infantmin_policies, es.infantmax_policies, 
es.childmin_policies, es.childmax_policies, es.teenmin_policies, es.teenmax_policies, es.adultmin_policies, 
es.adultmax_policies, es.starton_monday_policies, es.starton_tuesday_policies, es.starton_wednesday_policies, es.starton_thursday_policies, 
es.starton_friday_policies, es.starton_saturday_policies, es.starton_sunday_policies,
es.mustinclude_monday_policies, es.mustinclude_tuesday_policies, es.mustinclude_wednesday_policies, es.mustinclude_thursday_policies, es.mustinclude_friday_policies, 
es.mustinclude_saturday_policies, es.mustinclude_sunday_policies,
c.country_name, s.suppliername, st.servicetype
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
            'services_notes'     => $row['services_notes'],
            'address_voucherdetails' => $row['address_voucherdetails'],
            'country_voucherdetails' => $row['country_voucherdetails'],
            'state_voucherdetails'   => $row['state_voucherdetails'],
            'postcode_voucherdetails' => $row['postcode_voucherdetails'],
            'vouchercreation_voucherdetails' => $row['vouchercreation_voucherdetails'],
            'printvoucher_voucherdetails'   => $row['printvoucher_voucherdetails'],
            'vouchertext1_voucherdetails'   => $row['vouchertext1_voucherdetails'],
            'vouchertext2_voucherdetails'   => $row['vouchertext2_voucherdetails'],
            'vouchertext3_voucherdetails'   => $row['vouchertext3_voucherdetails'],
            'vouchertext4_voucherdetails'   => $row['vouchertext4_voucherdetails'],
            'locality_costdetails' => $row['locality_costdetails'],
            'charged_unit_adults_costdetails' => $row['charged_unit_adults_costdetails'],
            'min_adults_costdetails'   => $row['min_adults_costdetails'],
            'max_adults_costdetails' => $row['max_adults_costdetails'],
            'charged_unit_children_costdetails' => $row['charged_unit_children_costdetails'],
            'min_children_costdetails'   => $row['min_children_costdetails'],
            'max_children_costdetails'   => $row['max_children_costdetails'],
            // 'taxbasis_costdetails'   => $row['taxbasis_costdetails'],
            'duration_costdetails'   => $row['duration_costdetails'],
            'invoice_desciption_costdetails'   => $row['invoice_desciption_costdetails'], 
            'settingapplyto_policies' => $row['settingapplyto_policies'],
            'pickoffdropoff_policies'   => $row['pickoffdropoff_policies'],
            'crossseasonsrates_policies' => $row['crossseasonsrates_policies'],
            'infantmin_policies' => $row['infantmin_policies'],
            'infantmax_policies'   => $row['infantmax_policies'],
            'childmin_policies'   => $row['childmin_policies'],
            'childmax_policies'   => $row['childmax_policies'],
            'teenmin_policies'   => $row['teenmin_policies'],
            'teenmax_policies'   => $row['teenmax_policies'], 
            'adultmin_policies'   => $row['adultmin_policies'],
            'adultmax_policies'   => $row['adultmax_policies'],
            'starton_monday_policies'   => $row['starton_monday_policies'],
            'starton_tuesday_policies'   => $row['starton_tuesday_policies'], 
            'starton_wednesday_policies'   => $row['starton_wednesday_policies'],
            'starton_thursday_policies'   => $row['starton_thursday_policies'], 
            'starton_friday_policies'   => $row['starton_friday_policies'],
            'starton_saturday_policies'   => $row['starton_saturday_policies'], 
            'starton_sunday_policies'   => $row['starton_sunday_policies'],
            'mustinclude_monday_policies'   => $row['mustinclude_monday_policies'],
            'mustinclude_tuesday_policies'   => $row['mustinclude_tuesday_policies'], 
            'mustinclude_wednesday_policies'   => $row['mustinclude_wednesday_policies'],
            'mustinclude_thursday_policies'   => $row['mustinclude_thursday_policies'], 
            'mustinclude_friday_policies'   => $row['mustinclude_friday_policies'],
            'mustinclude_saturday_policies'   => $row['mustinclude_saturday_policies'], 
            'mustinclude_sunday_policies'   => $row['mustinclude_sunday_policies']
        );
    }
    $myData = $excursionservices;
    echo json_encode($myData);
} else {
    //echo "NO DATA";
    
    $excursionservices[] = array(
        'countryfk'          => '-',
        'servicetypefk'      => '-',
        'supplierfk'         => '-',
        'optioncode'         => '-',
        'descriptionservice' => '-',
        'comments'           => '-',
        'services_notes'     => '-',
        'address_voucherdetails' => '-',
        'country_voucherdetails' => '-',
        'state_voucherdetails'   => '-',
        'postcode_voucherdetails' => '-',
        'vouchercreation_voucherdetails' => '-',
        'printvoucher_voucherdetails'   => '-',
        'vouchertext1_voucherdetails'   => '-',
        'vouchertext2_voucherdetails'   => '-',
        'vouchertext3_voucherdetails'   => '-',
        'vouchertext4_voucherdetails'   => '-',
    );
    $myData = $excursionservices;
    echo json_encode($myData);
}
