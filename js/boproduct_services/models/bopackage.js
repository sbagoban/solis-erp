$(document).ready(function(){
    var idService = document.getElementById("idService").innerHTML;
    serviceCost(idService, '0', '0');
});

$('#is_pakage').on('change', function() {
    var idService = document.getElementById("idService").innerHTML;
    if ( this.value == 'Y' ) { 
        $('#services_block').css("display", "block");
        var allDate =  $("#daterangeServiceFromTo1").val();
        var allDate_split = allDate.split("-");
        var date_from = allDate_split[0];
        var date_to = allDate_split[1];
        var date_1 = date_from.split("/");
        var date_from_y = date_1[2];
        var date_from_m = date_1[1];
        var date_from_d = date_1[0];
        var valid_from = date_from_y+"-"+date_from_m+"-"+date_from_d;
        var date_2 = date_to.split("/");
        var date_to_y = date_2[2];
        var date_to_m = date_2[1];
        var date_to_d = date_2[0];
        var valid_to = date_to_y+"-"+date_to_m+"-"+date_to_d;
        
        serviceCost(idService, valid_from.replace(/\s/g, ''), valid_to.replace(/\s/g, ''));
    } else { 
        $('#services_block').css("display", "none");
    }
});

function serviceCost(idService, valid_from, valid_to) {
    //$("#services_cost").multiselect('destroy');
    const url_service_cost = "php/api/backofficeproduct/selectservicecostpackage.php?t=" + encodeURIComponent(global_token)+ "&id_product_service=" + idService + "&valid_from=" + valid_from+ "&valid_to=" + valid_to;
    $.ajax({
        url: url_service_cost,
        method: "POST",
        dataType: "json",
        success: function (data)
        {
            $("#services_cost").multiselect('destroy');
            $("#services_cost").attr('multiple', 'multiple');
            $.each(data, function (key, val) {
                $("#services_cost").append('<option value="' + val.id_product_service + '">'+ val.product_name + ' / ' + val.service_name +'</option>');
                $("#services_cost").multiselect('destroy');
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
