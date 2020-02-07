// JavaScript Document
/*Date : 2019, 30 December
Application : Transfer - Booking System
Developer : slouis@solis360.com*/

$(function(){
	// Save Booking
	$('#btn-saveTransfer').click(
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
        
		if($('#transfer_paidBy').val() == "" || $('#transfer_paidBy').val() == 0)
			{
				alert("Select payer type");
				saveError = true;
			}
		else if ($('#transfer_payer').val() == "" || $('#transfer_payer').val() == 0)
			{
				if($('#transfer_paidBy').val() == "TO")
					{
						alert("Select paying agency or reload transfer form");
					}
				else
					{
						alert("Select paying client or reload transfer form");
					}
				saveError = true;
			}
        else if ($("#transfer_vehicle").val() == 0 || $("#transfer_vehicle").val() == "")
            {
				alert("Select a vehicle");
                var saveError = true;
            }
		else if ($("#transfer_type").val() == 'BOTH' && $("#transfer_destination_to").val() == 0)
            {
                alert("Select the destination");
                var saveError = true;
            }
		else if ($("#transfer_type").val() == 'BOTH' && $("#transfer_destination_to").val() == "")
            {
                alert("Select the destination");
                var saveError = true;
            }
		else if ($("#transfer_type").val() == 'BOTH' && $("#transfer_arrivalDate").val() == "")
            {
                alert("Select Arrival Date");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'BOTH' && $("#transfer_departureDate").val() == "" ) 
             {
                 alert("Select Departure Date");
                 var saveError = true;
             }
        else  if ($("#transfer_type").val() == 'BOTH' && $("#id_product_service_arr_claim").val() == "")
             {
                 alert("An error occured, reload the transfer");
                 var saveError = true;
             }
        else  if ($("#transfer_type").val() == 'BOTH' && $("#id_product_service_dep_claim").val() == "")
             {
                 alert("An error occured, reload the transfer");
                 var saveError = true;
             }
        else if ($("#transfer_type").val() == 'ARR' &&  $("#transfer_destination_to").val() == 0)
            {
                alert("Select the destination");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ARR' &&  $("#transfer_destination_to").val() == "")
            {
                alert("Select the destination");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ARR' && $("#transfer_pickupDate").val() == "" ) 
            {
                alert("Select Pickup Date");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ARR' && $("#id_product_service_arr_claim").val() == "") 
            {
                alert("An error occured, reload the transfer");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'DEP' &&  $("#transfer_destination_from").val() == 0)
            {
                alert("Select the destination");
                var saveError = true;
            } 
        else if ($("#transfer_type").val() == 'DEP' &&  $("#transfer_destination_from").val() == "")
            {
                alert("Select the destination");
                var saveError = true;
            } 
        else if ($("#transfer_type").val() == 'DEP' && $("#transfer_pickupDate").val() == "" ) 
            {
                alert("Select Pickup Date");
                var saveError = true;
            } 
        else if ($("#transfer_type").val() == 'DEP' && $("#id_product_service_dep_claim").val() == "")
            {
                    alert("An error occured, reload the transfer");
                    var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' &&  $("#transfer_destination_from").val() == 0)
            {
                alert("Select the destination from");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' &&  $("#transfer_destination_from").val() == "")
            {
                alert("Select the destination from");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' &&  $("#transfer_destination_to").val() == 0)
            {
                alert("Select the destination to");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' &&  $("#transfer_destination_to").val() == "")
            {
                alert("Select the destination to");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' && $("#transfer_pickupDate").val() == "" ) 
            {
                alert("Select Pickup Date");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL' && $("#id_product_service_dep_claim").val() == "")
            {
                alert("An error occured, reload the transfer");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_port").val() == 0)
            {
                alert("Select the Activity type");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_port").val() == "")
            {
               alert("Select the Activity type");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_destination_from").val() == 0)
            {
                alert("Select the destination from");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_destination_from").val() == "")
            {
                alert("Select the destination from");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_destination_to").val() == 0)
            {
                alert("Select the destination to");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' &&  $("#transfer_destination_to").val() == "")
            {
                alert("Select the destination to");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' && $("#transfer_pickupDate").val() == "" ) 
            {
                alert("Select Pickup Date");
                var saveError = true;
            }
        else if ($("#transfer_type").val() == 'ACTIVITY' && $("#id_product_service_dep_claim").val() == "")
            {
                alert("An error occured, reload the transfer");
                var saveError = true;
            }
        else if($('#transfer_client').val() == 0 || $('#transfer_client').val() == null)
            {
                alert("Select your client");
                saveError = true;
            }
		else if($('#transfer_adultAmt').val() == ""  || $('#transfer_teenAmt').val() == "" || $('#transfer_childAmt').val() == ""  || $('#transfer_infantAmt').val() == "")
            {
                alert("Select your client");
                saveError = true;
            }
		else if($('#transfer_adultAmt').val() == 0  && $('#transfer_teenAmt').val() == 0 && $('#transfer_childAmt').val() == 0  && $('#transfer_infantAmt').val() == 0)
            {
                alert("Select your client");
                saveError = true;
            }
		else if($('#transfer_rebateClaim').val() != "None")
			{
				if ($('#transfer_rebateClaimApproveBy').val() == "" ||$('#transfer_rebateClaimApproveBy').val() == 0 ||$('#transfer_rebateClaimApproveBy').val() == "None" ||$('#transfer_rebateClaimApproveBy').val() == null  )
					{
						alert("Select rebate approver");
						saveError = true;
					}
				else if($('#transfer_rebateClaim').val() == "Percentage")
					{
						if($('#transfer_claimPercentageRebate').val() == ""||$('#transfer_claimPercentageRebate').val() == 0)
							{
								alert("Input rebate percentage");
								saveError = true;
							}
					}
				else if($('#transfer_rebateClaim').val() == "Fixed Tariff")
					{
                        if (claimType == 'UNIT')
                            {
                                if ($('#transfer_adultClaimRebate').val() > 0 && $('#transfer_adultClaimRebate').val() == "")
                                    {
                                        alert("Insert rebate amount");
                                        saveError = true;
                                    }
                                else if ($('#transfer_adultClaimRebate').val() > unitClaim)
                                    {
                                        alert("Rebate amount cannot be greater than initial claim");
                                        console.log("Unit Claim : "+unitClaim+" Unit Rebate :"+$('#transfer_adultClaimRebate').val());
                                        saveError = true;
                                    }
                                
                            }
                        else if (claimType == 'PAX')
                            {
                                if ($('#transfer_adultAmt').val() > 0 && $('#transfer_adultClaimRebate').val() == "")
                                    {
                                        alert("Insert rebate for Adult");
                                        saveError = true;
                                    }
                                else if ($('#transfer_adultClaimRebate').val() > adultClaim)
                                    {
                                        alert("Adult Rebate cannot be greater than initial claim");
                                        console.log("Adult Claim : "+adultClaim+" Adult Rebate :"+$('#transfer_adultClaimRebate').val());
                                        saveError = true;
                                    }
                                else if ($('#transfer_childAmt').val() > 0 && $('#transfer_childClaimRebate').val() == "")
                                    {
                                        alert("Insert rebate for Child");
                                        saveError = true;
                                    }
                                else if ($('#transfer_childClaimRebate').val() > childClaim)
                                    {
                                        alert("Child Rebate cannot be greater than initial claim");
                                        console.log("Child Claim : "+childClaim+" Child Rebate :"+$('#transfer_childClaimRebate').val());
                                        saveError = true;
                                    }
                                else if ($('#transfer_infantAmt').val() > 0 && $('#transfer_InfantClaimRebate').val() == "")
                                    {
                                        alert("Insert rebate for Infant");
                                        saveError = true;
                                    }
                                else if ($('#transfer_InfantClaimRebate').val() > childClaim)
                                    {
                                        alert("Infant Rebate cannot be greater than initial claim");
                                        console.log("Infant Claim : "+infantClaim+" Infant Rebate :"+$('#transfer_InfantClaimRebate').val());
                                        saveError = true;
                                    }
                                
                            }
					}
			}
        
		if(!saveError)
		{
			if($('#id_booking_transfer_claim').val() == 0 ||$('#id_booking_transfer_claim').val() == '')
				{
                    // save Transfer
                    var transferData = {action: 'CREATE'}
					saveTransfer(transferData);
				}
			else
				{
                    var updateConfirm = confirm("Are you sure you want to update the booking transfer?");
                    if(updateConfirm)
                    {
                        // update Transfer
					   updateTransfer();
                    }
				}
		}
    });
});


function saveTransfer(transferData) { 
    var id_booking_transfer_claim = -1;
    var id_booking = $('#id_booking').val();
    var transfer_booking_date = $("#transfer_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var transfer_status = $('#transfer_status').val();
    var transfer_service_paid_by = $('#transfer_paidBy').val();
	if (transfer_service_paid_by == "TO")
		{
			var id_tour_operator = $('#transfer_payer').val();
			var id_client = 0;
		}
	else
		{
			var id_tour_operator = 0;
			var id_client = $('#transfer_payer').val();
		}
    var transfer_type = $('#transfer_type').val();
    var transfer_port = $('#transfer_port').val();
    var transfer_vehicle = $('#transfer_vehicle').val();
    if ($("#transfer_type").val() == 'BOTH')
        {
            var transfer_destination_from = $('#transfer_destination_to').val();
            var transfer_destination_to = $('#transfer_destination_to').val();
            var transfer_arrivalDate = $("#transfer_arrivalDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_arrivalFlight = $('#transfer_arrivalFlight').val();
            var transfer_arrivalTime = $('#transfer_arrivalTime').val();
            var transfer_departureDate = $("#transfer_departureDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_departureFlight = $('#transfer_departureFlight').val();
            var transfer_departureTime = $('#transfer_departureTime').val();
        }
    else if ($("#transfer_type").val() == 'ARR')
        {
            var transfer_destination_from = $('#transfer_destination_from').val();
            var transfer_destination_to = $('#transfer_destination_to').val();
            var transfer_arrivalDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_arrivalFlight = $('#transfer_pickupFlight').val();
            var transfer_arrivalTime = $('#transfer_pickupTime').val();
            var transfer_departureDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_departureFlight = $('#transfer_pickupFlight').val();
            var transfer_departureTime = $('#transfer_pickupTime').val();
        }
    else if ($("#transfer_type").val() == 'DEP')
        {
            var transfer_destination_from = $('#transfer_destination_from').val();
            var transfer_destination_to = $('#transfer_destination_to').val();
            var transfer_arrivalDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_arrivalFlight = $('#transfer_pickupFlight').val();
            var transfer_arrivalTime = $('#transfer_pickupTime').val();
            var transfer_departureDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_departureFlight = $('#transfer_pickupFlight').val();
            var transfer_departureTime = $('#transfer_pickupTime').val();
        }
    else if ($("#transfer_type").val() == 'INTER HOTEL')
        {
            var transfer_destination_from = $('#transfer_destination_from').val();
            var transfer_destination_to = $('#transfer_destination_to').val();
            var transfer_arrivalDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_arrivalFlight = $('#transfer_pickupFlight').val();
            var transfer_arrivalTime = $('#transfer_pickupTime').val();
            var transfer_departureDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_departureFlight = $('#transfer_pickupFlight').val();
            var transfer_departureTime = $('#transfer_pickupTime').val();
        }
    else if ($("#transfer_type").val() == 'ACTIVITY')
        {
            var transfer_destination_from = $('#transfer_destination_from').val();
            var transfer_destination_to = $('#transfer_destination_to').val();
            var transfer_arrivalDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_arrivalFlight = $('#transfer_pickupFlight').val();
            var transfer_arrivalTime = $('#transfer_pickupTime').val();
            var transfer_departureDate = $("#transfer_pickupDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var transfer_departureFlight = $('#transfer_pickupFlight').val();
            var transfer_departureTime = $('#transfer_pickupTime').val();
        }
    var transfer_client = $('#transfer_client').val();
    var transfer_adultAmt = $('#transfer_adultAmt').val();
    var transfer_childAmt = $('#transfer_childAmt').val();
    var transfer_infantAmt = $('#transfer_infantAmt').val();
    var transfer_total_pax = parseInt(transfer_adultAmt) + parseInt(transfer_childAmt) + parseInt(transfer_infantAmt);
    var id_product_service_arr_claim = $('#id_product_service_arr_claim').val();
    var id_product_service_dep_claim = $('#id_product_service_dep_claim').val();
    var transfer_rebateClaim = $('#transfer_rebateClaim').val();
	if (transfer_rebateClaim == "Percentage")
		{
			var transfer_rebateClaimApproveBy = $('#transfer_rebateClaimApproveBy').val();
			var transfer_claimPercentageRebate = $('#transfer_claimPercentageRebate').val();
			var transfer_adult_claim_after_rebate = 0;
			var transfer_child_claim_after_rebate = 0;
			var transfer_infant_claim_after_rebate = 0;
		}
	else if (transfer_rebateClaim == "Fixed Tariff")
		{
			var transfer_rebateClaimApproveBy = $('#transfer_rebateClaimApproveBy').val();
			var transfer_claimPercentageRebate = 0;
			if (transfer_adultAmt == 0)
			{
				var transfer_adult_claim_after_rebate = 0;
			}
			else
			{
				var transfer_adult_claim_after_rebate = $('#transfer_adultClaimRebate').val();
			}
		
			if (transfer_childAmt == 0)
			{
				var transfer_child_claim_after_rebate = 0;
			}
			else
			{
				var transfer_child_claim_after_rebate = $('#transfer_childClaimRebate').val();
			}
			
			if (transfer_infantAmt == 0)
			{
				var transfer_infant_claim_after_rebate = 0;
			}
			else
			{
				var transfer_infant_claim_after_rebate = $('#transfer_InfantClaimRebate').val();
			}
		}
	else if (transfer_rebateClaim == "FOC")
		{
			var transfer_rebateClaimApproveBy = $('#transfer_rebateClaimApproveBy').val();
			var transfer_claimPercentageRebate = 100;
			var transfer_adult_claim_after_rebate = 0;
			var transfer_child_claim_after_rebate = 0;
			var transfer_infant_claim_after_rebate = 0;
		}
	else
		{
			var transfer_rebateClaimApproveBy = 0;
			var transfer_claimPercentageRebate = 0;
			var transfer_adult_claim_after_rebate = 0;
			var transfer_child_claim_after_rebate = 0;
			var transfer_infant_claim_after_rebate = 0;
		}
    var transfer_remarks = $('#transfer_serviceRemark').val();
    var transfer_internal_remarks = $('#transfer_internalRemark').val();
    
    var objReservationTransfer = {
        id_booking_transfer_claim : id_booking_transfer_claim,
        id_booking : id_booking,
        transfer_booking_date : transfer_booking_date,
        transfer_status : transfer_status,
        transfer_service_paid_by : transfer_service_paid_by,
        id_tour_operator : id_tour_operator,
        id_client : id_client,
        transfer_type : transfer_type,
        transfer_port : transfer_port,  //special Name
        transfer_vehicle : transfer_vehicle, // id_product
        transfer_destination_from : transfer_destination_from, // hotel id //service_name
        transfer_destination_to : transfer_destination_to, // hotel id //service_name
        transfer_arrivalDate : transfer_arrivalDate,
        transfer_arrivalFlight : transfer_arrivalFlight,
        transfer_arrivalTime : transfer_arrivalTime,
        transfer_departureDate : transfer_departureDate,
        transfer_departureFlight : transfer_departureFlight,
        transfer_departureTime : transfer_departureTime,
        transfer_client : transfer_client,
        transfer_adultAmt : transfer_adultAmt,
        transfer_childAmt : transfer_childAmt,
        transfer_infantAmt : transfer_infantAmt,
        transfer_total_pax : transfer_total_pax,
        id_product_service_arr_claim : id_product_service_arr_claim,
        id_product_service_dep_claim : id_product_service_dep_claim,
        transfer_rebateClaim : transfer_rebateClaim,
        transfer_rebateClaimApproveBy : transfer_rebateClaimApproveBy,
        transfer_claimPercentageRebate : transfer_claimPercentageRebate,
        transfer_adult_claim_after_rebate : transfer_adult_claim_after_rebate,
        transfer_child_claim_after_rebate : transfer_child_claim_after_rebate,
        transfer_infant_claim_after_rebate : transfer_infant_claim_after_rebate,
        transfer_remarks : transfer_remarks,
        transfer_internal_remarks : transfer_internal_remarks,
        action: transferData.action
    }
    
   	const url_save_bookingTransfer= "php/api/bookingSystem/saveBookingTransfer.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_bookingTransfer,
        method : "POST",
        data : objReservationTransfer, 
        dataType: "json",                                                                           
        success : function(data){
            if (data.OUTCOME == 'OK' && data.MESSAGE == 'ALL')
                {
                    console.log(data);
                    if(objReservationTransfer.action == 'UPDATE')
                        {
                            toastr.info('Transfer updated successfully');
                        }
                    else
                        {
                            toastr.success('New Transfer saved successfully');
                        }
                        allBookingTransfer(data.id_booking);
                        $("#dossierService").collapse('show');
                        $("#serviceDetails").collapse('hide');
                        objReservationTransfer.action = 'RESET';
                        newTransfer(objReservationTransfer);
                }
            else if (data.OUTCOME == 'OK' && data.MESSAGE == 'DEPARTURE')
                {
                    if(objReservationTransfer.action == 'UPDATE')
                        {
                            toastr.info('Transfer updated successfully');
                        }
                    else
                        {
                            toastr.success('New Transfer saved successfully');
                        }
                        allBookingTransfer(data.id_booking);
                        $("#dossierService").collapse('show');
                        $("#serviceDetails").collapse('hide');
                        objReservationTransfer.action = 'RESET';
                        newTransfer(objReservationTransfer);
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

function updateTransfer() { 
	var id_booking_transfer_claim = $('#id_booking_transfer_claim').val();
    var id_booking = $('#id_booking').val();
    var transferData = {
        id_booking: id_booking,
        id_booking_transfer_claim: id_booking_transfer_claim,
        action : 'UPDATE'
    }
    if($('#id_booking_transfer_claim').val() == '' && $('#id_booking_transfer_claim').val() != '')
        {
            alert("An error occured, please load back the Transfer");
        }
    else
        {	
            deleteTransfer(transferData);
        }
}

