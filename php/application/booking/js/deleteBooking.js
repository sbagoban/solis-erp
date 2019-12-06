// JavaScript Document
/*Date : 2019, 16 October
Application : Booking System
Developer : slouis@solis360.com*/

$(function(){
	// Delete Booking
	$('#btn-deleteBooking').click(
	function()
	{
		var saveError = false;
		if(!saveError)
		{
			if($('#id_booking').val() != 0 ||$('#id_booking').val() != '')
			{
                var deleteConfirm = confirm("Are you sure you want to delete the dossier?");
                if(deleteConfirm)
                {
                    // delete dossier
                    deleteBooking();
                }
			}
			else
			{
				alert("An error occured, reload dossier");
			}
			
		}

	});
	// .Save Booking
});

function deleteBooking() { 
	var id_booking = $('#id_booking').val();
    var objReservationDossier = {id_booking: id_booking};
    const url_delete_booking = "php/api/bookingSystem/deleteBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
    $.ajax({
        url : url_delete_booking,
        method : "POST",
        data : objReservationDossier, 
        dataType: "json",                                                                             
        success : function(data){
            if (data.OUTCOME == 'OK')
                {
                    toastr.error('Booking deleted successfully');
                    newBooking();
                }
            else
                {
                     toastr.warning('An error occured. Please do it again');
                }
        },
        error: function(error) {
            toastr.info('An error occured. Please do it again');
            console.log('Error ${error}');
        }
    });
}
