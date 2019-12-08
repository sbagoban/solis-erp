<?php

session_start();

if (!isset($_SESSION["solis_userid"])) {
    die("NO LOG IN!");
}

if (!isset($_GET["t"])) {
    die("INVALID TOKEN");
}
if ($_GET["t"] != $_SESSION["token"]) {
    die("INVALID TOKEN");
}

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

$sql = "
        SELECT A.*, C.* FROM
        
        (
            select t.active, t.phy_address, t.api_active, t.api_token,t.tocode,
            t.companytypefk, t.phy_countryfk, t.description_private,
            t.description_public, t.id,  t.ratecode, t.specialratecode,
            t.toname, t.transferratecode,t.phy_address2,t.phy_city,t.phy_postcode,
            ct.comptype,
            t.mail_address,t.mail_address2,
            t.mail_city,t.mail_postcode,t.mail_countryfk,
            t.taxindicatorfk,t.commission,t.markup,t.iata_code,t.id_vat,
            group_concat(tc.companyfk SEPARATOR ',') AS selected_companies_ids,
            group_concat(com.companyname SEPARATOR '<BR>') AS selected_companies
            from tbltouroperator t
            left join tblcompanytypes ct on t.companytypefk = ct.id
            left join tbltouroperator_company tc on t.id = tc.tofk
            left join tblcompanies com on tc.companyfk = com.id
            WHERE t.deleted = 0
            group by 
            t.active, t.phy_address, t.api_active, t.api_token,
            t.companytypefk, t.phy_countryfk, t.description_private,
            t.description_public, t.id, t.ratecode, t.specialratecode,
            t.toname, t.transferratecode,
            ct.comptype, t.tocode,t.phy_address2,
            t.phy_city,t.phy_postcode,
            t.mail_address,t.mail_address2,
            t.mail_city,t.mail_postcode,t.mail_countryfk, t.taxindicatorfk,
            t.commission, t.markup, t.iata_code, t.id_vat
            order by t.tocode
        ) A
        left join
        (
                select toc.tofk, 
                group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR '<br>') AS countries,
                group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR ' , ') AS market_countries_display,
                group_concat(c.id ORDER BY c.country_name ASC SEPARATOR ',') AS market_countries_ids
                from tbltouroperator top
                inner join tblto_countries toc on top.id = toc.tofk
                inner join tblcountries c on toc.countryfk = c.id
                group by toc.tofk
        ) C

on A.id = C.tofk";

$data = new JSONDataConnector($con, "PDO");

$data->render_complex_sql($sql, "id", "toname,
    phy_countryfk,
    companytypefk,
    ratecode,
    specialratecode,
    transferratecode,
    active,
    api_token,
    api_active,
    email,
    phone,
    phy_address,
    description_private,
    description_public,comptype,
    selected_companies_ids,selected_companies,
    tocode,phy_address2,phy_city,phy_postcode,
    mail_address,mail_address2,
    mail_city,mail_postcode,mail_countryfk,
    taxindicatorfk,commission,markup,iata_code,countries,market_countries_display,
    market_countries_ids,id_vat");
?>



    