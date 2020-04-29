$('#modal-pictures').on('hidden.bs.modal', function () {
    $("#modal-pictures").reload();
    alert('dsgdsg');
});

const XHRUpload = Uppy.XHRUpload;
var uppy = Uppy.Core()
.use(Uppy.Dashboard, {
    inline: true,
    target: '#drag-drop-area',
    width: 870,
    height: 550,
    thumbnailWidth: 380,
    showProgressDetails: true
})
uppy.use(XHRUpload, {
    endpoint: 'php/api/backofficeproduct/saveimages.php?t=' + encodeURIComponent(global_token),
    formData: true,
    fieldName: 'files[]'
})

uppy.on('complete', (result) => {
    console.log('Upload complete! We’ve uploaded these files:', result.successful)
    console.log('result', result);
})

// And display uploaded files
uppy.on('upload-success', (file, response) => {
    var id_product_service = document.getElementById('id_product_service_modal').textContent;
    saveImagesPath(id_product_service, response);
});

function saveImagesPath (id_product_service, imageResponse) {
    var objServicesImages = {
        id_product_service_images :-1, //for new items, id is always -1
        id_product_service : id_product_service,
        product_service_images_path : imageResponse.body.path,
        active: 1
    };

    const url_save_images = "php/api/backofficeproduct/saveimagespath.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_images,
        method : "POST",
        data : objServicesImages,                                                                                                                                                                                                                                                                                                                                                                                                                
        success : function(data){
            $('.toast_added_image').stop().fadeIn(400).delay(3000).fadeOut(500);
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}