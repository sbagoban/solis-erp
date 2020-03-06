<?php

function _spo_attach_touroperator($con, $countryid, $touroperator_id = "") {

    //if $touroperator_id blank then 
    //get all touroperators belonging to that $countryid
    //attach each one of them to the SPOs where $countryid is an element of SPOs.countries
    //else
    //attach the specified touroperator to the SPOs where $countryid is an element of SPOs.countries
    
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

        //now get all spo ids where $countryid is an element 
        //of spo.countries

        $arr_spoids = array();
        $sql = "SELECT DISTINCT soc.spo_fk
                FROM tblspecial_offer_countries soc
                INNER JOIN tblspecial_offer so ON soc.spo_fk = so.id
                WHERE soc.country_fk = :country_fk AND so.deleted = 0";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":country_fk" => $countryid));
        while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr_spoids[] = $rw["spo_fk"];
        }

        //now bind all together in tblspecial_offer_touroperator

        for ($i = 0; $i < count($arr_spoids); $i++) {
            for ($j = 0; $j < count($arr_toids); $j++) {
                $spoid = $arr_spoids[$i];
                $toid = $arr_toids[$j];

                $sql = "SELECT * FROM tblspecial_offer_touroperator WHERE 
                        spofk=:spofk AND tofk=:tofk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":spofk" => $spoid,
                    ":tofk" => $toid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert record
                    $sql = "INSERT INTO tblspecial_offer_touroperator
                            (spofk,tofk)
                            VALUES 
                            (:spofk,:tofk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":spofk" => $spoid,
                        ":tofk" => $toid));
                }
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

function _spo_detach_touroperator($con, $countryid, $touroperator_id = "") {

    //if $touroperator_id blank then 
    //remove all touroperators from SPOs where $countryid is an element of SPOs.countries
    //else
    //remove only the touroperator from SPOs where $countryid is an element of SPOs.countries
    
    try {

        $sql = "DELETE FROM tblspecial_offer_touroperator 
                WHERE spofk IN 
                ( SELECT spo_fk 
                  FROM tblspecial_offer_countries 
                  WHERE country_fk=:country_fk
                )";

        if ($touroperator_id != "") {
            $sql .= " AND tofk=$touroperator_id";
        }

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":country_fk" => $countryid));

        return "OK";
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

?>
