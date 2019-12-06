// JavaScript Document
/*Date : 2019, 27 September
Application : Booking System
Developer : slouis@solis360.com*/

$("#aTitle").text("Booking System");

$(function(){
	$('.select2').select2({
		placeholder : 'Select'
	});
	  //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
	// .Main Page
	
	
	// Booking Section
		
		// Tour Operator
			$('#booking_toName').select2('val', '0');
			const url_tourOperator = "php/api/touroperators/allTourOperator.php?t=" + encodeURIComponent(global_token);

			$.ajax({
					type: "POST",
					url: url_tourOperator,
					dataType: "json",
					cache: false,
					success: function(data)
						{
							$("#booking_toName").empty();
							$.each(data, function (key, val) {
							$("#booking_toName").append('<option value="' + val.id_to + '">'+val.to_name+ ' - '+val.country_name+'</option>');
							});  
						}
					}
				);
		//.Tour Operator
		
		// Pax Origin
			const url_country = "php/api/countries/allCountry.php?t=" + encodeURIComponent(global_token);

			$.ajax({
					type: "POST",
					url: url_country,
					dataType: "json",
					cache: false,
					success: function(data)
						{
							$("#").empty();
							$.each(data, function (key, val) {
							$("#booking_paxOrigin").append('<option value="' + val.id_country + '">'+val.country_name+ '</option>');
							});  
						}
					}
				);
	
			$('#booking_toName').on("change", function (e) 
				{ 
					var booking_toName = $('#booking_toName').val();
					var objToName = {booking_toName: booking_toName};
					const url_TourOperator = "php/api/touroperators/tourOperatorCountry.php?t=" + encodeURIComponent(global_token) + "&id_tour_operator=" +booking_toName;
					$.ajax({
						url : url_TourOperator,
						method : "POST",
						data : objToName, 
						dataType: "json",    
						success : function(data){
							var id_country = data[0].id_country;
							$("#booking_paxOrigin").val(id_country);
							$('#booking_paxOrigin').select2().trigger('change');
    						$('#booking_paxOrigin').attr("disabled", false); 
						},
						error: function(error) {
							console.log('Error ${error}');
						}
					});
				});
		//.Pax Origin
	
		// Department

			const url_bookingDept = "php/api/department/bookingDept.php?t=" + encodeURIComponent(global_token);

			$.ajax({
					type: "POST",
					url: url_bookingDept,
					dataType: "json",
					cache: false,
					success: function(data)
						{
							$("#").empty();
							$.each(data, function (key, val) {
								if (val.dept_name == 'FIT'){
									$("#booking_dept").append('<option value="' + val.id_dept + '" selected>'+val.dept_name+ '</option>');
								}
								else
								{
									$("#booking_dept").append('<option value="' + val.id_dept + '">'+val.dept_name+ '</option>');
								}
							});  
						}
					}
				);

		//.Department
	
		// Travel Date
		var dateToday = new Date(); 
		$('#booking_travelDate').daterangepicker({
			"showDropdowns": true,
			"autoApply": true,
			"opens": "center",
			minDate: dateToday,
        	locale: {
            			format: 'DD/MM/YYYY'
        			}
		});
		// .Travel Date
	
		// Closure Date
		$('#booking_travelDate').on('apply.daterangepicker', function(ev, picker) {
			$('#booking_closureDate').readOnly = false;
			var travel_end = picker.endDate.format('DD/MM/YYYY');
			var travel = picker.endDate._d;
			$('#booking_closureDate').daterangepicker({
			    "singleDatePicker": true,
				"showDropdowns": true,
				"autoApply": true,
				"opens": "center",
				minDate: travel,
				locale: {
							format: 'DD/MM/YYYY'
						}
			});
		});
		// .Closure Date
	
	// .Booking Section
	
	// Client Section
	$('#client_dob').daterangepicker({
			    "singleDatePicker": true,
				"showDropdowns": true,
				"autoApply": true,
				"opens": "center",
				maxDate: dateToday,
				locale: {
							format: 'DD/MM/YYYY'
						}
	});
	// .Client Section
});

