function insertRateGrid(idBlock) {
    $('#rateServiceDateSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/backoffservices_rates/rateservicetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idBlock,
            "dataSrc" : ""
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
                "targets": -3,
                "data": null,
                "class": 'editBtnCol',
                "defaultContent": "<a class='btn'><i aria-hidden='true' class='fa fa-external-link btnEditRateDetails'></i> Edit</a>"
            },      
            {
                "targets": -4,
                "data": null,
                "class": 'deleteBtnCol',
                "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
            }
        ]
    });

    $('#rateServiceDateSort tbody').on( 'click', '.btn', function () {
        var tableEdit = $('#rateServiceDateSort').DataTable();
        var data = tableEdit.row($(this).parents('tr')).data();
        editRowRate(data);
        rateDetailsEditRows(data.id);
        document.getElementById("serviceDateDisplay").innerHTML = 'From : ' + data.servicedatefrom + '&nbsp;&nbsp;To : ' + data.servicedateto;
    });
    
    $('#rateServiceDateSort tbody').on( 'click', '.deleteBtn', function () {
        var table = $('#rateServiceDateSort').DataTable();
        var data = table.row($(this).parents('tr')).data();
        deleteRowRateServiceDetails(data);
        rateDetailsEditRows(data.id);
    });
}

function deleteRowRateServiceDetails(data) {
    var idBlock = document.getElementById('idBlock').innerHTML;
    var objDel = {id: data.id};
    const url_delete_rateDetails = "php/api/backoffservices_rates/rateservicetable_delete.php?t=" + encodeURIComponent(global_token) + "&id=" + data.id;
    $.ajax({
        url: url_delete_rateDetails,
        method: "POST",
        data: objDel,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    insertRateGrid(idBlock);
}
