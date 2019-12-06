<?php

try {
    
    $api_name = "api_product_service_extra";
    
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

    //check for extra parameters
    if(!isset($_POST["id_product_service"]))
    {
        throw new Exception("ERR_NO_PRODUCT_SERVICE_ID");
    }
    
    $id_product_service = $_POST["id_product_service"];

    $sql = "select * 
    from product_service_extra 
    where id_product_service = :id_product_service 
    and active = 1
    order by id_product_service;";

    $array_data = utilities_render_query($con, $sql, "id_product_service_extra", "id_service_extra",
    "extra_name", "id_product_service", "extra_description", "charge",
    array(":id_product_service"=>$id_product_service));
    
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}
?>