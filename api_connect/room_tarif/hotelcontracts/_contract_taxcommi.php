<?php

function _contract_taxcommi($con, $contractid) {
    $arr_taxcommi = array();

    //create general node
    $arr_taxcommi[] = array("room_rwid" => "GENERAL",
        "room_id" => "GENERAL",
        "room_name" => "General Setting",
        "room_numbedrooms" => "",
        "room_action" => "",
        "room_hasexception" => "YES",
        "buying_settings" => _getGeneralAndRoomException($con, "", $contractid, "BUYING"),
        "selling_settings" => _getGeneralAndRoomException($con, "", $contractid, "SELLING"));


    //now get all rooms for that hotel that have been defined at contract level
    $sql = "select hr.description, hr.id as roomid, hr.numbedrooms, hr.roomname,
            scr.id as tblservice_contract_rooms_id
            from tblservice_contract_rooms scr
            inner join tblhotel_rooms hr on scr.roomfk = hr.id
            where scr.servicecontractfk = :contractid
            order by hr.roomname asc";

    $stmtroom = $con->prepare($sql);
    $stmtroom->execute(array(":contractid" => $contractid));
    while ($rwroom = $stmtroom->fetch(PDO::FETCH_ASSOC)) {

        $room_rwid = $rwroom["tblservice_contract_rooms_id"];
        $room_id = $rwroom["roomid"];
        $room_name = $rwroom["roomname"];
        $room_numbedrooms = $rwroom["numbedrooms"];

        //get the dates for that room

        $arr_taxcommi[] = array("room_rwid" => $room_rwid,
            "room_id" => $room_id,
            "room_name" => $room_name,
            "room_numbedrooms" => $room_numbedrooms,
            "room_action" => "",
            "room_hasexception" => _determineRoomHasException($con, $room_id, $contractid),
            "buying_settings" => _getGeneralAndRoomException($con, $room_id, $contractid, "BUYING"),
            "selling_settings" => _getGeneralAndRoomException($con, $room_id, $contractid, "SELLING"));
    }

    return $arr_taxcommi;
}

function _determineRoomHasException($con, $room_id, $contractid)
{
    $sql = "SELECT COUNT(sct.id) as X
            FROM tblservice_contract_taxcomm sct
            WHERE sct.service_contract_fk=:contractid
            AND room_exception_id = :roomid";
    
    $hasexception = "NO";
    
    $stmtsetting = $con->prepare($sql);
    $stmtsetting->execute(array(":contractid" => $contractid, ":roomid"=>$room_id));
    if ($rwsetting = $stmtsetting->fetch(PDO::FETCH_ASSOC)) {
        if($rwsetting["X"] > 0)
        {
            $hasexception = "YES";
        }
    }
    
    
    return $hasexception;
}

function _getGeneralAndRoomException($con, $room_id, $contractid, $buysell) {
    
    $arr_setting = array();
    
    $roomcond = " AND room_exception_id IS NULL ";
    if ($room_id != "") {
        $roomcond = " AND room_exception_id = $room_id ";
    }

    $sql = "SELECT sct.* , ti.core_addon,ti.item_name,ti.abbrv, ti.code
            FROM tblservice_contract_taxcomm sct
            INNER JOIN tbltaxcomm_items ti on sct.item_fk = ti.id
            WHERE sct.service_contract_fk=:contractid
            AND sct.buying_selling = :buysell
            $roomcond
            order by sct.row_index ASC";


    $stmtsetting = $con->prepare($sql);
    $stmtsetting->execute(array(":contractid" => $contractid, ":buysell"=>$buysell));
    while ($rwsetting = $stmtsetting->fetch(PDO::FETCH_ASSOC)) {

        $rwid = $rwsetting["id"];
        $buying_selling = $rwsetting["buying_selling"];
        $row_index = $rwsetting["row_index"];
        $item_fk = $rwsetting["item_fk"];
        $item_name = $rwsetting["item_name"];
        $item_abbrv = $rwsetting["abbrv"];
        $item_code = $rwsetting["code"];
        $core_addon = $rwsetting["core_addon"];
        $basis = $rwsetting["basis"];
        $applyon_formula = $rwsetting["applyon_formula"];
        $rounding = $rwsetting["rounding"];
        
        
        $arr_setting[] = array("setting_rwid" => $rwid,
            "setting_buying_selling" => $buying_selling,
            "setting_row_index" => $row_index,
            "setting_item_fk" => $item_fk,
            "setting_item_name" => $item_name,
            "setting_item_abbrv" => $item_abbrv,
            "setting_item_code" => $item_code,
            "setting_core_addon" => $core_addon,
            "setting_basis" => $basis,
            "setting_applyon_formula" => $applyon_formula,
            "setting_rounding" => $rounding,
            "setting_action" => "",
            "setting_values" => _getSettingValues($con, $rwid));
    }
    
    return $arr_setting;
}


function _getSettingValues($con, $rwid)
{
   $arr_values = array();
   
   $sql = "select sctv.*, c.currency_code
           from tblservice_contract_taxcomm_values sctv 
           inner join tblcurrency c on sctv.currency_fk = c.id
           where sctv.service_contract_taxcomm_fk = :rwid";


    $stmtsetting = $con->prepare($sql);
    $stmtsetting->execute(array(":rwid" => $rwid));
    while ($rwsetting = $stmtsetting->fetch(PDO::FETCH_ASSOC)) {

        $rwid = $rwsetting["id"];
        $currency_fk = $rwsetting["currency_fk"];
        $value = $rwsetting["value"];
        $currency_code = $rwsetting["currency_code"];
        
        $arr_values[] = array("value_rwid" => $rwid,
            "value_currency_fk" => $currency_fk,
            "value_value" => $value,
            "value_currency_code" => $currency_code,
            "value_action" => "");
    }
   
   
   return $arr_values;
     
}

?>
