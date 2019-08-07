function quoteDetailsEditRows(data, idQuoteDetails) {
    console.log('>> Chk',data);
    $('#quoteDetailsSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bckoffservices/extraservicetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + data.id,
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 3,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

        "buttons":[
            // {
            // "extend":    "csvHtml5",
            // "text":      "<i class='fa fa-file-text-o'> Excel</i>",
            // "titleAttr": "Download in Excel Format"
            // }
        ],
        
        // "columnDefs": [
        //     { "visible": false, "targets": 6},
        //     { "visible": false, "targets": 7},
        //     { "visible": false, "targets": 8},
        //     { "visible": false, "targets": 9},
        //     { "visible": false, "targets": 10},
        //     { "visible": false, "targets": 11},
        //     { "visible": false, "targets": 12},
        //     { "visible": false, "targets": 13},
        //     { "visible": false, "targets": 14},
        //     { "visible": false, "targets": 15},
        //     { "visible": false, "targets": 16}
        // ],
        "columns" : [ {
            "data" : "extraname"
        }, {
            "data" : "extradescription"
        }, {
            "data" : "chargeper"
        },
            {
                "targets": -1,
                "data": null,
                "class": 'editBtnCol',
                "defaultContent": "<a class='btn'><i aria-hidden='true' class='fa fa-external-link btnEditQuoteDetails'></i> Edit</a>"
            },
            {
                "targets": -2,
                "data": null,
                "class": 'deleteBtnCol',
                "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
            }
        ]
    });
    $('#quoteDetailsSort tbody').on( 'click', 'a', function () {
        var table = $('#quoteDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        console.log(data);
        editRowQuoteDetailschk(data);
    });
    $('#quoteDetailsSort tbody').on( 'click', 'i', function () {
        var table = $('#quoteDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        console.log(data);
        deleteRowQuoteDetailschk(data);
    });
} 


function editRowQuoteDetailschk(data) {
    $('#editModal').modal();
    document.getElementById('addNameEdit').value = data.extraname;
    document.getElementById('addDescEdit').value = data.extradescription;
    chkDataId = data.id; // Global Variable
    chkchargeper = data.chargeper;
}


$('#btnSaveEditQuoteDetails').click(function (event) { 
    var chkExtraName = document.getElementById('addNameEdit').value;
    var chkExtraDesc = document.getElementById('addDescEdit').value;
    
    var objEditQuoteDetails = {
        id: chkDataId,
        extraname: chkExtraName,        
        extradescription: chkExtraDesc,
        chargeper: chkchargeper
    };
    const url_edit_QuoteDetails = "php/api/bckoffservices/updatequotedetails.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_edit_QuoteDetails,
        method: "POST",
        data: objEditQuoteDetails,
        success: function (data) {
            console.log('value', data);
            quoteDetailsEditRows();
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
});

function deleteRowQuoteDetailschk(data) {
    console.log('>>>>>>>>>>>>>>>>>>', data.id);
    const url_delete_QuoteDetails = "php/api/bckoffservices/deleteextraservice.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_delete_QuoteDetails,
        method: "POST",
        type: "DELETE",
        data: data.id,
        success: function (data) {
            console.log('value', data);
            //quoteDetailsEditRows();
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
}