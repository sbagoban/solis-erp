<?php

function _inventory_attach_touroperator($con, $countryid, $touroperator_id = "") {

    //if $touroperator_id blank then 
    //  get all touroperators belonging to that $countryid
    //  attach each one of them to the Inventory where $countryid 
    //  is an element of Inventory.countries
    //else
    //  attach the specified touroperator to the Inventory 
    //  where $countryid is an element of Inventory.countries
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

        //now get all inventory ids where $countryid is an element 
        //of inventory.countries

        $arr_inventoryids = array();
        $sql = "SELECT DISTINCT soc.inventoryfk
                FROM tblinventory_countries soc
                INNER JOIN tblinventory so ON soc.inventoryfk = so.id
                WHERE soc.countryfk = :countryfk AND so.deleted = 0";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":countryfk" => $countryid));
        while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr_inventoryids[] = $rw["inventoryfk"];
        }

        //now bind all together in tblinventory_touroperators

        for ($i = 0; $i < count($arr_inventoryids); $i++) {
            for ($j = 0; $j < count($arr_toids); $j++) {
                $inventoryid = $arr_inventoryids[$i];
                $toid = $arr_toids[$j];

                $sql = "SELECT * FROM tblinventory_touroperators WHERE 
                        inventoryfk=:inventoryfk AND tofk=:tofk";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":inventoryfk" => $inventoryid,
                    ":tofk" => $toid));
                if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //insert record
                    $sql = "INSERT INTO tblinventory_touroperators
                            (inventoryfk,tofk)
                            VALUES 
                            (:inventoryfk,:tofk)";
                    $stmt = $con->prepare($sql);
                    $stmt->execute(array(":inventoryfk" => $inventoryid,
                        ":tofk" => $toid));
                }
            }
        }

        return "OK";
    } catch (Exception $ex) {
        return $ex->getMessage();
    }
}

function _inventory_detach_touroperator($con, $countryid, $touroperator_id = "") {
    //if $touroperator_id blank then 
    //  remove all touroperators from Inventory where $countryid is 
    //  an element of Inventory.countries
    //else
    //  remove only the touroperator from Inventory where 
    //  $countryid is an element of Inventory.countries
    //end if

    try {

        $sql = "DELETE FROM tblinventory_touroperators 
                WHERE inventoryfk IN 
                ( SELECT inventoryfk 
                  FROM tblinventory_countries 
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
