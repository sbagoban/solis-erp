// JavaScript Document
/*Date : 2019, 16 October
Application : Booking System
Developer : slouis@solis360.com*/


$(function(){
	
$(".nav-link").on('click', function(e)
{
	var id_booking = $('#id_booking').val();
	var id_booking_r = $('#id_booking').attr("readonly");
	if (id_booking_r == 'readonly' && id_booking != '')
	{
		tabClicked = $(this).attr('aria-controls');
		$('#myTabContent').load("php/application/booking/"+tabClicked+".php");
	}
	else
	{
		alert("Open a Dossier");
		$('#myTabContent').html('');
		$('.nav-item').removeClass('active');
	}
});


});
	