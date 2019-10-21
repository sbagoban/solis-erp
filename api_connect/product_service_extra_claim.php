<?php

try {
    
    $api_name = "api_product_service_extra_claim";
    
    require_once("../php/utils/utilities.php");
    require_once("../php/connector/pdo_connect_main_login_internet.php");
    
    date_default_timezone_set('Indian/Mauritius');
    
    $con = connect_login_pdo();
    
    //authenticate user
    $authenticate_outcome = utils_authenticate_api_user($con, $_POST, $api_name);
    if(!is_array($authenticate_outcome))
    {
        throw new Exception($authenticate_outcome);
    }
    
    //all is valid
    $tofk = $authenticate_outcome["TOID"]; //get the toid of the user

    $sql = "select * from product_service_extra_claim where active=1 order by id_product_service_claim;";

    $array_data = utilities_render_query($con, $sql, 
    "id_product_service_extra_claim","id_product_service_claim","id_product_service","valid_from","valid_to",
    "id_dept","specific_to","charge","ps_adult_claim","ps_teen_claim","ps_child_claim","ps_infant_claim",
    "id_currency","currency", array());     
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}
?>