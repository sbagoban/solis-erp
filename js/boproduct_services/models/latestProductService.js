$(document).ready(function(){
    setInterval(function(){
        const url_edit_product = "php/api/backofficeproduct/latestProductService.php?t=" + encodeURIComponent(global_token);

        $.ajax({
            url: url_edit_product,
            dataType: 'json',
            success: function(data) {
                var html = '';
                $.each(data, function(key, value){
                    if (data.is_pakage == 'N') {
                        var package_1 = 'yes';
                    } else {
                        var package_1 = 'No';
                    }
                    html += '<div class="panel-heading">Product Name : <span id="product_name_latest">&nbsp;'+ value.product_name +'</span></div>';
                        html += '<div class="panel-body">';
                            html += '<label>Product Type : </label>&nbsp;' + value.servicetype+'<br>';
                            html += '<label>Service Name : </label>&nbsp;' + value.service_name+'<br>';
                            html += '<label>Package : </label>&nbsp;' + package_1 +'<br>';
                        html += '</div>';
                });
            $('#parent_latest').html(html);

            }
        });
    }, 5000);
});
