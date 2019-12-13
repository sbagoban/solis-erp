function allBookingClient(id_booking) {
    // Request call everything from database
    $('#tbl-bookingClient').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bookingSystem/allClient.php?t=" + encodeURIComponent(global_token)  + "&id_booking=" + id_booking,
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
            "data" : "type"
        }, {
            data: null,
                render: function ( data, type, row ) {
                    return data.title+' '+data.surname+' '+data.other_name;
                },
                editField: ['title', 'surname', 'other_name']
        },{
            data: null,
                render: function ( data, type, row ) {
                    return data.age+' '+data.yearMonth;
                },
                editField: ['age', 'yearMonth']
        }, 
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<button type="button" id="btnEditClient" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i>' +
                '<button type="button" id="btnDeleteClient" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ],
		"initComplete": function () {
            $('#tbl-bookingClient tbody')
                .off()
                .on( 'click', '#btnEditClient', function (e) {
                    var table = $('#tbl-bookingClient').DataTable();
					var data = table.row( $(this).parents('tr') ).data();
                    $('#btn-deleteClient').prop('disabled', false);
					editClient(data.id_booking_client);
                })
                .on( 'click', '#btnDeleteClient', function (e) {
                	var table = $('#tbl-bookingClient').DataTable();
					var data = table.row( $(this).parents('tr') ).data();
                    var deleteConfirm = confirm("Are you sure you want to delete the client?");
                    if(deleteConfirm)
                    {
					   deleteClient(data);
                    }
                })
        }
    });
	
}

// EDIT CLIENT DATA
function editClient(id_booking_client) {
	console.log('tes', id_booking_client);
    const url_search_booking = "php/api/bookingSystem/readBookingClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_client=" +id_booking_client;
    $.ajax({
        url: url_search_booking,
        method: "POST",
        dataType: "json",
        success: function (data) {
			console.log(data, 'ht')
			displayClient(data);
            toastr.success('Client found');
        },
        error: function (error) {
            console.log('Error ${error}');
            toastr.warning('Client not found');
        }
    });
}

// DISPLAY CLIENT DATA
function displayClient(clientData) {
    newClient();
	var client_type = clientData[0].type;
	$("#client_type").val(client_type);
    $('#client_type').select2().trigger('change');
	var client_vip = clientData[0].is_vip;
	if (client_vip == 0)
		{
			$('#client_vip').prop('checked', false);
		}
	else
		{
			$('#client_vip').prop('checked', true);
		}
	$("#id_client").val(clientData[0].id_client);
	$("#id_booking_client").val(clientData[0].id_booking_client);
	var client_title = clientData[0].title;
	$("#client_title").val(client_title);
    $('#client_title').select2().trigger('change');
	$('#client_surname').val(clientData[0].surname);
	$('#client_forename').val(clientData[0].other_name);
	var client_dob = clientData[0].client_dob
	if ( client_dob == "" ||client_dob == null )
		{
			$('#client_dob').val();
		}
	else
		{
			var client_dob = clientData[0].client_dob;
			var dob = client_dob.split("-").reverse();
			var client_dob = dob[0]+"/"+dob[1]+"/"+dob[2];
			$('#client_dob').val(client_dob);
		}
	$('#client_years').val(clientData[0].age);
	$('#client_yearMonth').val(clientData[0].yearMonth);
    $('#client_yearMonth').select2().trigger('change');
	$('#client_passport').val(clientData[0].passport_no);
	$('#client_remarks').val(clientData[0].remarks);
	$("#id_client").attr("readonly", true); 
	$("#id_booking_client").attr("readonly", true); 
    $('#btn-saveClient').prop('disabled', false);
    $('#btn-newClient').prop('disabled', false);
    $('#btn-deleteClient').prop('disabled', false);
}