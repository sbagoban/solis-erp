document.getElementById("aTitle").innerHTML = "Back Office Excursions";

$(document).ready(function () {    
    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();  
    var counter = 0;
    var button = document.getElementById("btnCounter");

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

    $("#txtEditor").Editor();
    // Add Excursions

    $('#multiselectRate0').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });

    $('#multiselectRate1').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });

    $('#multiselectRate2').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });
    quoteDetailsPaxBreaks();
    dateRangePicker();
});

function quoteDetailsPaxBreaks() {
    var max_fields_limit      = 9999; //set limit for maximum input fields
    var x = 1; //initialize counter for text box
    $('.add_more_button').click(function(e){ //click event on add more fields button having class add_more_button
        e.preventDefault();
            if(x < max_fields_limit){ //check conditions
                x++; //counter increment
                $('.input_fields_container_part').append('<div class="col-md-4"><input type="number" max="9999" min="1" class="form-control" name="tags"/><a href="#" class="remove_field" style="margin-left:10px;">Remove</a></div>'); //add input field
            }
    });  

    $('.input_fields_container_part').on("click",".remove_field", function(e){ //user click on remove text links
        e.preventDefault(); 
        $(this).parent('div').remove(); 
        x--;
    });
}

function dateRangePicker() {
    $('input[name="daterange"]').daterangepicker({
        opens: 'left'
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        var chkkk = start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD');
        $('#selectedClosedDate').append('<div class="col-md-4">' + chkkk + '<a href="#" class="remove_field1"><i aria-hidden="true" class="fa fa-trash-o fa-lg"></i></a></div>');
    });
    
    $('#selectedClosedDate').on("click",".remove_field1", function(e){ //user click on remove text links
        e.preventDefault(); 
        $(this).parent('div').remove();
    });
}