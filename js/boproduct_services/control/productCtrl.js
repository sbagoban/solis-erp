$(document).ready(function () {
    productCtrl();
    $('#btn-saveProductServices').attr('disabled', 'disabled');
    $("#age_inf_from").prop("readonly", true);
    $("#age_inf_to").prop("readonly", true);
    $("#age_child_from").prop("readonly", true);
    $("#age_child_to").prop("readonly", true);
    $("#age_teen_from").prop("readonly", true);
    $("#age_teen_to").prop("readonly", true);

    applyFor();
});

function applyFor() {
    var chkinfant = document.getElementById("for_infant");
    var chkchild = document.getElementById("for_child");
    var chkteen = document.getElementById("for_teen");

    $('input').on('click',function () {
        if (chkinfant.checked) {
            $("#age_inf_from").prop("readonly", false);
            $("#age_inf_to").prop("readonly", false);
        } if (chkinfant.checked == false) {
            $("#age_inf_from").prop("readonly", true);
            $("#age_inf_to").prop("readonly", true);
        }

        if (chkchild.checked) {
            $("#age_child_from").prop("readonly", false);
            $("#age_child_to").prop("readonly", false);
        } if (chkchild.checked == false) {
            $("#age_child_from").prop("readonly", true);
            $("#age_child_to").prop("readonly", true);
        }

        if (chkteen.checked) {
            $("#age_teen_from").prop("readonly", false);
            $("#age_teen_to").prop("readonly", false);
        } if (chkteen.checked == false) {
            $("#age_teen_from").prop("readonly", true);
            $("#age_teen_to").prop("readonly", true);
        }
    });
}


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
    var min_pax = document.getElementById("min_pax").value;
    var max_pax = document.getElementById("max_pax").value;
    
    if(service_name.length > 0 && min_pax.length >0 && max_pax.length >0) {
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
