function addExtraServiceCost(data) {
    $('#modal-extraServices').modal('show');
    $("#btn-saveProductServicesExtraCost").css("display", "block");
    $("#btn-updateProductServicesExtraCost").css("display", "none");
    // $("#charge_1").on('change', function() {
    //     if ($(this).val() == 'UNIT'){
    //         $(".blockPax").hide();
	// 		$(".blockUnit").show();
    //     } else {
    //         $(".blockPax").show();
    //         $(".blockUnit").hide();
    //     }
    // });

    $('#btn-saveProductServicesExtraCost').click(function () {
        var ps_adult_cost = $('#ps_adult_cost_ex').val();
        var ps_teen_cost = $('#ps_teen_cost_ex').val();
        var ps_child_cost = $('#ps_child_cost_ex').val();
        var ps_infant_cost = $('#ps_infant_cost_ex').val();
        var id_product_service_extra = $('#id_product_service_extra_1').val();
        var extra_name = $('#id_product_service_extra_1').find(":selected").text();
        var id_product_service_cost = document.getElementById("id_product_service_cost_extra").innerHTML;
        var id_product_service = data.id_product_service;
        var valid_from = data.valid_from;
        var valid_to = data.valid_to;
        var charge = $('#charge_1').val();
        var currency = data.currency_code;
        var id_currency = data.id_currency;
        if (extra_name != 'Select an option') {
            var objExtraServiceCost = {
                id_product_service_extra_cost:-1, //for new items, id is always -1
                id_product_service_extra: id_product_service_extra,

                id_product_service_cost: id_product_service_cost,
                id_product_service: id_product_service,
                valid_from: valid_from,
                valid_to : valid_to,
                ps_adult_cost: ps_adult_cost,
                ps_teen_cost: ps_teen_cost,
                ps_child_cost: ps_child_cost,
                ps_infant_cost: ps_infant_cost,
                charge: charge,
                id_currency: id_currency, 
                currency: currency,
                extra_name: extra_name
            };
            const url_save_extra_service_cost = "php/api/backofficeproduct/saveextraservicecost.php?t=" + encodeURIComponent(global_token);
            $.ajax({
                url : url_save_extra_service_cost,
                method : "POST",
                data : objExtraServiceCost,  
                cache: false,                                                                                                                                                                                                                                                                                                                                                                                                                                            
                success : function(data){
                    resetExtraServicesCostForm();
                    allExtraServicesCostGrid(id_product_service_cost);
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
            resetExtraServicesCostForm();
        }
    });
}

function resetExtraServicesCostForm() {
    $('#ps_adult_cost_ex').val('').end();
    $('#ps_teen_cost_ex').val('').end();
    $('#ps_child_cost_ex').val('').end();
    $('#ps_infant_cost_ex').val('').end();
    // $('#charge_1').val('').end();
    $('#id_product_service_extra_1').val('').end();
}
