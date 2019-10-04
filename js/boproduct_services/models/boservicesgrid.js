$(document).ready(function(){
    allServicesGrid();
});

function allServicesGrid() {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product");

    $('#tbl-productServices').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridservices.php?t=" + encodeURIComponent(global_token) + "&id_product=" + id_product,
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
            "data" : "id_product"
        }, {
            "data" : "allName"
        }, {
            "data" : "deptname"
        }, {
            "data" : "charges"
        }, {
            "data" : "valid_range"
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnAddProductServices" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i></button>' +
                '<button type="button" id="btnAddProductServicesExtra" class="btn btn-primary"><i class="fa fa-gg-circle"></i></button>' + 
                '<button type="button" id="btnEditProduct" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteService" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ]
    });
    $('#tbl-productServices tbody').on( 'click', '#btnDeleteService', function () {
        var table = $('#tbl-productServices').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceDelete(data);
    });
    $('#tbl-productServices tbody').on( 'click', '#btnAddProductServicesExtra', function () {
        var table = $('#tbl-productServices').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        addServiceExtra(data);
    });
    $('#tbl-productServices tbody').on( 'click', '#btnEditProduct', function () {
        var table = $('#tbl-productServices').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        serviceEdit(data);
    });
    $('#tbl-productServices tbody').on( 'click', '#btnAddProductServices', function () {
        var table = $('#tbl-productServices').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        addProductServices(data);
    });
}

// Delete Product
function serviceDelete(data) {
    var objDelService = {id_product_services: data.id_product_services};
    const url_delete_service= "php/api/backofficeproduct/deleteservice.php?t=" + encodeURIComponent(global_token) + "&id_product_services=" + data.id_product_services;
    $.ajax({
        url: url_delete_service,
        method: "POST",
        data: objDelService,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    allServicesGrid();
}

// // Edit Product
function serviceEdit(data) {
    document.getElementById("idService").innerHTML = data.id_product_services;
    $('#valid_from').val(data.valid_from);
    $('#valid_to').val(data.valid_to);
    $('#id_dept').val(data.id_dept);    
    $('#product_name').val(data.product_name);
    $('#id_countries').val(data.id_countries);
    $('#id_coasts').val(data.id_coasts);
    $('#service_name').val(data.service_name);
    $('#id_tax').val(data.id_tax);
    $('#charges').val(data.charges);
    $('#duration').val(data.duration);
    $('#transfer_included').val(data.transfer_included);
    $('#description').val(data.description);
    $('#comments').val(data.comments);
    $('#cancellation').val(data.cancellation);
    $('#age_inf_to').val(data.age_inf_to);
    $('#age_child_to').val(data.age_child_to);
    $('#age_teen_to').val(data.age_teen_to);
    $('#min_pax').val(data.min_pax);
    $('#max_pax').val(data.max_pax);

    var chkMonday = document.getElementById("on_monday");
    var chkTuesday = document.getElementById("on_tuesday");
    var chkWednesday = document.getElementById("on_wednesday");
    var chkThursday = document.getElementById("on_thursday");
    var chkFriday = document.getElementById("on_friday");
    var chkSaturday = document.getElementById("on_saturday");
    var chkSunday = document.getElementById("on_sunday");

    if (data.on_monday == 1){
        chkMonday.checked = true;
    }
    if (data.on_monday == 0){
        chkMonday.checked = false;
    }
    if (data.on_tuesday == 1){
        chkTuesday.checked = true;
    }
    if (data.on_tuesday == 0){
        chkTuesday.checked = false;
    }
    if (data.on_wednesday == 1){
        chkWednesday.checked = true;
    }
    if (data.on_wednesday == 0){
        chkWednesday.checked = false;
    }
    if (data.on_thursday == 1){
        chkThursday.checked = true;
    }
    if (data.on_thursday == 0){
        chkThursday.checked = false;
    }
    if (data.on_friday == 1){
        chkFriday.checked = true;
    }
    if (data.on_friday == 0){
        chkFriday.checked = false;
    }
    if (data.on_saturday == 1){
        chkSaturday.checked = true;
    }
    if (data.on_saturday == 0){
        chkSaturday.checked = false;
    }
    if (data.on_sunday == 1){
        chkSunday.checked = true;
    }
    if (data.on_sunday == 0){
        chkSunday.checked = false;
    }
}

// Add Product Cost Services
function addProductServices(data) { 
    console.log('-->', data);
    window.location.href = "index.php?m=productservicescost&psid=" 
    + data.id_product_services + "&iddept=" 
    + data.id_dept+ "&productname=" 
    + data.product_name + "&servicename=" 
    + data.service_name+ "&idcoast=" + data.id_coasts+ "&idcreditor=" + data.id_creditor+ "&charges=" 
    + data.charges + "&id_product_services=" 
    + data.id_product_services;
}

function addServiceExtra(data) {
    modalExtraService(data);
    $('#modal-extraServices1').modal('show');
}