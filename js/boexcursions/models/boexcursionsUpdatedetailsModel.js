
function editRowService(data) {
    console.log('test->>', data);
    ////////////////////////////////////////////////////
    // model --> Search Block Display None By Deafult //
    ////////////////////////////////////////////////////
    document.getElementById('editServiceBlock').setAttribute('style', 'display: block');
    document.getElementById('serviceDetails').setAttribute('style', 'display: block');

    // Set Value - From Selected Line
    document.getElementById("OptionCodeDisplay").innerHTML = data.optioncode;
    document.getElementById("descriptionDisplay").innerHTML = data.descriptionservice;
    document.getElementById("commentsDisplay").innerHTML = data.comments;
    document.getElementById("idBlock").innerHTML = data.id;
    // Set Value to textfield - From selected line
    document.getElementById("textFieldDescriptionCostDetails").value = data.descriptionservice;
    document.getElementById("textFieldCommentsCostDetails").value = data.comments;
    document.getElementById("textFieldInvoiceCostDetails").value = data.supplierfk;
    // Set value to Rich text area
    $('#txtEditor').Editor('setText', data.services_notes); 

    $('html, body').animate({
        scrollTop: $("#OptionCodeDisplay").offset().top
    }, 2000);
}

function dateManipulation() {
    var time = document.getElementById('duration').value;
    // Hours, minutes and seconds
    var hrs = ~~(time / 3600);
    var mins = ~~((time % 3600) / 60);
    var secs = ~~time % 60;

    // Output like "1:01" or "4:03:59" or "123:03:59"
    var ret = "";

    if (hrs > 0) {
        ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
    }

    ret += "" + mins + ":" + (secs < 10 ? "0" : "");
    ret += "" + secs;
    return ret;
}

function displayTaxBasis() {
    var ele = document.getElementsByName('radioGroupTax');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            var check = ele[i].value;
    }
    return check;
}

function displayCostChildren() {
    var ele = document.getElementsByName('radioCostChildren');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            var check = ele[i].value;
    }
    return check;
}


function displayCostAdults() {
    var ele = document.getElementsByName('radioGroupAdult');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            var check = ele[i].value;
    }
    return check;
}

/////////////////////////////////////////
// model --> update form value to dB //////
/////////////////////////////////////////
$("#updateService").click(function () {
    var idCostDetails = document.getElementById('idBlock').innerHTML;
    var ddlChooseLocality = $('#ddlChooseLocality').val();
    var descriptionInvoiceCostDetails = document.getElementById('descriptionInvoiceCostDetails').value;
    var textFieldDescriptionCostDetails = document.getElementById('textFieldDescriptionCostDetails').value;
    var textFieldCommentsCostDetails = document.getElementById('textFieldCommentsCostDetails').value;
    var minChildren = document.getElementById('minChildren').value;
    var maxChildren = document.getElementById('maxChildren').value;
    var minAdults = document.getElementById('minAdults').value;
    var maxAdults = document.getElementById('maxAdults').value;
    var timeDuration = dateManipulation();
    var taxBasis = displayTaxBasis();
    var costChargedChildren = displayCostChildren();
    var costChargedAdults = displayCostAdults();

    var objUpdateService = {
        id: idCostDetails,
        locality_costdetails: ddlChooseLocality, //please make sure the names match in JS and PHP
        invoice_desciption_costdetails: descriptionInvoiceCostDetails,
        descriptionservice: textFieldDescriptionCostDetails,
        comments: textFieldCommentsCostDetails,
        duration_costdetails: timeDuration,
        taxbasis_costdetails: taxBasis,
        charged_unit_children_costdetails: costChargedChildren,
        min_children_costdetails: minChildren,
        max_children_costdetails: maxChildren,
        charged_unit_adults_costdetails: costChargedAdults,
        min_adults_costdetails: minAdults,
        max_adults_costdetails: maxAdults
    };

    const url_update_costDetails = "php/api/bckoffservices/savecostdetails.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_update_costDetails,
        method: "POST",
        data: objUpdateService,
        success: function (data) {
            console.log('value', data);
            resetFormUpdatedService();
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });    
});
// End click

function resetFormUpdatedService() {
    $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
    $('#updateService').attr('disabled', 'disabled');
    $('select option:contains("Select an option")').prop('selected',true);
    $('#textFieldDescriptionCostDetails').val('');
    $('#textFieldCommentsCostDetails').val('');
    $('#descriptionInvoiceCostDetails').val('');
    $('#minAdults').val('');
    $('#maxAdults').val('');
    $('#minChildren').val('');
    $('#maxChildren').val('');
}

    /////////////////////////////////////////
    // model --> Update Notes to dB /////////
    /////////////////////////////////////////
    $("#updateNotes").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        var txtEditorService = $(".Editor-editor").html();
        var objNotes = {
            id:idCostDetails,
            services_notes: txtEditorService
        };
        const url_update_notes = "php/api/bckoffservices/savenotes.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url: url_update_notes,
            method: "POST",
            data: objNotes,
            success: function (data) {
                console.log('value', data);
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function (error) {
                console.log('Error ${error}');
            }
        }); 
    }); 