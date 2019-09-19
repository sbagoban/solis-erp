$(document).ready(function(){
    allServiceGridCost();
});

function allServiceGridCost() {
    // Request call everything from database
    $('#productServiceRateSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bckoffservices/servicesgrid.php?t=" + encodeURIComponent(global_token),
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "pageLength": 5,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

        "buttons":[
            {
            "extend":    "csvHtml5",
            "text":      "<i class='fa fa-file-text-o'> Excel</i>",
            "titleAttr": "Download in Excel Format",
            }
        ],
        "columnDefs": [
        ],
        "columns" : [ {
            "data" : "countryfk"
        }, {
            "data" : "servicetypefk"
        }, {
            "data" : "supplierfk"
        }, {
            "data" : "optioncode"
        }, {
            "data" : "descriptionservice"
        }, {
            "data" : "comments"
        },
            {
                "targets": -1,
                "data": null,                
                "class": 'addBtnCol',
                "defaultContent": '<div class="btn-group"><button type="button" class="btn btn-primary"><i class="fa fa-fw fa-plus-circle"></i></button><button type="button" class="btn btn-primary"><i class="fa fa-fw fa-edit"></i><button type="button" class="btn btn-primary"><i class="fa fa-fw fa-trash"></i></button></button></div>'
            }
        ]
    });
    $('#productServiceRateSort tbody').on( 'click', '.addBtnCol', function () {
        var table = $('#productServiceRateSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        //editRowService(data);
    } );
}
