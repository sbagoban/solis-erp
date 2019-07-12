document.getElementById("aTitle").innerHTML = "BOOKING SEARCH ENGINE";

$(document).ready(function () {

    $('.datepicker-in').datepicker({
        format: 'dd-mm-yyyy',
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