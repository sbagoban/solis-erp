// Edit Extra Service
function editAllExtraServiceCost(data) {
    if (data.id_product_service_extra_cost > 0) { 
        $("#btn-saveProductServicesExtraCost").css("display", "none");
        $("#btn-updateProductServicesExtraCost").css("display", "block");
        $("#id_product_service_extra_1").val(data.id_product_service_extra);
        $("#charge_1").val(data.charge);
        var chargePer = document.getElementById('charge_1').value;

        if (chargePer == 'UNIT'){
            $(".blockPax").hide();
            $(".blockUnit").show();
            $("#ps_adult_cost_ex").val(data.ps_adult_cost);
        } else {
            $(".blockPax").show();
            $(".blockUnit").hide();
            $("#ps_adult_cost_ex").val(data.ps_adult_cost);
            $("#ps_child_cost_ex").val(data.ps_child_cost);
            $("#ps_infant_cost_ex").val(data.ps_infant_cost);
            $("#ps_teen_cost_ex").val(data.ps_teen_cost);
        }
    }
    // $("#charge_1").on('change', function() {
    //     if ($(this).val() == 'UNIT'){
    //         $(".blockPax").hide();
	// 		$(".blockUnit").show();
    //     } else {
    //         $(".blockPax").show();
    //         $(".blockUnit").hide();
    //     }
    // });
    $('#modal-extraServices').on('hidden.bs.modal', function() {
        $(this).find("input,select").val('').end();
    });

    // Update - Product Service Extra
    $('#btn-updateProductServicesExtraCost').click(function () {
        var ps_adult_cost = $('#ps_adult_cost_ex').val();
        var ps_teen_cost = $('#ps_teen_cost_ex').val();
        var ps_child_cost = $('#ps_child_cost_ex').val();
        var ps_infant_cost = $('#ps_infant_cost_ex').val();
        var extra_name = $('#id_product_service_extra_1').find(":selected").text();
        var id_product_service_cost = document.getElementById("id_product_service_cost_extra").innerHTML;
        var charge = $('#charge_1').val();


        if (extra_name != 'Select an option') {
            var objExtraServiceEditCost = {
                ps_adult_cost: ps_adult_cost,
                ps_teen_cost: ps_teen_cost,
                ps_child_cost: ps_child_cost,
                ps_infant_cost: ps_infant_cost,
                charge: charge,
                extra_name: extra_name
            };
            const url_update_extra_service_cost = "php/api/backofficeproduct/updateextraservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_service_extra_cost=" + data.id_product_service_extra_cost;
            $.ajax({
                url : url_update_extra_service_cost,
                method : "POST",
                data : objExtraServiceEditCost,  
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