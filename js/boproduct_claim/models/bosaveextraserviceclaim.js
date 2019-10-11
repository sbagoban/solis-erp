function addExtraServiceClaim(data) {
    $('#btn-saveProductServicesExtraCost').click(function() {
        var id_product_service_extra_cost  = document.getElementById("id_product_service_extra_cost").innerHTML;
        var charge = document.getElementById("product_service_claim_charge").innerHTML;
        var id_product_service_claim = data.id_product_service_claim;
        var id_product_service_cost = data.id_product_service_cost;
        var id_product_service = data.id_product_service;
        var valid_from = data.valid_from;
        var valid_to = data.valid_to;
        var id_dept = data.id_dept;
        var specific_to = data.specific_to;
        var ps_adult_claim  = $('#ps_adult_claim_1').val();
        var ps_teen_claim  = $('#ps_teen_claim_1').val();
        var ps_child_claim  = $('#ps_child_claim_1').val();
        var ps_infant_claim  = $('#ps_infant_claim_1').val();
        var id_currency = data.id_currency;
        var currency = data.currency;
    
        const url_save_extraservice_claim = "php/api/backofficeserviceclaim/saveextraserviceclaim.php?t=" + encodeURIComponent(global_token);
        var objExtraServiceClaim = {
            id_product_service_extra_claim: -1,
            id_product_service_claim: id_product_service_claim, 
            id_product_service_cost:id_product_service_cost, 
            id_product_service: id_product_service,
            valid_from: valid_from, 
            valid_to: valid_to, 
            id_dept: id_dept, 
            specific_to: specific_to, 
            ps_adult_claim: ps_adult_claim, 
            ps_teen_claim: ps_teen_claim, 
            ps_child_claim: ps_child_claim, 
            ps_infant_claim: ps_infant_claim, 
            id_currency: id_currency, 
            currency: currency, 
            id_product_service_extra_cost: id_product_service_extra_cost, 
            charge: charge
        };
    
        $.ajax({
            url : url_save_extraservice_claim,
            method : "POST",
            data : objExtraServiceClaim,                                                                                                                                                                                                                                                                                                                                                                                                                
            success : function(data){
                resetExtraServicesClaim();
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
        extraServiceGridClaim(data);
    });
}

function resetExtraServicesClaim() {
    $('#id_product_service_extra').val('');
    $('#ps_adult_claim_1').val('');
    $('#ps_teen_claim_1').val('');
    $('#ps_child_claim_1').val('');
    $('#ps_infant_claim_1').val('');
}
