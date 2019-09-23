//$("#txtDateStart").datepicker({dateFormat:'mm/dd/yy', minDate: new Date(2010,11,12) });
$('#btn-saveProductServicesCost').click(function () {
    var valid_from = $('#valid_from').val();
    var valid_to = $('#valid_to').val();
    var ps_adult_cost = $('#ps_adult_cost').val();
    var ps_teen_cost = $('#ps_teen_cost').val();
    var ps_child_cost = $('#ps_child_cost').val();
    var ps_infant_cost = $('#ps_infant_cost').val();
    var id_currency = $('#id_currency').val();

    var objServiceCost = {
        id_product_services_cost:-1, //for new items, id is always -1
        valid_from: valid_from,
        valid_to : valid_to,
        ps_adult_cost: ps_adult_cost,
        ps_teen_cost: ps_teen_cost,
        ps_child_cost: ps_child_cost,
        ps_infant_cost: ps_infant_cost,
        id_currency: id_currency
    };
    const url_save_service_cost = "php/api/backofficeproduct/saveservicecost.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_service_cost,
        method : "POST",
        data : objServiceCost,                                                                                                                                                                                                                                                                                                                                                                                                                                              
        success : function(data){
            console.log('value', data);
            resetFormAddServiceCost();
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
});

// Function Reset Form Add New Service
function resetFormAddServiceCost() {
    $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
    $('#valid_from').val('');
    $('#valid_to').val('');
    $('#ps_adult_cost').val('');
    $('#ps_teen_cost').val('');
    $('#ps_child_cost').val('');
    $('#ps_infant_cost').val('');
    $('#id_currency').val('');
}