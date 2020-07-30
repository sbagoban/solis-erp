$(document).ready(function() {
       var allParams = window.location.href.split('data=').pop();
	   const urlParams = new URLSearchParams(allParams);
//     id_product_service_cost = urlParams.get("id_product_service_cost"); 
//     id_product_service = urlParams.get("id_product_service"); 
//     id_product_service_claim = document.getElementById("id_product_service_claim").innerHTML;
	   charge = urlParams.get("charge"); 


//     // Check from - two textfield
//      $('#pax_to').on('change', function() {

// 		var pax_from = $('#pax_from').val();
//         var pax_to_val = $(this).val();

//         if (pax_from > pax_to_val) { 
//         	alert('"Pax from" should be greater or equal to "pax to"');
//         	// disable button
//         	$("#enableCounter").css("display", "none");
// 			$("#disableCounter").css("display", "block");
//         } else { 
//         	// Activate button
//         	console.log('Good value');
//         	$("#enableCounter").css("display", "block");
// 			$("#disableCounter").css("display", "none");
//         }
//     });
});

$("#modal-paxBreakServicesCost").on("hidden.bs.modal", function(){
	$('#serviceLineId').text('');
	$('#pax_from').val('');
	$('#pax_to').val('');
	$('#ps_adult_cost_modal').val('');
	$('#ps_teen_cost_modal').val('');
	$('#ps_child_cost_modal').val('');
	$('#ps_infant_cost_modal').val('');
});

// Save Data
function multiplePriceServiceCost(data) {
	console.log('data', data);
	// Active Line Data
	$('#modal-paxBreakServicesCost').modal('show');
	$('#serviceLineId').text(data.id_product_service_cost);

	// Action Save
	$('#btnCounter_pax').click(function (event) { 
		// check wheter on same service line claim before adding - why this 
		//  a bit weird reaction while adding form in bootstrap modal
		serviceLineId = $("#serviceLineId").text();
		if (data.id_product_service_cost == serviceLineId) {
			var charge_pax_break = $('#charge_pax_break').val();
			var pax_from = $('#pax_from').val();
			var pax_to = $('#pax_to').val();

			if (charge == 'UNIT') { 
				ps_adult_cost_break = $('#ps_adult_cost_modal').val();
				ps_teen_cost_break = 0;
				ps_child_cost_break = 0;
				ps_infant_cost_break = 0;
			} else {
				ps_adult_cost_break = $('#ps_adult_cost_modal').val();
				ps_teen_cost_break = $('#ps_teen_cost_modal').val();
				ps_child_cost_break = $('#ps_child_cost_modal').val();
				ps_infant_cost_break = $('#ps_infant_cost_modal').val();
			}

		 	const url_save_productservice_cost_pax_break = "php/api/backofficeproduct/saveproductservicecostpaxbreaks.php?t=" + encodeURIComponent(global_token);
			var objPaxBreak = {
					id_product_service_pax_break_cost: -1,
					id_product_service_cost: data.id_product_service_cost,
					id_product_service: id_product_service, 
		            charge: charge,
		            pax_from: pax_from,
		            pax_to: pax_to,
		            ps_adult_cost_break: ps_adult_cost_break,
		            ps_teen_cost_break: ps_teen_cost_break,
		            ps_child_cost_break: ps_child_cost_break,
		            ps_infant_cost_break: ps_infant_cost_break
			}

			$.ajax({
		        url : url_save_productservice_cost_pax_break,
		        method : "POST",
		        data : objPaxBreak, 
		        cache: false,        
		        dataType: 'JSON',                                                                                                                                                                                                                                                                                                                                                                                                            
		        success : function(data) {
		        	console.log('duplicate_entry', data);
		        	if (data.OUTCOME == 'ERROR: duplicate_entry') {
		            	alert('Duplicate entry, Please check Pax From And Pax To');
		        		allServicesPaxBreakGridCost(objPaxBreak);	
		        	} else {
		        		allServicesPaxBreakGridCost(data);
		            	resetFormPaxbreaks();
		        	}
		        },
		        error: function(error) {	
		        }
		    });
		}
	});
}

// Pax Break datatable
function allServicesPaxBreakGridCost(data) { 
	var chkmultipleprice = document.getElementById("multiple_price");
    $('#tbl-productServicesCostPaxBreaks').DataTable({
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backofficeproduct/gridpaxbreakscostlist.php?t=" + encodeURIComponent(global_token) + "&id_product_service_cost=" + data.id_product_service_cost,
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
            "data" : "id_product_service_pax_break_cost"
        }, {
            "data" : "charge"
        }, {
            "data" : "pax_from"
        }, {
            "data" : "pax_to"
        }, { 
            "data" : "ps_adult_cost_break"
        }, { 
            "data" : "ps_teen_cost_break"
        }, { 
            "data" : "ps_child_cost_break"
        }, { 
            "data" : "ps_infant_cost_break"
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
            $('#tbl-productServicesCostPaxBreaks tbody')
                .off()
                .on( 'click', '#btnPaxBreak', function (e) {
                    var table = $('#tbl-productServicesCostPaxBreaks').DataTable();
                    var data = table.row( $(this).parents('tr')).data();
                    deletePaxBreakCost(data);
                })
        }
    }); 
}

function deletePaxBreakCost(lineParam) {
	console.log(lineParam, 'lineParam');
	var objDelPaxbreakCost = {id_product_service_pax_break_cost: lineParam.id_product_service_pax_break_cost};
    const url_delete_paxbreakCost = "php/api/backofficeproduct/deletepaxbreakcost.php?t=" + encodeURIComponent(global_token) + "&id_product_service_pax_break_cost=" + lineParam.id_product_service_pax_break_cost;
    $.ajax({
        url: url_delete_paxbreakCost,
        method: "POST",
        data: objDelPaxbreakCost,
        dataType: "json",
        success: function (data) {
           	console.log(data);
            allServicesPaxBreakGridCost(lineParam);
        },
        error: function (error) {
            console.log("error", error);
        }
    });
}

function resetFormPaxbreaks() {
	$('#pax_from').val('');
	$('#pax_to').val('');
	$('#ps_adult_cost_modal').val('');
	$('#ps_teen_cost_modal').val('');
	$('#ps_child_cost_modal').val('');
	$('#ps_infant_cost_modal').val('');
}
