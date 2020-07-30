$(document).ready(function(){
    allServicesGridCost();
});

function allServicesGridCost(addedCost) {

    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product_service = urlParams.get("psid");

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
        "aaSorting": [ [0,'desc'] ],
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "columnDefs": [
            
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
                '<i id="btnDeleteServiceCost" class="fa fa-fw fa-trash-o" title="Delete Line"></i>' + 
                '<i id="btnAddClaim" class="fa fa-fw fa-money" title="Add Claim"></i></div>'
            },
            {
                "data" : null,
                    render: function( data, type, row ) {
                        var multiple_price_cost = data.multiple_price_cost ;
                        if (data.multiple_price_cost == 1) {
                            var icon =  '<i class = "fa fa-fw fa-user-plus" style="font-size:18px;color:#00a65a" title="Multiple Price" id="btnPaxBreaksCost"></i> &nbsp; &nbsp;';
                        } else {
                            var icon =  '<i class = "fa fa-fw fa-user-plus" style="font-size:18px;color:#e6e6e6" title="Single price"></i> &nbsp; &nbsp;';                            
                        }
                        return icon;
                }
            }
        ],
        "initComplete": function () {
            $('#tbl-productServicesCost tbody')
                .off()
                .on( 'click', '#btnAddExtraServices', function (e) {
                    var table = $('#tbl-productServicesCost').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    serviceCostExtra(data);
                })
                .on( 'click', '#btnDeleteServiceCost', function (e) {
                    var table = $('#tbl-productServicesCost').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    alertServiceCostDelete(data);
                })
                .on( 'click', '#btnEditServiceCost', function (e) {
                    var table = $('#tbl-productServicesCost').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    serviceCostEdit(data);
                    allExtraServicesCostGrid(data.id_product_service_cost);
                })
                .on( 'click', '#btnAddClaim', function (e) {
                    var table = $('#tbl-productServicesCost').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    addServiceClaim(data);
                })
                .on( 'click', '#btnPaxBreaksCost', function (e) {
                    var table = $('#tbl-productServicesCost').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    allServicesPaxBreakGridCost(data);
                    multiplePriceServiceCost(data);
                })
                
                if (addedCost == true) {
                    row = $('#tbl-productServicesCost tr:first-child');
                    $(row).addClass('DTTT_selected');
                }
        }
    });
    
}

function alertServiceCostDelete (data) {
    swal({
		title: "Are you sure?",
		text: "you want to delete ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Yes, delete it!',
		closeOnConfirm: false,
        showConfirmButton: false
	},
	function(){
        serviceCostDelete(data);
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
        dataType: "json",
        success: function (data) { 
            if (data.OUTCOME == 'OK') {
                swal({
                    title: "Deleted!",
                    text: "Your row has been deleted.",
                    type: "success",
                    timer: 2000
                });
                resetFormAddServiceCost();
            }
        },
        error: function (error) {
            swal("Cancelled", "Not Deleted - Please try again...", "error");
        }
    });
    allServicesGridCost();
    allExtraServicesCostGrid(data.id_product_service_cost);
}

// Edit Product
function serviceCostEdit(data) {
    var allParams = window.location.href.split('productservicecost').pop();

    const urlParams = new URLSearchParams(allParams);

    var valid_from = urlParams.get("valid_from"); 
    var valid_to = urlParams.get("valid_to"); 
    var valid_from = valid_from.split("-");
	var valid_from = valid_from[0]+","+valid_from[1]+","+valid_from[2];
	var valid_from = new Date(valid_from);
	var valid_to = valid_to.split("-");
    var valid_to = valid_to[0]+","+valid_to[1]+","+valid_to[2];
    var valid_to = new Date(valid_to);
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

    $('#daterangeServiceFromTo').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        },
        "opens": "center",
        startDate : start_date,
        endDate : end_date,        
        "minDate" : valid_from,
        "maxDate" : valid_to
    });

    if (data.multiple_price_cost == 1) {
        $('#multiple_price_cost').prop('checked', true);
        var multiple_price_cost = 1;    
        document.getElementById("ps_adult_cost").disabled = true;
        document.getElementById("ps_teen_cost").disabled = true;
        document.getElementById("ps_child_cost").disabled = true;
        document.getElementById("ps_infant_cost").disabled = true;
    } else { 
        $('#multiple_price_cost').prop('checked', false);
        var multiple_price_cost = 0;   
    } 
}

// Add Extra Service
function serviceCostExtra(data) {
    document.getElementById("id_product_service_cost_extra").innerHTML = data.id_product_service_cost;
    addExtraServiceCost(data);
}

// Add claim 
function addServiceClaim(data) {
    var allParams = window.location.href.split('productservicecost').pop();
    const urlParams = new URLSearchParams(allParams);
    var for_adult = urlParams.get("for_adult"); 
    var for_child = urlParams.get("for_child"); 
    var for_infant = urlParams.get("for_infant"); 
    var for_teen = urlParams.get("for_teen"); 

    var params = jQuery.param(data);
    window.location.href = "index.php?m=servicerate_claim&pscid=" 
    + data.id_product_service_cost 
    + "&data=" +params
    + "&for_adult=" +for_adult
    + "&for_child=" +for_child
    + "&for_infant=" +for_infant
    + "&for_teen=" +for_teen;
}
