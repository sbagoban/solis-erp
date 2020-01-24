$(document).ready(function(){
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var product_name = urlParams.get("product_name");
    var servicetype = urlParams.get("servicetype");
    $('#product_name').val(product_name);
	$('#daterangeServiceFromTo').daterangepicker({
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		locale: {
					format: 'DD/MM/YYYY'
				}
    });
    if (servicetype == 'TRANSFER') {
        $('.adult_blk').css("display", "block");
    }
    
});

function changeTransfer() {
    $( "#service_name_transfer" ).change(function () {
        $( "#service_name_transfer option:selected" ).each(function() {
            service_name = $( this ).text();
        });
    }).change();
    return service_name;
}

function specialNameTransfer() {
    $( "#special_name_transfer" ).change(function () {
        $( "#special_name_transfer option:selected" ).each(function() {
            special_name_transfer = $(this).text();
        });
    }).change();
    return special_name_transfer;
}

function saveService() {
    var idService = document.getElementById("idService").innerHTML;
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product"); 
    var servicetype = urlParams.get("servicetype");

    var id_service_type = urlParams.get("id_service_type"); 
    var id_product_type = urlParams.get("id_product_type");

    var product_name = urlParams.get("product_name");
	var valid_from = $("#daterangeServiceFromTo").data('daterangepicker').startDate.format('YYYY-MM-DD');
	var valid_to = $("#daterangeServiceFromTo").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var product_name = product_name;
    var id_dept = $('#id_dept').val();
    var id_country = $('#id_country').val();
    var id_coast = $('#id_coast').val();
    var charge = $('#charge').val();
    var duration = $('#duration').val();
    var transfer_included = $('#transfer_included').val();
    var description = $('#description').val();
    var comments = $('#comments').val();
    var cancellation = $('#cancellation').val();
    var age_inf_to = $('#age_inf_to').val();
    var age_child_to = $('#age_child_to').val();
    var age_teen_to = $('#age_teen_to').val();
    var age_inf_from = $('#age_inf_from').val();
    var age_child_from = $('#age_child_from').val();
    var age_teen_from = $('#age_teen_from').val();
    var min_pax = $('#min_pax').val();
    var max_pax = $('#max_pax').val();
    var chkmonday = document.getElementById("on_monday");
    var chktuesday = document.getElementById("on_tuesday");    
    var chkwednesday = document.getElementById("on_wednesday");    
    var chkthursday = document.getElementById("on_thursday");    
    var chkfriday = document.getElementById("on_friday");
    var chksaturday = document.getElementById("on_saturday");
    var chksunday = document.getElementById("on_sunday");
    var chkinfant = document.getElementById("for_infant");
    var chkchild = document.getElementById("for_child");
    var chkteen = document.getElementById("for_teen");
    var chkadult = document.getElementById("for_adult");
    var min_age = $('#min_age').val();
    var max_age = $('#max_age').val();
    var is_pakage = $('#is_pakage').val();
    var id_product_service_induded = $('#services_cost').val();
    var max_adult = $('#max_adult').val();
    var on_approved_chk = $('#on_approved').prop('checked');
    var on_api_chk = $('#on_api').prop('checked');
    if (on_api_chk == true) { 
        var on_api_1 = 1;
    } else { 
        var on_api_1 = 0;
    }

    if (on_approved_chk == true) { 
        var on_approved_1 = 1;
    } else  { 
        var on_approved_1 = 0;
    }
    if (is_pakage == 'N') { 
        id_product_service_induded = 0;
    } 

    if (chkmonday.checked) {
        on_monday = 1;
    } else if (chkmonday.checked == false) {
        on_monday = 0;
    } 
    if (chktuesday.checked) {
        on_tuesday = 1;
    } else if (chktuesday.checked == false) {
        on_tuesday = 0;
    } 
    if (chkwednesday.checked) {
        on_wednesday = 1;
    } else if (chkwednesday.checked == false) {
        on_wednesday = 0;
    } 
    if (chkthursday.checked) {
        on_thursday = 1;
    } else if (chkthursday.checked == false) {
        on_thursday = 0;
    } 
    if (chkfriday.checked) {
        on_friday = 1;
    } else if (chkfriday.checked == false) {
        on_friday = 0;
    } 
    if (chksaturday.checked) {
        on_saturday = 1;
    } else if (chksaturday.checked == false) {
        on_saturday = 0;
    } 
    if (chksunday.checked) {
        on_sunday = 1;
    } else if (chksunday.checked == false) {
        on_sunday = 0;
    }  

    if (chkinfant.checked) {
        for_infant = 1;
    } else if (chkinfant.checked == false) {
        for_infant = 0;
    } 
    if (chkchild.checked) {
        for_child = 1;
    } else if (chkchild.checked == false) {
        for_child = 0;
    } 
    if (chkteen.checked) {
        for_teen = 1;
    } else if (chkteen.checked == false) {
        for_teen = 0;
    }  

    if (chkadult.checked) {
        for_adult = 1;
    } else if (chkadult.checked == false) {
        for_adult = 0;
    }

    if (servicetype == 'TRANSFER') { 
        //var service_name = $('#service_name_transfer option:selected').text();
        var service_name = changeTransfer();
            if (service_name == 'INTER HOTEL') {
                var special_name = 'None';
            } else {
                var special_name = specialNameTransfer();
            }
        console.log('-->', special_name);
        var id_creditor = 0;
        var id_tax = '3';
        for_adult = 1;
        for_child = 1;
        for_infant = 1;
    } else {
        var id_creditor = $('#id_creditor').val();
        var service_name = $('#service_name').val();
        var special_name = $('#special_name').val();
        var id_tax = $('#id_tax').val();
    }

    if (idService == 0) {
        var objService = {
            id_product_service :-1, //for new items, id is always -1
            id_product : id_product,
            valid_from : valid_from,
            valid_to : valid_to,
            product_name : product_name,
            id_dept : id_dept,
            id_country : id_country,
            id_coast : id_coast,
            service_name : service_name,
            id_tax : id_tax,
            charge : charge,
            duration : dateManipulationDuration(),
            transfer_included : transfer_included,
            description : description,
            comments : comments,
            on_monday : on_monday,
            on_tuesday : on_tuesday,
            on_wednesday : on_wednesday,
            on_thursday : on_thursday,
            on_friday : on_friday,
            on_saturday : on_saturday,
            on_sunday : on_sunday,
            cancellation : cancellation,
            age_inf_to : age_inf_to,
            age_child_to : age_child_to,
            age_teen_to : age_teen_to,
            age_inf_from : age_inf_from,
            age_child_from : age_child_from,
            age_teen_from : age_teen_from,
            min_pax : min_pax,
            max_pax : max_pax,
            id_creditor : id_creditor,
            for_infant : for_infant,
            for_child : for_child,
            for_teen : for_teen,  
            for_adult : for_adult,          
            min_age : min_age,
            max_age : max_age, 
            is_pakage : is_pakage, 
            id_product_service_induded : id_product_service_induded, 
            id_service_type : id_service_type, 
            id_product_type : id_product_type, 
            special_name : special_name,
            servicetype : servicetype, 
            max_adult : max_adult, 
            on_api : on_api_1, 
            on_approved : on_approved_1
        };
        const url_save_service = "php/api/backofficeproduct/saveservice.php?t=" + encodeURIComponent(global_token);
        if (is_pakage == 'N' || (is_pakage == 'Y' &&  id_product_service_induded.length > 0)) { 
            $.ajax({
                url : url_save_service,
                method : "POST",
                data : objService,                                                                                   
                success : function(data){
                    console.log('value', data);
                    resetServicesForm();
                    var added = true;
                    allServicesGrid(added);
                    $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        } else { 
            $('.toast_error').stop().fadeIn(400).delay(3000).fadeOut(500);
        }

    } else {
        // Edit Drop Down Services - Delete first and the Saved
        console.log('is_pakage', is_pakage);
        if (servicetype != 'TRANSFER' && is_pakage == 'Y') {
            editServicesInclude(id_product_service_induded);
        }
        const url_edit_service = "php/api/backofficeproduct/updateservice.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + idService;
        const url_edit_delete_service = "php/api/backofficeproduct/updatedeleteservice.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + idService;
        var objServiceUpdate = {
            id_product : id_product,
            valid_from : valid_from,
            valid_to : valid_to,
            product_name : product_name,
            id_dept : id_dept,
            id_country : id_country,
            id_coast : id_coast,
            service_name : service_name,
            special_name : special_name,
            id_tax : id_tax,
            charge : charge,
            duration : dateManipulationDuration(),
            transfer_included : transfer_included,
            description : description,
            comments : comments,
            on_monday : on_monday,
            on_tuesday : on_tuesday,
            on_wednesday : on_wednesday,
            on_thursday : on_thursday,
            on_friday : on_friday,
            on_saturday : on_saturday,
            on_sunday : on_sunday,
            cancellation : cancellation,
            age_inf_to : age_inf_to,
            age_child_to : age_child_to,
            age_teen_to : age_teen_to,
            min_pax : min_pax,
            max_pax : max_pax,
            id_creditor : id_creditor,
            for_infant : for_infant,
            for_child : for_child,
            for_teen : for_teen,
            for_adult : for_adult,
            age_inf_from : age_inf_from,
            age_child_from : age_child_from,
            age_teen_from : age_teen_from,            
            min_age : min_age,
            max_age : max_age, 
            is_pakage : is_pakage, 
            special_name : special_name,            
            servicetype : servicetype, 
            max_adult : max_adult,
            on_api : on_api_1, 
            on_approved : on_approved_1
        };

        var chargeDetail = document.getElementById("chargeDetail").innerHTML;
        if (chargeDetail == charge) {
            $.ajax({
                url : url_edit_service,
                method : "POST",
                data : objServiceUpdate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    console.log('value', data);
                    resetServicesForm();
                    //allServicesGrid();
                    $('.toast_update').stop().fadeIn(400).delay(3000).fadeOut(500);
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        } if (chargeDetail != charge) { 
            $.ajax({
                url : url_edit_delete_service,
                method : "POST",
                data : objServiceUpdate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
                success : function(data){
                    console.log('value', data);
                    resetServicesForm();
                    //allServicesGrid();
                    $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
                },
                error: function(error) {
                    console.log('Error ${error}');
                }
            });
        }
		allServicesGrid();
    }
    document.getElementById("idService").innerHTML = 0;
    document.getElementById("chargeDetail").innerHTML = 0;
}

function dateManipulationDuration() {
    var hrs = document.getElementById('duration1').value;    
    var min = document.getElementById('duration2').value;
    if (hrs == ' ' || min ==' ') {
        ret = '00:00';
    } else {
        var ret = "";
        ret += hrs + ":" + min;
    }
    console.log('check duration', ret);
    return ret;
}

function resetServicesForm() {
    $('#valid_from').val('');
    $('#valid_to').val('');
    $('#id_dept').val('');
    $('#id_country').val('');
    $('#id_coast').val('');
    $('#service_name').val('');
    $('#id_tax').val('');
    $('#charge').val('');
    $('#duration1').val('');    
    $('#duration2').val('');
    //$('#transfer_included').val('');
    $('#description').val('');
    $('#comments').val('');
    $('#cancellation').val('');
    $('#age_inf_to').val('');
    $('#age_child_to').val('');
    $('#age_teen_to').val('');
    $('#age_inf_from').val('');
    $('#age_child_from').val('');
    $('#age_teen_from').val('');
    $('#min_pax').val('');
    $('#max_pax').val('');
    $("#on_monday").prop("checked", false);
    $("#on_tuesday").prop("checked", false);
    $("#on_wednesday").prop("checked", false);
    $("#on_thursday").prop("checked", false);
    $("#on_friday").prop("checked", false);
    $("#on_saturday").prop("checked", false);
    $("#on_sunday").prop("checked", false);
    $("#for_infant").prop("checked", false);
    $("#for_child").prop("checked", false);
    $("#for_teen").prop("checked", false);
    $("#for_adult").prop("checked", false);
    $("#age_inf_from").prop("readonly", true);
    $("#age_inf_to").prop("readonly", true);
    $("#age_child_from").prop("readonly", true);
    $("#age_child_to").prop("readonly", true);
    $("#age_teen_from").prop("readonly", true);
    $("#age_teen_to").prop("readonly", true);
    $('#id_creditor').val('');
    $('#service_name_transfer').val('');    
    $('#min_age').val('');
    $('#max_age').val('');
    $('#is_pakage').val('N');    
    $('#services_block').css("display", "none");    
    $('#services_cost').val([]).multiselect('refresh');
    $('#services_cost').val('');
    $('#special_name_transfer').val('');
    $('#special_name').val('');
    $('#max_adult').val('');
    $('.toggle:eq(0)').addClass('btn-default off').removeClass('btn-success');
    $('#on_api').prop('checked', false);
    $('.toggle:eq(1)').addClass('btn-default off').removeClass('btn-success');
    $('#on_approved').prop('checked', false);
    
}

function specificServiceSelected(val) { 
    const url_selected_service = "php/api/backofficeproduct/populateselectedservice.php?t=" + encodeURIComponent(global_token)+ "&id_product_service=" + val.id_product_service; 
    $.ajax({
        type: "POST",
        url: url_selected_service,
        dataType: "json",
        cache: false,
        success: function(data)
                {
                var valArr = [data];
                valArr.forEach(myFunction);
                function myFunction(value) {
                    loadSelectedService(value);             
                }
        }    
    });
}

function loadSelectedService(value) {
    $("#services_block").css("display", "block");
    console.log('-->', value);
    const url_service_selected = "php/api/backofficeproduct/selectservicecost.php?t=" + encodeURIComponent(global_token); 
    $.ajax({
        type: "POST",
        url: url_service_selected,
        dataType: "json",
        cache: false,
        success: function(data)
            {
                $("#services_cost").attr('multiple', 'multiple');
                $("#services_cost").empty();
                $.each(data, function (key, val) {
                    $("#services_cost").append('<option value="' + val.id_product_service_cost + '">' + val.service_name + '</option>');
                }); 
                arrToSelected = [];
                for (var i = 0, l = value.length; i < l; i++) {
                    var objSelected = value[i].id_product_service_induded;
                    arrToSelected.push(objSelected);
                    $("#services_cost").find("option[value=" + objSelected + "]").prop("selected", true)
                    $("#services_cost").multiselect("refresh")    
                }
                $("#services_cost").multiselect({
                    buttonWidth: '295px',
                    includeSelectAllOption: true,
                    nonSelectedText: 'Select an Option',
                    enableFiltering: true,
                    enableHTML: true,
                    buttonClass: 'btn large btn-default',
                    enableCaseInsensitiveFiltering: true
                });
            }
        }
    );
}

function editServicesInclude(id_product_service_induded) {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product"); 
    var servicetype = urlParams.get("servicetype"); 

    var id_service_type = urlParams.get("id_service_type"); 
    var id_product_type = urlParams.get("id_product_type");

    var id_product_service = document.getElementById("idService").innerHTML;

    const url_update_service_included = "php/api/backofficeproduct/deleteselectedservice.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" +id_product_service;
    var objServiceIncluded = {id_product_service: id_product_service};
    $.ajax({
        url: url_update_service_included,
        method: "POST",
        data: objServiceIncluded,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });

    const url_save_service_included = "php/api/backofficeproduct/saveselectedselectedservice.php?t=" + encodeURIComponent(global_token);
    var objSaveServicesIncluded = {
        id_product_service_package: -1,
        id_product_service : id_product_service,
        id_product : id_product,
        id_service_type : id_service_type,
        id_product_type : id_product_type,
        id_product_service_induded: id_product_service_induded
    };
    $.ajax({
        url: url_save_service_included,
        method: "POST",
        data: objSaveServicesIncluded,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
}