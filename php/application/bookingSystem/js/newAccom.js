// JavaScript Document
/*Date : 2020, 09 January
Application : Activity - Booking Accom
Developer : slouis@solis360.com*/
var target_action = 'NULL';

$(function(){
	$('#accom_stay').val('');
    $("#rebateSection").hide();
    var id_booking = $('#id_booking').val();

    // Stay Period
    $('#accom_stay').on('apply.daterangepicker', function(ev, picker) {
        var in_date = picker.startDate;
        var out_date =  picker.endDate;
        var accom_in = new Date(in_date); 
        var accom_out = new Date(out_date); 
        var Difference_In_Time = accom_out.getTime() - accom_in.getTime(); 
        var accom_time = Difference_In_Time / (1000 * 3600 * 24); 
        var accom_nights = Math.round(accom_time)-1; 
        $('#accom_night').val(accom_nights);
        
        validationValueAccomContract(id_booking);
        
    });
    // .Stay Period
    
    // Hotel Change
	$('#accom_hotel').change(function(){
        var hoid = $('#accom_hotel').val();
        if ($('#accom_hotel').val() != 0 )
            {
                var dsRate = new dhtmlXDataStore();

                dsRate.load("php/api/ratescalculator/hotelroom_combo.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function ()
                {
                    $("#accom_room").empty();
                    $("#accom_room").append('<option value="0">None</option>');
                    for (var i = 0; i < dsRate.dataCount(); i++)
                    {
                        var item = dsRate.item(dsRate.idByIndex(i));
                        var value = item.value;
                        var txt = item.text;

                        $("#accom_room").append('<option value="' + value + '">'+txt+'</option>');
                        $("#accom_room").val('0');
                        $('#accom_room').select2().trigger('change');	
                    }

                });
                resetClient();
                validationValueAccomContract(id_booking);
            }
	});
	// .Hotel Change
    
    // Meal Plan
	$('#accom_mealPlan').change(function(){
        if ($('#accom_mealPlan').val() != 0 )
            {
                validationValueAccomContract(id_booking);
            }
	});
    
    // Hotel Room
	$('#accom_room').change(function(){
        if ($('#accom_room').val() != 0 )
            {
                validationValueAccomContract(id_booking);
            }
	});
	// .Hotel Room
    
    
	//Rebate
	$("#accom_rebate").change(function(){
		if ($("#accom_rebate").val() == 'None')
			{
				$("#rebateSection").hide();
				$("#accom_approvedBy").val('');
				$('#accom_approvedBy').select2().trigger('change');
				$("#accom_approvedBy").prop("disabled", true);
				$("#accom_percentageRebate").val('');
				$("#accom_adultRebate").val('');
				$("#accom_teenRebate").val('');
				$("#accom_childRebate").val('');
				$("#accom_InfantRebate").val('');
			}
		else if ($("#activity_rebate").val() == 'Percentage')
			{
				$("#rebateSection").show();
				$("#accom_percentageRebate").show();
				$("#rebate_fix").hide();
				$("#accom_approvedBy").prop("disabled", false);
				$("#accom_adultRebate").val('');
				$("#accom_teenRebate").val('');
				$("#accom_childRebate").val('');
				$("#accom_InfantRebate").val('');
			}
		else if ($("#accom_rebate").val() == 'Fixed Tariff')
			{
				$("#rebateSection").show();
				$("#accom_percentageRebate").hide();
				$("#rebate_fix").show();
				$("#accom_approvedBy").prop("disabled", false);
				$("#accom_percentageRebate").val('');
			}
		else if ($("#accom_rebate").val() == 'FOC')
			{
				$("#accom_percentageRebate").hide();
				$("#rebateSection").hide();
				$("#accom_approvedBy").val('');
				$("#accom_approvedBy").prop("disabled", false);
				$('#accom_approvedBy').select2().trigger('change');
				$("#accom_percentageRebate").val('');
				$("#accom_adultRebate").val('');
				$("#accom_teenRebate").val('');
				$("#accom_childRebate").val('');
				$("#accom_InfantRebate").val('');
			}
	});
	//.Rebate
    
    // Room Combination
	/*$('#accom_room').change(function(){
        var arr_params_resa = {
                max_pax: 0,
                hotelroom: 0,
                checkin_date: 0,
            
        }   
        
        _rates_calculator_reservation_gen_room_combination()
    });*/
    // .Room Combination
	
});
// New Accom
function newAccom(dataDetails){
    target_action = dataDetails.action;
    console.log(target_action + "1");
	$('.bookingAccom').val('');
	$('.bookingAccom').val(null).trigger('change');
	$("#accom_status").val("QUOTE");
	$('#accom_status').select2().trigger('change'); 
	$("#accom_paidBy").val("TO");
	$('#accom_paidBy').select2().trigger('change'); 
	$("#accom_rebate").val("None");
	$('#accom_rebate').select2().trigger('change'); 
	$("#accom_hotel").val("0");
	$('#accom_hotel').select2().trigger('change'); 
	$("#accom_mealPlan").val("0");
	$("#accom_approvedBy").prop("disabled", true);
	$('#accom_stay').val('');
    // Booking Date
    var dateToday = new Date(); 
	$('#accom_bookingDate').daterangepicker({
		"singleDatePicker": true,
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		maxDate: dateToday,
		locale: {
					format: 'DD/MM/YYYY'
				}
	});
    // .Booking Date
	loadTourOperator(dataDetails);
}
// .New Activity

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
                    $("#accom_payer").append('<option value="' + val.id_client + '">'+val.title+ ' '+val.surname+' '+val.other_name+'</option>');
                    });  

                },
                error: function (error) 
                {
                    console.log('Error ${error}');
                }
            });
}
// .Booking Client

//Reset Client
function resetClient(){
    if (target_action  != 'READ')
        {
            $("#accom_client").val('default').selectpicker("refresh");    
            $("#accom_adultAmt").val(0);
            $("#accom_teenAmt").val(0);
            $("#accom_childAmt").val(0);
            $("#accom_infantAmt").val(0);
        }
}
//.Reset Client

// Validation Param Accom Contract
function validationValueAccomContract(id_booking){
    if ($("#accom_bookingDate").val() != "" && $("#accom_paidBy").val() != "" && $("#accom_payer").val() != 0 && $("#accom_payer").val() != "" && $("#accom_stay").val() != "" && $("#accom_night").val() != "" && $("#accom_hotel").val() != 0 && $("#accom_mealPlan").val() != 0 && $("#accom_room").val() != 0 && $("#accom_room").val() != "" ) 
    {
         loadAccomContract(id_booking);
    }
}
//. Validation Param Accom Contract

// Load Accom Contract
function loadAccomContract(id_booking){
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
    $.ajax({
        url: url_search_booking,
        method: "POST",
        dataType: "json",
        success: function (data) 
        {
            var max_pax = parseInt(data[0].adult_amt) +  parseInt(data[0].teen_amt) +  parseInt(data[0].child_amt) +  parseInt(data[0].infant_amt);
            var travelDate = data[0].booking_from;
            
            var arr_params_resa = {
                        mealplan : $('#accom_mealPlan').val(),
                        touroperator : $('#accom_payer').val(),
                        hotel :  $('#accom_hotel').val(),
                        hotelroom : $('#accom_room').val(),
                        checkin_date: $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        checkout_date: $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD'),
                        booking_date : $("#accom_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        travel_date : data[0].booking_from,
                        max_pax  : max_pax
                }
        
            const getAccomContract= "php/api/bookingSystem/accomContract.php?t=" + encodeURIComponent(global_token);
            $.ajax({
            url : getAccomContract,
            method : "POST",
            data : arr_params_resa, 
            dataType: "json",                                                                           
            success : function(data){
                data = JSON.parse(data);
                console.log(data);
            }
            });

        },
        error: function (error) 
        {
            console.log('Error ${error}');
        }
    });
   /* var max_pax = 4

    var arr_params_resa = {
                mealplan : $('#accom_mealPlan').val(),
                touroperator : $('#accom_payer').val(),
                hotel :  $('#accom_hotel').val(),
                hotelroom : $('#accom_room').val(),
                checkin_date: checkin_date,
                checkout_date: checkin_out,
                booking_date : '2020-',
                travel_date : $("#booking_travelDate").data('daterangepicker').startDate.format('YYYY-MM-DD'),
                max_pax  : max_pax
        }   */
}
// .Load Accom Contract

