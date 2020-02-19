function gridAccomDetails(data) {
    var html = '';
    if (data.ROOM_TYPE == 'PERSONS') {
        var  room_charge = 'PERSONS';
    } else if (data.ROOM_TYPE == 'UNITS') {
        var  room_charge = 'UNIT';
    }

    html += '<table id="tbl-accomDetails" class="table table-bordered table-hover">';
        html += '<tr>';
            html += '<th>Room Charge</th>';
                html += '<td colspan="3">' + room_charge + '</td>';
        html += '</tr>';
        html += '<tr>';
            html += '<th>Room Status</th>';
                html += '<td colspan="3">On Request</td>';
        html += '</tr>';
        html += '<tr id="age_policies"';
            html += '<th>Room Policy</th>';
                if (data.AGE_POLICIES.length < 0) {
                    html += '<td>Adult</td>';
                } else {
                    $.each(data.AGE_POLICIES, function(key, value){
                        html += '<td>Child : ' + value.AGEFROM + ' - ' + value.AGETO + '</td>';
                    });
                }
        html += '</tr>';
        html += '<tr>';
            html += '<th class="text-center"  colspan="4">Room Claim Description</th>';
        html += '</tr>';

        html += '<tr>';
            html += '<th>Pax Type</th>';
            html += '<th>Claim</th>';
            html += '<th>Special Claim</th>';
            html += '<th>Cost</th>';
        html += '</tr>';

        $.each(data.COST_CLAIM_AMOUNTS, function(key_name, value){
            $.each(value, function(key, value){ 
                html += '<tr>';
                    html += '<td>' + key_name + '</td>';
                    html += '<td>' + value.CLAIM_WITHOUT_SPO + '</td>';
                    html += '<td>' + value.CLAIM_WITH_SPO + '</td>';
                    html += '<td>' + value.COST + '</td>';
                html += '</tr>';
            });
        });

        html += '<tr>';
            html += '<th class="text-center"  colspan="4">Special Offer apply</th>';
        html += '</tr>';

        $.each(data.SPECIAL_OFFERS.SPOS, function(key, value_1){
            html += '<tr>';
                html += '<td colspan="4">' + value_1.SPO_NAME + '</td>';
            html += '</tr>';
        });

        // TOTAL_DISCOUNTED_CLAIM_AMOUNT amount
        var TOTAL_DISCOUNTED_CLAIM_AMOUNT = data.SPECIAL_OFFERS.TOTAL_DISCOUNTED_CLAIM_AMOUNT;
        html += '<tr>';
            html += '<td colspan="3"> Total discounted claim amount :</td>';
            html += '<td colspan="1">' + TOTAL_DISCOUNTED_CLAIM_AMOUNT  + '</td>';
        html += '</tr>';

    html += '</table>';

    $('#grid_accom_details').html(html);
}