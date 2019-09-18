function quoteDetailsEditRows(data, idQuoteDetails) {
    $('#quoteDetailsSort').DataTable({       
        "processing" : true,

        "ajax" : {
            "url" : "php/api/bckoffservices/extraservicetable.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idQuoteDetails,
            //"url" : "php/api/bckoffservices/extraservicetable.php?t=" + encodeURIComponent(global_token),
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
        "columns" : [
        {
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
    $('#quoteDetailsSort tbody').on( 'click', '.editBtnCol', function () {
        var table = $('#quoteDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
        editRowQuoteDetailschk(data);
    });
    $('#quoteDetailsSort tbody').on( 'click', '.deleteBtnCol', function () {
        var table = $('#quoteDetailsSort').DataTable();
        var data = table.row( $(this).parents('tr')).data();
        deleteRowQuoteDetailschk(data);
    });
} 

function deleteRowQuoteDetailschk(data) {
    var idCostDetails = document.getElementById('idBlock').innerHTML;
    var objDel = {id: data.id};
    const url_delete_QuoteDetails = "php/api/bckoffservices/deleteextraservice.php?t=" + encodeURIComponent(global_token) + "&id=" + data.id;
    $.ajax({
        url: url_delete_QuoteDetails,
        method: "POST",
        data: objDel,
        success: function (data) {
            console.log('value -2', data);
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
    quoteDetailsEditRows(data, idCostDetails);
}

function editRowQuoteDetailschk(data) {
    $('#editModal').modal();
    document.getElementById('addNameEdit').value = data.extraname;
    document.getElementById('addDescEdit').value = data.extradescription;
    if (data.chargeper == "unit") {
        document.getElementById('chargePerUnitEdit').checked = true;
    } else if (data.chargeper == "person") { 
        document.getElementById('chargePerPersonEdit').checked = true;
    }
    chkDataId = data.id; // Global Variable
    //chkchargeper = data.chargeper;
}

function chargePerToRadio() {
    var ele = document.getElementsByName('radioChargePerEdit');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            var check = ele[i].value;
    }
    return check;
}

$('#btnSaveEditQuoteDetails').click(function (event) { 
    var chkExtraName = document.getElementById('addNameEdit').value;
    var chkExtraDesc = document.getElementById('addDescEdit').value;
    var chkchargeper = chargePerToRadio();
    var idCostDetails = document.getElementById('idBlock').innerHTML;
    var objEditQuoteDetails = {
        id: chkDataId,
        extraname: chkExtraName,        
        extradescription: chkExtraDesc,
        chargeper: chkchargeper
    };
    const url_edit_quoteDetails = "php/api/bckoffservices/updatequotedetails.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_edit_quoteDetails,
        method: "POST",
        data: objEditQuoteDetails,
        success: function (data) {
            quoteDetailsEditRows(data, idCostDetails);
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
});

$('#updateQuoteDetails').click(function (event) { 
    var idCostDetails = document.getElementById('idBlock').innerHTML;
    updateInfantChildrenPax(idCostDetails);
    savePaxBreaks(idCostDetails);
    updatePaxBreaksDetails(idCostDetails);
});

function updateInfantChildrenPax(idCostDetails) {
    var chkChildrenPayBreaks = document.getElementById('childrenPayBreaks');
    var chkInfantPayBreaks = document.getElementById('infantPayBreaks');
    if (chkChildrenPayBreaks.checked == true) {
        chkChildrenPayBreaksValue = 1;
    } 
    if (chkChildrenPayBreaks.checked == false) {
        chkChildrenPayBreaksValue = 0;
    }
    if (chkInfantPayBreaks.checked == true) {
        chkInfantPayBreaksValue = 1;
    } 
    if (chkInfantPayBreaks.checked == false) {
        chkInfantPayBreaksValue = 0;
    }
    
    var objUpdateQuoteDetails = {
        id: idCostDetails,
        includechildren_paybreaks: chkChildrenPayBreaksValue,        
        includeinfant_paybreaks: chkInfantPayBreaksValue
    };
    const url_update_QuoteDetails = "php/api/bckoffservices/updatequotedetailspaybreaksinfantchildren.php?t=" + encodeURIComponent(global_token) + "&idservicesfk=" + idCostDetails;
    $.ajax({
        url: url_update_QuoteDetails,
        method: "POST",
        data: objUpdateQuoteDetails,
        success: function (data) {
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
}

function savePaxBreaks(idCostDetails) {

}
