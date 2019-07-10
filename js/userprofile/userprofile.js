document.getElementById("aTitle").innerHTML = "USER PROFILE";


function change_userphoto()
{
    $('#myModal').modal('toggle');

}


function uploadPhoto()
{
    var upload_form = null;
    var xhr = null;

    var fileSelect = document.getElementById('file-select');
    var uploadButton = document.getElementById('upload-button');

    if (fileSelect.files.length == 0) {
        dhtmlx.alert({
            text: "No Photo Selected to be Uploaded!",
            type: "alert-warning",
            title: "Upload Photo",
            callback: function () {
            }
        });
        return;
    }

    // Update button text.
    uploadButton.value = 'Uploading...';
    uploadButton.disable = true;

    var files = fileSelect.files;
    var formData = new FormData();
    for (var i = 0; i < files.length; i++) {
        var file = files[i];

        // Check the file type, need image type
        if (!file.type.match('image.*')) {
            continue;
        }

        formData.append('photos', file, file.name);
        formData.append('token', global_token);
    }

    xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/api/userprofile/uploadphoto.php', true);

    xhr.onload = function () {

        if (xhr.status === 200) {
            if (xhr.responseText.indexOf("OK") != -1)
            {
                location.reload();

            } else
            {
                dhtmlx.alert({
                    text: xhr.responseText,
                    type: "alert-warning",
                    title: "Upload Photo",
                    callback: function () {
                        var uploadButton = document.getElementById('upload-button');
                        uploadButton.disable = false;
                        uploadButton.value = 'Upload Photo';
                    }
                });
            }
        } else {
            dhtmlx.alert({
                text: "There has been an error while sending the photo to the server!",
                type: "alert-warning",
                title: "Upload Photo",
                callback: function () {
                    var uploadButton = document.getElementById('upload-button');
                    uploadButton.disable = false;
                    uploadButton.value = 'Upload Photo';
                }
            });
        }
    };

    xhr.send(formData);
}

function reset_userphoto()
{
    var params = "token=" + encodeURIComponent(global_token);
    dhtmlxAjax.post("php/api/userprofile/resetphoto.php", params, function (loader) {

        if (loader)
        {
            if (loader.xmlDoc.responseURL == "")
            {
                dhtmlx.alert({
                    text: "Connection Lost!",
                    type: "alert-warning",
                    title: "RESET",
                    callback: function () {
                    }
                });
                return false;
            }

            var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

            if (!json_obj)
            {
                dhtmlx.alert({
                    text: loader.xmlDoc.responseText,
                    type: "alert-warning",
                    title: "RESET",
                    callback: function () {
                    }
                });
                return false;
            }
            if (json_obj.OUTCOME == "OK")
            {
                location.reload();
            } else
            {
                dhtmlx.alert({
                    text: json_obj.OUTCOME,
                    type: "alert-warning",
                    title: "RESET",
                    callback: function () {
                    }
                });
            }

        }
    });
}