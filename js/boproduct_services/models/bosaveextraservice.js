function modalExtraService(data) { 
    var id_product_service = data.id_product_service;
    allExtraServicesGrid(id_product_service);
    $('#btnAddExtraService').click(function () {
        var id_service_extra = $('#extra_name').val();
        var extra_name = $('#extra_name').find(":selected").text();
        var extra_description = $('#extra_description').val();
        var charge = $('#chargeExtra').val();
        var id_product_service = data.id_product_service;
        // Save Extra Service
        var objExtraService = {
            id_product_service_extra :-1, //for new items, id is always -1
            id_service_extra : id_service_extra,
            extra_name : extra_name,
            extra_description : extra_description,
            id_product_service : id_product_service,
            charge: charge
        };
        const url_save_extra_service = "php/api/backofficeproduct/saveextraservice.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_extra_service,
            method : "POST",
            data : objExtraService,                                                                                                                                                                                                                                                                                                                                                                                                                
            success : function(data){
                console.log('value', data);
                resetExtraServicesForm();
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
        allExtraServicesGrid(id_product_service);
    });
}

function resetExtraServicesForm() {
    $('#extra_name').val('');
    $('#extra_description').val('');
    $('#charge').val('');
}