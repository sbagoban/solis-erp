// JavaScript Document
/*Date : 2020, 09 January
Application : Activity - Booking Accom
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
    var accomData = {
			id_booking: id_booking,
            action: target_action
    }   
    
	newAccom(accomData);
    
    // Date
	var booking_from = $("#booking_travelDate").data('daterangepicker').startDate._d;
	var booking_to = $("#booking_travelDate").data('daterangepicker').endDate._d;
    
	// Accom Paid By
	$('#accom_paidBy').change(function()
		{
			$('#accom_stay').val('');
		
	 		if ($('#accom_paidBy').val() == "TO")
				{
					loadTourOperator(accomData);
				}
				else if ($('#accom_paidBy').val() == "Client")
				{
                    loadBookingClient(accomData);
				}
		});
	//. Accom Paid By  
    
    // Accom Stay
    $('#accom_stay').daterangepicker({
        "showDropdowns": true,
        "autoApply": true,
        "opens": "center",
        minDate: booking_from,
        maxDate:booking_to,
        locale: {
                    format: 'DD/MM/YYYY'
                }
    });
    // .Accom Stay
    
    //Hotel
	const url_search_hotelList = "php/api/hotel/allHotel.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_hotelList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#accom_hotel").empty();
			$("#accom_hotel").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#accom_hotel").append('<option value="' + val.id + '"  name="' + val.hotelname + '">'+val.hotelname+'</option>');
			$("#accom_hotel").val('0');
			$('#accom_hotel').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Hotel
    
    // Meal Plan
	const url_search_mealplan = "php/api/mealPlans/allMealPlan.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_mealplan,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#accom_mealPlan").empty();
			$("#accom_mealPlan").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#accom_mealPlan").append('<option value="' + val.id + '">'+val.meal_full_name+'</option>');
			$("#accom_mealPlan").val('0');
			$('#accom_mealPlan').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
    // Meal Plan
    
    // Client
	$('#accom_client').selectpicker();
	const url_search_client = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +accomData.id_booking;
		$.ajax({
			url: url_search_client,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#accom_client").val('').selectpicker('refresh');
				$.each(data, function (key, val) {
				$("#accom_client").append('<option value="' + val.id_booking_client + '" name="' + val.age + '">'+val.title+ ' '+val.surname+' '+val.other_name+' - '+val.type+'</option>');
				});  
				$("#accom_client").selectpicker('refresh');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
	//.Client
    
    //Approved Discount List
	const url_search_discountUserList = "php/api/users/approveDiscountUser.php?t=" + encodeURIComponent(global_token);
	$.ajax({
		url: url_search_discountUserList,
		method: "POST",
		dataType: "json",
		success: function (data) 
		{
			$("#accom_approvedBy").empty();
			$("#accom_approvedBy").append('<option value="0">None</option>');
			$.each(data, function (key, val) {
			$("#accom_approvedBy").append('<option value="' + val.id_user + '">'+val.full_name+'</option>');
			$("#accom_approvedBy").val('0');
			$('#accom_approvedBy').select2().trigger('change');	
			}); 

		},
		error: function (error) 
		{
			console.log('Error ${error}');
		}
	});
	//.Approved Discount List
});

// Tour Operator
function loadTourOperator(accomData){
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +accomData.id_booking;
		$.ajax({
			url: url_search_booking,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#accom_payer").empty();
				$("#accom_payer").append('<option value="' + data[0].id_tour_operator + '">'+data[0].to_name+'</option>');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
    
}
// .Tour Operator

// Booking Client
function loadBookingClient(accomData){
         const url_search_booking = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +accomData.id_booking;
            $.ajax({
                url: url_search_booking,
                method: "POST",
                dataType: "json",
                success: function (data) 
                {
                    $("#accom_payer").empty();
                    $.each(data, function (key, val) {
                    	$("#accom_payer").append('<option value="' + val.id_client + '" name="' + val.age + '">'+val.title+ ' '+val.surname+' '+val.other_name+'</option>');
                    });  
                },
                error: function (error) 
                {
                    console.log('Error ${error}');
                }
            });
}
// .Booking Client
