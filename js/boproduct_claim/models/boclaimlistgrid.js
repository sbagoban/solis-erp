$(document).ready(function(){
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product_service_cost = urlParams.get("id_product_services_cost"); 
    allServicesGridClaim(id_product_service_cost);
});

function allServicesGridClaim(id_product_service_cost) {
    $('#tbl-productServicesClaim').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeserviceclaim/gridclaimlist.php?t=" + encodeURIComponent(global_token) + "&id_product_service_cost=" +id_product_service_cost,
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
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
            "data" : "id_product_service_claim"
        }, {
            "data" : "allName"
        }, {
            "data" : "deptname"
        }, {
            "data" : "charges"
        }, {
            "data" : "currency"
        }, {
            "data" : "allDate"
        },  {
            "data" : "specific_to"
        },  
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<i class="fa fa-fw fa-plus-circle" id="btnAddExtraServices" data-toggle="modal" data-target="#modal-extraServicesClaim"></i></div>' +
                '<i class="fa fa-fw fa-edit"></i>'+
                '<i class="fa fa-fw fa-trash"></i>'
            }
        ]
    });
    $('#tbl-productServicesClaim tbody').on( 'click', '#btnAddExtraServices', function () {
        var table = $('#tbl-productServicesClaim').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceClaim(data);
    });
}
