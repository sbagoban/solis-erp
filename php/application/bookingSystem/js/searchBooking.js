$(function(){
	$('#btn-searchBooking').click(
		function()
		{
			var id_booking = $('#id_booking').val();
			var id_booking_r = $('#id_booking').attr("readonly");
			if (typeof id_booking_r === 'undefined')
				{
					disableDossierField();
					$('.bookingDossier').val('');
					$('.bookingDossier').val(null).trigger('change');
					searchBooking(id_booking);
					$("#id_booking").attr("readonly", false); 
					var id_booking_r = $('#id_booking').attr("readonly");
				}
			else if (id_booking_r == 'readonly')
				{
					disableDossierField();
					$('.bookingDossier').val('');
					$('.bookingDossier').val(null).trigger('change');
					$("#id_booking").attr("readonly", false); 
					var id_booking_r = $('#id_booking').attr("readonly");
					$('#myTabContent').html('');
					$('.nav-item').removeClass('active');
				}
			else
				{
					console.log("SEARCH ERROR");
				}
		});
});


function runScript(e) {
    //See notes about 'which' and 'key'
    if (e.keyCode == 13) {
        var id_booking = document.getElementById("id_booking");
        searchBooking(id_booking.value);
        return false;
    }
}

function searchBooking(id_booking) {
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
    $.ajax({
        url: url_search_booking,
        method: "POST",
        dataType: "json",
        success: function (data) {
            displayData(data[0]);
            if (data[0].OUTCOME == 'OK')
                {
                    toastr.success('Booking found');
                }
            else
                {
                    toastr.warning('Booking not found');
                }
            
        },
        error: function (error) {
            toastr.warning('Booking not found');
            console.log('Error ${error}');
        }
    });
}

function displayData(bookingData) {
    enableDossierField();
	$('#booking_toRef').val(bookingData.to_ref);
	var id_tour_operator = bookingData.id_tour_operator;
	$("#booking_toName").val(id_tour_operator);
    $('#booking_toName').select2().trigger('change');
	var id_country = bookingData.id_country;
	$("#booking_paxOrigin").val(id_country);
    $('#booking_paxOrigin').select2().trigger('change');
	var id_dept = bookingData.id_dept;
	$("#booking_dept").val(id_dept);
    $('#booking_dept').select2().trigger('change');
	$('#booking_dossierName').val(bookingData.booking_for);
	var clientType = bookingData.pax_type;
	$("#booking_clientType").val(clientType);
    $('#booking_clientType').select2().trigger('change');
	var start_date = bookingData.booking_from;
	var date_from = start_date.split("-").reverse();
	var start_date = date_from[0]+"/"+date_from[1]+"/"+date_from[2];
	var end_date = bookingData.booking_to;
	var date_to = end_date.split("-").reverse();
	var end_date = date_to[0]+"/"+date_to[1]+"/"+date_to[2];
	var opt_date = bookingData.opt_date;
	var opt_to = end_date.split("-").reverse();
	var opt_date = opt_to[0]+"/"+opt_to[1]+"/"+opt_to[2];
	$('#booking_travelDate').data('daterangepicker').setStartDate(start_date);
	$('#booking_travelDate').data('daterangepicker').setEndDate(end_date);
	$('#booking_adultAmt').val(bookingData.adult_amt);
	$('#booking_teenAmt').val(bookingData.teen_amt);
	$('#booking_childAmt').val(bookingData.child_amt);
	$('#booking_infantAmt').val(bookingData.infant_amt);
	var status = bookingData.status;
	$("#booking_status").val(status);
    $('#booking_status').select2().trigger('change');
	$('#booking_closureDate').daterangepicker({
			    "singleDatePicker": true,
				"showDropdowns": true,
				"autoApply": true,
				"opens": "center",
				minDate: end_date,
				locale: {
							format: 'DD/MM/YYYY'
						}
			});
	$('#booking_closureDate').data('daterangepicker').setStartDate(opt_date);
	$('#booking_createdBy').val(bookingData.created_name);
	$('#booking_remarks').val(bookingData.remarks);
	$("#id_booking").attr("readonly", true); 
    $('#btn-saveBooking').prop('disabled', false);
    $('#btn-deleteBooking').prop('disabled', false);
}