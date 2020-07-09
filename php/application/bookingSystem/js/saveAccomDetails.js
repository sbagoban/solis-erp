$(document).ready(function() {
    arrClientAdult = [];
    dossierAccomodation();
});

$('#btn-saveAccom').click(function() {
    var id_booking = $("#id_booking").val();
    const url_search_booking = "php/api/bookingSystem/readBooking.php?t=" + encodeURIComponent(global_token) + "&id_booking=" +id_booking;
    $.ajax({
        url: url_search_booking,
        method: "POST",
        dataType: "json",
        success: function (dataRead)
        {

            var max_pax = parseInt(dataRead[0].adult_amt) +  parseInt(dataRead[0].teen_amt) +  parseInt(dataRead[0].child_amt) +  parseInt(dataRead[0].infant_amt);
            var travelDate = dataRead[0].booking_from;
            
            // Load AcommContract
            var mealplan = $('#accom_mealPlan').val();
            var accom_payer = $('#accom_payer').val();
            var accom_hotel = $('#accom_hotel').val();
            var accom_room = $('#accom_room').val();
            var accom_stay_start = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var accom_stay_end = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
            var accom_bookingDate = $("#accom_bookingDate").data('daterangepicker').startDate.format('YYYY-MM-DD');
            var travel_date = dataRead[0].booking_from;
            var accom_client = $('#accom_client').val();

            // call accom client
            // fetch accom client age
            // otherFile.js
            // initialize array
            var checkClientDetails = document.getElementById("clientDetails").innerHTML;
            // append new value to the array
            // arrClientDetails.push(checkClientDetails);
            string_to_array = function (str) {
                return str.trim().split(" ");
            };
            var arrClientDetails = string_to_array(checkClientDetails);
            for (let index = 0; index < arrClientDetails.length; index++) {
                count_person = index;
                element = arrClientDetails[index];
            }
                accom_client_details = [{count:count_person, age: element, bride_groom:""}];
                var arr_params_resa = {
                    mealplan : mealplan,
                    touroperator : accom_payer,
                    hotel :  accom_hotel,
                    hotelroom : accom_room,
                    checkin_date: accom_stay_start,
                    checkout_date: accom_stay_end,
                    booking_date : accom_bookingDate,
                    travel_date : travel_date,
                    max_pax : max_pax, 
                    checkin_time : '', 
                    checkout_time : '', 
                    suppmealplan : '', 
                    wedding_interested: 0,
                    arr_pax: accom_client_details
                }
            
                const getAccomContract= "php/api/bookingSystem/accomContract.php?t=" + encodeURIComponent(global_token);
                $('#loadingmessage_2').show();
                $.ajax({
                    url : getAccomContract,
                    method : "POST",
                    data : arr_params_resa, 
                    dataType: "json",
                        success : function(contractData) {
                            $('#loadingmessage_2').hide();
                             // id_booking_room_claim primary key
                            var room_stay_from = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var room_stay_to = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
                            var id_booking = $('#id_booking').val();
                            var room_adult_amt = $('#accom_adultAmt').val();
                            var room_teen_amt = $('#accom_TeentAmt').val();
                            var room_child_amt = $('#accom_childAmt').val();
                            var room_infant_amt = $('#accom_InfantAmt').val();
                            var room_status = $('#booking_status').val();
                            var objBookingRoom = { 
                                id_booking_room: -1,
                                id_booking: id_booking,
                                stay_from: room_stay_from,
                                stay_to: room_stay_to,
                                room_adult_amt: room_adult_amt,
                                room_teen_amt: room_teen_amt,
                                room_child_amt: room_child_amt,
                                room_infant_amt: room_infant_amt,
                                room_status: room_status
                            }
    
                            const url_save_booking = "php/api/bookingSystem/saveBookingRoom.php?t=" + encodeURIComponent(global_token);
                            $.ajax({
                                url : url_save_booking,
                                method : "POST",
                                data : objBookingRoom,
                                dataType: "json",
                                success : function(data){
                                    if (data.OUTCOME == "OK") {
                                        saveBookingRoomClaim(data, contractData);
                                    }
                                },
                                error: function(error) {
                                    console.log('Error ${error}');
                                }
                            });
                            
                        }, 
                        error: function (error) {
                            console.log('Error ${error}');
                        }
                });
            // End For Loop} 
            
        },
        error: function (error) 
        {
            console.log('Error ${error}');
        }
    });
    
});

function saveBookingRoomClaim(roomData, bookingDetails) {
    var booking_dept = $('#booking_dept').val();
    var id_tour_operator = $('#accom_payer').val();
    var room_booking_date = $('#accom_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var id_hotel = $('#accom_hotel').val();
    var hotelname = $('#accom_hotel').find('option:selected').attr("name");
    var room_details = $('#accom_room').find('option:selected').attr("name");
    var accom_mealPlan = $('#accom_mealPlan').val();
    var room_clients = $('#accom_room').val();
    var id_contract = document.getElementById("id_contract").innerHTML;
    var id_client = $('#accom_client').val();
    var room_service_paid_by = $('#accom_paidBy').val();    
    var meal_plan = $('#accom_mealPlan').val();
    var room_adult_amt = $('#accom_adultAmt').val();
    var room_teen_amt = $('#accom_TeentAmt').val();
    var room_child_amt = $('#accom_childAmt').val();
    var room_infant_amt = $('#accom_InfantAmt').val();
    var room_total_pax = +room_adult_amt + +room_teen_amt + +room_child_amt + +room_infant_amt;
    var room_remarks = $('#accom_serviceRemark').val();
    var room_internal_remarks = $('#accom_internalRemark').val();
    var booking_status = $('#booking_status').val();
    var room_claim_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].CLAIM_WITHOUT_SPO";
    var room_cost_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].COST";
    var id_room = bookingDetails.ROOM_ID;
    var room_infant_claim_rebate = 0;
    var room_infant_cost_rebate = 0;
    var room_child_cost_rebate = 0;
    var room_child_claim_rebate = 0;
    var room_teen_cost_rebate = 0;
    var room_teen_claim_rebate = 0;
    var room_adult_cost_rebate = 0;
    var room_adult_claim_rebate = 0;
    var room_rebate_cost_percentage = 0;
    var room_rebate_claim_percentage = 0;
    var room_rebate_claim_approve_by = 0;
    var room_rebate_claim_type = $('#accom_rebate').val();
    var room_rebate_claim_approve_by = $('#accom_approvedBy').val();
    var id_claim_cur = bookingDetails.CLAIM_CURRENCY_ID;

    var accom_client = $('#accom_client').val();
    // Only Adult
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length > 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length == 0) {
        adult_cost = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].COST;
        adult_claim = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].CLAIM_WITHOUT_SPO;
        children_claim = 0;
    } 
    // Adult And Children
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length > 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length > 0) { 
        adult_cost = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].COST;
        adult_claim = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].CLAIM_WITHOUT_SPO;
        children_claim = 0;
    }

    // Only Children
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length == 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length > 0) { 
        children_cost = bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN[0].COST;
        children_claim = bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN[0].CLAIM_WITHOUT_SPO;
    }

    arrCost = [];
    bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.forEach(element => {
        arrCost.push(element.COST);
    });
    var total_cost = arrCost.reduce((a, b) => a + b, 0);

    arrClaim = [];
    bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.forEach(element => {
        arrClaim.push(element.CLAIM_WITHOUT_SPO);
    });
    var total_claim = arrClaim.reduce((a, b) => a + b, 0);

    var accom_night = $('#accom_night').val();
    var final_claim = (total_claim * room_adult_amt) * accom_night;

    if (roomData.ROOM_TYPE == "PERSONS") {
        room_charge = "PAX";
    } else { 
        room_charge = "UNIT"
    }

    var objBookingRoomClaim = { 
        id_booking_room_claim: -1,
        id_booking: roomData.id_booking,
        id_booking_room: roomData.id_booking_room, 
        room_service_paid_by: room_service_paid_by,
        id_tour_operator: id_tour_operator, 
        id_client: 0,
        room_stay_from: roomData.stay_from, 
        room_stay_to: roomData.stay_to,
        room_booking_date: room_booking_date,
        id_contract: id_contract,
        id_hotel: id_hotel,
        hotelname: hotelname,
        id_room: id_room,
        room_details: room_details,
        service_details: "TARIFF", 
        room_claim_calcultation: room_claim_calcultation,
        room_adult_amt: room_adult_amt,
        room_teen_amt: room_teen_amt,
        room_child_amt: room_child_amt,
        room_infant_amt: room_infant_amt,
        room_total_pax: room_total_pax,
        id_dept: booking_dept, 
        room_charge: room_charge, 
        id_service_tax: 3, 
        tax_value: 15, 
        id_claim_cur: id_claim_cur, 
        room_adult_claim: adult_claim, 
        room_teen_claim: children_claim,
        room_child_claim: children_claim,
        room_infant_claim: children_claim,
        room_total_claim: final_claim,
        room_rebate_claim_type: room_rebate_claim_type,    
        room_rebate_claim_approve_by: room_rebate_claim_approve_by,
        room_rebate_claim_percentage: 0,
        room_adult_claim_rebate: 0,
        room_adult_claim_after_rebate: 0, 
        room_teen_claim_rebate: 0, 
        room_teen_claim_after_rebate: 0,
        room_child_claim_rebate: 0, 
        room_child_claim_after_rebate: 0,
        room_infant_claim_rebate: 0,
        room_infant_claim_after_rebate: 0, 
        room_total_claim_after_rebate: 0, 
        room_remarks: room_remarks, 
        room_internal_remarks: 	room_internal_remarks, 
        room_status: roomData.room_status, 
        booking_client: accom_client
    }

    const url_save_booking_claim = "php/api/bookingSystem/saveBookingRoomClaim.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_booking_claim,
        method : "POST",
        data : objBookingRoomClaim,
		dataType: "json",
        success : function(data){
            saveBookingRoomCost(data, bookingDetails);
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function saveBookingRoomCost(dataCost, bookingDetails) {
    var room_rebate_cost_type = "NONE";
    arrCost = [];
    bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.forEach(element => {
        arrCost.push(element.COST);
    });
    var total_cost = arrCost.reduce((a, b) => a + b, 0);

    // Only Adult
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length > 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length == 0) {
        adult_cost = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].COST;
        children_cost = 0;
    } 
    // Adult And Children
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length > 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length > 0) { 
        adult_cost = bookingDetails.COST_CLAIM_AMOUNTS.ADULTS[0].COST;
        children_cost = 0;
    }

    // Only Children
    if (bookingDetails.COST_CLAIM_AMOUNTS.ADULTS.length == 0 && bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN.length > 0) { 
        children_cost = bookingDetails.COST_CLAIM_AMOUNTS.CHILDREN[0].COST;
        adult_cost = 0;
    }
    
    var room_rebate_cost_approve_by = 0;
    var room_cost_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].COST";
    var id_cost_cur = dataCost.id_claim_cur;

    var room_stay_from = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var room_stay_to = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
    
    var booking_dept = $('#booking_dept').val();
    var objBookingRoomCost = { 
        id_booking_room_cost: -1,
        id_booking_room_claim: dataCost.id_booking_room_claim,
        id_booking: dataCost.id_booking,
        id_booking_room: dataCost.id_booking_room, 
        room_service_paid_by: dataCost.room_service_paid_by,
        id_tour_operator: dataCost.id_tour_operator, 
        id_client: 0,
        room_stay_from: room_stay_from, 
        room_stay_to: room_stay_to,
        room_booking_date: dataCost.room_booking_date,
        id_contract: dataCost.id_contract,
        id_hotel: dataCost.id_hotel,
        hotelname: dataCost.hotelname,
        id_room: dataCost.id_room,
        room_details: dataCost.room_details,
        service_details: "TARIFF", 
        room_cost_calcultation: room_cost_calcultation,
        room_adult_amt: dataCost.room_adult_amt,
        room_teen_amt: dataCost.room_teen_amt,
        room_child_amt: dataCost.room_child_amt,
        room_infant_amt: dataCost.room_infant_amt,
        room_total_pax: dataCost.room_total_pax,
        id_dept: booking_dept, 
        room_charge: dataCost.room_charge, 
        id_service_tax: 3, 
        tax_value: 15, 
        id_cost_cur: id_cost_cur, 
        room_adult_cost: adult_cost, 
        room_teen_cost: children_cost,
        room_child_cost: children_cost,
        room_infant_cost: children_cost,
        room_total_cost: total_cost,
        room_rebate_cost_type: room_rebate_cost_type,    
        room_rebate_cost_approve_by: room_rebate_cost_approve_by,
        room_rebate_cost_percentage: 0,
        room_adult_cost_rebate: 0,
        room_adult_cost_after_rebate: 0, 
        room_teen_cost_rebate: 0, 
        room_teen_cost_after_rebate: 0,
        room_child_cost_rebate: 0, 
        room_child_cost_after_rebate: 0,
        room_infant_cost_rebate: 0,
        room_infant_cost_after_rebate: 0,
        room_total_cost_after_rebate: 0,
        room_status: dataCost.room_status
    }

    const url_save_booking_cost = "php/api/bookingSystem/saveBookingRoomCost.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_booking_cost,
        method : "POST",
        data : objBookingRoomCost,
		dataType: "json",
        success : function(data){
            if (data.OUTCOME == 'OK') {
                var id_booking = $("#id_booking").val();
                allBookingAccom(id_booking);
                dossierAccomodation(data);
                if (bookingDetails.SPECIAL_OFFERS.SPOS.length == 0) {
                    toastr.success('New Booking saved successfully');
                } else { 
                    setTimeout(function() {
                        toastr.success('New Booking saved successfully');
                    }, 2000);
                    
                    for($i=0; $i < bookingDetails.SPECIAL_OFFERS.SPOS.length; $i++) {
                        saveBookingRoomClaimSpo(dataCost, bookingDetails.SPECIAL_OFFERS.SPOS[$i], data, bookingDetails.SPECIAL_OFFERS.TOTAL_DISCOUNTED_CLAIM_AMOUNT);
                    }
                    // Save accom for spo - create a new line -
                    // save cost and save claim
                }
            }
        }, 
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function dossierAccomodation (data) {

}

function saveBookingRoomClaimSpo(roomData, bookingDetails, costData, TOTAL_DISCOUNTED_CLAIM_AMOUNT) {
    var booking_dept = $('#booking_dept').val();
    var id_tour_operator = $('#accom_payer').val();
    var room_booking_date = $('#accom_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD');
    var id_hotel = $('#accom_hotel').val();
    var hotelname = $('#accom_hotel').find('option:selected').attr("name");
    var room_details = $('#accom_room').find('option:selected').attr("name");
    var id_contract = document.getElementById("id_contract").innerHTML;
    var id_client = $('#accom_client').val();
    var room_service_paid_by = $('#accom_paidBy').val();
    var room_adult_amt = $('#accom_adultAmt').val();
    var room_teen_amt = $('#accom_TeentAmt').val();
    var room_child_amt = $('#accom_childAmt').val();
    var room_infant_amt = $('#accom_InfantAmt').val();
    var room_total_pax = +room_adult_amt + +room_teen_amt + +room_child_amt + +room_infant_amt;
    var room_remarks = $('#accom_serviceRemark').val();
    var room_internal_remarks = 'SPO - NAME' + bookingDetails.SPO_ID + 'SPO - NAME' + bookingDetails.SPO_NAME;
    var room_claim_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].CLAIM_WITHOUT_SPO";
    var id_room = costData.id_room;
    var room_rebate_claim_approve_by = $('#accom_approvedBy').val();
    var room_rebate_claim_type = $('#accom_rebate').val();    
    id_claim_cur = costData.id_cost_cur;
    var accom_client = $('#accom_client').val();
    
    if (roomData.ROOM_TYPE == "PERSONS") {
        room_charge = "PAX";
    } else { 
        room_charge = "UNIT"
    }

    var final_offer_claim = TOTAL_DISCOUNTED_CLAIM_AMOUNT - roomData.room_total_claim;

    var room_stay_from = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
    var room_stay_to = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
    // Object - 
    var objBookingRoomClaimSpo = { 
        id_booking_room_claim: -1,
        id_booking: roomData.id_booking,
        id_booking_room: roomData.id_booking_room, 
        room_service_paid_by: room_service_paid_by,
        id_tour_operator: id_tour_operator, 
        id_client: 0,
        room_stay_from: room_stay_from, 
        room_stay_to: room_stay_to,
        room_booking_date: room_booking_date,
        id_contract: id_contract,
        id_hotel: id_hotel,
        hotelname: hotelname,
        id_room: id_room,
        room_details: room_details,
        service_details: "OFFER", 
        room_claim_calcultation: room_claim_calcultation,
        room_adult_amt: room_adult_amt,
        room_teen_amt: room_teen_amt,
        room_child_amt: room_child_amt,
        room_infant_amt: room_infant_amt,
        room_total_pax: room_total_pax,
        id_dept: booking_dept, 
        room_charge: room_charge, 
        id_service_tax: 3,
        tax_value: 15, 
        id_claim_cur: id_claim_cur, 
        room_adult_claim: TOTAL_DISCOUNTED_CLAIM_AMOUNT, // add spo total here
        room_teen_claim: 0,
        room_child_claim: 0,
        room_infant_claim: 0,
        room_total_claim: final_offer_claim,
        room_rebate_claim_type: room_rebate_claim_type,
        room_rebate_claim_approve_by: room_rebate_claim_approve_by,
        room_rebate_claim_percentage: 0,
        room_adult_claim_rebate: 0,
        room_adult_claim_after_rebate: 0, 
        room_teen_claim_rebate: 0, 
        room_teen_claim_after_rebate: 0,
        room_child_claim_rebate: 0, 
        room_child_claim_after_rebate: 0,
        room_infant_claim_rebate: 0,
        room_infant_claim_after_rebate: 0, 
        room_total_claim_after_rebate: 0, 
        room_remarks: room_remarks, 
        room_internal_remarks: 	room_internal_remarks, 
        room_status: roomData.room_status, 
        booking_client: accom_client
    }

    const url_save_booking_claim_spo = "php/api/bookingSystem/saveBookingRoomClaim.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url : url_save_booking_claim_spo,
        method : "POST",
        data : objBookingRoomClaimSpo,
		dataType: "json",
        success : function(data){
            if (data.OUTCOME == "OK") {
                saveBookingRoomCostSpo(data, roomData, bookingDetails, costData, TOTAL_DISCOUNTED_CLAIM_AMOUNT);
            }
        },
        error: function(error) {
            console.log('Error ${error}');
        }
    });
}

function saveBookingRoomCostSpo(data, roomData, bookingDetails, costData, TOTAL_DISCOUNTED_CLAIM_AMOUNT) {
        var id_booking = $('#id_booking').val();
        var booking_dept = $('#booking_dept').val();
        var id_tour_operator = $('#accom_payer').val();
        var room_booking_date = $('#accom_bookingDate').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var id_hotel = $('#accom_hotel').val();
        var hotelname = $('#accom_hotel').find('option:selected').attr("name");
        var room_details = $('#accom_room').find('option:selected').attr("name");
        var id_contract = document.getElementById("id_contract").innerHTML;
        var id_client = $('#accom_client').val();
        var room_service_paid_by = $('#accom_paidBy').val();
        var room_adult_amt = $('#accom_adultAmt').val();
        var room_teen_amt = $('#accom_TeentAmt').val();
        var room_child_amt = $('#accom_childAmt').val();
        var room_infant_amt = $('#accom_InfantAmt').val();
        var room_total_pax = +room_adult_amt + +room_teen_amt + +room_child_amt + +room_infant_amt;
        var room_remarks = $('#accom_serviceRemark').val();
        var room_internal_remarks = 'SPO - NAME' + bookingDetails.SPO_ID + 'SPO - NAME' + bookingDetails.SPO_NAME;
        var room_claim_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].CLAIM_WITHOUT_SPO";
        var room_cost_calcultation = "data.COST_CLAIM_AMOUNTS.ADULTS.length * data.COST_CLAIM_AMOUNTS.ADULTS.[0].COST";
        var id_room = costData.id_room;
        var room_rebate_claim_approve_by = $('#accom_approvedBy').val();
        var room_rebate_claim_type = $('#accom_rebate').val();
        var id_cost_cur = data.id_claim_cur;
    
        var accom_client = $('#accom_client').val();
        
        if (roomData.ROOM_TYPE == "PERSONS") {
            room_charge = "PAX";
        } else { 
            room_charge = "UNIT"
        }
    
        
        var room_stay_from = $("#accom_stay").data('daterangepicker').startDate.format('YYYY-MM-DD');
        var room_stay_to = $("#accom_stay").data('daterangepicker').endDate.format('YYYY-MM-DD');
        // Object - 
        var objBookingRoomCostSpo = { 
            id_booking_room_cost: -1,
            id_booking_room_claim: data.id_booking_room_claim, 
            id_booking: roomData.id_booking,
            id_booking_room: roomData.id_booking_room, 
            room_service_paid_by: room_service_paid_by,
            id_tour_operator: id_tour_operator, 
            id_client: 0,
            room_adult_cost: costData.room_adult_cost,
            room_stay_from: room_stay_from, 
            room_stay_to: room_stay_to,
            room_booking_date: room_booking_date,
            id_contract: id_contract,
            id_hotel: id_hotel,
            hotelname: hotelname,
            id_room: id_room,
            room_details: room_details,
            service_details: "OFFER", 
            room_claim_calcultation: room_claim_calcultation,
            room_cost_calcultation : room_cost_calcultation ,
            room_adult_amt: room_adult_amt,
            room_teen_amt: room_teen_amt,
            room_child_amt: room_child_amt,
            room_infant_amt: room_infant_amt,
            room_total_pax: room_total_pax,
            id_dept: booking_dept, 
            room_charge: room_charge, 
            id_service_tax: 3,
            tax_value: 15, 
            id_cost_cur: id_cost_cur, 
            room_adult_claim: TOTAL_DISCOUNTED_CLAIM_AMOUNT, // add spo total here
            room_teen_claim: 0,
            room_child_claim: 0,
            room_infant_claim: 0,
            room_teen_cost: 0,
            room_child_cost: 0,
            room_infant_cost: 0,
            room_total_claim: TOTAL_DISCOUNTED_CLAIM_AMOUNT,
            room_rebate_claim_type: room_rebate_claim_type,
            room_rebate_claim_approve_by: room_rebate_claim_approve_by,
            room_rebate_claim_percentage: 0,
            room_adult_claim_rebate: 0,
            room_adult_claim_after_rebate: 0, 
            room_teen_claim_rebate: 0, 
            room_teen_claim_after_rebate: 0,
            room_child_claim_rebate: 0, 
            room_child_claim_after_rebate: 0,
            room_infant_claim_rebate: 0,
            room_infant_claim_after_rebate: 0, 
            room_total_claim_after_rebate: 0, 
            room_remarks: room_remarks, 
            room_internal_remarks: 	room_internal_remarks, 
            room_status: roomData.room_status, 
            booking_client: accom_client, 
            room_total_cost: costData.room_total_cost, 
            room_rebate_cost_type: roomData.room_rebate_claim_type,
            room_rebate_cost_approve_by: 0,
            room_rebate_cost_percentage: 0,
            room_adult_cost_rebate: 0,
            room_adult_cost_after_rebate: 0, 
            room_teen_cost_rebate: 0, 
            room_teen_cost_after_rebate: 0,
            room_child_cost_rebate: 0, 
            room_child_cost_after_rebate: 0,
            room_infant_cost_rebate: 0,
            room_infant_cost_after_rebate: 0,
            room_total_cost_after_rebate: 0
        }
    
        const url_save_booking_cost_spo = "php/api/bookingSystem/saveBookingRoomCost.php?t=" + encodeURIComponent(global_token);
    
        $.ajax({
            url : url_save_booking_cost_spo,
            method : "POST",
            data : objBookingRoomCostSpo,
            dataType: "json",
            success : function(data){
                if (data.OUTCOME == "OK") { 
                    setTimeout(function() {
                        toastr.info('New Booking offer saved successfully');
                    }, 5000);
                    allBookingAccom(id_booking)
                }
            },
            error: function(error) {
                console.log('Error ${error}');
            }
        });
    }