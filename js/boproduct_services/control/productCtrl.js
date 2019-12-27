$(document).ready(function () {
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
    })
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
