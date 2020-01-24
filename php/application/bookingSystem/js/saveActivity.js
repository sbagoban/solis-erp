// JavaScript Document
/*Date : 2019, 6 November
Application : Activity - Booking System
Developer : slouis@solis360.com*/

$(function(){
	// Save Booking
	$('#btn-saveActivity').click(
	function()
	{
        var saveError = false;
        if ($(".rebateAdult").html() == 'Unit')
            {
                var claimType = 'UNIT';
            }
        else
            {
                var claimType = 'PAX';
            }
		var adultClaim = $("#ps_adult_claim").html();
		var unitClaim = $("#ps_unit_claim").html();
		var teenClaim = $("#ps_teen_claim").html();
		var childClaim = $("#ps_child_claim").html();
		var infantClaim = $("#ps_infant_claim").html();
        
		if($('#activity_paidBy').val() == "" || $('#activity_paidBy').val() == 0)
			{
				alert("Select payer type");
				saveError = true;
			}
		else if ($('#activity_payer').val() == "" || $('#activity_payer').val() == 0)
			{
				if($('#activity_paidBy').val() == "TO")
					{
						alert("Select paying agency or reload activity form");
					}
				else
					{
						alert("Select paying client or reload activity form");
					}
				saveError = true;
			}
		else if($('#activity_date').val() == "" || $('#activity_date').val() == 0)
			{
				alert("Select activity date");
				saveError = true;
			}
		else if($('#activity_type').val() == "" || $('#activity_type').val() == 0)
			{
				alert("Select activity service");
				saveError = true;
			}
		else if($('#activity_service').val() == "" || $('#activity_service').val() == 0)
			{
				alert("Select activity service");
				saveError = true;
			}
		else if($('#activity_client').val() == "" || $('#activity_client').val() == 0)
			{
				alert("Select client for the activity");
				saveError = true;
			}
		else if($('#activity_adultAmt').val() == ""  || $('#activity_teenAmt').val() == "" || $('#activity_childAmt').val() == ""  || $('#activity_infantAmt').val() == "")
			{
				alert("Select your client");
				saveError = true;
			}
		else if($('#activity_adultAmt').val() == 0  && $('#activity_teenAmt').val() == 0 && $('#activity_childAmt').val() == 0  && $('#activity_infantAmt').val() == 0)
			{
				alert("Select your client");
				saveError = true;
			}
		else if($('#activity_rebate').val() != "None")
			{
				if ($('#activity_approvedBy').val() == "" ||$('#activity_approvedBy').val() == 0 ||$('#activity_approvedBy').val() == "None" ||$('#activity_approvedBy').val() == null  )
					{
						alert("Select rebate approver");
						saveError = true;
					}
				else if($('#activity_rebate').val() == "Percentage")
					{
						if($('#activity_percentageRebate').val() == ""||$('#activity_percentageRebate').val() == 0)
							{
								alert("Input rebate rebate");
								saveError = true;
							}
					}
				else if($('#activity_rebate').val() == "Fixed Tariff")
					{
                        if (claimType == 'UNIT')
                            {
                                if ($('#activity_adultAmt').val() > 0 && $('#activity_adultRebate').val() == "")
                                    {
                                        alert("Insert rebate amount");
                                        saveError = true;
                                    }
                                else if ($('#activity_adultRebate').val() > unitClaim)
                                    {
                                        alert("Rebate amount cannot be greater than initial claim");
                                        console.log("Unit Claim : "+unitClaim+" Unit Rebate :"+$('#activity_adultRebate').val());
                                        saveError = true;
                                    }
                                
                            }
                        else if (claimType == 'PAX')
                            {
                                if ($('#activity_adultAmt').val() > 0 && $('#activity_adultRebate').val() == "")
                                    {
                                        alert("Insert rebate for Adult");
                                        saveError = true;
                                    }
                                else if ($('#activity_adultRebate').val() > adultClaim)
                                    {
                                        alert("Adult Rebate cannot be greater than initial claim");
                                        console.log("Adult Claim : "+adultClaim+" Adult Rebate :"+$('#activity_adultRebate').val());
                                        saveError = true;
                                    }
                                else if ($('#activity_teenAmt').val() > 0 && $('#activity_teenRebate').val() == "")
                                    {
                                        alert("Insert rebate for Teen");
                                        saveError = true;
                                    }
                                else if ($('#activity_teenRebate').val() > teenClaim)
                                    {
                                        alert("Teen Rebate cannot be greater than initial claim");
                                        console.log("Teen Claim : "+teenClaim+" Teen Rebate :"+$('#activity_teenRebate').val());
                                        saveError = true;
                                    }
                                else if ($('#activity_childAmt').val() > 0 && $('#activity_childRebate').val() == "")
                                    {
                                        alert("Insert rebate for Child");
                                        saveError = true;
                                    }
                                else if ($('#activity_childRebate').val() > childClaim)
                                    {
                                        alert("Child Rebate cannot be greater than initial claim");
                                        console.log("Child Claim : "+childClaim+" Child Rebate :"+$('#activity_childRebate').val());
                                        saveError = true;
                                    }
                                else if ($('#activity_infantAmt').val() > 0 && $('#activity_InfantRebate').val() == "")
                                    {
                                        alert("Insert rebate for Infant");
                                        saveError = true;
                                    }
                                else if ($('#activity_infantRebate').val() > childClaim)
                                    {
                                        alert("Infant Rebate cannot be greater than initial claim");
                                        console.log("Infant Claim : "+infantClaim+" Infant Rebate :"+$('#activity_infantRebate').val());
                                        saveError = true;
                                    }
                                
                            }
					}
			}
        
		if(!saveError)
		{
			if($('#id_booking_activity_claim').val() == 0 ||$('#id_booking_activity_claim').val() == '')
				{
                    // save Activity
                    var activityData = {action: 'CREATE'}
					saveActivity(activityData);
				}
			else
				{
                    var updateConfirm = confirm("Are you sure you want to update the booking activity?");
                    if(updateConfirm)
                    {
                        // update Activity
					   updateActivity();
                    }
				}
		}
    });
});

function saveActivity(activityData) { 
    var id_booking_activity_claim = -1;
    var id_booking = $('#id_booking').val();
    var activity_service_paid_by = $('#activity_paidBy').val();
	if (activity_service_paid_by == "TO")
		{
			var id_tour_operator = $('#activity_payer').val();
			var id_client = 0;
		}
	else
		{
			var id_tour_operator = 0;
			var id_client = $('#activity_payer').val();
		}
    var activity_date = $("#activity_date").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var activity_time = $('#activity_time').val();
    var activity_booking_date = $("#activity_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var id_product = $('#activity_type').val();
    var id_product_service = $('#activity_service').val();
    //var id_hotel = $('#activity_pickupHotel').val();
    //var activity_pickup_time = $('#activity_pickupTime').val();
    var activity_adult_amt = $('#activity_adultAmt').val();
    var activity_teen_amt = $('#activity_teenAmt').val();
    var activity_child_amt = $('#activity_childAmt').val();
    var activity_infant_amt = $('#activity_infantAmt').val();
    var activity_total_pax = parseInt(activity_adult_amt) + parseInt(activity_teen_amt) + parseInt(activity_child_amt) + parseInt(activity_infant_amt);
    var id_product_service_claim = $('#id_product_service_claim').val();
    var activity_rebate_type = $('#activity_rebate').val();
	if (activity_rebate_type == "Percentage")
		{
			var activity_rebate_approve_by = $('#activity_approvedBy').val();
			var activity_rebate_percentage = $('#activity_percentageRebate').val();
			var activity_adult_claim_after_rebate = 0;
			var activity_teen_claim_after_rebate = 0;
			var activity_child_claim_after_rebate = 0;
			var activity_infant_claim_after_rebate = 0;
		}
	else if (activity_rebate_type == "Fixed Tariff")
		{
			var activity_rebate_approve_by = $('#activity_approvedBy').val();
			var activity_rebate_percentage = 0;
			if (activity_adult_amt == 0)
			{
				var activity_adult_claim_after_rebate = 0;
			}
			else
			{
				var activity_adult_claim_after_rebate = $('#activity_adultRebate').val();
			}
			
			if (activity_teen_amt == 0)
			{
				var activity_teen_claim_after_rebate = 0;
			}
			else
			{
				var activity_teen_claim_after_rebate = $('#activity_teenRebate').val();
			}
			
			if (activity_child_amt == 0)
			{
				var activity_child_claim_after_rebate = 0;
			}
			else
			{
				var activity_child_claim_after_rebate = $('#activity_childRebate').val();
			}
			
			if (activity_infant_amt == 0)
			{
				var activity_infant_claim_after_rebate = 0;
			}
			else
			{
				var activity_infant_claim_after_rebate = $('#activity_InfantRebate').val();
			}
		}
	else if (activity_rebate_type == "FOC")
		{
			var activity_rebate_approve_by = $('#activity_approvedBy').val();
			var activity_rebate_percentage = 100;
			var activity_adult_claim_after_rebate = 0;
			var activity_teen_claim_after_rebate = 0;
			var activity_child_claim_after_rebate = 0;
			var activity_infant_claim_after_rebate = 0;
		}
	else
		{
			var activity_rebate_approve_by = 0;
			var activity_rebate_percentage = 0;
			var activity_adult_claim_after_rebate = 0;
			var activity_teen_claim_after_rebate = 0;
			var activity_child_claim_after_rebate = 0;
			var activity_infant_claim_after_rebate = 0;
		}
    //var activity_client_room_no = $('#activity_pickupRoomNo').val();
    //var id_language = $('#activity_language').val();
    //var id_rep = $('#activity_representative').val();
    //var activity_voucher_no = $('#activity_voucherNo').val();
    var activity_remarks = $('#activity_serviceRemark').val();
    var activity_internal_remarks = $('#activity_internalRemark').val();
    var activity_status = $('#activity_status').val();
    var booking_client = $('#activity_client').val();
    
    var objReservationActivity = {
        id_booking_activity_claim: -1,
        id_product_service_claim: id_product_service_claim,
        id_booking: id_booking,
        activity_service_paid_by: activity_service_paid_by,
        id_tour_operator: id_tour_operator,
        id_client: id_client,
        activity_date: activity_date,
        activity_time: activity_time,
        activity_booking_date: activity_booking_date,
        id_product: id_product,
        id_product_service: id_product_service,
		activity_adult_amt:activity_adult_amt,
		activity_teen_amt:activity_teen_amt,
		activity_child_amt:activity_child_amt,
		activity_infant_amt:activity_infant_amt,
		activity_total_pax:activity_total_pax,
        activity_rebate_type: activity_rebate_type,
        activity_rebate_approve_by: activity_rebate_approve_by,
        activity_rebate_percentage: activity_rebate_percentage,
        activity_adult_claim_after_rebate: activity_adult_claim_after_rebate,
        activity_teen_claim_after_rebate: activity_teen_claim_after_rebate,
        activity_child_claim_after_rebate: activity_child_claim_after_rebate,
        activity_infant_claim_after_rebate: activity_infant_claim_after_rebate,
        activity_remarks: activity_remarks,
        activity_internal_remarks: activity_internal_remarks,
        activity_status: activity_status,
        booking_client: booking_client,
        action: activityData.action
    }
    
   	const url_save_bookingActivity= "php/api/bookingSystem/saveBookingActivity.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_bookingActivity,
        method : "POST",
        data : objReservationActivity, 
        dataType: "json",                                                                           
        success : function(data){
            if (data.OUTCOME == 'OK')
                {
                    if(objReservationActivity.action == 'UPDATE')
                        {
                            toastr.info('Activity updated successfully');
                        }
                    else
                        {
                            toastr.success('New Activity saved successfully');
                        }
                        $('#id_booking_activity_claim').val(data.id_booking_activity_claim);
                        allBookingActivity(data.id_booking);
                        $("#dossierService").collapse('show');
                        $("#serviceDetails").collapse('hide');
                        objReservationActivity.action = 'RESET';
                        newActivity(objReservationActivity);
                }
            else
                {
                    toastr.warning('An error occured. Please do it again');
                }
        },
        error: function(error) {
            toastr.warning('An error occured. Please do it again');
            console.log('Error ${error}');
        }
    });
}

function updateActivity() { 
	var id_booking_activity_claim = $('#id_booking_activity_claim').val();
    var id_booking = $('#id_booking').val();
    var activityData = {
        id_booking: id_booking,
        id_booking_activity_claim: id_booking_activity_claim,
        action : 'UPDATE'
    }
    if($('#id_booking_activity_claim').val() == '' && $('#id_booking_activity_claim').val() != '')
        {
            alert("An error occured, please load back the Activity");
        }
    else
        {	
            deleteActivity(activityData);
        }
}