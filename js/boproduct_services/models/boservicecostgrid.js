$(document).ready(function(){
    allServicesGridCost();
});

function allServicesGridCost() {
    var id_product_service = window.location.href.split('psid=').pop();
    $('#tbl-productServicesCost').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + id_product_service,
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
            "data" : "id_product_service_cost"
        }, 
        
        // {
        //     "data" : "allDate"
        // }, 
        {
            data: null,
                render: function ( data, type, row ) {
                    var start_date = data.valid_from;
                    var date_from = start_date.split("-");
                    var date_from_y = date_from[0];
                    var date_from_m = date_from[1];
                    var date_from_d = date_from[2];
                    var start_date = date_from_d+"/"+date_from_m+"/"+date_from_y;
                    var end_date = data.valid_to;
                    var date_to = end_date.split("-");
                    var date_to_y = date_to[0];
                    var date_to_m = date_to[1];
                    var date_to_d = date_to[2];
                    var end_date = date_to_d+"/"+date_to_m+"/"+date_to_y;
                    return start_date+' - '+end_date;
                },
                editField: ['valid_from', 'valid_to']
        },


        
        {
            "data" : "currency_code"
        }, {
            "data" : "charge"
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<i id="btnAddExtraServices" class="fa fa-fw fa-plus-circle" title="Extra Services Cost"></i>' +
                '<i id="btnEditServiceCost" class="fa fa-fw fa-edit" title="Edit Line"></i>' +
                '<i id="btnDeleteServiceCost" class="fa fa-fw fa-trash-o" title="Delete Line"></i></div>'
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
        allExtraServicesCostGrid(data.id_product_service_cost);
    });
}

// // Delete Product
function serviceCostDelete(data) {
    var objDelServiceCost = {id_product_service_cost: data.id_product_service_cost};
    const url_delete_service_cost= "php/api/backofficeproduct/deleteservicecost.php?t=" + encodeURIComponent(global_token) + "&id_product_service_cost=" + data.id_product_service_cost;
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
    document.getElementById("id_product_service_cost_1").innerHTML = data.id_product_service_cost;
		
	var start_date = data.valid_from;
	var date_from = start_date.split("-");
	var date_from_y = date_from[0];
	var date_from_m = date_from[1];
	var date_from_d = date_from[2];
    var start_date = date_from_d+"/"+date_from_m+"/"+date_from_y;
	var end_date = data.valid_to;
	var date_to = end_date.split("-");
	var date_to_y = date_to[0];
	var date_to_m = date_to[1];
	var date_to_d = date_to[2];
    var end_date = date_to_d+"/"+date_to_m+"/"+date_to_y;
	var date_range = start_date+ " - " + end_date;
	
    $('#daterangeServiceFromTo').val(date_range);
    $('#ps_adult_cost').val(data.ps_adult_cost);
    $('#ps_teen_cost').val(data.ps_teen_cost);
    $('#ps_child_cost').val(data.ps_child_cost);
    $('#ps_infant_cost').val(data.ps_infant_cost);
    $('#id_currency').val(data.id_currency);
}

// Add Extra Service
function serviceCostExtra(data) {
    document.getElementById("id_product_service_cost_extra").innerHTML = data.id_product_service_cost;
    addExtraServiceCost(data);
}