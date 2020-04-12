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

if (!isset($_GET["hoid"])) {
    die("INVALID HOTEL ID");
}

$hotelfk = $_GET["hoid"];

require_once("../../connector/pdo_connect_main.php");
require_once("../../connector/db_pdo.php");
require_once("../../connector/data_connector.php");

$con = pdo_con();

//to prevent mysql from truncating group_concat values
$sql = "SET SESSION group_concat_max_len=10000;";
$stmt = $con->prepare($sql);
$stmt->execute();


$data = new JSONDataConnector($con, "PDO");

$sql = "SELECT 

c.*, 
r.roomnames, r.rooms_display, r.rooms_ids,
co.countries, co.market_countries_display, co.market_countries_ids, 
tp.tour_operator_names, tp.tour_operators_display, tp.tour_operators_ids,
rc.myrates, rc.selected_rate_codes_display, rc.selected_rate_codes_ids,
ch.agerange, ch.children_ages_display, ch.children_ages_ids,
dep.mydepts, dep.departments_ids, dep.departments_display,
curbuy.mycurrencies_buy,
curbuy.selected_currency_buy_ids,
curbuy.selected_currency_buy_display,
cursell.mycurrencies_sell,
cursell.selected_currency_sell_ids,
cursell.selected_currency_sell_display

FROM

(
        select
	sc.id, 
        sc.contractname, sc.active_internal,
        sc.active_external,sc.non_refundable,
        sc.deleted,
        sc.service_code, sc.areafk, sc.coastfk, sc.hotelfk,
        sc.days_mon, sc.days_tue, sc.days_web, sc.days_thu,
        sc.days_fri, sc.days_sat, sc.days_sun,
        sc.cross_season, sc.tax_code_fk, 
        sc.rollover_basis,
        sc.rollover_value,
        sc.invoice_text,
        DATE_FORMAT(sc.date_received,'%d-%m-%Y') AS date_received,
        DATE_FORMAT(sc.date_created,'%d-%m-%Y') AS date_created,
        DATE_FORMAT(sc.date_modified,'%d-%m-%Y') AS date_modified,
        sc.countryfk,sc.mealplan_fk,mp.meal,
        DATE_FORMAT(sc.active_from,'%d-%m-%Y') AS active_from,
	DATE_FORMAT(sc.active_to,'%d-%m-%Y') AS active_to
	FROM tblservice_contract sc
        LEFT JOIN tblmealplans mp on sc.mealplan_fk = mp.id
	WHERE 
	sc.hotelfk = $hotelfk AND
	sc.service_code = 'ACC' AND 
	sc.deleted = 0
	order by sc.active_from DESC, sc.active_to DESC
) c

-- rooms
left join

(
	select sc.id, 
        group_concat(hr.roomname ORDER BY hr.roomname ASC SEPARATOR '<br>') as roomnames,
        group_concat(hr.roomname ORDER BY hr.roomname ASC SEPARATOR ' , ') as rooms_display,
        group_concat(hr.id ORDER BY hr.roomname ASC SEPARATOR ',') as rooms_ids
	from tblservice_contract sc
	inner join tblservice_contract_rooms scr on sc.id = scr.servicecontractfk
	inner join tblhotel_rooms hr on scr.roomfk = hr.id
	where sc.hotelfk = $hotelfk
	group by sc.id
) r

on c.id = r.id

-- tour operators
left join

(
        select sc.id, 
	group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR '<br>') as tour_operator_names,
	group_concat(tp.toname ORDER BY tp.toname ASC SEPARATOR ' , ') as tour_operators_display,
	group_concat(tp.id ORDER BY tp.toname ASC SEPARATOR ',') as tour_operators_ids
        from tblservice_contract sc
        inner join tblservice_contract_touroperator scto on sc.id = scto.service_contract_fk
        inner join tbltouroperator tp on scto.tofk = tp.id
        where sc.hotelfk = $hotelfk
        group by sc.id
) tp

on c.id = tp.id

-- countries
left join

(
	select sc.id, 
        group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR '<br>') AS countries,
        group_concat(c.country_name ORDER BY c.country_name ASC SEPARATOR ' , ') AS market_countries_display,
        group_concat(c.id ORDER BY c.country_name ASC SEPARATOR ',') AS market_countries_ids
	from tblservice_contract sc
	inner join tblservice_contract_countries so on sc.id = so.service_contract_fk
	inner join tblcountries c on so.countryfk = c.id
	where sc.hotelfk = $hotelfk
	group by sc.id
) co

on c.id = co.id

-- rate codes
left join 
(
	select sc.id, 
    group_concat(rc.ratecodes ORDER BY rc.ratecodes ASC SEPARATOR '<br>') AS myrates,
    group_concat(rc.ratecodes ORDER BY rc.ratecodes ASC SEPARATOR ' , ') AS selected_rate_codes_display,
    group_concat(rc.id ORDER BY rc.ratecodes ASC SEPARATOR ',') AS selected_rate_codes_ids
    
    from tblservice_contract sc
    inner join tblservice_contract_rates scr on sc.id = scr.service_contract_fk
    inner join tblratecodes rc on scr.ratefk = rc.id
    where sc.hotelfk = $hotelfk
    group by sc.id
) rc

on c.id = rc.id

-- children policy

left join

(
	select sc.id, 
    group_concat(CONCAT(cr.agefrom, ' - ', cr.ageto) ORDER BY cr.agefrom ASC SEPARATOR '<br>') as agerange,
    group_concat(CONCAT(cr.agefrom, ' - ', cr.ageto) ORDER BY cr.agefrom ASC SEPARATOR ' , ') as children_ages_display,
    group_concat(cr.id ORDER BY cr.agefrom ASC SEPARATOR ',') as children_ages_ids
    from tblservice_contract sc
    inner join tblservice_contract_childages scc on sc.id = scc.service_contract_fk
    inner join tblchildrenagerange cr on scc.child_age_fk = cr.id
    where sc.hotelfk = $hotelfk
    group by sc.id
)
ch

on c.id = ch.id

-- departments

left join 
(
    select sc.id, 
    group_concat(concat(d.deptcode, ' - ', d.deptname) ORDER BY d.deptcode separator '<BR>') as mydepts,
    group_concat(d.id ORDER BY d.deptcode separator ',') as departments_ids,
    group_concat(concat(d.deptcode, ' - ', d.deptname) ORDER BY d.deptcode separator ' , ') as departments_display
    from tblservice_contract sc 
    inner join tblservice_contract_departments scd on sc.id = scd.service_contract_fk
    inner join tbldepartments d on scd.departmentfk = d.id
    where sc.hotelfk = $hotelfk
    group by sc.id
) dep

on c.id = dep.id

-- currencies buy

left join 

(
    select sc.id,
    group_concat(c.currency_code ORDER BY c.currency_code separator '<BR>') as mycurrencies_buy,
    group_concat(c.id ORDER BY c.currency_code separator ',') as selected_currency_buy_ids,
    group_concat(c.currency_code ORDER BY c.currency_code separator ' , ') as selected_currency_buy_display
    from tblservice_contract sc
    inner join tblservice_contract_currency scc on sc.id = scc.service_contract_fk
    inner join tblcurrency c on scc.currencyfk = c.id
    where sc.hotelfk = $hotelfk and scc.buy_sell = 'BUY'
    group by sc.id
    
) curbuy

on c.id = curbuy.id

-- currencies sell

left join 

(
    select sc.id,
    group_concat(c.currency_code ORDER BY c.currency_code separator '<BR>') as mycurrencies_sell,
    group_concat(c.id ORDER BY c.currency_code separator ',') as selected_currency_sell_ids,
    group_concat(c.currency_code ORDER BY c.currency_code separator ' , ') as selected_currency_sell_display
    from tblservice_contract sc
    inner join tblservice_contract_currency scc on sc.id = scc.service_contract_fk
    inner join tblcurrency c on scc.currencyfk = c.id
    where sc.hotelfk = $hotelfk and scc.buy_sell = 'SELL'
    group by sc.id
    
) cursell

on c.id = cursell.id


order by c.active_from DESC, c.active_to DESC";

$data->render_complex_sql($sql, "id", "contractname,active_internal,non_refundable,
    active_external,deleted,invoice_text,
    service_code,areafk,coastfk,hotelfk,mealplan_fk,meal,
    days_mon,days_tue,days_web,days_thu,days_fri,days_sat,days_sun,
    cross_season,tax_indicator_fk,ratecode,rollover_basis,
    rollover_value,date_received,date_created,date_modified,countryfk,
    active_from,active_to,
    roomnames,countries,myrates,agerange,
    selected_rate_codes_display,selected_rate_codes_ids,
    children_ages_display,children_ages_ids,
    market_countries_display,market_countries_ids,
    rooms_display,rooms_ids,
    mydepts,departments_ids,departments_display,
    mycurrencies_buy,selected_currency_buy_ids,selected_currency_buy_display,
    mycurrencies_sell,selected_currency_sell_ids,selected_currency_sell_display,
    tour_operator_names,tour_operators_display,tour_operators_ids");
?>


    

