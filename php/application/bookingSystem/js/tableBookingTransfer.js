function allBookingTransfer(id_booking) {
$('#tbl-bookingTransfer').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bookingSystem/allBookingTransfer.php?t=" + encodeURIComponent(global_token)  + "&id_booking=" + id_booking,
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
            "data" : "transfer_name"
        },{
            "data" : "transfer_date"
        },{
            "data" : "transfer_rebate_claim_type"
        },{
            
            data: null,
                render: function ( data, type, row ) {
                    return data.transfer_total_claim_after_rebate+' '+data.currency_code;
                },
                editField: ['transfer_total_claim_after_rebate', 'currency_code']
        },{
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnEditTransfer" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteTransfer" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ],
		"initComplete": function () {
            $('#tbl-bookingTransfer tbody')
                .off()
                .on( 'click', '#btnEditTransfer', function (e) {
                    var table = $('#tbl-bookingTransfer').DataTable();
					var data = table.row( $(this).parents('tr') ).data();
					editTransfer(data.id_booking_transfer_claim);
                })
                .on( 'click', '#btnDeleteTransfer', function (e) {
                	var table = $('#tbl-bookingTransfer').DataTable();
					var transferData = table.row( $(this).parents('tr') ).data();
                    if(transferData.planning_status == 1)
                    {
                        alert("Transfer already on planning and cannot be deleted");
                    }
                     else
                     {
                        var deleteConfirm = confirm("Are you sure you want to delete the booking transfer?");
                        if(deleteConfirm)
                        {
                            transferData.action = 'DELETE'; 
                            deleteTransfer(transferData);
                        }
                     }
                })
        }
    });
	
}

// EDIT TARNSFER DATA
function editTransfer(id_booking_transfer_claim) {
     const url_search_bookingTransfer = "php/api/bookingSystem/readBookingTransfer.php?t=" + encodeURIComponent(global_token) + "&id_booking_transfer_claim=" +id_booking_transfer_claim;
     $.ajax({
        url: url_search_bookingTransfer,
        method: "POST",
        dataType: "json",
        success: function (data) {
            if (data[0].OUTCOME == 'OK')
                {
                    displayTransfer(data);
                    toastr.success('Transfer found');
                }
            else
                {
                    toastr.warning('Transfer not found');
                }
        },
        error: function (error) {
            console.log('Error ${error}');
            toastr.warning('Transfer not found');
        }
    });
}

// DISPLAY TRANSFER DATA
function displayTransfer(transferDetails) {
    transferDetails[0].action = 'RESET';
    newTransfer(transferDetails[0]);
    console.log(transferDetails[0]);
	$("#id_booking_transfer_claim").val(transferDetails[0].id_booking_transfer_claim);
    var bookingDate = transferDetails[0].transfer_booking_date;
    var bookingDate = bookingDate.split("-").reverse();
    var transfer_bookingDate = bookingDate[0]+"/"+bookingDate[1]+"/"+bookingDate[2];
    $("#transfer_bookingDate").data('daterangepicker').setStartDate(transfer_bookingDate);
	$("#transfer_status").val(transferDetails[0].transfer_status);
    $("#transfer_status").select2().trigger('change');
    if (transferDetails[0].transfer_status)
	$("#transfer_paidBy").val(transferDetails[0].transfer_service_paid_by);
    $("#transfer_paidBy").select2().trigger('change');
	if (transferDetails[0].transfer_service_paid_by == 'TO')
        {
            loadTourOperator(transferDetails[0].id_booking);
            $("#transfer_payer").val(transferDetails[0].id_tour_operator);
            $("#transfer_payer").select2().trigger('change');
        }
    else
        {
            loadBookingClient(transferDetails[0].id_booking);
            $("#transfer_payer").val(transferDetails[0].id_client);
            $("#transfer_payer").select2().trigger('change');
        }
    if (transferDetails[0].transfer_type == 'ARRIVAL')
        {
            $("#transfer_type").val('ARR');
            $("#transfer_type").select2().trigger('change');
            $("#transfer_port").val(transferDetails[0].transfer_from_name);
            $("#transfer_port").select2().trigger('change');
            $("#transfer_destination_to").val(transferDetails[0].id_transfer_to);
            $("#transfer_destination_to").select2().trigger('change');
            $("#id_product_service_arr_claim").val(transferDetails[0].id_product_service_claim);
            $("#id_product_service_dep_claim").val();
        }
    else if (transferDetails[0].transfer_type == 'DEPARTURE')
        {
            $("#transfer_type").val('DEP');
            $("#transfer_type").select2().trigger('change');
            $("#transfer_port").val(transferDetails[0].transfer_to_name);
            $("#transfer_port").select2().trigger('change');
            $("#transfer_destination_from").val(transferDetails[0].id_transfer_from);
            $("#transfer_destination_from").select2().trigger('change');
            $("#id_product_service_arr_claim").val();
            $("#id_product_service_dep_claim").val(transferDetails[0].id_product_service_claim);
        }
    else if (transferDetails[0].transfer_type == 'INTER HOTEL')
        {
            $("#transfer_type").val('INTER HOTEL');
            $("#transfer_type").select2().trigger('change');
            $("#transfer_port").prop("disabled", true);
            $("#transfer_destination_from").val(transferDetails[0].id_transfer_from);
            $("#transfer_destination_from").select2().trigger('change');
            $("#transfer_destination_to").val(transferDetails[0].id_transfer_to);
            $("#transfer_destination_to").select2().trigger('change');
            $("#id_product_service_arr_claim").val();
            $("#id_product_service_dep_claim").val(transferDetails[0].id_product_service_claim);
        }
    else if (transferDetails[0].transfer_type == 'ACTIVITY')
        {
            $("#transfer_type").val('ACTIVITY');
            $("#transfer_type").select2().trigger('change');
            $("#transfer_port").prop("disabled", false);
            $("#transfer_port").val(transferDetails[0].transfer_special_name);
            $("#transfer_port").select2().trigger('change');
            $("#transfer_destination_from").val(transferDetails[0].id_transfer_from);
            $("#transfer_destination_from").select2().trigger('change');
            $("#transfer_destination_to").val(transferDetails[0].id_transfer_to);
            $("#transfer_destination_to").select2().trigger('change');
            $("#id_product_service_arr_claim").val();
            $("#id_product_service_dep_claim").val(transferDetails[0].id_product_service_claim);
        }
    var transferDataSearch ={
        id_booking : transferDetails[0].id_booking,
        transfer_type : transferDetails[0].transfer_type,
        transfer_port :  $("#transfer_port").val()
    }
    const url_product = "php/api/bookingSystem/transferList.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_product,
        method: "POST",
        dataType: "json",
        data:transferDataSearch ,
        success: function (data) 
        {
            $("#transfer_vehicle").empty();
            var transferFound = false;
            $("#transfer_vehicle").append('<option value="0">Select</option>');
            $.each(data, function (key, val) {
                if (transferDetails[0].id_product == val.id_product)
                    {
                        transferFound = true;
                        $("#transfer_vehicle").append('<option value="' + val.id_product + '" selected>'+val.product_name+ '</option>');
                    }
                else
                    {
                        $("#transfer_vehicle").append('<option value="' + val.id_product + '">'+val.product_name+ '</option>');
                        
                    }
                if (transferFound == false)
                    {
                        console.log(transferDetails[0].id_product +" /"+ val.id_product);
                        // LLS - TO DO : query specific id_product
                        $("#transfer_vehicle").append('<option value="' + val.id_product + '" selected disabled>'+val.product_name+ '</option>');
                    }
            }); 
            console.log(transferDataSearch);

        },
        error: function (error) 
        {
            console.log('Error ${error}');
        }
    });
    // .Transfer Product
    
    $("#transfer_vehicle").select2().trigger('change');
    var pickupDate = transferDetails[0].transfer_date;
    var pickupDate = pickupDate.split("-").reverse();
    var transfer_pickupDate = pickupDate[0]+"/"+pickupDate[1]+"/"+pickupDate[2];
    $("#transfer_pickupDate").data('daterangepicker').setStartDate(transfer_pickupDate);
	$("#transfer_pickupFlight").val(transferDetails[0].transfer_flight_no);
	$("#transfer_pickupTime").val(transferDetails[0].transfer_time);
    
    //.bookingTransferClient
    const url_search_bookingTransfer = "php/api/bookingSystem/readBookingTransferClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_transfer_claim=" +transferDetails[0].id_booking_transfer_claim;
     $.ajax({
        url: url_search_bookingTransfer,
        method: "POST",
        dataType: "json",
        success: function (data) {
            $.each(data, function (key, val) {
                    var selectedClient = val.id_client;
                console.log(selectedClient);
                    $('#transfer_client').find('[value='+selectedClient+']').prop('selected', true);
                    $("#transfer_client").selectpicker('refresh');
                });
    $("#transfer_adultAmt").val(transferDetails[0].transfer_adult_amt);
	$("#transfer_childAmt").val(transferDetails[0].transfer_child_amt);
	$("#transfer_infantAmt").val(transferDetails[0].transfer_infant_amt);
            
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    //.bookingTransferClient
    console.log(transferDetails[0].transfer_adult_amt);
	$("#transfer_rebateClaim").val(transferDetails[0].transfer_rebate_claim_type);
    $('#transfer_rebateClaim').select2().trigger('change');
    if (transferDetails[0].transfer_rebate_claim_type == 'None')
        {
            $("#claimRebateSection").hide();
            $("#transfer_rebateClaimApproveBy").val('');
            $('#transfer_rebateClaimApproveBy').select2().trigger('change');
            $("#transfer_rebateClaimApproveBy").prop("disabled", true);
            $("#transfer_claimPercentageRebate").val('');
            $("#transfer_adultClaimRebate").val('');
            $("#transfer_childClaimRebate").val('');
            $("#transfer_InfantClaimRebate").val('');
        }   
    else if (transferDetails[0].transfer_rebate_claim_type == 'Percentage')
        {
            $("#claimRebateSection").show();
            $("#transfer_rebateClaimApproveBy").prop("disabled", false);
            $("#transfer_rebateClaimApproveBy").val(transferDetails[0].transfer_rebate_claim_approve_by);
            $('#transfer_rebateClaimApproveBy').select2().trigger('change');
            $("#transfer_claimPercentageRebate").show();
            $("#transfer_claimPercentageRebate").val(transferDetails[0].transfer_rebate_claim_percentage);
            $("#rebate_fix").hide();
            $("#transfer_adultClaimRebate").val('');
            $("#transfer_childClaimRebate").val('');
            $("#transfer_InfantClaimRebate").val('');
        }
    else if (transferDetails[0].transfer_rebate_claim_type == 'Fixed Tariff')
        {
            $("#claimRebateSection").show();
            $("#rebate_fix").show();
            $("#transfer_rebateClaimApproveBy").prop("disabled", false);
            $("#transfer_rebateClaimApproveBy").val(transferDetails[0].transfer_rebate_claim_approve_by);
            $('#transfer_rebateClaimApproveBy').select2().trigger('change');
            $("#transfer_adultClaimRebate").val(transferDetails[0].transfer_adult_claim_rebate);
            $("#transfer_childClaimRebate").val(transferDetails[0].transfer_child_claim_rebate);
            $("#transfer_InfantClaimRebate").val(transferDetails[0].transfer_infant_claim_rebate);
            $("#transfer_claimPercentageRebate").hide();
            $("#transfer_claimPercentageRebate").val('');
        }
    else if (transferDetails[0].transfer_rebate_claim_type == 'FOC')
        {
            $("#transfer_claimPercentageRebate").hide();
            $("#claimRebateSection").hide();
            $("#transfer_rebateClaimApproveBy").prop("disabled", false);
            $("#transfer_rebateClaimApproveBy").val(transferDetails[0].transfer_rebate_claim_approve_by);
            $('#transfer_rebateClaimApproveBy').select2().trigger('change');
            $("#transfer_claimPercentageRebate").val('');
            $("#transfer_adultClaimRebate").val('');
            $("#transfer_childClaimRebate").val('');
            $("#transfer_InfantClaimRebate").val('');
        }
    $("#transfer_serviceRemark").val(transferDetails[0].transfer_remarks);
    $("#transfer_internalRemark").val(transferDetails[0].transfer_internal_remarks);
    if(transferDetails[0].planning_status == 1)
        {
            $("#btn-deleteTransfer").prop("disabled",true);
            $("#btn-saveTransfer").prop("disabled",true);
        }
    else
        {
            $("#btn-saveTransfer").prop("disabled", false);
            $("#btn-deleteTransfer").prop("disabled", false);
        }
}