$(document).ready(function () {
    document.getElementById("aTitle").innerHTML = "";
});

$('#btn-saveServicesClaim').click(function (e) {
    var isValid = true;
    $('#daterangeServiceFromTo,#ps_adult_claim,#id_currency,#specific_to,#ddlMultiSpecificTo').each(function () {
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
    if (isValid == false) {
        e.preventDefault();
    }

    // Control for claim form
    
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var ps_adult_cost_url = urlParams.get("ps_adult_cost");
    var ps_teen_cost_url = urlParams.get("ps_teen_cost");
    var ps_child_cost_url = urlParams.get("ps_child_cost");
    var ps_infant_cost_url = urlParams.get("ps_infant_cost");

    var ps_adult_claim = $('#ps_adult_claim').val();
    var ps_teen_claim = $('#ps_teen_claim').val();
    var ps_child_claim = $('#ps_child_claim').val();
    var ps_infant_claim = $('#ps_infant_claim').val();

    if ((ps_adult_cost_url > 0) && (ps_adult_claim == '' || ps_adult_claim == null)) {
        alert("Please enter claim for adult");
    } else if ((ps_teen_cost_url > 0) && (ps_teen_claim == '' || ps_teen_claim == null)) {
        alert("Please enter claim for teen");
    } else if ((ps_child_cost_url > 0) && (ps_child_claim == '' || ps_child_claim == null)) {
        alert("Please enter claim for child");
    } else if ((ps_infant_cost_url > 0) && (ps_infant_claim == '' || ps_infant_claim == null)) {
        alert("Please enter claim for infant");
    } else {
        saveClaim();
    }
});

