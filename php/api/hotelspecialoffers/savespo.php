<?php

try {

//=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
    set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }
        throw new Exception($errstr . " " . $errno);
    });


    session_start();

    require_once("../../connector/pdo_connect_main.php");
    require_once("../../utils/utilities.php");

    $con = pdo_con();

    $con->beginTransaction();

    if (!isset($_SESSION["solis_userid"])) {
        throw new Exception("NO LOG IN!");
    }

    if (!isset($_POST["token"])) {
        throw new Exception("INVALID TOKEN");
    }
    if ($_POST["token"] != $_SESSION["token"]) {
        throw new Exception("INVALID TOKEN");
    }

    //NAME 

    $name = json_decode($_POST["name"], true); //+

    $id = $name["id"];
    $hotelfk = $name["hotel_fk"];
    $active_internal = $name["active_internal"];
    $active_external = $name["active_external"];
    $non_refundable = $name["non_refundable"];
    $spo_type = $name["spo_type"];
    $sponame = $name["sponame"];
    $spocode = $name["spocode"];
    $template = $name["template"];
    $rooms_ids = $name["rooms_ids"];
    $market_countries_ids = $name["market_countries_ids"];
    $toids = $name["tour_operators_ids"];


    //===========================================================================

    if ($id == "-1") {

        $sql = "INSERT INTO tblspecial_offer
                (service_code,date_created) 
                VALUES ('ACC',NOW())";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        $id = $con->lastInsertId();
    }


    //=============================================


    $sql = "UPDATE tblspecial_offer SET 
            sponame=:sponame, spocode=:spocode, template=:template,
            active_internal=:active_internal, active_external=:active_external,
            non_refundable=:non_refundable,
            hotel_fk=:hotel_fk, spo_type=:spo_type, deleted=0
            WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":sponame" => $sponame,
        ":spocode" => $spocode,
        ":template" => $template,
        ":active_internal" => $active_internal,
        ":active_external" => $active_external,
        ":non_refundable" => $non_refundable,
        ":hotel_fk" => $hotelfk,
        ":spo_type" => $spo_type));

    $outcome = saverooms($rooms_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    $outcome = savecountries($market_countries_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    $outcome = savetouroperators($toids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //=========================================================================
    //PERIODS

    $periods = json_decode($_POST["periods"], true);
    $rate_fk = $periods["rate_fk"];
    $booking_before_date = $periods["booking_before_date"];
    $booking_before_days = $periods["booking_before_days"];

    $booking_before_date_from = utils_stringBlank($periods["booking_before_date_from"], null);
    if (!is_null($booking_before_date_from)) {
        $booking_before_date_from = date("Y-m-d", strtotime($booking_before_date_from));
    }


    $booking_before_date_to = utils_stringBlank($periods["booking_before_date_to"], null);
    if (!is_null($booking_before_date_to)) {
        $booking_before_date_to = date("Y-m-d", strtotime($booking_before_date_to));
    }


    $booking_before_days_from = utils_stringBlank($periods["booking_before_days_from"], null);
    $booking_before_days_to = utils_stringBlank($periods["booking_before_days_to"], null);

    $sql = "UPDATE tblspecial_offer SET 
            rate_fk=:rate_fk, 
            booking_before_date_from=:booking_before_date_from,
            booking_before_date_to=:booking_before_date_to,
            booking_before_days_from=:booking_before_days_from, 
            booking_before_days_to=:booking_before_days_to,
            booking_before_date=:booking_before_date,
            booking_before_days=:booking_before_days
            WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":rate_fk" => $rate_fk,
        ":booking_before_date_from" => $booking_before_date_from,
        ":booking_before_date_to" => $booking_before_date_to,
        ":booking_before_days_from" => $booking_before_days_from,
        ":booking_before_days_to" => $booking_before_days_to,
        ":booking_before_date" => $booking_before_date,
        ":booking_before_days" => $booking_before_days));


    $period_validity = json_decode($_POST["period_validity"], true); //+


    $outcome = saveperiodvalidity($period_validity);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //=========================================================================
    //CONDITIONS
    $conditions = json_decode($_POST["conditions"], true);
    $meals_ids = $conditions["meals_ids"];
    $min_stay_from = utils_stringBlank($conditions["min_stay_from"], null);
    $min_stay_to = utils_stringBlank($conditions["min_stay_to"], null);
    $min_stay_priority = utils_stringBlank($conditions["min_stay_priority"], null);
    $conditions_text = $conditions["conditions_text"];
    $added_values_text = $conditions["added_values_text"];

    $sql = "UPDATE tblspecial_offer SET min_stay_priority=:min_stay_priority,
            min_stay_from=:min_stay_from, 
            min_stay_to=:min_stay_to, conditions_text=:conditions_text, 
            added_values_text=:added_values_text WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":min_stay_priority"=>$min_stay_priority,
        ":min_stay_from" => $min_stay_from,
        ":min_stay_to" => $min_stay_to,
        ":conditions_text" => $conditions_text,
        ":added_values_text" => $added_values_text));


    $outcome = savemeals($meals_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }


    //========================================================================
    //APPLICABLE
    $applicable = json_decode($_POST["applicable"], true);

    $child_supp_sharing_ids = $applicable["child_supp_sharing_ids"];
    $child_supp_own_ids = $applicable["child_supp_own_ids"];

    $outcome = saveapplicable_childages($child_supp_sharing_ids, "SHARING");
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    $outcome = saveapplicable_childages($child_supp_own_ids, "OWN");
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //=========================================================================
    //DISCOUNTS
    $discounts = json_decode($_POST["discounts"], true);
    $discount_basis = utils_stringBlank($discounts["discount_basis"], null);
    $discount_value = utils_stringBlank($discounts["discount_value"], null);

    $sql = "UPDATE tblspecial_offer SET discount_basis=:discount_basis, 
            discount_value=:discount_value WHERE id=:id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":discount_basis" => $discount_basis,
        ":discount_value" => $discount_value));

    //=========================================================================
    //WEDDING HONEYMOON

    if ($template == "honeymoon") {

        $wedding_discounts = json_decode($_POST["wedding_discounts"], true);
        $wedding_certificate_exceed_limit_value = utils_stringBlank($wedding_discounts["wedding_certificate_exceed_limit_value"], null);
        $wedding_certificate_exceed_limit_basis = utils_stringBlank($wedding_discounts["wedding_certificate_exceed_limit_basis"], null);

        $wedding_apply_discount_both = utils_stringBlank($wedding_discounts["wedding_apply_discount_both"], null);
        $wedding_apply_discount_both_basis = utils_stringBlank($wedding_discounts["wedding_apply_discount_both_basis"], null);
        $wedding_apply_discount_both_sngl_dbl = utils_stringBlank($wedding_discounts["wedding_apply_discount_both_sngl_dbl"], null);
        $wedding_apply_discount_both_value = utils_stringBlank($wedding_discounts["wedding_apply_discount_both_value"], null);

        $wedding_apply_discount_groom = utils_stringBlank($wedding_discounts["wedding_apply_discount_groom"], null);
        $wedding_apply_discount_groom_basis = utils_stringBlank($wedding_discounts["wedding_apply_discount_groom_basis"], null);
        $wedding_apply_discount_groom_sngl_dbl = utils_stringBlank($wedding_discounts["wedding_apply_discount_groom_sngl_dbl"], null);
        $wedding_apply_discount_groom_value = utils_stringBlank($wedding_discounts["wedding_apply_discount_groom_value"], null);

        $wedding_apply_discount_bride = utils_stringBlank($wedding_discounts["wedding_apply_discount_bride"], null);
        $wedding_apply_discount_bride_basis = utils_stringBlank($wedding_discounts["wedding_apply_discount_bride_basis"], null);
        $wedding_apply_discount_bride_sngl_dbl = utils_stringBlank($wedding_discounts["wedding_apply_discount_bride_sngl_dbl"], null);
        $wedding_apply_discount_bride_value = utils_stringBlank($wedding_discounts["wedding_apply_discount_bride_value"], null);

        $sql = "UPDATE tblspecial_offer SET 
            wedding_certificate_exceed_limit_basis=:wedding_certificate_exceed_limit_basis, 
            wedding_certificate_exceed_limit_value=:wedding_certificate_exceed_limit_value,
            wedding_apply_discount_both=:wedding_apply_discount_both,
            wedding_apply_discount_groom=:wedding_apply_discount_groom,
            wedding_apply_discount_bride=:wedding_apply_discount_bride,
            wedding_apply_discount_both_basis=:wedding_apply_discount_both_basis,
            wedding_apply_discount_both_value=:wedding_apply_discount_both_value,
            wedding_apply_discount_both_sngl_dbl=:wedding_apply_discount_both_sngl_dbl,
            wedding_apply_discount_groom_basis=:wedding_apply_discount_groom_basis,
            wedding_apply_discount_groom_value=:wedding_apply_discount_groom_value,
            wedding_apply_discount_groom_sngl_dbl=:wedding_apply_discount_groom_sngl_dbl,
            wedding_apply_discount_bride_basis=:wedding_apply_discount_bride_basis,
            wedding_apply_discount_bride_value=:wedding_apply_discount_bride_value,
            wedding_apply_discount_bride_sngl_dbl=:wedding_apply_discount_bride_sngl_dbl
            WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":wedding_certificate_exceed_limit_basis" => $wedding_certificate_exceed_limit_basis,
            ":wedding_certificate_exceed_limit_value" => $wedding_certificate_exceed_limit_value,
            ":wedding_apply_discount_both" => $wedding_apply_discount_both,
            ":wedding_apply_discount_groom" => $wedding_apply_discount_groom,
            ":wedding_apply_discount_bride" => $wedding_apply_discount_bride,
            ":wedding_apply_discount_both_basis" => $wedding_apply_discount_both_basis,
            ":wedding_apply_discount_both_value" => $wedding_apply_discount_both_value,
            ":wedding_apply_discount_both_sngl_dbl" => $wedding_apply_discount_both_sngl_dbl,
            ":wedding_apply_discount_groom_basis" => $wedding_apply_discount_groom_basis,
            ":wedding_apply_discount_groom_value" => $wedding_apply_discount_groom_value,
            ":wedding_apply_discount_groom_sngl_dbl" => $wedding_apply_discount_groom_sngl_dbl,
            ":wedding_apply_discount_bride_basis" => $wedding_apply_discount_bride_basis,
            ":wedding_apply_discount_bride_value" => $wedding_apply_discount_bride_value,
            ":wedding_apply_discount_bride_sngl_dbl" => $wedding_apply_discount_bride_sngl_dbl));
    }


    //=========================================================================
    //FREE NIGHTS
    if ($template == "free_nights") {
        $free_nights = json_decode($_POST["free_nights"], true);
        $free_nights_cumulative = $free_nights["free_nights_cumulative"];
        $free_nights_placed_at = $free_nights["free_nights_placed_at"];

        $sql = "UPDATE tblspecial_offer SET 
            free_nights_cumulative=:free_nights_cumulative, 
            free_nights_placed_at=:free_nights_placed_at
            WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":free_nights_cumulative" => $free_nights_cumulative,
            ":free_nights_placed_at" => $free_nights_placed_at));


        $free_nights_validity = json_decode($_POST["free_nights_validity"], true); //+
        $outcome = savefreenightvalidity($free_nights_validity);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        $free_nights_grid = json_decode($_POST["free_nights_grid"], true); //+
        $outcome = savefreenights($free_nights_grid);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }
    }


    //=========================================================================
    //ROOM UPGRADE
    if ($template == "free_upgrade") {
        $room_upgrade = json_decode($_POST["room_upgrade"], true);
        $outcome = saveroomupgrade($room_upgrade);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }
    }
    //=========================================================================
    //WEDDING ANNIVERSARY
    if ($template == "wedding_anniversary") {
        $wedding_anniversary = json_decode($_POST["wedding_anniversary"], true);

        $wedding_date_before_value = utils_DMY_YMD($wedding_anniversary["wedding_date_before_value"], null);
        $wedding_date_before_basis = utils_stringBlank($wedding_anniversary["wedding_date_before_basis"], null);

        $wedding_date_after_value = utils_DMY_YMD($wedding_anniversary["wedding_date_after_value"], null);
        $wedding_date_after_basis = utils_stringBlank($wedding_anniversary["wedding_date_after_basis"], null);

        $wedding_anniversary_applicable_value = utils_stringBlank($wedding_anniversary["wedding_anniversary_applicable_value"], null);
        $wedding_anniversary_applicable_basis = utils_stringBlank($wedding_anniversary["wedding_anniversary_applicable_basis"], null);

        $wedding_apply_discount_both = utils_stringBlank($wedding_anniversary["wedding_apply_discount_both"], null);
        $wedding_apply_discount_both_basis = utils_stringBlank($wedding_anniversary["wedding_apply_discount_both_basis"], null);
        $wedding_apply_discount_both_sngl_dbl = utils_stringBlank($wedding_anniversary["wedding_apply_discount_both_sngl_dbl"], null);
        $wedding_apply_discount_both_value = utils_stringBlank($wedding_anniversary["wedding_apply_discount_both_value"], null);

        $wedding_apply_discount_groom = utils_stringBlank($wedding_anniversary["wedding_apply_discount_groom"], null);
        $wedding_apply_discount_groom_basis = utils_stringBlank($wedding_anniversary["wedding_apply_discount_groom_basis"], null);
        $wedding_apply_discount_groom_sngl_dbl = utils_stringBlank($wedding_anniversary["wedding_apply_discount_groom_sngl_dbl"], null);
        $wedding_apply_discount_groom_value = utils_stringBlank($wedding_anniversary["wedding_apply_discount_groom_value"], null);

        $wedding_apply_discount_bride = utils_stringBlank($wedding_anniversary["wedding_apply_discount_bride"], null);
        $wedding_apply_discount_bride_basis = utils_stringBlank($wedding_anniversary["wedding_apply_discount_bride_basis"], null);
        $wedding_apply_discount_bride_sngl_dbl = utils_stringBlank($wedding_anniversary["wedding_apply_discount_bride_sngl_dbl"], null);
        $wedding_apply_discount_bride_value = utils_stringBlank($wedding_anniversary["wedding_apply_discount_bride_value"], null);

        $sql = "UPDATE tblspecial_offer SET 
            wedding_date_before_basis=:wedding_date_before_basis, 
            wedding_date_after_basis=:wedding_date_after_basis,
            wedding_date_before_value=:wedding_date_before_value, 
            wedding_date_after_value=:wedding_date_after_value,
            wedding_anniversary_applicable_basis=:wedding_anniversary_applicable_basis, 
            wedding_anniversary_applicable_value=:wedding_anniversary_applicable_value,
            wedding_apply_discount_both=:wedding_apply_discount_both,
            wedding_apply_discount_groom=:wedding_apply_discount_groom,
            wedding_apply_discount_bride=:wedding_apply_discount_bride,
            wedding_apply_discount_both_basis=:wedding_apply_discount_both_basis,
            wedding_apply_discount_both_value=:wedding_apply_discount_both_value,
            wedding_apply_discount_both_sngl_dbl=:wedding_apply_discount_both_sngl_dbl,
            wedding_apply_discount_groom_basis=:wedding_apply_discount_groom_basis,
            wedding_apply_discount_groom_value=:wedding_apply_discount_groom_value,
            wedding_apply_discount_groom_sngl_dbl=:wedding_apply_discount_groom_sngl_dbl,
            wedding_apply_discount_bride_basis=:wedding_apply_discount_bride_basis,
            wedding_apply_discount_bride_value=:wedding_apply_discount_bride_value,
            wedding_apply_discount_bride_sngl_dbl=:wedding_apply_discount_bride_sngl_dbl
            WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":wedding_date_before_basis" => $wedding_date_before_basis,
            ":wedding_date_after_basis" => $wedding_date_after_basis,
            ":wedding_date_before_value" => $wedding_date_before_value,
            ":wedding_date_after_value" => $wedding_date_after_value,
            ":wedding_anniversary_applicable_basis" => $wedding_anniversary_applicable_basis,
            ":wedding_anniversary_applicable_value" => $wedding_anniversary_applicable_value,
            ":wedding_apply_discount_both" => $wedding_apply_discount_both,
            ":wedding_apply_discount_groom" => $wedding_apply_discount_groom,
            ":wedding_apply_discount_bride" => $wedding_apply_discount_bride,
            ":wedding_apply_discount_both_basis" => $wedding_apply_discount_both_basis,
            ":wedding_apply_discount_both_value" => $wedding_apply_discount_both_value,
            ":wedding_apply_discount_both_sngl_dbl" => $wedding_apply_discount_both_sngl_dbl,
            ":wedding_apply_discount_groom_basis" => $wedding_apply_discount_groom_basis,
            ":wedding_apply_discount_groom_value" => $wedding_apply_discount_groom_value,
            ":wedding_apply_discount_groom_sngl_dbl" => $wedding_apply_discount_groom_sngl_dbl,
            ":wedding_apply_discount_bride_basis" => $wedding_apply_discount_bride_basis,
            ":wedding_apply_discount_bride_value" => $wedding_apply_discount_bride_value,
            ":wedding_apply_discount_bride_sngl_dbl" => $wedding_apply_discount_bride_sngl_dbl));
    }

    //=========================================================================
    //FAMILY OFFER
    if ($template == "family_offer") {
        $family_offer = json_decode($_POST["family_offer"], true);

        $family_offer_room_applicable = utils_stringBlank($family_offer["family_offer_room_applicable"], null);
        $family_offer_adult_min = utils_stringBlank($family_offer["family_offer_adult_min"], null);
        $family_offer_adult_max = utils_stringBlank($family_offer["family_offer_adult_max"], null);
        $family_offer_children_min = utils_stringBlank($family_offer["family_offer_children_min"], null);
        $family_offer_children_max = utils_stringBlank($family_offer["family_offer_children_max"], null);


        $sql = "UPDATE tblspecial_offer SET 
                family_offer_room_applicable=:family_offer_room_applicable, 
                family_offer_adult_min=:family_offer_adult_min,
                family_offer_adult_max=:family_offer_adult_max, 
                family_offer_children_min=:family_offer_children_min,
                family_offer_children_max=:family_offer_children_max
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":family_offer_room_applicable" => $family_offer_room_applicable,
            ":family_offer_adult_min" => $family_offer_adult_min,
            ":family_offer_adult_max" => $family_offer_adult_max,
            ":family_offer_children_min" => $family_offer_children_min,
            ":family_offer_children_max" => $family_offer_children_max));



        $family_offer_children = json_decode($_POST["family_offer_children"], true);
        $outcome = savefamilyofferchildren($family_offer_children);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }
    }

    //=========================================================================
    //WEDDING PARTY
    if ($template == "wedding_party") {
        $wedding_party = json_decode($_POST["wedding_party"], true);

        $wedding_min_guests = utils_stringBlank($wedding_party["wedding_min_guests"], null);
        $wedding_max_guests = utils_stringBlank($wedding_party["wedding_max_guests"], null);

        $wedding_apply_discount_both = utils_stringBlank($wedding_party["wedding_apply_discount_both"], null);
        $wedding_apply_discount_both_basis = utils_stringBlank($wedding_party["wedding_apply_discount_both_basis"], null);
        $wedding_apply_discount_both_sngl_dbl = utils_stringBlank($wedding_party["wedding_apply_discount_both_sngl_dbl"], null);
        $wedding_apply_discount_both_value = utils_stringBlank($wedding_party["wedding_apply_discount_both_value"], null);

        $wedding_apply_discount_groom = utils_stringBlank($wedding_party["wedding_apply_discount_groom"], null);
        $wedding_apply_discount_groom_basis = utils_stringBlank($wedding_party["wedding_apply_discount_groom_basis"], null);
        $wedding_apply_discount_groom_sngl_dbl = utils_stringBlank($wedding_party["wedding_apply_discount_groom_sngl_dbl"], null);
        $wedding_apply_discount_groom_value = utils_stringBlank($wedding_party["wedding_apply_discount_groom_value"], null);

        $wedding_apply_discount_bride = utils_stringBlank($wedding_party["wedding_apply_discount_bride"], null);
        $wedding_apply_discount_bride_basis = utils_stringBlank($wedding_party["wedding_apply_discount_bride_basis"], null);
        $wedding_apply_discount_bride_sngl_dbl = utils_stringBlank($wedding_party["wedding_apply_discount_bride_sngl_dbl"], null);
        $wedding_apply_discount_bride_value = utils_stringBlank($wedding_party["wedding_apply_discount_bride_value"], null);

        $sql = "UPDATE tblspecial_offer SET 
            wedding_min_guests=:wedding_min_guests,
            wedding_max_guests=:wedding_max_guests,
            wedding_apply_discount_both=:wedding_apply_discount_both,
            wedding_apply_discount_groom=:wedding_apply_discount_groom,
            wedding_apply_discount_bride=:wedding_apply_discount_bride,
            wedding_apply_discount_both_basis=:wedding_apply_discount_both_basis,
            wedding_apply_discount_both_value=:wedding_apply_discount_both_value,
            wedding_apply_discount_both_sngl_dbl=:wedding_apply_discount_both_sngl_dbl,
            wedding_apply_discount_groom_basis=:wedding_apply_discount_groom_basis,
            wedding_apply_discount_groom_value=:wedding_apply_discount_groom_value,
            wedding_apply_discount_groom_sngl_dbl=:wedding_apply_discount_groom_sngl_dbl,
            wedding_apply_discount_bride_basis=:wedding_apply_discount_bride_basis,
            wedding_apply_discount_bride_value=:wedding_apply_discount_bride_value,
            wedding_apply_discount_bride_sngl_dbl=:wedding_apply_discount_bride_sngl_dbl
            WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":wedding_min_guests" => $wedding_min_guests,
            ":wedding_max_guests" => $wedding_max_guests,
            ":wedding_apply_discount_both" => $wedding_apply_discount_both,
            ":wedding_apply_discount_groom" => $wedding_apply_discount_groom,
            ":wedding_apply_discount_bride" => $wedding_apply_discount_bride,
            ":wedding_apply_discount_both_basis" => $wedding_apply_discount_both_basis,
            ":wedding_apply_discount_both_value" => $wedding_apply_discount_both_value,
            ":wedding_apply_discount_both_sngl_dbl" => $wedding_apply_discount_both_sngl_dbl,
            ":wedding_apply_discount_groom_basis" => $wedding_apply_discount_groom_basis,
            ":wedding_apply_discount_groom_value" => $wedding_apply_discount_groom_value,
            ":wedding_apply_discount_groom_sngl_dbl" => $wedding_apply_discount_groom_sngl_dbl,
            ":wedding_apply_discount_bride_basis" => $wedding_apply_discount_bride_basis,
            ":wedding_apply_discount_bride_value" => $wedding_apply_discount_bride_value,
            ":wedding_apply_discount_bride_sngl_dbl" => $wedding_apply_discount_bride_sngl_dbl));
    }


    //========================================================================= 
    //SENIOR OFFER
    if ($template == "senior_offer") {
        $senior_offer = json_decode($_POST["senior_offer"], true);

        $senior_guests_aged_from = utils_stringBlank($senior_offer["senior_guests_aged_from"], null);
        $senior_min_guests = utils_stringBlank($senior_offer["senior_min_guests"], null);
        $senior_discount_basis = utils_stringBlank($senior_offer["senior_discount_basis"], null);
        $senior_discount_value = utils_stringBlank($senior_offer["senior_discount_value"], null);

        $sql = "UPDATE tblspecial_offer SET 
                senior_guests_aged_from=:senior_guests_aged_from,
                senior_min_guests=:senior_min_guests,
                senior_discount_basis=:senior_discount_basis,
                senior_discount_value=:senior_discount_value
                WHERE id=:id";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id,
            ":senior_guests_aged_from" => $senior_guests_aged_from,
            ":senior_min_guests" => $senior_min_guests,
            ":senior_discount_basis" => $senior_discount_basis,
            ":senior_discount_value" => $senior_discount_value));
    }

    //=========================================================================
    //MEAL UPGRADE
    if ($template == "meals_upgrade") {
        $grid_meal_upgrade = json_decode($_POST["meal_upgrade_grid"], true);
        $outcome = savemealupgrade($grid_meal_upgrade);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }
    }


    //=========================================================================
    //FLAT RATES
    if ($template == "flat_rate") {

        //save group date periods
        $flat_rate_validity_group_grid = json_decode($_POST["flat_rate_validity_group_grid"], true);
        $outcome = saveflatratevaliditygroup($flat_rate_validity_group_grid);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save meal supplements
        $flat_rate_supplement_grid = json_decode($_POST["flat_rate_supplement_grid"], true);
        $outcome = saveflatratesupp($flat_rate_supplement_grid);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save checkin out settings        
        $flat_rate_checkinout_grid = json_decode($_POST["flat_rate_checkinout_grid"], true);
        $outcome = saveflatratecheckinout($flat_rate_checkinout_grid);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save cancellation settings        
        $flat_rate_cancellation_grid = json_decode($_POST["flat_rate_cancellation_grid"], true);
        $outcome = saveflatratecancellation($flat_rate_cancellation_grid);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }


        //save currency details
        $flat_rate_currency_details = json_decode($_POST["flat_rate_currency_details"], true);
        $outcome = saveflatratecurrencydetails($flat_rate_currency_details);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save currency exchange rates
        $flat_rate_exchrates = json_decode($_POST["flat_rate_exchrates"], true);
        $outcome = saveflatratecurrencyexgrates($flat_rate_exchrates);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save currency mapping
        $flat_rate_currencymap = json_decode($_POST["flat_rate_currencymap"], true);
        $outcome = saveflatratecurrencymapping($flat_rate_currencymap);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }


        //save tax commission settings
        $flat_rate_taxcomm = json_decode($_POST["flat_rate_taxcomm"], true);
        $outcome = saveflatratetaxcommi($flat_rate_taxcomm);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //save capacity
        $flat_rate_capacity = json_decode($_POST["flat_rate_capacity"], true);
        $outcome = saveflatratecapacity_rates($flat_rate_capacity);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }
    }

    //=========================================================================
    //DONE

    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function saveapplicable_childages($child_age_ids, $sharing_own) {
    try {

        global $con;
        global $id;

        $tablename = "";

        if ($sharing_own == "SHARING") {
            $tablename = "tblspecial_offer_applicable_childsupp_sharing";
        } else {
            $tablename = "tblspecial_offer_applicable_childsupp_own";
        }


        $arr_needed_ids = array();

        $arr_child_age_ids = explode(",", $child_age_ids);

        for ($i = 0; $i < count($arr_child_age_ids); $i++) {

            $ageid = trim($arr_child_age_ids[$i]);

            if ($ageid != "") {
                //check if exists
                $sql = "SELECT * FROM $tablename WHERE 
                    spo_fk=:spo_fk AND 
                    child_age_fk=:child_age_fk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":child_age_fk" => $ageid));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                } else {
                    //insert
                    $sql = "INSERT INTO $tablename (spo_fk, child_age_fk) VALUES 
                        (:spo_fk, :child_age_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_fk" => $id, ":child_age_fk" => $ageid));
                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);


        $sql = "DELETE FROM $tablename WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE $sharing_own APPLICABLE CHILD AGES: " . $ex->getMessage();
    }
}

function savemeals($meals_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_mealids = explode(",", $meals_ids);

        for ($i = 0; $i < count($arr_mealids); $i++) {

            $mealid = $arr_mealids[$i];

            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_mealplan WHERE 
                    spo_fk=:spo_fk AND 
                    mealplanfk=:mealplanfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":mealplanfk" => $mealid));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_mealplan (spo_fk, mealplanfk) VALUES 
                        (:spo_fk, :mealplanfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":mealplanfk" => $mealid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);

        $sql = "DELETE FROM tblspecial_offer_mealplan WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE MEAL PLANS: " . $ex->getMessage();
    }
}

function saverooms($rooms_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_roomids = explode(",", $rooms_ids);

        for ($i = 0; $i < count($arr_roomids); $i++) {

            $roomid = $arr_roomids[$i];

            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_rooms WHERE spo_fk=:spo_fk AND 
                    roomfk=:roomfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":roomfk" => $roomid));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_rooms (spo_fk, roomfk) VALUES 
                        (:spo_fk, :roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":roomfk" => $roomid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);

        $sql = "DELETE FROM tblspecial_offer_rooms WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOMS: " . $ex->getMessage();
    }
}

function savecountries($market_countries_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_countries = explode(",", $market_countries_ids);

        for ($i = 0; $i < count($arr_countries); $i++) {

            $countryid = $arr_countries[$i];

            //if($countryid != "")
            //{
            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_countries WHERE spo_fk=:spo_fk AND 
                        country_fk=:country_fk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":country_fk" => $countryid));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_countries (spo_fk, country_fk) VALUES 
                            (:spo_fk, :country_fk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":country_fk" => $countryid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
            //}            
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);

        $sql = "DELETE FROM tblspecial_offer_countries WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE COUNTRIES: " . $ex->getMessage();
    }
}

function saveperiodvalidity($period_validity) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($period_validity); $i++) {



            $valid_from = $period_validity[$i]["cells"]["valid_from"];
            $valid_to = $period_validity[$i]["cells"]["valid_to"];
            $seasonid = $period_validity[$i]["cells"]["season"];

            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_validityperiods
                    WHERE spo_fk=:spo_fk AND 
                    valid_from=:valid_from AND valid_to=:valid_to";
            
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":valid_from" => $valid_from,
                ":valid_to" => $valid_to));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                
                //update
                $sql = "UPDATE tblspecial_offer_validityperiods 
                        SET season_fk = :season_fk WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rw["id"], 
                                     ":season_fk" => $seasonid));
                    
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_validityperiods 
                        (spo_fk, valid_from, valid_to, season_fk) VALUES 
                        (:spo_fk, :valid_from, :valid_to, :season_fk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, 
                                     ":valid_from" => $valid_from, 
                                     ":valid_to" => $valid_to,
                                     ":season_fk" => $seasonid));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_validityperiods WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE VALIDITY PERIODS: " . $ex->getMessage();
    }
}

function savefreenights($free_nights_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($free_nights_grid); $i++) {

            $pay_nights = $free_nights_grid[$i]["cells"]["pay_nights"];
            $stay_nights = $free_nights_grid[$i]["cells"]["stay_nights"];


            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_freenights
                        WHERE spo_fk=:spo_fk AND 
                        pay_nights=:pay_nights AND stay_nights=:stay_nights";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":pay_nights" => $pay_nights,
                ":stay_nights" => $stay_nights));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_freenights 
                            (spo_fk, pay_nights, stay_nights) 
                            VALUES 
                            (:spo_fk, :pay_nights, :stay_nights)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id,
                    ":pay_nights" => $pay_nights,
                    ":stay_nights" => $stay_nights));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_freenights WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FREE NIGHTS: " . $ex->getMessage();
    }
}

function savefreenightvalidity($free_nights_validity) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($free_nights_validity); $i++) {

            $x = $free_nights_validity[$i]["cells"]["X"];
            $valid_from = utils_DMY_YMD($free_nights_validity[$i]["cells"]["valid_from"]);
            $valid_to = utils_DMY_YMD($free_nights_validity[$i]["cells"]["valid_to"]);

            if ($x == 1) {
                //check if exists
                $sql = "SELECT * FROM tblspecial_offer_freenights_validity_periods
                        WHERE spo_fk=:spo_fk AND 
                        valid_from=:valid_from AND valid_to=:valid_to";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":valid_from" => $valid_from,
                    ":valid_to" => $valid_to));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                } else {
                    //insert
                    $sql = "INSERT INTO tblspecial_offer_freenights_validity_periods 
                            (spo_fk, valid_from, valid_to) 
                            VALUES 
                            (:spo_fk, :valid_from, :valid_to)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_fk" => $id,
                        ":valid_from" => $valid_from,
                        ":valid_to" => $valid_to));

                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_freenights_validity_periods 
                    WHERE spo_fk=:spo_fk";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FREE NIGHTS VALIDITY PERIODS: " . $ex->getMessage();
    }
}

function savefamilyofferchildren($family_offer_children) {
    try {


        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($family_offer_children); $i++) {

            $child_age_from = $family_offer_children[$i]["cells"]["child_age_from"];
            $child_age_to = $family_offer_children[$i]["cells"]["child_age_to"];
            $discount_percentage_value = $family_offer_children[$i]["cells"]["discount_percentage_value"];
            $discount_value = $family_offer_children[$i]["cells"]["discount_value"];


            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_familyoffer_childage_discount
                        WHERE spo_fk=:spo_fk AND 
                        child_age_from=:child_age_from AND
                        child_age_to=:child_age_to AND 
                        discount_percentage_value=:discount_percentage_value AND 
                        discount_value=:discount_value";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id,
                ":child_age_from" => $child_age_from,
                ":child_age_to" => $child_age_to,
                ":discount_percentage_value" => $discount_percentage_value,
                ":discount_value" => $discount_value));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_familyoffer_childage_discount 
                            (spo_fk, child_age_from, child_age_to,
                            discount_percentage_value,discount_value) 
                            VALUES 
                            (:spo_fk, :child_age_from, :child_age_to,
                            :discount_percentage_value, :discount_value)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id,
                    ":child_age_from" => $child_age_from,
                    ":child_age_to" => $child_age_to,
                    ":discount_percentage_value" => $discount_percentage_value,
                    ":discount_value" => $discount_value));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_familyoffer_childage_discount WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FAMILY OFFER CHILDREN: " . $ex->getMessage();
    }
}

function savemealupgrade($mealgrid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($mealgrid); $i++) {

            $meal_from_fk = $mealgrid[$i]["cells"]["meal_from_fk"];
            $meal_to_fk = $mealgrid[$i]["cells"]["meal_to_fk"];


            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_upgrade_meal
                        WHERE spo_fk=:spo_fk AND 
                        meal_from_fk=:meal_from_fk AND
                        meal_to_fk=:meal_to_fk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":meal_from_fk" => $meal_from_fk,
                ":meal_to_fk" => $meal_to_fk));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_upgrade_meal 
                            (spo_fk, meal_from_fk, meal_to_fk) 
                            VALUES 
                            (:spo_fk, :meal_from_fk, :meal_to_fk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":meal_from_fk" => $meal_from_fk, ":meal_to_fk" => $meal_to_fk));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_upgrade_meal WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE MEAL UPGRADE: " . $ex->getMessage();
    }
}

function saveflatratevaliditygroup_childrenages($grpid, $children_ages_ids) {
    try {

        global $con;

        $arr_needed_ids = array();

        $arr_child_age_ids = explode(",", $children_ages_ids);

        for ($i = 0; $i < count($arr_child_age_ids); $i++) {

            $ageid = trim($arr_child_age_ids[$i]);

            //check if exists
            $sql = "SELECT * FROM 
                    tblspecial_offer_flatrate_group_validity_period_childages
                    WHERE spo_fltrate_grp_valid_period_fk=$grpid AND 
                    childage_fk=$ageid";

            $stmt = $con->prepare($sql);
            $stmt->execute();

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_group_validity_period_childages 
                        (spo_fltrate_grp_valid_period_fk, childage_fk) 
                        VALUES 
                        ($grpid,$ageid)";
                $stmt = $con->prepare($sql);
                $stmt->execute();
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }
        //====================================================================
        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_group_validity_period_childages
                WHERE spo_fltrate_grp_valid_period_fk=$grpid ";
        if ($deleteids != "") {

            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute();

        return "OK";
    } catch (Exception $ex) {
        return "GROUP AGE: " . $ex->getMessage();
    }
}

function saveflatratesupp_childrenages($suppid, $data_arr) {
    try {

        global $con;

        $arr_needed_ids = array();

        $arrkeys = array_keys($data_arr);

        for ($i = 0; $i < count($arrkeys); $i++) {

            $key = trim($arrkeys[$i]);

            if (strpos($key, "_") !== false) {



                $age_arr = explode("_", $key);
                $age_from = trim($age_arr[0]);
                $age_to = trim($age_arr[1]);
                $count = utils_stringBlank($data_arr[$key], 0);

                //check if exists
                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_mealsupp_children_ages
                        WHERE spo_ftrte_mealsupp_fk=$suppid AND 
                        child_age_from=$age_from AND child_age_to=$age_to";

                $stmt = $con->prepare($sql);
                $stmt->execute();

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_mealsupp_children_ages SET
                            child_count = :count WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":count" => $count, ":id" => $rw["id"]));
                } else {
                    //insert
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_mealsupp_children_ages 
                            (spo_ftrte_mealsupp_fk, child_age_from,child_age_to,child_count) 
                            VALUES 
                            ($suppid, $age_from,$age_to,:count)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":count" => $count));
                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }
        //====================================================================
        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_mealsupp_children_ages
                WHERE spo_ftrte_mealsupp_fk=$suppid ";
        if ($deleteids != "") {

            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute();

        return "OK";
    } catch (Exception $ex) {
        return "CHILD AGE: " . $ex->getMessage();
    }
}

function saveflatratecancellation_rooms($suppid, $rooms_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_rooms = explode(",", $rooms_ids);

        for ($i = 0; $i < count($arr_rooms); $i++) {

            $room_id = trim($arr_rooms[$i]);

            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_flatrate_cancellation_rooms
                        WHERE spo_ftrte_cancellation_fk=:spo_ftrte_cancellation_fk
                        AND roomfk=:roomfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_ftrte_cancellation_fk" => $suppid,
                ":roomfk" => $room_id));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_flatrate_cancellation_rooms 
                            (spo_ftrte_cancellation_fk, roomfk) 
                            VALUES 
                            (:spo_ftrte_cancellation_fk, :roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_ftrte_cancellation_fk" => $suppid,
                    ":roomfk" => $room_id));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }


        //clean up
        $sql = "DELETE FROM tblspecial_offer_flatrate_cancellation_rooms WHERE 
                    spo_ftrte_cancellation_fk=:spo_ftrte_cancellation_fk";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_ftrte_cancellation_fk" => $suppid));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOM: " . $ex->getMessage();
    }
}

function saveflatratecheckinout_rooms($suppid, $rooms_ids) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        $arr_rooms = explode(",", $rooms_ids);

        for ($i = 0; $i < count($arr_rooms); $i++) {

            $room_id = trim($arr_rooms[$i]);

            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_flatrate_checkinout_rooms
                        WHERE spo_ftrte_checkinout_fk=:spo_ftrte_checkinout_fk
                        AND roomfk=:roomfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_ftrte_checkinout_fk" => $suppid,
                ":roomfk" => $room_id));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_flatrate_checkinout_rooms 
                            (spo_ftrte_checkinout_fk, roomfk) 
                            VALUES 
                            (:spo_ftrte_checkinout_fk, :roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_ftrte_checkinout_fk" => $suppid,
                    ":roomfk" => $room_id));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }


        //clean up
        $sql = "DELETE FROM tblspecial_offer_flatrate_checkinout_rooms WHERE 
                    spo_ftrte_checkinout_fk=:spo_ftrte_checkinout_fk";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_ftrte_checkinout_fk" => $suppid));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOM: " . $ex->getMessage();
    }
}

function saveflatratecancellation_dateperiods($suppid, $dateperiods) {
    try {

        global $con;

        $arr_needed_ids = array();

        $arr_dates = explode("<br>", $dateperiods);

        for ($i = 0; $i < count($arr_dates); $i++) {

            $dates = trim($arr_dates[$i]);

            if ($dates != "") {
                $arr_single_dates = explode(" - ", $dates);
                $dtfrom = utils_DMY_YMD(trim($arr_single_dates[0]));
                $dtto = utils_DMY_YMD(trim($arr_single_dates[1]));

                //check if exists
                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_cancellation_dateperiods
                        WHERE spo_ftrte_cancellation_fk=$suppid AND 
                        valid_from=:valid_from AND 
                        valid_to=:valid_to";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":valid_from" => $dtfrom, ":valid_to" => $dtto));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                } else {
                    //insert
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_cancellation_dateperiods 
                            (spo_ftrte_cancellation_fk, valid_from, valid_to) 
                            VALUES 
                            (:spo_ftrte_cancellation_fk, :valid_from, :valid_to)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_ftrte_cancellation_fk" => $suppid,
                        ":valid_from" => $dtfrom,
                        ":valid_to" => $dtto));
                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //clean up

        $sql = "DELETE FROM 
                 tblspecial_offer_flatrate_cancellation_dateperiods
                 WHERE 
                 spo_ftrte_cancellation_fk=$suppid";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return "OK";
    } catch (Exception $ex) {
        return "DATE PERIODS: " . $ex->getMessage();
    }
}

function saveflatratecheckinout_dateperiods($suppid, $dateperiods) {
    try {

        global $con;

        $arr_needed_ids = array();

        $arr_dates = explode("<br>", $dateperiods);

        for ($i = 0; $i < count($arr_dates); $i++) {

            $dates = trim($arr_dates[$i]);

            if ($dates != "") {
                $arr_single_dates = explode(" - ", $dates);
                $dtfrom = utils_DMY_YMD(trim($arr_single_dates[0]));
                $dtto = utils_DMY_YMD(trim($arr_single_dates[1]));

                //check if exists
                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_checkinout_dateperiods
                        WHERE spo_ftrte_checkinout_fk=$suppid AND 
                        valid_from=:valid_from AND 
                        valid_to=:valid_to";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":valid_from" => $dtfrom, ":valid_to" => $dtto));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                } else {
                    //insert
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_checkinout_dateperiods 
                            (spo_ftrte_checkinout_fk, valid_from, valid_to) 
                            VALUES 
                            (:spo_ftrte_checkinout_fk, :valid_from, :valid_to)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_ftrte_checkinout_fk" => $suppid,
                        ":valid_from" => $dtfrom,
                        ":valid_to" => $dtto));
                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //clean up

        $sql = "DELETE FROM 
                 tblspecial_offer_flatrate_checkinout_dateperiods
                 WHERE 
                 spo_ftrte_checkinout_fk=$suppid";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return "OK";
    } catch (Exception $ex) {
        return "DATE PERIODS: " . $ex->getMessage();
    }
}

function saveflatratesupp_dateperiods($suppid, $dateperiods) {

    try {

        global $con;

        $arr_needed_ids = array();

        $arr_dates = explode("<br>", $dateperiods);

        for ($i = 0; $i < count($arr_dates); $i++) {

            $dates = trim($arr_dates[$i]);

            if ($dates != "") {
                $arr_single_dates = explode(" - ", $dates);
                $dtfrom = utils_DMY_YMD(trim($arr_single_dates[0]));
                $dtto = utils_DMY_YMD(trim($arr_single_dates[1]));

                //check if exists
                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_mealsupp_dateperiods
                        WHERE spo_ftrte_mealsupp_fk=$suppid AND 
                        valid_from=:valid_from AND 
                        valid_to=:valid_to";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":valid_from" => $dtfrom, ":valid_to" => $dtto));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                } else {
                    //insert
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_mealsupp_dateperiods 
                            (spo_ftrte_mealsupp_fk, valid_from, valid_to) 
                            VALUES 
                            (:spo_ftrte_mealsupp_fk, :valid_from, :valid_to)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_ftrte_mealsupp_fk" => $suppid,
                        ":valid_from" => $dtfrom,
                        ":valid_to" => $dtto));
                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                 tblspecial_offer_flatrate_mealsupp_dateperiods
                 WHERE 
                 spo_ftrte_mealsupp_fk=$suppid";

        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute();

        return "OK";
    } catch (Exception $ex) {
        return "DATE PERIODS: " . $ex->getMessage();
    }
}

function saveflatratecapacity_rates($flat_rate_capacity) {
    try {

        global $con;
        global $id;

        $arr_roomids_needed = array();

        for ($i = 0; $i < count($flat_rate_capacity); $i++) {
            $room_rwid = $flat_rate_capacity[$i]["room_rwid"];
            $room_id = $flat_rate_capacity[$i]["room_id"];
            $room_action = $flat_rate_capacity[$i]["room_action"];
            $room_variants = $flat_rate_capacity[$i]["room_variants"];
            $arr_room_dates = $flat_rate_capacity[$i]["room_dates"];

            if ($room_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_roomcapacity 
                        WHERE spo_fk=:id AND roomfk=:roomfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $id, ":roomfk" => $room_id));
            } else {
                $arr_roomids_needed[] = $room_id;

                $sql = "SELECT * FROM tblspecial_offer_flatrate_roomcapacity 
                        WHERE spo_fk=:id AND roomfk=:roomfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $id, ":roomfk" => $room_id));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $room_rwid = $rw["id"];
                    $sql = "UPDATE tblspecial_offer_flatrate_roomcapacity 
                            SET variant=:variant
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":variant" => $room_variants,
                        ":id" => $room_rwid));
                } else {
                    $sql = "INSERT INTO tblspecial_offer_flatrate_roomcapacity 
                            (spo_fk,roomfk,variant)
                            VALUES
                            (:spo_fk,:roomfk,:variant)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_fk" => $id,
                        ":roomfk" => $room_id,
                        ":variant" => $room_variants));

                    $room_rwid = $con->lastInsertId();
                }

                $outcome = saveflatrateroomcapacitydates($room_rwid, $arr_room_dates);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }

        //clean up non needed roomids
        $deleteids = implode(",", $arr_roomids_needed);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblspecial_offer_flatrate_roomcapacity 
                    WHERE spo_fk=:id AND roomfk NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE CAPACITY: " . $ex->getMessage();
    }
}

function saveflatrateroomcapacitydates($room_rwid, $arr_room_dates) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_room_dates); $i++) {
            $date_rwid = $arr_room_dates[$i]["date_rwid"];
            $date_dtfrom = utils_stringBlank($arr_room_dates[$i]["date_dtfrom"], "");
            $date_dtto = utils_stringBlank($arr_room_dates[$i]["date_dtto"], "");
            $date_action = $arr_room_dates[$i]["date_action"];

            $arr_capacity_rules = $arr_room_dates[$i]["date_capacity_rules"];
            $arr_adultpolicies_rules = $arr_room_dates[$i]["date_adultpolicies_rules"];
            $arr_childpolicies_rules = $arr_room_dates[$i]["date_childpolicies_rules"];
            $arr_singleparentpolicies_rules = $arr_room_dates[$i]["date_singleparentpolicies_rules"];


            if ($date_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_roomcapacity_dates 
                        WHERE id=:id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $date_rwid));
            } else {

                $cond_dtfrom = " AND override_dtfrom='$date_dtfrom' ";
                if ($date_dtfrom == "") {
                    $cond_dtfrom = " AND override_dtfrom IS NULL ";
                }

                $cond_dtto = " AND override_dtto='$date_dtto' ";
                if ($date_dtto == "") {
                    $cond_dtto = " AND override_dtto IS NULL ";
                }

                $sql = "SELECT * FROM tblspecial_offer_flatrate_roomcapacity_dates 
                        WHERE
                        spo_flatrates_roomcapacity_fk=:spo_flatrates_roomcapacity_fk
                        $cond_dtfrom
                        $cond_dtto";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_flatrates_roomcapacity_fk" => $room_rwid));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                    $date_rwid = $rw["id"];
                } else {

                    if ($date_dtfrom == "") {
                        $date_dtfrom = null;
                    }
                    if ($date_dtto == "") {
                        $date_dtto = null;
                    }

                    $sql = "INSERT INTO tblspecial_offer_flatrate_roomcapacity_dates 
                            (override_dtfrom,override_dtto,
                             spo_flatrates_roomcapacity_fk)
                            VALUES
                            (:override_dtfrom,:override_dtto,
                             :spo_flatrates_roomcapacity_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":override_dtfrom" => $date_dtfrom,
                        ":override_dtto" => $date_dtto,
                        ":spo_flatrates_roomcapacity_fk" => $room_rwid));

                    $date_rwid = $con->lastInsertId();
                    $arr_needed_ids[] = $date_rwid;
                }


                //================================            
                $outcome = saveflatratermcapdtrules($date_rwid, $arr_capacity_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveflatratermadultpolicydtrules($date_rwid, $arr_adultpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveflatratermchildpolicydtrules($date_rwid, $arr_childpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $outcome = saveflatratermsngprntpolicydtrules($date_rwid, $arr_singleparentpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                //================================
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_roomcapacity_dates WHERE 
                    spo_flatrates_roomcapacity_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $room_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "CAPACITY DATES: " . $ex->getMessage();
    }
}

function saveflatratermsngprntpolicydtrules($date_rwid, $arr_singleparentpolicies_rules) {
    try {

        global $con;
        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_singleparentpolicies_rules); $i++) {

            $rule_rwid = $arr_singleparentpolicies_rules[$i]["rule_rwid"];
            $rule_rulecounter = $arr_singleparentpolicies_rules[$i]["rule_rulecounter"];
            $rule_category = $arr_singleparentpolicies_rules[$i]["rule_category"];
            $rule_ageranges = $arr_singleparentpolicies_rules[$i]["rule_ageranges"];
            $rule_action = $arr_singleparentpolicies_rules[$i]["rule_action"];
            $arr_rule_policy = $arr_singleparentpolicies_rules[$i]["rule_policy"];

            if ($rule_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_snglprnt_rm_dt_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_snglprnt_rm_dt_rules
                        (spo_flatrate_roomcapacity_dates_fk,
                         rulecounter,rulecategory,ruleageranges)
                        VALUES
                        (:spo_flatrate_roomcapacity_dates_fk,
                         :rulecounter,:rulecategory,:ruleageranges)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_flatrate_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":ruleageranges" => $rule_ageranges));

                    $rule_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_snglprnt_rm_dt_rules 
                            SET rulecounter=:rulecounter, rulecategory=:rulecategory,
                            ruleageranges=:ruleageranges WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $rule_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":ruleageranges" => $rule_ageranges));
                }

                $outcome = saveflatratermsngprntpolicydtrulesages($rule_rwid, $arr_rule_policy);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $rule_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules
                    WHERE 
                    spo_flatrate_roomcapacity_dates_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $date_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "SNGPRNT DATES RULE: " . $ex->getMessage();
    }
}

function saveflatratermsngprntpolicydtrulesages($rule_rwid, $arr_rule_policy) {
    try {

        global $con;
        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_rule_policy); $i++) {

            $policy_rwid = $arr_rule_policy[$i]["policy_rwid"];
            $policy_adult_child = $arr_rule_policy[$i]["policy_adult_child"];
            $policy_category = $arr_rule_policy[$i]["policy_category"];
            $policy_basis = $arr_rule_policy[$i]["policy_basis"];
            $policy_child_agefrom = $arr_rule_policy[$i]["policy_child_agefrom"];
            $policy_child_ageto = $arr_rule_policy[$i]["policy_child_ageto"];
            $policy_action = $arr_rule_policy[$i]["policy_action"];
            $arr_policy_values = $arr_rule_policy[$i]["policy_values"];

            if ($policy_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {

                if ($policy_rwid < 0) {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages
                            (spo_flatrate_snglprnt_rm_dt_rules_fk,
                            category,
                            basis,adult_child,child_agefrom,child_ageto)
                            VALUES
                            (:spo_flatrate_snglprnt_rm_dt_rules_fk,
                            :category,
                            :basis,:adult_child,:child_agefrom,:child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_flatrate_snglprnt_rm_dt_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":adult_child" => $policy_adult_child,
                        ":child_agefrom" => $policy_child_agefrom,
                        ":child_ageto" => $policy_child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages
                            SET category=:category,
                            basis=:basis, adult_child=:adult_child,
                            child_agefrom=:child_agefrom, child_ageto=:child_ageto
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $policy_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":adult_child" => $policy_adult_child,
                        ":child_agefrom" => $policy_child_agefrom,
                        ":child_ageto" => $policy_child_ageto));
                }


                $outcome = saveflatratermsngprntpolicydtrulesagesvalues($policy_rwid, $arr_policy_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $policy_rwid;
            }
        }



        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages
                    WHERE 
                    spo_flatrate_snglprnt_rm_dt_rules_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $rule_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "SNGPRNT DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveflatratermsngprntpolicydtrulesagesvalues($policy_rwid, $arr_policy_values) {
    try {

        global $con;
        $arr_needed_ids = array();


        for ($i = 0; $i < count($arr_policy_values); $i++) {
            $value_rwid = $arr_policy_values[$i]["value_rwid"];
            $value_currencyfk = utils_stringBlank($arr_policy_values[$i]["value_currencyfk"], null);
            $value_basis = $arr_policy_values[$i]["value_basis"];
            $value_value = $arr_policy_values[$i]["value_value"];
            $value_action = $arr_policy_values[$i]["value_action"];

            
            if ($value_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                if ($value_rwid < 0) {
                $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages_values
                        (spo_flatrate_snglprnt_rm_dt_rules_ages_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:spo_flatrate_snglprnt_rm_dt_rules_ages_fk,
                         :currencyfk,:basis,:value)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":spo_flatrate_snglprnt_rm_dt_rules_ages_fk" => $policy_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));

                $value_rwid = $con->lastInsertId();
            } else {

                $sql = "UPDATE tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages_values 
                        SET currencyfk=:currencyfk, basis=:basis,
                        value=:value WHERE id=:id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $value_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));
            }

            $arr_needed_ids[] = $value_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_snglprnt_rm_dt_rules_ages_values
                    WHERE 
                    spo_flatrate_snglprnt_rm_dt_rules_ages_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $policy_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "SNGPRNT DATES RULE AGES VALUES: " . $ex->getMessage();
    }
}

function saveflatratermchildpolicydtrules($date_rwid, $arr_childpolicies_rules) {
    try {

        global $con;
        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_childpolicies_rules); $i++) {

            $rule_rwid = $arr_childpolicies_rules[$i]["rule_rwid"];
            $rule_rulecounter = $arr_childpolicies_rules[$i]["rule_rulecounter"];
            $rule_category = $arr_childpolicies_rules[$i]["rule_category"];
            $rule_sharing_single = $arr_childpolicies_rules[$i]["rule_sharing_single"];
            $rule_action = $arr_childpolicies_rules[$i]["rule_action"];
            $arr_rule_policy = $arr_childpolicies_rules[$i]["rule_policy"];


            if ($rule_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_ch_rm_dt_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {

                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_ch_rm_dt_rules
                            (spo_flatrate_roomcapacity_dates_fk,
                            rulecounter,rulecategory,sharing_single)
                            VALUES
                            (:spo_flatrate_roomcapacity_dates_fk,
                            :rulecounter,:rulecategory,:sharing_single)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_flatrate_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":sharing_single" => $rule_sharing_single));

                    $rule_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_ch_rm_dt_rules SET 
                            rulecounter=:rulecounter,
                            rulecategory=:rulecategory,
                            sharing_single=:sharing_single
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $rule_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":sharing_single" => $rule_sharing_single));
                }

                $outcome = saveflatratermchildpolicydtrulesages($rule_rwid, $arr_rule_policy);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $rule_rwid;
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_ch_rm_dt_rules
                    WHERE 
                    spo_flatrate_roomcapacity_dates_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $date_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "CHILD DATES RULE: " . $ex->getMessage();
    }
}

function saveflatratermchildpolicydtrulesages($rule_rwid, $arr_rule_policy) {
    try {

        global $con;
        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_rule_policy); $i++) {

            //return print_r($arr_rule_policy[$i]);

            $policy_rwid = $arr_rule_policy[$i]["policy_rwid"];
            $policy_category = $arr_rule_policy[$i]["policy_category"];
            $policy_basis = $arr_rule_policy[$i]["policy_basis"];
            $child_agefrom = $arr_rule_policy[$i]["policy_units_additional_child_agefrom"];
            $child_ageto = $arr_rule_policy[$i]["policy_units_additional_child_ageto"];
            $policy_action = $arr_rule_policy[$i]["policy_action"];
            $arr_policy_values = $arr_rule_policy[$i]["policy_values"];

            if ($policy_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_ch_rm_dt_rules_ages 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {

                if ($policy_rwid < 0) {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_ch_rm_dt_rules_ages
                            (spo_flatrate_childpolicy_room_dates_rules_fk,
                            category,basis,child_agefrom,child_ageto)
                            VALUES
                            (:spo_flatrate_childpolicy_room_dates_rules_fk,
                            :category,:basis,:child_agefrom,:child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_flatrate_childpolicy_room_dates_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":child_agefrom" => $child_agefrom,
                        ":child_ageto" => $child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {
                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_ch_rm_dt_rules_ages
                            SET category=:category,
                            basis=:basis,child_agefrom=:child_agefrom,
                            child_ageto=:child_ageto WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $policy_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":child_agefrom" => $child_agefrom,
                        ":child_ageto" => $child_ageto));
                }

                $outcome = saveflatratermchildpolicydtrulesagesvalues($policy_rwid, $arr_policy_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $policy_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_ch_rm_dt_rules_ages
                    WHERE 
                    spo_flatrate_childpolicy_room_dates_rules_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $rule_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "CHILD DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveflatratermchildpolicydtrulesagesvalues($policy_rwid, $arr_policy_values) {
    try {

        global $con;
        $arr_needed_ids = array();


        for ($i = 0; $i < count($arr_policy_values); $i++) {
            $value_rwid = $arr_policy_values[$i]["value_rwid"];
            $value_currencyfk = utils_stringBlank($arr_policy_values[$i]["value_currencyfk"], null);
            $value_basis = $arr_policy_values[$i]["value_basis"];
            $value_value = $arr_policy_values[$i]["value_value"];
            $value_action = $arr_policy_values[$i]["value_action"];
            
            if ($value_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_ch_rm_dt_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                if ($value_rwid < 0) {
                $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_ch_rm_dt_rules_ages_values
                        (spo_flatrate_child_policy_room_dates_rules_ages_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:spo_flatrate_child_policy_room_dates_rules_ages_fk,
                         :currencyfk,:basis,:value)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":spo_flatrate_child_policy_room_dates_rules_ages_fk" => $policy_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));

                $value_rwid = $con->lastInsertId();
            } else {

                $sql = "UPDATE tblspecial_offer_flatrate_ch_rm_dt_rules_ages_values 
                        SET currencyfk=:currencyfk, basis=:basis,
                        value=:value WHERE id=:id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $value_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));
            }

            $arr_needed_ids[] = $value_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_ch_rm_dt_rules_ages_values
                    WHERE 
                    spo_flatrate_child_policy_room_dates_rules_ages_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $policy_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "CHILD DATES RULE AGES VALUES: " . $ex->getMessage();
    }
}

function saveflatratermadultpolicydtrules($date_rwid, $arr_adultpolicies_rules) {
    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_adultpolicies_rules); $i++) {

            $rule_rwid = $arr_adultpolicies_rules[$i]["rule_rwid"];
            $rule_rulecounter = $arr_adultpolicies_rules[$i]["rule_rulecounter"];
            $rule_category = $arr_adultpolicies_rules[$i]["rule_category"];
            $rule_action = $arr_adultpolicies_rules[$i]["rule_action"];
            $arr_rule_ages = $arr_adultpolicies_rules[$i]["rule_policy"];

            if ($rule_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_ad_rm_dt_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_ad_rm_dt_rules
                            (spo_flatrate_roomcapacity_dates_fk,rulecounter,rulecategory)
                            VALUES
                            (:spo_flatrate_roomcapacity_dates_fk,:rulecounter,:rulecategory)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_flatrate_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category));

                    $rule_rwid = $con->lastInsertId();
                } else {
                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_ad_rm_dt_rules
                            SET rulecounter=:rulecounter, 
                            rulecategory=:rulecategory
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $rule_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category));
                }

                $outcome = saveflatratermadultpolicydtrulesages($rule_rwid, $arr_rule_ages);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $arr_needed_ids[] = $rule_rwid;
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_ad_rm_dt_rules WHERE 
                    spo_flatrate_roomcapacity_dates_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $date_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "FLAT RATE ADULT DATE RULES: " . $ex->getMessage();
    }
}

function saveflatratermadultpolicydtrulesages($rule_rwid, $arr_rule_ages) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_rule_ages); $i++) {

            $policy_rwid = $arr_rule_ages[$i]["policy_rwid"];
            $policy_category = $arr_rule_ages[$i]["policy_category"];
            $policy_basis = $arr_rule_ages[$i]["policy_basis"];
            $policy_units_additional_child_agefrom = $arr_rule_ages[$i]["policy_units_additional_child_agefrom"];
            $policy_units_additional_child_ageto = $arr_rule_ages[$i]["policy_units_additional_child_ageto"];
            $policy_action = $arr_rule_ages[$i]["policy_action"];
            $arr_values = $arr_rule_ages[$i]["policy_values"];

            if ($policy_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_ad_rm_dt_rules_ages
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {
                if ($policy_rwid < 0) {

                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_ad_rm_dt_rules_ages
                            (spo_flatrate_adultpolicy_room_dates_rules_fk,
                            category,basis,units_additional_child_agefrom,
                            units_additional_child_ageto)
                            VALUES
                            (:spo_flatrate_adultpolicy_room_dates_rules_fk,
                            :category,:basis,:units_additional_child_agefrom,
                            :units_additional_child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_flatrate_adultpolicy_room_dates_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":units_additional_child_agefrom" => $policy_units_additional_child_agefrom,
                        ":units_additional_child_ageto" => $policy_units_additional_child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE 
                            tblspecial_offer_flatrate_ad_rm_dt_rules_ages
                            SET 
                            category=:category, basis=:basis, 
                            units_additional_child_agefrom=:units_additional_child_agefrom,
                            units_additional_child_ageto=:units_additional_child_ageto
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $policy_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":units_additional_child_agefrom" => $policy_units_additional_child_agefrom,
                        ":units_additional_child_ageto" => $policy_units_additional_child_ageto));
                }

                $outcome = saveflatratermadultpolicydtrulesagesvalues($policy_rwid, $arr_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $arr_needed_ids[] = $policy_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_ad_rm_dt_rules_ages WHERE 
                    spo_flatrate_adultpolicy_room_dates_rules_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $rule_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ADULT DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveflatratermadultpolicydtrulesagesvalues($policy_rwid, $arr_values) {
    try {
        global $con;
        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_values); $i++) {


            $value_rwid = $arr_values[$i]["value_rwid"];
            $value_currencyfk = utils_stringBlank($arr_values[$i]["value_currencyfk"], null);
            $value_basis = $arr_values[$i]["value_basis"];
            $value_value = $arr_values[$i]["value_value"];
            $value_action = $arr_values[$i]["value_action"];

            if ($value_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_ad_rm_dt_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                if ($value_rwid < 0) {
                $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_ad_rm_dt_rules_ages_values
                        (spo_fte_ad_rm_dt_rules_ag_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:spo_fte_ad_rm_dt_rules_ag_fk,
                         :currencyfk,:basis,:value)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":spo_fte_ad_rm_dt_rules_ag_fk" => $policy_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));

                $value_rwid = $con->lastInsertId();
            } else {

                $sql = "UPDATE tblspecial_offer_flatrate_ad_rm_dt_rules_ages_values 
                        SET currencyfk=:currencyfk, basis=:basis,
                        value=:value WHERE id=:id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $value_rwid,
                    ":currencyfk" => $value_currencyfk,
                    ":basis" => $value_basis,
                    ":value" => $value_value));
            }

            $arr_needed_ids[] = $value_rwid;
            }
            
            
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_ad_rm_dt_rules_ages_values
                    WHERE 
                    spo_fte_ad_rm_dt_rules_ag_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $policy_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "ADULT DATE RULE AGES VALUES: " . $ex->getMessage();
    }
}

function saveflatratermcapdtrules($date_rwid, $arr_capacity_rules) {
    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_capacity_rules); $i++) {
            $rule_rwid = $arr_capacity_rules[$i]["rule_rwid"];
            $rule_action = $arr_capacity_rules[$i]["rule_action"];
            $arr_rule_capacity = $arr_capacity_rules[$i]["rule_capacity"];

            if ($rule_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_roomcapacity_dates_rules 
                        WHERE
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_roomcapacity_dates_rules
                            (spo_flatrates_roomcapacity_dates_fk,rulecounter)
                            VALUES
                            (:spo_flatrates_roomcapacity_dates_fk,:rulecounter)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_flatrates_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i));

                    $rule_rwid = $con->lastInsertId();
                }

                $arr_needed_ids[] = $rule_rwid;

                $outcome = saveflatratermcapdtruleages($rule_rwid, $arr_rule_capacity);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_roomcapacity_dates_rules WHERE 
                    spo_flatrates_roomcapacity_dates_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $date_rwid));


        return "OK";
    } catch (Exception $ex) {
        return "CAPACITY DATE RULES: " . $ex->getMessage();
    }
}

function saveflatratermcapdtruleages($rule_rwid, $arr_rule_capacity) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_rule_capacity); $i++) {

            $capacity_rwid = $arr_rule_capacity[$i]["capacity_rwid"];
            $capacity_category = $arr_rule_capacity[$i]["capacity_category"];
            $capacity_minpax = $arr_rule_capacity[$i]["capacity_minpax"];
            $capacity_maxpax = $arr_rule_capacity[$i]["capacity_maxpax"];
            $capacity_child_agefrom = $arr_rule_capacity[$i]["capacity_child_agefrom"];
            $capacity_child_ageto = $arr_rule_capacity[$i]["capacity_child_ageto"];
            $capacity_action = $arr_rule_capacity[$i]["capacity_action"];


            $sql = "SELECT * FROM 
                    tblspecial_offer_flatrate_roomcapacity_dates_rules_ages
                    WHERE 
                    spo_flatrates_roomcapacity_dates_rules_fk=:rulerwid
                    AND category=:category AND minpax=:minpax AND maxpax=:maxpax
                    AND child_agefrom=:child_agefrom AND child_ageto=:child_ageto";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":rulerwid" => $rule_rwid,
                ":category" => $capacity_category,
                ":minpax" => $capacity_minpax,
                ":maxpax" => $capacity_maxpax,
                ":child_agefrom" => $capacity_child_agefrom,
                ":child_ageto" => $capacity_child_ageto));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                $sql = "INSERT INTO 
                        tblspecial_offer_flatrate_roomcapacity_dates_rules_ages
                        (spo_flatrates_roomcapacity_dates_rules_fk,
                        category,
                        minpax,
                        maxpax,
                        child_agefrom,
                        child_ageto)
                        VALUES
                        (:rulerwid,
                        :category,
                        :minpax,
                        :maxpax,
                        :child_agefrom,
                        :child_ageto)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":rulerwid" => $rule_rwid,
                    ":category" => $capacity_category,
                    ":minpax" => $capacity_minpax,
                    ":maxpax" => $capacity_maxpax,
                    ":child_agefrom" => $capacity_child_agefrom,
                    ":child_ageto" => $capacity_child_ageto));

                $arr_needed_ids[] = $con->lastInsertId();
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_roomcapacity_dates_rules_ages WHERE 
                    spo_flatrates_roomcapacity_dates_rules_fk=:id ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $rule_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "CAPACITY DATE RULE AGES: " . $ex->getMessage();
    }
}

function saveflatratetaxcommi($taxcomm) {
    try {

        //process the buying settings
        $outcome = saveFlatRateTaxCommiBuySellValues($taxcomm["buying_settings"], "BUYING");
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //process the selling settings
        $outcome = saveFlatRateTaxCommiBuySellValues($taxcomm["selling_settings"], "SELLING");
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: TAXCOMMI: " . $ex->getMessage();
    }
}

function saveFlatRateTaxCommiBuySellValues($arr_settings, $buying_selling) {
    try {
        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($j = 0; $j < count($arr_settings); $j++) {
            $setting_rwid = $arr_settings[$j]["setting_rwid"];
            $setting_buying_selling = $arr_settings[$j]["setting_buying_selling"];
            $setting_row_index = $arr_settings[$j]["setting_row_index"];
            $setting_item_fk = $arr_settings[$j]["setting_item_fk"];
            $setting_basis = $arr_settings[$j]["setting_basis"];
            $setting_applyon_formula = $arr_settings[$j]["setting_applyon_formula"];
            $setting_rounding = $arr_settings[$j]["setting_rounding"];
            $setting_action = $arr_settings[$j]["setting_action"];
            $arr_values = $arr_settings[$j]["setting_values"];

            if ($setting_action != "DELETE") {


                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_taxcomm WHERE 
                        spo_fk=:spo_fk AND 
                        buying_selling=:buying_selling  
                        AND row_index=:row_index";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":spo_fk" => $id,
                    ":buying_selling" => $setting_buying_selling,
                    ":row_index" => $setting_row_index));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $setting_rwid = $rw["id"];

                    $sql = "UPDATE tblspecial_offer_flatrate_taxcomm SET 
                            item_fk=:item_fk, basis=:basis, 
                            applyon_formula=:applyon_formula, rounding=:rounding
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":item_fk" => $setting_item_fk,
                        ":basis" => $setting_basis,
                        ":applyon_formula" => $setting_applyon_formula,
                        ":rounding" => $setting_rounding,
                        ":id" => $setting_rwid));
                } else {
                    $sql = "INSERT INTO
                            tblspecial_offer_flatrate_taxcomm 
                            (spo_fk,buying_selling,row_index,
                            item_fk,basis,applyon_formula,rounding)
                            VALUES
                            (:spo_fk,:buying_selling,:row_index,
                            :item_fk,:basis,:applyon_formula,:rounding)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":spo_fk" => $id,
                        ":buying_selling" => $setting_buying_selling,
                        ":row_index" => $setting_row_index,
                        ":item_fk" => $setting_item_fk,
                        ":basis" => $setting_basis,
                        ":applyon_formula" => $setting_applyon_formula,
                        ":rounding" => $setting_rounding));
                    $setting_rwid = $con->lastInsertId();
                }

                $arr_needed_ids[] = $setting_rwid;

                $outcome = saveFlatRateTaxCommiValues($setting_rwid, $arr_values);

                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);

        $sql = "DELETE FROM tblspecial_offer_flatrate_taxcomm 
                    WHERE spo_fk=:id 
                    AND buying_selling=:buying_selling";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id, ":buying_selling" => $buying_selling));


        return "OK";
    } catch (Exception $ex) {
        return "$buying_selling: " . $ex->getMessage();
    }
}

function saveFlatRateTaxCommiValues($setting_rwid, $arr_values) {

    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_values); $i++) {
            $value_rwid = $arr_values[$i]["value_rwid"];
            $value_currency_fk = $arr_values[$i]["value_currency_fk"];
            $value_value = $arr_values[$i]["value_value"];
            $value_action = $arr_values[$i]["value_action"];
            
            if ($value_action == "DELETE") {
                $sql = "DELETE FROM tblspecial_offer_flatrate_taxcomm_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                
                $sql = "SELECT * FROM 
                tblspecial_offer_flatrate_taxcomm_values WHERE
                special_offer_taxcomm_fk=:special_offer_taxcomm_fk
                AND currency_fk=:currency_fk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ":special_offer_taxcomm_fk" => $setting_rwid,
                ":currency_fk" => $value_currency_fk));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $value_rwid = $rw["id"];
                $sql = "UPDATE tblspecial_offer_flatrate_taxcomm_values SET 
                    value=:value WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $value_rwid,
                    ":value" => $value_value));
            } else {
                $sql = "INSERT INTO tblspecial_offer_flatrate_taxcomm_values 
                    (special_offer_taxcomm_fk,currency_fk,value)
                    VALUES
                    (:special_offer_taxcomm_fk,:currency_fk,:value)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":special_offer_taxcomm_fk" => $setting_rwid,
                    ":currency_fk" => $value_currency_fk,
                    ":value" => $value_value));

                $value_rwid = $con->lastInsertId();
            }

            $arr_needed_ids[] = $value_rwid;
            
            }
        }


        //clean up

        $sql = "DELETE FROM tblspecial_offer_flatrate_taxcomm_values WHERE 
                    special_offer_taxcomm_fk=:id ";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $setting_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "TAXCOMMI VALUES: " . $ex->getMessage();
    }
}

function saveflatratecurrencymapping($currencymap_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($currencymap_grid); $i++) {

            $rwid = $currencymap_grid[$i]["rwid"];
            $currency_id_buy = $currencymap_grid[$i]["cells"]["currency_buy"];
            $action = $currencymap_grid[$i]["cells"]["action"];
            $currency_id_sell = $currencymap_grid[$i]["cells"]["currency_id_sell"];

            if ($action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_currency_mapping WHERE 
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else {
                $sql = "SELECT * FROM 
                        tblspecial_offer_flatrate_currency_mapping 
                        WHERE spo_fk=:id AND 
                        currencybuy_fk=:currencybuy_fk AND 
                        currencysell_fk=:currencysell_fk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id,
                    ":currencybuy_fk" => $currency_id_buy,
                    ":currencysell_fk" => $currency_id_sell));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_currency_mapping
                            (spo_fk,currencybuy_fk,currencysell_fk) 
                            VALUES 
                            (:spo_fk,:currencybuy_fk,:currencysell_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_fk" => $id,
                        ":currencybuy_fk" => $currency_id_buy,
                        ":currencysell_fk" => $currency_id_sell));

                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //===================================
        //now delete remaining non needed ids
        $ids_to_delete = implode(",", $arr_needed_ids);
        if ($ids_to_delete != "") {
            $sql = "DELETE FROM tblspecial_offer_flatrate_currency_mapping WHERE 
                spo_fk=:id AND id NOT IN ($ids_to_delete)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: CURRENCY MAPPING: " . $ex->getMessage();
    }
}

function saveflatratecurrencyexgrates($exchrates_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($exchrates_grid); $i++) {

            $rates_id = $exchrates_grid[$i]["rwid"];
            $rates_exchange_rate = $exchrates_grid[$i]["cells"]["rates_exchange_rate"];
            $rates_action = $exchrates_grid[$i]["cells"]["action"];
            $currency_id_from = $exchrates_grid[$i]["cells"]["currency_id_from"];
            $currency_id_to = $exchrates_grid[$i]["cells"]["currency_id_to"];

            if ($rates_action == "DELETE") {
                $sql = "DELETE FROM 
                        tblspecial_offer_flatrate_currency_exchangerates WHERE 
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rates_id));
            } else {
                $sql = "SELECT * FROM tblspecial_offer_flatrate_currency_exchangerates 
                        WHERE spo_fk=:id AND 
                        from_currencyfk=:from_currencyfk AND 
                        to_currencyfk=:to_currencyfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id,
                    ":from_currencyfk" => $currency_id_from,
                    ":to_currencyfk" => $currency_id_to));
                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];

                    $sql = "UPDATE tblspecial_offer_flatrate_currency_exchangerates 
                            SET exchange_rate=:exchange_rate
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $rw["id"],
                        ":exchange_rate" => $rates_exchange_rate));
                } else {
                    $sql = "INSERT INTO 
                            tblspecial_offer_flatrate_currency_exchangerates
                            (spo_fk,from_currencyfk,to_currencyfk,
                            exchange_rate) 
                            VALUES 
                            (:spo_fk,:from_currencyfk,:to_currencyfk,
                            :exchange_rate)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spo_fk" => $id,
                        ":from_currencyfk" => $currency_id_from,
                        ":to_currencyfk" => $currency_id_to,
                        ":exchange_rate" => $rates_exchange_rate));

                    $arr_needed_ids[] = $con->lastInsertId();
                }
            }
        }

        //===================================
        //now delete remaining non needed ids
        $ids_to_delete = implode(",", $arr_needed_ids);
        if ($ids_to_delete != "") {
            $sql = "DELETE FROM tblspecial_offer_flatrate_currency_exchangerates WHERE 
                spo_fk=:id AND id NOT IN ($ids_to_delete)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: EXCHANGE RATES: " . $ex->getMessage();
    }
}

function saveflatratecurrencydetails($currency_details) {
    try {

        $mycostprice_currencyfk = $currency_details["mycostprice_currencyfk"];
        $selected_currency_buy_ids = $currency_details["selected_currency_buy_ids"];
        $selected_currency_sell_ids = $currency_details["selected_currency_sell_ids"];


        global $con;
        global $id;


        //check if exists
        $sql = " UPDATE tblspecial_offer SET " .
                " flatrate_mycostprice_currencyfk=:costprice_currencyfk " .
                " WHERE id=:id";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id, ":costprice_currencyfk" => $mycostprice_currencyfk));



        //====================================================================
        //CURRENCIES BUY
        $outcome = saveflatratecurrenciesbuysell("BUY", $selected_currency_buy_ids);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }

        //====================================================================
        //CURRENCIES SELL
        $outcome = saveflatratecurrenciesbuysell("SELL", $selected_currency_sell_ids);
        if ($outcome != "OK") {
            throw new Exception($outcome);
        }


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: CURRENCY DETAILS: " . $ex->getMessage();
    }
}

function saveflatratecurrenciesbuysell($bs, $currencyids) {
    try {

        global $con;
        global $id;

        //=========================================
        if ($currencyids != "") {
            $sql = "DELETE FROM tblspecial_offer_flatrate_currency 
                    WHERE spo_fk=:id AND buy_sell='$bs' AND
                    currencyfk NOT IN ($currencyids)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));


            $arr_currency_ids = explode(",", $currencyids);
            for ($i = 0; $i < count($arr_currency_ids); $i++) {
                $curid = trim($arr_currency_ids[$i]);
                if ($curid != "") {
                    
                }
                $sql = "SELECT * FROM 
                    tblspecial_offer_flatrate_currency WHERE 
                    spo_fk=:id AND buy_sell='$bs' AND 
                    currencyfk=:currencyfk";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":currencyfk" => $curid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert 
                    $sql = "INSERT INTO tblspecial_offer_flatrate_currency 
                        (spo_fk,buy_sell,currencyfk) 
                        VALUES (:id,'$bs',:currencyfk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $id, ":currencyfk" => $curid));
                }
            }
        }
        return "OK";
    } catch (Exception $ex) {
        return "SAVE CURRENCY $bs: " . $ex->getMessage();
    }
}

function saveflatratecancellation($cancellation_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();
        $suppid = "";

        for ($i = 0; $i < count($cancellation_grid); $i++) {

            $cancellation_type = $cancellation_grid[$i]["cells"]["cancellation_type"];
            $charge_basis = $cancellation_grid[$i]["cells"]["charge_basis"];
            $charge_value = $cancellation_grid[$i]["cells"]["charge_value"];
            $days_before_arrival_from = utils_stringBlank($cancellation_grid[$i]["cells"]["days_before_arrival_from"], null);
            $days_before_arrival_to = utils_stringBlank($cancellation_grid[$i]["cells"]["days_before_arrival_to"], null);
            $date_before_arrival_from = utils_stringBlank($cancellation_grid[$i]["cells"]["date_before_arrival_from"], null);
            $date_before_arrival_to = utils_stringBlank($cancellation_grid[$i]["cells"]["date_before_arrival_to"], null);
            $rooms_ids = $cancellation_grid[$i]["cells"]["rooms_ids"];
            $dateperiods = $cancellation_grid[$i]["cells"]["dateperiods"];


            if (!is_null($date_before_arrival_from)) {
                $date_before_arrival_from = date("Y-m-d", strtotime(str_replace("/", "-", $date_before_arrival_from)));
            }
            if (!is_null($date_before_arrival_to)) {
                $date_before_arrival_to = date("Y-m-d", strtotime(str_replace("/", "-", $date_before_arrival_to)));
            }



            //insert
            $sql = "INSERT INTO 
                    tblspecial_offer_flatrate_cancellation 
                    (spofk, cancellation_type, charge_basis, charge_value,
                    days_before_arrival_from,days_before_arrival_to,
                    dates_before_arrival_from,dates_before_arrival_to) 
                    VALUES 
                    (:spo_fk, :cancellation_type, :charge_basis, :charge_value,
                    :days_before_arrival_from,:days_before_arrival_to,
                    :dates_before_arrival_from,:dates_before_arrival_to)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id,
                ":cancellation_type" => $cancellation_type,
                ":charge_basis" => $charge_basis,
                ":charge_value" => $charge_value,
                ":days_before_arrival_from" => $days_before_arrival_from,
                ":days_before_arrival_to" => $days_before_arrival_to,
                ":dates_before_arrival_from" => $date_before_arrival_from,
                ":dates_before_arrival_to" => $date_before_arrival_to));
            $suppid = $con->lastInsertId();
            $arr_needed_ids[] = $suppid;



            //====================================================================
            //save checkinout date periods
            $outcome = saveflatratecancellation_dateperiods($suppid, $dateperiods);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //save checinout rooms
            $outcome = saveflatratecancellation_rooms($suppid, $rooms_ids);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //====================================================================
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM 
                    tblspecial_offer_flatrate_cancellation
                    WHERE 
                    spofk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: CANCELLATION: " . $ex->getMessage();
    }
}

function saveflatratecheckinout($checkinout_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();
        $suppid = "";

        for ($i = 0; $i < count($checkinout_grid); $i++) {

            $checkinout_type = $checkinout_grid[$i]["cells"]["checkinout_type"];
            $time_before_after = $checkinout_grid[$i]["cells"]["time_before_after"];
            $time_checkinout = $checkinout_grid[$i]["cells"]["time_checkinout"];
            $charge_basis = $checkinout_grid[$i]["cells"]["charge_basis"];
            $charge_value = $checkinout_grid[$i]["cells"]["charge_value"];
            $rooms_ids = $checkinout_grid[$i]["cells"]["rooms_ids"];
            $dateperiods = $checkinout_grid[$i]["cells"]["dateperiods"];


            //insert
            $sql = "INSERT INTO 
                    tblspecial_offer_flatrate_checkinout 
                    (spofk, checkinout_type, time_before_after, time_checkinout,
                    charge_basis,charge_value) 
                    VALUES 
                    (:spo_fk, :checkinout_type, :time_before_after, :time_checkinout,
                    :charge_basis,:charge_value)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id,
                ":checkinout_type" => $checkinout_type,
                ":time_before_after" => $time_before_after,
                ":time_checkinout" => $time_checkinout,
                ":charge_basis" => $charge_basis,
                ":charge_value" => $charge_value));
            $suppid = $con->lastInsertId();
            $arr_needed_ids[] = $suppid;



            //====================================================================
            //save checkinout date periods
            $outcome = saveflatratecheckinout_dateperiods($suppid, $dateperiods);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //save checinout rooms
            $outcome = saveflatratecheckinout_rooms($suppid, $rooms_ids);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //====================================================================
        }


        //clean up
        $sql = "DELETE FROM 
                tblspecial_offer_flatrate_checkinout
                WHERE 
                spofk=:spo_fk";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }


        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: CHECK IN OUT: " . $ex->getMessage();
    }
}

function saveflatratesupp($supp_grid) {

    try {

        global $con;
        global $id;

        $arr_needed_ids = array();
        $suppid = "";

        for ($i = 0; $i < count($supp_grid); $i++) {

            $mealplanfk = $supp_grid[$i]["cells"]["mealplanfk"];
            $ismain = $supp_grid[$i]["cells"]["ismain"];
            $dateperiods = $supp_grid[$i]["cells"]["dateperiods"];
            $adult = utils_stringBlank($supp_grid[$i]["cells"]["adult"], null);


            //insert
            $sql = "INSERT INTO 
                    tblspecial_offer_flatrate_mealsupp 
                    (spofk, mealplanfk, adult, ismain) 
                    VALUES 
                    (:spo_fk, :mealplanfk, :adult, :ismain)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id,
                ":mealplanfk" => $mealplanfk,
                ":adult" => $adult, ":ismain" => $ismain));
            $suppid = $con->lastInsertId();
            $arr_needed_ids[] = $suppid;



            //====================================================================
            //save group date periods
            $outcome = saveflatratesupp_dateperiods($suppid, $dateperiods);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //save children ages
            $outcome = saveflatratesupp_childrenages($suppid, $supp_grid[$i]["cells"]);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //====================================================================
        }



        //clean up
        $deleteids = implode(",", $arr_needed_ids);

        $sql = "DELETE FROM 
                tblspecial_offer_flatrate_mealsupp
                WHERE 
                spofk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: MEAL SUPP: " . $ex->getMessage();
    }
}

function saveflatratevaliditygroup($group_grid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();
        $grpid = "";

        for ($i = 0; $i < count($group_grid); $i++) {

            $X = $group_grid[$i]["cells"]["X"];
            $dt_from = utils_DMY_YMD($group_grid[$i]["cells"]["dt_from"]);
            $dt_to = utils_DMY_YMD($group_grid[$i]["cells"]["dt_to"]);
            $group_no = $group_grid[$i]["cells"]["group_no"];
            $children_ages_ids = $group_grid[$i]["cells"]["children_ages_ids"];


            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_flatrate_group_validity_period
                    WHERE spo_fk=:spo_fk AND 
                    dt_from=:dt_from AND dt_to=:dt_to AND groupno=:groupno";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":dt_from" => $dt_from,
                ":dt_to" => $dt_to, ":groupno" => $group_no));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
                $grpid = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_flatrate_group_validity_period 
                            (spo_fk, dt_from, dt_to,groupno) 
                            VALUES 
                            (:spo_fk, :dt_from, :dt_to, :groupno)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":dt_from" => $dt_from,
                    ":dt_to" => $dt_to, ":groupno" => $group_no));
                $grpid = $con->lastInsertId();
                $arr_needed_ids[] = $grpid;
            }

            //====================================================================

            $outcome = saveflatratevaliditygroup_childrenages($grpid, $children_ages_ids);
            if ($outcome != "OK") {
                throw new Exception($outcome);
            }

            //====================================================================
        }



        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_flatrate_group_validity_period
                    WHERE 
                    spo_fk=:spo_fk";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE FLAT RATE: GROUP VALIDITY: " . $ex->getMessage();
    }
}

function saveroomupgrade($roomgrid) {
    try {

        global $con;
        global $id;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($roomgrid); $i++) {

            $room_from_fk = $roomgrid[$i]["cells"]["room_from_fk"];
            $room_to_fk = $roomgrid[$i]["cells"]["room_to_fk"];


            //check if exists
            $sql = "SELECT * FROM tblspecial_offer_upgrade_rooms
                        WHERE spo_fk=:spo_fk AND 
                        room_from_fk=:room_from_fk AND
                        room_to_fk=:room_to_fk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":spo_fk" => $id, ":room_from_fk" => $room_from_fk,
                ":room_to_fk" => $room_to_fk));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_needed_ids[] = $rw["id"];
            } else {
                //insert
                $sql = "INSERT INTO tblspecial_offer_upgrade_rooms 
                            (spo_fk, room_from_fk, room_to_fk) 
                            VALUES 
                            (:spo_fk, :room_from_fk, :room_to_fk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spo_fk" => $id, ":room_from_fk" => $room_from_fk, ":room_to_fk" => $room_to_fk));
                $arr_needed_ids[] = $con->lastInsertId();
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        $sql = "DELETE FROM tblspecial_offer_upgrade_rooms WHERE 
                    spo_fk=:spo_fk ";
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":spo_fk" => $id));


        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOM UPGRADE: " . $ex->getMessage();
    }
}

function savetouroperators($toids) {
    try {

        global $con;
        global $id;


        $sql = "DELETE FROM tblspecial_offer_touroperator 
                WHERE spofk=:id AND 
                tofk NOT IN ($toids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_to_ids = explode(",", $toids);
        for ($i = 0; $i < count($arr_to_ids); $i++) {
            $toid = trim($arr_to_ids[$i]);
            $sql = "SELECT * FROM tblspecial_offer_touroperator WHERE 
                spofk=:id AND tofk=:tofk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":tofk" => $toid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblspecial_offer_touroperator 
                    (spofk,tofk) 
                    VALUES (:id,:tofk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":tofk" => $toid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TOUR OPERATORS: " . $ex->getMessage();
    }
}

?>
