<?php

try {
    
    $api_name = "api_hotels";
    
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
    
    $sql = "select * from tblhotels where deleted = 0 order by hotelname;";

    $array_data = utilities_render_query($con, $sql, "id", 
            "hotelname,hoteltypefk,groupfk,description,phy_address,phy_address2,phy_city,
            phy_postcode,phy_countryfk,areafk,coastfk,lat,lon,active,rating", 
            "hotelname,hoteltype_id,hotelgroup_id,description,address,address2,city,
            postcode,country_id,area_id,coast_id,lat,lon,active,rating", array());
    
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}


?>