// JavaScript Document
/*Date : 2019, 16 October
Application : Booking System
Developer : slouis@solis360.com*/

$(function(){
	// Save Booking
	$('#btn-saveBooking').click(
	function()
	{
		var saveError = false;
		if($('#booking_toName').val() == "" || $('#booking_toName').val() == 0)
		{
			alert("Select Tour Operator");
			saveError = true;
		}
		else if ($('#booking_paxOrigin').val() == "" || $('#booking_paxOrigin').val() == 0)
		{
			alert("Select Pax Origin");
			saveError = true;
		}
		else if($('#booking_dept').val() == "" || $('#booking_dept').val() == 0)
		{
			alert("Select Department");
			saveError = true;
		}
		else if($('#booking_dossierName').val() == "" || $('#booking_dossierName').val() == 0)
		{
			alert("Input Dossier name");
			saveError = true;
		}
		else if($('#booking_clientType').val() == "" || $('#booking_clientType').val() == 0)
		{
			alert("Select clien type");
			saveError = true;
		}
		else if($('#booking_travelDate').val() == "" || $('#booking_travelDate').val() == 0)
		{
			alert("Input travel date");
			saveError = true;
		}
		else if($('#booking_status').val() == "" || $('#booking_status').val() == 0)
		{
			alert("Select reservation status");
			saveError = true;
		}

		if(!saveError)
		{
			if($('#booking_adultAmt').val() > 0 || $('#booking_teenAmt').val() > 0)
			{
				if($('#id_booking').val() == 0 ||$('#id_booking').val() == '')
				{
					// save dossier
					saveBooking();
				}
				else
				{
                    var updateConfirm = confirm("Are you sure you want to update the dossier?");
                    if(updateConfirm)
                    {
                        // update dossier
                        updateBooking();
                    }
				}
			}
			else
			{
				alert("One adult or Teen is required per booking");
			}
		}

	});
	// .Save Booking
});

function saveBooking() { 
    var booking_toRef = $('#booking_toRef').val().toUpperCase();
    var booking_toName = $('#booking_toName').val();
    var booking_paxOrigin = $('#booking_paxOrigin').val();
    var booking_dept = $('#booking_dept').val();
    var booking_dossierName = $('#booking_dossierName').val().toUpperCase();
    var booking_clientType = $('#booking_clientType').val();
	var booking_from = $("#booking_travelDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
	var booking_to = $("#booking_travelDate").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var booking_adultAmt = $('#booking_adultAmt').val();
    var booking_teenAmt = $('#booking_teenAmt').val();
    var booking_childAmt = $('#booking_childAmt').val();
    var booking_infantAmt = $('#booking_infantAmt').val();
    var booking_status = $('#booking_status').val();
    if( $("#booking_closureDate").val() == '')
       {
           var booking_closureDate = booking_to;
       }
    else
       {
            var booking_closureDate = $("#booking_closureDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
       }
    var booking_remarks = $('#booking_remarks').val();
    
    var objReservationDossier = {
        id_booking: -1,
        booking_toRef: booking_toRef,
        booking_toName: booking_toName,
        booking_paxOrigin: booking_paxOrigin,
        booking_dept: booking_dept,
        booking_dossierName: booking_dossierName,
        booking_clientType: booking_clientType,
        booking_from: booking_from,
        booking_to: booking_to,
        booking_adultAmt: booking_adultAmt,
        booking_teenAmt: booking_teenAmt,
        booking_childAmt: booking_childAmt,
        booking_infantAmt: booking_infantAmt,
        booking_status: booking_status,
        booking_closureDate: booking_closureDate,
        booking_remarks: booking_remarks
    }
    const url_save_booking = "php/api/bookingSystem/saveBooking.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_booking,
        method : "POST",
        data : objReservationDossier, 
        dataType: "json",                                                                           
        success : function(data){
            if (data.OUTCOME == 'OK')
                {
                    $('#id_booking').val(data.id_booking);
                    $('#booking_createdBy').val(data.created_by);
                    $('#myTabContent').html('');
                    $('.nav-item').removeClass('active');
                    toastr.success('New Booking saved successfully');
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


function updateBooking() { 
	var id_booking = $('#id_booking').val();
    var booking_toRef = $('#booking_toRef').val();
    var booking_toName = $('#booking_toName').val();
    var booking_paxOrigin = $('#booking_paxOrigin').val();
    var booking_dept = $('#booking_dept').val();
    var booking_dossierName = $('#booking_dossierName').val();
    var booking_clientType = $('#booking_clientType').val();
	var booking_from = $("#booking_travelDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
	var booking_to = $("#booking_travelDate").data('daterangepicker').endDate.format('YYYY-MM-DD');
    var booking_adultAmt = $('#booking_adultAmt').val();
    var booking_teenAmt = $('#booking_teenAmt').val();
    var booking_childAmt = $('#booking_childAmt').val();
    var booking_infantAmt = $('#booking_infantAmt').val();
    var booking_status = $('#booking_status').val();
    var booking_closureDate = $("#booking_closureDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var booking_remarks = $('#booking_remarks').val();
    
    var objReservationDossier = {
        id_booking: id_booking,
        booking_toRef: booking_toRef,
        booking_toName: booking_toName,
        booking_paxOrigin: booking_paxOrigin,
        booking_dept: booking_dept,
        booking_dossierName: booking_dossierName,
        booking_clientType: booking_clientType,
        booking_from: booking_from,
        booking_to: booking_to,
        booking_adultAmt: booking_adultAmt,
        booking_teenAmt: booking_teenAmt,
        booking_childAmt: booking_childAmt,
        booking_infantAmt: booking_infantAmt,
        booking_status: booking_status,
        booking_closureDate: booking_closureDate,
        booking_remarks: booking_remarks
    }
    const url_save_booking = "php/api/bookingSystem/updateBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
    $.ajax({
        url : url_save_booking,
        method : "POST",
        data : objReservationDossier, 
        dataType: "json",                                                                             
        success : function(data){
            if (data.OUTCOME == 'OK') 
                {
                    $('#myTabContent').html('');
                    $('.nav-item').removeClass('active');
                    toastr.info('Booking updated successfully');
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
