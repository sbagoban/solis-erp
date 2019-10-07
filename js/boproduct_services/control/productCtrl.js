$(document).ready(function () {
    productCtrl();
    $('#btn-saveProductServices').attr('disabled', 'disabled');
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
    var service_name = document.getElementById("service_name").value;
    var id_creditor = document.getElementById("id_creditor").value;
    var duration = document.getElementById("duration").value;
    var min_pax = document.getElementById("min_pax").value;
    var max_pax = document.getElementById("max_pax").value;
    
    if(service_name.length > 0 && min_pax.length >0 && max_pax.length >0 && duration.length >0) {
        $('#btn-saveProductServices').attr('disabled', false); 
    }          
    else {
        $('#btn-saveProductServices').attr('disabled',true);
    }
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
