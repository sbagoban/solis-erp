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
    require_once("../../api/hotelcontracts/_contract_attach_detach_touroperator.php");
    require_once("../../api/hotelspecialoffers/_spo_attach_detach_touroperator.php");
    require_once("../../api/hotelinventory/_inventory_attach_detach_touroperator.php");

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

    $arrdata_details = json_decode($_POST["data_details"], true);
    $arrdata_notes = json_decode($_POST["data_notes"], true);
    $arrdata_api = json_decode($_POST["data_api"], true);
    $arrdata_market = json_decode($_POST["data_market"], true);
    
    $oldcountryid = $_POST["oldcountryid"];
    

    $id = $arrdata_details["id"];

    $active = trim($arrdata_details["active"]);
    //$tocode = trim($arrdata_details["tocode"]);
    $toname = trim($arrdata_details["toname"]);
    $companytypefk = trim($arrdata_details["companytypefk"]);


    $phy_address = trim($arrdata_details["phy_address"]);
    $phy_address2 = trim($arrdata_details["phy_address2"]);
    $phy_city = trim($arrdata_details["phy_city"]);
    $phy_postcode = trim($arrdata_details["phy_postcode"]);
    $phy_countryfk = trim($arrdata_details["phy_countryfk"]);

    $mail_address = trim($arrdata_details["mail_address"]);
    $mail_address2 = trim($arrdata_details["mail_address2"]);
    $mail_city = trim($arrdata_details["mail_city"]);
    $mail_postcode = trim($arrdata_details["mail_postcode"]);
    $mail_countryfk = trim($arrdata_details["mail_countryfk"]);

    $taxindicatorfk = trim($arrdata_details["taxindicatorfk"]);
    $id_vat = trim($arrdata_details["id_vat"]);
    $commission = trim($arrdata_details["commission"]);
    $markup = trim($arrdata_details["markup"]);
    $iata_code = trim($arrdata_details["iata_code"]);

    $ratecode = trim($arrdata_details["ratecode"]);
    $specialratecode = trim($arrdata_details["specialratecode"]);
    $transferratecode = trim($arrdata_details["transferratecode"]);
    
    
    $newcountryid = $arrdata_market["market_countries_ids"];
    
    $api_token = trim($arrdata_api["api_token"]);
    $api_active = trim($arrdata_api["api_active"]);

    $description_private = trim($arrdata_notes["description_private"]);
    $description_public = trim($arrdata_notes["description_public"]);

    $companyids = $_POST["companyids"];

    $arr_currencies = json_decode($_POST["json_currencies"], true);

    $arr_contacts = json_decode($_POST["json_contacts"], true);


    
    /*
    //check duplicates for TO Code
    $sql = "SELECT * FROM tbltouroperator WHERE tocode = :tocode AND id <> :id ";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":tocode" => $tocode, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE OPERATOR CODE!");
    }
    */

    //check duplicates for TO Name
    $sql = "SELECT * FROM tbltouroperator WHERE toname = :toname AND id <> :id AND deleted=0";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":toname" => $toname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE OPERATOR NAME!");
    }

    //check api token
    if ($api_token != "") {
        $sql = "SELECT * FROM tbltouroperator WHERE deleted=0 AND api_token = :api_token AND id <> :id ";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":api_token" => $api_token, ":id" => $id));
        if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("DUPLICATE API TOKEN!");
        }
    }


    if ($id == "-1") {

        //============ DETAILS, API ===================================
        //
        //generate unique token
        $token = utils_generate_unique_token(40, $con, "tbltouroperator", "api_token");

        $sql = "INSERT INTO tbltouroperator 
                (toname,phy_countryfk,companytypefk,
                ratecode,specialratecode,transferratecode,active,api_token,api_active,
                phy_address,phy_address2,phy_city,phy_postcode,
                mail_address,mail_address2,mail_city,mail_postcode,
                mail_countryfk,taxindicatorfk,commission,markup,
                iata_code,id_vat) 
                
                VALUES (:toname,:phy_countryfk,:companytypefk,
                :ratecode,:specialratecode,:transferratecode,:active,:api_token,
                :api_active,
                :phy_address,:phy_address2,:phy_city,:phy_postcode,
                :mail_address,:mail_address2,:mail_city,:mail_postcode,
                :mail_countryfk,:taxindicatorfk,:commission,:markup,
                :iata_code,:id_vat) ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":toname" => $toname,
            ":phy_countryfk" => $phy_countryfk,
            ":companytypefk" => $companytypefk,
            ":ratecode" => $ratecode,
            ":specialratecode" => $specialratecode,
            ":transferratecode" => $transferratecode,
            ":active" => 1,
            ":api_token" => $token,
            ":api_active" => 0,
            ":phy_address" => $phy_address,
            ":phy_address2" => $phy_address2,
            ":phy_city" => $phy_city,
            ":phy_postcode" => $phy_postcode,
            ":mail_address" => $mail_address,
            ":mail_address2" => $mail_address2,
            ":mail_city" => $mail_city,
            ":mail_postcode" => $mail_postcode,
            ":mail_countryfk" => $mail_countryfk,
            ":taxindicatorfk" => $taxindicatorfk,
            ":commission" => $commission,
            ":markup" => $markup,
            ":iata_code" => $iata_code,
            ":id_vat" => $id_vat));

        $id = $con->lastInsertId();
    } else {

        //============ DETAILS, API, NOTES ===================================

        $sql = "UPDATE tbltouroperator SET                
                toname = :toname ,
                phy_countryfk = :phy_countryfk ,
                companytypefk = :companytypefk,
                ratecode = :ratecode,
                specialratecode = :specialratecode,
                transferratecode = :transferratecode,
                active = :active,
                api_token = :api_token,
                api_active = :api_active,
                phy_address = :phy_address,
                description_private = :description_private,
                description_public = :description_public,
                phy_address2 = :phy_address2,
                phy_city = :phy_city,
                phy_postcode = :phy_postcode,
                mail_address = :mail_address,
                mail_address2 = :mail_address2,
                mail_city = :mail_city,
                mail_postcode = :mail_postcode,
                mail_countryfk = :mail_countryfk,
                taxindicatorfk = :taxindicatorfk,
                commission = :commission,
                markup = :markup,
                iata_code = :iata_code,
                id_vat=:id_vat
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":toname" => $toname,
            ":phy_countryfk" => $phy_countryfk,
            ":companytypefk" => $companytypefk,
            ":ratecode" => $ratecode,
            ":specialratecode" => $specialratecode,
            ":transferratecode" => $transferratecode,
            ":active" => $active,
            ":api_token" => $api_token,
            ":api_active" => $api_active,
            ":phy_address" => $phy_address,
            ":phy_address2" => $phy_address2,
            ":phy_city" => $phy_city,
            ":phy_postcode" => $phy_postcode,
            ":mail_address" => $mail_address,
            ":mail_address2" => $mail_address2,
            ":mail_city" => $mail_city,
            ":mail_postcode" => $mail_postcode,
            ":mail_countryfk" => $mail_countryfk,
            ":taxindicatorfk" => $taxindicatorfk,
            ":commission" => $commission,
            ":markup" => $markup,
            ":iata_code" => $iata_code,
            ":description_private" => $description_private,
            ":description_public" => $description_public,
            ":id_vat" => $id_vat,
            ":id"=>$id));


        //============ COMPANIES ===================================
        //attach TO to company(ies)
        $sql = "DELETE FROM tbltouroperator_company WHERE tofk=:tofk AND companyfk NOT IN ($companyids)";
        $stmt = $con->prepare($sql);
        $stmt->execute(array(":tofk" => $id));

        $arr_ids = explode(",", $companyids);
        for ($i = 0; $i < count($arr_ids); $i++) {
            $companyid = $arr_ids[$i];

            //check if link already exits. If not then create one.
            $sql = "SELECT * FROM tbltouroperator_company WHERE tofk=:tofk AND companyfk=:companyfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":tofk" => $id, ":companyfk" => $companyid));
            if (!$rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //insert
                $sql = "INSERT INTO tbltouroperator_company (tofk,companyfk) VALUES (:tofk,:companyfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":tofk" => $id, ":companyfk" => $companyid));
            }
        }
        
        //============= COUNTRIES ===================================
        if($oldcountryid != $newcountryid)
        {
            $sql = "DELETE FROM tblto_countries WHERE tofk=:tofk AND countryfk=:countryfk";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":tofk" => $id, ":countryfk"=>$oldcountryid));
            
            $sql = "INSERT INTO tblto_countries (tofk,countryfk) VALUES (:tofk,:countryfk)";
            $stmt = $con->prepare($sql);
            $stmt->execute(array(":tofk" => $id, ":countryfk"=>$newcountryid));
            
            //========= detach =================
            $outcome = _contract_detach_touroperator($con,$oldcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("DETACH FROM CONTRACT: $outcome");
            }
            
            $outcome = _spo_detach_touroperator($con,$oldcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("DETACH FROM SPO: $outcome");
            }
            
            /*
            $outcome = _inventory_detach_touroperator($con,$oldcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("DETACH FROM INVENTORY: $outcome");
            }
             */
            
            //========= attach =================
            
            $outcome = _contract_attach_touroperator($con,$newcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("ATTACH TO CONTRACT: $outcome");
            }
            
            $outcome = _spo_attach_touroperator($con,$newcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("ATTACH TO SPO: $outcome");
            }
            
            /*
            $outcome = _inventory_attach_touroperator($con,$newcountryid,$id);
            if($outcome != "OK")
            {
                throw new Exception("ATTACH TO INVENTORY: $outcome");
            }
             * 
             */
            
        }

        //============ CURRENCIES ===================================
        for ($i = 0; $i < count($arr_currencies); $i++) {
            $rwid = $arr_currencies[$i]["rwid"];
            $currencyid = $arr_currencies[$i]["cells"]["currencyid"];
            $use_default = $arr_currencies[$i]["cells"]["use_default"];
            $tax_code = $arr_currencies[$i]["cells"]["tax_code"];
            $terms_value = $arr_currencies[$i]["cells"]["terms_value"];
            $terms_code = $arr_currencies[$i]["cells"]["terms_code"];
            $credit_limit = $arr_currencies[$i]["cells"]["credit_limit"];
            $bankfk = $arr_currencies[$i]["cells"]["bankfk"];
            $bankaccount = $arr_currencies[$i]["cells"]["bankaccount"];
            $accountname = $arr_currencies[$i]["cells"]["accountname"];
            $action = $arr_currencies[$i]["cells"]["action"];


            if ($bankfk == "") {
                $bankfk = null;
            }

            if ($action == "DELETE") {
                $sql = "DELETE FROM tblto_currency WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else if ($action == "ADD") {
                $sql = "INSERT INTO tblto_currency (currencyid,tax_code,
                        terms_code,terms_value,use_default,credit_limit,
                        bankfk,bankaccount,accountname,tofk) VALUES
                        (:currencyid,:tax_code,
                        :terms_code,:terms_value,:use_default,:credit_limit,
                        :bankfk,:bankaccount,:accountname,:tofk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":currencyid" => $currencyid,
                    ":tax_code" => $tax_code,
                    ":terms_code" => $terms_code,
                    ":terms_value" => $terms_value,
                    ":use_default" => $use_default,
                    ":credit_limit" => $credit_limit,
                    ":bankfk" => $bankfk,
                    ":bankaccount" => $bankaccount,
                    ":accountname" => $accountname,
                    ":tofk" => $id));
            } else {
                $sql = "UPDATE tblto_currency SET currencyid=:currencyid,
                        tax_code=:tax_code,
                        terms_code=:terms_code,
                        terms_value=:terms_value,
                        use_default=:use_default,
                        credit_limit=:credit_limit,
                        bankfk=:bankfk,
                        bankaccount=:bankaccount,
                        accountname=:accountname WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":currencyid" => $currencyid,
                    ":tax_code" => $tax_code,
                    ":terms_code" => $terms_code,
                    ":terms_value" => $terms_value,
                    ":use_default" => $use_default,
                    ":credit_limit" => $credit_limit,
                    ":bankfk" => $bankfk,
                    ":bankaccount" => $bankaccount,
                    ":accountname" => $accountname,
                    ":id" => $rwid));
            }
        }


        //============ CONTACTS ===================================
        for ($i = 0; $i < count($arr_contacts); $i++) {
            $rwid = $arr_contacts[$i]["rwid"];
            $deptfk = $arr_contacts[$i]["cells"]["deptfk"];
            $contact_name = $arr_contacts[$i]["cells"]["contact_name"];
            $contact_phone = $arr_contacts[$i]["cells"]["contact_phone"];
            $contact_mobile = $arr_contacts[$i]["cells"]["contact_mobile"];
            $contact_fax = $arr_contacts[$i]["cells"]["contact_fax"];
            $contact_email = $arr_contacts[$i]["cells"]["contact_email"];
            $contact_webaddress = $arr_contacts[$i]["cells"]["contact_webaddress"];
            $dept_default = $arr_contacts[$i]["cells"]["dept_default"];
            $action = $arr_contacts[$i]["cells"]["action"];

            if ($action == "DELETE") {
                $sql = "DELETE FROM tblto_contacts WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else if ($action == "ADD") {
                $sql = "INSERT INTO tblto_contacts (tofk,deptfk,contact_name,contact_phone,
                        contact_mobile,contact_fax,contact_email,contact_webaddress,dept_default)
                        VALUES (:tofk,:deptfk,:contact_name,:contact_phone,:contact_mobile,
                        :contact_fax,:contact_email,:contact_webaddress,:dept_default)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":tofk" => $id,
                    ":deptfk" => $deptfk,
                    ":contact_name" => $contact_name,
                    ":contact_phone" => $contact_phone,
                    ":contact_mobile" => $contact_mobile,
                    ":contact_fax" => $contact_fax,
                    ":contact_email" => $contact_email,
                    ":contact_webaddress" => $contact_webaddress,
                    ":dept_default" => $dept_default));
            } else {
                $sql = "UPDATE tblto_contacts
                        SET                                                
                        deptfk = :deptfk,
                        contact_name = :contact_name,
                        contact_phone = :contact_phone,
                        contact_mobile = :contact_mobile,
                        contact_fax = :contact_fax,
                        contact_email = :contact_email,
                        contact_webaddress = :contact_webaddress,
                        dept_default = :dept_default
                        WHERE id = :id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":deptfk" => $deptfk,
                    ":contact_name" => $contact_name,
                    ":contact_phone" => $contact_phone,
                    ":contact_mobile" => $contact_mobile,
                    ":contact_fax" => $contact_fax,
                    ":contact_email" => $contact_email,
                    ":contact_webaddress" => $contact_webaddress,
                    ":dept_default" => $dept_default,
                    ":id" => $rwid));
            }
        }
    }


    $con->commit();

    echo json_encode(array("OUTCOME" => "OK", "ID" => $id));
} catch (Exception $ex) {
    $con->rollBack();
    die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
}
?>
