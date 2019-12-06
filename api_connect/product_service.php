<?php

try {
    
    $api_name = "api_product_service";
    
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
    
    $sql = "select * from product_service 
    where active = 1
    order by id_product_service;";

    $array_data = utilities_render_query($con, $sql, "id_product_service", "id_product", "id_service_type", "id_product_type", "valid_from", 
    "valid_to", "id_dept", "deptname", "id_country", "id_coast", "service_name", "special_name", "id_creditor", "id_tax", "tx_code",
    "charge", "duration", "transfer_included", "on_web", "on_api", "description", "comments", "is_pakage", "on_monday", "on_tuesday", "on_wednesday",
    "on_thursday", "on_friday", "on_saturday", "on_sunday", "cancellation", "for_infant", "for_child", "for_teen", "age_inf_from", "age_inf_to", 
    "age_child_from", "age_child_to", "age_teen_from", "age_teen_to", "min_pax", "max_pax", 
    array());
    
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}


?>