<?php

        
function _spo_loadspo($con, $spoid, $hotelfk) {
    $arr_spo = array();
    
    $arr_spo["GENERAL"] = _load_spo_general($con,$spoid);
    $arr_spo["APPLICABLE_CHILDSUPP_OWN"] = _load_childsupp_own_sharing($con,$spoid, "OWN");
    $arr_spo["APPLICABLE_CHILDSUPP_SHARING"] = _load_childsupp_own_sharing($con,$spoid, "SHARING");
    $arr_spo["COUNTRIES"] = _load_countries($con,$spoid);
    $arr_spo["FAMILY_OFFER_CHLDRENAGE"] = _load_family_offer_childrenage($con,$spoid);
    $arr_spo["FREE_NIGHTS"] = _load_free_nights($con,$spoid);
    $arr_spo["FREE_NIGHTS_VALIDITY"] = _load_free_nights_validity($con,$spoid);
    $arr_spo["MEAL_PLAN"] = _load_mealplan($con,$spoid);
    $arr_spo["ROOMS"] = _load_rooms($con,$spoid);
    $arr_spo["UPGRADE_MEALS"] = _load_upgrade_meal($con,$spoid);
    $arr_spo["UPGRADE_ROOMS"] = _load_upgrade_rooms($con,$spoid);
    $arr_spo["VALIDITY_PERIODS"] = _load_validity_periods($con,$spoid);
    $arr_spo["FLAT_RATES_TAX_COMMI"] = _load_flat_rate_tax_commi($con,$spoid);
    $arr_spo["FLAT_RATES_VALIDY_PERIOD_GROUP"] = _load_flat_rate_validity_period_group($con,$spoid);
    $arr_spo["FLAT_RATES_CHECKINOUT"] = _load_flat_rate_checkinout($con,$spoid);
    $arr_spo["FLAT_RATES_CANCELLATION"] = _load_flat_rate_cancellation($con,$spoid);
    $arr_spo["FLAT_RATES_SUPPLEMENTS"] = _load_flat_rate_supplements($con,$spoid);
    $arr_spo["FLAT_RATES_CURRENCY_BUY"] = _load_flat_rate_currency($con,$spoid,"BUY");
    $arr_spo["FLAT_RATES_CURRENCY_SELL"] = _load_flat_rate_currency($con,$spoid,"SELL");
    $arr_spo["FLAT_RATES_EXCHANGE_RATES"] = _load_flat_rate_exgrates($con,$spoid);
    $arr_spo["FLAT_RATES_MAPPING"] = _load_flat_rate_mapping($con,$spoid);
    $arr_spo["FLAT_RATES_CAPACITY"] = _load_flat_rate_capacity($con,$spoid);
      
        
    return $arr_spo;
}


function _load_flat_rate_tax_commi($con,$spoid)
{
    return _spo_taxcommi($con, $spoid);
}

function _load_countries($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT soc.*, c.countrycode_3, c.countrycode_2, c.country_name
            FROM tblspecial_offer_countries soc 
            INNER JOIN tblcountries c on soc.country_fk = c.id
            WHERE spo_fk=:id;";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr;
}



function _load_spo_general($con,$spoid)
{
    $sql = "SELECT *,
            DATE_FORMAT(booking_before_date_from,'%d-%m-%Y') AS booking_before_date_from,
            DATE_FORMAT(booking_before_date_to,'%d-%m-%Y') AS booking_before_date_to,
            
            (
            SELECT group_concat(country_fk SEPARATOR ',')
            FROM tblspecial_offer_countries soc 
            INNER JOIN tblcountries c on soc.country_fk = c.id
            WHERE spo_fk=:id 
            group by spo_fk ) as market_countries_ids,

            (SELECT group_concat(country_name SEPARATOR ' , ')
            FROM tblspecial_offer_countries soc 
            INNER JOIN tblcountries c on soc.country_fk = c.id
            WHERE spo_fk=:id
            group by spo_fk ) as market_countries_display,


            (SELECT group_concat(roomfk SEPARATOR ',')
            FROM tblspecial_offer_rooms sor
            INNER JOIN tblhotel_rooms hr on sor.roomfk = hr.id
            WHERE spo_fk=:id 
            group by spo_fk) as rooms_ids,

            (SELECT group_concat(roomname SEPARATOR ' , ')
            FROM tblspecial_offer_rooms sor
            INNER JOIN tblhotel_rooms hr on sor.roomfk = hr.id
            WHERE sor.spo_fk=:id group by spo_fk) as rooms_display,

            (SELECT group_concat(mealplanfk SEPARATOR ',')
            FROM tblspecial_offer_mealplan som
            INNER JOIN tblmealplans m on som.mealplanfk = m.id
            WHERE som.spo_fk=:id group by spo_fk) as meals_ids,

            (SELECT group_concat(meal SEPARATOR ' , ')
            FROM tblspecial_offer_mealplan som
            INNER JOIN tblmealplans m on som.mealplanfk = m.id
            WHERE som.spo_fk=:id group by spo_fk) as meal_display,

            (SELECT group_concat(child_age_fk SEPARATOR ',') 
            FROM tblspecial_offer_applicable_childsupp_sharing sop
            INNER JOIN tblchildrenagerange ca 
            ON sop.child_age_fk = ca.id
            WHERE spo_fk=:id group by spo_fk) AS child_supp_sharing_ids,

            (select group_concat(CONCAT(agefrom, '-', ageto) ORDER BY agefrom ASC SEPARATOR ' , ')
            FROM tblspecial_offer_applicable_childsupp_sharing sop
            INNER JOIN tblchildrenagerange ca 
            ON sop.child_age_fk = ca.id
            WHERE spo_fk=:id group by spo_fk) AS child_supp_sharing,

            (SELECT group_concat(child_age_fk SEPARATOR ',') 
            FROM tblspecial_offer_applicable_childsupp_own sop
            INNER JOIN tblchildrenagerange ca 
            ON sop.child_age_fk = ca.id
            WHERE spo_fk=:id group by spo_fk) AS child_supp_own_ids,

            (select group_concat(CONCAT(agefrom, '-', ageto) ORDER BY agefrom ASC SEPARATOR ' , ')
            FROM tblspecial_offer_applicable_childsupp_own sop
            INNER JOIN tblchildrenagerange ca 
            ON sop.child_age_fk = ca.id
            WHERE spo_fk=:id group by spo_fk) AS child_supp_own,
            
            (select group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR ' , ') 
             FROM tblspecial_offer_touroperator sots
             inner join tbltouroperator tp on sots.tofk = tp.id
             WHERE spofk=:id group by spofk
            ) as tour_operators_display,
            
            (select group_concat(tp.id ORDER BY tp.toname ASC SEPARATOR ',')
             FROM tblspecial_offer_touroperator sots
             inner join tbltouroperator tp on sots.tofk = tp.id
             WHERE spofk=:id group by spofk
            ) as tour_operators_ids
            

            FROM tblspecial_offer WHERE id=:id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        return $rw;
    }
    
    return [];

}

function _load_childsupp_own_sharing($con,$spoid, $own_sharing)
{
    $arr = array();
    
    $table_name = "";
    if($own_sharing == "OWN")
    {
        $table_name = "tblspecial_offer_applicable_childsupp_own";
    }
    else
    {
        $table_name = "tblspecial_offer_applicable_childsupp_sharing";
    }
    
    $sql = "SELECT sop.*, ca.agefrom, ca.ageto
            FROM $table_name sop LEFT JOIN tblchildrenagerange ca 
            ON sop.child_age_fk = ca.id
            WHERE spo_fk=:id ";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] = $rw;
    }
    
    return $arr;
}


function _load_family_offer_childrenage($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT *
            FROM tblspecial_offer_familyoffer_childage_discount 
            WHERE spo_fk=:id";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr;
}

function _load_free_nights($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT *
            FROM tblspecial_offer_freenights 
            WHERE spo_fk=:id";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr;
}

function _load_free_nights_validity($con,$spoid)
{
   $arr = array();
    
    $sql = "SELECT id, 
            DATE_FORMAT(valid_from,'%d-%m-%Y') AS valid_from,
            DATE_FORMAT(valid_to,'%d-%m-%Y') AS valid_to
            FROM tblspecial_offer_freenights_validity_periods 
            WHERE spo_fk=:id";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_mealplan($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT som.*, m.meal, m.mealfullname,m.description
            FROM tblspecial_offer_mealplan som
            INNER JOIN tblmealplans m on som.mealplanfk = m.id
            WHERE som.spo_fk=:id";
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_rooms($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT sor.*, hr.roomname,hr.description,hr.numbedrooms
            FROM tblspecial_offer_rooms sor
            INNER JOIN tblhotel_rooms hr on sor.roomfk = hr.id
            WHERE sor.spo_fk=:id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_upgrade_meal($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT *
            FROM tblspecial_offer_upgrade_meal
            WHERE spo_fk=:id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_upgrade_rooms($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT *
            FROM tblspecial_offer_upgrade_rooms
            WHERE spo_fk=:id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_validity_periods($con,$spoid)
{
   $arr = array();
    
    $sql = "SELECT vp.id, vp.spo_fk, vp.valid_from, vp.valid_to, 
            ifnull(s.season,'CUSTOM') as season, 
            ifnull(vp.season_fk,'') as season_fk
            FROM tblspecial_offer_validityperiods vp
            LEFT JOIN tblseasons s ON vp.season_fk = s.id
            WHERE vp.spo_fk=:id
            order by vp.valid_from desc";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr;  
}

function _load_flat_rate_validity_period_group($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT spogvp.id, spogvp.spo_fk,spogvp.dt_from,spogvp.dt_to,spogvp.groupno,
            GROUP_CONCAT(spogvpca.childage_fk SEPARATOR ',') AS child_age_ids,
            GROUP_CONCAT(CONCAT(car.agefrom,'-',car.ageto) ORDER BY car.agefrom ASC SEPARATOR ' , ') AS child_age_display
            FROM 
            tblspecial_offer_flatrate_group_validity_period spogvp
            LEFT JOIN 
            tblspecial_offer_flatrate_group_validity_period_childages spogvpca
            ON spogvp.id = spogvpca.spo_fltrate_grp_valid_period_fk
            LEFT JOIN tblchildrenagerange car ON spogvpca.childage_fk = car.id
            WHERE 
            spogvp.spo_fk = :id
            GROUP BY spogvp.spo_fk,spogvp.dt_from,spogvp.dt_to,spogvp.groupno,spogvp.id
            ORDER BY spogvp.dt_from,spogvp.dt_to";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr;  
}


function _load_flat_rate_checkinout($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT * FROM tblspecial_offer_flatrate_checkinout 
            WHERE spofk=:id ORDER BY 
            checkinout_type ASC, time_before_after ASC, time_checkinout ASC";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        
        $arr_rooms = _load_flatrate_checkinout_cancellation_rooms($con,$rw["id"],"CHECKINOUT");
        $rw["room_names"] = $arr_rooms["room_names"];
        $rw["room_ids"] = $arr_rooms["room_ids"];
        $rw["date_periods"] = _load_flatrate_supp_checkinout_cancellation_dateperiods($con,$rw["id"],"CHECKINOUT");
        $arr[] =  $rw;
    }
    
    return $arr;  
}

function _load_flat_rate_cancellation($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT id,spofk,cancellation_type,charge_basis,
            charge_value,
            IFNULL(days_before_arrival_from, '') AS days_before_arrival_from,
            IFNULL(days_before_arrival_to, '') AS days_before_arrival_to,
            IFNULL(dates_before_arrival_from, '') AS dates_before_arrival_from,
            IFNULL(dates_before_arrival_to, '') AS dates_before_arrival_to            
            FROM tblspecial_offer_flatrate_cancellation 
            WHERE spofk=:id ORDER BY id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        
        $arr_rooms = _load_flatrate_checkinout_cancellation_rooms($con,$rw["id"],"CANCELLATION");
        $rw["room_names"] = $arr_rooms["room_names"];
        $rw["room_ids"] = $arr_rooms["room_ids"];
        $rw["date_periods"] = _load_flatrate_supp_checkinout_cancellation_dateperiods($con,$rw["id"],"CANCELLATION");
        $arr[] =  $rw;
    }
    
    return $arr;  
}

function _load_flat_rate_supplements($con,$spoid)
{
    $arr = array();
    
    $sql = "SELECT * FROM tblspecial_offer_flatrate_mealsupp 
            WHERE spofk=:id ORDER BY id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        
        $rw["children_count"] = _load_flatrate_supp_children_count($con,$rw["id"]);
        $rw["date_periods"] = _load_flatrate_supp_checkinout_cancellation_dateperiods($con,$rw["id"],"SUPPLEMENTS");
        $arr[] =  $rw;
    }
    
    return $arr;  
}

function _load_flatrate_supp_checkinout_cancellation_dateperiods($con,$id, $type)
{
    $table_name = "";
    $fk = "";
    if($type == "CHECKINOUT")
    {
        $table_name = "tblspecial_offer_flatrate_checkinout_dateperiods";
        $fk = "spo_ftrte_checkinout_fk";
    }
    else if($type == "CANCELLATION")
    {
        $table_name = "tblspecial_offer_flatrate_cancellation_dateperiods";
        $fk = "spo_ftrte_cancellation_fk";
    }
    else if($type == "SUPPLEMENTS")
    {
        $table_name = "tblspecial_offer_flatrate_mealsupp_dateperiods";
        $fk = "spo_ftrte_mealsupp_fk";
    }
    
    $dates = "";
    $sql = "select $fk,
            group_concat(concat(DATE_FORMAT(valid_from,'%d-%m-%Y'), ' - ', DATE_FORMAT(valid_to,'%d-%m-%Y') ) ORDER BY valid_from ASC SEPARATOR '<br>') as date_periods
            from $table_name
            WHERE $fk = $id
            group by $fk";
     
     $query_parent = $con->prepare($sql);
     $query_parent->execute(array(":id" => $id));

    if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $dates = $rw["date_periods"];
    }
    
    return $dates;
}

function _load_flatrate_checkinout_cancellation_rooms($con,$id,$type)
{
    $table_name = "";
    $fk = "";
    
    if($type == "CHECKINOUT")
    {
        $table_name = "tblspecial_offer_flatrate_checkinout_rooms";
        $fk = "spo_ftrte_checkinout_fk";
    }
    else if($type == "CANCELLATION")
    {
        $table_name = "tblspecial_offer_flatrate_cancellation_rooms";
        $fk = "spo_ftrte_cancellation_fk";
    }
    
    $arr_rooms = array();
    $arr_rooms["room_ids"] = "";
    $arr_rooms["room_names"] = "";
    
    $sql = "select $fk,
            group_concat(a.roomfk SEPARATOR ',') as room_ids,
            group_concat(b.roomname SEPARATOR ',') as room_names
            from 
            $table_name a
            INNER JOIN tblhotel_rooms b ON a.roomfk = b.id
            WHERE $fk = $id
            group by $fk";
    
     $query_parent = $con->prepare($sql);
     $query_parent->execute(array(":id" => $id));

     if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr_rooms["room_ids"] = $rw["room_ids"];
        $arr_rooms["room_names"] = $rw["room_names"];
     }
     
     return $arr_rooms;
     
}


function _load_flatrate_supp_children_count($con,$id)
{
    $sql = "SELECT * FROM 
            tblspecial_offer_flatrate_mealsupp_children_ages
            WHERE spo_ftrte_mealsupp_fk=$id";
    
    $arr = array();
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $id));

    while ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $arr[] =  $rw;
    }
    
    return $arr; 
}

function _load_flat_rate_currency($con,$spoid,$buysell)
{   
    
    $ids = "";
    $display = "";
    $sql = "select sc.id,
            group_concat(c.id ORDER BY c.currency_code separator ',') as selected_currency_ids,
            group_concat(c.currency_code ORDER BY c.currency_code separator ' , ') as selected_currency_display
            from tblspecial_offer sc
            inner join tblspecial_offer_flatrate_currency scc on sc.id = scc.spo_fk
            inner join tblcurrency c on scc.currencyfk = c.id
            where sc.id = $spoid and scc.buy_sell = '$buysell'
            group by sc.id";
    
    $query_parent = $con->prepare($sql);
    $query_parent->execute(array(":id" => $spoid));

    if ($rw = $query_parent->fetch(PDO::FETCH_ASSOC)) {
        $ids = $rw["selected_currency_ids"];
        $display = $rw["selected_currency_display"]; 
    }
    
    $arr = array();
    $arr["currency_ids"] = $ids;
    $arr["currency_display"] = $display;
    
    return $arr; 
}


function _load_flat_rate_exgrates($con,$spoid)
{
    $arr_rates = array();

    $sql = "SELECT a.*, ca.currency_code as currency_code_from,
            cb.currency_code as currency_code_to
            FROM 
            tblspecial_offer_flatrate_currency_exchangerates a
            INNER JOIN tblcurrency ca on a.from_currencyfk = ca.id
            INNER JOIN tblcurrency cb on a.to_currencyfk = cb.id
            WHERE spo_fk=$spoid
            ORDER BY from_currencyfk, to_currencyfk";
    $stmt_rates = $con->prepare($sql);
    $stmt_rates->execute();
    while ($rwrates = $stmt_rates->fetch(PDO::FETCH_ASSOC)) {

        $arr_rates[] =  $rwrates;
    }

    return $arr_rates;
}

function _load_flat_rate_mapping($con,$spoid)
{
    $arr_rates = array();

    $sql = "SELECT a.*, ca.currency_code as currency_code_buy,
            cb.currency_code as currency_code_sell
            FROM 
            tblspecial_offer_flatrate_currency_mapping a
            INNER JOIN tblcurrency ca on a.currencybuy_fk = ca.id
            INNER JOIN tblcurrency cb on a.currencysell_fk = cb.id
            WHERE a.spo_fk=$spoid
            ORDER BY currencybuy_fk, currencysell_fk;";
    $stmt_rates = $con->prepare($sql);
    $stmt_rates->execute();
    while ($rwrates = $stmt_rates->fetch(PDO::FETCH_ASSOC)) {
        $arr_rates[] =  $rwrates;
    }

    return $arr_rates;
}


function _load_flat_rate_capacity($con,$spoid)
{
    //get all rooms defined at spo level

    $arr_room = array();

    $sql = "select hr.*, scr.id as room_rwid, 
            scrc.variant as variant,
            scrc.id as spo_roomcapacity_id
            from tblspecial_offer_rooms  scr
            inner join tblhotel_rooms hr on scr.roomfk = hr.id
            inner join tblspecial_offer_flatrate_roomcapacity scrc on scrc.roomfk = scr.roomfk and scrc.spo_fk = :spoid
            where scr.spo_fk = :spoid
            order by hr.roomname asc";
    $stmtroom = $con->prepare($sql);
    $stmtroom->execute(array(":spoid" => $spoid));
    while ($rwroom = $stmtroom->fetch(PDO::FETCH_ASSOC)) {

        $room_rwid = $rwroom["spo_roomcapacity_id"];
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
            "room_dates" => _load_capacity_room_dates($con, $room_rwid, $room_id, $spoid));
    }
    return $arr_room;
}

function _load_capacity_room_dates($con, $room_rwid , $room_id, $spoid)
{
    $arr_dates = array();
    $sql = "SELECT 
            IFNULL(override_dtfrom,'') as override_dtfrom,
            IFNULL(override_dtto,'') as override_dtto,
            spo_flatrates_roomcapacity_fk,id
            FROM tblspecial_offer_flatrate_roomcapacity_dates 
            WHERE spo_flatrates_roomcapacity_fk=:room_rwid 
            ORDER BY override_dtfrom ASC, override_dtto ASC";

    $stmtdates = $con->prepare($sql);
    $stmtdates->execute(array(":room_rwid" => $room_rwid));
    while ($rwdates = $stmtdates->fetch(PDO::FETCH_ASSOC)) {
        $date_rwid = $rwdates["id"];
        $date_dtfrom = $rwdates["override_dtfrom"];
        $date_dtto = $rwdates["override_dtto"];

        $arr_dates[] = array("date_rwid" => $date_rwid,
            "date_dtfrom" => $date_dtfrom,
            "date_dtto" => $date_dtto,
            "date_action" => "",
            "date_minstay_rules"=>array(), //dummy to look like contract
            "date_mealextrasupplement_rules"=>array(), //dummy to look like contract
            "date_policies_cancellation"=>_load_capacity_room_dates_capacityrules_cancellation($con, $date_dtfrom, $date_dtto, $room_id, $spoid),
            "date_policies_checkinout" => _load_capacity_room_dates_capacityrules_checkinout($con, $date_dtfrom, $date_dtto, $room_id, $spoid),
            "date_mealsupplement_rules" => _load_capacity_room_dates_capacityrules_meal_supplement($con, $date_dtfrom, $date_dtto, $spoid),
            "date_capacity_rules" => _load_capacity_room_dates_capacityrules($con, $date_rwid),
            "date_adultpolicies_rules" => _load_capacity_room_dates_capacityrules_adultpoliciesrules($con, $date_rwid),
            "date_childpolicies_rules" => _load_capacity_room_dates_capacityrules_childpoliciesrules($con, $date_rwid),
            "date_singleparentpolicies_rules" => _load_capacity_room_dates_capacityrules_singleparentpoliciesrules($con, $date_rwid));
    }

    return $arr_dates;
}


function _load_capacity_room_dates_capacityrules_cancellation($con, $date_dtfrom, $date_dtto, $room_id, $spoid)
{
    $arr_rules = array();
    
    $sql = "select * from tblspecial_offer_flatrate_cancellation
            where spofk = :spoid and 
            id IN 
            (
                SELECT spo_ftrte_cancellation_fk 
                FROM tblspecial_offer_flatrate_cancellation_rooms
                WHERE roomfk=:roomid 
            )
            and id IN 
            (
                select spo_ftrte_cancellation_fk from 
                tblspecial_offer_flatrate_cancellation_dateperiods
                where valid_from=:valid_from AND valid_to = :valid_to
            )";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":spoid" => $spoid,":roomid" => $room_id,
                              ":valid_from"=>$date_dtfrom,":valid_to"=>$date_dtto));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $rwid = $rwvalue["id"];
        $canceltype = $rwvalue["cancellation_type"];
        $charge_method = $rwvalue["charge_basis"];
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

function _load_capacity_room_dates_capacityrules_checkinout($con, $date_dtfrom, $date_dtto, $room_id, $spoid)
{
    $arr_rules = array();


    $sql = "select * from tblspecial_offer_flatrate_checkinout
            where spofk = :spoid and 
            id in (
            select spo_ftrte_checkinout_fk FROM tblspecial_offer_flatrate_checkinout_rooms
            WHERE roomfk=:roomid )
            and id in (select spo_ftrte_checkinout_fk from 
            tblspecial_offer_flatrate_checkinout_dateperiods
            where valid_from=:valid_from AND valid_to = :valid_to)";

    $stmtvalue = $con->prepare($sql);
    $stmtvalue->execute(array(":spoid" => $spoid,":roomid" => $room_id,
                              ":valid_from"=>$date_dtfrom,":valid_to"=>$date_dtto));

    while ($rwvalue = $stmtvalue->fetch(PDO::FETCH_ASSOC)) {
        $rwid = $rwvalue["id"];
        $policytype = $rwvalue["checkinout_type"];
        $time_beforeafter = $rwvalue["time_before_after"];
        $checkinout_time = $rwvalue["time_checkinout"];
        $charge_type = $rwvalue["charge_basis"];
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

function _load_capacity_room_dates_capacityrules_meal_supplement($con, $date_dtfrom, $date_dtto, $spoid)
{
    //get all meal supplements for that room and date ranges
    $arr_meal = array();

    $sql = "select * from tblspecial_offer_flatrate_mealsupp where spofk = :spoid
            and id in 
            (
               select spo_ftrte_mealsupp_fk 
               FROM tblspecial_offer_flatrate_mealsupp_dateperiods                
                WHERE valid_from=:valid_from AND valid_to=:valid_to
            )
            order by spofk";

    $query = $con->prepare($sql);
    $query->execute(array(":spoid" => $spoid, ":valid_from"=>$date_dtfrom, ":valid_to"=>$date_dtto));
    while ($rw = $query->fetch(PDO::FETCH_ASSOC)) {

        $meal_id = $rw["id"];
        $meal_mealplanfk = $rw["mealplanfk"];
        $meal_ismain = $rw["ismain"];
        $meal_adult_count = $rw["adult"];


        $arr_meal[] = array("meal_rwid" => $meal_id,
            "meal_mealplanfk" => $meal_mealplanfk,
            "meal_ismain" => $meal_ismain,
            "meal_adult_count" => $meal_adult_count,
            "meal_action" => "",
            "meal_children" => _load_capacity_room_dates_capacityrules_meal_supplement_children($meal_id, $con));
    }

    return $arr_meal;
}

function _load_capacity_room_dates_capacityrules_meal_supplement_children($meal_id, $con)
{
    $arr_children = array();

    //get children count by age range
    $sql = "SELECT * FROM tblspecial_offer_flatrate_mealsupp_children_ages 
            WHERE spo_ftrte_mealsupp_fk=:meal_id 
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

function _load_capacity_room_dates_capacityrules($con, $date_rwid)
{
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblspecial_offer_flatrate_roomcapacity_dates_rules 
            WHERE spo_flatrates_roomcapacity_dates_fk=:dateid 
            ORDER BY rulecounter ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];

        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_action" => "",
            "rule_capacity" => _load_capacity_room_dates_capacityrules_capacity($con, $rule_rwid));
    }

    return $arr_rules;
}

function _load_capacity_room_dates_capacityrules_capacity($con, $rule_rwid)
{
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM 
            tblspecial_offer_flatrate_roomcapacity_dates_rules_ages 
            WHERE spo_flatrates_roomcapacity_dates_rules_fk=:ruleid 
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


function _load_capacity_room_dates_capacityrules_singleparentpoliciesrules($con, $date_rwid)
{
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules 
            WHERE spo_flatrate_roomcapacity_dates_fk=:dateid 
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
            "rule_policy" => _load_capacity_room_dates_capacityrules_singleparentpoliciesrules_ages($con, $rule_rwid));
    }

    return $arr_rules;
}


function _load_capacity_room_dates_capacityrules_singleparentpoliciesrules_ages($con, $rule_rwid)
{
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages 
            WHERE spo_flatrate_snglprnt_rm_dt_rules_fk=:ruleid 
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
            "policy_values" => _load_capacity_room_dates_capacityrules_singleparentpoliciesrules_ages_values($con, $capacity_rwid));
    }

    return $arr_capacity;
}


function _load_capacity_room_dates_capacityrules_singleparentpoliciesrules_ages_values($con, $capacity_rwid)
{
    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages_values 
            WHERE spo_flatrate_snglprnt_rm_dt_rules_ages_fk=:fk";

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



function _load_capacity_room_dates_capacityrules_childpoliciesrules($con, $date_rwid)
{
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblspecial_offer_flatrate_ch_rm_dt_rules 
            WHERE spo_flatrate_roomcapacity_dates_fk=:dateid 
            ORDER BY id ASC";

    $stmtrules = $con->prepare($sql);
    $stmtrules->execute(array(":dateid" => $date_rwid));
    while ($rwrules = $stmtrules->fetch(PDO::FETCH_ASSOC)) {
        $rule_rwid = $rwrules["id"];
        $rule_rulecounter = $rwrules["rulecounter"];
        $rule_category = $rwrules["rulecategory"];
        $rule_sharing_single = $rwrules["sharing_single"];

        $arr_rules[] = array("rule_rwid" => $rule_rwid,
            "rule_rulecounter" => $rule_rulecounter,
            "rule_category" => $rule_category,
            "rule_sharing_single" => $rule_sharing_single,
            "rule_action" => "",
            "rule_policy" => _load_capacity_room_dates_capacityrules_childpoliciesrules_ages($con, $rule_rwid));
    }

    return $arr_rules;
}

function _load_capacity_room_dates_capacityrules_childpoliciesrules_ages($con, $rule_rwid)
{
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM 
            tblspecial_offer_flatrate_ch_rm_dt_rules_ages 
            WHERE spo_flatrate_childpolicy_room_dates_rules_fk=:ruleid 
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
            "policy_values" => _load_capacity_room_dates_capacityrules_childpoliciesrules_ages_values($con, $capacity_rwid));
    }

    return $arr_capacity;
}


function _load_capacity_room_dates_capacityrules_childpoliciesrules_ages_values($con, $capacity_rwid)
{
    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM tblspecial_offer_flatrate_ch_rm_dt_rules_ages_values 
            WHERE spo_flatrate_child_policy_room_dates_rules_ages_fk=:fk";

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

function _load_capacity_room_dates_capacityrules_adultpoliciesrules($con, $date_rwid)
{
    $arr_rules = array();
    $sql = "SELECT * 
            FROM tblspecial_offer_flatrate_ad_rm_dt_rules 
            WHERE spo_flatrate_roomcapacity_dates_fk=:dateid 
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
            "rule_policy" => _load_capacity_room_dates_capacityrules_adultpoliciesrules_ages($con, $rule_rwid));
    }

    return $arr_rules;
}

function _load_capacity_room_dates_capacityrules_adultpoliciesrules_ages($con, $rule_rwid)
{
    //get the capacity for that room and that date period
    $arr_capacity = array();
    $sql = "SELECT * FROM 
            tblspecial_offer_flatrate_ad_rm_dt_rules_ages 
            WHERE spo_flatrate_adultpolicy_room_dates_rules_fk=:ruleid 
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
            "policy_values" => _load_capacity_room_dates_capacityrules_adultpoliciesrules_ages_values($con, $capacity_rwid));
    }

    return $arr_capacity;
}

function _load_capacity_room_dates_capacityrules_adultpoliciesrules_ages_values($con, $capacity_rwid)
{
    $arr_values = array();
    $sql = "SELECT *, IFNULL(currencyfk,'') AS mycurrencyfk
            FROM 
            tblspecial_offer_flatrate_ad_rm_dt_rules_ages_values 
            WHERE 
            spo_fte_ad_rm_dt_rules_ag_fk=:fk";

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
?>
