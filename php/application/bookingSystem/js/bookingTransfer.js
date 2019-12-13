// JavaScript Document
/*Date : 2019, 03 December
Application : Transfer - Booking System
Developer : slouis@solis360.com*/
$(function(){
var target_action = 'NULL';
    $('.select2').select2({
		placeholder : 'Select'
	});
    
    $("#dossierService").collapse('show');
    $("#serviceDetails").collapse('hide');
    $(".panel-title").click(function(){
        $(".panel-collapse").collapse('toggle');
    });
    
	var id_booking = $('#id_booking').val();
    var transferData = {
			id_booking: id_booking,
            action: target_action
    }   
    newTransfer(transferData);
 /* //  allBookingTransfer(transferData.id_booking);*/
    
    // Transfer Product
        const url_product = "php/api/bookingSystem/transferList.php?t=" + encodeURIComponent(global_token) ;
        $.ajax({
            url: url_product,
            dataType: "json",
            success: function (data) 
            {
                $("#transfer_vehicle").empty();
                $("#transfer_vehicle").append('<option value="0">Select</option>');
                $.each(data, function (key, val) {
                $("#transfer_vehicle").append('<option value="' + val.id_product + '">'+val.product_name+ '</option>');
                $("#transfer_vehicle").val('0');
                $('#transfer_vehicle').select2().trigger('change');	
                }); 

            },
            error: function (error) 
            {
                console.log('Error ${error}');
            }
        });
    // .Transfer Product
        
    // Transfer Arrival Date and Departure Date
    $('#transfer_arrivalDate').datepicker(
    {
        format: 'dd/mm/yyyy',
        startDate: '-30d',
        changeMonth: true,
        changeYear: true
    }).on('changeDate', function(selected) {
        $('#transfer_departureDate').val('');
        $('#transfer_departureFlight').val('');
        $('#transfer_departureTime').val('');
        startDate = new Date(selected.date.valueOf());
        $('#transfer_departureDate').datepicker('setStartDate', startDate);
    });
    // .Transfer Arrival Date and Departure Date
  
    // Transfer Arrival Time and Departure Time
    $('#transfer_arrivalTime').timepicker({
		minuteStep: 1,
		showMeridian: false,
		defaultTime: false
    });
    
    $('#transfer_departureTime').timepicker({
		minuteStep: 1,
		showMeridian: false,
		defaultTime: false
    });
	// .Transfer Arrival Time and Departure Time
    
	//Destination From 
	const url_search_hotelList = "php/api/hotel/allHotel.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_hotelList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#transfer_destination").empty();
			$("#transfer_destination").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#transfer_destination").append('<option value="' + val.id + '">'+val.hotelname+'</option>');
			$("#transfer_destination").val('0');
			$('#transfer_destination').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Destination From 
    
    // Client
	$('#transfer_client').selectpicker();
	const url_search_booking = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking;
		$.ajax({
			url: url_search_booking,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#transfer_client").val('').selectpicker('refresh');
				$.each(data, function (key, val) {
				$("#transfer_client").append('<option value="' + val.id_booking_client + '">'+val.title+ ' '+val.surname+' '+val.other_name+' - '+val.type+'</option>');
				});  
				$("#transfer_client").selectpicker('refresh');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
	//.Client
    
    
    /*
    
    $('#btn-newTransfer').click(function() {
        var transferData = {
                id_booking: id_booking,
                action: 'RESET'
        }   
	//	newTransfer(transferData);
    //    allBookingTransfer(transferData.id_booking);
        $("#dossierService").collapse('show');
        $("#serviceDetails").collapse('hide');
        
	});
   
    //Approved Discount List
	const url_search_discountUserList = "php/api/users/approveDiscountUser.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_discountUserList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#activity_approvedBy").empty();
			$("#activity_approvedBy").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#activity_approvedBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#activity_approvedBy").val('0');
			$('#activity_approvedBy').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Approved Discount List*/
});