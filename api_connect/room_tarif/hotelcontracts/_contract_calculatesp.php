<?php

function _contract_calculatesp($con, $value_input, $currenyid_input, $currencyid_sell, $arr_buy, $arr_sell, $exchgrates, $__arr_alphabets, $item_is) {

    //$item_is is an array of {ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
    
    $arr_calculations = array();
    $arr_variables = array();

    //======================================================================
    //STEP 1
    //parse across each item in $arr_buy and $arr_sell to create array of variables
    //also, create a row in $arr_calculations for each item in arrbuy and arrsell

    $alpha_index = 0;
    for ($i = 0; $i < count($arr_buy); $i++) {
        if ($arr_buy[$i]["setting_action"] != "DELETE") {
            $row_name = $__arr_alphabets[$alpha_index];
            $arr_variables[$row_name] = 0;

            //======

            $setting_item_fk = $arr_buy[$i]["setting_item_fk"];
            $setting_item_name = $arr_buy[$i]["setting_item_name"];
            $setting_item_abbrv = $arr_buy[$i]["setting_item_abbrv"];
            $setting_item_code = $arr_buy[$i]["setting_item_code"];
            $setting_core_addon = $arr_buy[$i]["setting_core_addon"];
            $setting_basis = $arr_buy[$i]["setting_basis"];
            $setting_applyon_formula = $arr_buy[$i]["setting_applyon_formula"];
            $setting_rounding = $arr_buy[$i]["setting_rounding"];
            $setting_values = $arr_buy[$i]["setting_values"];

            $arr_calculations[] = array("setting_row_name" => $row_name,
                "setting_buying_selling" => "BUYING",
                "setting_item_fk" => $setting_item_fk,
                "setting_item_name" => $setting_item_name,
                "setting_item_abbrv" => $setting_item_abbrv,
                "setting_item_code" => $setting_item_code,
                "setting_core_addon" => $setting_core_addon,
                "setting_basis" => $setting_basis,
                "setting_applyon_formula" => $setting_applyon_formula,
                "setting_rounding" => $setting_rounding,
                "setting_values" => $setting_values,
                "my_calculated_value" => 0,
                "my_calculated_value_currencyid" => $currenyid_input);
            //======

            $alpha_index++;
        }
    }


    //get the currency conversion rules for the $currenyid_input
    //$currencyid_mapped_to = _getCurrencyMappedTo($currenyid_input, $exchgrates);
    //this currency buying has a currency selling depends on it
    
    for ($i = 0; $i < count($arr_sell); $i++) {
        if ($arr_sell[$i]["setting_action"] != "DELETE") {
            $row_name = $__arr_alphabets[$alpha_index];
            $arr_variables[$row_name] = 0;

            //======

            $setting_item_fk = $arr_sell[$i]["setting_item_fk"];
            $setting_item_name = $arr_sell[$i]["setting_item_name"];
            $setting_item_abbrv = $arr_sell[$i]["setting_item_abbrv"];
            $setting_item_code = $arr_sell[$i]["setting_item_code"];
            $setting_core_addon = $arr_sell[$i]["setting_core_addon"];
            $setting_basis = $arr_sell[$i]["setting_basis"];
            $setting_applyon_formula = $arr_sell[$i]["setting_applyon_formula"];
            $setting_rounding = $arr_sell[$i]["setting_rounding"];
            $setting_values = $arr_sell[$i]["setting_values"];

            $arr_calculations[] = array("setting_row_name" => $row_name,
                "setting_buying_selling" => "SELLING",
                "setting_item_fk" => $setting_item_fk,
                "setting_item_name" => $setting_item_name,
                "setting_item_abbrv" => $setting_item_abbrv,
                "setting_item_code" => $setting_item_code,
                "setting_core_addon" => $setting_core_addon,
                "setting_basis" => $setting_basis,
                "setting_applyon_formula" => $setting_applyon_formula,
                "setting_rounding" => $setting_rounding,
                "setting_values" => $setting_values,
                "my_calculated_value" => 0,
                "my_calculated_value_currencyid" => $currencyid_sell);
            //======

            $alpha_index++;
        }
    }



    //======================================================================
    //STEP 2
    //EXECUTE CALCULATIONS BUYING SIDE FIRST
    for ($i = 0; $i < count($arr_calculations); $i++) {
        $item = $arr_calculations[$i];
        if ($item["setting_buying_selling"] == "BUYING") {
            $arr_calculations[$i]["my_calculated_value"] = _executeFormulaBuySell($arr_variables, $item, $value_input, $currenyid_input, "BUYING", $item_is);
        }
    }

    //======================================================================
    //STEP 3
    //CONVERT SELLING VALUES IF ANY
    //OR EXECUTE SELLING VALUES IF ANY
    for ($i = 0; $i < count($arr_calculations); $i++) {
        $item = $arr_calculations[$i];
        if ($item["setting_buying_selling"] == "SELLING") {

            if ($item["setting_item_code"] == "CVSP") {
                $arr_calculations[$i]["my_calculated_value"] = _executeConversionBuySell($arr_variables, $item, $currenyid_input, $currencyid_sell, $exchgrates);
            } else {
                $arr_calculations[$i]["my_calculated_value"] = _executeFormulaBuySell($arr_variables, $item, $value_input, $currencyid_sell, "SELLING" , $item_is);
            }
        }
    }


    //DONE!

    return $arr_calculations;
}

function _executeConversionBuySell(&$arr_variables, $item, $currenyid_input, $currencyid_mapped_to, $exchgrates) {
    //get the value that needs to be converted
    $row_name = $item["setting_row_name"];
    $formula = $item["setting_applyon_formula"];
    $formula = _replace_variables($formula, $arr_variables);

    $tempval = 0;
    eval('$tempval =' . $formula . ';'); //temp val stores the value that needs to be converted

    if ($currencyid_mapped_to == $currenyid_input) {
        //no conversion needed because same currency buying and selling
        $arr_variables[$item["setting_row_name"]] = $tempval;
        return $tempval;
    }

    //otherwise, need a conversion
    //1. convert tempval.currencyinput to tempval.currencycost
    $rate = _getConversionRate($currenyid_input, $exchgrates);
    if ($rate != 0) {
        $tempval = $tempval / $rate;
    }


    //2. convert tempval.currencycost to tempval.currencymapped
    $rate = _getConversionRate($currencyid_mapped_to, $exchgrates);
    $tempval = ($tempval * $rate);

    //3. rounding
    $tempval = _decideRounding($tempval, $item["setting_rounding"]);

    //4. assign tempval to row variable
    $arr_variables[$item["setting_row_name"]] = $tempval;

    return $tempval;
}

function _getConversionRate($currencyid_to, $exchgrates) {
    $arr_currency_exgrates = $exchgrates["exchange_rates"];

    for ($i = 0; $i < count($arr_currency_exgrates); $i++) {
        if ($arr_currency_exgrates[$i]["rates_action"] != "DELETE" &&
                $arr_currency_exgrates[$i]["rates_to_currencyfk"] == $currencyid_to) {
            return $arr_currency_exgrates[$i]["rates_exchange_rate"];
        }
    }

    return 0;
}

/*
  function _getCurrencyMappedTo($currenyid_input, $exchgrates)
  {
  $arr_currency_mapping = $exchgrates["currency_mapping"];

  for($i = 0; $i < count($arr_currency_mapping); $i++)
  {
  if($arr_currency_mapping[$i]["mapping_action"] != "DELETE" &&
  $arr_currency_mapping[$i]["mapping_buy_currencyfk"] == $currenyid_input)
  {
  return $arr_currency_mapping[$i]["mapping_sell_currencyfk"];
  }
  }

  return -1;
  }
 * 
 */

function _executeFormulaBuySell(&$arr_variables, $item, $value_input, $currenyid, $buying_selling, $item_is) {
    $value = 0;


    //replaces all variables with values and then executes the formula
    if ($item["setting_row_name"] == "A" && $buying_selling == "BUYING") {
        //first row will have the initial value
        $arr_variables["A"] = $value_input;
        return $value_input;
    } else {
        $row_name = $item["setting_row_name"];
        $formula = $item["setting_applyon_formula"];
        $formula = _replace_variables($formula, $arr_variables);

        $tempval = 0;
        eval('$tempval =' . $formula . ';');

        if ($item["setting_core_addon"] == "CORE") {
            $value = $tempval;
            //place calculated value in array of variables
            $arr_variables[$item["setting_row_name"]] = $value;
        } else if ($item["setting_core_addon"] == "ADDON") {
            
            //setting_basis = {% PPPN ALL,% PPPN ROOM,FLAT PPPN ALL,FLAT PPPN ROOM,FLAT }
            //apply percentage or flat value on $tempval
            
            $basis_value = _getValueFromCurrency($item["setting_values"], $currenyid);
            
            
            //==========================================================
            if ($item["setting_basis"] == "% PPPN ALL") {
                //percentage applied on all items
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                $tempval = ($basis_value / 100) * $tempval;
                $tempval = _decideRounding($tempval, $item["setting_rounding"]);
            }
            //==========================================================
            if ($item["setting_basis"] == "% PNI ALL") {
                //percentage applied on all items
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                $tempval = ($basis_value / 100) * $tempval;
                $tempval = _decideRounding($tempval, $item["setting_rounding"]);
            }
            //==========================================================
            else if ($item["setting_basis"] == "% PPPN ROOM") {
                //percentage applied on ROOM, ECI, LCO only
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                
                if(in_array("ROOM", $item_is) || in_array("ECI", $item_is) ||
                   in_array("LCO", $item_is))
                {
                    $tempval = ($basis_value / 100) * $tempval;
                    $tempval = _decideRounding($tempval, $item["setting_rounding"]);
                }
                else
                {
                    $tempval = 0;
                }                
            }
            //==========================================================
            else if ($item["setting_basis"] == "% PNI ROOM") {
                //percentage applied on ROOM, ECI, LCO only
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                
                if(in_array("ROOM", $item_is) || in_array("ECI", $item_is) ||
                   in_array("LCO", $item_is))
                {
                    $tempval = ($basis_value / 100) * $tempval;
                    $tempval = _decideRounding($tempval, $item["setting_rounding"]);
                }
                else
                {
                    $tempval = 0;
                }                
            }
            //==========================================================
            else if ($item["setting_basis"] == "FLAT PPPN ALL") {
                                
                //flat PPPN applied on all items
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                if($tempval == 0){
                    $basis_value = 0; //Free of Charge
                }
                $tempval = $basis_value;
                $tempval = _decideRounding($tempval, $item["setting_rounding"]);               
                
            }
            //==========================================================
            else if ($item["setting_basis"] == "FLAT PNI") {
                                
                //flat PNI applied on all items
                //{ROOM, ECI, LCO, MEAL_SUPP, MEAL_EXTRA_SUPP, CANCELLATION}
                if($tempval == 0){
                    $basis_value = 0; //Free of Charge
                }
                $tempval = $basis_value;
                $tempval = _decideRounding($tempval, $item["setting_rounding"]);               
                
            }
            //==========================================================
            else if ($item["setting_basis"] == "FLAT PPPN ROOM") {
                
                //flat PPPN applied to ROOM, ECI, LCO
                
                if($tempval == 0){
                    $basis_value = 0; //Free of Charge
                }
                
                if(in_array("ROOM", $item_is) || in_array("ECI", $item_is) ||
                   in_array("LCO", $item_is))
                {
                    $tempval = $basis_value;
                    $tempval = _decideRounding($tempval, $item["setting_rounding"]);
                }   
                else
                {
                    $tempval = 0;
                }
            }
            
            $value = $tempval;
            //place calculated value in array of variables
            $arr_variables[$item["setting_row_name"]] = $value;
        }
    }

    return $value;
}

function _decideRounding($tempval, $rounding) {
    if($tempval < 0)
    {
        $tempval = 0;
    }
    
    if ($rounding == "ROUNDUP") {
        return ceil($tempval);
    } else if ($rounding == "ROUNDDOWN") {
        return floor($tempval);
    } else if ($rounding == "NOROUND") {
        return $tempval;
    }
    return $tempval;
}

function _getValueFromCurrency($arr_setting_values, $currenyid_input) {
    for ($i = 0; $i < count($arr_setting_values); $i++) {
        if ($arr_setting_values[$i]["value_currency_fk"] == $currenyid_input &&
                $arr_setting_values[$i]["value_action"] != "DELETE") {
            return $arr_setting_values[$i]["value_value"];
        }
    }

    return 0;
}

function _replace_variables($formula, $arr_variables) {
    $arr_alphabets = array_keys($arr_variables);

    for ($i = 0; $i < count($arr_alphabets); $i++) {
        $alphabet = $arr_alphabets[$i];
        $alphabet_value = $arr_variables[$alphabet];

        $formula = str_replace($alphabet, $alphabet_value, $formula);
    }

    return $formula;
}

?>
