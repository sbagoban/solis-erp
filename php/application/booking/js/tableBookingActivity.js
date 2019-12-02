function allBookingActivity(id_booking) {
$('#tbl-bookingActivity').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bookingSystem/allBookingActivity.php?t=" + encodeURIComponent(global_token)  + "&id_booking=" + id_booking,
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 4,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        "columnDefs": [
            { width: 200, targets: -1 }
        ],
        "buttons":[
            {
            }
        ],
        "columnDefs": [
        ],
        "columns" : [ {
            data: null,
                render: function ( data, type, row ) {
                    return data.product_name+' - '+data.service_name;
                },
                editField: ['product_name', 'service_name']
        },{
            "data" : "activity_date"
        },{
            "data" : "activity_rebate_type"
        },{
            
            data: null,
                render: function ( data, type, row ) {
                    return data.activity_total_claim_after_disc+' '+data.currency_code;
                },
                editField: ['activity_total_claim_after_disc', 'currency_code']
        },{
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnEditActivity" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteActivity" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ],
		"initComplete": function () {
            $('#tbl-bookingActivity tbody')
                .off()
                .on( 'click', '#btnEditActivity', function (e) {
                    var table = $('#tbl-bookingActivity').DataTable();
					var data = table.row( $(this).parents('tr') ).data();
					editActivity(data.id_booking_activity);
                })
                .on( 'click', '#btnDeleteActivity', function (e) {
                	var table = $('#tbl-bookingActivity').DataTable();
					var activityData = table.row( $(this).parents('tr') ).data();
                    var deleteConfirm = confirm("Are you sure you want to delete the booking activity?");
                    if(deleteConfirm)
                    {
                        activityData.action = 'DELETE'; 
                        deleteActivity(activityData);
                    }
                })
        }
    });
	
}

// EDIT ACTIVITY DATA
function editActivity(id_booking_activity) {
     const url_search_bookingActivity = "php/api/bookingSystem/readBookingActivity.php?t=" + encodeURIComponent(global_token) + "&id_booking_activity=" +id_booking_activity;
     $.ajax({
        url: url_search_bookingActivity,
        method: "POST",
        dataType: "json",
        success: function (data) {
            if (data[0].OUTCOME == 'OK')
                {
                    displayActivity(data);
                    toastr.success('Activity found');
                    $('#btn-deleteActivity').prop('disabled', false);
                }
            else
                {
                    toastr.warning('Activity not found');
                }
        },
        error: function (error) {
            console.log('Error ${error}');
            toastr.warning('Activity not found');
        }
    });
}

// DISPLAY ACTIVITY DATA
function displayActivity(activityDetails) {
    activityDetails[0].action = 'RESET';
    newActivity(activityDetails[0]);
    console.log(activityDetails[0]);
	$("#id_booking_activity").val(activityDetails[0].id_booking_activity);
    var bookingDate = activityDetails[0].activity_booking_date;
    var bookingDate = bookingDate.split("-").reverse();
    var activity_bookingDate = bookingDate[0]+"/"+bookingDate[1]+"/"+bookingDate[2];
    $('#activity_bookingDate').data('daterangepicker').setStartDate(activity_bookingDate);
	$("#activity_status").val(activityDetails[0].activity_status);
    $('#activity_status').select2().trigger('change');
    if (activityDetails[0].activity_status)
	$("#activity_paidBy").val(activityDetails[0].activity_service_paid_by);
    $('#activity_paidBy').select2().trigger('change');
	if (activityDetails[0].activity_service_paid_by == 'TO')
        {
            loadTourOperator(activityDetails[0].id_booking);
            $("#activity_payer").val(activityDetails[0].id_tour_operator);
            $('#activity_payer').select2().trigger('change');
        }
    else
        {
            loadBookingClient(activityDetails[0].id_booking);
            $("#activity_payer").val(activityDetails[0].id_client);
            $('#activity_payer').select2().trigger('change');
        }
    var activityDate = activityDetails[0].activity_date;
    var activityDate = activityDate.split("-").reverse();
    var activity_date = activityDate[0]+"/"+activityDate[1]+"/"+activityDate[2];
    $('#activity_date').data('daterangepicker').setStartDate(activity_date);
    var activityFullDate = $("#activity_date").data('daterangepicker').startDate._d + ' ';
   //Load activity ID product
    var activityFullDay = activityFullDate.split(" ");
    var activity_day = activityFullDay[0];
    var activityProductData = {
			id_booking: activityDetails[0].id_booking,
			paid_by: $('#activity_paidBy').val(),
			payer: $('#activity_payer').val(),
			activity_date: $('#activity_date').data('daterangepicker').startDate.format('YYYY-MM-DD'),
			activity_day :activity_day,
            activity_product :activityDetails[0].id_product
			}
   // .Load activity ID product
    // Product
	 const url_product = "php/api/bookingSystem/activityList.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityProductData.id_booking;
		$.ajax({
			url: url_product,
			method: "POST",
			dataType: "json",
        	data : activityProductData, 
			success: function (data) 
			{
				$("#activity_type").empty();
                var activityFound = false;
				$("#activity_type").append('<option value="" disabled>Select</option>');
				$.each(data, function (key, val) {
				    if(activityDetails[0].id_product == val.id_product )
                        {
                            var activityFound = true;
                            $("#activity_type").append('<option value="' + val.id_product + '" selected>'+val.product_name+ '</option>');
                        }
                    else
                        {
                            $("#activity_type").append('<option value="' + val.id_product + '">'+val.product_name+ '</option>');
                        }
                    if (activityFound == false)
                        {
                            console.log(activityDetails[0].id_product +" /"+ val.id_product);
                            // LLS - TO DO : query specific id_product
                            $("#activity_type").append('<option value="' + val.id_product + '" selected disabled>'+val.product_name+ '</option>');
                        }
                });
                // Product Service
                const url_productService = "php/api/bookingSystem/activityServiceList.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +activityProductData.id_booking;
                $.ajax({
                url: url_productService,
                method: "POST",
                dataType: "json",
                data : activityProductData, 
                success: function (data) 
                    {
                        console.log(data);
                        target_action = 'READ';
                        $("#activity_service").empty();
                        var activityServiceFound = false;
                        $("#activity_service").append('<option value="" disabled>Select</option>');
                        $.each(data, function (key, val) {
                            if(activityDetails[0].id_product_service == val.id_product_service )
                                {
                                    var activityServiceFound = true;
                                    $("#activity_service").append('<option value="' + val.id_product_service + '" selected>'+val.service_name+ '</option>');
                                }
                            else
                                {
                                    $("#activity_service").append('<option value="' + val.id_product_service + '">'+val.service_name+ '</option>');
                                }
                            if (activityServiceFound == false)
                                {
                                    console.log(activityDetails[0].id_product_service +" /"+ val.id_product_service);
                                    // LLS - TO DO : query specific id_product_service
                                    $("#activity_service").append('<option value="' + activityDetails[0].id_product_service + '" selected disabled>'+val.service_name+ '</option>');
                                }
                        });
                        $('#activity_service').select2().trigger('change'); 
                    },
                    error: function (error) 
                    {
                        console.log('Error ${error}');
                    }
                });
                //.Product Service       
			},
			error: function (error) 
			{
				console.log('Error ${error}');
			}
        });
    // .Product
    //.bookingActivityClient
    const url_search_bookingActivity = "php/api/bookingSystem/readBookingActivityClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_activity=" +activityDetails[0].id_booking_activity;
     $.ajax({
        url: url_search_bookingActivity,
        method: "POST",
        dataType: "json",
        success: function (data) {
            $.each(data, function (key, val) {
                    var selectedClient = val.id_client;
                console.log(selectedClient);
                    $('#activity_client').find('[value='+selectedClient+']').prop('selected', true);
                    $("#activity_client").selectpicker('refresh');
                });
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    //.bookingActivityClient
    $("#activity_adultAmt").val(activityDetails[0].activity_adult_amt);
	$("#activity_teenAmt").val(activityDetails[0].activity_teen_amt);
	$("#activity_childAmt").val(activityDetails[0].activity_child_amt);
	$("#activity_infantAmt").val(activityDetails[0].activity_infant_amt);
	$("#activity_rebate").val(activityDetails[0].activity_rebate_type);
    $('#activity_rebate').select2().trigger('change');
    if (activityDetails[0].activity_rebate_type == 'None')
        {
				$("#rebateSection").hide();
				$("#activity_approvedBy").val('');
				$('#activity_approvedBy').select2().trigger('change');
				$("#activity_approvedBy").prop("disabled", true);
				$("#activity_percentageRebate").val('');
				$("#activity_adultRebate").val('');
				$("#activity_teenRebate").val('');
				$("#activity_childRebate").val('');
				$("#activity_InfantRebate").val('');
        }
    else if (activityDetails[0].activity_rebate_type == 'Percentage')
        {
            $("#rebateSection").show();
            $("#activity_approvedBy").prop("disabled", false);
            $("#activity_approvedBy").val(activityDetails[0].activity_rebate_approve_by);
            $('#activity_approvedBy').select2().trigger('change');
            $("#activity_percentageRebate").show();
            $("#activity_percentageRebate").val(activityDetails[0].activity_discount_percentage);
            $("#rebate_fix").hide();
            $("#activity_adultRebate").val('');
            $("#activity_teenRebate").val('');
            $("#activity_childRebate").val('');
            $("#activity_InfantRebate").val('');
        }
    else if (activityDetails[0].activity_rebate_type == 'Fixed Tariff')
        {
            $("#rebateSection").show();
            $("#rebate_fix").show();
            $("#activity_adultRebate").val(activityDetails[0].activity_adult_claim_after_disc);
            $("#activity_teenRebate").val(activityDetails[0].activity_teen_claim_after_disc);
            $("#activity_childRebate").val(activityDetails[0].activity_child_claim_after_disc);
            $("#activity_InfantRebate").val(activityDetails[0].activity_infant_claim_after_disc);
            $("#activity_percentageRebate").hide();
            $("#activity_approvedBy").prop("disabled", false);
            $("#activity_percentageRebate").val('');
        }
    else if (activityDetails[0].activity_rebate_type == 'FOC')
        {
            $("#activity_percentageRebate").hide();
            $("#rebateSection").hide();
            $("#activity_approvedBy").val('');
            $("#activity_approvedBy").prop("disabled", false);
            $('#activity_approvedBy').select2().trigger('change');
            $("#activity_percentageRebate").val('');
            $("#activity_adultRebate").val('');
            $("#activity_teenRebate").val('');
            $("#activity_childRebate").val('');
            $("#activity_InfantRebate").val('');
        }
    $("#activity_representative").val(activityDetails[0].id_rep);
    $('#activity_representative').select2().trigger('change');
    $("#activity_voucherNo").val(activityDetails[0].activity_voucher_no);
    $("#activity_language").val(activityDetails[0].id_language);
    $('#activity_language').select2().trigger('change');
    $("#activity_pickupHotel").val(activityDetails[0].id_hotel);
    $('#activity_pickupHotel').select2().trigger('change');
    $("#activity_pickupTime").val(activityDetails[0].activity_pickup_time);
    $("#activity_pickupRoomNo").val(activityDetails[0].activity_client_room_no);
    $("#activity_serviceRemark").val(activityDetails[0].activity_remarks);
    $("#activity_internalRemark").val(activityDetails[0].activity_internal_remarks);
}