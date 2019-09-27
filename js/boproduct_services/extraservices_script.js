/////////////////////////////////////////
// model --> fetch Api Extra Name Exist//
/////////////////////////////////////////
const url_extra = "php/api/backofficeproduct/comboextraservices.php?t=" + encodeURIComponent(global_token); 

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
                dropdown.append('<option value="' + result.id_services_extra + '" name="'+result.extra_name+'">' + result.extra_name + '</option>');
            });
        }
    }
}
/////////////////////////////////////////
// model --> fetch Api extra created ////
/////////////////////////////////////////
const url_extra_created = "php/api/backofficeproduct/comboextraforservices.php?t=" + encodeURIComponent(global_token); 

$.ajax({
    url : url_extra_created,
    type : "GET",
    success : function(data) {
        helpersDropdownExtraCreated.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_product_services_extra_1'),
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
                dropdown.append('<option value="' + result.id_product_services_extra + '" name="'+result.extra_name+'">' + result.extra_name + '</option>');
            });
        }
    }
}
