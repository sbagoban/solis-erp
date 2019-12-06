<?php

try {
    
    $api_name = "api_hotel_room_rates";
    
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
    
    /*
    adults: Array(2)
        0: {count: "1", age: "30", bride_groom: "BRIDE"}
        1: {count: "2", age: "30", bride_groom: "GROOM"}
    checkin_date: "2019-04-03"
    checkin_time: "13:00"
    checkout_date: "2019-04-24"
    checkout_time: "15:00"
    children: Array(2)
        0: {count: "1", age: "4", sharing_own: "SHARING"}
        1: {count: "2", age: "5", sharing_own: "SHARING"}
    country: "845"
    hotel: "19" <-------------------------- get hotel id from hotel room id provided
    hotelroom: "2"
    mealplan: "6"
    rate: "1"
    supp_mealplan: "4"
    touroperator: "10"
    */
    
    /*
    chk_is_wedding: 1
    chk_show_invalid_spos: 0
    spo_booking_date: Mon Apr 01 2019 17:49:00 GMT+0400 (Mauritius Standard Time) {}
    spo_chosen: "LOWEST"
    spo_party_pax: "20"
    spo_travel_date: Wed Apr 03 2019 17:50:00 GMT+0400 (Mauritius Standard Time) {}
    spo_type: "BOTH"
     */
    
    //validate parameters
    $validate_outcome = validate_rates_parameters($_POST);
    if($validate_outcome != "OK")
    {
        throw new Exception($authenticate_outcome);
    }
    
    
    //all is valid
    $tofk = $authenticate_outcome["TOID"]; //get the toid of the user
    
    
    echo json_encode(array("OUTCOME"=>"OK","DATA"=>$array_data));
    
} catch (Exception $ex) {
    echo json_encode(array("OUTCOME"=>$ex->getMessage()));
}

function validate_rates_parameters($arrPOST)
{   
    //is not blank: checkin_date, checkout_date, hotel, hotelroom, mealplan,spo_booking_date, spo_travel_date
    
    //checkin_date < checkout_date
    //spo_booking_date <= spo_travel_date
    
    //adult.count and children.count cannot be both 0
    //cannot have chilren.sharing_own both SHARING and OWN
    
    //if chk_is_wedding == 1 then must be GROOM and BRIDE in adults array
    
    return "OK";
    

    
}


?>