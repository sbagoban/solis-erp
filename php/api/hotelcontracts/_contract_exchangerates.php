<?php

function _contract_exchangerates($con, $contractid) {

    $arr_rates = array(
        "exchange_rates" => _getContractExg($con, $contractid),
        "currency_mapping" => _getContractCurrMap($con, $contractid));

    return $arr_rates;
}

function _getContractExg($con, $contractid) {
    $arr_rates = array();

    $sql = "SELECT * FROM tblservice_contract_currency_exchangerates "
            . "WHERE service_contract_fk=:contractid "
            . "ORDER BY from_currencyfk, to_currencyfk";
    $stmt_rates = $con->prepare($sql);
    $stmt_rates->execute(array(":contractid" => $contractid));
    while ($rwrates = $stmt_rates->fetch(PDO::FETCH_ASSOC)) {

        $rates_from_currencyfk = $rwrates["from_currencyfk"];
        $rates_to_currencyfk = $rwrates["to_currencyfk"];
        $rates_id = $rwrates["id"];
        $rates_exchange_rate = $rwrates["exchange_rate"];


        $arr_rates[] = array("rates_from_currencyfk" => $rates_from_currencyfk,
            "rates_to_currencyfk" => $rates_to_currencyfk,
            "rates_id" => $rates_id,
            "rates_exchange_rate" => $rates_exchange_rate,
            "rates_action" => "");
    }

    return $arr_rates;
}

function _getContractCurrMap($con, $contractid) {
    $arr_rates = array();

    $sql = "SELECT * FROM tblservice_contract_currency_mapping "
            . "WHERE service_contract_fk=:contractid "
            . "ORDER BY currencybuy_fk, currencysell_fk";
    $stmt_rates = $con->prepare($sql);
    $stmt_rates->execute(array(":contractid" => $contractid));
    while ($rwrates = $stmt_rates->fetch(PDO::FETCH_ASSOC)) {

        $mapping_buy_currencyfk = $rwrates["currencybuy_fk"];
        $mapping_sell_currencyfk = $rwrates["currencysell_fk"];
        $rates_id = $rwrates["id"];
        
        $arr_rates[] = array(
            "mapping_id" => $rates_id,
            "mapping_sell_currencyfk" => $mapping_sell_currencyfk,
            "mapping_buy_currencyfk" => $mapping_buy_currencyfk,
            "mapping_action" => "");
    }

    return $arr_rates;
}

?>
