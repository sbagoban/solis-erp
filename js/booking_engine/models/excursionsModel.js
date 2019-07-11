///////////////////////////////////////
// model --> fetch Api Select Market //
///////////////////////////////////////
$(document).ready(function(){
    const URL = "php/api/combos/market_combo.php?t=" + encodeURIComponent(global_token); 
        $.ajax({
            url : URL,
            type : "GET",
            success : function(data) {
                helpersDropdown.buildDropdown(
                    jQuery.parseJSON(data),
                    $('#ddlSelectMarketExcursions'),
                    'Select an option'
                );
            }, 
            error:function(error) {
                console.log('Error ${error}');
            }
        })


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
                dropdown.append('<option value="' + result.id + '">' + result.market_name + '</option>');
            });
        }
    }
}

////////////////////////////////////////
// model --> Pick up + Drop Off Place //
////////////////////////////////////////

// NOTE : Call --> OnKeyup
    $('#pickUpTags').typeahead({
        source: function (query, result) {
            $.ajax({
                url: "php/api/combos/market_combo.php?t=" + encodeURIComponent(global_token),
                data: 'query=' + query,            
                dataType: "json",
                type: "POST",
                success: function (data) {
                    result($.map(data, function (item) {
                        return item;
                    }));
                }
            });
        }
    });
});