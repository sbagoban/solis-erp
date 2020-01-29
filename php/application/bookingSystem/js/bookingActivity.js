// JavaScript Document
/*Date : 2019, 22 October
Application : Activity - Booking System
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
        else if (targetPanel == "serviceDetails" && $("#id_product_service_claim").val() == "")
            {
                 alert("No service selected");
            }
        else if (targetPanel == "serviceDetails" && $("#id_product_service_claim").val() != "")
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
    });
    /*
    if (targetPanel == "#serviceDetails" && $("#id_product_service_claim").val() == "")
        {
            console.log("Service Details");
            alert("No service selected");
            if($('#serviceDetails').hasClass('in') === true) {
                $("#serviceDetails").collapse('toggle');  
                $("#dossierService").collapse('toggle');  
            } 
        }
    else if (targetPanel == "#dossierService")
        {
          //  if ($(this).attr("aria-expanded"));
        }
    });*/

    
	var id_booking = $('#id_booking').val();
    var activityData = {
			id_booking: id_booking,
            action: target_action
    }   
	newActivity(activityData);
    allBookingActivity(activityData.id_booking);
    
    $('#btn-newActivity').click(function() {
        var activityData = {
                id_booking: id_booking,
                action: 'RESET'
        }   
		newActivity(activityData);
        allBookingActivity(activityData.id_booking);
        $("#dossierService").collapse('show');
        $("#serviceDetails").collapse('hide');
        
	});
    
    // Date
	var booking_from = $("#booking_travelDate").data('daterangepicker').startDate._d;
	var booking_to = $("#booking_travelDate").data('daterangepicker').endDate._d;

	// Activity Paid By
	$('#activity_paidBy').change(function()
		{
			$('#activity_date').val('');
		
	 		if ($('#activity_paidBy').val() == "TO")
				{
					loadTourOperator(activityData);
				}
				else if ($('#activity_paidBy').val() == "Client")
				{
                    loadBookingClient(activityData);
				}
		});
	//. Activity Paid By
    
	$('#activity_date').daterangepicker({
		"singleDatePicker": true,
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		minDate: booking_from,
		maxDate:booking_to,
		locale: {
					format: 'DD/MM/YYYY'
				}
	});
	//.Date
    
    // Activity Time
    $('#activity_time').timepicker({
		minuteStep: 1,
		showMeridian: false,
		defaultTime: false
    });
	// .Activity Time
    
    // Client
	$('#activity_client').selectpicker();
	const url_search_booking = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
		$.ajax({
			url: url_search_booking,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#activity_client").val('').selectpicker('refresh');
				$.each(data, function (key, val) {
				$("#activity_client").append('<option value="' + val.id_booking_client + '">'+val.title+ ' '+val.surname+' '+val.other_name+' - '+val.type+'</option>');
				});  
				$("#activity_client").selectpicker('refresh');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
	//.Client
    
    //Approved rebate List
	const url_search_rebateUserList = "php/api/users/approverebateUser.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_rebateUserList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#activity_claimApprovedBy").empty();
			$("#activity_claimApprovedBy").append('<option value="0">None</option>');
			$("#activity_costApprovedBy").empty();
			$("#activity_costApprovedBy").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#activity_claimApprovedBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#activity_claimApprovedBy").val('0');
			$('#activity_claimApprovedBy').select2().trigger('change');	
			$("#activity_costApprovedBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#activity_costApprovedBy").val('0');
			$('#activity_costApprovedBy').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Approved rebate List
});