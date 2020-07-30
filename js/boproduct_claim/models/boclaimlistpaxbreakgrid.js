$(document).ready(function() {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    id_product_service_cost = urlParams.get("id_product_service_cost"); 
    id_product_service = urlParams.get("id_product_service"); 
    id_product_service_claim = document.getElementById("id_product_service_claim").innerHTML;
    charge = urlParams.get("charge"); 

    // check whether cost is multiple price or not
    multiple_price_cost = urlParams.get("multiple_price_cost");
    if (multiple_price_cost == 1) { 
    	$("#multiple_price").prop("checked", true);
    	$("#multiple_price").prop('disabled',true);           
        document.getElementById("ps_adult_claim").disabled = true;
        document.getElementById("ps_teen_claim").disabled = true;
        document.getElementById("ps_child_claim").disabled = true;
        document.getElementById("ps_infant_claim").disabled = true;

        var flagPaxBreak = pad('1', 3);
        $("#ps_adult_claim").val(flagPaxBreak); 
        $("#ps_teen_claim").val(flagPaxBreak); 
        $("#ps_child_claim").val(flagPaxBreak); 
        $("#ps_infant_claim").val(flagPaxBreak);
    } else {
    	$("#multiple_price"). prop("checked", false);    	
    }


    // Check from - two textfield
     $('#pax_to').on('change', function() {

		var pax_from = $('#pax_from').val();
        var pax_to_val = $(this).val();

        if (pax_from > pax_to_val) { 
        	alert('"Pax from" should be greater or equal to "pax to"');
        	// disable button
        	$("#enableCounter").css("display", "none");
			$("#disableCounter").css("display", "block");
        } else { 
        	// Activate button
        	console.log('Good value');
        	$("#enableCounter").css("display", "block");
			$("#disableCounter").css("display", "none");
        }
    });
});

// Use flag 001 to the paxbreak single line values 
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

$("#modal-paxBreakServicesClaim").on("hidden.bs.modal", function(){
	$('#serviceLineId').text('');
	$('#pax_from').val('');
	$('#pax_to').val('');
	$('#ps_adult_claim_modal').val('');
	$('#ps_teen_claim_modal').val('');
	$('#ps_child_claim_modal').val('');
	$('#ps_infant_claim_modal').val('');
});

// Save Data
function multiplePriceServiceClaim(data) {
	// Active Line Data
	$('#modal-paxBreakServicesClaim').modal('show');
	$('#serviceLineId').text(data.id_product_service_claim);

	// Action Save
	$('#btnCounter_pax').click(function (event) { 

		// check wheter on same service line claim before adding - why this 
		//  a bit weird reaction while adding form in bootstrap modal
		serviceLineId = $("#serviceLineId").text();
		if (data.id_product_service_claim == serviceLineId) {
			var charge_pax_break = $('#charge_pax_break').val();
			var pax_from = $('#pax_from').val();
			var pax_to = $('#pax_to').val();

			if (charge == 'UNIT') { 
				ps_adult_claim_break = $('#ps_adult_claim_modal').val();
				ps_teen_claim_break = 0;
				ps_child_claim_break = 0;
				ps_infant_claim_break = 0;
			} else {
				ps_adult_claim_break = $('#ps_adult_claim_modal').val();
				ps_teen_claim_break = $('#ps_teen_claim_modal').val();
				ps_child_claim_break = $('#ps_child_claim_modal').val();
				ps_infant_claim_break = $('#ps_infant_claim_modal').val();
			}

			// Check RollOver for pax breaks
		    if (data.rollover_type == 'Percentage') {

		        if (ps_adult_claim_break == "" || ps_adult_claim_break == "0") {
		            ps_adult_claim_rollover = 0;
		        } else { 
		            ps_adult_claim_per = (parseInt(data.rollover_value) / 100) * parseInt(ps_adult_claim_break);
		            ps_adult_claim_rollover = ps_adult_claim_per + parseInt(ps_adult_claim_break);
		        }

		        if (ps_teen_claim_break == "" || ps_teen_claim_break == "0") {
		            ps_teen_claim_rollover = 0;
		        } else {
		            ps_teen_claim_per = (parseInt(data.rollover_value) / 100) * parseInt(ps_teen_claim_break);
		            ps_teen_claim_rollover = ps_teen_claim_per + parseInt(ps_teen_claim_break);
		        }

		        if (ps_child_claim_break == "" || ps_child_claim_break == "0") {
		            ps_child_claim_rollover = 0;
		        } else {            
		            ps_child_claim_per = (parseInt(data.rollover_value) / 100) * parseInt(ps_child_claim_break);
		            ps_child_claim_rollover = ps_child_claim_per + parseInt(ps_child_claim_break);
		        }

		        if (ps_infant_claim_break == "" || ps_infant_claim_break == "0") {
		            ps_infant_claim_rollover = 0;
		        } else {
		            ps_infant_claim_per = (parseInt(data.rollover_value) / 100) * parseInt(ps_infant_claim_break);
		            ps_infant_claim_rollover = ps_infant_claim_per + parseInt(ps_infant_claim_break);
		        }

		    } else if (data.rollover_type == 'Fix Amount') {
		        //check if zero
		        if (ps_adult_claim_break == "" || ps_adult_claim_break == "0") {
		            ps_adult_claim_rollover = 0;
		        } else {
		            ps_adult_claim_rollover = parseInt(ps_adult_claim_break) + parseInt(data.rollover_value);
		        }

		        if (ps_teen_claim_break == "" || ps_teen_claim_break == "0") {
		            ps_teen_claim_rollover = 0;
		        } else {
		            ps_teen_claim_rollover = parseInt(ps_teen_claim_break) + parseInt(data.rollover_value);
		        }

		        if (ps_child_claim_break == "" || ps_child_claim_break == "0") {
		            ps_child_claim_rollover = 0;
		        } else {
		            ps_child_claim_rollover = parseInt(ps_child_claim_break) + parseInt(data.rollover_value);
		        }

		        if (ps_infant_claim_break == "" || ps_infant_claim_break == "0") {
		            ps_infant_claim_rollover = 0;
		        } else {
		            ps_infant_claim_rollover = parseInt(ps_infant_claim_break) + parseInt(data.rollover_value); 
		        }

		    } else { 
		        ps_adult_claim_rollover = 0;
		        ps_teen_claim_rollover = 0;
		        ps_child_claim_rollover = 0;
		        ps_infant_claim_rollover = 0;
		    }

		 	const url_save_productservice_claim_pax_break = "php/api/backofficeserviceclaim/saveproductserviceclaimpaxbreaks.php?t=" + encodeURIComponent(global_token);
			var objPaxBreak = {
					id_product_service_pax_break_claim: -1,
					id_product_service_claim: data.id_product_service_claim,
					id_product_service_cost: id_product_service_cost,
					id_product_service: id_product_service, 
		            charge: charge,
		            pax_from: pax_from,
		            pax_to: pax_to,
		            ps_adult_claim_break: ps_adult_claim_break,
		            ps_teen_claim_break: ps_teen_claim_break,
		            ps_child_claim_break: ps_child_claim_break,
		            ps_infant_claim_break: ps_infant_claim_break,
		            ps_infant_claim_rollover: ps_infant_claim_rollover,
		            ps_child_claim_rollover: ps_child_claim_rollover,
		            ps_teen_claim_rollover: ps_teen_claim_rollover,
		            ps_adult_claim_rollover: ps_adult_claim_rollover,
		            rollover_value: data.rollover_value,
		            rollover_type: data.rollover_type
			}
			console.log('objPaxBreak', objPaxBreak);
			$.ajax({
		        url : url_save_productservice_claim_pax_break,
		        method : "POST",
		        data : objPaxBreak, 
		        cache: false,        
		        dataType: 'JSON',                                                                                                                                                                                                                                                                                                                                                                                                            
		        success : function(data) {
		        	console.log('duplicate_entry', data);
		        	if (data.OUTCOME == 'ERROR: duplicate_entry') {
		        		alert('Duplicate entry, Please check Pax From And Pax To');
		        		allServicesPaxBreakGridClaim(objPaxBreak);
		        	} else {
		        		allServicesPaxBreakGridClaim(data);
		            	resetFormPaxbreaks(); 
		        	}  
		        },
		        error: function(error) {
		            console.log('Error', error);
		        }
		    });
		}
	});
}

// Pax Break datatable
function allServicesPaxBreakGridClaim(data) { 
	var chkmultipleprice = document.getElementById("multiple_price");
    $('#tbl-productServicesClaimPaxBreaks').DataTable({
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeserviceclaim/gridpaxbreaksclaimlist.php?t=" + encodeURIComponent(global_token) + "&id_product_service_claim=" + data.id_product_service_claim,
            "dataSrc" : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 5,
        "aaSorting": [ [0,'desc'] ],
        "dom": "<'row'<'form-inline' <'col-sm-2'B>>>"
        +"<'row'<'col-sm-2 col-md-2'l><'col-sm-2 col-md-10'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-2 col-md-2'i><'col-sm-10 col-md-10'p>>",
        "columnDefs": [
            { width: 200, targets: -1 }
        ],
        "buttons":[
            {
            "extend":    "csvHtml5",
            "text":      "<i class='fa fa-file-text-o'> Excel</i>",
            "titleAttr": "Download in Excel Format",
            }
        ],
        "columnDefs": [
        ],
        "columns" : [ 
        {
            "data" : "id_product_service_pax_break_claim"
        }, {
            "data" : "charge"
        }, {
            "data" : "pax_from"
        }, {
            "data" : "pax_to"
        }, { 
            "data" : "ps_adult_claim_break"
        }, { 
            "data" : "ps_teen_claim_break"
        }, { 
            "data" : "ps_child_claim_break"
        }, { 
            "data" : "ps_infant_claim_break"
        },  
            {
                "targets": -1,
                "data": null,                
                "class": 'btnCol',
                "defaultContent": 
                '<div class="btn-group">' +
                '<i class="fa fa-fw fa-trash-o" id="btnPaxBreak"></i>'
            }
        ],

        "initComplete": function () {
            $('#tbl-productServicesClaimPaxBreaks tbody')
                .off()
                .on( 'click', '#btnPaxBreak', function (e) {
                    var table = $('#tbl-productServicesClaimPaxBreaks').DataTable();
                    var data = table.row( $(this).parents('tr')).data();
                    deletePaxBreak(data);
                })
        }
    }); 
}

function deletePaxBreak(lineParam) {
	var objDelPaxbreak = {id_product_service_pax_break_claim: lineParam.id_product_service_pax_break_claim};
    const url_delete_paxbreak = "php/api/backofficeserviceclaim/deletepaxbreak.php?t=" + encodeURIComponent(global_token) + "&id_product_service_pax_break_claim=" + lineParam.id_product_service_pax_break_claim;
    $.ajax({
        url: url_delete_paxbreak,
        method: "POST",
        data: objDelPaxbreak,
        dataType: "json",
        success: function (data) {
           	console.log(data);
            allServicesPaxBreakGridClaim(lineParam);
        },
        error: function (error) {
            console.log("error", error);
        }
    });
}

function resetFormPaxbreaks() {
	$('#pax_from').val('');
	$('#pax_to').val('');
	$('#ps_adult_claim_modal').val('');
	$('#ps_teen_claim_modal').val('');
	$('#ps_child_claim_modal').val('');
	$('#ps_infant_claim_modal').val('');
}
