$(document).ready(function(){
    /////////////////////////////////////////////
    // model --> fetch Api Select Service Type //
    /////////////////////////////////////////////
    const URL = "php/api/combos/servicetype_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : URL,
        type : "GET",
        success : function(data) {
            helpersDropdown.buildDropdown(
                jQuery.parseJSON(data),
                $('#ddlSelectServiceType'),
                'Select an option'
            );
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdown = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.servicetype + '">' + result.servicetype + '</option>');
                });
            }
        }
    }

    /////////////////////////////////////////
    // model --> fetch Api Select Location //
    /////////////////////////////////////////
    const URL_CountryCombo = "php/api/combos/countryexcursions_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : URL_CountryCombo,
        type : "GET",
        success : function(data) {
            helpersDropdownLocation.buildDropdown(
                jQuery.parseJSON(data),
                $('#ddlChooseLocation'),
                'Select an option'
            );
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdownLocation = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.text + '">' + result.text + '</option>');
                });
            }
        }
    }
    
    /////////////////////////////////////////
    // model --> Selected Option Code1///////
    /////////////////////////////////////////
    $("#ddlOptionCode").change(function () {
        document.getElementById('generate').setAttribute('style', 'display: block');
        document.getElementById('generateNone').setAttribute('style', 'display: none');
    });

    $("#createNewService").click(function () {
        var ddlLocationSelected = $('#ddlChooseLocation').val();
        var ddlServiceTypeSelected = $('#ddlSelectServiceType').val();
        var ddlSupplierSelected = $('#ddlChooseSupplier').val();
        var ddlOptionCodeSelected = $('#ddlOptionCode').val();
        var generatedCode = document.getElementsByTagName('code')[0].innerHTML;
        var descriptionValue = document.getElementById('addedDescription').value;
        var commentValue = document.getElementById('addedComment').value;

        console.log('Location', ddlLocationSelected, 'ServiceType', ddlServiceTypeSelected, 'Supplier Selected', ddlSupplierSelected, 'Option Code', ddlOptionCodeSelected, 'Generated code',generatedCode, 'desciption', descriptionValue);
    });
    
});


