function allExtraServicesCostGrid(id_product_service_cost) {
    // Request call everything from database
    $('#tbl-extraServiceCost').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridextraservicecost.php?t=" + encodeURIComponent(global_token)  + "&id_product_service_cost=" + id_product_service_cost,
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
            "data" : "id_product_service_extra_cost"
        }, {
            "data" : "extra_name"
        }, {
            "data" : "charge"
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnDeleteExtraServiceCost" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ]
    });
    $('#tbl-extraServiceCost tbody').on( 'click', '#btnDeleteExtraServiceCost', function () {
        var table = $('#tbl-extraServiceCost').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        deleteExtraServiceCost(data);
    });
}

// Delete Product
function deleteExtraServiceCost(data) {
    var objDelExtraServiceCost = {id_product_service_extra_cost: data.id_product_service_extra_cost};
    const url_delete_extraservice_cost = "php/api/backofficeproduct/deleteextraservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_service_extra_cost=" + data.id_product_service_extra_cost;
    $.ajax({
        url: url_delete_extraservice_cost,
        method: "POST",
        data: objDelExtraServiceCost,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    allExtraServicesCostGrid(data.id_product_service_cost);
}
