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

                        $("#accom_room").append('<option value="' + value + '"  name="' + txt + '">'+txt+'</option>');
                        $("#accom_room").val('0');
                        $('#accom_room').select2().trigger('change');	
                    }

                });
                resetClient();
               // validationValueAccomContract(id_booking);
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
    
    // Client
	$('#accom_client').change(function() {
        if ($('#accom_client').val() != 0 ) {
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
    allBookingAccom(dataDetails.id_booking)
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
    if ($("#accom_bookingDate").val() != "" && $("#accom_paidBy").val() != "" 
    && $("#accom_payer").val() != 0 && $("#accom_payer").val() != "" 
    && $("#accom_stay").val() != "" && $("#accom_night").val() != "" 
    && $("#accom_hotel").val() != 0 && $("#accom_mealPlan").val() != 0 
    && $("#accom_room").val() != 0 && $("#accom_room").val() != "" 
    && $("#accom_client").val() != 0 && $("#accom_client").val() != "") 
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
            loadAccomTarif(data, max_pax);
        },
        error: function (error) 
        {
            console.log('Error ${error}');
        }
    });
}



// .Load Accom Contract
function loadAccomTarif(data, max_pax) {
    var mealplan = $('#accom_mealPlan').val();
    var accom_payer = $('#accom_payer').val();
    var accom_hotel = $('#accom_hotel').val();
    var accom_room = $('#accom_room').val();
    var accom_stay_start = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var accom_stay_end = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var accom_bookingDate = $("#accom_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var travel_date = data[0].booking_from;
    var accom_client = $('#accom_client').val();
    var arr_params_resa = {
        mealplan : mealplan,
        touroperator : accom_payer,
        hotel :  accom_hotel,
        hotelroom : accom_room,
        checkin_date: accom_stay_start,
        checkout_date: accom_stay_end,
        booking_date : accom_bookingDate,
        travel_date : travel_date,
        max_pax : max_pax, 
        checkin_time : '', 
        checkout_time : '', 
        suppmealplan : '', 
        wedding_interested: 0,
        arr_pax: [accom_client]
    }

    const getAccomContract= "php/api/bookingSystem/accomContract.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : getAccomContract,
        method : "POST",
        data : arr_params_resa, 
        dataType: "json",
            success : function(contractData){
                if (contractData.OUTCOME == "OK") {
                    gridAccomDetails(contractData);
                    //saveAccomDetails(data);
                } else if (data == "FAIL_NO_CONTRACT") {
                    toastr.warning('No Tarif found.');
                }
            }, 
            error: function (error) {
                console.log('Error ${error}');
            }
    });
}


// Client 
// Activity Client
$("#accom_client").on("changed.bs.select",function(e, clickedIndex, newValue, oldValue) {

    var numberOfClient = $("#accom_client :selected").length;
    var valueOfClient = $("#accom_client").val();
    if (numberOfClient == 0) {
            var clientCount = {
                pax_amt: 0,
                infant_amt: 0,
                child_amt: 0,
                teen_amt:0,
                adult_amt: 0
            }
            $("#activity_adultAmt").val("");
            $("#activity_teenAmt").val("");
            $("#activity_childAmt").val("");
            $("#activity_infantAmt").val("");
            
    } else {
        var clientCount = {
            pax_amt: valueOfClient.length,
            infant_amt: 0,
            child_amt: 0,
            teen_amt:0,
            adult_amt: 0
        }
    }
    $.each(valueOfClient, function (key, val) {
        if ($("#accom_hotel").val() == 0 || $("#accom_hotel").val() == null) {
                alert("Please Select A hotel");
                var inegibleClient = $('#accom_client option[value="'+val+'"]').val();
                $('#accom_client').find('[value='+inegibleClient+']').prop('selected', false);
                $("#accom_client").selectpicker('refresh');
        } else {
            const url_search_booking = "php/api/bookingSystem/readBookingClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_client=" +val;
            $.ajax({
                url: url_search_booking,
                method: "POST",
                dataType: "json",
                success: function (clientData) {
                    var id_booking = $("#id_booking").val();
                    clientType = clientData[0].type;

                    if (clientData[0].age == null) {
                        // no age
                    } else {
                        // readBooking
                        const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
                        $.ajax({
                            url: url_search_booking,
                            method: "POST",
                            dataType: "json",
                            success: function (dataRead)
                            {

                                var max_pax = parseInt(dataRead[0].adult_amt) +  parseInt(dataRead[0].teen_amt) +  parseInt(dataRead[0].child_amt) +  parseInt(dataRead[0].infant_amt);
                                var travelDate = dataRead[0].booking_from;
                                
                                // Load AcommContract
                                var mealplan = $('#accom_mealPlan').val();
                                var accom_payer = $('#accom_payer').val();
                                var accom_hotel = $('#accom_hotel').val();
                                var accom_room = $('#accom_room').val();
                                var accom_stay_start = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
                                var accom_stay_end = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
                                var accom_bookingDate = $("#accom_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
                                var travel_date = dataRead[0].booking_from;
                                var accom_client = $('#accom_client').val();
                                var arr_params_resa = {
                                    mealplan : mealplan,
                                    touroperator : accom_payer,
                                    hotel :  accom_hotel,
                                    hotelroom : accom_room,
                                    checkin_date: accom_stay_start,
                                    checkout_date: accom_stay_end,
                                    booking_date : accom_bookingDate,
                                    travel_date : travel_date,
                                    max_pax : max_pax, 
                                    checkin_time : '', 
                                    checkout_time : '', 
                                    suppmealplan : '', 
                                    wedding_interested: 0,
                                    arr_pax: [accom_client]
                                }
                            
                                const getAccomContract= "php/api/bookingSystem/accomContract.php?t=" + encodeURIComponent(global_token);
                                $.ajax({
                                    url : getAccomContract,
                                    method : "POST",
                                    data : arr_params_resa, 
                                    dataType: "json",
                                        success : function(contractData) {
                                            console.log(contractData, 'asdfsf');
                                            if (contractData.OUTCOME == "OK") {
                                                gridAccomDetails(contractData);
                                                //saveAccomDetails(contractData);
                                                if (contractData.PAX_LIMITS_POSSIBILITIES.length != 0) {
                                                    // Age Policies
                                                    if (contractData.AGE_POLICIES.length == 0) {
                                                        if (clientData[0].type != 'ADULT') {
                                                            alert('Room Policy - Adult Only');
                                                            var inegibleClient = $('#accom_client option[value="'+val+'"]').val();
                                                            $('#accom_client').find('[value='+inegibleClient+']').prop('selected', false);
                                                            $("#accom_client").selectpicker('refresh');
                                                        } else {
                                                            var test = clientCount.adult_amt++;
                                                            $('#accom_adultAmt_1').val(test);
                                                        }
                                                    } else if (contractData.AGE_POLICIES.length == 1) {
                                                        if ((clientData[0].age > contractData.AGE_POLICIES[0].AGETO)) { // single line display ADULT
                                                            // adult
                                                            $('#accom_adultAmt_1').val(clientCount.adult_amt++);
                                                        } else {
                                                            // child
                                                            $('#accom_childAmt').val(clientCount.child_amt++);
                                                            // Display in Text Field Number of adult && Child selected
                                                        }
                                                    }  else if (contractData.AGE_POLICIES.length == 2) {
                                                        if ((clientData[0].age < contractData.AGE_POLICIES[0].AGETO)) {
                                                            // Infant
                                                            $('#accom_InfantAmt').val(clientCount.infant_amt++);
                                                        } else if ((clientData[0].age < contractData.AGE_POLICIES[1].AGETO)) {
                                                            // child
                                                            $('#accom_childAmt').val(clientCount.child_amt++);
                                                        } else { 
                                                            // Adult
                                                            $('#accom_adultAmt_1').val(clientCount.adult_amt++);
                                                        }
                                                    }  else if (contractData.AGE_POLICIES.length == 3) {
                                                        if ((clientData[0].age < contractData.AGE_POLICIES[0].AGETO)) {
                                                            // Infant
                                                            $('#accom_InfantAmt').val(clientCount.infant_amt++);
                                                        } else if ((clientData[0].age < contractData.AGE_POLICIES[1].AGETO)) {
                                                            // child
                                                            $('#accom_childAmt').val(clientCount.child_amt++);
                                                        } else if ((clientData[0].age < contractData.AGE_POLICIES[2].AGETO)) { 
                                                            // Teen
                                                            $('#accom_TeentAmt').val(clientCount.teen_amt++);
                                                        } else {
                                                            // Adult
                                                            $('#accom_adultAmt_1').val(clientCount.adult_amt++);
                                                        }
                                                    }
                                                } else {
                                                    alert("Pax Limit Reached.");
                                                    var inegibleClient = $('#accom_client option[value="'+val+'"]').val();
                                                    $('#accom_client').find('[value='+inegibleClient+']').prop('selected', false);
                                                    $("#accom_client").selectpicker('refresh');
                                                }
                                            }
                                            else if (contractData == "FAIL_NO_CONTRACT") {
                                                toastr.warning('No Tarif found.');
                                            }
                                        }, 
                                        error: function (error) {
                                            console.log('Error ${error}');
                                        }
                                });
                            },
                            error: function (error) 
                            {
                                console.log('Error ${error}');
                            }
                        });
                    }
                },
                error: function (error) {
                    console.log('----Error ${error}');
                }
            });
        }
    });

});
