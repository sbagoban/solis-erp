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

    // function addRow() {
    //     var main = $('.addMain').val();
    //     var preferred = $('.addPrefer').val();
    //     var common = $('.addCommon').val();
    //     $(formatRows(main, preferred, common, counter++)).insertAfter('#addRow');
    //    // test = $(input).val('');
    // }
    
    // button.onclick = function(){
    //     counter++;
    // }

    // function formatRows(main, prefer, common, counter) {
    //     return '<tr><td class="col-xs-3"><input type="text" value="' +main+ '" class="form-control editable" disabled/></td>' +
    //         '<td class="col-xs-3"><input type="text" value="' +prefer+ '" class="form-control editable" disabled/></td>' +
    //         '<td class="col-xs-3"><div class="resize4 form-control">' +
    //         '<div class="policiesGroup"><p><input type="radio" id="chargePerPerson'+counter+'" name="radioChargePer'+counter+'">' +
    //         '<label for="chargePerPerson'+counter+'">Person</label></p><p><input type="radio" id="chargePerUnit'+counter+'" name="radioChargePer'+counter+'">' + 
    //         '<label for="chargePerUnit'+counter+'">Unit</label></p></div></div></td>' +
    //         '<td class="col-xs-1 text-center"><a data-toggle="tooltip">' +
    //         '<i id="deleteBtn" class="fa fa-trash-o fa-lg deleteBtn" aria-hidden="true" title="Delete Row"></i>' +
    //         '&nbsp;<i id="editBtn" class="fa fa fa-external-link editBtn" aria-hidden="true"  title="Edit Row"></a>' +
    //         '</td></tr>';
    // };
    

    // // Add Row
    // $('.addBtn').click(function()  {
    //     addRow();
    // });

    // // Remove Row
    // $("body").on("click", ".deleteBtn", function () {
    //     $(this).closest("tr").remove();
    // });


    $("#txtEditor").Editor();
    // Add Excursions
    // Add Excursions
});

