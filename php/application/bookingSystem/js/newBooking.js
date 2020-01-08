$(function(){
    disableDossierField();
	
	$('#btn-newBooking').click(function() {
		enableDossierField();
		newBooking();
		$('#myTabContent').html('');
		$('.nav-item').removeClass('active');
	});
});

function newBooking(){
	$('#id_booking').val('');
	$('.bookingDossier').val('');
	$('.bookingDossier').val(null).trigger('change');
    $('#booking_paxOrigin').attr("disabled", true); 
	$("#booking_dept").val('19');
	$('#booking_dept').select2().trigger('change');
	$("#booking_clientType").val('NORMAL');
	$('#booking_clientType').select2().trigger('change');
	$("#booking_status").val('QUOTE');
	$('#booking_status').select2().trigger('change');
    $('#btn-saveBooking').prop('disabled', false);
	$('#myTabContent').html('');
	$('.nav-item').removeClass('active');
}

function disableDossierField() {
    $("#id_booking").attr("readonly", false); 
    $("#booking_toRef").attr("readonly", true);     
    $('#booking_toName').prop('disabled', true);
    $('#booking_paxOrigin').attr("disabled", true);    
    $('#booking_dept').prop('disabled', true);
    $("#booking_dossierName").attr("readonly", true);
    $('#booking_clientType').prop('disabled', true);
    $("#booking_travelDate").attr("readonly", true);
    $("#booking_adultAmt").attr("readonly", true);
    $("#booking_teenAmt").attr("readonly", true);
    $("#booking_childAmt").attr("readonly", true);
    $("#booking_infantAmt").attr("readonly", true);
    $('#booking_status').prop('disabled', true);
    $("#booking_createdBy").attr("readonly", true);
    $("#booking_description").attr("readonly", true);
    $('#btn-saveBooking').prop('disabled', true);
    $('#btn-deleteBooking').prop('disabled', true);
	$('#myTabContent').html('');
	$('.nav-item').removeClass('active');
}

function enableDossierField() {
    $("#id_booking").attr("readonly", true); 
    $("#booking_toRef").attr("readonly", false);     
    $('#booking_toName').prop('disabled', false);
    $('#booking_dept').prop('disabled', false);
    $("#booking_dossierName").attr("readonly", false);
    $('#booking_clientType').prop('disabled', false);
    $("#booking_travelDate").attr("readonly", false);
    $("#booking_adultAmt").attr("readonly", false);
    $("#booking_teenAmt").attr("readonly", false);
    $("#booking_childAmt").attr("readonly", false);
    $("#booking_infantAmt").attr("readonly", false);
    $('#booking_status').prop('disabled', false);
    $("#booking_description").attr("readonly", false);
}