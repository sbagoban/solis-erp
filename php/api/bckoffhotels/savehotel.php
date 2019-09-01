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

    $arrdata_details = json_decode($_POST["data_details"], true);
    $arrdata_notes = json_decode($_POST["data_notes"], true);

    $id = $arrdata_details["id"];

    $active = trim($arrdata_details["active"]);
    $hotelname = trim($arrdata_details["hotelname"]);
    $property_name = trim($arrdata_details["property_name"]);
    $company_name = trim($arrdata_details["company_name"]);
    $hoteltypefk = trim($arrdata_details["hoteltypefk"]);
    $rating = trim($arrdata_details["rating"]);
    
    $groupfk = trim($arrdata_details["groupfk"]);
    if ($groupfk == "-") {
        $groupfk = null;
    }

    $description = trim($arrdata_notes["description"]);

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

    $areafk = trim($arrdata_details["areafk"]);
    $coastfk = trim($arrdata_details["coastfk"]);

    $website = trim($arrdata_details["website"]);

    $lat = trim($arrdata_details["lat"]);
    $lon = trim($arrdata_details["lon"]);
    if($lat == ""){$lat=null;}
    if($lon == ""){$lon=null;}
    
    $ratecode = trim($arrdata_details["ratecode"]);
    $specialratecode = trim($arrdata_details["specialratecode"]);


    $arr_currencies = json_decode($_POST["json_currencies"], true);
    $arr_contacts = json_decode($_POST["json_contacts"], true);
    $arr_commission = json_decode($_POST["json_commission"], true);


    //check duplicates for Hotel Name
    $sql = "SELECT * FROM tblhotels WHERE hotelname = :hotelname AND id <> :id AND deleted=0";
    $stmt = $con->prepare($sql);
    $stmt->execute(array(":hotelname" => $hotelname, ":id" => $id));
    if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception("DUPLICATE HOTEL NAME!");
    }


    if ($id == "-1") {

        //============ DETAILS ===================================
        //
        
        $sql = "INSERT INTO tblhotels 
                (hotelname,
                hoteltypefk,
                groupfk,
                description,
                phy_address,
                phy_address2,
                phy_city,
                phy_postcode,
                phy_countryfk,
                areafk,
                coastfk,
                website,
                ratecode,
                specialratecode,
                lat,
                lon,
                active,
                mail_address,
                mail_address2,
                mail_city,
                mail_postcode,
                mail_countryfk,
                deleted,
                rating,
                property_name,
                company_name)
                VALUES
                (:hotelname,
                :hoteltypefk,
                :groupfk,
                :description,
                :phy_address,
                :phy_address2,
                :phy_city,
                :phy_postcode,
                :phy_countryfk,
                :areafk,
                :coastfk,
                :website,
                :ratecode,
                :specialratecode,
                :lat,
                :lon,
                :active,
                :mail_address,
                :mail_address2,
                :mail_city,
                :mail_postcode,
                :mail_countryfk,
                0,
                :rating,
                :property_name,
                :company_name);";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotelname" => $hotelname,
            ":hoteltypefk" => $hoteltypefk,
            ":groupfk" => $groupfk,
            ":description" => $description,
            ":phy_address" => $phy_address,
            ":phy_address2" => $phy_address2,
            ":phy_city" => $phy_city,
            ":phy_postcode" => $phy_postcode,
            ":phy_countryfk" => $phy_countryfk,
            ":areafk" => $areafk,
            ":coastfk" => $coastfk,
            ":website" => $website,
            ":ratecode" => $ratecode,
            ":specialratecode" => $specialratecode,
            ":lat" => $lat,
            ":lon" => $lon,
            ":active" => $active,
            ":mail_address" => $mail_address,
            ":mail_address2" => $mail_address2,
            ":mail_city" => $mail_city,
            ":mail_postcode" => $mail_postcode,
            ":mail_countryfk" => $mail_countryfk,
            ":rating"=>$rating,
            ":property_name"=>$property_name,
            ":company_name"=>$company_name));

        $id = $con->lastInsertId();
    } else {

        //============ DETAILS, API, NOTES ===================================

        $sql = "UPDATE tblhotels
                SET
                hotelname = :hotelname,
                hoteltypefk = :hoteltypefk,
                groupfk = :groupfk,
                description = :description,
                phy_address = :phy_address,
                phy_address2 = :phy_address2,
                phy_city = :phy_city,
                phy_postcode = :phy_postcode,
                phy_countryfk = :phy_countryfk,
                areafk = :areafk,
                coastfk = :coastfk,
                website = :website,
                ratecode = :ratecode,
                specialratecode = :specialratecode,
                lat = :lat,
                lon = :lon,
                active = :active,
                mail_address = :mail_address,
                mail_address2 = :mail_address2,
                mail_city = :mail_city,
                mail_postcode = :mail_postcode,
                mail_countryfk = :mail_countryfk,
                rating=:rating,
                property_name=:property_name,
                company_name=:company_name
                WHERE id=:id ";

        $stmt = $con->prepare($sql);
        $stmt->execute(array(":hotelname" => $hotelname,
            ":hoteltypefk" => $hoteltypefk,
            ":groupfk" => $groupfk,
            ":description" => $description,
            ":phy_address" => $phy_address,
            ":phy_address2" => $phy_address2,
            ":phy_city" => $phy_city,
            ":phy_postcode" => $phy_postcode,
            ":phy_countryfk" => $phy_countryfk,
            ":areafk" => $areafk,
            ":coastfk" => $coastfk,
            ":website" => $website,
            ":ratecode" => $ratecode,
            ":specialratecode" => $specialratecode,
            ":lat" => $lat,
            ":lon" => $lon,
            ":active" => $active,
            ":mail_address" => $mail_address,
            ":mail_address2" => $mail_address2,
            ":mail_city" => $mail_city,
            ":mail_postcode" => $mail_postcode,
            ":mail_countryfk" => $mail_countryfk,
            ":rating"=>$rating,
            ":property_name"=>$property_name,
            ":company_name"=>$company_name,
            ":id" => $id));


        
        //============ CURRENCIES ===================================
        for ($i = 0; $i < count($arr_currencies); $i++) {
            $rwid = $arr_currencies[$i]["rwid"];
            $currencyid = $arr_currencies[$i]["cells"]["currencyid"];
            $use_default = $arr_currencies[$i]["cells"]["use_default"];
            $tax_code = $arr_currencies[$i]["cells"]["tax_code"];
            $terms_value = $arr_currencies[$i]["cells"]["terms_value"];
            $terms_code = $arr_currencies[$i]["cells"]["terms_code"];
            $bankfk = $arr_currencies[$i]["cells"]["bankfk"];
            $bankaccount = $arr_currencies[$i]["cells"]["bankaccount"];
            $accountname = $arr_currencies[$i]["cells"]["accountname"];
            $action = $arr_currencies[$i]["cells"]["action"];


            if ($bankfk == "") {
                $bankfk = null;
            }

            if ($action == "DELETE") {
                $sql = "DELETE FROM tblhotel_currency WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else if ($action == "ADD") {
                $sql = "INSERT INTO tblhotel_currency (currencyid,tax_code,
                        terms_code,terms_value,use_default,
                        bankfk,bankaccount,accountname,hotelfk) VALUES
                        (:currencyid,:tax_code,
                        :terms_code,:terms_value,:use_default,
                        :bankfk,:bankaccount,:accountname,:hotelfk)";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":currencyid" => $currencyid,
                    ":tax_code" => $tax_code,
                    ":terms_code" => $terms_code,
                    ":terms_value" => $terms_value,
                    ":use_default" => $use_default,
                    ":bankfk" => $bankfk,
                    ":bankaccount" => $bankaccount,
                    ":accountname" => $accountname,
                    ":hotelfk" => $id));
            } else {
                $sql = "UPDATE tblhotel_currency SET currencyid=:currencyid,
                        tax_code=:tax_code,
                        terms_code=:terms_code,
                        terms_value=:terms_value,
                        use_default=:use_default,
                        bankfk=:bankfk,
                        bankaccount=:bankaccount,
                        accountname=:accountname WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":currencyid" => $currencyid,
                    ":tax_code" => $tax_code,
                    ":terms_code" => $terms_code,
                    ":terms_value" => $terms_value,
                    ":use_default" => $use_default,
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
                $sql = "DELETE FROM tblhotel_contacts WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else if ($action == "ADD") {
                $sql = "INSERT INTO tblhotel_contacts (hotelfk,deptfk,contact_name,contact_phone,
                        contact_mobile,contact_fax,contact_email,contact_webaddress,dept_default)
                        VALUES (:hotelfk,:deptfk,:contact_name,:contact_phone,:contact_mobile,
                        :contact_fax,:contact_email,:contact_webaddress,:dept_default)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":hotelfk" => $id,
                    ":deptfk" => $deptfk,
                    ":contact_name" => $contact_name,
                    ":contact_phone" => $contact_phone,
                    ":contact_mobile" => $contact_mobile,
                    ":contact_fax" => $contact_fax,
                    ":contact_email" => $contact_email,
                    ":contact_webaddress" => $contact_webaddress,
                    ":dept_default" => $dept_default));
            } else {
                $sql = "UPDATE tblhotel_contacts
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
        
        
        //============ COMMISSION ===================================
        for ($i = 0; $i < count($arr_commission); $i++) {
            $rwid = $arr_commission[$i]["rwid"];
            $taxcodefk = $arr_commission[$i]["cells"]["taxcode_fk"];
            $taxamt = $arr_commission[$i]["cells"]["taxamt"];
            $commission = $arr_commission[$i]["cells"]["commission"];
            $markup = $arr_commission[$i]["cells"]["markup"];
            $dtfrom = $arr_commission[$i]["cells"]["dtfrom"];
            $dtto = $arr_commission[$i]["cells"]["dtto"];
            $action = $arr_commission[$i]["cells"]["action"];

            if ($action == "DELETE") {
                $sql = "DELETE FROM tblhotel_commission WHERE id=:id";
                $stmt = $con->prepare($sql);
                $stmt->execute(array(":id" => $rwid));
            } else if ($action == "ADD") {
                $sql = "INSERT INTO tblhotel_commission (hotelfk,taxcode_fk,taxamt,"
                        . "commission,markup,dtfrom,dtto) VALUES "
                        . "(:hotelfk,:taxcodefk,:taxamt,"
                        . ":commission,:markup,:dtfrom,:dtto)";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":hotelfk" => $id,
                    ":taxcodefk" => $taxcodefk,
                    ":taxamt" => $taxamt,
                    ":commission" => $commission,
                    ":markup" => $markup,
                    ":dtfrom" => $dtfrom,
                    ":dtto" => $dtto));
            } else {
                $sql = "UPDATE tblhotel_commission
                        SET                                                
                        taxcode_fk = :taxcodefk,
                        taxamt = :taxamt,
                        commission = :commission,
                        markup = :markup,
                        dtfrom = :dtfrom,
                        dtto = :dtto
                        WHERE id = :id";

                $stmt = $con->prepare($sql);
                $stmt->execute(array(":taxcodefk" => $taxcodefk,
                    ":taxamt" => $taxamt,
                    ":commission" => $commission,
                    ":markup" => $markup,
                    ":dtfrom" => $dtfrom,
                    ":dtto" => $dtto,
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
