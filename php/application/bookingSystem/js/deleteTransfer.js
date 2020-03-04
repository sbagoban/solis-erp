// JavaScript Document
/*Date : 2020, 15 January
Application : Transfer - Booking System
Developer : slouis@solis360.com*/
$(function(){
	// DELETE TRANSFER
	$('#btn-deleteTransfer').click(
	function()
	{
        var id_booking_transfer_claim = $('#id_booking_transfer_claim').val();
        var id_booking = $('#id_booking').val();
        var transferData = {
            id_booking: id_booking,
            id_booking_transfer_claim: id_booking_transfer_claim,
            action: 'DELETE'
        }
        if($('#id_booking_transfer_claim').val() == '' && $('#id_booking_transfer_claim').val() != '')
			{
				alert("An error occured, please load back the Transfer");
			}
		else
			{	
                var deleteConfirm = confirm("Are you sure you want to delete the booking transfer?");
                if(deleteConfirm)
                    {
                        deleteTransfer(transferData);
                    }
            }
    });
    
    
});

function deleteTransfer(transferData) { 
    var objReservationTransfer = {
        id_booking: transferData.id_booking,
        id_booking_transfer_claim: transferData.id_booking_transfer_claim,
        action: transferData.action
    }
     const url_delete_transfer = "php/api/bookingSystem/deleteBookingTransfer.php?t="  + encodeURIComponent(global_token) + "&id_booking=" +transferData.id_booking + "&id_booking_transfer_claim=" +transferData.id_booking_transfer_claim;
    $.ajax({
        url : url_delete_transfer,
        method : "POST",
        data : objReservationTransfer, 
        dataType: "json",                                                                             
        success : function(data){
             if (data.OUTCOME == 'OK')
                {
                    console.log(data);
                    if(objReservationTransfer.action == 'DELETE')
                        {
                            toastr.error('Transfer deleted successfully');
                            newTransfer(objReservationTransfer);
                            allBookingTransfer(data.id_booking);
                            $("#dossierService").collapse('show');
                            $("#serviceDetails").collapse('hide');
                            resetClient();
                        }
                     else if(objReservationTransfer.action == 'UPDATE')
                        {
                            saveTransfer(objReservationTransfer);
                        }
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