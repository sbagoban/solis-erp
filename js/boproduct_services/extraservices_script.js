/////////////////////////////////////////
// model --> fetch Api Extra Name Exist//
/////////////////////////////////////////
$(document).ready(function(){
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");
    if (servicetype == "TRANSFER") {
        url_extra = "php/api/backofficeproduct/comboextraservicestransfer.php?t=" + encodeURIComponent(global_token); 
    } 
    if (servicetype == "EXCURSION") {
        url_extra = "php/api/backofficeproduct/comboextraservices.php?t=" + encodeURIComponent(global_token); 
    }
    
    $.ajax({
        url : url_extra,
        type : "GET",
        success : function(data) {
            helpersDropdownExtraExist.buildDropdown(
                jQuery.parseJSON(data),
                $('#extra_name'),
                'Select an option' 
            );
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdownExtraExist = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.id_service_extra + '" name="'+result.extra_name+'">' + result.extra_name + '</option>');
                });
            }
        }
    }
});
/////////////////////////////////////////
// model --> fetch Api extra created ////
/////////////////////////////////////////
var allParams = window.location.href.split('data=').pop();
const urlParams = new URLSearchParams(allParams);
var id_product_service = urlParams.get("id_product_service");

var productname = urlParams.get("productname");
var servicename = urlParams.get("servicename");

$('#product_name').val(productname + ' - ' + servicename);
const url_extra_created = "php/api/backofficeproduct/comboextraforservices.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + id_product_service; 

$.ajax({
    url : url_extra_created,
    type : "GET",
    success : function(data) {
        helpersDropdownExtraCreated.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_product_service_extra_1'),
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
                dropdown.append('<option value="' + result.id_product_service_extra + '" name="'+result.extra_name+'">' + result.extra_name + ' - ' + result.extra_description  +'</option>');
            });
        }
    }
}
