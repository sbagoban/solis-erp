// Edit Extra Service
function editAllExtraServiceClaim(data) {
    if (data.id_product_service_extra_claim > 0) { 
        $("#id_product_service_extra").prop("disabled", true);
        $("#btn-saveProductServicesExtraClaim").css("display", "none");
        $("#btn-updateProductServicesExtraClaim").css("display", "block");
        $("#id_product_service_extra").val(data.id_product_service_extra_cost);

        if (data.charge == 'UNIT'){
            $("#ps_adult_claim_1").val(data.ps_adult_claim);
            $(".blockPax").hide();
            $(".blockUnit").show();
        } else {
            $("#ps_adult_claim_1").val(data.ps_adult_claim);
            $("#ps_teen_claim_1").val(data.ps_teen_claim);
            $("#ps_child_claim_1").val(data.ps_child_claim);
            $("#ps_infant_claim_1").val(data.ps_infant_claim);
            $(".blockPax").show();
            $(".blockUnit").hide();
        }
    }
    $('#modal-extraServicesClaim').on('hidden.bs.modal', function() {
        $(this).find("input,select").val('').end();
    });

    // Update - Product Service Extra
    $('#btn-updateProductServicesExtraClaim').click(function () {
        var ps_adult_claim_1 = $('#ps_adult_claim_1').val();
        var ps_teen_claim_1 = $('#ps_teen_claim_1').val();
        var ps_child_claim_1 = $('#ps_child_claim_1').val();
        var ps_infant_claim_1 = $('#ps_infant_claim_1').val();

        var extra_name = $('#id_product_service_extra').find(":selected").text();

        if (extra_name != 'Select an option') {
            var objExtraServiceEditClaim = {
                ps_adult_claim: ps_adult_claim_1, 
                ps_teen_claim: ps_teen_claim_1, 
                ps_child_claim: ps_child_claim_1, 
                ps_infant_claim: ps_infant_claim_1
            };

            const url_update_extra_service_claim = "php/api/backofficeserviceclaim/updateextraclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_extra_claim=" + data.id_product_service_extra_claim;
            $.ajax({
                url : url_update_extra_service_claim,
                method : "POST",
                data : objExtraServiceEditClaim,  
                cache: false,                                                                                                                                                                                                                                                                                                                                                                                                                                            
                success : function(data){
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
            
            extraServiceGridClaim(data);
        }
    });
}