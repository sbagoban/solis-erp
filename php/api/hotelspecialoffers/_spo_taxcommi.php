<?php

function _spo_taxcommi($con, $spoid) {

    //create general node
    $arr_taxcommi = array(
        "buying_settings" => _getSettings($con,$spoid, "BUYING"),
        "selling_settings" => _getSettings($con,$spoid, "SELLING"));


    return $arr_taxcommi;
}


function _getSettings($con, $spoid, $buysell) {
    
    $arr_setting = array();
    
    
    $sql = "SELECT sct.* , ti.core_addon,ti.item_name,ti.abbrv, ti.code
            FROM tblspecial_offer_flatrate_taxcomm sct
            INNER JOIN tbltaxcomm_items ti on sct.item_fk = ti.id
            WHERE sct.spo_fk=:spo_fk
            AND sct.buying_selling = :buysell
            order by sct.row_index ASC";


    $stmtsetting = $con->prepare($sql);
    $stmtsetting->execute(array(":spo_fk" => $spoid, ":buysell"=>$buysell));
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
           from tblspecial_offer_flatrate_taxcomm_values sctv 
           inner join tblcurrency c on sctv.currency_fk = c.id
           where sctv.special_offer_taxcomm_fk = :rwid";


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
