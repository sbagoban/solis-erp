$(document).ready(function(){
    allServicesGrid();
});

function allServicesGrid(added) {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product");
    var servicetype = urlParams.get("servicetype");
    $('#tbl-productServices').DataTable({     
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            if ( aData.is_pakage == "Y" ){
                $('td', nRow).css('background-color', '#bde8ff');
            }
            $('#btnAddClaimTransfer', nRow).css('display', 'none');
            $('#btnAddClaimPackage', nRow).css('display', 'none');       
            if (servicetype == "TRANSFER") {
                $('#btnAddProductServices', nRow).css('display', 'none');   
                $('#btnAddProductServicesExtra', nRow).css('display', 'none');
                $('#btnAddClaimPackage', nRow).css('display', 'none');                
                $('#btnAddClaimTransfer', nRow).css('display', 'inline-flex');
            }

            if (servicetype == "ACTIVITY" && aData.is_pakage == "Y") {
                $('#btnAddProductServices', nRow).css('display', 'none');   
                $('#btnAddProductServicesExtra', nRow).css('display', 'none');                
                $('#btnAddClaimPackage', nRow).css('display', 'inline-flex');
            }
        },  
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
        "aaSorting": [ [1,'desc'] ],
        
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
            data : null,
                    render: function( data, type, row ) {
                        var id_product_service = data.id_product_service;
                        if (data.on_approved == "1") {
                            var icon =  '<i class = "fa fa-check fa-check-circle" style="font-size:18px;color:#00a65a" title="Service line Live"></i> &nbsp; &nbsp;';
                        } else {
                            var icon =  '<i class = "fa fa-check fa-check-circle" style="font-size:18px;color:#e6e6e6" title="Service line Not Live"></i> &nbsp; &nbsp;';                            
                        }
                        return icon+ ' ' +id_product_service;
                    }
        }, {
            "data" : "allName"
        }, {
            "data" : "deptname"
        }, {
            "data" : "charge"
        }, {
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
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<i id="btnAddProductServices" class="fa fa-fw fa-plus-circle" title="Product Service Cost"></i>' +
                '<i id="btnAddClaimTransfer" class="fa fa-fw fa-money" title="Add Claim Transfer"></i>' +
                '<i id="btnAddClaimPackage" class="fa fa-fw fa-money" title="Add Claim Package"></i>' +
                '<i id="btnAddProductServicesExtra"  class="fa fa-gg-circle" title="Extra Service"></i>' + 
                '<i id="btnEditProduct" class="fa fa-fw fa-edit" title="Edit Line"></i>' +
                '<i id="btnDuplicateProductService" class="fa fa-fw fa-clone" title="Duplicate line"></i>' +
                '<i id="btnDeleteService" class="fa fa-fw fa-trash-o" title="Delete Service"></i></div>'
            }
        ],
        "initComplete": function () {
            $('#tbl-productServices tbody')
                .off()
                .on( 'dblclick', 'tr', function (e) {
                    // Open Modal                    
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( this ).data();
                    //saveImages(data);
                    $('#modal-pictures').modal('show');
                    document.getElementById('id_product_service_modal').textContent  = data.id_product_service;
                })
                .on( 'click', '#btnAddClaimTransfer', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    addCostTransfer(data);
                })
                .on( 'click', '#btnAddClaimPackage', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    addCostPackage(data);
                })
                .on( 'click', '#btnDuplicateProductService', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    duplicateProductServices(data);
                })
                .on( 'click', '#btnAddProductServices', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    addProductServices(data);
                })
                .on( 'click', '#btnEditProduct', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    serviceEdit(data);
                })
                .on( 'click', '#btnAddProductServicesExtra', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    addServiceExtra(data);
                })
                .on( 'click', '#btnDeleteService', function (e) {
                    var table = $('#tbl-productServices').DataTable();
                    var data = table.row( $(this).parents('tr') ).data();
                    alertServiceDelete(data);
                })
                if (added == true) {
                    row = $('#tbl-productServices tr:first-child');
                    $(row).addClass('DTTT_selected');
                }
        }

    });
}

function alertServiceDelete (data) {
    swal({
		title: "Are you sure?",
		text: "you want to delete ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'Yes, delete it!',
		closeOnConfirm: false,
		//closeOnCancel: false
	},
	function(){
        serviceDelete(data);
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
        dataType: "json",
        success: function (data) {
            if (data.OUTCOME == 'OK') { 
                swal("Deleted!", "Deleted !", "success");
                resetServicesForm();
            }
        },
        error: function (error) {
            swal("Cancelled", "Not Deleted - Please try again...", "error");
        }
    });
    allServicesGrid();
}

// // Edit Product
function serviceEdit(data) {
    if (data.on_api == 1) { 
        $('.toggle:eq(1)').addClass('btn-success').removeClass('btn-default off');
        $('#on_api').prop('checked', true);
    } else { 
        $('.toggle:eq(1)').addClass('btn-default off').removeClass('btn-success');
        $('#on_api').prop('checked', false);
        $(".toggle:eq(1)").removeClass("add_disabled");
    }
    if (data.on_approved == 1) { 
        $('.toggle:eq(0)').addClass('btn-success').removeClass('btn-default off');
        $('#on_approved').prop('checked', true);
    } else { 
        $('.toggle:eq(0)').addClass('btn-default off').removeClass('btn-success');
        $('#on_approved').prop('checked', false);
    }

    document.getElementById("idService").innerHTML = data.id_product_service;
    document.getElementById("chargeDetail").innerHTML = data.charge;

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
    var date_valid_from = date_from_y+"-"+date_from_m+"-"+date_from_d
	var end_date = data.valid_to;
	var date_to = end_date.split("-");
	var date_to_y = date_to[0];
	var date_to_m = date_to[1];
	var date_to_d = date_to[2];
    var end_date = date_to_d+"/"+date_to_m+"/"+date_to_y;
    var date_valid_to = date_to_y+"-"+date_to_m+"-"+date_to_d;
    var date_range = start_date+ " - " + end_date;

    if (data.service_name == "SOUTH EAST" || service_name == "OTHER COAST") {
        $('#special_name_transfer').css('display', 'block');  
        //$("#special_name_transfer option[value='DROP ON']").hide();
        $("#special_name_transfer option[value='DROP OFF']").hide();
        $("#special_name_transfer option[value='FULL DAY']").hide();
        $("#special_name_transfer option[value='HALF DAY']").hide();
        $("#special_name_transfer option[value='NIGHT TOUR']").hide();
        $("#special_name_transfer option[value='AIRPORT']").show();
        $("#special_name_transfer option[value='PORT']").show();
    } else if (data.service_name == "INTER HOTEL") {                     
        $('#special_name_transfer').css('display', 'none');
    } else if (data.service_name == "ACTIVITY") {        
        $('#special_name_transfer').css('display', 'block');              
        $("#special_name_transfer option[value='AIRPORT']").hide();
        $("#special_name_transfer option[value='PORT']").hide();                    
       // $("#special_name_transfer option[value='DROP ON']").show();
        $("#special_name_transfer option[value='DROP OFF']").show();
        $("#special_name_transfer option[value='FULL DAY']").show();
        $("#special_name_transfer option[value='HALF DAY']").show();
        $("#special_name_transfer option[value='NIGHT TOUR']").show();
    }
    $('#daterangeServiceFromTo1').val(date_range);
    $('#daterangeServiceFromTo1').daterangepicker({
        "showDropdowns": true,
		"opens": "center",
        locale: {
            format: 'DD/MM/YYYY'
        },
        "autoApply": true,
        "opens": "center",
        startDate: start_date,
        endDate: end_date
    });

    $('#id_dept').val(data.id_dept);    
    $('#product_name').val(data.product_name);
    $('#id_country').val(data.id_country);
    $('#id_coast').val(data.id_coast);
    $('#service_name').val(data.service_name);
    $('#service_name_transfer').val(data.service_name);
    $('#max_adult').val(data.max_adult);
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
    $('#min_age').val(data.min_age);
    $('#max_age').val(data.max_age);
    $('#is_pakage').val(data.is_pakage);
    
    $('#special_name').val(data.special_name);
    $('#special_name_transfer').val(data.special_name);
    
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
    var chkAdult= document.getElementById("for_adult");

    if (data.is_pakage == 'N') { 
        $('#services_block').css("display", "none");
        $("#services_cost option:selected").prop("selected", false);
        $("#services_cost option").remove();
        $('#services_cost').multiselect('rebuild');
    }
    if (data.is_pakage == 'Y') {
        $("#services_cost").multiselect('destroy');
        $("#services_cost").attr('multiple', 'multiple');
        specificServiceSelected(data, data.id_product_service, date_valid_from, date_valid_to);
        $('#services_block').css("display", "block");
        $('#id_creditor').attr("disabled", true);
    } 

    if (data.for_adult == 1){
        chkAdult.checked = true;
    }
    if (data.for_adult == 0){
        chkAdult.checked = false;
    }

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
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");
    window.location.href = "index.php?m=productservicescost&psid=" 
    + data.id_product_service + "&iddept=" 
    + data.id_dept+ "&productname=" 
    + data.product_name + "&servicename=" 
    + data.service_name+ "&idcoast=" + data.id_coast+ "&idcreditor=" + data.id_creditor+ "&charge=" 
    + data.charge + "&id_product_service=" 
    + data.id_product_service
    + "&valid_from=" + data.valid_from + "&valid_to=" + data.valid_to + "&servicetype=" + servicetype + "&for_adult=" + data.for_adult + "&for_child=" + data.for_child
    + "&for_infant=" + data.for_infant
    + "&for_teen=" + data.for_teen;
}

function addServiceExtra(data) {
    modalExtraService(data);
    $('#modal-extraServices1').modal('show');
}

function duplicateProductServices(data) {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams); 

    var id_service_type = urlParams.get("id_service_type"); 
    var id_product_type = urlParams.get("id_product_type");
    var servicetype = urlParams.get("servicetype");
    var objServiceDuplicate = {
        id_product_service :-1, //for new items, id is always -1
        id_product : data.id_product,
        valid_from : data.valid_from,
        valid_to : data.valid_to,
        product_name : data.product_name,
        id_dept : data.id_dept,
        id_country : data.id_country,
        id_coast : data.id_coast,
        service_name : data.service_name,
        id_tax : data.id_tax,
        charge : data.charge,
        duration : data.duration,
        transfer_included : data.transfer_included,
        description : data.description,
        comments : data.comments,
        on_monday : data.on_monday,
        on_tuesday : data.on_tuesday,
        on_wednesday : data.on_wednesday,
        on_thursday : data.on_thursday,
        on_friday : data.on_friday,
        on_saturday : data.on_saturday,
        on_sunday : data.on_sunday,
        cancellation : data.cancellation,
        age_inf_to : data.age_inf_to,
        age_child_to : data.age_child_to,
        age_teen_to : data.age_teen_to,
        age_inf_from : data.age_inf_from,
        age_child_from : data.age_child_from,
        age_teen_from : data.age_teen_from,
        min_pax : data.min_pax,
        max_pax : data.max_pax,
        id_creditor : data.id_creditor,
        for_infant : data.for_infant,
        for_child : data.for_child,
        for_teen : data.for_teen,  
        for_adult : data.for_adult,          
        min_age : data.min_age,
        max_age : data.max_age, 
        is_pakage : data.is_pakage, 
        id_service_type : id_service_type, 
        id_product_type : id_product_type,
        id_product_service_included : 0,
        servicetype : servicetype,
        special_name : data.special_name,
        max_adult : data.max_adult,
        on_api : 0, 
        on_approved : 0
    };

    const url_duplicate_service = "php/api/backofficeproduct/saveservice.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_duplicate_service,
        method : "POST",
        data : objServiceDuplicate,  
        dataType: "json",
        cache: false,                                                                                     
        success : function(val){
            serviceEdit(val);
            document.getElementById('id_prod_serv').innerHTML = val.id_product_service;
            duplicateCost(data, val.id_product_service); 
            duplicateExtra(data, val.id_product_service);   
            duplicateExtraCost(data, val.id_product_service, val.id_product_service_cost); 
            duplicateIncludedServices(data, val.id_product_service);

        },
        error: function(error) {
            console.log('Error ${error}', error);
        }
    });
    $('.toast_duplicate').stop().fadeIn(400).delay(2000).fadeOut(500);
    var added = true;       
    allServicesGrid(added);
}

function duplicateCost(data, id_prod_serv) {
    var objCost = {id_product_service: id_prod_serv};
    const url_duplicate_service_cost = "php/api/backofficeproduct/duplicateservice.php?t=" + encodeURIComponent(global_token)+ "&id_product_service1=" + data.id_product_service;
    $.ajax({
        url : url_duplicate_service_cost,
        method : "POST",
        data : objCost,                                                                                    
        success : function(data){
            $('.toast_duplicate_cost').stop().fadeIn(3500).delay(3000).fadeOut(500);
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function duplicateExtra(data, id_prod_serv) {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");
    
    if (servicetype == 'ACTIVITY') {
        var objExtra = {id_prod_serv: id_prod_serv};
        const url_duplicate_service_extra = "php/api/backofficeproduct/duplicateserviceextra.php?t=" + encodeURIComponent(global_token)+ "&id_product_service1=" + data.id_product_service;
        $.ajax({
            url : url_duplicate_service_extra,
            method : "POST",
            data : objExtra,                                                                                    
            success : function(data){
                $('.toast_duplicate_extra').stop().fadeIn(6500).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    }
}

function duplicateExtraCost(data, id_prod_serv, id_prod_cost) {  
    var objExtraCost = {
        id_prod_serv: id_prod_serv, 
        id_prod_cost: id_prod_cost
    };
    const url_duplicate_service_extra_cost = "php/api/backofficeproduct/duplicateserviceextracost.php?t=" + encodeURIComponent(global_token)+ "&id_product_service1=" + data.id_product_service;
    $.ajax({
        url : url_duplicate_service_extra_cost,
        method : "POST",
        data : objExtraCost,                                                                                    
        success : function(data){
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function duplicateIncludedServices(data, id_prod_serv) {
    var objIncludedService = {id_prod_serv: id_prod_serv};
    const url_duplicate_included_service = "php/api/backofficeproduct/duplicateincludedservices.php?t=" + encodeURIComponent(global_token)+ "&id_product_service1=" + data.id_product_service;
    $.ajax({
        url : url_duplicate_included_service,
        method : "POST",
        data : objIncludedService,                                                                                    
        success : function(data){
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function addCostTransfer(value) { 
    console.log('1', value);
    var objtransfercostdetails = {id_product_service: value.id_product_service};
    const url_transfer_cost_details = "php/api/backofficeproduct/selecttransfercostdetails.php?t=" + encodeURIComponent(global_token)+ "&id_product_service=" + value.id_product_service;
    $.ajax({
        url : url_transfer_cost_details,
        method : "POST",
        data : objtransfercostdetails, 
        dataType: "json",                                                                                 
        success : function(data){            
            console.log('test 1', data);
            id_product_service_cost = data[0].id_product_service_cost;
            var params = jQuery.param(value);
            window.location.href = "index.php?m=servicerate_claim&data=" +params + "&servicetype=" +"TRANSFER"+ "&id_product_service_cost="+id_product_service_cost;
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });    
}


function addCostPackage(value) {
    var objpackagecostdetails = {id_product_service: value.id_product_service};
    const url_package_cost_details = "php/api/backofficeproduct/selecttransfercostdetails.php?t=" + encodeURIComponent(global_token)+ "&id_product_service=" + value.id_product_service;
    $.ajax({
        url : url_package_cost_details,
        method : "POST",
        data : objpackagecostdetails,
        dataType: "json",
        success : function(data){
            console.log('test', data);
            id_product_service_cost = data[0].id_product_service_cost;
            var params = jQuery.param(value);
            window.location.href = "index.php?m=servicerate_claim&data=" +params + "&servicetype=" +"ACTIVITY"+ "&id_product_service_cost="+id_product_service_cost;
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });    
}
