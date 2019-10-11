
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
    if (isValid == false)
        e.preventDefault();
});

