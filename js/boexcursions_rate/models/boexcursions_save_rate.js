
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
    loadMarketMultiselect();
    // $("select#multiselectRate22").change(function(){
    //     var selectedMarket = $(this).children("option:selected").val();
    //     //loadCountries(selectedMarket);
    // });
    $("select#multiselectRate25").change(function(){
        var selectedCountry = $(this).children("option:selected").val();
        loadTourOperator(selectedCountry);
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

function loadMarketMultiselect() {
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
                            loadCountries(selectedMarket);
                        });
                    }
                });
            }
        }
    );
}

function loadCountries(selectedMarket) {
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
                $("#multiselectRate25").append('<option value="' + val.id + '">' + val.country_name + '</option>'); 
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
                            selectedCountriesArr.push($(this).val());
                            updateRateCountries(selectedCountriesArr, index);
                        });
                    }
                });
                
                $('#multiselectRate25').multiselect('rebuild');
            }
        }
    );
}

function updateRateCountries(selectedCountriesArr, index) {
    var total = $(selectedCountriesArr).length;
    if (index === total - 1) { 
        $('#updateRateDetails').click(function () {
            saveCountriesMultiselect(selectedCountriesArr);
        });
    }
}

function saveCountriesMultiselect(selectedCountriesArr) { 
    var idBlock = document.getElementById('idBlock').innerHTML;
    const url_save_countries = "php/api/backoffservices_rates/ratescountries_multiselect_save.php?t=" + encodeURIComponent(global_token); 
    var jsonString = JSON.stringify(selectedCountriesArr);
    objSelectedCountries = {
        id: -1,
        idservicesfk: idBlock,
        countriesData: jsonString
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

function loadTourOperator(countryId) {
        const url_to = "php/api/backoffservices_rates/to_rates.php?t=" + encodeURIComponent(global_token) + "&id=" + countryId; 
        $.ajax({
            type: "POST",
            url: url_to,
            data: countryId,
            dataType: "json",
            cache: false,
            success: function(data)
                {
                    $.each(data, function (key, val) {
                        $("#multiselectRate24").append('<option value="' + val.value + '">' + val.text + '</option>');
                    });
                    $("#multiselectRate24").attr('multiple', 'multiple'); 
                    $("#multiselectRate24").multiselect({
                        buttonWidth: '313px',
                        includeSelectAllOption: true,
                        nonSelectedText: 'Select an Option',
                        enableFiltering: true,
                        enableHTML: true,
                        buttonClass: 'btn large btn-primary',
                        enableCaseInsensitiveFiltering: true
                    });
                }, 
                
            error: function (error) 
                {
                    console.log('chk error', error);
                }
            },
        );
    
}

// var oldarr = [];
// var newarr = [];
// function multiselectCountries() {
//     if ($("#multiselectRate25 option:selected").length == 1) {
//         oldarr = [];
//         oldarr.push($("#multiselectRate25 option:selected").val());
//     }
//     else {
//         newarr = [];
//         $("#multiselectRate25 option:selected").each(function(i){
//             newarr.push($(this).val());
//         });
//         newitem = $(newarr).not(oldarr).get();
//         if (newitem.length > 0) {
//             oldarr.push(newitem[0]);
//         }
//         else {
//             oldarr = newarr;
//         }
//     }
//     updateRateCountries(oldarr);
//     console.log('>> 5 old', oldarr);
// }
