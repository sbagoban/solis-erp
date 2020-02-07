// JavaScript Document
/*Date : 2019, 03 December
Application : Transfer - Booking System
Developer : slouis@solis360.com*/
var target_action = 'NULL';

$(function(){
	$('#transfer_arrivalDate').val('');
	$('#transfer_departureDate').val('');
    $("#claimRebateSection").hide();
    $("#costRebateSection").hide();
    
	// Transfer Paid By
	$('#transfer_paidBy').change(function()
		{
			$('#transfer_date').val('');
        
            var id_booking = $('#id_booking').val();
            var transferData = {
                    id_booking: id_booking,
                    action: target_action
            }   

	 		if ($('#transfer_paidBy').val() == 'TO')
				{
					loadTourOperator(transferData);
				}
				else if ($('#transfer_paidBy').val() == "Client")
				{
                    loadBookingClient(transferData);
				}
		});
	//. Transfer Paid By
    
    // Transfer Type
    
    $("#transfer_type").change(function()
    {
        
        $("#dossierService").collapse('show');
        $("#serviceDetails").collapse('hide');
        $("#id_product_service_arr_claim").val('');
        $("#id_product_service_arr_claim").val('');

        if ($("#transfer_type").val() == 'BOTH')
            {
                $(".arrivalLine").show();
                $(".departureLine").show();
                $(".pickupLine").hide();
                $(".destinationFrom").hide();
                $(".destinationTo").show();
                $("#transfer_destination_from").val('0');
                $('#transfer_destination_from').select2().trigger('change');	
                $("#transfer_destination_to").val('0');
                $('#transfer_destination_to').select2().trigger('change');	
                $("#transfer_arrivalDate").val('');
                $("#transfer_arrivalFlight").val('');
                $("#transfer_arrivalTime").val('');
                $("#transfer_departureDate").val('');
                $("#transfer_departureFlight").val('');
                $("#transfer_departureTime").val('');
                $("#transfer_port").prop( "disabled", false );
                $("#transfer_port").empty();
                $("#transfer_port").append('<option value="AIRPORT">AIRPORT</option>');
                //$("#transfer_port").append('<option value="PORT">PORT</option>');
                $("#transfer_port").val('AIRPORT');
                $('#transfer_port').select2().trigger('change');	
            }
         else if ($("#transfer_type").val() == 'ARR')
             {
                $(".arrivalLine").hide();
                $(".departureLine").hide();
                $(".pickupLine").show();
                $("#pickupLabel").html('Arrival');
                $(".destinationFrom").hide();
                $(".destinationTo").show();
                $("#transfer_destination_from").val('0');
                $('#transfer_destination_from').select2().trigger('change');	
                $("#transfer_destination_to").val('0');
                $('#transfer_destination_to').select2().trigger('change');	
                $("#transfer_arrivalDate").val('');
                $("#transfer_arrivalFlight").val('');
                $("#transfer_arrivalTime").val('');
                $("#transfer_departureDate").val('');
                $("#transfer_departureFlight").val('');
                $("#transfer_departureTime").val('');
                $("#transfer_pickupDate").val('');
                $("#transfer_pickupFlight").prop( "disabled", false );
                $("#transfer_port").prop( "disabled", false );
                $("#transfer_pickupFlight").val('');
                $("#transfer_pickupTime").val('');
                $("#transfer_port").empty();
                $("#transfer_port").append('<option value="AIRPORT">AIRPORT</option>');
                //$("#transfer_port").append('<option value="PORT">PORT</option>');
                $("#transfer_port").val('AIRPORT');
                $('#transfer_port').select2().trigger('change');	
                 
             }
        else if ($("#transfer_type").val() == 'DEP')
            {
                $(".arrivalLine").hide();
                $(".departureLine").hide();
                $(".pickupLine").show();
                $("#pickupLabel").html('Departure');
                $(".destinationFrom").show();
                $(".destinationTo").hide();
                $("#transfer_destination_from").val('0');
                $('#transfer_destination_from').select2().trigger('change');	
                $("#transfer_destination_to").val('0');
                $('#transfer_destination_to').select2().trigger('change');	
                $("#transfer_arrivalDate").val('');
                $("#transfer_arrivalFlight").val('');
                $("#transfer_arrivalTime").val('');
                $("#transfer_departureDate").val('');
                $("#transfer_departureFlight").val('');
                $("#transfer_departureTime").val('');
                $("#transfer_pickupDate").val('');
                $("#transfer_pickupFlight").prop( "disabled", false );
                $("#transfer_port").prop( "disabled", false );
                $("#transfer_pickupFlight").val('');
                $("#transfer_pickupTime").val('');
                $("#transfer_port").empty();
                $("#transfer_port").append('<option value="AIRPORT">AIRPORT</option>');
                //$("#transfer_port").append('<option value="PORT">PORT</option>');
                $("#transfer_port").val('AIRPORT');
                $('#transfer_port').select2().trigger('change');	
            }
        else if ($("#transfer_type").val() == 'INTER HOTEL')
            {
                $(".arrivalLine").hide();
                $(".departureLine").hide();
                $(".pickupLine").show();
                $("#pickupLabel").html('Inter Hotel');
                $(".destinationFrom").show();
                $(".destinationTo").show();
                $("#transfer_destination_from").val('0');
                $('#transfer_destination_from').select2().trigger('change');	
                $("#transfer_destination_to").val('0');
                $('#transfer_destination_to').select2().trigger('change');	
                $("#transfer_arrivalDate").val('');
                $("#transfer_arrivalFlight").val('');
                $("#transfer_arrivalTime").val('');
                $("#transfer_departureDate").val('');
                $("#transfer_departureTime").val('');
                $("#transfer_departureFlight").val('');
                $("#transfer_pickupDate").val('');
                $("#transfer_pickupFlight").prop( "disabled", true );
                $("#transfer_port").prop( "disabled", true );
                $("#transfer_port").empty();
                $("#transfer_port").append('<option value="NONE">NONE</option>');
                $("#transfer_pickupFlight").val('');
                $("#transfer_pickupTime").val('');
            }
        else if ($("#transfer_type").val() == 'ACTIVITY')
            {
                $(".arrivalLine").hide();
                $(".departureLine").hide();
                $(".pickupLine").show();
                $("#pickupLabel").html('Activity');
                $(".destinationFrom").show();
                $(".destinationTo").show();
                $("#transfer_destination_from").val('0');
                $('#transfer_destination_from').select2().trigger('change');	
                $("#transfer_destination_to").val('0');
                $('#transfer_destination_to').select2().trigger('change');	
                $("#transfer_arrivalDate").val('');
                $("#transfer_arrivalFlight").val('');
                $("#transfer_arrivalTime").val('');
                $("#transfer_departureDate").val('');
                $("#transfer_departureTime").val('');
                $("#transfer_departureFlight").val('');
                $("#transfer_pickupDate").val('');
                $("#transfer_pickupFlight").prop( "disabled", true );
                $("#transfer_port").prop( "disabled", false );
                $("#transfer_port").empty();
                $("#transfer_port").append('<option value="DROP ON">DROP ON</option>');
                $("#transfer_port").append('<option value="DROP OFF">DROP OFF</option>');
                $("#transfer_port").append('<option value="HALF DAY">HALF DAY</option>');
                $("#transfer_port").append('<option value="FULL DAY">FULL DAY</option>');
                $("#transfer_port").append('<option value="NIGHT TOUR">NIGHT TOUR</option>');
                $("#transfer_port").val('DROP ON');
                $('#transfer_port').select2().trigger('change');	
                $("#transfer_pickupFlight").val('');
                $("#transfer_pickupTime").val('');
            }
        
        validationTransferVehicle();
        validationValueTransferClaim();
    });
    //. Transfer Type
    
    // Transfer Arrival
    $('#transfer_arrivalDate').on('apply.daterangepicker', function(ev, picker) {
        $("#dossierService").collapse('show');
        $("#serviceDetails").collapse('hide');
        $("#id_product_service_arr_claim").val('');
        $("#id_product_service_arr_claim").val('');
        var arrivalDate = picker.endDate.format('DD/MM/YYYY');
        var arrivalFullDate = picker.endDate._d;
        var booking_to = $("#booking_travelDate").data('daterangepicker').endDate._d;
        $('#transfer_departureDate').daterangepicker({      
            "singleDatePicker": true,
            "showDropdowns": true,
            "autoApply": true,
            "opens": "center",
            minDate: arrivalFullDate,
            maxDate: booking_to,
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('#transfer_departureDate').val(''); 
        $('#transfer_departureDate').on('apply.daterangepicker', function(ev, picker) {
            $("#dossierService").collapse('show');
            $("#serviceDetails").collapse('hide');
            $("#id_product_service_arr_claim").val('');
            $("#id_product_service_arr_claim").val('');
           validationValueTransferClaim();
        });
        validationValueTransferClaim();
    });
    // .Transfer Arrival
    
    // Transfer Port
    $("#transfer_port").change(function(){
        validationTransferVehicle();
        validationValueTransferClaim();
    });
    // .Transfer Port
    
     // Vehicle 
    $("#transfer_vehicle").change(function(){
        resetClient();
        validationValueTransferClaim();
    });
    //.Vehicle 
    
    // Destination 
    $("#transfer_destination_from").change(function(){
        validationValueTransferClaim();
    });
    $("#transfer_destination_to").change(function(){
        validationValueTransferClaim();
    });
    //.Destination
    
    // Transfer Client
	$("#transfer_client").on('changed.bs.select', function (e, clickedIndex, newValue, oldValue) {
		var numberOfClient = $("#transfer_client :selected").length;
		var valueOfClient = $("#transfer_client").val();
        if (numberOfClient == 0)
        {
            var clientCount = {
                pax_amt: valueOfClient.length,
                infant_amt: 0,
                child_amt: 0,
                teen_amt:0,
                adult_amt: 0
            }
            $("#transfer_adultAmt").val("");
            $("#transfer_childAmt").val("");
            $("#transfer_infantAmt").val("");
            
        }
        else
        {
            var clientCount = {
                pax_amt: 0,
                infant_amt: 0,
                child_amt: 0,
                teen_amt:0,
                adult_amt: 0
            }
            $.each(valueOfClient, function (key, val) {
                if ($("#transfer_type").val() == "BOTH" && $("#id_product_service_arr_claim").val() == "" && $("#id_product_service_dep_claim").val() == "" )
                    {
                        alert("Select transfer details");
                        var inegibleClient = $('#transfer_client option[value="'+val+'"]').val();
                        $('#transfer_client').find('[value='+inegibleClient+']').prop('selected', false);
                        $("#transfer_client").selectpicker('refresh');
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
                                        if ($("#transfer_type").val() == "BOTH" || $("#transfer_type").val() == "ARR")
                                            {
                                                 var id_transfer_claim = $("#id_product_service_arr_claim").val();
                                            }
                                        else
                                            {
                                                 var id_transfer_claim = $("#id_product_service_dep_claim").val();
                                            }
                                        const url_serviceClaim = "php/api/bookingSystem/transferSpecificService.php?t=" + encodeURIComponent(global_token) + "&id_transfer_claim=" +id_transfer_claim;
                                        $.ajax({
                                            url: url_serviceClaim,
                                            method: "GET",
                                            dataType: "json",
                                            success: function (transferData) 
                                            {
                                                var transfer_maxPax = transferData[0].max_pax;
                                                var transfer_maxAdult = transferData[0].max_adult;
                                                var transfer_maxOther = parseInt(transfer_maxPax) - parseInt(transfer_maxAdult);
                                                var transfer_forInfant = transferData[0].for_infant;
                                                var transfer_forChild = transferData[0].for_child;
                                                var transfer_forAdult = transferData[0].for_adult;
                                                if (transfer_forAdult == 1 && clientData[0].type == "ADULT")
                                                    {
                                                        if (transfer_maxAdult != null && clientCount.adult_amt < transfer_maxAdult && clientCount.pax_amt <= transfer_maxPax)
                                                            {
                                                                clientCount.adult_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else if (transfer_forChild == 1 && clientData[0].type == "CHILD")
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax )
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else if (transfer_forInfant == 1 && clientData[0].type == "INFANT")
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax )
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else
                                                    {
                                                        var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
                                                            alert(clientName+" as "+clientData[0].type+ " does not satisfy pax policy of the transfer. Please input the date of birth");
                                                        var inegibleClient = $('#transfer_client option[value="'+val+'"]').val();
                                                        $('#transfer_client').find('[value='+inegibleClient+']').prop('selected', false);
                                                        $("#transfer_client").selectpicker('refresh');
                                                        clientCount.pax_amt -= 1;
                                                    }
                                                $("#transfer_adultAmt").val(clientCount.adult_amt);
                                                $("#transfer_childAmt").val(clientCount.child_amt);
                                                $("#transfer_infantAmt").val(clientCount.infant_amt);
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
                                        if ($("#transfer_type").val() == "BOTH" || $("#transfer_type").val() == "ARR")
                                            {
                                                 var id_transfer_claim = $("#id_product_service_arr_claim").val();
                                            }
                                        else
                                            {
                                                 var id_transfer_claim = $("#id_product_service_dep_claim").val();
                                            }
                                        const url_serviceClaim = "php/api/bookingSystem/transferSpecificService.php?t=" + encodeURIComponent(global_token) + "&id_transfer_claim=" +id_transfer_claim;
                                        $.ajax({
                                            url: url_serviceClaim,
                                            method: "GET",
                                            dataType: "json",
                                            success: function (transferData)
                                            {
                                                var transfer_maxPax = transferData[0].max_pax;
                                                var transfer_maxAdult = transferData[0].max_adult;
                                                var transfer_maxOther = parseInt(transfer_maxPax) - parseInt(transfer_maxAdult);
                                                var transfer_forInfant = transferData[0].for_infant;
                                                var transfer_infantTo = transferData[0].age_inf_to;
                                                var transfer_forChild = transferData[0].for_child;
                                                var transfer_childTo = transferData[0].age_child_to;
                                                var transfer_forAdult = transferData[0].for_adult;

                                                if (transfer_forInfant == 1 && transfer_infantTo != null && transfer_infantTo >= age)
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax)
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }

                                                    }
                                                else if (transfer_forChild == 1 && transfer_childTo != null && transfer_childTo >= age)
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax)
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }

                                                    }
                                                else if (transfer_forAdult == 1 && transfer_childTo != null && transfer_childTo <= age)
                                                    {
                                                        if (transfer_maxAdult != null && clientCount.pax_amt <= transfer_maxAdult)
                                                            {
                                                                clientCount.adult_amt ++;
                                                            }
                                                        else if (transfer_maxAdult == null)
                                                            {
                                                                clientCount.adult_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum Adult for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else if (transfer_forAdult == 1 && client_type == "ADULT")
                                                    {
                                                        if (transfer_maxAdult != null && clientCount.pax_amt <= transfer_maxAdult)
                                                            {
                                                                clientCount.adult_amt ++;
                                                            }
                                                        else if (transfer_maxAdult == null)
                                                            {
                                                                clientCount.adult_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum Adult for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else if (transfer_forChild == 1 && client_type == "CHILD")
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax)
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.child_amt ++;
                                                            }
                                                        else
                                                            {														
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else if (transfer_forInfant == 1 && client_type == "INFANT")
                                                    {
                                                        if (transfer_maxPax != null && clientCount.pax_amt <= transfer_maxPax)
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else if (transfer_maxPax == null)
                                                            {
                                                                clientCount.infant_amt ++;
                                                            }
                                                        else
                                                            {												
                                                                alert("Maximum pax for the transfer already reached");
                                                                var inegibleClient=$.merge($(valueOfClient).not(oldValue).get(),$(oldValue).not(valueOfClient).get());
                                                                $('#transfer_client').find('[value='+inegibleClient[0]+']').prop('selected', false);
                                                                $("#transfer_client").selectpicker('refresh');
                                                                clientCount.pax_amt -= 1;
                                                            }
                                                    }
                                                else
                                                    {
                                                        var clientName = clientData[0].title+ " "+clientData[0].surname+ " "+" "+clientData[0].other_name;
                                                        alert(clientName+" as "+client_type+ " does not satisfy pax policy of the transfer");
                                                        var inegibleClient = $('#transfer_client option[value="'+val+'"]').val();
                                                        $('#transfer_client').find('[value='+inegibleClient+']').prop('selected', false);
                                                        $("#transfer_client").selectpicker('refresh');
                                                        clientCount.pax_amt -= 1;
                                                    }
                                        $("#transfer_adultAmt").val(clientCount.adult_amt);
                                        $("#transfer_childAmt").val(clientCount.child_amt);
                                        $("#transfer_infantAmt").val(clientCount.infant_amt);
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
	$("#transfer_rebateClaim").change(function(){
		if ($("#transfer_rebateClaim").val() == 'None')
			{
				$("#claimRebateSection").hide();
				$("#transfer_rebateClaimApproveBy").val('');
				$('#transfer_rebateClaimApproveBy').select2().trigger('change');
				$("#transfer_rebateClaimApproveBy").prop("disabled", true);
				$("#transfer_claimPercentageRebate").val('');
				$("#transfer_adultClaimRebate").val('');
				$("#transfer_childClaimRebate").val('');
				$("#transfer_InfantClaimRebate").val('');
			}
		else if ($("#transfer_rebateClaim").val() == 'Percentage')
			{
				$("#claimRebateSection").show();
				$("#transfer_claimPercentageRebate").show();
				$("#claimRebateFix").hide();
				$("#transfer_rebateClaimApproveBy").prop("disabled", false);
				$("#transfer_adultClaimRebate").val('');
				$("#transfer_childClaimRebate").val('');
				$("#transfer_InfantClaimRebate").val('');
			}
		else if ($("#transfer_rebateClaim").val() == 'Fixed Tariff')
			{
				$("#claimRebateSection").show();
				$("#transfer_claimPercentageRebate").hide();
				$("#claimRebateFix").show();
				$("#transfer_rebateClaimApproveBy").prop("disabled", false);
				$("#transfer_claimPercentageRebate").val('');
			}
		else if ($("#transfer_rebateClaim").val() == 'FOC')
			{
				$("#transfer_claimPercentageRebate").hide();
				$("#claimRebateSection").hide();
				$("#transfer_rebateClaimApproveBy").val('');
				$("#transfer_rebateClaimApproveBy").prop("disabled", false);
				$('#transfer_rebateClaimApproveBy').select2().trigger('change');
				$("#transfer_claimPercentageRebate").val('');
				$("#transfer_adultClaimRebate").val('');
				$("#transfer_childClaimRebate").val('');
				$("#transfer_InfantClaimRebate").val('');
			}
	});
	//.Rebate Claim
    
	//Rebate Cost
	$("#transfer_rebateCost").change(function(){
		if ($("#transfer_rebateCost").val() == 'None')
			{
				$("#costRebateSection").hide();
				$("#transfer_costApprovedBy").val('');
				$('#transfer_costApprovedBy').select2().trigger('change');
				$("#transfer_costApprovedBy").prop("disabled", true);
				$("#transfer_costPercentageRebate").val('');
				$("#transfer_adultCostRebate").val('');
				$("#transfer_childCostRebate").val('');
				$("#transfer_InfantCostRebate").val('');
			}
		else if ($("#transfer_rebateCost").val() == 'Percentage')
			{
				$("#costRebateSection").show();
				$("#transfer_costPercentageRebate").show();
				$("#costRebateFix").hide();
				$("#transfer_costApprovedBy").prop("disabled", false);
				$("#transfer_adultCostRebate").val('');
				$("#transfer_childCostRebate").val('');
				$("#transfer_InfantCostRebate").val('');
			}
		else if ($("#transfer_rebateCost").val() == 'Fixed Tariff')
			{
				$("#costRebateSection").show();
				$("#transfer_costPercentageRebate").hide();
				$("#costRebateFix").show();
				$("#transfer_costApprovedBy").prop("disabled", false);
				$("#transfer_costPercentageRebate").val('');
			}
		else if ($("#transfer_rebateCost").val() == 'FOC')
			{
				$("#transfer_costPercentageRebate").hide();
				$("#costRebateSection").hide();
				$("#transfer_costApprovedBy").val('');
				$("#transfer_costApprovedBy").prop("disabled", false);
				$('#transfer_costApprovedBy').select2().trigger('change');
				$("#transfer_costPercentageRebate").val('');
				$("#transfer_adultCostRebate").val('');
				$("#transfer_childCostRebate").val('');
				$("#transfer_InfantCostRebate").val('');
			}
	});
	//.Rebate Cost
});

// New Transfer
function newTransfer(transferData){
    target_action = transferData.action;
   
	$('.bookingTransfer').val('');
	$('.bookingTransfer').val(null).trigger('change');
	$("#transfer_status").val("QUOTE");
	$('#transfer_status').select2().trigger('change'); 
	$("#transfer_paidBy").val("TO");
	$('#transfer_paidBy').select2().trigger('change'); 
	$("#transfer_type").val("NONE");
	$('#transfer_type').select2().trigger('change'); 
    $("#transfer_port").prop( "disabled", true);
    $("#transfer_port").empty();
    $("#transfer_port").append('<option value="NONE">NONE</option>');
    $("#transfer_vehicle").prop( "disabled", false);
    $("#transfer_vehicle").empty();
    $("#transfer_vehicle").append('<option value="NONE">NONE</option>');
    $(".arrivalLine").show();
    $(".departureLine").show();
    $(".pickupLine").hide();
    $(".destinationFrom").hide();
    $(".destinationTo").show();
    $("#transfer_destination_from").val('0');
    $('#transfer_destination_from').select2().trigger('change');	
    $("#transfer_destination_to").val('0');
    $('#transfer_destination_to').select2().trigger('change');	
    $("#transfer_arrivalDate").val('');
    $("#transfer_arrivalFlight").val('');
    $("#transfer_arrivalTime").val('');
    $("#transfer_departureDate").val('');
    $("#transfer_departureFlight").val('');
    $("#transfer_departureTime").val('');
    $("#transfer_pickupDate").val('');
    $("#transfer_pickupFlight").val('');
    $("#transfer_pickupTime").val('');

	$("#transfer_rebateClaim").val("None");
	$('#transfer_rebateClaim').select2().trigger('change'); 
	$("#transfer_rebateCost").val("None");
	$('#transfer_rebateCost').select2().trigger('change'); 
	$("#transfer_rebateClaimApproveBy").prop("disabled", true);
	$("#transfer_costApprovedBy").prop("disabled", true);
	$('#transfer_date').val('');
    // Booking Date
    var dateToday = new Date(); 
	$('#transfer_bookingDate').daterangepicker({
		"singleDatePicker": true,
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		maxDate: dateToday,
		locale: {
					format: 'DD/MM/YYYY'
				}
	});
    
    $(".destinationFrom").hide();
    $(".destinationTo").hide();
    $("#transfer_destination_from").val('0');
    $('#transfer_destination_from').select2().trigger('change');	
    $("#transfer_destination_to").val('0');
    $('#transfer_destination_to').select2().trigger('change');	
	$('#transfer_departureDate').val('');
    // .Booking Date
	loadTourOperator(transferData);
    
    //Arrival Date
    var booking_from = $("#booking_travelDate").data('daterangepicker').startDate._d;
    var booking_to = $("#booking_travelDate").data('daterangepicker').endDate._d;
    $('#transfer_arrivalDate').daterangepicker({
        "singleDatePicker": true,
        "showDropdowns": true,
        "autoApply": true,
        "opens": "center",
        minDate:booking_from,
        maxDate: booking_to,
        locale: {
                    format: 'DD/MM/YYYY'
                }
    });
	$('#transfer_arrivalDate').val('');
    //.Arrival Date
    
    //Pickup Date
    var booking_from = $("#booking_travelDate").data('daterangepicker').startDate._d;
    var booking_to = $("#booking_travelDate").data('daterangepicker').endDate._d;
    $('#transfer_pickupDate').daterangepicker({
        "singleDatePicker": true,
        "showDropdowns": true,
        "autoApply": true,
        "opens": "center",
        minDate:booking_from,
        maxDate: booking_to,
        locale: {
                    format: 'DD/MM/YYYY'
                }
    });
    $('#transfer_pickupDate').on('apply.daterangepicker', function(ev, picker) {
       validationValueTransferClaim();
    });
    //.Pickup Date
    $("#btn-saveTransfer").prop("disabled", false);
  //  $("#btn-deleteTransfer").prop("disabled", false);
}
// .New Activity

// Tour Operator
function loadTourOperator(transferData){
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking;
		$.ajax({
			url: url_search_booking,
			method: "POST",
			dataType: "json",
			success: function (data) 
			{
				$("#transfer_payer").empty();
				$("#transfer_payer").append('<option value="' + data[0].id_tour_operator + '">'+data[0].to_name+'</option>');

			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
		});
    
}
// .Tour Operator

// Booking Client
function loadBookingClient(transferData){
         const url_search_booking = "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking;
            $.ajax({
                url: url_search_booking,
                method: "POST",
                dataType: "json",
                success: function (data) 
                {
                    $("#transfer_payer").empty();
                    $.each(data, function (key, val) {
                    $("#transfer_payer").append('<option value="' + val.id_client + '">'+val.title+ ' '+val.surname+' '+val.other_name+'</option>');
                    });  

                },
                error: function (error) 
                {
                    console.log('Error ${error}');
                }
            });
}
// .Booking Client

// Validation Value for Transfer Vehicle
function validationTransferVehicle(){
    if ($("#transfer_type").val() != null &&  $("#transfer_port").val() != null )
        {
            var transferData = {
                    id_booking: $("#id_booking").val(),
                    transfer_type: $("#transfer_type").val(),
                    transfer_port: $("#transfer_port").val()
            }   
             loadVehicleProduct(transferData);
        }
}
//. Validation Value for Transfer Claim

// Vehicle List - Product Vehicle
function loadVehicleProduct(transferData){
    const url_product = "php/api/bookingSystem/transferList.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_product,
        method: "POST",
        dataType: "json",
        data:transferData ,
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
}
    
    
// .Vehicle List - Product Vehicle

// Validation Value for Transfer Claim
function validationValueTransferClaim(){
    if ($("#transfer_type").val() == 'BOTH')
        {
             if ($("#transfer_vehicle").val() != 0 && $("#transfer_destination_to").val() != 0 && $("#transfer_payer").val() != "" && $("#transfer_arrivalDate").val() != "" && $("#transfer_departureDate").val() != "" ) 
             {
                 console.log("Load Transfer Tariff");
                 var transferData = {
                        id_booking: $('#id_booking').val(),
                        transfer_bookingDate: $('#transfer_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        transfer_paidBy: $('#transfer_paidBy').val(),
                        transfer_payer: $('#transfer_payer').val(),
                        transfer_type: $('#transfer_type').val(),
                        transfer_port: $('#transfer_port').val(),
                        transfer_vehicle: $('#transfer_vehicle').val(),
                        transfer_destination_from: $('#transfer_destination_to').val(),
                        transfer_destination_to: $('#transfer_destination_to').val(),
                        transfer_arrivalDate: $('#transfer_arrivalDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        transfer_departureDate: $('#transfer_departureDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                        action: target_action
                }
                loadTransferArrivalClaim(transferData);   
             }
        }
    else if ($("#transfer_type").val() == 'ARR')
        {
             if ($("#transfer_vehicle").val() != 0 && $("#transfer_destination_to").val() != 0 && $("#transfer_payer").val() != "" && $("#transfer_pickupDate").val() != "")
             {
                console.log("Load Transfer Tariff");

                var transferData = {
                    id_booking: $('#id_booking').val(),
                    transfer_bookingDate: $('#transfer_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    transfer_paidBy: $('#transfer_paidBy').val(),
                    transfer_payer: $('#transfer_payer').val(),
                    transfer_type: $('#transfer_type').val(),
                    transfer_port: $('#transfer_port').val(),
                    transfer_vehicle: $('#transfer_vehicle').val(),
                    transfer_destination_from: $('#transfer_destination_from').val(),
                    transfer_destination_to: $('#transfer_destination_to').val(),
                    transfer_arrivalDate: $('#transfer_pickupDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    transfer_departureDate: '',
                    action: target_action
                }
                loadTransferArrivalClaim(transferData);   
             }
        }
    else if ($("#transfer_type").val() == 'DEP')
        {
             if ($("#transfer_vehicle").val() != 0 && $("#transfer_destination_from").val() != 0 && $("#transfer_payer").val() != "" && $("#transfer_pickupDate").val() != "")
             {
                console.log("Load Transfer Tariff");

                var transferData = {
                    id_booking: $('#id_booking').val(),
                    transfer_bookingDate: $('#transfer_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    transfer_paidBy: $('#transfer_paidBy').val(),
                    transfer_payer: $('#transfer_payer').val(),
                    transfer_type: $('#transfer_type').val(),
                    transfer_port: $('#transfer_port').val(),
                    transfer_vehicle: $('#transfer_vehicle').val(),
                    transfer_destination_from: $('#transfer_destination_from').val(),
                    transfer_destination_to: $('#transfer_destination_to').val(),
                    transfer_arrivalDate: '',
                    transfer_departureDate: $('#transfer_pickupDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    action: target_action
                }
                loadTransferDepartureClaim(transferData);   
             }
        }
    else if ($("#transfer_type").val() == 'INTER HOTEL')
        {
             if ($("#transfer_vehicle").val() != 0 && $("#transfer_destination_from").val() != 0&& $("#transfer_destination_to").val() != 0 && $("#transfer_payer").val() != "" && $("#transfer_pickupDate").val() != "")
             {
                console.log("Load Transfer Tariff");

                var transferData = {
                    id_booking: $('#id_booking').val(),
                    transfer_bookingDate: $('#transfer_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    transfer_paidBy: $('#transfer_paidBy').val(),
                    transfer_payer: $('#transfer_payer').val(),
                    transfer_type: $('#transfer_type').val(),
                    transfer_port: '',
                    transfer_vehicle: $('#transfer_vehicle').val(),
                    transfer_destination_from: $('#transfer_destination_from').val(),
                    transfer_destination_to: $('#transfer_destination_to').val(),
                    transfer_arrivalDate: '',
                    transfer_departureDate: $('#transfer_pickupDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    action: target_action
                }
                loadTransferDepartureClaim(transferData);   
             }
        }
    else if ($("#transfer_type").val() == 'ACTIVITY')
        {
             if ($("#transfer_vehicle").val() != 0 && $("#transfer_destination_from").val() != 0&& $("#transfer_destination_to").val() != 0 && $("#transfer_payer").val() != "" && $("#transfer_pickupDate").val() != "")
             {
                console.log("Load Transfer Tariff");

                var transferData = {
                    id_booking: $('#id_booking').val(),
                    transfer_bookingDate: $('#transfer_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    transfer_paidBy: $('#transfer_paidBy').val(),
                    transfer_payer: $('#transfer_payer').val(),
                    transfer_type: $('#transfer_type').val(),
                    transfer_port: $('#transfer_port').val(),
                    transfer_vehicle: $('#transfer_vehicle').val(),
                    transfer_destination_from: $('#transfer_destination_from').val(),
                    transfer_destination_to: $('#transfer_destination_to').val(),
                    transfer_arrivalDate: '',
                    transfer_departureDate: $('#transfer_pickupDate').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    action: target_action
                }
                loadTransferDepartureClaim(transferData);   
             }
        }
}
//. Validation Value for Transfer Claim

// Transfer Claim
function loadTransferArrivalClaim(transferData){
    console.log("Arrival");
    if ($('#transfer_type').val() == "BOTH")
        {
            transferData.transfer_type = "ARR";
        }
	 const url_serviceClaim = "php/api/bookingSystem/transferServiceClaim.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking;
		$.ajax({
			url: url_serviceClaim,
			method: "POST",
			dataType: "json",
        	data : transferData, 
			success: function (data) 
			{
                if (data[0].OUTCOME == 'OK')
                {
                    console.log(data);
                    var charge = data[0].charge;
                    console.log(transferData.transfer_type);
                    
                    if (charge == 'PAX')
                    {
                        $(".pax_charge").show();
                        $(".unit_charge").hide();
                        $(".rebateAdult").html();
                        $(".rebateAdult").html('Adult');
                        if ($('#transfer_type').val() == "BOTH")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#id_product_service_arr_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ARR")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "DEP")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "INTER HOTEL")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").show();
                                $("#transfer_activity").hide();
                                $("#ps_unit_int_claim").html(data[0].ps_adult_claim);
                                $("#ps_adult_int_claim").html(data[0].ps_adult_claim);
                                $("#ps_child_int_claim").html(data[0].ps_child_claim);
                                $(".ps_claim_int_cur").html(" "+data[0].claim_curr);
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ACTIVITY")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").show();
                                $("#ps_unit_act_claim").html(data[0].ps_adult_claim);
                                $("#ps_adult_act_claim").html(data[0].ps_adult_claim);
                                $("#ps_child_act_claim").html(data[0].ps_child_claim);
                                $(".ps_claim_act_cur").html(" "+data[0].claim_curr);
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        $("#ps_adult_arr_claim").html(data[0].ps_adult_claim);
                        $("#ps_child_arr_claim").html(data[0].ps_child_claim);
                        $(".ps_claim_arr_cur").html(" "+data[0].claim_curr);
                        $("#ps_adult_dep_claim").html(data[0].ps_adult_claim);
                        $("#ps_child_dep_claim").html(data[0].ps_child_claim);
                        $(".ps_claim_dep_cur").html(" "+data[0].claim_curr);
                        $("#transfer_childClaimRebate").hide();
                        $(".rebateChild").hide();
                        }
                    else if (charge == 'UNIT')
                    {
                        $(".unit_charge").show();
                        $(".pax_charge").hide();
                        $("#rebateAdult").html();
                        $(".rebateAdult").html('Unit');
                        $("#transfer_teenRebate").hide();
                        $(".rebateTeen").hide();
                        $("#transfer_childClaimRebate").hide();
                        $(".rebateChild").hide();
                        $("#transfer_InfantClaimRebate").hide();
                        $(".rebateInfant").hide();
                        if ($('#transfer_type').val() == "BOTH")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ARR")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "DEP")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "INTER HOTEL")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").show();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ACTIVITY")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").show();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        $("#ps_unit_arr_claim").html(data[0].ps_adult_claim);
                        $("#ps_unit_dep_claim").html(data[0].ps_adult_claim);
                        $(".ps_unit_arr_cur").html(" "+data[0].claim_curr);
                    }
                    
                    $("#id_product_service_arr_claim").val(data[0].id_product_service_claim);
                    $("#transfer_description").html(data[0].description);
                    if ($('#transfer_type').val() == "BOTH")
                        {
                            transferData.transfer_type = "DEP";
                            loadTransferDepartureClaim(transferData);   
                        }
                    else
                        {
                            if($('#serviceDetails').hasClass('in') === false) 
                            {
                                $("#serviceDetails").collapse('toggle');  
                                $("#dossierService").collapse('toggle');  
                            } 
                        }
                    //loadTransferExtra(data[0].id_product_service_claim);
                }
                else if (data[0].OUTCOME == 'NO DATA') 
                 {
                    toastr.warning('No tariff found, please select another vehicle or date');
                    if($('#dossierService').hasClass('in') === false) 
                    {
                        $("#id_product_service_arr_claim").val('');
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                 }
                else
                 {
                    toastr.warning('An error occured. Please do it again ');
                    if($('#dossierService').hasClass('in') === false) 
                    {
                        $("#id_product_service_arr_claim").val('');
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                 }
			},
			error: function (error) 
			{
                toastr.warning('An error occured. Please do it again');
				console.log('Error ${error}');
			}
		});
}

function loadTransferDepartureClaim(transferData){
    console.log(transferData.transfer_type);
    if ($('#transfer_type').val() == "BOTH")
        {
            transferData.transfer_type = "DEP";
        }
	 const url_serviceClaim = "php/api/bookingSystem/transferServiceClaim.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking;
		$.ajax({
			url: url_serviceClaim,
			method: "POST",
			dataType: "json",
        	data : transferData, 
			success: function (data) 
			{
                if (data[0].OUTCOME == 'OK')
                {
                    console.log(data);
                    var charge = data[0].charge;
                    console.log($('#transfer_type').val());
                    
                    if (charge == 'PAX')
                    {
                        $(".pax_charge").show();
                        $(".unit_charge").hide();
                        $(".rebateAdult").html();
                        $(".rebateAdult").html('Adult');
                        if ($('#transfer_type').val() == "BOTH")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ARR")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "DEP")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "INTER HOTEL")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").show();
                                $("#transfer_activity").hide();
                                $("#ps_unit_int_claim").html(data[0].ps_adult_claim);
                                $("#ps_adult_int_claim").html(data[0].ps_adult_claim);
                                $("#ps_child_int_claim").html(data[0].ps_child_claim);
                                $(".ps_claim_int_cur").html(" "+data[0].claim_curr);
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ACTIVITY")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").show();
                                $("#ps_unit_act_claim").html(data[0].ps_adult_claim);
                                $("#ps_adult_act_claim").html(data[0].ps_adult_claim);
                                $("#ps_child_act_claim").html(data[0].ps_child_claim);
                                $(".ps_claim_act_cur").html(" "+data[0].claim_curr);
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        $("#ps_adult_dep_claim").html(data[0].ps_adult_claim);
                        $("#ps_child_dep_claim").html(data[0].ps_child_claim);
                        $(".ps_claim_dep_cur").html(" "+data[0].claim_curr);
                        $("#transfer_childClaimRebate").hide();
                        $(".rebateChild").hide();
                        }
                    else if (charge == 'UNIT')
                    {
                        $(".unit_charge").show();
                        $(".pax_charge").hide();
                        $("#rebateAdult").html();
                        $(".rebateAdult").html('Unit');
                        $("#transfer_teenRebate").hide();
                        $(".rebateTeen").hide();
                        $("#transfer_childClaimRebate").hide();
                        $(".rebateChild").hide();
                        $("#transfer_InfantClaimRebate").hide();
                        $(".rebateInfant").hide();
                        if ($('#transfer_type').val() == "BOTH")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ARR")
                            {
                                $("#transfer_in").show();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "DEP")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").show();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").hide();
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "INTER HOTEL")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").show();
                                $("#transfer_activity").hide();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        else if ($('#transfer_type').val() == "ACTIVITY")
                            {
                                $("#transfer_in").hide();
                                $("#transfer_out").hide();
                                $("#transfer_interHotel").hide();
                                $("#transfer_activity").show();
                                $("#id_product_service_arr_claim").val('');
                                $("#id_product_service_dep_claim").val('');
                            }
                        $("#ps_unit_dep_claim").html(data[0].ps_adult_claim);
                    }
                    
                    $("#id_product_service_dep_claim").val(data[0].id_product_service_claim);
                    $("#transfer_description").html(data[0].description);
                  
                    if($('#serviceDetails').hasClass('in') === false) {
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                    
                    //loadTransferExtra(data[0].id_product_service_claim);
                }
                
                else if (data[0].OUTCOME == 'NO DATA') 
                 {
                    toastr.warning('No tariff found, please select another vehicle or date');
                    if($('#dossierService').hasClass('in') === false) 
                    {
                        $("#id_product_service_arr_claim").val('');
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                 }
                else
                 {
                    toastr.warning('An error occured. Please do it again ');
                    if($('#dossierService').hasClass('in') === false) 
                    {
                        $("#id_product_service_arr_claim").val('');
                        $("#id_product_service_dep_claim").val('');
                        $("#serviceDetails").collapse('toggle');  
                        $("#dossierService").collapse('toggle');  
                    } 
                 }
			},
			error: function (error) 
			{
                toastr.warning('An error occured. Please do it again 2');
				console.log('Error ${error}');
			}
		});
}
// .Transfer Claim

//Reset Client
function resetClient(){
    if (target_action  != 'READ')
        {
            $("#transfer_client").val('default').selectpicker("refresh");    
            $("#transfer_adultAmt").val(0);
            $("#transfer_childAmt").val(0);
            $("#transfer_infantAmt").val(0);
        }
}
//.Reset Client
