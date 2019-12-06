function loadActivityExtra(id_product_service_claim){
    // Request call everything from database
    $('#tbl-activityExtra').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bookingSystem/allActivityExtra.php?t=" + encodeURIComponent(global_token)  + "&id_product_service_claim=" + id_product_service_claim,
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
        "columns" : [ 
            {
                data: null,
                render: function ( data, type, row ) {
                    return data.extra_name+' - '+data.extra_description;
                },
                editField: ['extra_name', 'extra_description']
             },
            {
                data: null,
                render: function ( data, type, row ) {
                if({data:"charge"} == 'PAX') 
                {   
                    return 'Adult '+ data.ps_adult_claim+' '+data.claim_cur+ ' Teen '+ data.ps_teen_claim+' '+data.claim_cur+ ' Child '+ data.ps_child_claim+' '+data.claim_cur+ ' Infant '+ data.ps_infant_claim+' '+data.claim_cur;
                }
                else
                {
                    return 'Unit '+ data.ps_adult_claim+' '+data.claim_cur;
                }
                }
            },
            {
                data: null,
                render: function ( data, type, row ) {
                if({data:"charge"} == 'PAX') 
                {   
                    return  'Adult '+ data.ps_adult_cost+' '+data.cost_cur+ ' Teen '+ data.ps_teen_cost+' '+data.cost_cur+ ' Child '+ data.ps_child_cost+' '+data.cost_cur+ ' Infant '+ data.ps_infant_cost+' '+data.cost_cur;
                }
                else
                {
                    return 'Unit '+ data.ps_adult_cost+' '+data.cost_cur
                }
                }
            }
        ]
    });
	
}