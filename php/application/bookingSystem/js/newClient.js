// JavaScript Document
/*Date : 2019, 17 October
Application : Client - Booking System
Developer : slouis@solis360.com*/
$(function(){
	$('#client_dob').val('');
	var id_booking = $('#id_booking').val();
	allBookingClient(id_booking) 
    newClient();

	$('#btn-newClient').click(function() {
		newClient();
		enableClientField();
	});
});

function newClient(){
	$('.bookingClient').val('');
	$('.bookingClient').prop("checked", false);
	$('.bookingClient').val(null).trigger('change');
	$("#client_type").val('ADULT');
	$('#client_type').select2().trigger('change');
	$("#client_title").val('MR');
	$('#client_title').select2().trigger('change');
    $('#client_surname').val($('#booking_dossierName').val());
    $('#btn-saveBooking').prop('disabled', false);
    $('#btn-deleteClient').prop('disabled', true);
}

function disableClientField() {
    $('.bookingClient').attr("readonly", true); 
}

function enableClientField() {
    $('.bookingClient').attr("readonly", false); 
}


