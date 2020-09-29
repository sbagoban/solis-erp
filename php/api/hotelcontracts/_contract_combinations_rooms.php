<?php

function _contract_combinations_rooms($arr_rooms, $roomid, $dateid, $arr_ages) {

    //get the room first from roomid
    //if dateid is blank, then get all dates, else get the selected dateid

    $arr_combinations = array();

    for ($i = 0; $i < count($arr_rooms); $i++) {
        if ($arr_rooms[$i]["room_id"] == $roomid && $arr_rooms[$i]["room_action"] != "DELETE") {

            $arr_combinations["room_id"] = $arr_rooms[$i]["room_id"];
            $arr_combinations["room_name"] = $arr_rooms[$i]["room_name"];
            $arr_combinations["room_variants"] = $arr_rooms[$i]["room_variants"];

            $arr_room_combinations = array();

            $arr_roomdates = $arr_rooms[$i]["room_dates"];

            for ($j = 0; $j < count($arr_roomdates); $j++) {
                if ($arr_roomdates[$j]["date_action"] != "DELETE") {

                    if ($dateid == "" || $dateid == $arr_roomdates[$j]["date_rwid"]) {
                        if ($arr_combinations["room_variants"] == "PERSONS") {
                            $arr_room_combinations[] = _contract_combinationscapacitydates_persons($arr_roomdates[$j]);
                        } else {
                            //UNITS
                            $arr_room_combinations[] = _contract_combinationscapacitydates_units($arr_roomdates[$j], $arr_ages);
                        }
                    }
                }
            }

            $arr_combinations["room_combinations"] = $arr_room_combinations;


            return $arr_combinations;
        }
    }
    return $arr_combinations;
}

function _contract_combinationscapacitydates_units($room_date, $arr_ages) {
    $arr_dates_combinations = array();

    $arr_rules = $room_date["date_capacity_rules"];
    $dtfrom = $room_date["date_dtfrom"];
    $dtto = $room_date["date_dtto"];

    $arr_dates_combinations = array("dtfrom" => $dtfrom,
        "dtto" => $dtto,
        "combinations_array" => _contract_combinationscapacitydates_units_combinations($arr_rules, $arr_ages));

    return $arr_dates_combinations;
}

function _contract_combinationscapacitydates_persons($room_date) {

    $arr_dates_combinations = array();

    $arr_rules = $room_date["date_capacity_rules"];
    $dtfrom = $room_date["date_dtfrom"];
    $dtto = $room_date["date_dtto"];

    $arr_dates_combinations = array("dtfrom" => $dtfrom,
        "dtto" => $dtto,
        "combinations_array" => _contract_combinationscapacitydates_persons_combinations($arr_rules));

    return $arr_dates_combinations;
}

function _contract_combinationscapacitydates_units_combinations($arr_rules, $arr_ages) {
    $mycombinations_arr = array();

    for ($i = 0; $i < count($arr_rules); $i++) {
        $therulerow = $arr_rules[$i];

        if ($therulerow["rule_action"] != "DELETE") {

            $therulecapacities_arr = $therulerow["rule_capacity"];

            $arr_ageranges = _contract_combinations_getageranges_units($therulecapacities_arr);

            //========================================================================
            //========================================================================
            //generate the standard possibilities first
            $arr_std_min_max = _contract_combinations_getstdminmax_units($therulecapacities_arr, "STANDARDOCCUPATION", 0, 0);
            $arr_occup = _contract_combinationscapacitydates_units_combinations_std($arr_std_min_max, $arr_ageranges, $arr_ages);


            //========================================================================
            //========================================================================
            //get the additional adults and children per age groups
            $arr_extra_min_max = array();

            $arr_add_adult_min_max = _contract_combinations_getstdminmax_units($therulecapacities_arr, "ADDITIONALPERSONS", 0, 0);

            $arr_extra_min_max[] = array("AGEFROM" => -1, "AGETO" => -1, "MIN" => $arr_add_adult_min_max["MIN"], "MAX" => $arr_add_adult_min_max["MAX"]);

            for ($i = 0; $i < count($arr_ageranges); $i++) {
                $age_from = $arr_ageranges[$i]["AGEFROM"];
                $age_to = $arr_ageranges[$i]["AGETO"];
                $arr_add_ch_min_max = _contract_combinations_getstdminmax_units($therulecapacities_arr, "CH", $age_from, $age_to);
                $arr_extra_min_max[] = array("AGEFROM" => $age_from, "AGETO" => $age_to, "MIN" => $arr_add_ch_min_max["MIN"], "MAX" => $arr_add_ch_min_max["MAX"]);
            }

            //generate extra combinations 
            $arr_extra_occup = _contract_combinationscapacitydates_units_combinations_add($arr_extra_min_max);


            //========================================================================
            //========================================================================
            //mix std occupancy with extra occupancy
            $mycombinations_arr = array();

            for ($i = 0; $i < count($arr_occup); $i++) {
                $mycombinations_arr[] = $arr_occup[$i]; //push std occupation first


                for ($j = 0; $j < count($arr_extra_occup); $j++) {
                    //push items from $arr_extra_occup not found in $arr_occup
                    $arr_item = _contract_combinationscapacitydates_units_combine_stdextra($arr_occup[$i], $arr_extra_occup[$j]);

                    //validate the $arr_item to make sure it does not exceed allowable capacity
                    if (_contract_combinationscapacitydates_units_validate_combi_item($arr_item, $arr_extra_min_max, $arr_std_min_max)) {
                        $mycombinations_arr[] = $arr_item;
                    }
                }
            }


            //========================================================================
            //========================================================================
            //eliminate duplicates
            $mycombinations_arr = array_values(array_unique($mycombinations_arr, SORT_REGULAR));
        }
    }

    return $mycombinations_arr;
}

function _contract_combinationscapacitydates_units_validate_combi_item($arr_item, $arr_extra_min_max, $arr_std_min_max) {
    //return true of this combination in $arr_item is permissible
    //false otherwise

    $std_max = $arr_std_min_max["MAX"];

    for ($i = 0; $i < count($arr_item); $i++) {
        $agefrom = $arr_item[$i]["AGEFROM"];
        $ageto = $arr_item[$i]["AGETO"];
        $no = $arr_item[$i]["No"];

        if ($std_max > 0) {
            _contract_combinationscapacitydates_deduct($std_max, $no);
            $arr_item[$i]["No"] = $no;
        }

        if ($arr_item[$i]["No"] > 0) {
            for ($j = 0; $j < count($arr_extra_min_max); $j++) {
                $_agfrom = $arr_extra_min_max[$j]["AGEFROM"];
                $_agto = $arr_extra_min_max[$j]["AGETO"];
                $_max = $arr_extra_min_max[$j]["MAX"];
                if ($agefrom == $_agfrom && $ageto == $_agto) {
                    _contract_combinationscapacitydates_deduct($_max, $no);
                    $arr_item[$i]["No"] = $no;
                    $arr_extra_min_max[$j]["MAX"] = $_max;
                }
            }
        }
    }

    //count total pax left in $arr_item 
    //must be <= 0
    $sum = 0;
    for ($i = 0; $i < count($arr_item); $i++) {
        $sum += $arr_item[$i]["No"];
    }

    if ($sum > 0) {
        return false;
    }


    return true;
}

function _contract_combinationscapacitydates_deduct(&$x1, &$x2) {
    if ($x1 == $x2) {
        $x1 = 0;
        $x2 = 0;
    } else if ($x1 > $x2) {
        $x1 -= $x2;
        $x2 = 0;
    } else {
        $x2 -= $x1;
        $x1 = 0;
    }

    return;
}

function _contract_combinationscapacitydates_units_combine_stdextra($std_item, $extra_item) {
    for ($i = 0; $i < count($extra_item); $i++) {
        $ag_from = $extra_item[$i]["AGEFROM"];
        $ag_to = $extra_item[$i]["AGETO"];
        $no = $extra_item[$i]["No"];

        $found = false;

        for ($j = 0; $j < count($std_item); $j++) {
            $_ag_from = $std_item[$j]["AGEFROM"];
            $_ag_to = $std_item[$j]["AGETO"];

            if ($_ag_from == $ag_from && $_ag_to == $ag_to) {
                $std_item[$j]["No"] += $no;
                $found = true;
            }
        }

        if (!$found) {
            $std_item[] = $extra_item[$i];
        }
    }

    return $std_item;
}

function _contract_combinationscapacitydates_units_combinations_add($arr_extra_min_max) {
    $x = 0;
    $str = "";
    $arr_extra_combinations = array();


    _contract_combinationscapacitydates_units_combinations_extra_recur($x, $arr_extra_min_max, $str, $arr_extra_combinations);

    //===============================================

    $arr_matrix = array();

    for ($i = 0; $i < count($arr_extra_combinations); $i++) {
        $arr = array();

        $str = $arr_extra_combinations[$i];
        $arr_pax = explode("_", $str);
        for ($x = 0; $x < count($arr_pax); $x++) {
            $count = $arr_pax[$x];
            if ($count > 0) {
                $agefrom = $arr_extra_min_max[$x]["AGEFROM"];
                $ageto = $arr_extra_min_max[$x]["AGETO"];

                $arr[] = array("AGEFROM" => $agefrom, "AGETO" => $ageto, "No" => $count);
            }
        }

        $arr_matrix[] = $arr;
    }

    //===============================================

    return $arr_matrix;
}

function _contract_combinationscapacitydates_units_combinations_extra_recur($x, $arr_extra_min_max, $str, &$arr_extra_combinations) {
    if ($x == count($arr_extra_min_max)) {
        $arr_extra_combinations[] = $str;
    } else {
        $min = $arr_extra_min_max[$x]["MIN"];
        $max = $arr_extra_min_max[$x]["MAX"];

        for ($j = $min; $j <= $max; $j++) {
            $temp_str = $str;
            if (trim($str) != "") {
                $temp_str .= "_";
            }
            $temp_str .= $j;
            _contract_combinationscapacitydates_units_combinations_extra_recur(($x + 1), $arr_extra_min_max, $temp_str, $arr_extra_combinations);
        }
    }
}

function _contract_combinationscapacitydates_units_combinations_std($arr_std_min_max, $arr_ageranges, $arr_contract_ages) {

    $min = $arr_std_min_max["MIN"];
    $max = $arr_std_min_max["MAX"];
    
    //===========================
    //first matrix of ages
    $ad_ages = array("-1_-1"); //<---------  adults
    for($i = 0; $i < count($arr_contract_ages); $i++)
    {
        $age_rgn = $arr_contract_ages[$i];
        $ad_ages[] = $age_rgn["AGEFROM"] . "_" . $age_rgn["AGETO"];
    }
    //===========================
    
    //===========================
    //2nd matrix of qunatities
    $qty = array(); 
    for($i = 0; $i <= $max; $i++)
    {
        $qty[] = $i;
    }
    //===========================
    
    //multiply the two matrices together
    $arr_arrays = array();
    for ($i = 0; $i < count($ad_ages); $i++) {
        $_arr = array();

        for ($j = 0; $j < count($qty); $j++) {
            $_arr[] = $ad_ages[$i] . ":" . $qty[$j];
        }

        $arr_arrays[] = $_arr;
    }
    //================================
    
    $possible_combinations = generate_units_std_combinations($arr_arrays);
    $possible_combinations = validate_units_standard_combinations($possible_combinations, $min, $max);

    //reformat the combinations array
    $arr_return = array();

    for ($i = 0; $i < count($possible_combinations); $i++) {
        $combination = $possible_combinations[$i];

        $arr_return_combination = array();

        for ($j = 0; $j < count($combination); $j++) {
            $pax = $combination[$j];

            $arr_details = explode(":", $pax);
            $age_ranges = $arr_details[0];
            $count = $arr_details[1];

            $arr_age_ranges = explode("_", $age_ranges);
            $age_from = $arr_age_ranges[0];
            $age_to = $arr_age_ranges[1];

            $arr_return_combination[] = array("AGEFROM" => $age_from, "AGETO" => $age_to, "No" => $count);
        }

        $arr_return[] = $arr_return_combination;
    }

    return $arr_return;
}

function validate_units_standard_combinations($possible_combinations, $min, $max) {
    $arr_final = array();

    //make sure that the sum_pax is within the min max ranges
    for ($i = 0; $i < count($possible_combinations); $i++) {

        $arr_persons = $possible_combinations[$i];

        $sum_persons = 0;
        $arr_persons_final = array();
        for ($j = 0; $j < count($arr_persons); $j++) {
            $pax = $arr_persons[$j]; //$pax in the form "AD:1"
            $arr_details = explode(":", $pax);
            $sum_persons += $arr_details[1];

            if ($arr_details[1] > 0) {
                $arr_persons_final[] = $pax;
            }
        }

        if ($sum_persons >= $min && $sum_persons <= $max) {
            $arr_final[] = $arr_persons_final;
        }
    }

    return $arr_final;
}

function generate_units_std_combinations($arrays, $i = 0) {
    if (!isset($arrays[$i])) {
        return array();
    }
    if ($i == count($arrays) - 1) {
        return $arrays[$i];
    }

    // get combinations from subsequent arrays
    $tmp = generate_units_std_combinations($arrays, $i + 1);

    $result = array();

    // concat each array from tmp with each element from $arrays[$i]
    foreach ($arrays[$i] as $v) {
        foreach ($tmp as $t) {
            $result[] = is_array($t) ?
                    array_merge(array($v), $t) :
                    array($v, $t);
        }
    }

    return $result;
}

function _contract_combinations_getstdminmax_units($arr_rules, $category, $agfrom, $agto) {
    $min = 0;
    $max = 0;

    for ($i = 0; $i < count($arr_rules); $i++) {
        $line = $arr_rules[$i];
        if ($line["capacity_action"] != "DELETE" && $line["capacity_category"] == $category) {
            if ($category == "CH") {
                if ($line["capacity_child_agefrom"] == $agfrom &&
                        $line["capacity_child_ageto"] == $agto) {
                    $min = $line["capacity_minpax"];
                    $max = $line["capacity_maxpax"];
                }
            } else {
                $min = $line["capacity_minpax"];
                $max = $line["capacity_maxpax"];
            }
        }
    }

    return array("MIN" => $min, "MAX" => $max);
}

function _contract_combinationscapacitydates_persons_combinations($arr_rules) {

    $mycombinations_arr = array();

    for ($i = 0; $i < count($arr_rules); $i++) {
        $therulerow = $arr_rules[$i];

        $capacity_rule_id = $therulerow["rule_rwid"];

        if ($therulerow["rule_action"] != "DELETE") {

            $therulecapacities_arr = $therulerow["rule_capacity"];

            // [-1,-1],[0,4],[12,17]
            $arr_ageranges = _contract_combinations_getageranges_persons($therulecapacities_arr);

            // [0] :: {-1-1 No1}, {-1-1 No2}]
            // [1] :: [{0 4 No0},{0 4 No1},{0 4 No2}]  
            // [2] :: {12 17 No0},{12 17 No1}]

            $final_arr = _contract_combinations_generatecountperage($therulecapacities_arr, $arr_ageranges);

            $arr_permutations = _contract_combinations_permutations($final_arr);

            $mycombinations_arr = array_merge($mycombinations_arr, $arr_permutations);

            //eliminate duplicates
            $mycombinations_arr = array_values(array_unique($mycombinations_arr, SORT_REGULAR));
        }
    }



    return $mycombinations_arr;
}

function _contract_combinations_getageranges_units($therulecapacities_arr) {
    //get all age ranges possible in the rule
    $arr_ageranges = array();

    //get children
    for ($i = 0; $i < count($therulecapacities_arr); $i++) {
        if ($therulecapacities_arr[$i]["capacity_action"] != "DELETE") {
            $capacity_category = $therulecapacities_arr[$i]["capacity_category"];
            $capacity_child_agefrom = $therulecapacities_arr[$i]["capacity_child_agefrom"];
            $capacity_child_ageto = $therulecapacities_arr[$i]["capacity_child_ageto"];

            if ($capacity_category == "CH") {

                //is agefrom and ageto in array?
                if (!_isagerangeinarray($arr_ageranges, $capacity_child_agefrom, $capacity_child_ageto)) {
                    $arr_ageranges[] = array("AGEFROM" => $capacity_child_agefrom, "AGETO" => $capacity_child_ageto);
                }
            }
        }
    }

    return $arr_ageranges;
}

function _contract_combinations_getageranges_persons($therulecapacities_arr) {
    //get all age ranges possible in the rule
    $arr_ageranges = array();

    $arr_ageranges[] = array("AGEFROM" => -1, "AGETO" => -1); //ADULTS
    //now get children
    for ($i = 0; $i < count($therulecapacities_arr); $i++) {
        if ($therulecapacities_arr[$i]["capacity_action"] != "DELETE") {


            $capacity_category = $therulecapacities_arr[$i]["capacity_category"];
            $capacity_child_agefrom = $therulecapacities_arr[$i]["capacity_child_agefrom"];
            $capacity_child_ageto = $therulecapacities_arr[$i]["capacity_child_ageto"];

            if ($capacity_category == "CHILD") {

                //is agefrom and ageto in array?
                if (!_isagerangeinarray($arr_ageranges, $capacity_child_agefrom, $capacity_child_ageto)) {
                    $arr_ageranges[] = array("AGEFROM" => $capacity_child_agefrom, "AGETO" => $capacity_child_ageto);
                }
            }
        }
    }

    return $arr_ageranges;
}

function _isagerangeinarray($arr_ageranges, $agefrom, $ageto) {
    for ($i = 0; $i < count($arr_ageranges); $i++) {
        if ($arr_ageranges[$i]["AGEFROM"] == $agefrom &&
                $arr_ageranges[$i]["AGETO"] == $ageto) {
            return true;
        }
    }

    return false;
}

function _contract_combinations_generatecountperage($therulecapacities_arr, $arr_ageranges) {

    $final_arr = array();


    for ($i = 0; $i < count($arr_ageranges); $i++) {
        $agefrom = $arr_ageranges[$i]["AGEFROM"];
        $ageto = $arr_ageranges[$i]["AGETO"];

        $min = 0;
        $max = 0;

        for ($j = 0; $j < count($therulecapacities_arr); $j++) {
            if ($agefrom == -1 && $ageto == -1 &&
                    $therulecapacities_arr[$j]["capacity_category"] == "ADULT") {
                $min = $therulecapacities_arr[$j]["capacity_minpax"];
                $max = $therulecapacities_arr[$j]["capacity_maxpax"];


                while ($min <= $max) {
                    $final_arr[$i][] = array("AGEFROM" => $agefrom,
                        "AGETO" => $ageto,
                        "No" => $min);
                    $min++;
                }
            } else if ($agefrom == $therulecapacities_arr[$j]["capacity_child_agefrom"] &&
                    $ageto == $therulecapacities_arr[$j]["capacity_child_ageto"] &&
                    $therulecapacities_arr[$j]["capacity_category"] == "CHILD") {
                $min = $therulecapacities_arr[$j]["capacity_minpax"];
                $max = $therulecapacities_arr[$j]["capacity_maxpax"];

                $arr_counts = array();
                while ($min <= $max) {
                    $final_arr[$i][] = array("AGEFROM" => $agefrom,
                        "AGETO" => $ageto,
                        "No" => $min);
                    $min++;
                }
            }
        }
    }

    return $final_arr;
}

//function _contract_combinations_permutations($arrays, $i = 0) {
function _contract_combinations_permutations($arrays) {
    $result = array(array());
    foreach ($arrays as $property => $property_values) {
        $tmp = array();
        foreach ($result as $result_item) {
            foreach ($property_values as $property_value) {
                $tmp[] = array_merge($result_item, array($property => $property_value));
            }
        }
        $result = $tmp;
    }
    return $result;
}

//}
?>
