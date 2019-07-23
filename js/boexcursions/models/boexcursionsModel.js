$(document).ready(function(){
    ////////////////////////////////////////////////////
    // model --> Search Block Display None By Deafult //
    ////////////////////////////////////////////////////
    document.getElementById('searchServiceDetails').setAttribute('style', 'display: none');

    /////////////////////////////////////////////
    // model --> fetch Api Select Service Type //
    /////////////////////////////////////////////
    const url = "php/api/combos/servicetype_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url,
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
    const url_countrycombo = "php/api/combos/countryexcursions_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url_countrycombo,
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

    /////////////////////////////////////////
    // model --> save form value to dB //////
    /////////////////////////////////////////
    $("#createNewService").click(function () {
        var ddlLocationSelected = $('#ddlChooseLocation').val();
        var ddlServiceTypeSelected = $('#ddlSelectServiceType').val();
        var ddlSupplierSelected = $('#ddlChooseSupplier').val();
        var ddlOptionCodeSelected = $('#ddlOptionCode').val();
        var generatedCode = document.getElementsByTagName('code')[0].innerHTML;
        var descriptionValue = document.getElementById('addedDescription').value;
        var commentValue = document.getElementById('addedComment').value;

        var objService = {
            id:-1, //for new items, id is always -1
            locationservice: ddlLocationSelected, //please make sure the names match in JS and PHP
            servicetype: ddlServiceTypeSelected,
            supplier: ddlSupplierSelected,
            optioncode: ddlOptionCodeSelected+generatedCode,
            descriptionservice: descriptionValue,
            comments: commentValue
        };

        
        const url_save_service = "php/api/bckoffservices/savenewservices.php?token=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service,
            method : "POST",
            data : objService,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
                console.log('value', data);
                // TO ADD THIS IN SUCCESS //
                document.getElementById('searchServiceDetails').setAttribute('style', 'display: block');
                $('.toast').stop().fadeIn(400).delay(3000).fadeOut(500);
                // TO ADD THIS IN SUCCESS //
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });

        
    });
    // End click
});


