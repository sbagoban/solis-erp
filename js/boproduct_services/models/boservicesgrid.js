$(document).ready(function(){
    allServicesGrid();
});

function allServicesGrid() {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product");
	console.log(id_product);
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
            "data" : "charge"
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
    var objDelService = {id_product_service: data.id_product_service};
    const url_delete_service= "php/api/backofficeproduct/deleteservice.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + data.id_product_service;
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
    document.getElementById("idService").innerHTML = data.id_product_service;
    
	var time_duration = data.duration;
	var time_all = time_duration.split(":");
	var time_hours = time_all[0];
    var time_min = time_all[1];
    $('#duration1').val(time_hours);    
    $('#duration2').val(time_min);

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
    $('#id_dept').val(data.id_dept);    
    $('#product_name').val(data.product_name);
    $('#id_country').val(data.id_country);
    $('#id_coast').val(data.id_coast);
    $('#service_name').val(data.service_name);
    $('#id_creditor').val(data.id_creditor);
    $('#id_tax').val(data.id_tax);
    $('#charge').val(data.charge);
    $('#transfer_included').val(data.transfer_included);
    $('#description').val(data.description);
    $('#comments').val(data.comments);
    $('#cancellation').val(data.cancellation);
    $('#age_inf_to').val(data.age_inf_to);
    $('#age_child_to').val(data.age_child_to);
    $('#age_teen_to').val(data.age_teen_to);
    $('#min_pax').val(data.min_pax);
    $('#max_pax').val(data.max_pax);
    $('#age_inf_from').val(data.age_inf_from);
    $('#age_child_from').val(data.age_child_from);
    $('#age_teen_from').val(data.age_teen_from);

    var chkMonday = document.getElementById("on_monday");
    var chkTuesday = document.getElementById("on_tuesday");
    var chkWednesday = document.getElementById("on_wednesday");
    var chkThursday = document.getElementById("on_thursday");
    var chkFriday = document.getElementById("on_friday");
    var chkSaturday = document.getElementById("on_saturday");
    var chkSunday = document.getElementById("on_sunday");

    var chkInfant = document.getElementById("for_infant");
    var chkChild = document.getElementById("for_child");
    var chkTeen= document.getElementById("for_teen");

    console.log(data.for_infant, 'test');
    if (data.for_infant == 1){
        chkInfant.checked = true;
    }
    if (data.for_infant == 0){
        chkInfant.checked = false;
    }
    if (data.for_child == 1){
        chkChild.checked = true;
    }
    if (data.for_child == 0){
        chkChild.checked = false;
    }
    if (data.for_teen == 1){
        chkTeen.checked = true;
    }
    if (data.for_teen == 0){
        chkTeen.checked = false;
    }


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
    + data.id_product_service + "&iddept=" 
    + data.id_dept+ "&productname=" 
    + data.product_name + "&servicename=" 
    + data.service_name+ "&idcoast=" + data.id_coast+ "&idcreditor=" + data.id_creditor+ "&charge=" 
    + data.charge + "&id_product_service=" 
    + data.id_product_service
    + "&valid_from=" + data.valid_from + "&valid_to=" + data.valid_to;
}

function addServiceExtra(data) {
    modalExtraService(data);
    $('#modal-extraServices1').modal('show');
}