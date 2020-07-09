function allBookingAccom(id_booking) {
$('#tbl-bookingAccom').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bookingSystem/allBookingRoom.php?t=" + encodeURIComponent(global_token)  + "&id_booking=" + id_booking,
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
            "data" : "room_details"
        },{
            data: null,
                render: function ( data, type, row ) {
                    var start_date = data.room_stay_from;
                    var date_from = start_date.split("-");
                    var date_from_y = date_from[0];
                    var date_from_m = date_from[1];
                    var date_from_d = date_from[2];
                    var start_date = date_from_d+"/"+date_from_m+"/"+date_from_y;
                    var end_date = data.room_stay_to;
                    var date_to = end_date.split("-");
                    var date_to_y = date_to[0];
                    var date_to_m = date_to[1];
                    var date_to_d = date_to[2];
                    var end_date = date_to_d+"/"+date_to_m+"/"+date_to_y;
                    return start_date+' - '+end_date;
                },
                editField: ['room_stay_from', 'room_stay_to']
        },{
            "data" : "room_rebate_claim_type"
        },{
            "data" : "service_details"
        },{
            
            data: null,
                render: function ( data, type, row ) {
                    return data.room_total_claim_after_rebate+' '+data.currency_code;
                },
                editField: ['room_total_claim_after_rebate', 'currency_code']
        },
        // {
        //         "targets": -1,
        //         "data": null,                
        //         "class": 'btnCol',
        //         "defaultContent": 
        //         '<div class="btn-group">' +
        //         '<button type="button" id="btnEditAccom" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
        //         '<button type="button" id="btnDeleteAccom" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
        //     }
        ],
		"initComplete": function () {
            $('#tbl-bookingAccom tbody')
                .off()
                .on( 'click', '#btnEditAccom', function (e) {
                    var table = $('#tbl-bookingAccom').DataTable();
					var data = table.row( $(this).parents('tr') ).data();
					editAccom(data.id_booking_room);
                })
                .on( 'click', '#btnDeleteAccom', function (e) {
                    var table = $('#tbl-bookingAccom').DataTable();
					var accomData = table.row( $(this).parents('tr') ).data();
                    var deleteConfirm = confirm("Are you sure you want to delete the booking accom?");
                    if(deleteConfirm) {
                        accomData.action = 'DELETE';
                        deleteAccom(accomData);
                    }
                })
        }
    });
	
}

// EDIT ACCOM DATA
function editAccom(id_booking_room) {
    const url_search_bookingAccom = "php/api/bookingSystem/readBookingRoom.php?t=" + encodeURIComponent(global_token) + "&id_booking_room=" +id_booking_room;
    $.ajax({
        url: url_search_bookingAccom,
        method: "POST",
        dataType: "json",
        success: function (data) {
            if (data[0].OUTCOME == 'OK')
                {
                    displayAccom(data);
                    toastr.success('Accom found');
                }
            else
                {
                    toastr.warning('Accom not found');
                }
        },
        error: function (error) {
            console.log('Error ${error}');
            toastr.warning('Accom not found');
        }
    });
}

// DISPLAY DATA
function displayAccom(accomDetails) {
    accomDetails[0].action = 'RESET';
    newAccom(accomDetails[0]);
	$("#id_booking_room").val(accomDetails[0].id_booking_room);
    var bookingDate = accomDetails[0].accom_booking_date;
    var bookingDate = bookingDate.split("-").reverse();
    var accom_bookingDate = bookingDate[0]+"/"+bookingDate[1]+"/"+bookingDate[2];
    $("#accom_bookingDate").data('daterangepicker').setStartDate(accom_bookingDate);
	$("#accom_status").val(accomDetails[0].accom_status);
    $("#accom_status").select2().trigger('change');
    if (accomDetails[0].accom_status)
	$("#accom_paidBy").val(accomDetails[0].accom_service_paid_by);
    $("#accom_paidBy").select2().trigger('change');
	if (accomDetails[0].accom_service_paid_by == 'TO')
        {
            loadTourOperator(accomDetails[0].id_booking);
            $("#accom_payer").val(accomDetails[0].id_tour_operator);
            $("#accom_payer").select2().trigger('change');
        }
    else
        {
            loadBookingClient(accomDetails[0].id_booking);
            $("#accom_payer").val(accomDetails[0].id_client);
            $("#accom_payer").select2().trigger('change');
        }
    var accomDataSearch ={
        id_booking : accomDetails[0].id_booking,
        id_booking_room : accomDetails[0].id_booking_room
    }
    

    //.bookingAccomClient
    const url_search_bookingAccom = "php/api/bookingSystem/readBookingAccomClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_room=" +accomDetails[0].id_booking_room;
    $.ajax({
        url: url_search_bookingAccom,
        method: "POST",
        dataType: "json",
        success: function (data) {
            $.each(data, function (key, val) {
                    var selectedClient = val.id_client;
                    $('#accom_client').find('[value='+selectedClient+']').prop('selected', true);
                    $("#accom_client").selectpicker('refresh');
                });
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    //.bookingAccomClient
	$("#accom_rebateClaim").val(accomDetails[0].accom_rebate_claim_type);
    $('#accom_rebateClaim').select2().trigger('change');
}

function deleteAccom(accomData) {
    console.log('accomData', accomData);
}