
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
                    dropdown.append('<option value="' + result.id + '">' + result.servicetype + '</option>');
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
                    dropdown.append('<option value="' + result.value + '">' + result.text + '</option>');
                });
            }
        }
    }

    /////////////////////////////////////////
    // model --> fetch Api Select Location //
    /////////////////////////////////////////
    const url_optioncode = "php/api/combos/contactdepartment_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url_optioncode,
        type : "GET",
        success : function(data) {
                helpersDropdownDepartment.buildDropdown(
                    jQuery.parseJSON(data), 
                    $('#ddlOptionCode'), 'Select option');
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdownDepartment = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.text + '">' + result.description + '</option>');
                });
            }
        }
    }
    
    /////////////////////////////////////////
    // model --> fetch Api Select Location //
    /////////////////////////////////////////
    const url_supplier = "php/api/combos/supplier_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url_supplier,
        type : "GET",
        success : function(data) {
            helpersDropdownSupplier.buildDropdown(
                jQuery.parseJSON(data),
                $('#ddlChooseSupplier'),
                'Select an option'
            );
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdownSupplier = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.value + '">' + result.suppliername + '</option>');
                });
            }
        }
    }
    
     /////////////////////////////////////////
    // model --> fetch Api Select Location //
    /////////////////////////////////////////
    const url_department = "php/api/combos/contactdepartment_combo.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        url : url_department,
        type : "GET",
        success : function(data) {
                helpersDropdownDepart.buildDropdown(
                    jQuery.parseJSON(data), 
                    $('#ddlChooseDept'), 'Select option');
        }, 
        error:function(error) {
            console.log('Error ${error}');
        }
    });
    var helpersDropdownDepart = {
        buildDropdown: function(result, dropdown, emptyMessage) {
            // Remove current options
            dropdown.html('');
            // Add the empty option with the empty message
            dropdown.append('<option value="">' + emptyMessage + '</option>');
            // Check result isnt empty
            if(result != '') {
                // Loop through each of the results and append the option to the dropdown
                $.each(result, function(data, result) {
                    dropdown.append('<option value="' + result.text + '">' + result.description + '</option>');
                });
            }
        }
    }

    /////////////////////////////////////////
    // model --> Selected Option Code1///////
    /////////////////////////////////////////
    $("#ddlOptionCode").change(function () {
        if ($('#ddlOptionCode').val() == '') {
            console.log('sasdg');
            document.getElementById('generate').setAttribute('style', 'display: none');
            document.getElementById('generateNone').setAttribute('style', 'display: block');
        } else { 
            document.getElementById('generate').setAttribute('style', 'display: block');
            document.getElementById('generateNone').setAttribute('style', 'display: none');
        }
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
            countryfk: ddlLocationSelected, //please make sure the names match in JS and PHP
            servicetypefk: ddlServiceTypeSelected,
            supplierfk: ddlSupplierSelected,
            optioncode: ddlOptionCodeSelected + generatedCode,
            descriptionservice: descriptionValue,
            comments: commentValue
        };

        const url_save_service = "php/api/bckoffservices/savenewservices.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service,
            method : "POST",
            data : objService,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
                console.log('value', data);
                callDataNewServiceGrid();
                resetFormAddNewService();
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });

        document.getElementById('searchServiceDetails').setAttribute('style', 'display: block');
        $('html, body').animate({
            scrollTop: $("#searchServiceDetails").offset().top
        }, 2000);

    });
    // End click

    // Function Reset Form Add New Service
    function resetFormAddNewService() {
        $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
        $('#createNewService').attr('disabled', 'disabled');
        $('select option:contains("Select an option")').prop('selected',true);
        $('#addedDescription').val('');
        $('#addedComment').val('');
        $("#output").html("0000");
        document.getElementById('generate').setAttribute('style', 'display: none');
        document.getElementById('generateNone').setAttribute('style', 'display: block');
    }

    
});


