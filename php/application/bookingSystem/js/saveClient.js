// JavaScript Document
/*Date : 2019, 17 October
Application : Client - Booking System
Developer : slouis@solis360.com*/

$(function(){
	// Save Booking
	$('#btn-saveClient').click(
	function()
	{
		if($('#id_client').val() == '' && $('#id_booking_client').val() != '')
			{
				alert("An error occured, please load back the Client form");
			}
		else if($('#id_client').val() != '' && $('#id_booking_client').val() == '')
			{
				alert("An error occured, please load back the Client form");
			}
		else if($('#id_client').val() != '' && $('#id_booking_client').val() != '')
			{
				var id_booking_client = $('#id_booking_client').val();
				const url_search_booking = "php/api/bookingSystem/readBookingClient.php?t=" + encodeURIComponent(global_token) + "&id_booking_client=" +id_booking_client;
				$.ajax({
					url: url_search_booking,
					method: "POST",
					dataType: "json",
					success: function (data) {
				    var paxType = data[0].type;
					paxValidation(paxType);
				},
				error: function (error) {
					console.log('Error ${error}');
				}
				});
				
			}
		else 
			{
				var paxType = "New";
				paxValidation(paxType);
			}
	});
	// .Save Client
});

function paxValidation(paxType){
	var paxCountValid = false;
		
	var id_booking = $('#id_booking').val();
	var objReservationClient = {id_booking:id_booking}
	const url_record_client = "php/api/bookingSystem/countBookingClient.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
	$.ajax({
		url : url_record_client,
		method : "POST",
		data : objReservationClient, 
		dataType: "json",                                                                             
		success : function(data){
			var client_adult = data.client_adult;
			var client_teen = data.client_teen;
			var client_child = data.client_child;
			var client_infant  = data.client_infant;
			if (paxType == "ADULT")
				{
					var client_adult = data.client_adult-1;
				}
			else if (paxType == "TEEN")
				{
					var client_teen = data.client_teen -1;
				}
			else if (paxType == "CHILD")
				{
					var client_child = data.client_child -1;
				}
			else if (paxType == "INFANT")
				{
					var client_infant = data.client_infant -1;
				}
			
			if ($('#client_type').val() == "ADULT")
				{
					if (data.adult_amt > parseInt(client_adult))
						{
							var paxCountValid = true;
							formValidation();
						}
					else
						{
							var paxCountValid = false;
							alert('Amount of Adult reached for the dossier');
							newClient();
						}
				}
			if ($('#client_type').val() == "TEEN")
				{
					if (data.teen_amt > parseInt(client_teen))
						{
							var paxCountValid = true;
							formValidation();
						}
					else
						{
							var paxCountValid = false;
							alert('Amount of Teen reached for the dossier');
							newClient();
						}
				}
			if ($('#client_type').val() == "CHILD")
				{
					if (data.child_amt > parseInt(client_child))
						{
							var paxCountValid = true;
							formValidation();
						}
					else
						{
							var paxCountValid = false;
							alert('Amount of Child reached for the dossier');
							newClient();
						}
				}
			if ($('#client_type').val() == "INFANT")
				{
					if (data.infant_amt > parseInt(client_infant))
						{
							var paxCountValid = true;
							formValidation();
						}
					else
						{
							var paxCountValid = false;
							alert('Amount of Infant reached for the dossier');
							newClient();
						}
				}
		},
		error: function(error) {
			console.log('Error ${error}');
		}
	});
}

function formValidation(){
	var formInputValid = false;
	if($('#client_type').val() == "" || $('#booking_toName').val() == 0)
	{
		alert("Select Client Type");
		formInputValid = true;
	}
	else if ($('#bookingClient').val() == "" || $('#bookingClient').val() == 0)
	{
		alert("Select Client Title");
		formInputValid = true;
	}
	else if($('#client_surname').val() == "" || $('#client_surname').val() == 0)
	{
		alert("Input Client Surname");
		formInputValid = true;
	}
	else if($('#client_forename').val() == "" || $('#client_forename').val() == 0)
	{
		alert("Input Client Forename");
		formInputValid = true;
	}
	else if($('#client_years').val() != "" || $('#client_yearMonth').val() == 0)
	{
		alert("Choose between Month/Years");
		formInputValid = true;
	}

	if(!formInputValid)
	{
		if($('#id_client').val() == '' && $('#id_booking_client').val() == '')
		{
			// save Client
			saveClient();
		}
		else
		{
			if($('#id_client').val() == '' && $('#id_booking_client').val() != '')
				{
					alert("An error occured, please load back the Client form");
				}
			else if($('#id_client').val() != '' && $('#id_booking_client').val() == '')
				{
					alert("An error occured, please load back the Client form");
				}
			else
				{
                    var updateConfirm = confirm("Are you sure you want to update the client?");
                    if(updateConfirm)
                    {
                        // update Client
                        updateClient();
                    }
				}
		}
	}

}
function saveClient() { 
    var id_booking = $('#id_booking').val();
    var client_type = $('#client_type').val();
	if ($("#client_vip").prop('checked'))
	{
         var client_vip = 1;
    } 
	else  
	{
         var client_vip = 0;
	}
    var client_title = $('#client_title').val();
    var client_surname = $('#client_surname').val().toUpperCase();
    var client_forename = $('#client_forename').val().toUpperCase();
	if ($("#client_dob").val() == "")
		{
			var client_dob = "";
		}
	else
		{
			var client_dob = $("#client_dob").val();
			var dob = client_dob.split("/").reverse();
			var client_dob = dob[0]+"-"+dob[1]+"-"+dob[2];
		}
    var client_years = $('#client_years').val();
    var client_yearMonth = $('#client_yearMonth').val();
    var client_passport = $('#client_passport').val();
    var client_remarks = $('#client_remarks').val();
    
    var objReservationClient = {
        id_booking: id_booking,
        client_type: client_type,
        client_vip: client_vip,
        client_title: client_title,
        client_surname: client_surname,
        client_forename: client_forename,
        client_dob: client_dob,
        client_years: client_years,
        client_yearMonth: client_yearMonth,
        client_passport: client_passport,
        client_remarks: client_remarks
    }
    const url_save_client = "php/api/bookingSystem/saveClient.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_client,
        method : "POST",
        data : objReservationClient, 
        dataType: "json",                                                                             
        success : function(data){
            if (data.OUTCOME == 'OK')
                {
                    $('#id_client').val(data.id_client);
                    $('#id_booking_client').val(data.id_booking_client);
                    allBookingClient(data.id_booking)
                    toastr.success('New Client saved successfully');
                    newClient();
                }
            else
                {
                     toastr.warning('An error occured. Please do it again');
                }
        },
        error: function(error) {
            toastr.warning('An error occured. Please do it again');
            console.log('Error ${error}');
        }
    });
}


function updateClient() { 
    var id_booking = $('#id_booking').val();
    var id_client = $('#id_client').val();
    var id_booking_client = $('#id_booking_client').val();
    var client_type = $('#client_type').val();
	if ($("#client_vip").prop('checked'))
	{
         var client_vip = 1;
    } 
	else  
	{
         var client_vip = 0;
	}
    var client_title = $('#client_title').val();
    var client_surname = $('#client_surname').val().toUpperCase();
    var client_forename = $('#client_forename').val().toUpperCase();
	if ($("#client_dob").val() == "")
		{
			var client_dob = "";
		}
	else
		{
			var client_dob = $("#client_dob").val();
			var dob = client_dob.split("/").reverse();
			var client_dob = dob[0]+"-"+dob[1]+"-"+dob[2];
		}
    var client_years = $('#client_years').val();
	if ($("#client_years").val() == "")
		{
			var client_yearMonth = '';
		}
	else
		{
			var client_yearMonth = $('#client_yearMonth').val();
		}
    var client_passport = $('#client_passport').val();
    var client_remarks = $('#client_remarks').val();
    
    var objReservationClient = {
        id_booking: id_booking,
        id_client: id_client,
        id_booking_client: id_booking_client,
        client_type: client_type,
        client_vip: client_vip,
        client_title: client_title,
        client_surname: client_surname,
        client_forename: client_forename,
        client_dob: client_dob,
        client_years: client_years,
        client_yearMonth: client_yearMonth,
        client_passport: client_passport,
        client_remarks: client_remarks
    }
    const url_update_client = "php/api/bookingSystem/updateClient.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_update_client,
        method : "POST",
        data : objReservationClient, 
        dataType: "json",                                                                             
        success : function(data){
            if (data.OUTCOME == 'OK') 
                {
                    $('#id_client').val(data.id_client);
                    $('#id_booking_client').val(data.id_booking_client);
                    allBookingClient(data.id_booking)
                    toastr.info('Client updated successfully');
                }
            else
                {
                    toastr.warning('An error occured. Please do it again');
                }
        },
        error: function(error) {
            toastr.warning('An error occured. Please do it again');
            console.log('Error ${error}');
        }
    });
}

