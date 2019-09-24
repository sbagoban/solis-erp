$(document).ready(function(){
    allServicesGridClaim();
});

function allServicesGridClaim() {
    $('#tbl-productServicesClaim').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeserviceclaim/gridserviceclaim.php?t=" + encodeURIComponent(global_token),
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
            "data" : "id_product_services_cost"
        }, {
            "data" : "id_product_services"
        }, {
            "data" : "service_name"
        }, {
            "data" : "coast"
        }, {
            "data" : "deptname"
        }, {
            "data" : "charges"
        },  {
            "data" : "allDate"
        },  
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<i class="fa fa-fw fa-plus-circle" id="btnAddExtraServices"></i></div>'
            }
        ]
    });
    $('#tbl-productServicesClaim tbody').on( 'click', '#btnAddExtraServices', function () {
        var table = $('#tbl-productServicesClaim').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceClaim(data);
    });
}


// Add Extra Service
function serviceClaim(data) {
    var params = jQuery.param(data)
    window.location.href = "index.php?m=servicerate_claim&pscid=" + data.id_product_services_cost + "&data=" +params;
}
