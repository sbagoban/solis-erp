//Date picker
$('#valid_from').datepicker({		
    autoclose: true,
    format: 'yyyy-mm-dd'
});
$('#valid_to').datepicker({		
    autoclose: true,
    format: 'yyyy-mm-dd'
});
/////////////////////////////////////////
// model --> fetch Api Currency sell ////
/////////////////////////////////////////
const url_currency_buy = "php/api/backoffservices_rates/currency_combo_rates.php?t=" + encodeURIComponent(global_token); 

$.ajax({
    url : url_currency_buy,
    type : "GET",
    success : function(data) {
        helpersDropdownCurrency.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_currency'),
            'Select an option'
        );
    }, 
    error:function(error) {
        console.log('Error ${error}');
    }
});
var helpersDropdownCurrency = {
    buildDropdown: function(result, dropdown, emptyMessage) {
        // Remove current options
        dropdown.html('');
        // Add the empty option with the empty message
        dropdown.append('<option value="">' + emptyMessage + '</option>');
        // Check result isnt empty
        if(result != '') {
            // Loop through each of the results and append the option to the dropdown
            $.each(result, function(data, result) {
                dropdown.append('<option value="' + result.value + '" name="' + result.text + '">' + result.text + '</option>');
            });
        }
    }
}



/////////////////////////////////////////
// model --> fetch Api extra created ////
/////////////////////////////////////////
var allParams = window.location.href.split('data=').pop();
const urlParams = new URLSearchParams(allParams);
var id_product_services_cost = urlParams.get("id_product_services_cost");

console.log('-->', id_product_services);
const url_extra_created = "php/api/backofficeserviceclaim/comboextraclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_services_cost=" + id_product_services_cost; 

$.ajax({
    url : url_extra_created,
    type : "GET",
    success : function(data) {
        helpersDropdownExtraCreated.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_product_services_extra'),
            'Select an option' 
        );
    }, 
    error:function(error) {
        console.log('Error ${error}');
    }
});
var helpersDropdownExtraCreated = {
    buildDropdown: function(result, dropdown, emptyMessage) {
        // Remove current options
        dropdown.html('');
        // Add the empty option with the empty message
        dropdown.append('<option value="">' + emptyMessage + '</option>');
        // Check result isnt empty
        if(result != '') {
            // Loop through each of the results and append the option to the dropdown
            $.each(result, function(data, result) {
                console.log(result, '<<<<----');
                dropdown.append('<option value="' + result.id_product_services_extra_cost + ','+result.charges+'" name="'+result.extra_name+'">' + result.extra_name + '</option>');
                $("#blockPax").css("display", "none");
                $("#blockUnit").css("display", "none");
            });
        }
        
        $("#id_product_services_extra").on('change', function() {
            var charges = $('#id_product_services_extra').val().split(',');
            document.getElementById("product_services_claim_charges").innerHTML = charges[1];
            document.getElementById("id_product_services_extra_cost").innerHTML = charges[0];
            if (charges[1] == 'UNIT') {
                $("#ps_teen_claim_1").css("display", "none");
                $("#ps_child_claim_1").css("display", "none");                
                $("#ps_infant_claim_1").css("display", "none");
                $("#ps_adult_claim_1").css("display", "block");
                $("#ps_teen_claim_11").css("display", "none");
                $("#ps_child_claim_11").css("display", "none");                
                $("#ps_infant_claim_11").css("display", "none");
                $("#ps_adult_claim_11").css("display", "block");
                document.getElementsByName('ps_adult_claim_1')[0].placeholder='Unit';
            } else {
                document.getElementsByName('ps_adult_claim_1')[0].placeholder='Adult';
                $("#ps_teen_claim_1").css("display", "block");
                $("#ps_child_claim_1").css("display", "block");                
                $("#ps_infant_claim_1").css("display", "block");
                $("#ps_adult_claim_1").css("display", "block");
                $("#ps_teen_claim_11").css("display", "block");
                $("#ps_child_claim_11").css("display", "block");                
                $("#ps_infant_claim_11").css("display", "block");
                $("#ps_adult_claim_11").css("display", "block");
            }
        });
    }
}
