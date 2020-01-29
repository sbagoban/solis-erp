// JavaScript Document
/*Date : 2019, 23 October
Application : Activity - Booking System
Developer : slouis@solis360.com*/
var target_action = 'NULL';

$(function(){
	$('#activity_date').val('');
    $("#claimRebateSection").hide();
    $("#costRebateSection").hide();
    
	// Activity Date
	$('#activity_date').on('apply.daterangepicker', function(ev, picker) {
        target_action = 'NULL';
		var activityFullDate = $("#activity_date").data('daterangepicker').startDate._d + ' ';
		var activityFullDay = activityFullDate.split(" ");
		var activity_day = activityFullDay[0];
        
		var activityData = {
			id_booking: $('#id_booking').val(),
			paid_by: $('#activity_paidBy').val(),
			payer: $('#activity_payer').val(),
			activity_date: $('#activity_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
			activity_day :activity_day
			
		}
       
        $("#activity_claim_rebate").val("None");
        $('#activity_claim_rebate').select2().trigger('change'); 
        $("#activity_claim_rebate").prop("disabled", true);
        $("#activity_cost_rebate").val("None");
        $('#activity_cost_rebate').select2().trigger('change'); 
        $("#activity_cost_rebate").prop("disabled", true);
        $("#activity_representative").val("0");
        $('#activity_representative').select2().trigger('change'); 
        $("#activity_representative").prop("disabled", true);
        $("#activity_pickupHotel").val("0");
        $('#activity_pickupHotel').select2().trigger('change'); 
        $("#activity_pickupHotel").prop("disabled", true);
        $("#activity_language").val("0");
        $('#activity_language').select2().trigger('change'); 
        $("#activity_language").prop("disabled", true);
        $("#activity_claimApprovedBy").val('0');
        $('#activity_claimApprovedBy').select2().trigger('change');	
        $("#activity_claimApprovedBy").prop("disabled", true);
        $("#activity_costApprovedBy").val('0');
        $('#activity_costApprovedBy').select2().trigger('change');	
        $("#activity_costApprovedBy").prop("disabled", true); 
       loadActivity(activityData);
	});
	//. Activity Date
    
    // Activity Type
	$('#activity_type').change(function(){
		var activityFullDate = $("#activity_date").data('daterangepicker').startDate._d + ' ';
		var activityFullDay = activityFullDate.split(" ");
		var activity_day = activityFullDay[0];
		var activityData = {
			id_booking: $('#id_booking').val(),
			id_dept: $('#booking_dept').val(),
			paid_by: $('#activity_paidBy').val(),
			payer: $('#activity_payer').val(),
			activity_date: $('#activity_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
			activity_day :activity_day,
			activity_product :$('#activity_type').val()
		}
        
        resetClient();
		loadActivityService(activityData);
        $("#activity_claim_rebate").val("None");
        $('#activity_claim_rebate').select2().trigger('change'); 
        $("#activity_claim_rebate").prop("disabled", true);
        $("#activity_cost_rebate").val("None");
        $('#activity_cost_rebate').select2().trigger('change'); 
        $("#activity_cost_rebate").prop("disabled", true);
        $("#activity_representative").val("0");
        $('#activity_representative').select2().trigger('change'); 
        $("#activity_representative").prop("disabled", true);
        $("#activity_pickupHotel").val("0");
        $('#activity_pickupHotel').select2().trigger('change'); 
        $("#activity_pickupHotel").prop("disabled", true);
        $("#activity_language").val("0");
        $('#activity_language').select2().trigger('change'); 
        $("#activity_language").prop("disabled", true);
        $("#activity_claimApprovedBy").val('0');
        $('#activity_claimApprovedBy').select2().trigger('change');	
        $("#activity_claimApprovedBy").prop("disabled", true);
        $("#activity_costApprovedBy").val('0');
        $('#activity_costApprovedBy').select2().trigger('change');	
        $("#activity_costApprovedBy").prop("disabled", true);
	});
	// .Activity Type
    
	// Activity Service
	$("#activity_service").change(function(){
		var activityFullDate = $("#activity_date").data('daterangepicker').startDate._d + ' ';
		var activityFullDay = activityFullDate.split(" ");
		var activity_day = activityFullDay[0];
		var activityData = {
			id_booking: $('#id_booking').val(),
			id_dept: $('#booking_dept').val(),
			paid_by: $('#activity_paidBy').val(),
			payer: $('#activity_payer').val(),
			activity_date: $('#activity_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
			activity_day :activity_day,
			activity_product :$('#activity_type').val(),
			activity_service :$('#activity_service').val()
		}
        resetClient();
        console.log(target_action+" action 2");
        if (target_action == 'NULL' || target_action == 'READ')
            {
                loadActivityClaim(activityData);
            }
	});
	//. Activity Service
    
    // Activity Client
	$("#activity_client").on("changed.bs.select",function(e, clickedIndex, newValue, oldValue) {
		var numberOfClient = $("#activity_client :selected").length;
		var valueOfClient = $("#activity_client").val();
        if (numberOfClient == 0)
        {
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
            
        }
        else
        {
		      var clientCount = {
			pax_amt: valueOfClient.length,
			infant_amt: 0,
			child_amt: 0,
			teen_amt:0,
			adult_amt: 0
		}
		      $.each(valueOfClient, function (key, val) {
			if ($("#activity_type").val() == null || $("#activity_service").val() == null )
				{
					alert("Select an activity");
					var inegibleClient = $('#activity_client option[value="'+val+'"]').val();
					$('#activity_client').find('[value='+inegibleClient+']').prop('selected', false);
					$("#activity_client").selectpicker('refresh');
					
				}
			else
				{
					const url_search_booking = "php/api/bookingSystem/readBookingClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_client=" +val;
					$.ajax({
						url: url_search_booking,
						method: "POST",
						dataType: "json",
						success: function (clientData) {
							if (clientData[0].age == null)
								{
									var id_service = $("#activity_service").val();
									const url_serviceClaim = "php/api/bookingSystem/activitySpecificService.php?t=" + encodeURIComponent(global_token) + "&id_service=" +id_service;
									$.ajax({
										url: url_serviceClaim,
										method: "GET",
										dataType: "json",
										success: function (activityData) 
										{
											var activity_maxPax = activityData[0].max_pax;
											var activity_forInfant = activityData[0].for_infant;
											var activity_forChild = activityData[0].for_child;
											var activity_forTeen = activityData[0].for_teen;
											var activity_forAdult = activityData[0].for_adult;
											if (activity_forAdult == 1 && clientData[0].type == "ADULT")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.adult_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.adult_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forTeen == 1 && clientData[0].type == "TEEN")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.teen_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.teen_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forChild == 1 && clientData[0].type == "CHILD")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.child_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.child_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forInfant == 1 && clientData[0].type == "INFANT")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.infant_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.infant_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else
												{
													var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
														alert(clientName+" as "+clientData[0].type+ " does not satisfy pax policy of the activity");
													var inegibleClient = $('#activity_client option[value="'+val+'"]').val();
													$('#activity_client').find('[value='+inegibleClient+']').prop('selected', false);
													$("#activity_client").selectpicker('refresh');
													clientCount.pax_amt -= 1;
												}
									$("#activity_adultAmt").val(clientCount.adult_amt);
									$("#activity_teenAmt").val(clientCount.teen_amt);
									$("#activity_childAmt").val(clientCount.child_amt);
									$("#activity_infantAmt").val(clientCount.infant_amt);
										}
									});
								}
							else
								{
									var age = parseInt(clientData[0].age);
									var yearMonth = clientData[0].yearMonth;
									if (yearMonth == "MONTH")
										{
											age = parseInt(age/12);
										}
									var client_type = clientData[0].type;
									var id_service = $("#activity_service").val();
									const url_serviceClaim = "php/api/bookingSystem/activitySpecificService.php?t=" + encodeURIComponent(global_token) + "&id_service=" +id_service;
									$.ajax({
										url: url_serviceClaim,
										method: "GET",
										dataType: "json",
										success: function (activityData)
										{
											var activity_maxPax = activityData[0].max_pax;
											var activity_minAge = activityData[0].min_age;
											var activity_maxAge = activityData[0].max_age;
											var activity_forInfant = activityData[0].for_infant;
											var activity_infantTo = activityData[0].age_inf_to;
											var activity_forChild = activityData[0].for_child;
											var activity_childTo = activityData[0].age_child_to;
											var activity_forTeen = activityData[0].for_teen;
											var activity_teenTo = activityData[0].age_teen_to;
											var activity_forAdult = activityData[0].for_adult;

											if (activity_minAge !=null && activity_minAge > age)
												{
													var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
													alert(clientName+" of "+age+ " years, does not satisfy min age requirement for the activity");
													var inegibleClient = $('#activity_client option[value="'+val+'"]').val();
													$('#activity_client').find('[value='+inegibleClient+']').prop('selected', false);
													$("#activity_client").selectpicker('refresh');
													clientCount.pax_amt = -1;
												}
											else if (activity_maxAge !=null && activity_maxAge < age)
												{
													var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
													alert(clientName+" of "+age+ " years, does not satisfy max age requirement for the activity");
													var inegibleClient = $('#activity_client option[value="'+val+'"]').val();
													$('#activity_client').find('[value='+inegibleClient+']').prop('selected', false);
													$("#activity_client").selectpicker('refresh');
													clientCount.pax_amt = -1;
												}
											else if (activity_forInfant == 1 && activity_infantTo != null && activity_infantTo >= age)
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.infant_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.infant_amt ++;
														}
													else
														{				
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}

												}
											else if (activity_forChild == 1 && activity_childTo != null && activity_childTo >= age)
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.child_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.child_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}

												}
											else if (activity_forTeen == 1 && activity_teenTo != null && activity_teenTo >= age)
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.teen_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.teen_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}

												}
											else if (activity_forTeen == 0 && activity_forAdult == 1 && activity_childTo != null && activity_childTo <= age)
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.adult_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.adult_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forAdult == 1 && activity_childTo != null && activity_childTo <= age && activity_teenTo != null && activity_teenTo <= age)
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.adult_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.adult_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forAdult == 1 && client_type == "ADULT")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.adult_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.adult_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forTeen == 1 && client_type == "TEEN")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.teen_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.teen_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forChild == 1 && client_type == "CHILD")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.child_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.child_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else if (activity_forInfant == 1 && client_type == "INFANT")
												{
													if (activity_maxPax != null && clientCount.pax_amt <= activity_maxPax)
														{
															clientCount.infant_amt ++;
														}
													else if (activity_maxPax == null)
														{
															clientCount.infant_amt ++;
														}
													else
														{												
															alert("Maximum pax for the activity already reached");
															var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
															$('#activity_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
															$("#activity_client").selectpicker('refresh');
															clientCount.pax_amt -= 1;
														}
												}
											else
												{
													var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
													alert(clientName+" as "+client_type+ " does not satisfy pax policy of the activity");
													var inegibleClient = $('#activity_client option[value="'+val+'"]').val();
													$('#activity_client').find('[value='+inegibleClient+']').prop('selected', false);
													$("#activity_client").selectpicker('refresh');
													clientCount.pax_amt -= 1;
												}
                                            
									$("#activity_adultAmt").val(clientCount.adult_amt);
									$("#activity_teenAmt").val(clientCount.teen_amt);
									$("#activity_childAmt").val(clientCount.child_amt);
									$("#activity_infantAmt").val(clientCount.infant_amt);
										}
									});
								}
						}

					});
				}
			
		});
        }
		
	});
	//. Activity Client
    
	//Rebate Claim
	$("#activity_claim_rebate").change(function(){
        if ($("#activity_claim_rebate").val() == 'None')
			{
				$("#claimRebateSection").hide();
				$("#activity_claimApprovedBy").val('');
				$('#activity_claimApprovedBy').select2().trigger('change');
				$("#activity_claimApprovedBy").prop("disabled", true);
				$("#activity_percentageClaimRebate").val('');
				$("#activity_adultClaimRebate").val('');
				$("#activity_teenClaimRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantClaimRebate").val('');
			}
		else if ($("#activity_claim_rebate").val() == 'Percentage')
			{
				$("#claimRebate_fix").hide();
				$("#claimRebateAmount").html('Claim Rebate %');
				$("#activity_claimApprovedBy").prop("disabled", false);
				$("#activity_adultClaimRebate").val('');
				$("#activity_teenClaimRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantClaimRebate").val('');
				$("#claimRebateSection").show();
				$("#activity_percentageClaimRebate").show();
			}
		else if ($("#activity_claim_rebate").val() == 'Fixed Tariff')
			{
				$("#activity_percentageClaimRebate").hide();
				$("#claimRebateAmount").html('Claim Rebate Tariff');
				$("#activity_claimApprovedBy").prop("disabled", false);
				$("#activity_percentageClaimRebate").val('');
				$("#claimRebateSection").show();
				$("#claimRebate_fix").show();
			}
		else if ($("#activity_claim_rebate").val() == 'FOC')
			{
				$("#activity_percentageClaimRebate").hide();
				$("#claimRebateSection").hide();
				$("#activity_claimApprovedBy").val('');
				$("#activity_claimApprovedBy").prop("disabled", false);
				$('#activity_claimApprovedBy').select2().trigger('change');
				$("#activity_percentageClaimRebate").val('');
				$("#activity_adultClaimRebate").val('');
				$("#activity_teenClaimRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantClaimRebate").val('');
			}
	});
	//.Rebate Claim
    
	//Rebate Cost
	$("#activity_cost_rebate").change(function(){
        if ($("#activity_cost_rebate").val() == 'None')
			{
				$("#costRebateSection").hide();
				$("#activity_costApprovedBy").val('');
				$('#activity_costApprovedBy').select2().trigger('change');
				$("#activity_costApprovedBy").prop("disabled", true);
				$("#activity_percentageCostRebate").val('');
				$("#activity_adultCostRebate").val('');
				$("#activity_teenCostRebate").val('');
				$("#activity_childCostRebate").val('');
				$("#activity_InfantCostRebate").val('');
			}
		else if ($("#activity_cost_rebate").val() == 'Percentage')
			{
				$("#costRebate_fix").hide();
				$("#costRebateAmount").html('Cost Rebate %');
				$("#activity_costApprovedBy").prop("disabled", false);
				$("#activity_adultCostRebate").val('');
				$("#activity_teenCostRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantCostRebate").val('');
				$("#costRebateSection").show();
				$("#activity_percentageCostRebate").show();
			}
		else if ($("#activity_cost_rebate").val() == 'Fixed Tariff')
			{
				$("#activity_percentageCostRebate").hide();
				$("#costRebateAmount").html('Cost Rebate Tariff');
				$("#activity_costApprovedBy").prop("disabled", false);
				$("#activity_percentageCostRebate").val('');
				$("#costRebateSection").show();
				$("#costRebate_fix").show();
			}
		else if ($("#activity_cost_rebate").val() == 'FOC')
			{
				$("#activity_percentageCostRebate").hide();
				$("#costRebateSection").hide();
				$("#activity_costApprovedBy").val('');
				$("#activity_costApprovedBy").prop("disabled", false);
				$('#activity_costApprovedBy').select2().trigger('change');
				$("#activity_percentageCostRebate").val('');
				$("#activity_adultCostRebate").val('');
				$("#activity_teenCostRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantCostRebate").val('');
			}
	});
	//.Rebate Cost
	
});

// New Activity
function newActivity(dataDetails){
    target_action = dataDetails.action;
    console.log(target_action + "1");
	$('.bookingActivity').val('');
	$('.bookingActivity').val(null).trigger('change');
	$("#activity_status").val("QUOTE");
	$('#activity_status').select2().trigger('change'); 
	$("#activity_paidBy").val("TO");
	$('#activity_paidBy').select2().trigger('change'); 
	$("#activity_claim_rebate").val("None");
	$('#activity_claim_rebate').select2().trigger('change'); 
	$("#activity_claim_rebate").prop("disabled", true);
	$("#activity_cost_rebate").val("None");
	$('#activity_cost_rebate').select2().trigger('change'); 
	$("#activity_cost_rebate").prop("disabled", true);
	$("#activity_representative").val("0");
	$('#activity_representative').select2().trigger('change'); 
	$("#activity_representative").prop("disabled", true);
	$("#activity_pickupHotel").val("0");
	$('#activity_pickupHotel').select2().trigger('change'); 
	$("#activity_pickupHotel").prop("disabled", true);
	$("#activity_language").val("0");
	$('#activity_language').select2().trigger('change'); 
	$("#activity_language").prop("disabled", true);
	$("#activity_claimApprovedBy").prop("disabled", true);
	$("#activity_costApprovedBy").prop("disabled", true);
	$('#activity_date').val('');
    // Booking Date
    var dateToday = new Date(); 
	$('#activity_bookingDate').daterangepicker({
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
function loadTourOperator(activityData){
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking;
		$.ajax({
			url: url_search_booking,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#activity_payer").empty();
				$("#activity_payer").append('<option value="' + data[0].id_tour_operator + '">'+data[0].to_name+'</option>');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
    
}
// .Tour Operator

// Booking Client
function loadBookingClient(activityData){
         const url_search_booking = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking;
            $.ajax({
                url: url_search_booking,
                method: "POST",
                dataType: "json",
                success: function (data) 
                {
                    $("#activity_payer").empty();
                    $.each(data, function (key, val) {
                    $("#activity_payer").append('<option value="' + val.id_client + '">'+val.title+ ' '+val.surname+' '+val.other_name+'</option>');
                    });  

                },
                error: function (error) 
                {
                    console.log('Error ${error}');
                }
            });
}
// .Booking Client

// Product
function loadActivity(activityData){
    const url_product = "php/api/bookingSystem/activityList.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking;
		$.ajax({
			url: url_product,
			method: "POST",
			dataType: "json",
        	data : activityData, 
			success: function (data) 
			{
				$("#activity_type").empty();
				$("#activity_type").append('<option value="" selected disabled>Select</option>');
				$.each(data, function (key, val) {
				$("#activity_type").append('<option value="' + val.id_product + '">'+val.product_name+ '</option>');
				}); 

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
}
// .Product

//Reset Client
function resetClient(){
    if (target_action  != 'READ')
        {
            $("#activity_client").val('default').selectpicker("refresh");    
            $("#activity_adultAmt").val(0);
            $("#activity_teenAmt").val(0);
            $("#activity_childAmt").val(0);
            $("#activity_infantAmt").val(0);
        }
}
//.Reset Client

// Product Service
function loadActivityService(activityData){
	 const url_productService = "php/api/bookingSystem/activityServiceList.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking;
		$.ajax({
			url: url_productService,
			method: "POST",
			dataType: "json",
        	data : activityData, 
			success: function (data) 
			{
				$("#activity_service").empty();
				$("#activity_service").append('<option value="" selected disabled>Select</option>');
				$.each(data, function (key, val) {
				$("#activity_service").append('<option value="' + val.id_product_service + '">'+val.service_name+ '</option>');
				}); 

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
}
// .Product Service

// Activity Claim
function loadActivityClaim(activityData){
	 const url_serviceClaim = "php/api/bookingSystem/activityServiceClaim.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking;
		$.ajax({
			url: url_serviceClaim,
			method: "POST",
			dataType: "json",
        	data : activityData, 
			success: function (data) 
			{
                if (data[0].OUTCOME == 'OK')
                {
                    $("#id_product_service_claim").val(data[0].id_product_service_claim);
                    var charge = data[0].charge;
                    if (charge == 'PAX')
                        {
                            
                            $(".unit_charge").hide();
                            $(".pax_charge").show();
                            $(".pax_charge").show();
                            $("#activity_teenClaimRebate").show();
                            $("#activity_teenCostRebate").show();
                            $(".rebateTeen").show();
                            $("#activity_childClaimRebate").show();
                            $("#activity_childCostRebate").show();
                            $(".rebateChild").show();
                            $("#activity_InfantClaimRebate").show();
                            $("#activity_InfantCostRebate").show();
                            $(".rebateInfant").show();
                            $(".rebateAdult").html();
                            $(".rebateAdult").html('Adult');
                            $("#min_pax").html(data[0].min_pax);
                            $("#max_pax").html(data[0].max_pax);
                            if(data[0].for_adult == 0)
                                {
                                    $(".adult_details").hide();
                                    $("#adult_policy").html("");
                                    $("#rebateAdult").hide();
                                    $("#activity_adultClaimRebate").hide();
                                    $("#activity_adultCostRebate").hide();
                                }
                            else
                                {
                                    $(".adult_details").show();
                                }
                            if(data[0].for_teen == 0)
                                {
                                    $(".teen_details").hide();
                                    $("#teen_policy").html("")
                                    $("#activity_teenClaimRebate").hide();
                                    $("#activity_teenCostRebate").hide();
                                }
                            else
                                {
                                    $(".teen_details").show();
                                    if(data[0].age_teen_from != null)
                                        {
                                            $("#teen_policy").html("Age from <b>"+data[0].age_teen_from+"</b> to <b>"+data[0].age_teen_to+"</b>");
                                        }
                                }
                            if(data[0].for_child == 0)
                                {
                                    $(".child_details").hide();
                                    $("#child_policy").html("");
                                    $("#activity_childClaimRebate").hide();
                                    $("#activity_childCostRebate").hide();
                                }
                            else
                                {
                                    $(".child_details").show();
                                    if(data[0].age_child_from != null)
                                        {
                                            $("#child_policy").html("Age from <b>"+data[0].age_child_from+"</b> to <b>"+data[0].age_child_to+"</b>");
                                        }
                                }
                            if(data[0].for_infant == 0)
                                {
                                    $(".infant_details").hide();
                                    $("#infant_policy").html("");
                                    $("#activity_InfantClaimRebate").hide();
                                    $("#activity_InfantCostRebate").hide();
                                }
                            else
                                {
                                    $(".infant_details").show();
                                    if(data[0].age_inf_from != null)
                                        {
                                            $("#infant_policy").html("Age from <b>"+data[0].age_inf_from+"</b> to <b>"+data[0].age_inf_to+"</b>");
                                        }
                                }

                            $(".ps_claim_cur").html(" "+data[0].claim_curr);
                            $(".ps_cost_cur").html(" "+data[0].cost_curr);
                            $("#ps_adult_claim").html(data[0].ps_adult_claim);
                            $("#ps_adult_cost").html(data[0].ps_adult_cost);
                            $("#age_teen_from").html(data[0].age_teen_from);
                            $("#age_teen_to").html(data[0].age_teen_to);
                            $("#ps_teen_claim").html(data[0].ps_teen_claim);
                            $("#ps_teen_cost").html(data[0].ps_teen_cost);
                            $("#age_child_from").html(data[0].age_child_from);
                            $("#age_child_to").html(data[0].age_child_to);
                            $("#ps_child_claim").html(data[0].ps_child_claim);
                            $("#ps_child_cost").html(data[0].ps_child_cost);
                            $("#age_inf_from").html(data[0].age_inf_from);
                            $("#age_inf_to").html(data[0].age_inf_to);
                            $("#ps_infant_claim").html(data[0].ps_infant_claim);
                            $("#ps_infant_cost").html(data[0].ps_infant_cost);

                        }
                    else if (charge == 'UNIT')
                        {
                            $(".unit_charge").show();
                            $(".pax_charge").hide();
                            $(".pax_charge").hide();
                            $("#activity_teenClaimRebate").hide();
                            $("#activity_teenCostRebate").hide();
                            $(".rebateTeen").hide();
                            $("#activity_childClaimRebate").hide();
                            $("#activity_childCostRebate").hide();
                            $(".rebateChild").hide();
                            $("#activity_InfantClaimRebate").hide();
                            $("#activity_InfantCostRebate").hide();
                            $(".rebateInfant").hide();
                            $("#rebateAdult").html();
                            $(".rebateAdult").html('Unit');
                            $("#min_pax_unit").html(data[0].min_pax);
                            $("#max_pax_unit").html(data[0].max_pax);
                            $(".ps_claim_cur").html(" "+data[0].claim_curr);
                            $(".ps_cost_cur").html(" "+data[0].cost_curr);
                            $("#ps_unit_claim").html(data[0].ps_adult_claim);
                            $("#ps_unit_cost").html(data[0].ps_adult_cost);
                        }
                    $("#activty_desc").html(data[0].description);
                    var pax_eligible = '';
                    var pax_eligibleCount = 0;
                    var pax_notEligible = '';
                    var pax_notEligibleCount = 0;
                    if(data[0].for_adult == 1)
                        {
                            pax_eligibleCount ++;
                            pax_eligible +="Adult";
                        }
                    else
                        {
                            pax_notEligibleCount ++;
                            pax_notEligible +="Adult";
                        }

                    if(data[0].for_teen == 1)
                        {
                            pax_eligibleCount ++;
                            if (pax_eligible == '')
                                {
                                     pax_eligible +="Teen";
                                }
                            else
                                {
                                     pax_eligible +=" ,Teen";
                                }
                        }
                    else
                        {
                            pax_notEligibleCount ++;
                            if (pax_notEligible == '')
                                {
                                     pax_notEligible +="Teen";
                                }
                            else
                                {
                                     pax_notEligible +=" ,Teen";
                                }
                        }

                    if(data[0].for_child == 1)
                        {
                            pax_eligibleCount ++;
                            if (pax_eligible == '')
                                {
                                     pax_eligible +="Child";
                                }
                            else
                                {
                                     pax_eligible +=" ,Child";
                                }
                        }
                    else
                        {
                            pax_notEligibleCount ++;
                            if (pax_notEligible == '')
                                {
                                     pax_notEligible +="Child";
                                }
                            else
                                {
                                     pax_notEligible +=" ,Child";
                                }
                        }

                    if(data[0].for_infant == 1)
                        {
                            pax_eligibleCount ++;
                            if (pax_eligible == '')
                                {
                                     pax_eligible +="Infant";
                                }
                            else
                                {
                                     pax_eligible +=" ,Infant";
                                }
                        }
                    else
                        {
                            pax_notEligibleCount ++;
                            if (pax_notEligible == '')
                                {
                                     pax_notEligible +="Infant";
                                }
                            else
                                {
                                     pax_notEligible +=" ,Infant";
                                }
                        }

                    if (pax_eligible != '')
                        {
                             pax_eligible +=" only are accepted";
                        }

                    if (pax_notEligible != '')
                        {
                             pax_notEligible +=" are not accpeted";
                        }

                    if (pax_notEligibleCount < pax_eligibleCount)
                        {
                            $("#activty_policy").html(pax_notEligible);
                        }
                    else if (pax_notEligibleCount > pax_eligibleCount)
                        {
                            $("#activty_policy").html(pax_eligible);
                        }
                    else
                        {
                            $("#activty_policy").html(pax_eligible);
                        }
                  //  $(".panel-collapse").collapse('toggle');
                    
                    loadActivityExtra(data[0].id_product_service_claim);
                    if($('#serviceDetails').hasClass('in') === false) {
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                    $("#activity_claim_rebate").prop("disabled", false);
                    $("#activity_cost_rebate").prop("disabled", false);
                    $("#activity_representative").prop("disabled", false);
                    $("#activity_pickupHotel").prop("disabled", false);
                    $("#activity_language").prop("disabled", false);
                }
                else
                 {
                     toastr.warning('An error occured. Please do it again');
                 }
			},
			error: function (error) 
			{
                toastr.warning('An error occured. Please do it again 2');
				console.log('Error ${error}');
			}
		});
}
// .Activity Claim



