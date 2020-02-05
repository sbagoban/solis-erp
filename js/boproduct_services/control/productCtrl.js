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

    $('#closure_date').daterangepicker({
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		locale: {
					format: 'DD/MM/YYYY'
				}
    });

    // Validation For On approved an on_api
    var on_approved_chk = $('#on_approved').prop('checked');
    var on_api_chk = $('#on_api').prop('checked');

    $('#on_approved').on('change', function() {
        var liveOnChange = $(this).prop('checked');
        if(liveOnChange == true) {
            $('#on_api').removeAttr("disabled");
            $('.toggle').removeAttr("disabled");
            $(".toggle:eq(1)").removeClass("add_disabled");
        } else {
            $('.toggle').prop('disabled', "disabled");
            $('#on_api').prop("disabled", true);
            $('.toggle:eq(1)').addClass('btn-default off').removeClass('btn-success');
            $('#on_api').prop('checked', false);
            $(".toggle:eq(1)").addClass("add_disabled");
        }
    });
});

function productCtrl() {
    // Disabled button by default
    $('#btnSaveProduct').attr('disabled', 'disabled');
    $('#productName').change(function(){
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
    var is_pakage = $('#is_pakage').val();
    var countPackage = $("#services_cost :selected").length;
    var service_name_transfer = $('#service_name_transfer').val();
    var special_name_transfer = $('#special_name_transfer').val();

    if (servicetype == "ACTIVITY" || servicetype =="OTHER") { 
        if (is_pakage == "Y" && countPackage <= 1) {
            alert("Please Choose atleast two services");
        } else if (charge == null || charge == "") {
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
            alert("Please choose an operation day");
        }  else if ($('.requiredChkApplyFor:checked').length == 0) {
            alert("Please Apply atleast one type of Pax");
        } else if ($('.requiredChkApplyFor:checked').length > 0) {
            validateAgePolicy();
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

function validateAgePolicy() {
    // var chkinfant = document.getElementById("for_infant");
    // var chkchild = document.getElementById("for_child");
    // var chkteen = document.getElementById("for_teen");
    // var chkadult = document.getElementById("for_adult");

    // var age_inf_from = document.getElementById("age_inf_from");
    // var age_inf_to = document.getElementById("age_inf_to");

    // if (chkinfant.checked) {
    //     if (age_inf_from == " " || age_inf_from == null) {
    //         $('#age_inf_from').style.border ="1px solid red";
    //         alert("Please insert Min Number of infant");
    //     } else if (age_inf_to == "" || age_inf_to == null) {
    //         $('#age_inf_from').style.border ="1px solid #aaa";
    //         $('#age_inf_to').style.border ="1px solid red";
    //         alert("Please insert Max Number of infant");
    //     }
    // } if (chkchild.checked) {
    //     alert('2');
    // } if (chkteen.checked) {
    //     alert('3');
    // } if (chkadult.checked) {
    //     alert('4');
    // }
        
    saveService();
}