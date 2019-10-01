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
        "pageLength": 5,
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
                '<i class="fa fa-fw fa-plus-circle" id="btnAddExtraServicesClaim" data-toggle="modal" data-target="#modal-extraServicesClaim"></i></div>' +
                '<i class="fa fa-fw fa-edit" id="btnEditClaim"></i>'+
                '<i class="fa fa-fw fa-trash" id="btnDeleteClaim" ></i>'
            }
        ]
    });
    $('#tbl-productServicesClaim tbody').on( 'click', '#btnAddExtraServicesClaim', function () {
        var table = $('#tbl-productServicesClaim').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        addExtraServiceClaim(data);
    });    
    $('#tbl-productServicesClaim tbody').on( 'click', '#btnDeleteClaim', function () {
        var table = $('#tbl-productServicesClaim').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        deleteServiceClaim(data);
    });   
    $('#tbl-productServicesClaim tbody').on( 'click', '#btnEditClaim', function () {
        var table = $('#tbl-productServicesClaim').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        editServiceClaim(data);
        extraServiceGridClaim(data);
    });
}


// Delete Product
function deleteServiceClaim(data) {
    var objDelClaim = {id_product_service_claim: data.id_product_service_claim};
    const url_delete_claim = "php/api/backofficeserviceclaim/deleteclaim.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" + data.id_product_service_claim;
    $.ajax({
        url: url_delete_claim,
        method: "POST",
        data: objDelClaim,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product_service_cost = urlParams.get("id_product_services_cost"); 
    allServicesGridClaim(id_product_service_cost);
}

function editServiceClaim(data) {
    document.getElementById("id_product_services_claim").innerHTML = data.id_product_service_claim;
    $('#valid_from').val(data.valid_from);
    $('#valid_to').val(data.valid_to);
    $('#ps_adult_claim').val(data.ps_adult_claim);
    $('#ps_teen_claim').val(data.ps_teen_claim);
    $('#ps_child_claim').val(data.ps_child_claim);
    $('#ps_infant_claim').val(data.ps_infant_claim);
    $('#id_currency').val(data.id_currency);
    $('#specific_to').val(data.specific_to);

    var chkmonday = data.ex_monday;
    var chktuesday = data.ex_tuesday;    
    var chkwednesday = data.ex_wednesday;    
    var chkthursday = data.ex_thursday;    
    var chkfriday = data.ex_friday;
    var chksaturday = data.ex_saturday;
    var chksunday = data.ex_sunday;

    if (chkmonday == '1') {
        $('#ex_monday').prop('checked', true);
    } else if (chkmonday == '0') {
        $('#ex_monday').prop('checked', false);
    } 
    if (chktuesday == '1') {
        $('#ex_tuesday').prop('checked', true);    
    } else if (chktuesday == '0') {
        $('#ex_tuesday').prop('checked', false);
    } 
    if (chkwednesday == '1') {
        $('#ex_wednesday').prop('checked', true);
    } else if (chkwednesday == '0') {
        $('#ex_wednesday').prop('checked', false);
    } 
    if (chkthursday == '1') {
        $('#ex_thursday').prop('checked', true);
    } else if (chkthursday == '0') {
        $('#ex_thursday').prop('checked', false);
    } 
    if (chkfriday == '1') {
        $('#ex_friday').prop('checked', true);
    } else if (chkfriday == '0') {
        $('#ex_friday').prop('checked', false);
    } 
    if (chksaturday == '1') {
        $('#ex_saturday').prop('checked', true);
    } else if (chksaturday == '0') {
        $('#ex_saturday').prop('checked', false);
    } 
    if (chksunday == '1') {
        $('#ex_sunday').prop('checked', true);
    } else if (chksunday == '0') {
        $('#ex_sunday').prop('checked', false);
    }  
}
