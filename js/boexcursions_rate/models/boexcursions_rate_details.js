function insertRateGridAllDetails(idBlock) {
    $('#displayRateDetailsSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backoffservices_rates/rateservicetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idBlock,
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "pageLength": 5,
        "responsive": true,
        "bPaginate": true,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

        "buttons":[
        ],
        "columns" : [
        {
            "data" : "servicedatefrom"
        }, {
            "data" : "servicedateto"
        },   
        {
            "data" : "servicedatefrom"
        }, {
            "data" : "servicedateto"
        },
        {
            "data" : "servicedatefrom"
        }, {
            "data" : "servicedateto"
        },
        {
            "targets": -1,
            "data": null,
            "class": 'editBtnCol',
            "defaultContent": "<a class='btn'><i aria-hidden='true' class='fa fa-external-link btnEditRateDetails'></i> Edit</a>"
        },      
        {
                "targets": -2,
                "data": null,
                "class": 'deleteBtnCol',
                "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
            }
        ]
    });

    $('#displayRateDetailsSort tbody').on( 'click', 'a', function () {
        var table = $('#displayRateDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        //editRowQuoteDetailschk(data);
    });
    
    $('#displayRateDetailsSort tbody').on( 'click', 'i', function () {
        var table = $('#displayRateDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr')).data();
        //deleteRowRateServiceDetails(data);
    });
}
