function allExtraServicesGrid(id_product_services) {
    console.log(id_product_services);
    // Request call everything from database
    $('#tbl-extraService').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridextraservice.php?t=" + encodeURIComponent(global_token)  + "&id_product_services=" + id_product_services,
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 4,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "columnDefs": [
            { width: 200, targets: -1 }
        ],
        "buttons":[
            {
            "extend":    "csvHtml5",
            "text":      "<i class='fa fa-file-text-o'> Excel</i>",
            "titleAttr": "Download in Excel Format",
            }
        ],
        "columnDefs": [
        ],
        "columns" : [ {
            "data" : "extra_name"
        }, {
            "data" : "extra_description"
        }, {
            "data" : "charges"
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnDeleteExtraService" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ]
    });
    $('#tbl-extraService tbody').on( 'click', '#btnDeleteExtraService', function () {
        var table = $('#tbl-extraService').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        deleteExtraService(data);
    });
}

// Delete Product
function deleteExtraService(data) {
    var objDelExtraService = {id_product_services_extra: data.id_product_services_extra};
    const url_delete_extraservice = "php/api/backofficeproduct/deleteextraservice.php?t=" + encodeURIComponent(global_token) + "&id_product_services_extra=" + data.id_product_services_extra;
    $.ajax({
        url: url_delete_extraservice,
        method: "POST",
        data: objDelExtraService,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    allExtraServicesGrid(data.id_product_services);
}
