function addExtraServiceCost(data) {
console.log(data);
    $("#charges_1").on('change', function() {
        if ($(this).val() == 'UNIT'){
            $("#blockPax").css("display", "none");
        $("#blockUnit").css("display", "block");
        } else {
            $("#blockPax").css("display", "block");
            $("#blockUnit").css("display", "none");
        }
    });

    $('#btn-saveProductServicesExtraCost').click(function () {
        var ps_adult_cost = $('#ps_adult_cost_ex').val();
        var ps_teen_cost = $('#ps_teen_cost_ex').val();
        var ps_child_cost = $('#ps_child_cost_ex').val();
        var ps_infant_cost = $('#ps_infant_cost_ex').val();
        var id_product_services_extra = $('#id_product_services_extra_1').val();
        var extra_name = $('#id_product_services_extra_1').find(":selected").text();
        var id_product_services_cost = document.getElementById("id_product_services_cost_extra").innerHTML;
        var id_product_services = data.id_product_services;
        var valid_from = data.valid_from;
        var valid_to = data.valid_to;
        var charges = $('#charges_1').val();
        var currency = data.currency_code;
        var id_currency = data.id_currency;

        console.log('id_product_services_extra', id_product_services_extra);
        var objExtraServiceCost = {
            id_product_services_extra_cost:-1, //for new items, id is always -1
            id_product_services_extra: id_product_services_extra,
            id_product_services_cost: id_product_services_cost,
            id_product_services: id_product_services,
            valid_from: valid_from,
            valid_to : valid_to,
            ps_adult_cost: ps_adult_cost,
            ps_teen_cost: ps_teen_cost,
            ps_child_cost: ps_child_cost,
            ps_infant_cost: ps_infant_cost,
            charges: charges,
            id_currency: id_currency, 
            currency: currency,
            extra_name: extra_name
        };
        const url_save_extra_service_cost = "php/api/backofficeproduct/saveextraservicecost.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_extra_service_cost,
            method : "POST",
            data : objExtraServiceCost,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
                console.log('value', data);
                resetExtraServicesCostForm();
                allExtraServicesCostGrid(id_product_services_cost);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    });
}

function resetExtraServicesCostForm() {
    $('#ps_adult_cost_ex').val('');
    $('#ps_teen_cost_ex').val('');
    $('#ps_child_cost_ex').val('');
    $('#ps_infant_cost_ex').val('');
    $('#id_product_services_extra').val('');
}
