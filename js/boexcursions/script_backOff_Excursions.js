document.getElementById("aTitle").innerHTML = "Back Office Excursions";

$(document).ready(function () {

    
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



    function addRow() {
        var main = $('.addMain').val();
        var preferred = $('.addPrefer').val();
        var common = $('.addCommon').val();
        $(formatRows(main, preferred, common, counter++)).insertAfter('#addRow');
       // test = $(input).val('');
    }
    
    button.onclick = function(){
        counter++;
    }

    function formatRows(main, prefer, common, counter) {
        return '<tr><td class="col-xs-3"><input type="text" value="' +main+ '" class="form-control editable" /></td>' +
            '<td class="col-xs-3"><input type="text" value="' +prefer+ '" class="form-control editable" /></td>' +
            '<td class="col-xs-3"><div class="resize4 form-control">' +
            '<div class="policiesGroup"><p><input type="radio" id="chargePerPerson'+counter+'" name="radioChargePer'+counter+'" checked>' +
            '<label for="chargePerPerson'+counter+'">Person</label></p><p><input type="radio" id="chargePerUnit'+counter+'" name="radioChargePer'+counter+'">' + 
            '<label for="chargePerUnit'+counter+'">Unit</label></p></div></div></td>' +
            '<td class="col-xs-1 text-center"><a class="deleteBtn" data-toggle="tooltip" title="Delete Row">' +
            '<i id="deleteBtn" class="fa fa-trash-o fa-lg" aria-hidden="true"></a></td></tr>';
    };
    // Add Row
    $('.addBtn').click(function()  {
        addRow();
    });
    // Remove Row
    $("body").on("click", ".deleteBtn", function () {
        $(this).closest("tr").remove();
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
});

$(document).ready(function () {
    $('#multiselect').multiselect({
        buttonWidth: '313px',
        includeSelectAllOption: true,
        nonSelectedText: 'Select an Option',
        enableFiltering: true,
        enableHTML: true,
        buttonClass: 'btn large btn-primary',
    });
    // Tooltip
    $('[data-toggle="tooltip"]').tooltip();   
});