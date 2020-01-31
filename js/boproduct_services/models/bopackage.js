$(document).ready(function(){
    var idService = document.getElementById("idService").innerHTML;
    serviceCost(idService);      
});

$('#is_pakage').on('change', function() {
    
    var idService = document.getElementById("idService").innerHTML;
    if ( this.value == 'Y' ) { 
        serviceCost(idService);      
        $('#services_block').css("display", "block");
    } else { 
        $('#services_block').css("display", "none");
    }
});

function serviceCost(idService) {
    const url_service_cost = "php/api/backofficeproduct/selectservicecostpackage.php?t=" + encodeURIComponent(global_token)+ "&id_product_service=" + idService;
    $.ajax({
        url: url_service_cost,
        method: "POST",
        dataType: "json",
        success: function (data)       
        {
            $("#services_cost").attr('multiple', 'multiple');
            $("#services_cost").empty();
            $.each(data, function (key, val) {
                console.log(key, val.id_product_service);
                $("#services_cost").append('<option value="' + val.id_product_service + '">'+ val.product_name + ' / ' + val.service_name +'</option>');
            });
            $("#services_cost").attr('multiple', 'multiple'); 
            $("#services_cost").multiselect({
                buttonWidth: '295px',
                includeSelectAllOption: true,
                nonSelectedText: 'Select an Option',
                enableFiltering: true,
                enableHTML: true,
                buttonClass: 'btn large btn-default',
                enableCaseInsensitiveFiltering: true
            });
        },
        error: function (error) 
        {
            console.log('Error ${error}');
        }
    });
}
