// JavaScript Document
/*Date : 2019, 17 October
Application : Client - Booking System
Developer : slouis@solis360.com*/

$(function(){
	// DELETE CLIENT
	$('#btn-deleteClient').click(
	function()
	{
        var deleteConfirm = confirm("Are you sure you want to delete the client?");
        if(deleteConfirm)
        {
			var id_booking = $('#id_booking').val();
			var id_client = $('#id_client').val();
			var id_booking_client = $('#id_booking_client').val();
			var clientData = {
				id_booking: id_booking,
				id_client: id_client,
				id_booking_client: id_booking_client
			}
		
            if($('#id_client').val() == '' && $('#id_booking_client').val() != '')
                {
                    alert("An error occured, please load back the Client form");
                }
            else if($('#id_client').val() != '' && $('#id_booking_client').val() == '')
                {
                    alert("An error occured, please load back the Client form");
                }
            else
                {	
                    deleteClient(clientData);
                }
        }

	});
	// .DELETE CLIENT
});

function deleteClient(clientData) { 
    var objReservationClient = {
        id_booking: clientData.id_booking,
        id_client: clientData.id_client,
        id_booking_client: clientData.id_booking_client
    }
    const url_delete_client = "php/api/bookingSystem/deleteBookingClient.php?t="  + encodeURIComponent(global_token) + "&id_booking=" +clientData.id_booking;
    $.ajax({
        url : url_delete_client,
        method : "POST",
        data : objReservationClient, 
        dataType: "json",                                                                             
        success : function(data){
            if (data.OUTCOME == 'OK')
                {
                    toastr.error('Client deleted successfully');
                    newClient();
                    allBookingClient(data.id_booking);
                }
            else
                {
                     toastr.warning('An error occured. Please do it again');
                }
        },
        error: function(error) {
            console.log('Error ${error}');
            toastr.warning('An error occured. Please do it again');
        }
    });
}
