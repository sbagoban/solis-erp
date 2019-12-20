$("#btnSaveProduct").click(function () {
    var id_product = document.getElementById("productId").textContent;
    var ddlProductTypeSelected = $('#ddlProductType').val();
    var ddlTypeSelected = $('#ddlType').val();
    var productNameSelected = document.getElementById('productName').value;
        if (id_product != 0) {
            const url_edit_product = "php/api/backofficeproduct/updateproduct.php?t=" + encodeURIComponent(global_token) + "&id_product=" + id_product;
            var objProductUpdate = {
                id_product_type: ddlProductTypeSelected,
                id_service_type : ddlTypeSelected,
                product_name: productNameSelected
            };
            $.ajax({
                url : url_edit_product,
                method : "POST",
                data : objProductUpdate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    console.log('value', data);
                    allProductGridCost(data);
                    resetFormAddProduct();
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        } else {
            var objProduct = {
                id_product:-1, //for new items, id is always -1
                id_product_type: ddlProductTypeSelected,
                id_service_type : ddlTypeSelected,
                product_name: productNameSelected,
                active: 1
            };
            const url_save_product = "php/api/backofficeproduct/savenewproduct.php?t=" + encodeURIComponent(global_token);
            $.ajax({
                url : url_save_product,
                method : "POST",
                data : objProduct,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    allProductGridCost(data);
                    resetFormAddProduct();
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
            document.getElementById("productId").innerHTML = 0;
        }    
        document.getElementById("productId").innerHTML = 0;
});
// End click

// Function Reset Form Add New Service
function resetFormAddProduct() {
    $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
    $('#productName').val('');
    $("#ddlType").val("Choose An Option");
    $("#ddlProductType").val("Choose An Option");
}