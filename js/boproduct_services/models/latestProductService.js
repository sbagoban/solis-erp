$(document).ready(function(){
    $('#loadingmessage').show();
    setInterval(function(){
        const url_edit_product = "php/api/backofficeproduct/latestProductService.php?t=" + encodeURIComponent(global_token);
        
        $.ajax({
            url: url_edit_product,
            dataType: 'json',
            success: function(data) {
                var html = '';
                $.each(data, function(key, value){
                    if (data[key].is_pakage == 'N') {
                        var package_1 = 'No';
                    } else {
                        var package_1 = 'Yes';
                    }
                    $('#loadingmessage').hide();            
                    html += '<div class="panel panel-primary">';
                    html += '<div class="panel-heading">Product Name : <span id="product_name_latest">&nbsp;'+ value.product_name +'</span></div>';
                            html += '<div class="panel panel-theme">';
                                html += '<div class="panel-body">';
                                    html += '<div class="col-md-8">';
                                        html += '<label>Product Type : </label>&nbsp;' + value.servicetype+'<br>';
                                        html += '<label>Service Name : </label>&nbsp;' + value.service_name+'<br>';
                                        html += '<label>Package : </label>&nbsp;' + package_1 +'<br>';
                                    html += '</div>';
                                    html += '<div class="col-md-4">';
                                        html += data[key].servicetype == 'ACTIVITY' ? '<i class="fa fa-tree" aria-hidden="true" style="font-size: 72px; color: #eaeaea"></i>' : '<i class="fa fa-car" aria-hidden="true" style="font-size: 72px; color: #eaeaea"></i>';
                                    html += '</div>';
                                html += '</div>';
                            html += '</div>';
                    html += '</div>';


                });
            $('#parent_latest').html(html);

            }
        });
    }, 5000);
});
