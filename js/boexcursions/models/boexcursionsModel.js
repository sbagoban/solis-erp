$(document).ready(function(){
    /////////////////////////////////////////
    // model --> fetch Api Select Location //
    /////////////////////////////////////////
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
    
    ////////////////////////////////////////
    // model --> Selected Location Option //
    ////////////////////////////////////////
    $("#ddlSelectServiceType").change(function () {
        var color = $(this);
        var ddlSelectServiceType = color.val(); 
        console.log(ddlSelectServiceType);
    });
});


