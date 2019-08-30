// function insertRateGrid(idBlock) {
//     $('#rateInsertDetailsSort').DataTable({       
//         "processing" : true,

//         "ajax" : {
//             "url" : "php/api/backoffservices_rates/rateclosedatetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idBlock,
//             dataSrc : ''
//         },
//         "destroy": true,
//         "bProcessing": true,
//         "bAutoWidth": false,
//         "pageLength": 5,
//         "responsive": true,
//         "bPaginate": true,
//         "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
//         +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
//         +"<'row'<'col-sm-12'tr>>"
//         +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

//         "buttons":[
//         ],
//         "columns" : [
//         {
//             "data" : "servicedatefrom"
//         }, {
//             "data" : "servicedateto"
//         }, {
//             "data" : "serviceclosedstartdate"
//         }, {
//             "data" : "serviceclosedenddate"
//         }, {
//             "data" : "serviceclosedstartdate"
//         }, {
//             "data" : "serviceclosedenddate"
//         },
        
//         {
//                 "targets": -2,
//                 "data": null,
//                 "class": 'deleteBtnCol',
//                 "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
//             }
//         ]
//     });
    
//     $('#rateInsertDetailsSort tbody').on( 'click', 'i', function () {
//         var table = $('#rateInsertDetailsSort').DataTable();
//         var data = table.row( $(this).parents('tr')).data();
//         //deleteRowRateDetailschk(data);
//     });
// }