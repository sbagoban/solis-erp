$("#searchService").click(function () {
    callDataSearchServiceGrid();
    document.getElementById('serviceDetails').setAttribute('style', 'display: none');
});

function callDataSearchServiceGrid() {    

    // Request call everything from database
    $('#sort').DataTable({       
        "processing" : true,
        
        "ajax" : {
            "url" : "php/api/bckoffservices/servicesgrid.php?t=" + encodeURIComponent(global_token),
            dataSrc : ''
        },
        "destroy": true,
        "bProcessing": true,
        "bAutoWidth": false,
        "responsive": true,
        "dom": "<'row'<'form-inline' <'col-sm-5'B>>>"
        +"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>"
        +"<'row'<'col-sm-12'tr>>"
        +"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

        "buttons":[
            {
            "extend":    "csvHtml5",
            "text":      "<i class='fa fa-file-text-o'> Excel</i>",
            "titleAttr": "Download in Excel Format",
            // "action":   function(){
            //         addFunctionHere ();
            //     }
            }
        ],
        "columnDefs": [
            { "visible": false, "targets": 6}
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
        }, {
            "data" : "services_notes"
        },
            {
                "targets": -1,
                "data": null,
                "defaultContent": "<a class='btn btnEdit'><i aria-hidden='true' class='fa fa-external-link'></i> Edit</a>"
            } 
        ]
    });
    $('#sort tbody').on( 'click', 'a', function () {
        var table = $('#sort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        editRowService(data);
    });
    animate();
}

function animate() {
    document.getElementById('searchServiceDetails').setAttribute('style', 'display: block');
    $('html, body').animate({
        scrollTop: $("#searchServiceDetails").offset().top
    }, 2000);
}
