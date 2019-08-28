    // Create Rate Button click
    $("#createRateDetails").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        servicedatefunc(idCostDetails);
    });

    function servicedatefunc(idBlock) {
        var serviceDateFrom = $('#datetimepickerFrom').datepicker("getDate");
        var serviceDateTo = $('.serviceDateTo').datepicker("getDate");
        var formatFrom = ($.datepicker.formatDate("yy-mm-dd", serviceDateFrom));
        var formatTo = ($.datepicker.formatDate("yy-mm-dd", serviceDateTo));
        
        var objRateDate = {
            id:-1, //for new items, id is always -1
            idservicesfk: idBlock, //please make sure the names match in JS and PHP
            servicedatefrom: formatFrom,
            servicedateto: formatTo
        };

        const url_save_service_rate1 = "php/api/backoffservices_rates/saveratesdate.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service_rate1,
            method : "POST",
            data : objRateDate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    }

    function selectedClosedDateFunc(closedStartDate, closedEndDate) {
        var idBlock = document.getElementById('idBlock').innerHTML;
        console.log(idBlock, closedStartDate, closedEndDate);
        var objRateClosedDate = {
            id:-1, //for new items, id is always -1
            idservicesfk: idBlock, //please make sure the names match in JS and PHP
            serviceclosedstartdate: closedStartDate,
            serviceclosedenddate: closedEndDate
        };

        const url_save_service_rate2 = "php/api/backoffservices_rates/saveratesclosedate.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url : url_save_service_rate2,
            method : "POST",
            data : objRateClosedDate,                                                                                                                                                                                                                                                                                                                                                                                                                                              
            success : function(data){
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
        rateDetailsEditRows(idBlock);
    }

    function rateDetailsEditRows(idBlock) {
        $('#rateDetailsSort').DataTable({       
            "processing" : true,
    
            "ajax" : {
                "url" : "php/api/backoffservices_rates/rateclosedatetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idBlock,
                dataSrc : ''
            },
            "destroy": true,
            "bProcessing": true,
            "bAutoWidth": false,
            "pageLength": 5,
            "responsive": true,
            "bPaginate": true,
            "bLengthChange": false,
            "bInfo": false,
            "bFilter": false,
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
            "columns" : [
            {
                "data" : "serviceclosedstartdate"
            }, {
                "data" : "serviceclosedenddate"
            }, 
                // {
                //     "targets": -1,
                //     "data": null,
                //     "class": 'editBtnCol',
                //     "defaultContent": "<a class='btn'><i aria-hidden='true' class='fa fa-external-link btnEditQuoteDetails'></i> Edit</a>"
                // },
                {
                    "targets": -2,
                    "data": null,
                    "class": 'deleteBtnCol',
                    "defaultContent": "<i aria-hidden='true' class='fa fa-trash-o fa-lg deleteBtn'></i>"
                }
            ]
        });
        // $('#rateDetailsSort tbody').on( 'click', 'a', function () {
        //     var table = $('#rateDetailsSort').DataTable();
        //     var data = table.row( $(this).parents('tr') ).data();
        //    //editRowQuoteDetailschk(data);
        // });
        $('#rateDetailsSort tbody').on( 'click', 'i', function () {
            var table = $('#rateDetailsSort').DataTable();
            var data = table.row( $(this).parents('tr')).data();
            deleteRowRateDetailschk(data);
        });
    } 
    

function deleteRowRateDetailschk(data) {
    var idBlock = document.getElementById('idBlock').innerHTML;
    var objDel = {id: data.id};
    const url_delete_rateDetails = "php/api/backoffservices_rates/deleterateclosed.php?t=" + encodeURIComponent(global_token) + "&id=" + data.id;
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
    rateDetailsEditRows(idBlock);
}