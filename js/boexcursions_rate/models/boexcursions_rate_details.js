function insertRateGridAllDetails(idBlockRates) {
    
    $('#displayRateDetailsSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backoffservices_rates/ratedetailsgrid.php?t=" + encodeURIComponent(global_token) + "&idrates_fk=" + idBlockRates,
            dataSrc: ""
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
            "data" : "serviceclosedstartdate"
        }, {
            "data" : "serviceclosedenddate"
        }, {
            "data" : "country_name"
        }, {
            "data" : "toname"
        }, {
            "data" : "ratecodes"
        }, {
            "data" : "servicedateto"
        },
        {
            "targets": -1,
            "data": null,
            "class": 'editBtnCol',
            "defaultContent": "<a class='btn'><i aria-hidden='true' class='fa fa-external-link'></i> Edit</a>"
        },      
        {
                "targets": -2,
                "data": null,
                "class": 'deleteBtnCol',
                "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtnCol'></i>"
            }
        ]
    });

    $('#displayRateDetailsSort tbody').on( 'click', '.editBtnCol', function () {
        var table = $('#displayRateDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        editRowRateDetailsSort(data);
    });
    
    $('#displayRateDetailsSort tbody').on( 'click', '.deleteBtnCol', function () {
        var table = $('#displayRateDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr')).data();
        deleteRowRateDetailsSort(data);
    });
}

function editRowRateDetailsSort (data) {
    $('#rateModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    console.log('--->>> ', data);
}

// function deleteRowRateServiceDetails(data) {
//     var idBlock = document.getElementById('idBlock').innerHTML;
//     var objDel = {id: data.id};
//     const url_delete_rateDetails = "php/api/backoffservices_rates/rateservicetable_delete.php?t=" + encodeURIComponent(global_token) + "&id=" + data.id;
//     $.ajax({
//         url: url_delete_rateDetails,
//         method: "POST",
//         data: objDel,
//         success: function (data) {
//         },
//         error: function (error) {
//             console.log('Error ${error}');
//         }
//     });
//     insertRateGrid(idBlock);
// }
