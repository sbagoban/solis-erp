/////////////////////////////////////////
// model --> update form value to dB //////
/////////////////////////////////////////
$("#updateService").click(function () {
    var ddlChooseLocality = $('#ddlChooseLocality').val();
    var ddlChooseDept = $('#ddlChooseDept').val();
    var textFieldDescriptionCostDetails = document.getElementById('textFieldDescriptionCostDetails').value;
    var textFieldCommentsCostDetails = document.getElementById('textFieldCommentsCostDetails').value;
    var descriptionInvoiceCostDetails = document.getElementById('descriptionInvoiceCostDetails').value;

    var objUpdateService = {
        countryfk: ddlLocationSelected, //please make sure the names match in JS and PHP
        servicetypefk: ddlServiceTypeSelected,
        supplierfk: ddlSupplierSelected
    };

    const url_update_service = "php/api/bckoffservices/bckoffservices/savecostdetails.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_update_service,
        method: "POST",
        data: objUpdateService,
        success: function (data) {
            console.log('value', data);
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
});
// End click