function extraServiceGridClaim(data) {
    $('#tbl-productServicesExtraClaim').DataTable({       
        "processing" : true,
        "ajax" : {
            "url" : "php/api/backofficeserviceclaim/gridextraserviceclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" + data.id_product_service_claim,
            "dataSrc" : ''
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
            "data" : "id_product_service_extra_claim"
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
                '<i class="fa fa-fw fa-edit" id="btnEditServiceClaim"></i>'+
                '<i class="fa fa-fw fa-trash-o" id="btnDeleteServiceClaim"></i></div>'
            }
        ],
        "initComplete": function () {
            $('#tbl-productServicesExtraClaim tbody')
                .off()
                .on( 'click', '#btnDeleteServiceClaim', function (e) {
                    var table = $('#tbl-productServicesExtraClaim').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    extraServiceClaimDelete(data);
                })
                .on( 'click', '#btnEditServiceClaim', function (e) {
                    var table = $('#tbl-productServicesExtraClaim').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    extraServiceClaimEdit(data);
                })
        }
    });
}

// // Delete Product
function extraServiceClaimDelete(data) {
    var objDelClaim = {id_product_service_extra_claim: data.id_product_service_extra_claim};
    const url_delete_extra_claim= "php/api/backofficeserviceclaim/deleteextraclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_extra_claim=" + data.id_product_service_extra_claim;
    $.ajax({
        url: url_delete_extra_claim,
        method: "POST",
        data: objDelClaim,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    extraServiceGridClaim(data);
    loadCountryClaim();
}

// Edit Product
function serviceCostEdit(data) {
    document.getElementById("id_product_service_cost_1").innerHTML = data.id_product_service_cost;
    $('#valid_from').val(data.valid_from);
    $('#valid_to').val(data.valid_to);
    $('#ps_adult_cost').val(data.ps_adult_cost);
    $('#ps_teen_cost').val(data.ps_teen_cost);
    $('#ps_child_cost').val(data.ps_child_cost);
    $('#ps_infant_cost').val(data.ps_infant_cost);
    $('#id_currency').val(data.id_currency);
}

// Edit Extra Service
function extraServiceClaimEdit(data) {
    $('#modal-extraServicesClaim').modal('show');
    editAllExtraServiceClaim(data);
}