document.getElementById("aTitle").innerHTML = "BOOKING SEARCH ENGINE";

$(document).ready(function () {

    // Add Excursions
    // Duration Picker
    $('#duration').durationPicker({
        showDays : false
    });
    $("[id^=flagDeleted]").on("change", function(e){
        if(e.target.checked){
            $('#alertModal').modal();
        }
    });

    // Add row
    var row=1;
    $(document).on("click", "#add-row", function () {
        var new_row = '<div class="col-md-12" id="addRowBody"><br><div class="input-group"><input type="text" id="name' + row + '" class="form-control" placeholder="Name"><span class="input-group-addon">></span><input type="text" id="name' + row + '" class="form-control" placeholder="Extra Description"><span class="input-group-addon">></span><input type="number" id="name' + row + '" max="20" min="1" class="form-control"></div></div><br>';
        $('#addRowBody').append(new_row);
        row++;
        return false;
    });

    $("#txtEditor").Editor();
    // Add Excursions
    // Add Excursions

    var date = new Date();
    date.setDate(date.getDate() + 1);
    console.log('chk', date.getDate() + 1);
    $('#datetimepicker').datepicker({
        format: 'dd-mm-yyyy',
        startDate: date
    });

    $('.filterBlock').slimScroll({
        height: '205px',
        color: '#DCDEE2',
        size: '5px',
        alwaysVisible: false
    });

    // Hotel Names Accomodations
    var hotelNamesObj = [
        "Heriatge", "Luxe", "Sofitel", "Long Beach", "Casuarina"
    ];

    $("#hotelNametags").autocomplete({
        appendTo: '.hotelNamecheck',
        source: hotelNamesObj
    });

    //  Pickup Excursions
    // var pickUpObj = [
    //     "Heriatge", "Luxe", "Sofitel", "Long Beach", "Casuarina"
    // ];

    // $('#pickUpTags').autocomplete({
    //     appendTo: '.pickUpCheck',
    //     source: pickUpObj
    // });

    // Drop Off Place Excursions
    // var dropOffObj = [
    //     "Heriatge", "Luxe", "Sofitel", "Long Beach", "Casuarina"
    // ];

    // $('#dropOffTags').autocomplete({
    //     appendTo: '.dropOffCheck',
    //     source: dropOffObj
    // });

});

// //- Using an anonymous function:
// document.getElementById("clickMe").onclick = function() {
//     $("#select_market").validate({
//         rules: {
//             item: {
//                 required: true
//             }
//         }
//     });
// };

$(document).ready(function () {
    $('#multiselect').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });

});