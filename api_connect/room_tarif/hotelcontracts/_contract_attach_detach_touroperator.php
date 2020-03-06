<?php

function _contract_attach_touroperator($con, $countryid, $touroperator_id = "") {

    //if $touroperator_id blank then 
    //  get all touroperators belonging to that $countryid
    //  attach each one of them to the contracts where $countryid is an element 
    //  of contract.countries
    //else
    //  attach the specified touroperator to the contracts where 
    //  $countryid is an element of contract.countries
    //end if

    try {

        $arr_toids = array();
        if ($touroperator_id != "") {
            $arr_toids[] = $touroperator_id;
        } else {
            $sql = "SELECT tofk FROM tblto_countries where countryfk = :countryfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":countryfk" => $countryid));
            while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr_toids[] = $rw["tofk"];
            }
        }

        //now get all contract ids with rate B where $countryid is an element 
        //of contract.countries

        $arr_contractids = array();
        $sql = "SELECT DISTINCT scc.service_contract_fk
                FROM tblservice_contract_countries scc
                INNER JOIN tblservice_contract sc ON scc.service_contract_fk = sc.id
                INNER JOIN tblservice_contract_rates scr on sc.id = scr.service_contract_fk
                INNER JOIN tblratecodes rc on scr.ratefk = rc.id
                WHERE scc.countryfk = :countryfk AND sc.deleted = 0 AND rc.ratecodes = 'B'";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":countryfk" => $countryid));
        while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr_contractids[] = $rw["service_contract_fk"];
        }

        //now bind all together in tblservice_contract_touroperator

        for ($i = 0; $i < count($arr_contractids); $i++) {
            for ($j = 0; $j < count($arr_toids); $j++) {
                $contractid = $arr_contractids[$i];
                $toid = $arr_toids[$j];

                $sql = "SELECT * FROM tblservice_contract_touroperator WHERE 
                        service_contract_fk=:service_contract_fk AND tofk=:tofk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":service_contract_fk" => $contractid,
                    ":tofk" => $toid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert record
                    $sql = "INSERT INTO tblservice_contract_touroperator
                            (service_contract_fk,tofk)
                            VALUES 
                            (:service_contract_fk,:tofk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":service_contract_fk" => $contractid,
                        ":tofk" => $toid));
                }
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

function _contract_detach_touroperator($con, $countryid, $touroperator_id = "") {

    //if $touroperator_id blank then 
    //  remove all touroperators from contracts where $countryid is an element of contract.countries
    //else
    //  remove only the touroperator from contracts where $countryid is an element of contract.countries
    //end if

    try {

        $sql = "DELETE FROM tblservice_contract_touroperator 
                WHERE service_contract_fk IN 
                ( SELECT service_contract_fk 
                  FROM tblservice_contract_countries 
                  WHERE countryfk=:countryfk
                )";

        if ($touroperator_id != "") {
            $sql .= " AND tofk=$touroperator_id";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":countryfk" => $countryid));

        return "OK";
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

?>
