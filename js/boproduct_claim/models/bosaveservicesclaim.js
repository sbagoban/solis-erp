$(document).ready(function () {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    // As TO is first in the list it should be loaded by default
    loadTourOperatorClaim();
    loadCountriesClaim();
    dateRangePickervalid();
    var charges = urlParams.get("charges"); 
    $('#charges').val(charges);
    if (charges == 'UNIT') {
        $("#ps_teen_claim").css("display", "none");        
        $("#ps_child_claim").css("display", "none");
        $("#ps_infant_claim").css("display", "none");
        
        $("#ps_teen_claim_addon").css("display", "none");        
        $("#ps_child_claim_addon").css("display", "none");
        $("#ps_infant_claim_addon").css("display", "none");
    }

    var monday = urlParams.get("on_monday"); 
    var tuesday = urlParams.get("on_tuesday"); 
    var wednesday = urlParams.get("on_wednesday"); 
    var thursday = urlParams.get("on_thursday"); 
    var friday = urlParams.get("on_friday"); 
    var saturday = urlParams.get("on_saturday"); 
    var sunday = urlParams.get("on_sunday"); 

    if (monday == 1){
        $("#ex_monday1").css("display", "block");
    } if (tuesday == 1){
        $("#ex_tuesday1").css("display", "block");
    } if (wednesday == 1){
        $("#ex_wednesday1").css("display", "block");
    } if (thursday == 1){
        $("#ex_thursday1").css("display", "block");
    } if (friday == 1){
        $("#ex_friday1").css("display", "block");
    } if (saturday == 1){
        $("#ex_saturday1").css("display", "block");
    } if (sunday == 1){
        $("#ex_sunday1").css("display", "block");
    }
});

$("#specific_to").change(function () {
    var specificto = this.value;
    
    if (specificto == "B") {
        $("#multiSpecificTo").css("display", "none");
        $("#multiSpecificMarket").css("display", "none");
        
        $('#ddlmultiSpecificMarket').multiselect('destroy');
        $('#ddlMultiSpecificTo').multiselect('destroy');
    }
    if (specificto == "A") {
        $("#multiSpecificMarket").css("display", "none");
        $("#multiSpecificTo").css("display", "block");
        loadTourOperatorClaim();
        $('#ddlMultiSpecificTo').multiselect('destroy');
    }
    if (specificto == "C") {
        
        $('#ddlmultiSpecificMarket').multiselect('destroy');
        $("#multiSpecificTo").css("display", "none");
        $("#multiSpecificMarket").css("display", "block");
        loadCountriesClaim();
    }
});

// Load Tour Operator depending on country ID
function loadTourOperatorClaim() {
    $("#multiSpecificMarket").css("display", "none");
    const url_to = "php/api/backofficeserviceclaim/tocombo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        type: "POST",
        url: url_to,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#ddlMultiSpecificTo").empty();
                $.each(data, function (key, val) {
                $("#ddlMultiSpecificTo").append('<option value="' + val.id + '">' + val.toname + '</option>');
                toname = val.toname;
            });                
                $("#ddlMultiSpecificTo").attr('multiple', 'multiple'); 
                $("#ddlMultiSpecificTo").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-default',
                    enableCaseInsensitiveFiltering: true,
                    onChange: function(element, checked) {
                        var brands = $('#ddlMultiSpecificTo option:selected');
                        var selected = [];
                        $(brands).each(function(index, brand){
                            selected.push($(this).val());
                            selectedTo = selected.join();
                        });
                    }
                });
            }
        }
    );
}

// Load Market By Default - On Button Edit click
function loadCountriesClaim() {
    const url_market = "php/api/backofficeserviceclaim/attachcountriesclaim.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        type: "POST",
        url: url_market,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#ddlmultiSpecificMarket").empty();
                $.each(data, function (key, val) {
                $("#ddlmultiSpecificMarket").append('<option value="' + val.id + '"  data-subtext="' + val.marketfk + '">'+ val.market_name + ' - ' + val.country_name + '</option>');
            });                
                $("#ddlmultiSpecificMarket").attr('multiple', 'multiple'); 
                $("#ddlmultiSpecificMarket").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-default',
                    enableCaseInsensitiveFiltering: true,
                    onChange: function(element, checked) {
                        var brands = $('#ddlmultiSpecificMarket option:selected');
                        var selectedCountriesArr = [];
                        $(brands).each(function(index, brand){
                            selectedRatesType = $(this).val();
                            selectedCountriesArr.push(selectedRatesType);
                            selectedCountriesArrJoin = selectedCountriesArr.join();
                        });
                    }
                });
            }
        }
    );
}


function dateRangePickervalid() {
    $('#daterangeServiceFromTo').daterangepicker({
        locale: {
            format: 'YYYY/MM/DD'
        },
        "autoApply": true,
		"opens": "center",
    }, function(start, end, label) {
        valid_from = start.format('YYYY-MM-DD');
        valid_to = end.format('YYYY-MM-DD');
    });
}
$("#btn-saveServicesClaim").click(function () {    
    var id_product_services_claim = document.getElementById("id_product_services_claim").innerHTML;
    var id_country = $('#ddlmultiSpecificMarket').val();
    var id_tour_operator = $('#ddlMultiSpecificTo').val();
    var date_range = $('#daterangeServiceFromTo').val();
    var dates = date_range.split(" - ");
    var valid_from = dates[0];
    var valid_to = dates[1];
    var valid_from = valid_from;
    var valid_to = valid_to;
    var ps_adult_claim = $('#ps_adult_claim').val();
    var ps_teen_claim = $('#ps_teen_claim').val();
    var ps_child_claim = $('#ps_child_claim').val();
    var ps_infant_claim = $('#ps_infant_claim').val();
    var id_currency = $('#id_currency').val();
    var currency = $('#id_currency').find(":selected").text();
    var specific_to = $('#specific_to').val();
    var specific_to_name = $('#specific_to').find(":selected").text();

    var chkmonday = document.getElementById("ex_monday");
    var chktuesday = document.getElementById("ex_tuesday");    
    var chkwednesday = document.getElementById("ex_wednesday");    
    var chkthursday = document.getElementById("ex_thursday");    
    var chkfriday = document.getElementById("ex_friday");
    var chksaturday = document.getElementById("ex_saturday");
    var chksunday = document.getElementById("ex_sunday");
    
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);    
    var id_dept = urlParams.get("id_dept"); 
    var id_product_service_cost = urlParams.get("id_product_services_cost"); 
    var id_product_service = urlParams.get("id_product_services"); 
    var charges = urlParams.get("charges");

    if (chkmonday.checked) {
        ex_monday = 1;
    } else if (chkmonday.checked == false) {
        ex_monday = 0;
    } 
    if (chktuesday.checked) {
        ex_tuesday = 1;
    } else if (chktuesday.checked == false) {
        ex_tuesday = 0;
    } 
    if (chkwednesday.checked) {
        ex_wednesday = 1;
    } else if (chkwednesday.checked == false) {
        ex_wednesday = 0;
    } 
    if (chkthursday.checked) {
        ex_thursday = 1;
    } else if (chkthursday.checked == false) {
        ex_thursday = 0;
    } 
    if (chkfriday.checked) {
        ex_friday = 1;
    } else if (chkfriday.checked == false) {
        ex_friday = 0;
    } 
    if (chksaturday.checked) {
        ex_saturday = 1;
    } else if (chksaturday.checked == false) {
        ex_saturday = 0;
    } 
    if (chksunday.checked) {
        ex_sunday = 1;
    } else if (chksunday.checked == false) {
        ex_sunday = 0;
    }  

    if (specific_to == 'A') { 
        id_country = 0;
    } else if (specific_to == 'B') {
        id_country = 0;
        id_tour_operator = 0;
    } else if (specific_to == 'C') {
        id_tour_operator = 0;
    }

    if (id_product_services_claim == 0) {
        const url_save_productservice_claim = "php/api/backofficeserviceclaim/saveproductservicesclaim.php?t=" + encodeURIComponent(global_token);
        var objProductServiceClaim = {
            id_product_service_claim: -1,
            id_product_service_cost: id_product_service_cost,
            id_product_service: id_product_service,


            valid_from: valid_from,
            valid_to: valid_to,
            id_dept: id_dept,
            specific_to: specific_to,
            charges: charges,
            ps_adult_claim: ps_adult_claim,
            ps_teen_claim: ps_teen_claim,
            ps_child_claim: ps_child_claim,
            ps_infant_claim: ps_infant_claim,        
            id_currency: id_currency,
            currency: currency,
            ex_monday: ex_monday,
            ex_tuesday: ex_tuesday,
            ex_wednesday: ex_wednesday,
            ex_thursday: ex_thursday,
            ex_friday: ex_friday,
            ex_saturday: ex_saturday,
            ex_sunday: ex_sunday,
            id_country: id_country,
            id_tour_operator: id_tour_operator,
            specific_to_name: specific_to_name
        };

        console.log(objProductServiceClaim);

        $.ajax({
            url : url_save_productservice_claim,
            method : "POST",
            data : objProductServiceClaim,                                                                                                                                                                                                                                                                                                                                                                                                                
            success : function(data){
                resetProductServicesClaim();;
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    } else {
        var id_product_service_claim = document.getElementById("id_product_services_claim").innerHTML;
        const url_update_productservice_claim = "php/api/backofficeserviceclaim/updateproductservicesclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" +id_product_service_claim;
        var objProductServiceClaimUpdate = {
            valid_from: valid_from,
            valid_to: valid_to,
            specific_to: specific_to,
            charges: charges,
            ps_adult_claim: ps_adult_claim,
            ps_teen_claim: ps_teen_claim,
            ps_child_claim: ps_child_claim,
            ps_infant_claim: ps_infant_claim,        
            id_currency: id_currency,
            currency: currency,
            ex_monday: ex_monday,
            ex_tuesday: ex_tuesday,
            ex_wednesday: ex_wednesday,
            ex_thursday: ex_thursday,
            ex_friday: ex_friday,
            ex_saturday: ex_saturday,
            ex_sunday: ex_sunday
        };

        $.ajax({
            url : url_update_productservice_claim,
            method : "POST",
            data : objProductServiceClaimUpdate,                                                                                                                                                                                                                                                                                                                                                                                                                
            success : function(data){
                resetProductServicesClaim();;
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });

        if (specific_to == 'A') { 
            // UPDATE MULTISELECT
            var id_product_service_claim = document.getElementById("id_product_services_claim").innerHTML;
            const url_update_to_claim = "php/api/backofficeserviceclaim/deleteselectedto.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" +id_product_service_claim;
            var objProductServiceClaimUpdateTo = {id_product_service_claim: id_product_service_claim};
            $.ajax({
                url: url_update_to_claim,
                method: "POST",
                data: objProductServiceClaimUpdateTo,
                success: function (data) {
                },
                error: function (error) {
                    console.log('Error ${error}');
                }
            });
            
            const url_save_countries_claim = "php/api/backofficeserviceclaim/saveselectedto.php?t=" + encodeURIComponent(global_token);
            var objProductServiceClaimSaveCountries = {
                id_product_services_claim_to: -1,
                id_product_service_claim: id_product_service_claim,
                id_tour_operator: id_tour_operator
            };
            $.ajax({
                url: url_save_countries_claim,
                method: "POST",
                data: objProductServiceClaimSaveCountries,
                success: function (data) {
                },
                error: function (error) {
                    console.log('Error ${error}');
                }
            });
        } else if (specific_to == 'C') {
            // UPDATE MULTISELECT
            var id_product_service_claim = document.getElementById("id_product_services_claim").innerHTML;
            const url_update_countries_claim = "php/api/backofficeserviceclaim/deleteselectedcountries.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" +id_product_service_claim;
            var objProductServiceClaimUpdateCountries = {id_product_service_claim: id_product_service_claim};
            $.ajax({
                url: url_update_countries_claim,
                method: "POST",
                data: objProductServiceClaimUpdateCountries,
                success: function (data) {
                },
                error: function (error) {
                    console.log('Error ${error}');
                }
            });
            
            const url_save_countries_claim = "php/api/backofficeserviceclaim/saveselectedcountries.php?t=" + encodeURIComponent(global_token);
            var objProductServiceClaimSaveCountries = {
                id_product_services_claim_countries: -1,
                id_product_service_claim: id_product_service_claim,
                id_country: id_country
            };
            $.ajax({
                url: url_save_countries_claim,
                method: "POST",
                data: objProductServiceClaimSaveCountries,
                success: function (data) {
                },
                error: function (error) {
                    console.log('Error ${error}');
                }
            });
        }
    }

    allServicesGridClaim(id_product_service_cost);
});

function resetProductServicesClaim() {
    $('#ddlmultiSpecificMarket').val([]).multiselect('refresh');
    $('#ddlMultiSpecificTo').val([]).multiselect('refresh');
    $('#valid_from').val('');
    $('#valid_to').val('');
    $('#ps_adult_claim').val('');
    $('#ps_teen_claim').val('');
    $('#ps_child_claim').val('');
    $('#ps_infant_claim').val('');
    $('#id_currency').val('');
    $('#specific_to').val('');
    $("#ex_monday").prop("checked", false);
    $("#ex_tuesday").prop("checked", false);
    $("#ex_wednesday").prop("checked", false);
    $("#ex_thursday").prop("checked", false);
    $("#ex_friday").prop("checked", false);
    $("#ex_saturday").prop("checked", false);
    $("#ex_sunday").prop("checked", false);
    $("#ddlmultiSpecificMarket").val('');
    $("#ddlMultiSpecificTo").val('');
}

function specificToCtrl(id_product_service_claim) {
    const url_claim_to = "php/api/backofficeserviceclaim/populateselectedto.php?t=" + encodeURIComponent(global_token)+ "&id_product_service_claim=" + id_product_service_claim; 
    $.ajax({
        type: "POST",
        url: url_claim_to,
        dataType: "json",
        cache: false,
        success: function(data)
                {
                var valArr = [data];
                valArr.forEach(myFunction);
                function myFunction(value) {
                    loadTourOperatorClaim2(value);              
                }
        }    
    });
}

function loadTourOperatorClaim2(value) {
    $("#multiSpecificMarket").css("display", "none");
    $("#multiSpecificTo").css("display", "block");
    const url_to = "php/api/backofficeserviceclaim/tocombo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        type: "POST",
        url: url_to,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#ddlMultiSpecificTo").empty();
                $.each(data, function (key, val) {
                    $("#ddlMultiSpecificTo").append('<option value="' + val.id + '">' + val.toname + '</option>');
                }); 
                arrToSelected = [];
                for (var i = 0, l = value.length; i < l; i++) {
                    var objSelected = value[i].id_tour_operator;
                    arrToSelected.push(objSelected);
                
                    $("#ddlMultiSpecificTo").find("option[value=" + objSelected + "]").prop("selected", true)
                    $("#ddlMultiSpecificTo").multiselect("refresh")    
                }  
                $("#ddlMultiSpecificTo").attr('multiple', 'multiple'); 
                $("#ddlMultiSpecificTo").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-default',
                    enableCaseInsensitiveFiltering: true
                });
            }
        }
    );
}


function specificCountriesCtrl(id_product_service_claim) {
    const url_claim_countries = "php/api/backofficeserviceclaim/populateselectedcountries.php?t=" + encodeURIComponent(global_token)+ "&id_product_service_claim=" + id_product_service_claim; 
    $.ajax({
        type: "POST",
        url: url_claim_countries,
        dataType: "json",
        cache: false,
        success: function(data)
                {
                var valArr1 = [data];
                valArr1.forEach(myFunction1);
                function myFunction1(value1) {
                    loadCountriesClaim2(value1);              
                }
        }    
    });
}

// Load Market By Default - On Button Edit click
function loadCountriesClaim2(value1) {
    console.log(value1);
    $("#multiSpecificMarket").css("display", "block");
    $("#multiSpecificTo").css("display", "none");
    const url_market_2 = "php/api/backofficeserviceclaim/attachcountriesclaim.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        type: "POST",
        url: url_market_2,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#ddlmultiSpecificMarket").empty();
                $.each(data, function (key, val) {
                    console.log(val.countryId);
                $("#ddlmultiSpecificMarket").append('<option value="' + val.countryId + '">'+ val.country_name + '</option>');
                });          
                arrCountriesSelected = [];
                for (var i = 0, l = value1.length; i < l; i++) {
                    var objSelected = value1[i].id_country;
                    arrCountriesSelected.push(objSelected);
                    $("#ddlmultiSpecificMarket").find("option[value=" + objSelected + "]").prop("selected", true);
                    $("#ddlmultiSpecificMarket").multiselect("refresh");    
                }      
                $("#ddlmultiSpecificMarket").attr('multiple', 'multiple'); 
                $("#ddlmultiSpecificMarket").multiselect({
                    buttonWidth: '313px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-default',
                    enableCaseInsensitiveFiltering: true
                });
            }
            
        }
    );
}