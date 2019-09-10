
    function servicedatefunc(serviceStartDate, serviceEndDate) {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        
        var objRateDate = {
            id:-1, //for new items, id is always -1
            idservicesfk: idCostDetails, //please make sure the names match in JS and PHP
            servicedatefrom: serviceStartDate,
            servicedateto: serviceEndDate
        };

        const url_save_service_rate1 = "php/api/backoffservices_rates/saveratesdate.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service_rate1,
            method : "POST",
            data : objRateDate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
        var idBlock = document.getElementById('idBlock').innerHTML;
        insertRateGrid(idBlock);
        
    }

    function selectedClosedDateFunc(closedStartDate, closedEndDate) {
        var idBlock = document.getElementById('idBlock').innerHTML;
        console.log(idBlock, closedStartDate, closedEndDate);
        var objRateClosedDate = {
            id:-1, //for new items, id is always -1
            idservicesfk: idBlock, //please make sure the names match in JS and PHP
            serviceclosedstartdate: closedStartDate,
            serviceclosedenddate: closedEndDate
        };

        const url_save_service_rate2 = "php/api/backoffservices_rates/saveratesclosedate.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service_rate2,
            method : "POST",
            data : objRateClosedDate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
        rateDetailsEditRows(idBlock);
    }

    function rateDetailsEditRows(idBlock) {
        $('#rateDetailsSort').DataTable({       
            "processing" : true,
    
            "ajax" : {
                "url" : "php/api/backoffservices_rates/rateclosedatetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idBlock,
                dataSrc : ''
            },
            "destroy": true,
            "bProcessing": true,
            "bAutoWidth": false,
            "pageLength": 5,
            "responsive": true,
            "bPaginate": true,
            "bLengthChange": false,
            "bInfo": false,
            "bFilter": false,
            "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
            +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
            +"<'row'<'col-sm-12'tr>>"
            +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
    
            "buttons":[
            ],
            "columns" : [
            {
                "data" : "serviceclosedstartdate"
            }, {
                "data" : "serviceclosedenddate"
            }, {
                    "targets": -2,
                    "data": null,
                    "class": 'deleteBtnCol',
                    "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
                }
            ]
        });

        $('#rateDetailsSort tbody').on( 'click', 'i', function () {
            var table = $('#rateDetailsSort').DataTable();
            var data = table.row( $(this).parents('tr')).data();
            deleteRowRateDetailschk(data);
        });
    } 
    

function deleteRowRateDetailschk(data) {
    var idBlock = document.getElementById('idBlock').innerHTML;
    var objDel = {id: data.id};
    const url_delete_rateDetails = "php/api/backoffservices_rates/deleterateclosed.php?t=" + encodeURIComponent(global_token) + "&id=" + data.id;
    $.ajax({
        url: url_delete_rateDetails,
        method: "POST",
        data: objDel,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    rateDetailsEditRows(idBlock);
}

///////////////////////////////////////
/////////// MARKET DROPDOWN ///////////
///////////////////////////////////////
$(document).ready(function () {
    
    $("select#multiselectRate25").change(function(){
        var selectedCountry = $(this).children("option:selected").val();
        // var brands = $('#multiselectRate25 option:selected');
        // $(brands).each(function(index, brand){
        //     var test = $(this).val();
        // });
        // console.log(test);
        // updateRateCountries(testtt);
    });
    $("select#multiselectRate24").change(function(){
        var selectedTourOperator = $(this).children("option:selected").val();
        loadRatesTypeMultiselect(selectedTourOperator);
    });
});

function editRowRate(idBlockRates) {
    loadMarketMultiselect(idBlockRates);
}

// Load Market By Default - On Button Edit click
function loadMarketMultiselect(idBlockRates) {
    const url_market = "php/api/combos/market_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        type: "POST",
        url: url_market,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#multiselectRate22").empty();
                $.each(data, function (key, val) {
                $("#multiselectRate22").append('<option value="' + val.id + '">' + val.market_name + '</option>');
            });                
                $("#multiselectRate22").attr('multiple', 'multiple'); 
                $("#multiselectRate22").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-primary',
                    enableCaseInsensitiveFiltering: true,
                    onChange: function(element, checked) {
                        var brands = $('#multiselectRate22 option:selected');
                        var selected = [];
                        $(brands).each(function(index, brand){
                            selected.push($(this).val());
                            selectedMarket = selected.join();
                            loadCountries(selectedMarket, idBlockRates);
                        });
                    }
                });
            }
        }
    );
}

// Load Countries Depending on market
function loadCountries(selectedMarket, idBlockRates) {
    const url_countries = "php/api/backoffservices_rates/ratescountries_multiselect.php?t=" + encodeURIComponent(global_token) + "&marketfk=" + selectedMarket; 
    $.ajax({
        type: "POST",
        url: url_countries,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#multiselectRate25").empty();
                $.each(data, function (key, val) {
                    $("#multiselectRate25").append('<option value="' + val.countryfk + '">' + val.country_name + '</option>'); 
                });                
                $("#multiselectRate25").attr('multiple', 'multiple'); 
                $("#multiselectRate25").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-primary',
                    enableCaseInsensitiveFiltering: true,
                    onChange: function(element, checked) {
                        var brands = $('#multiselectRate25 option:selected');
                        var selectedCountriesArr = [];
                        $(brands).each(function(index, brand){
                            selectedCountries = $(this).val();
                            // Push to array selectedCountriesArr
                            selectedCountriesArr.push($(this).val());
                            selectedCountriesTo = selectedCountriesArr.join();
                            loadTourOperator(selectedCountriesTo, idBlockRates);
                        });
                        if (checked) {
                            console.log('test', element);
                            saveCountriesMultiselect(selectedCountries, idBlockRates);
                        } else {
                            saveCountriesMultiselect(null, idBlockRates);
                        }
                    }
                });
                
                $('#multiselectRate25').multiselect('rebuild');
            }
        }
    );
}
// Load Tour Operator depending on country ID
function loadTourOperator(selectedCountriesTo, idBlockRates) {
    const url_to = "php/api/backoffservices_rates/to_rates.php?t=" + encodeURIComponent(global_token) + "&phy_countryfk=" + selectedCountriesTo; 
    $.ajax({
        type: "POST",
        url: url_to,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $.each(data, function (key, val) {
                    $("#multiselectRate24").append('<option value="' + val.id + '">' + val.toname + '</option>');
                });
                $("#multiselectRate24").attr('multiple', 'multiple'); 
                $("#multiselectRate24").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-primary',
                    enableCaseInsensitiveFiltering: true,
                    onChange: function(element, checked) {
                        var brands = $('#multiselectRate24 option:selected');
                        $(brands).each(function(index, brand){
                            selectedTo = $(this).val();
                        });
                        if (checked) {
                            console.log('test', element);
                            saveTourOperatorMultiselect(selectedTo, idBlockRates);
                        } else {
                            saveTourOperatorMultiselect(null, idBlockRates);
                        }
                    }
                });
                document.getElementById("errorPanel").style.display = "none";
            }, 
            
        error: function (error) 
            {
                console.log('chk error', error);
                document.getElementById("errorPanel").style.display = "block";
            }
        },
    );

}

// Save Countries
function saveCountriesMultiselect(selectedCountries, idBlockRates) {
    $('#updateRateDetails').click(function () {
        if (!null) {
            var idBlock = document.getElementById('idBlock').innerHTML;
            const url_save_countries = "php/api/backoffservices_rates/ratescountries_multiselect_save.php?t=" + encodeURIComponent(global_token); 
            //var jsonString = JSON.stringify(selectedCountriesArr);
            objSelectedCountries = {
                id: -1,
                idservicesfk: idBlock,
                idrates_fk: idBlockRates.id,                
                country_id: selectedCountries
            };
            $.ajax({
                type: "POST",
                url: url_save_countries,
                data: objSelectedCountries,
                cache: false,
                success: function(data) {
                    console.log("OK", data);
                }
            });
        }
    });
}

// Save TourOperator

function saveTourOperatorMultiselect(selectedTo, idBlockRates) {
    $('#updateRateDetails').click(function () {
        if (!null) {
            var idBlock = document.getElementById('idBlock').innerHTML;
            const url_save_to = "php/api/backoffservices_rates/ratesto_multiselect_save.php?t=" + encodeURIComponent(global_token); 
            //var jsonString = JSON.stringify(selectedCountriesArr);
            url_save_to = {
                id: -1,
                idservicesfk: idBlock,
                idrates_fk: idBlockRates.id,                
                country_id: selectedTo
            };
            $.ajax({
                type: "POST",
                url: url_save_countries,
                data: objSelectedTo,
                cache: false,
                success: function(data) {
                    console.log("OK", data);
                }
            });
        }
    });
}

function loadRatesTypeMultiselect(selectedTourOperator) {
    const url_ratecode = "php/api/backoffservices_rates/ratestype_combo.php?t=" + encodeURIComponent(global_token) + "&toid=" + selectedTourOperator; 
    $.ajax({
        type: "POST",
        url: url_ratecode,
        cache: false,
        dataType: "json",
        success: function(data)
            {
                $("#multiselectRate23").empty();
                $.each(data, function (key, val) {
                    console.log(val);
                $("#multiselectRate23").append('<option value="' + val.value + '">' + val.text + '</option>');
            });
                $("#multiselectRate23").attr('multiple', 'multiple'); 
                $("#multiselectRate23").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-primary',
                    enableCaseInsensitiveFiltering: true
                });
            }
        }
    );
}
