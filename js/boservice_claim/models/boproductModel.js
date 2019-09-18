$("#createNewService").click(function () {
    var ddlProductTypeSelected = $('#ddlProductType').val();
    var ddlTypeSelected = $('#ddlType').val();
    var productNameSelected = document.getElementById('productName').value;

    var objProduct = {
        id:-1, //for new items, id is always -1
        id_product_type: ddlProductTypeSelected,
        servicetypefk: ddlTypeSelected,
        product_name: productNameSelected,
        active: 1
    };

    const url_save_product = "php/api/bckoffservices/savenewproduct.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_product,
        method : "POST",
        data : objProduct,                                                                                                                                                                                                                                                                                                                                                                                                                                              
        success : function(data){
            console.log('value', data);
            allServiceGridCost();
            resetFormAddProduct();
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });

});
// End click

// Function Reset Form Add New Service
function resetFormAddProduct() {
    $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
}