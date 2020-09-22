<?php

function _contract_capacityarr($con, $contractid) {
    //get all rooms for that hotel that have been defined at contract level

    $arr_room = array();

    $sql = "select hr.*, scr.id as room_rwid, 
            scrc.variant as variant,
            scrc.id as service_contract_roomcapacity_id
            from tblservice_contract_rooms  scr
            inner join tblhotel_rooms hr on scr.roomfk = hr.id
            inner join tblservice_contract_roomcapacity scrc on scrc.roomfk = scr.roomfk and scrc.service_contract_fk = :contractid
            where scr.servicecontractfk = :contractid
            order by hr.roomname asc";
    $stmtroom = $con->prepare($sql);
    $stmtroom->execute(array(":contractid" => $contractid));
    while ($rwroom = $stmtroom->fetch(PDO::FETCH_ASSOC)) {

        $room_rwid = $rwroom["service_contract_roomcapacity_id"];
        $room_id = $rwroom["id"];
        $room_name = $rwroom["roomname"];
        $room_numbedrooms = $rwroom["numbedrooms"];
        $room_variants = $rwroom["variant"];

        //get the dates for that room

        $arr_room[] = array("room_rwid" => $room_rwid,
            "room_id" => $room_id,
            "room_name" => $room_name,
            "room_numbedrooms" => $room_numbedrooms,
            "room_variants" => $room_variants,
            "room_action" => "",
            "room_dates" => _contract_getRoomDates($con, $room_rwid));
    }
    return $arr_room;
}

function _contract_getRoomDates($con, $room_rwid) {
    $arr_dates = array();
    $sql = "SELECT 
            IFNULL(scrd.override_dtfrom,'') as override_dtfrom,
            IFNULL(scrd.override_dtto,'') as override_dtto,
            scrd.service_contract_roomcapacity_fk,
            scrd.id, s.season, s.id as seasonid
            FROM tblservice_contract_roomcapacity_dates scrd
            LEFT JOIN tblseasons s ON scrd.season_fk = s.id
            WHERE scrd.service_contract_roomcapacity_fk=:room_rwid 
            ORDER BY scrd.override_dtfrom ASC, scrd.override_dtto ASC, s.season ASC";

    $stmtdates = $con->prepare($sql);
    $stmtdates->execute(array(":room_rwid" => $room_rwid));
    while ($rwdates = $stmtdates->fetch(PDO::FETCH_ASSOC)) {
        $date_rwid = $rwdates["id"];
        $date_dtfrom = $rwdates["override_dtfrom"];
        $date_dtto = $rwdates["override_dtto"];
        $season_id = $rwdates["seasonid"];

        $arr_dates[] = array("date_rwid" => $date_rwid,
            "date_dtfrom" => $date_dtfrom,
            "date_dtto" => $date_dtto,
            "date_season_id" => $season_id,
            "date_action" => "",
            "date_minstay_rules" => _contract_getRoomDatesMinStayRules($con, $date_rwid),
            "date_mealsupplement_rules" => _contract_getRoomDatesMealSupplementRules($con, $date_rwid),
            "date_mealextrasupplement_rules" => _contract_getRoomDatesExtraSupplementRules($con, $date_rwid),
            "date_policies_checkinout" => _contract_getRoomDatesCheckInOutRules($con, $date_rwid),
            "date_policies_cancellation" => _contract_getRoomDatesCancellationRules($con, $date_rwid),
            "date_capacity_rules" => _contract_getRoomDatesCapacityRules($con, $date_rwid),
            "date_adultpolicies_rules" => _contract_getRoomDatesAdultPoliciesRules($con, $date_rwid),
            "date_childpolicies_rules" => _contract_getRoomDatesChildPoliciesRules($con, $date_rwid),
            "date_singleparentpolicies_rules" => _contract_getRoomDatesSingleParentPoliciesRules($con, $date_rwid));
    }

    return $arr_dates;
}

function _contract_getRoomDatesCapacityRules($con, $date_rwid) {
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblservice_contract_roomcapacity_dates_rules 
            WHERE service_contract_roomcapacity_dates_fk=:dateid 
            ORDER BY rulecounter ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];

        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_action" => "",
            "rule_capacity" => _contract_getRuleCapacity($con, $rule_rwid));
    }

    return $arr_rules;
}

function _contract_getRuleCapacity($con, $rule_rwid) {

    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM tblservice_contract_roomcapacity_dates_rules_ages 
            WHERE service_contract_roomcapacity_dates_rule_fk=:ruleid 
            ORDER BY category ASC, child_agefrom ASC, child_ageto ASC";

    $stmtcapacity = $con->prepare($sql);
    $stmtcapacity->execute(array(":ruleid" => $rule_rwid));

    while ($rwcapacity = $stmtcapacity->fetch(PDO::FETCH_ASSOC)) {
        $capacity_rwid = $rwcapacity["id"];
        $capacity_category = $rwcapacity["category"];
        $capacity_minpax = $rwcapacity["minpax"];
        $capacity_maxpax = $rwcapacity["maxpax"];
        $capacity_child_agefrom = $rwcapacity["child_agefrom"];
        $capacity_child_ageto = $rwcapacity["child_ageto"];

        $arr_capacity[] = array("capacity_rwid" => $capacity_rwid,
            "capacity_category" => $capacity_category, //standardoccupation,additionalpersons,crib,adult,child
            "capacity_minpax" => $capacity_minpax,
            "capacity_maxpax" => $capacity_maxpax,
            "capacity_child_agefrom" => $capacity_child_agefrom,
            "capacity_child_ageto" => $capacity_child_ageto,
            "capacity_action" => "");
    }

    return $arr_capacity;
}

function _contract_getRoomDatesSingleParentPoliciesRules($con, $date_rwid) {
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblservice_contract_snglprntpolicy_room_dates_rules 
            WHERE service_contract_roomcapacity_dates_fk=:dateid 
            ORDER BY id ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];
        $rule_category = $rwrules["rulecategory"];
        $ruleageranges = $rwrules["ruleageranges"];

        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_category" => $rule_category,
            "rule_ageranges" => $ruleageranges,
            "rule_action" => "",
            "rule_policy" => _contract_getSingelParentPolicyDateRuleAges($con, $rule_rwid));
    }

    return $arr_rules;
}

function _contract_getRoomDatesChildPoliciesRules($con, $date_rwid) {
    //CHANGES:: UPDATED FUNCTION
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblservice_contract_childpolicy_room_dates_rules 
            WHERE service_contract_roomcapacity_dates_fk=:dateid 
            ORDER BY id ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];
        $rule_category = $rwrules["rulecategory"];
        $rule_ageranges = $rwrules["ruleageranges"];
        $rule_sharing_single = $rwrules["sharing_single"];
        
        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_category" => $rule_category,
            "rule_sharing_single" => $rule_sharing_single,
            "rule_ageranges" => $rule_ageranges,
            "rule_action" => "",
            "rule_policy" => _contract_getChildPolicyDateRuleAges($con, $rule_rwid));
    }

    return $arr_rules;
}

function _contract_getRoomDatesAdultPoliciesRules($con, $date_rwid) {
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblservice_contract_adultpolicy_room_dates_rules 
            WHERE service_contract_roomcapacity_dates_fk=:dateid 
            ORDER BY id ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];
        $rule_category = $rwrules["rulecategory"];

        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_category" => $rule_category,
            "rule_action" => "",
            "rule_policy" => _contract_getAdultPolicyDateRuleAges($con, $rule_rwid));
    }

    return $arr_rules;
}

function _contract_getSingelParentPolicyDateRuleAges($con, $rule_rwid) {
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM tblservice_contract_snglprntpolicy_room_dates_rules_ages 
            WHERE service_contract_snglprntpolicy_room_dates_rules_fk=:ruleid 
            ORDER BY adult_child ASC, category ASC, 
            child_agefrom ASC, 
            child_ageto ASC";

    $stmtcapacity = $con->prepare($sql);
    $stmtcapacity->execute(array(":ruleid" => $rule_rwid));

    while ($rwcapacity = $stmtcapacity->fetch(PDO::FETCH_ASSOC)) {
        $capacity_rwid = $rwcapacity["id"];
        $capacity_adult_child = $rwcapacity["adult_child"];
        $capacity_category = $rwcapacity["category"];
        $basis = $rwcapacity["basis"];
        $child_agefrom = $rwcapacity["child_agefrom"];
        $child_ageto = $rwcapacity["child_ageto"];

        $arr_capacity[] = array("policy_rwid" => $capacity_rwid,
            "policy_adult_child" => $capacity_adult_child,
            "policy_category" => $capacity_category,
            "policy_basis" => $basis,
            "policy_child_agefrom" => $child_agefrom,
            "policy_child_ageto" => $child_ageto,
            "policy_action" => "",
            "policy_values" => _contract_getSingleParentPolicyDateRuleAgesValues($con, $capacity_rwid));
    }

    return $arr_capacity;
}

function _contract_getChildPolicyDateRuleAges($con, $rule_rwid) {
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM tblservice_contract_childpolicy_room_dates_rules_ages 
            WHERE service_contract_childpolicy_room_dates_rules_fk=:ruleid 
            ORDER BY category ASC, 
            child_agefrom ASC, 
            child_ageto ASC";

    $stmtcapacity = $con->prepare($sql);
    $stmtcapacity->execute(array(":ruleid" => $rule_rwid));

    while ($rwcapacity = $stmtcapacity->fetch(PDO::FETCH_ASSOC)) {
        $capacity_rwid = $rwcapacity["id"];
        $capacity_category = $rwcapacity["category"];
        $basis = $rwcapacity["basis"];
        $child_agefrom = $rwcapacity["child_agefrom"];
        $child_ageto = $rwcapacity["child_ageto"];

        $arr_capacity[] = array("policy_rwid" => $capacity_rwid,
            "policy_category" => $capacity_category, //standardoccupation,additionalpersons,adult,child
            "policy_basis" => $basis,
            "policy_units_additional_child_agefrom" => $child_agefrom,
            "policy_units_additional_child_ageto" => $child_ageto,
            "policy_action" => "",
            "policy_values" => _contract_getChildPolicyDateRuleAgesValues($con, $capacity_rwid));
    }

    return $arr_capacity;
}

function _contract_getAdultPolicyDateRuleAges($con, $rule_rwid) {

    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM tblservice_contract_adultpolicy_room_dates_rules_ages 
            WHERE service_contract_adultpolicy_room_dates_rules_fk=:ruleid 
            ORDER BY category ASC, 
            units_additional_child_agefrom ASC, 
            units_additional_child_ageto ASC";

    $stmtcapacity = $con->prepare($sql);
    $stmtcapacity->execute(array(":ruleid" => $rule_rwid));

    while ($rwcapacity = $stmtcapacity->fetch(PDO::FETCH_ASSOC)) {
        $capacity_rwid = $rwcapacity["id"];
        $capacity_category = $rwcapacity["category"];
        $basis = $rwcapacity["basis"];
        $units_additional_child_agefrom = $rwcapacity["units_additional_child_agefrom"];
        $units_additional_child_ageto = $rwcapacity["units_additional_child_ageto"];

        $arr_capacity[] = array("policy_rwid" => $capacity_rwid,
            "policy_category" => $capacity_category, //standardoccupation,additionalpersons,adult,child
            "policy_basis" => $basis,
            "policy_units_additional_child_agefrom" => $units_additional_child_agefrom,
            "policy_units_additional_child_ageto" => $units_additional_child_ageto,
            "policy_action" => "",
            "policy_values" => _contract_getAdultPolicyDateRuleAgesValues($con, $capacity_rwid));
    }

    return $arr_capacity;
}

function _contract_getSingleParentPolicyDateRuleAgesValues($con, $capacity_rwid) {
    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM tblservice_contract_snglprntpolicy_room_dates_rules_ages_values 
            WHERE service_contract_snglprntpolicy_room_dates_rules_ages_fk=:fk";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":fk" => $capacity_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $value_rwid = $rwvalue["id"];
        $value_currencyfk = $rwvalue["mycurrencyfk"];
        $value_basis = $rwvalue["basis"];
        $value_value = $rwvalue["value"];

        $arr_values[] = array("value_rwid" => $value_rwid,
            "value_currencyfk" => $value_currencyfk,
            "value_basis" => $value_basis,
            "value_value" => $value_value,
            "value_action" => "");
    }

    return $arr_values;
}

function _contract_getChildPolicyDateRuleAgesValues($con, $capacity_rwid) {
    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM tblservice_contract_child_policy_room_dates_rules_ages_values 
            WHERE service_contract_child_policy_room_dates_rules_ages_fk=:fk";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":fk" => $capacity_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $value_rwid = $rwvalue["id"];
        $value_currencyfk = $rwvalue["mycurrencyfk"];
        $value_basis = $rwvalue["basis"];
        $value_value = $rwvalue["value"];

        $arr_values[] = array("value_rwid" => $value_rwid,
            "value_currencyfk" => $value_currencyfk,
            "value_basis" => $value_basis,
            "value_value" => $value_value,
            "value_action" => "");
    }

    return $arr_values;
}

function _contract_getAdultPolicyDateRuleAgesValues($con, $capacity_rwid) {

    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM tblservice_contract_adult_policy_room_dates_rules_ages_values 
            WHERE service_contract_adult_policy_room_dates_rules_ages_fk=:fk";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":fk" => $capacity_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $value_rwid = $rwvalue["id"];
        $value_currencyfk = $rwvalue["mycurrencyfk"];
        $value_basis = $rwvalue["basis"];
        $value_value = $rwvalue["value"];

        $arr_values[] = array("value_rwid" => $value_rwid,
            "value_currencyfk" => $value_currencyfk,
            "value_basis" => $value_basis,
            "value_value" => $value_value,
            "value_action" => "");
    }

    return $arr_values;
}

function _contract_getRoomDatesMealSupplementRules($con, $date_rwid) {
    $arr_meal = array();

    $sql = "SELECT * FROM tblservice_contract_mealsupplement 
            WHERE service_contract_roomcapacity_dates_fk=:id 
            ORDER BY is_main DESC";

    $query = $con->prepare($sql);
    $query->execute(array(":id" => $date_rwid));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $meal_id = $rw["id"];
        $meal_mealplanfk = $rw["mealplanfk"];
        $meal_ismain = $rw["is_main"];
        $meal_adult_count = $rw["adult_count"];


        $arr_meal[] = array("meal_rwid" => $meal_id,
            "meal_mealplanfk" => $meal_mealplanfk,
            "meal_ismain" => $meal_ismain,
            "meal_adult_count" => $meal_adult_count,
            "meal_action" => "",
            "meal_children" => _contract_getMealSupplementChildrenMeals($meal_id, $con));
    }

    return $arr_meal;
}

function _contract_getMealSupplementChildrenMeals($meal_id, $con) {
    $arr_children = array();

    //get children count by age range
    $sql = "SELECT * FROM tblservice_contract_mealsupplement_childages 
            WHERE service_contract_mealsupplement_fk=:meal_id 
            ORDER BY child_age_from,child_age_to";

    $query_child = $con->prepare($sql);
    $query_child->execute(array(":meal_id" => $meal_id));
    while ($rw_child = $query_child->fetch(PDO::FETCH_ASSOC)) {

        $child_rwid = $rw_child["id"];
        $age_from = $rw_child["child_age_from"];
        $age_to = $rw_child["child_age_to"];
        $child_count = $rw_child["child_count"];

        $arr_children[] = array("child_rwid" => $child_rwid,
            "child_agefrom" => $age_from,
            "child_ageto" => $age_to,
            "child_count" => $child_count,
            "child_action" => "");
    }

    return $arr_children;
}

function _contract_getRoomDatesExtraSupplementRules($con, $date_rwid) {
        $arr_extra = array();

    $sql = "SELECT * FROM tblservice_contract_extrasupplement 
            WHERE service_contract_roomcapacity_dates_fk=:id
            ORDER BY extra_date ASC";

    $query = $con->prepare($sql);
    $query->execute(array(":id" => $date_rwid));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $extra_id = $rw["id"];
        $extra_extra_name = $rw["extra_name"];
        $extra_mandatory = $rw["mandatory"];
        $extra_spo_deductable = $rw["spo_deductable"];
        $extra_include_diner_rate_bb = $rw["include_diner_rate_bb"];
        $extra_hb_mealplan_fk = $rw["hb_mealplan_fk"];
        $extra_bb_mealplan_fk = $rw["bb_mealplan_fk"];
        $extra_extra_date = $rw["extra_date"];
        $extra_adult_count = $rw["adult_count"];
        
        if(is_null($extra_hb_mealplan_fk))
        {
            $extra_hb_mealplan_fk = "";
        }
        if(is_null($extra_bb_mealplan_fk))
        {
            $extra_bb_mealplan_fk = "";
        }

        $arr_extra[] = array("extra_rwid" => $extra_id,
            "extra_extra_name" => $extra_extra_name,
            "extra_mandatory" => $extra_mandatory,
            "extra_spo_deductable" => $extra_spo_deductable,
            "extra_include_diner_rate_bb" => $extra_include_diner_rate_bb,
            "extra_hb_mealplan_fk" => $extra_hb_mealplan_fk,
            "extra_bb_mealplan_fk" => $extra_bb_mealplan_fk,
            "extra_extra_date" => $extra_extra_date,
            "extra_adult_count" => $extra_adult_count,
            "extra_action" => "",
            "extra_children" => _contract_getMealSupplementExtraChildrenExtras($extra_id, $con));
    }
    
    return $arr_extra;
}

function _contract_getMealSupplementExtraChildrenExtras($extra_id, $con)
{

    $arr_children = array();
    
    //get children count by age range
    $sql = "SELECT * FROM tblservice_contract_extrasupplement_childages 
            WHERE service_contract_extrasupplement_fk=:extraid 
            ORDER BY child_age_from,child_age_to";

    $query_child = $con->prepare($sql);
    $query_child->execute(array(":extraid" => $extra_id));
    while ($rw_child = $query_child->fetch(PDO::FETCH_ASSOC)) {

        $child_rwid = $rw_child["id"];
        $age_from = $rw_child["child_age_from"];
        $age_to = $rw_child["child_age_to"];
        $child_count = $rw_child["child_count"];

        $arr_children[] = array("child_rwid"=>$child_rwid,
                                "child_agefrom"=>$age_from,
                                "child_ageto"=>$age_to,
                                "child_count"=>$child_count,
                                "child_action"=>"");
    }

    return $arr_children;
}

function _contract_getRoomDatesMinStayRules($con, $date_rwid) {
    
    $arr_rules = array();

    $sql = "SELECT *
            FROM tblservice_contract_minstay 
            WHERE service_contract_roomcapacity_dates_fk=:id";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":id" => $date_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $rwid = $rwvalue["id"];
        $description = $rwvalue["description"];
        $duration = $rwvalue["duration"];


        $arr_rules[] = array("minstay_rwid" => $rwid,
            "minstay_description" => $description,
            "minstay_duration" => $duration,
            "minstay_action" => "");
    }

    return $arr_rules;
}

function _contract_getRoomDatesCheckInOutRules($con, $date_rwid) {
    $arr_rules = array();


    $sql = "SELECT *
            FROM tblservice_contract_checkinout 
            WHERE service_contract_roomcapacity_dates_fk=:id 
            ORDER BY policytype ASC, time_beforeafter ASC, checkinout_time ASC";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":id" => $date_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $rwid = $rwvalue["id"];
        $policytype = $rwvalue["policytype"];
        $time_beforeafter = $rwvalue["time_beforeafter"];
        $checkinout_time = $rwvalue["checkinout_time"];
        $charge_type = $rwvalue["charge_type"];
        $charge_value = $rwvalue["charge_value"];


        $arr_rules[] = array("checkinout_rwid" => $rwid,
            "checkinout_policytype" => $policytype,
            "checkinout_time_beforeafter" => $time_beforeafter,
            "checkinout_checkinout_time" => $checkinout_time,
            "checkinout_charge_type" => $charge_type,
            "checkinout_charge_value" => $charge_value,
            "checkinout_action" => "");
    }


    return $arr_rules;
}

function _contract_getRoomDatesCancellationRules($con, $date_rwid) {
    $arr_rules = array();


    $sql = "SELECT id, canceltype, charge_method, charge_value,
            IFNULL(days_before_arrival_from,'') AS days_before_arrival_from,
            IFNULL(days_before_arrival_to,'') AS days_before_arrival_to,
            IFNULL(dates_before_arrival_from,'') AS dates_before_arrival_from,
            IFNULL(dates_before_arrival_to,'') AS dates_before_arrival_to
            FROM tblservice_contract_cancellation 
            WHERE service_contract_roomcapacity_dates_fk=:id";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":id" => $date_rwid));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $rwid = $rwvalue["id"];
        $canceltype = $rwvalue["canceltype"];
        $charge_method = $rwvalue["charge_method"];
        $charge_value = $rwvalue["charge_value"];
        $days_before_arrival_from = $rwvalue["days_before_arrival_from"];
        $days_before_arrival_to = $rwvalue["days_before_arrival_to"];
        $dates_before_arrival_from = $rwvalue["dates_before_arrival_from"];
        $dates_before_arrival_to = $rwvalue["dates_before_arrival_to"];



        $arr_rules[] = array("cancellation_rwid" => $rwid,
            "cancellation_canceltype" => $canceltype,
            "cancellation_charge_method" => $charge_method,
            "cancellation_charge_value" => $charge_value,
            "cancellation_days_before_arrival_from" => $days_before_arrival_from,
            "cancellation_days_before_arrival_to" => $days_before_arrival_to,
            "cancellation_dates_before_arrival_from" => $dates_before_arrival_from,
            "cancellation_dates_before_arrival_to" => $dates_before_arrival_to,
            "cancellation_action" => "");
    }



    return $arr_rules;
}

?>
