function modalExtraService(data) { 
    var id_product_services = data.id_product_services;
    allExtraServicesGrid(id_product_services);
    $('#btnAddExtraService').click(function () {
        var id_services_extra = $('#extra_name').val();
        var extra_name = $('#extra_name').find(":selected").text();
        var extra_description = $('#extra_description').val();
        var charges = $('#chargesExtra').val();
        var id_product_services = data.id_product_services;
console.log(charges, 'ddsfgsdfgddgdg');
        // Save Extra Service
        var objExtraService = {
            id_product_services_extra :-1, //for new items, id is always -1
            id_services_extra : id_services_extra,
            extra_name : extra_name,
            extra_description : extra_description,
            id_product_services : id_product_services,
            charges: charges
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
        allExtraServicesGrid(id_product_services);
    });
}

function resetExtraServicesForm() {
    $('#extra_name').val('');
    $('#extra_description').val('');
    $('#charges').val('');
}