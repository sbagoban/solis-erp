$('#is_pakage').on('change', function() {
    if ( this.value == 'Y' ) {        
        $('#services_block').css("display", "block");
        serviceCost();
    } else { 
        $('#services_block').css("display", "none");
    }
});

function serviceCost() {
    // $('#services_cost').selectpicker();
    const url_service_cost = "php/api/backofficeproduct/selectservicecostpackage.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_service_cost,
        method: "POST",
        dataType: "json",
        success: function (data)       
        {
            $("#services_cost").empty();
            $.each(data, function (key, val) {
                $("#services_cost").append('<option value="' + val.id_product_service_cost + '">'+ val.product_name + ' / ' + val.service_name +'</option>');
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
