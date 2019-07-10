<?php

function _contract_combinations_rooms($arr_rooms, $roomid, $dateid) {

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
                            $arr_room_combinations[] = _contract_combinationscapacitydates_units($arr_roomdates[$j]);
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

function _contract_combinationscapacitydates_units($room_date) {
    $arr_dates_combinations = array();

    $arr_rules = $room_date["date_capacity_rules"];
    $dtfrom = $room_date["date_dtfrom"];
    $dtto = $room_date["date_dtto"];

    $arr_dates_combinations = array("dtfrom" => $dtfrom,
        "dtto" => $dtto,
        "combinations_array" => _contract_combinationscapacitydates_units_combinations($arr_rules));

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

function _contract_combinationscapacitydates_units_combinations($arr_rules) {
    $mycombinations_arr = array();

    for ($i = 0; $i < count($arr_rules); $i++) {
        $therulerow = $arr_rules[$i];

        if ($therulerow["rule_action"] != "DELETE") {

            $therulecapacities_arr = $therulerow["rule_capacity"];

            $arr_std_min_max = _contract_combinations_getstdminmax_units($therulecapacities_arr, "STANDARDOCCUPATION", 0, 0);
            $arr_add_adult_min_max = _contract_combinations_getstdminmax_units($therulecapacities_arr, "ADDITIONALPERSONS", 0, 0);

            // [-1,-1],[0,4],[12,17]
            $arr_ageranges = _contract_combinations_getageranges_units($therulecapacities_arr);
        }
    }

    return $mycombinations_arr;
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
                    $max = $line["capacity_mixpax"];
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
            $mycombinations_arr = array_unique($mycombinations_arr, SORT_REGULAR);
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
                    $min ++;
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
                    $min ++;
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
