$(document).ready(function(){
    allServicesGridCost();
});

function allServicesGridCost() {
    var id_product_services = window.location.href.split('psid=').pop();
    $('#tbl-productServicesCost').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_services=" + id_product_services,
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
            "data" : "id_product_services"
        }, {
            "data" : "allDate"
        }, {
            "data" : "charges"
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnAddExtraServices" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i></button>' +
                '<button type="button" id="btnEditServiceCost" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteServiceCost" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ]
    });
    $('#tbl-productServicesCost tbody').on( 'click', '#btnAddExtraServices', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceCostExtra(data);
    });
    $('#tbl-productServicesCost tbody').on( 'click', '#btnDeleteServiceCost', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceCostDelete(data);
    });
    $('#tbl-productServicesCost tbody').on( 'click', '#btnEditServiceCost', function () {
        var table = $('#tbl-productServicesCost').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceCostEdit(data);
        allExtraServicesCostGrid(data.id_product_services_cost);
    });
}

// // Delete Product
function serviceCostDelete(data) {
    var objDelServiceCost = {id_product_services_cost: data.id_product_services_cost};
    const url_delete_service_cost= "php/api/backofficeproduct/deleteservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_services_cost=" + data.id_product_services_cost;
    $.ajax({
        url: url_delete_service_cost,
        method: "POST",
        data: objDelServiceCost,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    allServicesGridCost();
}

// Edit Product
function serviceCostEdit(data) {
    document.getElementById("id_product_services_cost_1").innerHTML = data.id_product_services_cost;
    $('#valid_from').val(data.valid_from);
    $('#valid_to').val(data.valid_to);
    $('#ps_adult_cost').val(data.ps_adult_cost);
    $('#ps_teen_cost').val(data.ps_teen_cost);
    $('#ps_child_cost').val(data.ps_child_cost);
    $('#ps_infant_cost').val(data.ps_infant_cost);
    $('#id_currency').val(data.id_currency);
}

// Add Extra Service
function serviceCostExtra(data) {
    document.getElementById("id_product_services_cost_extra").innerHTML = data.id_product_services_cost;
    addExtraServiceCost(data);
    $('#modal-extraServices').modal('show');
}