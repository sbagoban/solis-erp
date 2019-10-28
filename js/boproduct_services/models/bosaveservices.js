$(document).ready(function(){
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var product_name = urlParams.get("product_name");
    $('#product_name').val(product_name);
    var dateToday = new Date(); 
	$('#daterangeServiceFromTo').daterangepicker({
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		minDate: dateToday,
		locale: {
					format: 'DD/MM/YYYY'
				}
	});
});

$('#btn-saveProductServices').click(function () {
    var idService = document.getElementById("idService").innerHTML;
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var id_product = urlParams.get("id_product"); 
    var product_name = urlParams.get("product_name");
	var valid_from = $("#daterangeServiceFromTo").data('daterangepicker').startDate.format('YYYY-MM-DD');
	var valid_to = $("#daterangeServiceFromTo").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var product_name = product_name;
    var id_dept = $('#id_dept').val();
    var id_country = $('#id_country').val();
    var id_coast = $('#id_coast').val();
    var service_name = $('#service_name').val();    
    var id_tax = $('#id_tax').val();
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

    var id_creditor = $('#id_creditor').val();

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
            for_teen : for_teen
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
        const url_edit_service = "php/api/backofficeproduct/updateservice.php?t=" + encodeURIComponent(global_token) + "&id_product_service=" + idService;
        var objServiceUpdate = {
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
            min_pax : min_pax,
            max_pax : max_pax,
            id_creditor : id_creditor,
            for_infant : for_infant,
            for_child : for_child,
            for_teen : for_teen,
            for_infant : for_infant,
            for_child : for_child,
            for_teen : for_teen,            
            for_infant : for_infant,
            for_child : for_child,
            for_teen : for_teen,
            age_inf_from : age_inf_from,
            age_child_from : age_child_from,
            age_teen_from : age_teen_from
        };
        $.ajax({
            url : url_edit_service,
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
		allServicesGrid();
    }
    document.getElementById("idService").innerHTML = 0;    
}); 

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
    $('#transfer_included').val('');
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

    $("#age_inf_from").prop("readonly", true);
    $("#age_inf_to").prop("readonly", true);
    $("#age_child_from").prop("readonly", true);
    $("#age_child_to").prop("readonly", true);
    $("#age_teen_from").prop("readonly", true);
    $("#age_teen_to").prop("readonly", true);
    $('#id_creditor').val('');
}
