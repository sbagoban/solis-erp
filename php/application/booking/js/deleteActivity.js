// JavaScript Document
/*Date : 2019, 15 November
Application : Activity - Booking System
Developer : slouis@solis360.com*/
$(function(){
	// DELETE ACTIVITY
	$('#btn-deleteActivity').click(
	function()
	{
        var id_booking_activity = $('#id_booking_activity').val();
        var id_booking = $('#id_booking').val();
        var activityData = {
            id_booking: id_booking,
            id_booking_activity: id_booking_activity,
            action: 'DELETE'
        }
        if($('#id_booking_activity').val() == '' && $('#id_booking_activity').val() != '')
			{
				alert("An error occured, please load back the Activity");
			}
		else
			{	
                var deleteConfirm = confirm("Are you sure you want to delete the booking activity?");
                if(deleteConfirm)
                    {
                        deleteActivity(activityData);
                    }
            }
    });
    
    
});

function deleteActivity(activityData) { 
    var objReservationActivity = {
        id_booking: activityData.id_booking,
        id_booking_activity: activityData.id_booking_activity,
        action: activityData.action
    }
     const url_delete_activity = "php/api/bookingSystem/deleteBookingActivity.php?t="  + encodeURIComponent(global_token) + "&id_booking=" +activityData.id_booking + "&id_booking_activity=" +activityData.id_booking_activity;
    $.ajax({
        url : url_delete_activity,
        method : "POST",
        data : objReservationActivity, 
        dataType: "json",                                                                             
        success : function(data){
             if (data.OUTCOME == 'OK')
                {
                    if(objReservationActivity.action == 'DELETE')
                        {
                            toastr.error('Activity deleted successfully');
                            newActivity(objReservationActivity);
                            allBookingActivity(data.id_booking);
                            $("#dossierService").collapse('show');
                            $("#serviceDetails").collapse('hide');
                        }
                     else if(objReservationActivity.action == 'UPDATE')
                        {
                           saveActivity(objReservationActivity);
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