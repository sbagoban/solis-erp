$(document).ready(function () {
    productCtrl();
   // $('#btn-saveProductServices').attr('disabled', 'disabled');
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
    var chkadult = document.getElementById("for_adult");

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

        if (chkadult.checked && chkteen.checked == false && chkchild.checked == false && chkinfant.checked == false) {
            $('#min_age').css("border", "2px solid orange");
            $('#max_age').css("border", "2px solid orange");

            var minage = Number ($('#min_age').val());
            var maxage = Number ($('#max_age').val());
            if (minage == '' || maxage == '') {
                $('#btn-saveProductServices').attr('disabled', true); 
            }
        }
        if (chkadult.checked == false || chkteen.checked || chkchild.checked || chkinfant.checked) {
            $('#min_age').css("border", "1px solid black");
            $('#max_age').css("border", "1px solid black");
        }
    });
}

$('#max_age').change(function(){
    var minage = Number ($('#min_age').val());
    var maxage = Number ($('#max_age').val());
    if (minage > maxage){        
        $('#max_age').css("border", "2px solid orange");
        alert ('Choose a number greater than ' + minage );
    }
});

$('#min_age').change(function(){
    var minage = Number ($('#min_age').val());
    if (minage != ''){
        $('#btn-saveProductServices').attr('disabled', false); 
    }
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
    // if (servicetype == 'EXCURSION') {
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
