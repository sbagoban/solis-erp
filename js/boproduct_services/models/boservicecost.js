$(document).ready(function(){
    var allParams = window.location.href.split('productservicescost').pop();
    const urlParams = new URLSearchParams(allParams);    
    var charges = urlParams.get("charges"); 
    console.log(charges);
    if (charges == 'UNIT') {
        $("#ps_teen_cost").css("display", "none");        
        $("#ps_child_cost").css("display", "none");
        $("#ps_infant_cost").css("display", "none");
        
        $("#ps_teen_cost_addon").css("display", "none");        
        $("#ps_child_cost_addon").css("display", "none");
        $("#ps_infant_cost_addon").css("display", "none");
    }
});

//$("#txtDateStart").datepicker({dateFormat:'mm/dd/yy', minDate: new Date(2010,11,12) });
    $('#btn-saveProductServicesCost').click(function () {
        var allParams = window.location.href.split('productservicescost').pop();
        const urlParams = new URLSearchParams(allParams);

        var id_dept = urlParams.get("iddept"); 
        var id_product_services = urlParams.get("psid"); 
        
        var valid_from = $('#valid_from').val();
        var valid_to = $('#valid_to').val();
        var ps_adult_cost = $('#ps_adult_cost').val();
        var ps_teen_cost = $('#ps_teen_cost').val();
        var ps_child_cost = $('#ps_child_cost').val();
        var ps_infant_cost = $('#ps_infant_cost').val();
        var id_currency = $('#id_currency').val();
        var id_product_services_cost = document.getElementById("id_product_services_cost_1").innerHTML;

        if (id_product_services_cost != 0) {
            var objServiceCostEdit = {
                id_product_services: id_product_services,
                valid_from: valid_from,
                valid_to : valid_to,
                ps_adult_cost: ps_adult_cost,
                ps_teen_cost: ps_teen_cost,
                ps_child_cost: ps_child_cost,
                ps_infant_cost: ps_infant_cost,
                id_currency: id_currency,
                id_dept: id_dept
            };
            const url_edit_service_cost = "php/api/backofficeproduct/updateservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_services_cost=" + id_product_services_cost;
            $.ajax({
                url : url_edit_service_cost,
                method : "POST",
                data : objServiceCostEdit,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    console.log('value', data);
                    resetFormAddServiceCost();
                    allServicesGridCost();
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        } else {
            var objServiceCost = {
                id_product_services_cost:-1, //for new items, id is always -1
                id_product_services: id_product_services,
                valid_from: valid_from,
                valid_to : valid_to,
                ps_adult_cost: ps_adult_cost,
                ps_teen_cost: ps_teen_cost,
                ps_child_cost: ps_child_cost,
                ps_infant_cost: ps_infant_cost,
                id_currency: id_currency,
                id_dept: id_dept
            };
            const url_save_service_cost = "php/api/backofficeproduct/saveservicecost.php?t=" + encodeURIComponent(global_token);
            $.ajax({
                url : url_save_service_cost,
                method : "POST",
                data : objServiceCost,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    console.log('value', data);
                    resetFormAddServiceCost();
                    allServicesGridCost();
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        }
        document.getElementById("id_product_services_cost_1").innerHTML = 0;
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