$('#btn-saveProductServices').click(function () {
    var idService = document.getElementById("idService").innerHTML;
    var id_product = window.location.href.split('pid=').pop();
    var valid_from = $('#valid_from').val();
    var valid_to = $('#valid_to').val();
    var product_name = $('#product_name').val();
    var id_dept = $('#id_dept').val();
    var id_countries = $('#id_countries').val();
    var id_coasts = $('#id_coasts').val();
    var service_name = $('#service_name').val();    
    var id_tax = $('#id_tax').val();
    var charges = $('#charges').val();
    var duration = $('#duration').val();
    var transfer_included = $('#transfer_included').val();
    var description = $('#description').val();
    var comments = $('#comments').val();
    var cancellation = $('#cancellation').val();
    var age_inf_to = $('#age_inf_to').val();
    var age_child_to = $('#age_child_to').val();
    var age_teen_to = $('#age_teen_to').val();
    var min_pax = $('#min_pax').val();
    var max_pax = $('#max_pax').val();
    var chkmonday = document.getElementById("on_monday");
    var chktuesday = document.getElementById("on_tuesday");    
    var chkwednesday = document.getElementById("on_wednesday");    
    var chkthursday = document.getElementById("on_thursday");    
    var chkfriday = document.getElementById("on_friday");
    var chksaturday = document.getElementById("on_saturday");
    var chksunday = document.getElementById("on_sunday");

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

    if (idService == 0) {
        var objService = {
            id_product_services :-1, //for new items, id is always -1
            id_product : id_product,
            valid_from : valid_from,
            valid_to : valid_to,
            product_name : product_name,
            id_dept : id_dept,
            id_countries : id_countries,
            id_coasts : id_coasts,
            service_name : service_name,
            id_tax : id_tax,
            charges : charges,
            duration : duration,
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
            max_pax : max_pax
        };
    
        console.log(objService);
        const url_save_service = "php/api/backofficeproduct/saveservice.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service,
            method : "POST",
            data : objService,                                                                                                                                                                                                                                                                                                                                                                                                                
            success : function(data){
                console.log('value', data);
                resetServicesForm();
                allServicesGrid();
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    } else {
        const url_edit_service = "php/api/backofficeproduct/updateservice.php?t=" + encodeURIComponent(global_token) + "&id_product_services=" + idService;
        var objServiceUpdate = {
            id_product : id_product,
            valid_from : valid_from,
            valid_to : valid_to,
            product_name : product_name,
            id_dept : id_dept,
            id_countries : id_countries,
            id_coasts : id_coasts,
            service_name : service_name,
            id_tax : id_tax,
            charges : charges,
            duration : duration,
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
            max_pax : max_pax
        };
        $.ajax({
            url : url_edit_service,
            method : "POST",
            data : objServiceUpdate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
                console.log('value', data);
                resetServicesForm();
                allServicesGrid();
                $('.toast_added').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    }
    document.getElementById("idService").innerHTML = 0;    
}); 

function resetServicesForm() {
    $('#valid_from').val('');
    $('#valid_to').val('');
    $('#product_name').val('');
    $('#id_dept').val('');
    $('#id_countries').val('');
    $('#id_coasts').val('');
    $('#service_name').val('');
    $('#id_tax').val('');
    $('#charges').val('');
    $('#duration').val('');
    $('#transfer_included').val('');
    $('#description').val('');
    $('#comments').val('');
    $('#cancellation').val('');
    $('#age_inf_to').val('');
    $('#age_child_to').val('');
    $('#age_teen_to').val('');
    $('#min_pax').val('');
    $('#max_pax').val('');
    $("#on_monday").prop("checked", false);
    $("#on_tuesday").prop("checked", false);
    $("#on_wednesday").prop("checked", false);
    $("#on_thursday").prop("checked", false);
    $("#on_friday").prop("checked", false);
    $("#on_saturday").prop("checked", false);
    $("#on_sunday").prop("checked", false);
}
