<?php

try {
    
    $api_name = "api_hotel_rooms";
    
    require_once("../php/utils/utilities.php");
    require_once("../php/connector/pdo_connect_main_login_internet.php");
    
    date_default_timezone_set('Indian/Mauritius');
    
    $con = connect_login_pdo();
    
    //authenticate user
    $authenticate_outcome = utils_authenticate_api_user($con, $_GET, $api_name);
    if(!is_array($authenticate_outcome))
    {
        throw new Exception($authenticate_outcome);
    }
    
    //all is valid
    $tofk = $authenticate_outcome["TOID"]; //get the toid of the user
    
    
    //check for extra parameters
    if(!isset($_POST["hotel_id"]))
    {
        throw new Exception("ERR_NO_HOTEL_ID");
    }
    
    $hotel_fk = $_POST["hotel_id"];
    
    
    $sql = "select * from tblhotel_rooms where hotelfk = :hotelid order by roomname;";

    $array_data = utilities_render_query($con, $sql, "id", 
            "roomname,hotelfk,description", 
            "roomname,hotel_id,description", array(":hotelid"=>$hotel_fk));
    
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}


?>