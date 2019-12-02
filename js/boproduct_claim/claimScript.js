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
const url_currency_buy = "php/api/backofficeproduct/currency_combo_rates.php?t=" + encodeURIComponent(global_token); 

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
var id_product_service_cost = urlParams.get("id_product_service_cost");

const url_extra_created = "php/api/backofficeserviceclaim/comboextraclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_cost=" + id_product_service_cost; 

$.ajax({
    url : url_extra_created,
    type : "GET",
    success : function(data) {
        helpersDropdownExtraCreated.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_product_service_extra'),
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
                dropdown.append('<option value="' + result.id_product_service_extra_cost +'" name="'+result.extra_name+',*'+result.charge+'">' + result.extra_name + '</option>');
                $("#blockPax").css("display", "none");
                $("#blockUnit").css("display", "none");
            });
        }
        
        $("#id_product_service_extra").on('change', function() {
            
            var charge1 = $('#id_product_service_extra').find('option:selected').attr("name");

            var charge = charge1.split(',*');
            var idproductserviceextracost = $('#id_product_service_extra').val();
            document.getElementById("product_service_claim_charge").innerHTML = charge[1];
            document.getElementById("id_product_service_extra_cost").innerHTML = idproductserviceextracost;
            if (charge[1] == 'UNIT') {
				$(".blockPax").hide();
				$(".blockUnit").show();
            } 
			else 
			{
				$(".blockPax").show();
				$(".blockUnit").hide();
            }
        });
    }
}
