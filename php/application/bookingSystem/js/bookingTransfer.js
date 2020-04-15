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
    $('.panel-title').click(function(){
        var targetPanel = $(this).attr("panel");
        if (targetPanel == "dossierService")
            {
                if($('#dossierService').hasClass('in') === false)
                {
                    $("#serviceDetails").collapse('toggle');  
                    $("#dossierService").collapse('toggle');  
                } 
            }
        else if (targetPanel == "serviceDetails")
        {
            if ($("#id_product_service_arr_claim").val() =="" && $("#id_product_service_dep_claim").val() == "")
                {
                     alert("No service selected 2");
                }
            else if ($("#id_product_service_arr_claim").val() != "" || $("#id_product_service_dep_claim").val() != "")
                {
                    if($('#dossierService').hasClass('in') === true && $('#serviceDetails').hasClass('in') === false )
                    {
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    }
                    else if ($('#dossierService').hasClass('in') === false && $('#serviceDetails').hasClass('in') === false )
                    {
                        $("#serviceDetails").collapse('toggle');  
                    }
                    else if($('#dossierService').hasClass('in') === true && $('#serviceDetails').hasClass('in') === true )
                    {
                        $("#dossierService").collapse('toggle');  
                    }
                }
        }
        
    });
    
	var id_booking = $('#id_booking').val();
    var transferData = {
			id_booking: id_booking,
            action: target_action
    }   
    
    newTransfer(transferData);
    allBookingTransfer(transferData.id_booking);
    
      
    $('#btn-newTransfer').click(function() {
        var transferData = {
                id_booking: id_booking,
                action: 'RESET'
        }   
		newTransfer(transferData);
        allBookingTransfer(transferData.id_booking);
        $("#dossierService").collapse('show');
        $("#serviceDetails").collapse('hide');
        
	});
    
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
    $(".destinationFrom").hide();
    $(".destinationTo").hide();
    $("#transfer_destination_from").val('0');
    $('#transfer_destination_from').select2().trigger('change');	
    $("#transfer_destination_to").val('0');
    $('#transfer_destination_to').select2().trigger('change');	
    
	const url_search_hotelList = "php/api/hotel/allHotel.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_hotelList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#transfer_destination_from").empty();
			$("#transfer_destination_to").empty();
			$("#transfer_destination_from").append('<option value="0">None</option>');
			$("#transfer_destination_to").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#transfer_destination_from").append('<option value="' + val.id + '">'+val.hotelname+'</option>');
			$("#transfer_destination_to").append('<option value="' + val.id + '">'+val.hotelname+'</option>');
			$("#transfer_destination_from").val('0');
			$('#transfer_destination_from').select2().trigger('change');	
			$("#transfer_destination_to").val('0');
			$('#transfer_destination_to').select2().trigger('change');	
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
    
    //Approved Discount List
	const url_search_discountUserList = "php/api/users/approveRebateUser.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_discountUserList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#transfer_rebateClaimApproveBy").empty();
			$("#transfer_costApprovedBy").empty();
			$("#transfer_rebateClaimApproveBy").append('<option value="0">None</option>');
			$("#transfer_costApprovedBy").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#transfer_rebateClaimApproveBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#transfer_rebateClaimApproveBy").val('0');
			$('#transfer_rebateClaimApproveBy').select2().trigger('change');	
			$("#transfer_costApprovedBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#transfer_costApprovedBy").val('0');
			$('#transfer_costApprovedBy').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Approved Discount List
    
});
