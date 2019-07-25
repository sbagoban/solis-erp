function callDataNewServiceGrid() {
    //$('#searchServiceDetails').load(document.URL +  ' #searchServiceDetails');
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
            { "width": "10px", "targets": 0 },
            { "width": "40px", "targets": 1 },
            { "width": "100px", "targets": 2 },
            { "width": "70px", "targets": 3 },
            { "width": "70px", "targets": 4 },
            { "width": "70px", "targets": 5 },
            { "width": "70px", "targets": 6 }
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
        },{
            data: null,
            render: function ( data, type, row ) {
                // Add click function send data(passed as parameter) take id - send to edit tables - cost details etc --
                checker = row;
                return '<a class="btn btnEdit" onclick="check1()"><i aria-hidden="true" class="fa fa-external-link"></i> Edit</a>';
            }
        }],
        
    });
}

function check1 () {
    
    ////////////////////////////////////////////////////
    // model --> Search Block Display None By Deafult //
    ////////////////////////////////////////////////////
    document.getElementById('editServiceBlock').setAttribute('style', 'display: block');
    document.getElementById('serviceDetails').setAttribute('style', 'display: block');
    console.log('Data Check -->', checker);

    document.getElementById("OptionCodeDisplay").innerHTML = checker.optioncode;
}
