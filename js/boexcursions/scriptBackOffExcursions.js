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

    $('#multiselectRate1').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });
    
    quoteDetailsPaxBreaks();
    dateRangePickerServiceFromTo();
    dateRangePickerSalesFromTo();
    $('.checkerBtn').hide();
});

function quoteDetailsPaxBreaks() {
    var max_fields_limit      = 9999; //set limit for maximum input fields
    var x = 0; //initialize counter for text box
    $('.add_more_button').click(function(e){ //click event on add more fields button having class add_more_button
        e.preventDefault();
            if(x < max_fields_limit){ //check conditions
                x++; //counter increment
                $('.input_fields_container_part').append('<form class="paxBreaksForm"><div class="col-md-4" id="paxBreakMain"><div class="col-md-3"><input type="number" id="removeId' + x + '" class="form-control" name="paxBreaksFrom' + x + '" disabled/></div><div class="col-md-2 chkArrow"><i class="fa fa-arrow-right" aria-hidden="true"></i></div><div class="col-md-7"><input type="number" max="9999" min="1" class="form-control addedValue" id="addedId' + x + '" onkeyup = "CallTxtEvent('+ x +')" name="paxBreaksTo' + x + '"/></div><a href="#" class="remove_field" style="margin-left:10px;"><i aria-hidden="true" class="fa fa-trash-o fa-lg"></i></a></div></form>'); //add input field
            }
    });  

    $('.input_fields_container_part').on("click",".remove_field", function(e){ //user click on remove text links
        e.preventDefault();
        $(this).parent('div').remove(); 
        x--;
    });
}

function dateRangePicker(idBlockRates) {
    var counterClose = 0;
    $('#dateRangeClosedDate').daterangepicker({
        locale: {
            format: 'DD/MMM/YYYY'
        },
        opens: 'left'
    }, function(start, end, label) {
        counterClose++;
        var closedStartDate = start.format('YYYY-MM-DD');
        var closedEndDate = end.format('YYYY-MM-DD');

        var chkkk = start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD');
        selectedClosedDateFunc(closedStartDate, closedEndDate, idBlockRates);
    });
}

function dateRangePickerServiceFromTo() {
    $('#daterangeServiceFromTo').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
        opens: 'left'
    }, function(start, end, label) {
        var serviceStartDate = start.format('YYYY-MM-DD');
        var serviceEndDate = end.format('YYYY-MM-DD');
        servicedatefunc(serviceStartDate, serviceEndDate);
    });
}

function dateRangePickerSalesFromTo() {
    $('#daterangeSalesFromTo').daterangepicker({
        locale: {
            format: 'DD/MMM/YYYY'
        },
        opens: 'left'
    }, function(start, end, label) {
        var serviceSalesStartDate = start.format('YYYY-MM-DD');
        var serviceSalesEndDate = end.format('YYYY-MM-DD');
    });
}
