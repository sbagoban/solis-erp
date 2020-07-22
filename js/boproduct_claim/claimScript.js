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
            console.log('-->', charge);
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


$(document).ready(function(){ 
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product_service_cost = urlParams.get("id_product_service_cost");
    var servicetype = urlParams.get("servicetype");

    var ps_adult_cost = urlParams.get("ps_adult_cost");
    var ps_teen_cost = urlParams.get("ps_teen_cost");
    var ps_child_cost = urlParams.get("ps_child_cost");
    var ps_infant_cost = urlParams.get("ps_infant_cost");

    var for_infant = urlParams.get("for_infant");
    var for_child = urlParams.get("for_child");
    var for_teen = urlParams.get("for_teen");
    var for_adult = urlParams.get("for_adult");
    var is_pakage = urlParams.get("is_pakage");

    // here sandeep check
    console.log(id_product_service_cost > 0,  '&&', is_pakage != 'Y');
    if (id_product_service_cost > 0 && is_pakage != 'Y') {
        if (ps_adult_cost > 0) {
            $("#ps_adult_claim").css("display", "block");
            $("#ps_adult_claim").attr("placeholder", "Adult"); 
            $("#ps_adult_claim_modal").css("display", "block");
            $("#ps_adult_claim_modal").attr("placeholder", "Adult");   
        }
        if (ps_adult_cost <= 0) { 
            $("#ps_adult_claim_addon").css("display", "none");
            $("#ps_adult_claim").css("display", "none");
            $("#ps_adult_claim_addon_modal").css("display", "none");
            $("#ps_adult_claim_modal").css("display", "none");
        }
        
        if (ps_teen_cost > 0) {
            $("#ps_teen_claim").css("display", "block");
            $("#ps_teen_claim").attr("placeholder", "Teen");
            $("#ps_teen_claim_modal").css("display", "block");
            $("#ps_teen_claim_modal").attr("placeholder", "Teen");
        }
        if (ps_teen_cost <= 0) { 
            $("#ps_teen_claim_addon").css("display", "none");
            $("#ps_teen_claim").css("display", "none");
            $("#ps_teen_claim_addon_modal").css("display", "none");
            $("#ps_teen_claim_modal").css("display", "none");
        }
        
        if (ps_child_cost > 0) {
            $("#ps_child_claim").css("display", "block");
            $("#ps_child_claim").attr("placeholder", "Child");
            $("#ps_child_claim_modal").css("display", "block");
            $("#ps_child_claim_modal").attr("placeholder", "Child");
        }
        if (ps_child_cost <= 0) { 
            $("#ps_child_claim_addon").css("display", "none");
            $("#ps_child_claim").css("display", "none");
            $("#ps_child_claim_addon_modal").css("display", "none");
            $("#ps_child_claim_modal").css("display", "none");
        }

        if (ps_infant_cost > 0) {
            $("#ps_infant_claim").css("display", "block");
            $("#ps_infant_claim").attr("placeholder", "Infant");
            $("#ps_infant_claim_modal").css("display", "block");
            $("#ps_infant_claim_modal").attr("placeholder", "Infant");
        }
        if (ps_infant_cost <= 0) { 
            $("#ps_infant_claim_addon").css("display", "none");
            $("#ps_infant_claim").css("display", "none");
            $("#ps_infant_claim_addon_modal").css("display", "none");
            $("#ps_infant_claim_modal").css("display", "none");
        }
    } 

    // Check if package for this statement
    if (is_pakage == 'Y') {
        if (for_adult > 0) {
            $("#ps_adult_claim").css("display", "block");
            $("#ps_adult_claim").attr("placeholder", "Adult");
            $("#ps_adult_claim_modal").css("display", "block");
            $("#ps_adult_claim_modal").attr("placeholder", "Adult");
        }
        if (for_adult <= 0) { 
            $("#ps_adult_claim_addon").css("display", "none");
            $("#ps_adult_claim").css("display", "none");
            $("#ps_adult_claim_addon_modal").css("display", "none");
            $("#ps_adult_claim_modal").css("display", "none");
        }
        
        if (for_teen > 0) {
            $("#ps_teen_claim").css("display", "block");
            $("#ps_teen_claim").attr("placeholder", "Teen");
            $("#ps_teen_claim_modal").css("display", "block");
            $("#ps_teen_claim_modal").attr("placeholder", "Teen");
        }
        if (for_teen <= 0) { 
            $("#ps_teen_claim_addon").css("display", "none");
            $("#ps_teen_claim").css("display", "none");
            $("#ps_teen_claim_addon_modal").css("display", "none");
            $("#ps_teen_claim_modal").css("display", "none");
        }
        
        if (for_child > 0) {
            $("#ps_child_claim").css("display", "block");
            $("#ps_child_claim").attr("placeholder", "Child");
            $("#ps_child_claim_modal").css("display", "block");
            $("#ps_child_claim_modal").attr("placeholder", "Child");
        }
        if (for_child <= 0) { 
            $("#ps_child_claim_addon").css("display", "none");
            $("#ps_child_claim").css("display", "none");
            $("#ps_child_claim_addon_modal").css("display", "none");
            $("#ps_child_claim_modal").css("display", "none");
        }

        if (for_infant > 0) {
            $("#ps_infant_claim").css("display", "block");
            $("#ps_infant_claim").attr("placeholder", "Infant");
            $("#ps_infant_claim_modal").css("display", "block");
            $("#ps_infant_claim_modal").attr("placeholder", "Infant");
        }
        if (for_infant <= 0) { 
            $("#ps_infant_claim_addon").css("display", "none");
            $("#ps_infant_claim").css("display", "none");
            $("#ps_infant_claim_addon_modal").css("display", "none");
            $("#ps_infant_claim_modal").css("display", "none");
        }
    } 

    if (servicetype== "TRANSFER") {
        $("#ps_adult_claim").css("display", "block");
        $("#ps_adult_claim").attr("placeholder", "Adult");
        $("#ps_child_claim").css("display", "block");
        $("#ps_child_claim").attr("placeholder", "Child");
        $("#ps_adult_claim_modal").css("display", "block");
        $("#ps_adult_claim_modal").attr("placeholder", "Adult");
        $("#ps_child_claim_modal").css("display", "block");
        $("#ps_child_claim_modal").attr("placeholder", "Child");
    }

    /////////////////////////
    ///////ROLL OVER ////////
    /////////////////////////
    $('#txtRollOver').attr('disabled', 'disabled');
    $('#txtRollOver').val(0);
    $("#roll_over").on('change', function() {

        var roll_over = $('#roll_over').val();
        if (roll_over == 'Same Rate') {
            $('#txtRollOver').val(0);
            $('#txtRollOver').attr('disabled', 'disabled');
        }
        if (roll_over == 'On Request') {
            $('#txtRollOver').val(0);
            $('#txtRollOver').attr('disabled', 'disabled');
        }
        if (roll_over == 'Percentage') {
            $('#txtRollOver').removeAttr('disabled');
        }
        if (roll_over == 'Fix Amount') {
            $('#txtRollOver').removeAttr('disabled');
        }
    });
    
    /////////////////////////
    ///////ROLL OVER ////////
    /////////////////////////
});

// Paxbreak Multi price list modal
function multiplePrice() { 
    var chkmultipleprice = document.getElementById("multiple_price"); 
    if (chkmultipleprice.checked == true) {
        var multiple_price_chk = 1;        
        document.getElementById("ps_adult_claim").disabled = true;
        document.getElementById("ps_teen_claim").disabled = true;
        document.getElementById("ps_child_claim").disabled = true;
        document.getElementById("ps_infant_claim").disabled = true;
        $("#ps_adult_claim").val(0); 
        $("#ps_teen_claim").val(0); 
        $("#ps_child_claim").val(0); 
        $("#ps_infant_claim").val(0); 
    } else { 
        var multiple_price_chk = 0;        
        document.getElementById("ps_adult_claim").disabled = false;
        document.getElementById("ps_teen_claim").disabled = false;
        document.getElementById("ps_child_claim").disabled = false;
        document.getElementById("ps_infant_claim").disabled = false;
        $("#ps_teen_claim").attr("placeholder", "Teen");
        $("#ps_infant_claim").attr("placeholder", "Infant");
        $("#ps_child_claim").attr("placeholder", "Child");
        $("#ps_adult_claim").attr("placeholder", "Adult");
    }
}
