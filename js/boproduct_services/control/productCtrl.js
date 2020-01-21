$(document).ready(function () {
    document.getElementById("aTitle").innerHTML = "";
    productCtrl();
   // $('#btn-saveProductServices').attr('disabled', 'disabled');
    $("#age_inf_from").prop("readonly", true);
    $("#age_inf_to").prop("readonly", true);
    $("#age_child_from").prop("readonly", true);
    $("#age_child_to").prop("readonly", true);
    $("#age_teen_from").prop("readonly", true);
    $("#age_teen_to").prop("readonly", true);
});

function productCtrl() {
    // Disabled button by default
    $('#btnSaveProduct').attr('disabled', 'disabled');
    $('#productName').keyup(function(){
        if($(this).val().length !=0)
            $('#btnSaveProduct').attr('disabled', false);            
        else
            $('#btnSaveProduct').attr('disabled',true);
    }); 
    
}

function onkeyupCtrl() {
    //var service_name = document.getElementById("service_name").value;
    var id_creditor = document.getElementById("id_creditor").value;
    
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    // var servicetype = urlParams.get("servicetype"); 
    // if (servicetype == 'ACTIVITY') {
    //     if(service_name.length > 0) {
    //         $('#btn-saveProductServices').attr('disabled', false); 
    //     }          
    //     else {
    //         $('#btn-saveProductServices').attr('disabled',true);
    //     }
    // } else if (servicetype == 'TRANSFER') {
    //     $('#btn-saveProductServices').attr('disabled', false);
    // }
}

$('#btn-saveProductServicesCost').click(function (e) {
    var isValid = true;
    $('#ps_adult_cost,#ps_teen_cost,#ps_child_cost,#ps_infant_cost, #daterangeServiceFromTo, #id_currency').each(function () {
        if ($.trim($(this).val()) == '') {
            isValid = false;
            $(this).css({
                "border": "1px solid red",
                "background": "#FFCECE"
            });
        } 
        else {
            $(this).css({
                "border": "",
                "background": ""
            });
        }


    });
    if (isValid == false)
        e.preventDefault();
});

$( "#ddlType" ).change(function () {
    $( "#ddlType option:selected" ).each(function() {
        service_name = $( this ).text();
        if (service_name == "Others" || service_name == "Transfer") {
            console.log('OK');
            $('#ddlProductType').val('4');
            $("#ddlProductType option[value='1']").hide();
            $("#ddlProductType option[value='2']").hide();
            $("#ddlProductType option[value='3']").hide();
        } else { 
            $("#ddlProductType option[value='1']").show();
            $("#ddlProductType option[value='2']").show();
            $("#ddlProductType option[value='3']").show();
            $('#ddlProductType').val('1');
        } 
    });
}).change();


$('#btn-saveProductServices').click(function () {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");

    var service_name = $('#service_name').val();
    var id_creditor = $('#id_creditor').val();
    var id_coast = $('#id_coast').val();
    var id_dept = $('#id_dept').val();
    var id_country = $('#id_country').val();
    var id_tax = $('#id_tax').val();
    var charge = $('#charge').val();
    
    var service_name_transfer = $('#service_name_transfer').val();
    var special_name_transfer = $('#special_name_transfer').val();

    if (servicetype == "ACTIVITY" || servicetype =="OTHER") { 
        if (charge == null || charge == "") {
            alert("Please choose a Charge Pax / Unit");
            document.getElementById('id_tax').style.border ="1px solid #aaa";
            document.getElementById('charge').style.border ="1px solid red";
        } else if (id_dept == null || id_dept == "") {
            alert("Please choose a Department");
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_dept').style.border ="1px solid red";
        } else if (id_country == null || id_country == "") {
            alert("Please choose a Location");
            document.getElementById('id_dept').style.border ="1px solid #aaa";
            document.getElementById('id_country').style.border ="1px solid red";
        }
        else if (service_name == "") {
            document.getElementById('service_name').style.border ="1px solid red";
            alert("Please add a service name");
        } else if (id_creditor == "") {
            alert("Please choose a supplier");
            document.getElementById('service_name').style.border ="1px solid #aaa";
            document.getElementById('id_creditor').style.border ="1px solid red";
        } else if (id_coast == null || id_coast == "") {
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_coast').style.border ="1px solid red";
            alert("Please choose a coast");
        } else if (id_tax == null || id_tax == "") {
            alert("Please choose Tax Option");
            document.getElementById('id_country').style.border ="1px solid #aaa";
            document.getElementById('id_tax').style.border ="1px solid red";
        } else if ($('.requiredChkDate:checked').length == 0) {
            alert("Please choose an operation date");
        }  else if ($('.requiredChkApplyFor:checked').length == 0) {
            alert("Please Apply atleast one type of Pax");
        } else {
            saveService();
        }
    } 
    
    if (servicetype == "TRANSFER") {
        var isVisible = document.getElementById("special_name_transfer").style.display == "block";
        if (charge == null || charge == "") {
            alert("Please choose a Charge Pax / Unit");
            document.getElementById('id_tax').style.border ="1px solid #aaa";
            document.getElementById('charge').style.border ="1px solid red";
        } else if (id_dept == null || id_dept == "") {
            alert("Please choose a Department");
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_dept').style.border ="1px solid red";
        } else if (id_country == null || id_country == "") {
            alert("Please choose a Location");
            document.getElementById('id_dept').style.border ="1px solid #aaa";
            document.getElementById('id_country').style.border ="1px solid red";
        }
        else if (service_name_transfer == null || service_name_transfer == "") {
            alert("Please select a service name");
            document.getElementById('service_name_transfer').style.border ="1px solid red";
        } else if (((special_name_transfer == null) || (special_name_transfer == "")) && (isVisible == true)) {
            alert("Please select a special name");
            document.getElementById('service_name_transfer').style.border ="1px solid #aaa";
            document.getElementById('service_name_transfer').style.border ="1px solid red";
        } else {
            saveService();
        }
    } 
}); 