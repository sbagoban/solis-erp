function runScript(e) {
    //See notes about 'which' and 'key'
    if (e.keyCode == 13) {
        var tb = document.getElementById("id_searchBooking");
        // eval(tb.value);
        searchDossier(tb.value);
        return false;
    }
}

function searchDossier(id) {
    const url_search_dossier = "php/api/bookingengine/searchDossier.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id;
    //var objSearchBooking = {id_booking: id};
    $.ajax({
        url: url_search_dossier,
        method: "POST",
        dataType: "json",
        //data: objSearchBooking,
        success: function (data) {
            displayData(data[0]);
            $('.toast_found').stop().fadeIn(400).delay(3000).fadeOut(500);
        },
        error: function (error) {
            console.log('Error ${error}');
            $('.toast_notfound').stop().fadeIn(400).delay(3000).fadeOut(500);
        }
    });
}

function displayData(dossierData) {
    enableTextField();
    console.log(dossierData);
    $('#booking_toRef').val();
    $('#booking_toName').val();
    $('#booking_paxOrigin').val();
    $('#booking_dept').val();
    $('#booking_dossierName').val();
    $('#booking_clientType').val();
    $('#booking_travelDate').val();
    $('#booking_adultAmt').val();
    $('#booking_teenAmt').val();
    $('#booking_childAmt').val();
    $('#booking_infantAmt').val();
    $('#booking_status').val();
    $('#booking_closureDate').val();
    $('#booking_createdBy').val();
    $('#booking_description').val();
}