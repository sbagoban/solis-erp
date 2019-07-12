
$(document).ready(function(){
        ///////////////////////////////////////
        // model --> fetch Api Select Market //
        ///////////////////////////////////////
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
                        dropdown.append('<option value="' + result.id + '">' + result.market_name + '</option>');
                    });
                }
            }
        }

        ////////////////////////////////////////
        // model --> Pick up + Drop Off Place //
        ////////////////////////////////////////
        const URL2 = "php/api/combos/places_combo.php?t=" + encodeURIComponent(global_token); 
        $.ajax({
            url : URL2,
            type : "GET",            
            dataType: "json",
            success: function (data) {
                var placesNames = data.map(function (data) {
                    return data.places
                  });
                sendListPlacesNames(placesNames);
                sendListPlacesNamesDropOff(placesNames);

            }
        });
        ////////////////////////////////////////
        // model --> Pick up ///////////////////
        ////////////////////////////////////////
        function sendListPlacesNames(data) {
            $('#pickUpTags').autocomplete({
                source: data,
                appendTo: '.pickUpCheck',
                classes: {
                    "ui-autocomplete": "highlight"
                },
                select: function(event, ui) {
                    // Selected Value
                    console.log(ui.value);
                }
            });
        }
        ////////////////////////////////////////
        // model --> Drop Off Place ////////////
        ////////////////////////////////////////
        function sendListPlacesNamesDropOff(data) {
            $('#dropOffTags').autocomplete({
                source: data,
                appendTo: '.dropOffCheck',
                classes: {
                    "ui-autocomplete": "highlight"
                },
                select: function(event, ui) {
                    // Selected Value
                    console.log(ui.value);
                }
            });
        }
});


