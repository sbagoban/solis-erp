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

    $arrmain_details = json_decode($_POST["main_details"], true); //+

    $arrcurrency_details = json_decode($_POST["currency_details"], true); //+
    $obj_currency_exchrates = json_decode($_POST["currency_exchrates"], true); //+

    $arrtaxcomm = json_decode($_POST["taxcomm"], true); //+

    $arrcapacity = json_decode($_POST["capacity"], true);

    $arrnotes_details = json_decode($_POST["notes_details"], true); //+
    //ok, lets do this:

    $id = $arrmain_details["id"];
    $hotelfk = $arrmain_details["hotelfk"];
    $contractname = $arrmain_details["contractname"];
    $invoice_text = $arrmain_details["invoice_text"];
    $active_internal = $arrmain_details["active_internal"];
    $active_external = $arrmain_details["active_external"];
    $non_refundable = $arrmain_details["non_refundable"];
    $date_received = date("Y-m-d H:i:s", strtotime($arrmain_details["date_received"]));
    $active_from = date("Y-m-d H:i:s", strtotime($arrmain_details["active_from"]));
    $active_to = date("Y-m-d H:i:s", strtotime($arrmain_details["active_to"]));
    $countryfk = $arrmain_details["countryfk"];
    $mealplanfk = $arrmain_details["mealplan_fk"];
    $areafk = $arrmain_details["areafk"];
    $coastfk = $arrmain_details["coastfk"];
    $selected_rate_codes_ids = $arrmain_details["selected_rate_codes_ids"];
    $cross_season = $arrmain_details["cross_season"];
    $rollover_basis = $arrmain_details["rollover_basis"];
    $rollover_value = $arrmain_details["rollover_value"];
    $children_ages_ids = $arrmain_details["children_ages_ids"];
    $market_countries_ids = $arrmain_details["market_countries_ids"];
    $touroperator_ids = $arrmain_details["tour_operators_ids"];
    $rooms_ids = $arrmain_details["rooms_ids"];
    $departments_ids = $arrmain_details["departments_ids"];

    $internal_notes = $arrnotes_details["internal_notes"];
    $external_notes = $arrnotes_details["external_notes"];


    $mycostprice_currencyfk = $arrcurrency_details["mycostprice_currencyfk"];
    $selected_currency_buy_ids = $arrcurrency_details["selected_currency_buy_ids"];
    $selected_currency_sell_ids = $arrcurrency_details["selected_currency_sell_ids"];


    //===========================================================================
    //check for overlapping contract
    //mealplan + active_from - active_to + countries + rooms
    $sql = "SELECT sc.id, sc.contractname, 
            GROUP_CONCAT(DISTINCT c.countrycode_3 ORDER BY countrycode_3 ASC SEPARATOR ',') countries, 
            GROUP_CONCAT(DISTINCT hr.roomname ORDER BY roomname ASC SEPARATOR ',') AS rooms
            FROM tblservice_contract sc
            INNER JOIN tblservice_contract_countries scc ON sc.id = scc.service_contract_fk
            INNER JOIN tblservice_contract_rooms scr ON sc.id = scr.servicecontractfk
            INNER JOIN tblcountries c on scc.countryfk = c.id
            INNER JOIN tblhotel_rooms hr on scr.roomfk = hr.id
            WHERE sc.hotelfk = :hotelfk 
            AND :active_from <= sc.active_to  AND :active_to >= sc.active_from
            AND sc.mealplan_fk = :mealplan_fk
            and sc.deleted = 0 AND sc.id <> :id
            AND scc.countryfk IN ($market_countries_ids) 
            AND scr.roomfk IN ($rooms_ids)
            group by sc.id, sc.contractname";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":hotelfk" => $hotelfk,
        ":active_from" => $active_from,
        ":active_to" => $active_to,
        ":mealplan_fk" => $mealplanfk));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

        throw new Exception("This Contract <b>overlaps</b> with an another contract based on:<br>" .
                "1. Validity Date<br>2.Meal Plan<br>3. Market Countries <br>4. Rooms");
    }

    //===========================================================================

    if ($id == "-1") {

        $sql = "INSERT INTO tblservice_contract
                (service_code,date_created) 
                VALUES ('ACC',NOW())";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        $id = $con->lastInsertId();
    }


    //=============================================
    //MAIN 

    $sql = "UPDATE tblservice_contract
            SET
            contractname = :contractname,
            invoice_text = :invoice_text,
            active_internal = :active_internal,
            active_external = :active_external,
            non_refundable = :non_refundable,            
            areafk = :areafk,
            coastfk = :coastfk,
            hotelfk = :hotelfk,
            mealplan_fk = :mealplan_fk,
            days_mon = :days_mon,
            days_tue = :days_tue,
            days_web = :days_web,
            days_thu = :days_thu,
            days_fri = :days_fri,
            days_sat = :days_sat,
            days_sun = :days_sun,
            cross_season = :cross_season,
            rollover_basis = :rollover_basis,
            rollover_value = :rollover_value,
            date_received = :date_received,
            date_modified = NOW(),
            countryfk = :countryfk ,
            active_from = :active_from,
            active_to = :active_to,
            mycostprice_currencyfk=:mycostprice_currencyfk,
            internal_notes = :internal_notes,
            external_notes = :external_notes
            WHERE id = :id";

    $stmt = $con->prepare($sql);
    $stmt->execute(array(":id" => $id,
        ":contractname" => $contractname,
        ":invoice_text" => $invoice_text,
        ":active_internal" => $active_internal,
        ":active_external" => $active_external,
        ":non_refundable" => $non_refundable,
        ":areafk" => $areafk,
        ":coastfk" => $coastfk,
        ":hotelfk" => $hotelfk,
        ":mealplan_fk" => $mealplanfk,
        ":days_mon" => 1,
        ":days_tue" => 1,
        ":days_web" => 1,
        ":days_thu" => 1,
        ":days_fri" => 1,
        ":days_sat" => 1,
        ":days_sun" => 1,
        ":cross_season" => $cross_season,
        ":rollover_basis" => $rollover_basis,
        ":rollover_value" => $rollover_value,
        ":date_received" => $date_received,
        ":countryfk" => $countryfk,
        ":active_from" => $active_from,
        ":active_to" => $active_to,
        ":mycostprice_currencyfk" => $mycostprice_currencyfk,
        ":internal_notes" => $internal_notes,
        ":external_notes" => $external_notes));

    //========================================================================
    //RATES
    $outcome = saverates();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //========================================================================
    //COUNTRIES
    $outcome = savecountries();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //========================================================================
    //DEPARTMENTS    
    $outcome = savedepartments();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //========================================================================
    //ROOMS    
    $outcome = saverooms();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //========================================================================
    //TOUROPERATORS    
    $outcome = savetouroperators();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //========================================================================
    //CHILD AGES    
    $outcome = saveages();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //====================================================================
    //CURRENCIES BUY
    $outcome = savecurrenciesbuysell("BUY", $selected_currency_buy_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }
    //====================================================================
    //CURRENCIES SELL
    $outcome = savecurrenciesbuysell("SELL", $selected_currency_sell_ids);
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //======================================================================
    //CURRENCIES EXCHANGE RATES
    $outcome = saveCurrencyRates();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //======================================================================
    //CURRENCIES MAPPING
    $outcome = saveCurrencyMapping();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //========================================================================
    //TAX COMMI
    $outcome = saveTaxCommi();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //========================================================================
    //CAPACITY
    $outcome = saveCapacity();
    if ($outcome != "OK") {
        throw new Exception($outcome);
    }

    //=========================================================================
    //DONE

    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}

function saveCurrencyMapping() {
    try {

        global $con;
        global $id;
        global $obj_currency_exchrates;

        $arrcurrency_mapping = $obj_currency_exchrates["currency_mapping"];

        $arr_ids_needed = array();

        for ($i = 0; $i < count($arrcurrency_mapping); $i++) {
            $mapping_sell_currencyfk = $arrcurrency_mapping[$i]["mapping_sell_currencyfk"];
            $mapping_buy_currencyfk = $arrcurrency_mapping[$i]["mapping_buy_currencyfk"];
            $mapping_id = $arrcurrency_mapping[$i]["mapping_id"];
            $mapping_action = $arrcurrency_mapping[$i]["mapping_action"];

            if ($mapping_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_currency_mapping WHERE 
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $mapping_id));
            } else {
                $sql = "SELECT * FROM tblservice_contract_currency_mapping 
                        WHERE service_contract_fk=:id AND 
                        currencybuy_fk=:currencybuy_fk AND 
                        currencysell_fk=:currencysell_fk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id,
                    ":currencybuy_fk" => $mapping_buy_currencyfk,
                    ":currencysell_fk" => $mapping_sell_currencyfk));
                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_ids_needed[] = $rw["id"];
                } else {
                    $sql = "INSERT INTO tblservice_contract_currency_mapping
                            (service_contract_fk,currencybuy_fk,currencysell_fk) 
                            VALUES 
                            (:service_contract_fk,:currencybuy_fk,:currencysell_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_fk" => $id,
                        ":currencybuy_fk" => $mapping_buy_currencyfk,
                        ":currencysell_fk" => $mapping_sell_currencyfk));

                    $arr_ids_needed[] = $con->lastInsertId();
                }
            }
        }

        //===================================
        //now delete remaining non needed ids
        $ids_to_delete = implode(",", $arr_ids_needed);
        if ($ids_to_delete != "") {
            $sql = "DELETE FROM tblservice_contract_currency_mapping WHERE 
                service_contract_fk=:id AND id NOT IN ($ids_to_delete)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {

        return $ex->getMessage();
    }
}

function saveCurrencyRates() {
    try {

        global $con;
        global $id;
        global $obj_currency_exchrates;

        $arrcurrency_exchrates = $obj_currency_exchrates["exchange_rates"];

        $arr_ids_needed = array();

        for ($i = 0; $i < count($arrcurrency_exchrates); $i++) {
            $rates_from_currencyfk = $arrcurrency_exchrates[$i]["rates_from_currencyfk"];
            $rates_to_currencyfk = $arrcurrency_exchrates[$i]["rates_to_currencyfk"];
            $rates_id = $arrcurrency_exchrates[$i]["rates_id"];
            $rates_exchange_rate = $arrcurrency_exchrates[$i]["rates_exchange_rate"];
            $rates_action = $arrcurrency_exchrates[$i]["rates_action"];

            if ($rates_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_currency_exchangerates WHERE 
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rates_id));
            } else {
                $sql = "SELECT * FROM tblservice_contract_currency_exchangerates 
                        WHERE service_contract_fk=:id AND 
                        from_currencyfk=:from_currencyfk AND 
                        to_currencyfk=:to_currencyfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id,
                    ":from_currencyfk" => $rates_from_currencyfk,
                    ":to_currencyfk" => $rates_to_currencyfk));
                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_ids_needed[] = $rw["id"];

                    $sql = "UPDATE tblservice_contract_currency_exchangerates 
                            SET exchange_rate=:exchange_rate
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $rw["id"],
                        ":exchange_rate" => $rates_exchange_rate));
                } else {
                    $sql = "INSERT INTO tblservice_contract_currency_exchangerates
                            (service_contract_fk,from_currencyfk,to_currencyfk,
                            exchange_rate) 
                            VALUES 
                            (:service_contract_fk,:from_currencyfk,:to_currencyfk,
                            :exchange_rate)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_fk" => $id,
                        ":from_currencyfk" => $rates_from_currencyfk,
                        ":to_currencyfk" => $rates_to_currencyfk,
                        ":exchange_rate" => $rates_exchange_rate));

                    $arr_ids_needed[] = $con->lastInsertId();
                }
            }
        }

        //===================================
        //now delete remaining non needed ids
        $ids_to_delete = implode(",", $arr_ids_needed);
        if ($ids_to_delete != "") {
            $sql = "DELETE FROM tblservice_contract_currency_exchangerates WHERE 
                service_contract_fk=:id AND id NOT IN ($ids_to_delete)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {

        return $ex->getMessage();
    }
}

function saverates() {
    try {

        global $con;
        global $id;
        global $selected_rate_codes_ids;


        $sql = "DELETE FROM tblservice_contract_rates 
                WHERE service_contract_fk=:id AND 
                ratefk NOT IN ($selected_rate_codes_ids)";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_rates_ids = explode(",", $selected_rate_codes_ids);
        for ($i = 0; $i < count($arr_rates_ids); $i++) {
            $rateid = $arr_rates_ids[$i];
            $sql = "SELECT * FROM tblservice_contract_rates WHERE 
                service_contract_fk=:id AND ratefk=:ratefk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":ratefk" => $rateid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblservice_contract_rates (service_contract_fk,ratefk) 
                    VALUES (:id,:ratefk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":ratefk" => $rateid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE RATES: " . $ex->getMessage();
    }
}

function savecountries() {
    try {


        global $con;
        global $market_countries_ids;
        global $id;

        $sql = "DELETE FROM tblservice_contract_countries 
                WHERE service_contract_fk=:id AND 
                countryfk NOT IN ($market_countries_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_countries_ids = explode(",", $market_countries_ids);
        for ($i = 0; $i < count($arr_countries_ids); $i++) {
            $countryid = $arr_countries_ids[$i];

            //if($countryid != "")
            //{

            $sql = "SELECT * FROM tblservice_contract_countries WHERE 
                service_contract_fk=:id AND countryfk=:countryfk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":countryfk" => $countryid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblservice_contract_countries 
                        (service_contract_fk,countryfk) 
                        VALUES (:id,:countryfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":countryfk" => $countryid));
            }
            //}            
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE COUNTRIES: " . $ex->getMessage();
    }
}

function savedepartments() {

    try {

        global $con;
        global $departments_ids;
        global $id;

        $sql = "DELETE FROM tblservice_contract_departments 
                WHERE service_contract_fk=:id AND 
                departmentfk NOT IN ($departments_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_department_ids = explode(",", $departments_ids);
        for ($i = 0; $i < count($arr_department_ids); $i++) {
            $departmentid = $arr_department_ids[$i];
            $sql = "SELECT * FROM tblservice_contract_departments WHERE 
                service_contract_fk=:id AND departmentfk=:departmentfk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":departmentfk" => $departmentid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblservice_contract_departments 
                    (service_contract_fk,departmentfk) 
                    VALUES (:id,:departmentfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":departmentfk" => $departmentid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE DEPARTMENTS: " . $ex->getMessage();
    }
}

function savetouroperators() {
    try {

        global $con;
        global $touroperator_ids;
        global $id;


        $sql = "DELETE FROM tblservice_contract_touroperator 
                WHERE service_contract_fk=:id AND 
                tofk NOT IN ($touroperator_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_to_ids = explode(",", $touroperator_ids);
        for ($i = 0; $i < count($arr_to_ids); $i++) {
            $toid = trim($arr_to_ids[$i]);
            $sql = "SELECT * FROM tblservice_contract_touroperator WHERE 
                service_contract_fk=:id AND tofk=:tofk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":tofk" => $toid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblservice_contract_touroperator 
                    (service_contract_fk,tofk) 
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

function saverooms() {

    try {

        global $con;
        global $rooms_ids;
        global $id;


        $sql = "DELETE FROM tblservice_contract_rooms 
                WHERE servicecontractfk=:id AND 
                roomfk NOT IN ($rooms_ids)";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $id));

        $arr_room_ids = explode(",", $rooms_ids);
        for ($i = 0; $i < count($arr_room_ids); $i++) {
            $roomid = $arr_room_ids[$i];
            $sql = "SELECT * FROM tblservice_contract_rooms WHERE 
                servicecontractfk=:id AND roomfk=:roomfk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":roomfk" => $roomid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert 
                $sql = "INSERT INTO tblservice_contract_rooms 
                    (servicecontractfk,roomfk) 
                    VALUES (:id,:roomfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":roomfk" => $roomid));
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ROOMS: " . $ex->getMessage();
    }
}

function saveages() {

    global $con;
    global $children_ages_ids;
    global $id;

    try {

        if ($children_ages_ids != "") {
            $sql = "DELETE FROM tblservice_contract_childages 
                    WHERE service_contract_fk=:id AND 
                    child_age_fk NOT IN ($children_ages_ids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));


            $arr_childage_ids = explode(",", $children_ages_ids);
            for ($i = 0; $i < count($arr_childage_ids); $i++) {
                $ageid = $arr_childage_ids[$i];
                $sql = "SELECT * FROM tblservice_contract_childages WHERE 
                    service_contract_fk=:id AND child_age_fk=:child_age_fk";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":child_age_fk" => $ageid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert 
                    $sql = "INSERT INTO tblservice_contract_childages 
                        (service_contract_fk,child_age_fk) 
                        VALUES (:id,:child_age_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $id, ":child_age_fk" => $ageid));
                }
            }
        } else {
            //no child ages selected
            $sql = "DELETE FROM tblservice_contract_childages 
                    WHERE service_contract_fk=:id";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CHILD_AGES: " . $ex->getMessage();
    }
}

function savecurrenciesbuysell($bs, $currencyids) {
    try {

        global $con;
        global $id;

        //=========================================
        if ($currencyids != "") {
            $sql = "DELETE FROM tblservice_contract_currency 
                    WHERE service_contract_fk=:id AND buy_sell='$bs' AND
                    currencyfk NOT IN ($currencyids)";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));


            $arr_currency_ids = explode(",", $currencyids);
            for ($i = 0; $i < count($arr_currency_ids); $i++) {
                $curid = trim($arr_currency_ids[$i]);
                if ($curid != "") {
                    
                }
                $sql = "SELECT * FROM tblservice_contract_currency WHERE 
                    service_contract_fk=:id AND buy_sell='$bs' AND 
                    currencyfk=:currencyfk";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $id, ":currencyfk" => $curid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert 
                    $sql = "INSERT INTO tblservice_contract_currency 
                        (service_contract_fk,buy_sell,currencyfk) 
                        VALUES (:id,'$bs',:currencyfk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $id, ":currencyfk" => $curid));
                }
            }
        }
        return "OK";
    } catch (Exception $ex) {
        return "SAVE CURRENCY: " . $ex->getMessage();
    }
}

function saveRmMinStay($date_rwid, $arrminstay) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arrminstay); $i++) {

            $rwid = $arrminstay[$i]["minstay_rwid"];
            $duration = $arrminstay[$i]["minstay_duration"];
            $description = $arrminstay[$i]["minstay_description"];
            $action = $arrminstay[$i]["minstay_action"];


            if ($action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_minstay WHERE
                        service_contract_roomcapacity_dates_fk=:date_rwid
                        AND id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":date_rwid" => $date_rwid, ":id" => $rwid));
            } else {

                if ($rwid < 0) {

                    //insert
                    $sql = "INSERT INTO tblservice_contract_minstay 
                            (service_contract_roomcapacity_dates_fk,
                            description,duration)
                            VALUES 
                            (:date_rwid,
                            :description,:duration)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":date_rwid" => $date_rwid,
                        ":description" => $description,
                        ":duration" => $duration));
                    $rwid = $con->lastInsertId();
                } else {

                    //update
                    $sql = "UPDATE tblservice_contract_minstay 
                            SET 
                            description=:description,
                            duration=:duration
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":description" => $description,
                        ":duration" => $duration,
                        ":id" => $rwid));
                }

                $arr_needed_ids[] = $rwid;
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_minstay WHERE 
                    service_contract_roomcapacity_dates_fk=:date_rwid 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_rwid" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE MINSTAY: " . $ex->getMessage();
    }
}

function saveRmMealSupp($date_rwid, $arrmeal) {
    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arrmeal); $i++) {


            $meal_id = $arrmeal[$i]["meal_rwid"];
            $meal_mealplanfk = $arrmeal[$i]["meal_mealplanfk"];
            $meal_ismain = $arrmeal[$i]["meal_ismain"];
            $meal_adult_count = $arrmeal[$i]["meal_adult_count"];
            $meal_action = $arrmeal[$i]["meal_action"];
            $arr_meal_children = $arrmeal[$i]["meal_children"];

            if (!is_numeric($meal_adult_count)) {
                $meal_adult_count = 0;
            }


            if ($meal_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_mealsupplement WHERE 
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $meal_id));
            } else {
                if ($meal_id < 0) {
                    $sql = "INSERT INTO tblservice_contract_mealsupplement 
                            (service_contract_roomcapacity_dates_fk,
                            mealplanfk,is_main,adult_count)
                            VALUES
                            (:service_contract_roomcapacity_dates_fk,
                            :mealplanfk,:is_main,:adult_count)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_roomcapacity_dates_fk" => $date_rwid,
                        ":mealplanfk" => $meal_mealplanfk,
                        ":is_main" => $meal_ismain,
                        ":adult_count" => $meal_adult_count));

                    $meal_id = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_mealsupplement SET 
                            mealplanfk=:mealplanfk,is_main=:is_main,
                            adult_count=:adult_count
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $meal_id,
                        ":mealplanfk" => $meal_mealplanfk,
                        ":is_main" => $meal_ismain,
                        ":adult_count" => $meal_adult_count));
                }

                $arr_needed_ids[] = $meal_id;

                //insert update child ages
                $outcome = ammendMealChildAges($meal_id, $arr_meal_children);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_mealsupplement WHERE 
                    service_contract_roomcapacity_dates_fk=:date_rwid 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_rwid" => $date_rwid));
        }



        return "OK";
    } catch (Exception $ex) {

        return "SAVE MEAL SUPPLEMENT: " . $ex->getMessage();
    }
}

function ammendMealChildAges($meal_id, $arr_meal_children) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_meal_children); $i++) {


            $age_rwid = $arr_meal_children[$i]["child_rwid"];
            $age_from = $arr_meal_children[$i]["child_agefrom"];
            $age_to = $arr_meal_children[$i]["child_ageto"];
            $child_count = $arr_meal_children[$i]["child_count"];

            if (!is_numeric($child_count)) {
                $child_count = 0;
            }

            $sql = "SELECT * FROM tblservice_contract_mealsupplement_childages WHERE 
                    service_contract_mealsupplement_fk=:meal_id 
                    AND child_age_from=:child_age_from
                    AND child_age_to=:child_age_to 
                    AND child_count=:child_count";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ":meal_id" => $meal_id,
                ":child_age_from" => $age_from,
                ":child_age_to" => $age_to,
                ":child_count" => $child_count));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $age_rwid = $rw["id"];
            } else {
                //insert 
                $sql = "INSERT INTO tblservice_contract_mealsupplement_childages
                        (service_contract_mealsupplement_fk,child_age_from,
                        child_age_to,child_count) 
                        VALUES (:meal_id,:child_age_from,
                        :child_age_to,:child_count)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":meal_id" => $meal_id,
                    ":child_age_from" => $age_from,
                    ":child_age_to" => $age_to,
                    ":child_count" => $child_count));

                $age_rwid = $con->lastInsertId();
            }

            $arr_needed_ids[] = $age_rwid;
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_mealsupplement_childages WHERE 
                    service_contract_mealsupplement_fk=:meal_id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":meal_id" => $meal_id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE MEALS CHILDAGES: " . $ex->getMessage();
    }
}

function saveRmPoliciesCheckInOut($date_rwid, $arrcheckinout) {
    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arrcheckinout); $i++) {

            $checkinout_id = $arrcheckinout[$i]["checkinout_rwid"];
            $checkinout_policytype = $arrcheckinout[$i]["checkinout_policytype"];
            $checkinout_time_beforeafter = $arrcheckinout[$i]["checkinout_time_beforeafter"];
            $checkinout_checkinout_time = $arrcheckinout[$i]["checkinout_checkinout_time"];
            $checkinout_charge_type = $arrcheckinout[$i]["checkinout_charge_type"];
            $checkinout_charge_value = $arrcheckinout[$i]["checkinout_charge_value"];
            $checkinout_action = $arrcheckinout[$i]["checkinout_action"];

            if (!is_numeric($checkinout_charge_value)) {
                $checkinout_charge_value = 0;
            }

            if ($checkinout_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_checkinout WHERE
                        service_contract_roomcapacity_dates_fk=:date_rwid
                        AND id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":date_rwid" => $date_rwid, ":id" => $checkinout_id));
            } else {
                if ($checkinout_id < 0) {
                    $sql = "INSERT INTO tblservice_contract_checkinout
                            (service_contract_roomcapacity_dates_fk,policytype,
                            time_beforeafter,checkinout_time,charge_type,charge_value) 
                            VALUES
                            (:date_rwid,:policytype,
                            :time_beforeafter,:checkinout_time,:charge_type,:charge_value)
                            ";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":date_rwid" => $date_rwid,
                        ":policytype" => $checkinout_policytype,
                        ":time_beforeafter" => $checkinout_time_beforeafter,
                        ":checkinout_time" => $checkinout_checkinout_time,
                        ":charge_type" => $checkinout_charge_type,
                        ":charge_value" => $checkinout_charge_value));
                    $checkinout_id = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_checkinout SET 
                            policytype=:policytype,
                            time_beforeafter=:time_beforeafter,
                            checkinout_time=:checkinout_time,
                            charge_type=:charge_type,
                            charge_value=:charge_value 
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $checkinout_id,
                        ":policytype" => $checkinout_policytype,
                        ":time_beforeafter" => $checkinout_time_beforeafter,
                        ":checkinout_time" => $checkinout_checkinout_time,
                        ":charge_type" => $checkinout_charge_type,
                        ":charge_value" => $checkinout_charge_value));
                }

                $arr_needed_ids[] = $checkinout_id;
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_checkinout WHERE 
                    service_contract_roomcapacity_dates_fk=:date_rwid 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_rwid" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CHECKINOUT: " . $ex->getMessage();
    }
}

function saveRmPoliciesCancellation($date_rwid, $arrcancellation) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arrcancellation); $i++) {

            $cancellation_id = $arrcancellation[$i]["cancellation_rwid"];
            $cancellation_canceltype = $arrcancellation[$i]["cancellation_canceltype"];
            $cancellation_charge_method = $arrcancellation[$i]["cancellation_charge_method"];
            $cancellation_charge_value = $arrcancellation[$i]["cancellation_charge_value"];
            $cancellation_days_before_arrival_from = utils_stringBlank($arrcancellation[$i]["cancellation_days_before_arrival_from"], null);
            $cancellation_days_before_arrival_to = utils_stringBlank($arrcancellation[$i]["cancellation_days_before_arrival_to"], null);
            $cancellation_dates_before_arrival_from = utils_stringBlank($arrcancellation[$i]["cancellation_dates_before_arrival_from"], null);
            $cancellation_dates_before_arrival_to = utils_stringBlank($arrcancellation[$i]["cancellation_dates_before_arrival_to"], null);
            $cancellation_action = $arrcancellation[$i]["cancellation_action"];

            if ($cancellation_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_cancellation WHERE 
                        service_contract_roomcapacity_dates_fk=:date_rwid
                        AND id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":date_rwid" => $date_rwid, ":id" => $cancellation_id));
            } else {
                if ($cancellation_id < 0) {
                    $sql = "INSERT INTO tblservice_contract_cancellation 
                            (service_contract_roomcapacity_dates_fk,
                            canceltype,charge_method,charge_value,
                            days_before_arrival_from,days_before_arrival_to,
                            dates_before_arrival_from,dates_before_arrival_to)
                            VALUES
                            (:date_rwid,:canceltype,
                            :charge_method,:charge_value,
                            :days_before_arrival_from,:days_before_arrival_to,
                            :dates_before_arrival_from,:dates_before_arrival_to)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":date_rwid" => $date_rwid,
                        ":canceltype" => $cancellation_canceltype,
                        ":charge_method" => $cancellation_charge_method,
                        ":charge_value" => $cancellation_charge_value,
                        ":days_before_arrival_from" => $cancellation_days_before_arrival_from,
                        ":days_before_arrival_to" => $cancellation_days_before_arrival_to,
                        ":dates_before_arrival_from" => $cancellation_dates_before_arrival_from,
                        ":dates_before_arrival_to" => $cancellation_dates_before_arrival_to));

                    $cancellation_id = $con->lastInsertId();
                } else {
                    $sql = "UPDATE tblservice_contract_cancellation SET 
                            canceltype=:canceltype,
                            charge_method=:charge_method,
                            charge_value=:charge_value,
                            days_before_arrival_from=:days_before_arrival_from,
                            days_before_arrival_to=:days_before_arrival_to,
                            dates_before_arrival_from=:dates_before_arrival_from,
                            dates_before_arrival_to=:dates_before_arrival_to
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $cancellation_id,
                        ":canceltype" => $cancellation_canceltype,
                        ":charge_method" => $cancellation_charge_method,
                        ":charge_value" => $cancellation_charge_value,
                        ":days_before_arrival_from" => $cancellation_days_before_arrival_from,
                        ":days_before_arrival_to" => $cancellation_days_before_arrival_to,
                        ":dates_before_arrival_from" => $cancellation_dates_before_arrival_from,
                        ":dates_before_arrival_to" => $cancellation_dates_before_arrival_to));
                }
                $arr_needed_ids[] = $cancellation_id;
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_cancellation WHERE 
                    service_contract_roomcapacity_dates_fk=:date_rwid
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_rwid" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CANCELLATION: " . $ex->getMessage();
    }
}

function saveRmMealExtra($date_rwid, $arrextra) {
    try {

        global $id;
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arrextra); $i++) {

            $extra_id = $arrextra[$i]["extra_rwid"];
            $extra_extra_name = $arrextra[$i]["extra_extra_name"];
            $extra_mandatory = $arrextra[$i]["extra_mandatory"];
            $extra_include_diner_rate_bb = $arrextra[$i]["extra_include_diner_rate_bb"];
            $extra_hb_mealplan_fk = $arrextra[$i]["extra_hb_mealplan_fk"];
            $extra_bb_mealplan_fk = $arrextra[$i]["extra_bb_mealplan_fk"];
            $extra_extra_date = utils_stringBlank($arrextra[$i]["extra_extra_date"], null);
            $extra_adult_count = $arrextra[$i]["extra_adult_count"];
            $extra_action = $arrextra[$i]["extra_action"];
            $arr_extra_children = $arrextra[$i]["extra_children"];

            if (!is_numeric($extra_hb_mealplan_fk)) {
                $extra_hb_mealplan_fk = null;
            }
            if (!is_numeric($extra_bb_mealplan_fk)) {
                $extra_bb_mealplan_fk = null;
            }
            if (!is_numeric($extra_adult_count)) {
                $extra_adult_count = 0;
            }

            if ($extra_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_extrasupplement 
                        WHERE
                        service_contract_roomcapacity_dates_fk=:date_rwid
                        AND id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":date_rwid" => $date_rwid,
                    ":id" => $extra_id));
            } else {


                if ($extra_id < 0) {
                    $sql = "INSERT INTO tblservice_contract_extrasupplement 
                            (service_contract_roomcapacity_dates_fk,
                            extra_name,mandatory,
                            include_diner_rate_bb,hb_mealplan_fk,
                            bb_mealplan_fk,extra_date,adult_count)
                            VALUES
                            (:date_rwid,:extra_name,:mandatory,
                            :include_diner_rate_bb,:hb_mealplan_fk,
                            :bb_mealplan_fk,:extra_date,:adult_count)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":date_rwid" => $date_rwid,
                        ":extra_name" => $extra_extra_name,
                        ":mandatory" => $extra_mandatory,
                        ":include_diner_rate_bb" => $extra_include_diner_rate_bb,
                        ":hb_mealplan_fk" => $extra_hb_mealplan_fk,
                        ":bb_mealplan_fk" => $extra_bb_mealplan_fk,
                        ":extra_date" => $extra_extra_date,
                        ":adult_count" => $extra_adult_count));

                    $extra_id = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_extrasupplement 
                            SET extra_name=:extra_name,mandatory=:mandatory,
                            include_diner_rate_bb=:include_diner_rate_bb,
                            hb_mealplan_fk=:hb_mealplan_fk,
                            bb_mealplan_fk=:bb_mealplan_fk,
                            extra_date=:extra_date,adult_count=:adult_count
                            WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $extra_id,
                        ":extra_name" => $extra_extra_name,
                        ":mandatory" => $extra_mandatory,
                        ":include_diner_rate_bb" => $extra_include_diner_rate_bb,
                        ":hb_mealplan_fk" => $extra_hb_mealplan_fk,
                        ":bb_mealplan_fk" => $extra_bb_mealplan_fk,
                        ":extra_date" => $extra_extra_date,
                        ":adult_count" => $extra_adult_count));
                }

                $arr_needed_ids[] = $extra_id;

                //insert update child ages
                $outcome = ammendExtraChildAges($extra_id, $arr_extra_children);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_extrasupplement WHERE 
                    service_contract_roomcapacity_dates_fk=:date_rwid 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":date_rwid" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE EXTRA: " . $ex->getMessage();
    }
}

function ammendExtraChildAges($extra_id, $arr_extra_children) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_extra_children); $i++) {


            $age_rwid = $arr_extra_children[$i]["child_rwid"];
            $age_from = $arr_extra_children[$i]["child_agefrom"];
            $age_to = $arr_extra_children[$i]["child_ageto"];
            $child_count = $arr_extra_children[$i]["child_count"];

            $sql = "SELECT * FROM tblservice_contract_extrasupplement_childages WHERE 
                    service_contract_extrasupplement_fk=:service_contract_extrasupplement_fk 
                    AND child_age_from=:child_age_from
                    AND child_age_to=:child_age_to 
                    AND child_count=:child_count";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ":service_contract_extrasupplement_fk" => $extra_id,
                ":child_age_from" => $age_from,
                ":child_age_to" => $age_to,
                ":child_count" => $child_count));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $age_rwid = $rw["id"];
            } else {
                //insert 
                $sql = "INSERT INTO tblservice_contract_extrasupplement_childages
                        (service_contract_extrasupplement_fk,child_age_from,
                        child_age_to,child_count) 
                        VALUES (:service_contract_extrasupplement_fk,:child_age_from,
                        :child_age_to,:child_count)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":service_contract_extrasupplement_fk" => $extra_id,
                    ":child_age_from" => $age_from,
                    ":child_age_to" => $age_to,
                    ":child_count" => $child_count));

                $age_rwid = $con->lastInsertId();
            }

            $arr_needed_ids[] = $age_rwid;
        }

        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_extrasupplement_childages WHERE 
                    service_contract_extrasupplement_fk=:service_contract_extrasupplement_fk 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":service_contract_extrasupplement_fk" => $extra_id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE EXTRA CHILDAGES: " . $ex->getMessage();
    }
}

function saveTaxCommi() {
    try {

        global $arrtaxcomm;

        for ($i = 0; $i < count($arrtaxcomm); $i++) {
            $room_id = $arrtaxcomm[$i]["room_id"];
            $room_action = $arrtaxcomm[$i]["room_action"];
            $arr_buying_settings = $arrtaxcomm[$i]["buying_settings"];
            $arr_selling_settings = $arrtaxcomm[$i]["selling_settings"];
            $room_hasexception = $arrtaxcomm[$i]["room_hasexception"];

            $arr_needed_ids = array();


            if ($room_action != "DELETE" && $room_hasexception == "YES") {

                //==============================================================
                //process the buying settings
                $outcome = saveTaxCommiBuySellValues($room_id, $arr_buying_settings, "BUYING");
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                //process the selling settings
                $outcome = saveTaxCommiBuySellValues($room_id, $arr_selling_settings, "SELLING");
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                //==============================================================
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TAXCOMMI: " . $ex->getMessage();
    }
}

function saveTaxCommiBuySellValues($room_id, $arr_settings, $buying_selling) {
    try {
        global $con;
        global $id;

        $room_cond = " AND room_exception_id = $room_id ";

        if ($room_id == "GENERAL") {
            $room_id = null;
            $room_cond = " AND room_exception_id IS NULL ";
        }


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


                $sql = "SELECT * FROM tblservice_contract_taxcomm WHERE 
                        service_contract_fk=:service_contract_fk AND 
                        buying_selling=:buying_selling  
                        $room_cond
                        AND row_index=:row_index";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":service_contract_fk" => $id,
                    ":buying_selling" => $setting_buying_selling,
                    ":row_index" => $setting_row_index));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $setting_rwid = $rw["id"];

                    $sql = "UPDATE tblservice_contract_taxcomm SET 
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
                    $sql = "INSERT INTO tblservice_contract_taxcomm 
                            (service_contract_fk,buying_selling,row_index,
                            item_fk,basis,applyon_formula,rounding,room_exception_id)
                            VALUES
                            (:service_contract_fk,:buying_selling,:row_index,
                            :item_fk,:basis,:applyon_formula,:rounding,
                            :room_exception_id)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_fk" => $id,
                        ":buying_selling" => $setting_buying_selling,
                        ":row_index" => $setting_row_index,
                        ":item_fk" => $setting_item_fk,
                        ":basis" => $setting_basis,
                        ":applyon_formula" => $setting_applyon_formula,
                        ":rounding" => $setting_rounding,
                        ":room_exception_id" => $room_id));
                    $setting_rwid = $con->lastInsertId();
                }

                $arr_needed_ids[] = $setting_rwid;

                $outcome = saveTaxCommiValues($setting_rwid, $arr_values);

                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }


        //clean up
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_taxcomm 
                    WHERE service_contract_fk=:id 
                    $room_cond AND buying_selling=:buying_selling
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id, ":buying_selling" => $buying_selling));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TAXCOMMI $buying_selling: " . $ex->getMessage();
    }
}

function saveTaxCommiValues($setting_rwid, $arr_values) {

    try {
        global $con;
        global $id;


        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_values); $i++) {
            $value_rwid = $arr_values[$i]["value_rwid"];
            $value_currency_fk = $arr_values[$i]["value_currency_fk"];
            $value_value = $arr_values[$i]["value_value"];
            $value_action = $arr_values[$i]["value_action"];
            
            if ($value_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_taxcomm_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                
                $sql = "SELECT * FROM tblservice_contract_taxcomm_values WHERE
                service_contract_taxcomm_fk=:service_contract_taxcomm_fk
                AND currency_fk=:currency_fk";

            $stmt = $con->prepare($sql);
            $stmt->execute(array(
                ":service_contract_taxcomm_fk" => $setting_rwid,
                ":currency_fk" => $value_currency_fk));

            if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $value_rwid = $rw["id"];
                $sql = "UPDATE tblservice_contract_taxcomm_values SET 
                    value=:value WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $value_rwid,
                    ":value" => $value_value));
            } else {
                $sql = "INSERT INTO tblservice_contract_taxcomm_values 
                    (service_contract_taxcomm_fk,currency_fk,value)
                    VALUES
                    (:service_contract_taxcomm_fk,:currency_fk,:value)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":service_contract_taxcomm_fk" => $setting_rwid,
                    ":currency_fk" => $value_currency_fk,
                    ":value" => $value_value));

                $value_rwid = $con->lastInsertId();
            }

            $arr_needed_ids[] = $value_rwid;
            }
        }


        //clean up
        $sql = "DELETE FROM tblservice_contract_taxcomm_values WHERE 
                service_contract_taxcomm_fk=:id ";

        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql .= " AND id NOT IN ($deleteids)";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":id" => $setting_rwid));

        return "OK";
    } catch (Exception $ex) {
        return "SAVE TAXCOMMI VALUES: " . $ex->getMessage();
    }
}

function saveCapacity() {
    try {

        global $con;
        global $id;
        global $arrcapacity;

        $arr_roomids_needed = array();

        for ($i = 0; $i < count($arrcapacity); $i++) {
            $room_rwid = $arrcapacity[$i]["room_rwid"];
            $room_id = $arrcapacity[$i]["room_id"];
            $room_action = $arrcapacity[$i]["room_action"];
            $room_variants = $arrcapacity[$i]["room_variants"];
            $arr_room_dates = $arrcapacity[$i]["room_dates"];

            if ($room_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_roomcapacity 
                        WHERE service_contract_fk=:id AND roomfk=:roomfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $id, ":roomfk" => $room_id));
            } else {
                $arr_roomids_needed[] = $room_id;

                $sql = "SELECT * FROM tblservice_contract_roomcapacity 
                        WHERE service_contract_fk=:id AND roomfk=:roomfk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(
                    ":id" => $id, ":roomfk" => $room_id));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $room_rwid = $rw["id"];
                    $sql = "UPDATE tblservice_contract_roomcapacity 
                            SET variant=:variant
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":variant" => $room_variants,
                        ":id" => $room_rwid));
                } else {
                    $sql = "INSERT INTO tblservice_contract_roomcapacity 
                            (service_contract_fk,roomfk,variant)
                            VALUES
                            (:service_contract_fk,:roomfk,:variant)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_fk" => $id,
                        ":roomfk" => $room_id,
                        ":variant" => $room_variants));

                    $room_rwid = $con->lastInsertId();
                }

                $outcome = saveRoomCapacityDates($room_rwid, $arr_room_dates);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }

        //clean up non needed roomids
        $deleteids = implode(",", $arr_roomids_needed);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_roomcapacity 
                    WHERE service_contract_fk=:id AND roomfk NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $id));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CAPACITY: " . $ex->getMessage();
    }
}

function saveRoomCapacityDates($room_rwid, $arr_room_dates) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_room_dates); $i++) {
            $date_rwid = $arr_room_dates[$i]["date_rwid"];
            $date_dtfrom = utils_stringBlank($arr_room_dates[$i]["date_dtfrom"], "");
            $date_dtto = utils_stringBlank($arr_room_dates[$i]["date_dtto"], "");
            $date_season_id = utils_stringBlank($arr_room_dates[$i]["date_season_id"], null);
            
            $date_action = $arr_room_dates[$i]["date_action"];

            $arr_minstay = $arr_room_dates[$i]["date_minstay_rules"];
            $arr_meal_supp = $arr_room_dates[$i]["date_mealsupplement_rules"];
            $arr_meal_extra = $arr_room_dates[$i]["date_mealextrasupplement_rules"];
            $arr_policies_checkinout = $arr_room_dates[$i]["date_policies_checkinout"];
            $arr_policies_cancellation = $arr_room_dates[$i]["date_policies_cancellation"];

            $arr_capacity_rules = $arr_room_dates[$i]["date_capacity_rules"];
            $arr_adultpolicies_rules = $arr_room_dates[$i]["date_adultpolicies_rules"];
            $arr_childpolicies_rules = $arr_room_dates[$i]["date_childpolicies_rules"];
            $arr_singleparentpolicies_rules = $arr_room_dates[$i]["date_singleparentpolicies_rules"];


            if ($date_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_roomcapacity_dates 
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

                $sql = "SELECT * FROM tblservice_contract_roomcapacity_dates 
                        WHERE
                        service_contract_roomcapacity_fk=:service_contract_roomcapacity_fk
                        $cond_dtfrom
                        $cond_dtto";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":service_contract_roomcapacity_fk" => $room_rwid));

                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $arr_needed_ids[] = $rw["id"];
                    $date_rwid = $rw["id"];
                    
                    $sql = "UPDATE tblservice_contract_roomcapacity_dates 
                            SET season_fk=:season_fk WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":season_fk"=>$date_season_id,
                        ":id" => $room_rwid));
                    
                } else {

                    if ($date_dtfrom == "") {
                        $date_dtfrom = null;
                    }
                    if ($date_dtto == "") {
                        $date_dtto = null;
                    }

                    $sql = "INSERT INTO tblservice_contract_roomcapacity_dates 
                            (override_dtfrom,override_dtto,season_fk,
                             service_contract_roomcapacity_fk)
                            VALUES
                            (:override_dtfrom,:override_dtto,:season_fk,
                             :service_contract_roomcapacity_fk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":override_dtfrom" => $date_dtfrom,
                        ":override_dtto" => $date_dtto,":season_fk"=>$date_season_id,
                        ":service_contract_roomcapacity_fk" => $room_rwid));

                    $date_rwid = $con->lastInsertId();
                    $arr_needed_ids[] = $date_rwid;
                }


                //================================
                $outcome = saveRmMinStay($date_rwid, $arr_minstay);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmMealSupp($date_rwid, $arr_meal_supp);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmMealExtra($date_rwid, $arr_meal_extra);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmPoliciesCheckInOut($date_rwid, $arr_policies_checkinout);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmPoliciesCancellation($date_rwid, $arr_policies_cancellation);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $outcome = saveRmCapDtRules($date_rwid, $arr_capacity_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmAdultPolicyDtRules($date_rwid, $arr_adultpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $outcome = saveRmChildPolicyDtRules($date_rwid, $arr_childpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $outcome = saveRmSngPrntPolicyDtRules($date_rwid, $arr_singleparentpolicies_rules);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                //================================
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_roomcapacity_dates WHERE 
                    service_contract_roomcapacity_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $room_rwid));
        }


        return "OK";
    } catch (Exception $ex) {
        return "SAVE CAPACITY DATES: " . $ex->getMessage();
    }
}

function saveRmCapDtRules($date_rwid, $arr_capacity_rules) {
    try {
        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_capacity_rules); $i++) {
            $rule_rwid = $arr_capacity_rules[$i]["rule_rwid"];
            $rule_action = $arr_capacity_rules[$i]["rule_action"];
            $arr_rule_capacity = $arr_capacity_rules[$i]["rule_capacity"];

            if ($rule_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_roomcapacity_dates_rules WHERE
                        id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO tblservice_contract_roomcapacity_dates_rules
                            (service_contract_roomcapacity_dates_fk,rulecounter)
                            VALUES
                            (:service_contract_roomcapacity_dates_fk,:rulecounter)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i));

                    $rule_rwid = $con->lastInsertId();
                }

                $arr_needed_ids[] = $rule_rwid;

                $outcome = saveRmCapDtRuleAges($rule_rwid, $arr_rule_capacity);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_roomcapacity_dates_rules WHERE 
                    service_contract_roomcapacity_dates_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $date_rwid));
        }


        return "OK";
    } catch (Exception $ex) {
        return "SAVE CAPACITY DATE RULES: " . $ex->getMessage();
    }
}

function saveRmCapDtRuleAges($rule_rwid, $arr_rule_capacity) {
    try {

        global $con;

        $arr_needed_ids = array();

        for ($i = 0; $i < count($arr_rule_capacity); $i++) {

            //return print_r($arr_rule_capacity[$i]);

            $capacity_rwid = $arr_rule_capacity[$i]["capacity_rwid"];
            $capacity_category = $arr_rule_capacity[$i]["capacity_category"];
            $capacity_minpax = $arr_rule_capacity[$i]["capacity_minpax"];
            $capacity_maxpax = $arr_rule_capacity[$i]["capacity_maxpax"];
            $capacity_child_agefrom = $arr_rule_capacity[$i]["capacity_child_agefrom"];
            $capacity_child_ageto = $arr_rule_capacity[$i]["capacity_child_ageto"];
            $capacity_action = $arr_rule_capacity[$i]["capacity_action"];

            if ($capacity_action == "DELETE") {
                $sql = "DELETE FROM tblservice_contract_roomcapacity_dates_rules_ages
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $capacity_rwid));
            } else {

                if ($capacity_minpax == 0 && $capacity_maxpax == 0) {
                    //do nothing
                } else {
                    $sql = "SELECT * FROM tblservice_contract_roomcapacity_dates_rules_ages
                    WHERE 
                    service_contract_roomcapacity_dates_rule_fk=:rulerwid
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
                        $sql = "INSERT INTO tblservice_contract_roomcapacity_dates_rules_ages
                        (service_contract_roomcapacity_dates_rule_fk,
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
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_roomcapacity_dates_rules_ages WHERE 
                    service_contract_roomcapacity_dates_rule_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $rule_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CAPACITY DATE RULE AGES: " . $ex->getMessage();
    }
}

function saveRmAdultPolicyDtRules($date_rwid, $arr_adultpolicies_rules) {
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
                $sql = "DELETE FROM tblservice_contract_adultpolicy_room_dates_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO tblservice_contract_adultpolicy_room_dates_rules
                            (service_contract_roomcapacity_dates_fk,rulecounter,rulecategory)
                            VALUES
                            (:service_contract_roomcapacity_dates_fk,:rulecounter,:rulecategory)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category));

                    $rule_rwid = $con->lastInsertId();
                } else {
                    $sql = "UPDATE tblservice_contract_adultpolicy_room_dates_rules
                            SET rulecounter=:rulecounter, rulecategory=:rulecategory
                            WHERE id=:id";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":id" => $rule_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category));
                }

                $outcome = saveRmAdultPolicyDtRulesAges($rule_rwid, $arr_rule_ages);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $arr_needed_ids[] = $rule_rwid;
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_adultpolicy_room_dates_rules WHERE 
                    service_contract_roomcapacity_dates_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ADULT DATE RULES: " . $ex->getMessage();
    }
}

function saveRmAdultPolicyDtRulesAges($rule_rwid, $arr_rule_ages) {
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
                $sql = "DELETE FROM tblservice_contract_adultpolicy_room_dates_rules_ages
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {
                if ($policy_rwid < 0) {

                    $sql = "INSERT INTO tblservice_contract_adultpolicy_room_dates_rules_ages
                            (service_contract_adultpolicy_room_dates_rules_fk,
                            category,basis,units_additional_child_agefrom,
                            units_additional_child_ageto)
                            VALUES
                            (:service_contract_adultpolicy_room_dates_rules_fk,
                            :category,:basis,:units_additional_child_agefrom,
                            :units_additional_child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_adultpolicy_room_dates_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":units_additional_child_agefrom" => $policy_units_additional_child_agefrom,
                        ":units_additional_child_ageto" => $policy_units_additional_child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_adultpolicy_room_dates_rules_ages SET 
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

                $outcome = saveRmAdultPolicyDtRulesAgesValues($policy_rwid, $arr_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }


                $arr_needed_ids[] = $policy_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_adultpolicy_room_dates_rules_ages WHERE 
                    service_contract_adultpolicy_room_dates_rules_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $rule_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ADULT DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveRmAdultPolicyDtRulesAgesValues($policy_rwid, $arr_values) {
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
                $sql = "DELETE FROM tblservice_contract_adult_policy_room_dates_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {

                if ($value_rwid < 0) {
                    $sql = "INSERT INTO 
                        tblservice_contract_adult_policy_room_dates_rules_ages_values
                        (service_contract_adult_policy_room_dates_rules_ages_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:service_contract_adult_policy_room_dates_rules_ages_fk,
                         :currencyfk,:basis,:value)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_adult_policy_room_dates_rules_ages_fk" => $policy_rwid,
                        ":currencyfk" => $value_currencyfk,
                        ":basis" => $value_basis,
                        ":value" => $value_value));

                    $value_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_adult_policy_room_dates_rules_ages_values 
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
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_adult_policy_room_dates_rules_ages_values
                    WHERE 
                    service_contract_adult_policy_room_dates_rules_ages_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $policy_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE ADULT DATE RULE AGES VALUES: " . $ex->getMessage();
    }
}

function saveRmChildPolicyDtRules($date_rwid, $arr_childpolicies_rules) {
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
                $sql = "DELETE FROM tblservice_contract_childpolicy_room_dates_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {

                    $sql = "INSERT INTO tblservice_contract_childpolicy_room_dates_rules
                            (service_contract_roomcapacity_dates_fk,
                            rulecounter,rulecategory,sharing_single)
                            VALUES
                            (:service_contract_roomcapacity_dates_fk,
                            :rulecounter,:rulecategory,:sharing_single)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":sharing_single" => $rule_sharing_single));

                    $rule_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_childpolicy_room_dates_rules SET 
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

                $outcome = saveRmChildPolicyDtRulesAges($rule_rwid, $arr_rule_policy);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $rule_rwid;
            }
        }

        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_childpolicy_room_dates_rules
                    WHERE 
                    service_contract_roomcapacity_dates_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CHILD DATES RULE: " . $ex->getMessage();
    }
}

function saveRmChildPolicyDtRulesAges($rule_rwid, $arr_rule_policy) {
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
                $sql = "DELETE FROM tblservice_contract_childpolicy_room_dates_rules_ages 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {

                if ($policy_rwid < 0) {
                    $sql = "INSERT INTO tblservice_contract_childpolicy_room_dates_rules_ages
                            (service_contract_childpolicy_room_dates_rules_fk,
                            category,basis,child_agefrom,child_ageto)
                            VALUES
                            (:service_contract_childpolicy_room_dates_rules_fk,
                            :category,:basis,:child_agefrom,:child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_childpolicy_room_dates_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":child_agefrom" => $child_agefrom,
                        ":child_ageto" => $child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {
                    $sql = "UPDATE tblservice_contract_childpolicy_room_dates_rules_ages
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

                $outcome = saveRmChildPolicyDtRulesAgesValues($policy_rwid, $arr_policy_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $policy_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_childpolicy_room_dates_rules_ages
                    WHERE 
                    service_contract_childpolicy_room_dates_rules_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $rule_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CHILD DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveRmChildPolicyDtRulesAgesValues($policy_rwid, $arr_policy_values) {
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
                $sql = "DELETE FROM tblservice_contract_child_policy_room_dates_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                if ($value_rwid < 0) {
                    $sql = "INSERT INTO 
                        tblservice_contract_child_policy_room_dates_rules_ages_values
                        (service_contract_child_policy_room_dates_rules_ages_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:service_contract_child_policy_room_dates_rules_ages_fk,
                         :currencyfk,:basis,:value)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_child_policy_room_dates_rules_ages_fk" => $policy_rwid,
                        ":currencyfk" => $value_currencyfk,
                        ":basis" => $value_basis,
                        ":value" => $value_value));

                    $value_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_child_policy_room_dates_rules_ages_values 
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
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_child_policy_room_dates_rules_ages_values
                    WHERE 
                    service_contract_child_policy_room_dates_rules_ages_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $policy_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE CHILD DATES RULE AGES VALUES: " . $ex->getMessage();
    }
}

function saveRmSngPrntPolicyDtRules($date_rwid, $arr_singleparentpolicies_rules) {
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
                $sql = "DELETE FROM tblservice_contract_snglprntpolicy_room_dates_rules
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rule_rwid));
            } else {
                if ($rule_rwid < 0) {
                    $sql = "INSERT INTO 
                        tblservice_contract_snglprntpolicy_room_dates_rules
                        (service_contract_roomcapacity_dates_fk,
                         rulecounter,rulecategory,ruleageranges)
                        VALUES
                        (:service_contract_roomcapacity_dates_fk,
                         :rulecounter,:rulecategory,:ruleageranges)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_roomcapacity_dates_fk" => $date_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":ruleageranges" => $rule_ageranges));

                    $rule_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_snglprntpolicy_room_dates_rules 
                            SET rulecounter=:rulecounter, rulecategory=:rulecategory,
                            ruleageranges=:ruleageranges WHERE id=:id";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":id" => $rule_rwid,
                        ":rulecounter" => $i,
                        ":rulecategory" => $rule_category,
                        ":ruleageranges" => $rule_ageranges));
                }

                $outcome = saveRmSngPrntPolicyDtRulesAges($rule_rwid, $arr_rule_policy);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $rule_rwid;
            }
        }


        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_snglprntpolicy_room_dates_rules
                    WHERE 
                    service_contract_roomcapacity_dates_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $date_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE SNGPRNT DATES RULE: " . $ex->getMessage();
    }
}

function saveRmSngPrntPolicyDtRulesAges($rule_rwid, $arr_rule_policy) {
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
                        tblservice_contract_snglprntpolicy_room_dates_rules_ages
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $policy_rwid));
            } else {

                if ($policy_rwid < 0) {
                    $sql = "INSERT INTO 
                            tblservice_contract_snglprntpolicy_room_dates_rules_ages
                            (service_contract_snglprntpolicy_room_dates_rules_fk,
                            category,
                            basis,adult_child,child_agefrom,child_ageto)
                            VALUES
                            (:service_contract_snglprntpolicy_room_dates_rules_fk,
                            :category,
                            :basis,:adult_child,:child_agefrom,:child_ageto)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_snglprntpolicy_room_dates_rules_fk" => $rule_rwid,
                        ":category" => $policy_category,
                        ":basis" => $policy_basis,
                        ":adult_child" => $policy_adult_child,
                        ":child_agefrom" => $policy_child_agefrom,
                        ":child_ageto" => $policy_child_ageto));

                    $policy_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE 
                            tblservice_contract_snglprntpolicy_room_dates_rules_ages
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


                $outcome = saveRmSngPrntPolicyDtRulesAgesValues($policy_rwid, $arr_policy_values);
                if ($outcome != "OK") {
                    throw new Exception($outcome);
                }

                $arr_needed_ids[] = $policy_rwid;
            }
        }



        //clean ups
        $deleteids = implode(",", $arr_needed_ids);
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_snglprntpolicy_room_dates_rules_ages
                    WHERE 
                    service_contract_snglprntpolicy_room_dates_rules_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $rule_rwid));
        }


        return "OK";
    } catch (Exception $ex) {
        return "SAVE SNGPRNT DATES RULE AGES: " . $ex->getMessage();
    }
}

function saveRmSngPrntPolicyDtRulesAgesValues($policy_rwid, $arr_policy_values) {
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
                $sql = "DELETE FROM tblservice_contract_snglprntpolicy_room_dates_rules_ages_values 
                        WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $value_rwid));
            } else {
                if ($value_rwid < 0) {
                    $sql = "INSERT INTO 
                        tblservice_contract_snglprntpolicy_room_dates_rules_ages_values
                        (service_contract_snglprntpolicy_room_dates_rules_ages_fk,
                         currencyfk,basis,value)
                        VALUES
                        (:service_contract_snglprntpolicy_room_dates_rules_ages_fk,
                         :currencyfk,:basis,:value)";

                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(
                        ":service_contract_snglprntpolicy_room_dates_rules_ages_fk" => $policy_rwid,
                        ":currencyfk" => $value_currencyfk,
                        ":basis" => $value_basis,
                        ":value" => $value_value));

                    $value_rwid = $con->lastInsertId();
                } else {

                    $sql = "UPDATE tblservice_contract_snglprntpolicy_room_dates_rules_ages_values 
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
        if ($deleteids != "") {
            $sql = "DELETE FROM tblservice_contract_snglprntpolicy_room_dates_rules_ages_values
                    WHERE 
                    service_contract_snglprntpolicy_room_dates_rules_ages_fk=:id 
                    AND id NOT IN ($deleteids)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":id" => $policy_rwid));
        }

        return "OK";
    } catch (Exception $ex) {
        return "SAVE SNGPRNT DATES RULE AGES VALUES: " . $ex->getMessage();
    }
}

?>
