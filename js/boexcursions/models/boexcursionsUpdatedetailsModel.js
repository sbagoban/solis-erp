
    function editRowService(data) {
        console.log('test >>', data);
        // Set Value - From Selected Line
        document.getElementById("OptionCodeDisplay").innerHTML = data.optioncode;
        document.getElementById("descriptionDisplay").innerHTML = data.descriptionservice;
        document.getElementById("commentsDisplay").innerHTML = data.comments;
        ////////////////////////////////////////////////////
        // model --> Search Block Display None By Deafult //
        ////////////////////////////////////////////////////
        document.getElementById('editServiceBlock').setAttribute('style', 'display: block');
        document.getElementById('serviceDetails').setAttribute('style', 'display: block');

        document.getElementById("idBlock").innerHTML = data.id;

        // Set value to Rich text area - For Notes
        $('#txtEditor').Editor('setText', data.services_notes);
        
        // Cost details
        costDetailsEditRows(data);
        // Set Value to voucher details -
        voucherDetailsEditRows(data);

        // Set Value to voucher details -
        policiesDetailsEditRows(data);
        // Quote Details
        
        var idQuoteDetails = document.getElementById('idBlock').innerHTML;
        quoteDetailsEditRows(data, idQuoteDetails);

        $('html, body').animate({
            scrollTop: $("#OptionCodeDisplay").offset().top
        }, 2000);
    }

    function policiesDetailsEditRows(data) {
        document.getElementById('infantMin').value = data.infantmin_policies;        
        document.getElementById('infantMax').value = data.infantmax_policies;   
        document.getElementById('childMin').value  = data.childmin_policies;
        document.getElementById('childMax').value  = data.childmax_policies;
        document.getElementById('teenMin').value   = data.teenmin_policies;
        document.getElementById('teenMax').value   = data.teenmax_policies;
        document.getElementById('adultMin').value  = data.adultmin_policies;
        document.getElementById('adultMax').value  = data.adultmax_policies;

        radioButtonsSetting = data.settingapplyto_policies;
        radioButtonsCross = data.crossseasonsrates_policies;

        switch (radioButtonsSetting) {
            case "supplier":
                document.getElementById("supplierRadio").checked = true;
                break
            case "produt":
                document.getElementById("produtRadio").checked = true;
                break
            default:
                console.log("No voucher creation");
        }
        switch (radioButtonsCross) {
            case "use first rate":
                document.getElementById("useFirstRate").checked = true;
                break
            case "split service":
                document.getElementById("splitService").checked = true;
                break
            case "use average rate":
                document.getElementById("useAverageRate").checked = true;
                break
            case "not allowed":
                document.getElementById("notAllowed").checked = true;
                break
            default:
                console.log("No voucher creation");
        }
    }

    function costDetailsEditRows(data) {
        // Set Value to textfield - From selected line
        document.getElementById("ddlChooseLocality").value = data.locality_costdetails;
        document.getElementById("ddlChooseDept").value = data.locality_costdetails;
        document.getElementById("minAdults").value = data.min_adults_costdetails;
        document.getElementById("maxAdults").value = data.max_adults_costdetails;
        document.getElementById("minChildren").value = data.min_children_costdetails;
        document.getElementById("maxChildren").value = data.max_children_costdetails;
        document.getElementById("descriptionInvoiceCostDetails").value = data.invoice_desciption_costdetails;

        //$('#duration').durationPicker('setTime', data.duration_costdetails);
        //$('#selector').data('durationPicker').setValue(1);
        document.getElementById("textFieldDescriptionCostDetails").value = data.descriptionservice;
        document.getElementById("textFieldCommentsCostDetails").value = data.comments;
        document.getElementById("textFieldInvoiceCostDetails").value = data.supplierfk;

        radioButtonsAdults = data.charged_unit_adults_costdetails;
        radioButtonsChildren = data.charged_unit_children_costdetails
        //radioButtonsTax = data.taxbasis_costdetails

        switch (radioButtonsAdults) {
            case "adults":
                document.getElementById("adults").checked = true;
                break
            case "unit":
                document.getElementById("unit").checked = true;
                break
            default:
                console.log("No voucher creation");
        }

        switch (radioButtonsChildren) {
            case "children":
                document.getElementById("children").checked = true;
                break
            case "unit":
                document.getElementById("unitChildren").checked = true;
                break
            default:
                console.log("No voucher creation");
        }

        // switch (radioButtonsTax) {
        //     case "inclusive":
        //         document.getElementById("inclusive").checked = true;
        //         break
        //     case "exclusive":
        //         document.getElementById("exclusive").checked = true;
        //         break
        //     default:
        //         console.log("No voucher creation");
        // }

        
        var checkBoxPickOff = document.getElementById("pickOffDropOff");  
        var checkBoxStartMonday = document.getElementById("chkMonday");
        var checkBoxStartTuesday = document.getElementById("chkTuesday");
        var checkBoxStartWednesday = document.getElementById("chkWednesday");
        var checkBoxStartThursday = document.getElementById("chkThursday");
        var checkBoxStartFriday = document.getElementById("chkFriday");
        var checkBoxStartSaturday = document.getElementById("chkSaturday");
        var checkBoxStartSunday = document.getElementById("chkSunday");
        var checkBoxIncludeMonday = document.getElementById("chkMondayInclude");
        var checkBoxIncludeTuesday = document.getElementById("chkTuesdayInclude");
        var checkBoxIncludeWednesday = document.getElementById("chkWednesdayInclude");
        var checkBoxIncludeThursday = document.getElementById("chkThursdayInclude");
        var checkBoxIncludeFriday = document.getElementById("chkFridayInclude");
        var checkBoxIncludeSaturday = document.getElementById("chkSaturdayInclude");
        var checkBoxIncludeSunday = document.getElementById("chkSundayInclude");

        if (data.pickoffdropoff_policies == 1){
            checkBoxPickOff.checked = true;
        }
        if (data.pickoffdropoff_policies == 0) {
            checkBoxPickOff.checked = false;
        }
        if (data.starton_monday_policies == 1){
            checkBoxStartMonday.checked = true;
        }
        if (data.starton_monday_policies == 0) {
            checkBoxStartMonday.checked = false;
        }
        if (data.starton_tuesday_policies == 1){
            checkBoxStartTuesday.checked = true;
        }
        if (data.starton_tuesday_policies == 0) {
            checkBoxStartTuesday.checked = false;
        }
        if (data.starton_wednesday_policies == 1){
            checkBoxStartWednesday.checked = true;
        }
        if (data.starton_wednesday_policies == 0) {
            checkBoxStartWednesday.checked = false;
        }
        if (data.starton_thursday_policies == 1){
            checkBoxStartThursday.checked = true;
        }
        if (data.starton_thursday_policies == 0) {
            checkBoxStartThursday.checked = false;
        }
        if (data.starton_friday_policies == 1){
            checkBoxStartFriday.checked = true;
        }
        if (data.starton_friday_policies == 0) {
            checkBoxStartFriday.checked = false;
        }
        if (data.starton_saturday_policies == 1){
            checkBoxStartSaturday.checked = true;
        }
        if (data.starton_saturday_policies == 0) {
            checkBoxStartSaturday.checked = false;
        }
        if (data.starton_sunday_policies == 1){
            checkBoxStartSunday.checked = true;
        }
        if (data.starton_sunday_policies == 0) {
            checkBoxStartSunday.checked = false;
        }
        if (data.mustinclude_monday_policies == 1){
            checkBoxIncludeMonday.checked = true;
        }
        if (data.mustinclude_monday_policies == 0) {
            checkBoxIncludeMonday.checked = false;
        }
        if (data.mustinclude_tuesday_policies == 1){
            checkBoxIncludeTuesday.checked = true;
        }
        if (data.mustinclude_tuesday_policies == 0) {
            checkBoxIncludeTuesday.checked = false;
        }
        if (data.mustinclude_wednesday_policies == 1){
            checkBoxIncludeWednesday.checked = true;
        }
        if (data.mustinclude_wednesday_policies == 0) {
            checkBoxIncludeWednesday.checked = false;
        }
        if (data.mustinclude_thursday_policies == 1){
            checkBoxIncludeThursday.checked = true;
        }
        if (data.mustinclude_thursday_policies == 0) {
            checkBoxIncludeThursday.checked = false;
        }
        if (data.mustinclude_friday_policies == 1){
            checkBoxIncludeFriday.checked = true;
        }
        if (data.mustinclude_friday_policies == 0) {
            checkBoxIncludeFriday.checked = false;
        }        
        if (data.mustinclude_saturday_policies == 1){
            checkBoxIncludeSaturday.checked = true;
        }
        if (data.mustinclude_saturday_policies == 0) {
            checkBoxIncludeSaturday.checked = false;
        }
        if (data.mustinclude_sunday_policies == 1){
            checkBoxIncludeSunday.checked = true;
        }
        if (data.mustinclude_sunday_policies == 0) {
            checkBoxIncludeSunday.checked = false;
        }
    }

    ///////////////////////////////////////////////////////////////
    // model --> Search Block Display By Deafult voucher details //
    ///////////////////////////////////////////////////////////////
    function voucherDetailsEditRows(data) {
        document.getElementById('addressVoucherDetails').value = data.address_voucherdetails;        
        document.getElementById('countryVoucherDetails').value = data.country_voucherdetails;        
        document.getElementById('stateVoucherDetails').value = data.state_voucherdetails;
        document.getElementById('postCodeVoucherDetails').value = data.postcode_voucherdetails;
        document.getElementById('line1').value = data.vouchertext1_voucherdetails;        
        document.getElementById('line2').value = data.vouchertext2_voucherdetails;        
        document.getElementById('line3').value = data.vouchertext3_voucherdetails;
        document.getElementById('line4').value = data.vouchertext4_voucherdetails;
        radioButtonsCreation = data.vouchercreation_voucherdetails;
        radioButtonsPrint = data.printvoucher_voucherdetails;

        switch (radioButtonsCreation) {
            case "One Voucher":
                document.getElementById("oneVoucher").checked = true;
                break
            case "Voucher For Each Person":
                document.getElementById("voucherEachPerson").checked = true;
                break
            case "Vouchers for each Day":
                document.getElementById("voucherEachDay").checked = true;
                break
            case "Vouchers for each Person per Day":
                document.getElementById("voucherEachPersonDay").checked = true;
                break
            default:
                console.log("No voucher creation");
        }
        switch (radioButtonsPrint) {
            case "Print Voucher":
                document.getElementById("printVoucher").checked = true;
                break
            case "No Cost":
                document.getElementById("noCost").checked = true;
                break
            case "Record liability only":
                document.getElementById("recordLiability").checked = true;
                break
            default:
                console.log("No voucher creation");
        }
    }

        

    function dateManipulation() {
        var time = document.getElementById('duration').value;
        // Hours, minutes and seconds
        var hrs = ~~(time / 3600);
        var mins = ~~((time % 3600) / 60);
        var secs = ~~time % 60;

        // Output like "1:01" or "4:03:59" or "123:03:59"
        var ret = "";

        if (hrs > 0) {
            ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
        }

        ret += "" + mins + ":" + (secs < 10 ? "0" : "");
        ret += "" + secs;
        return ret;
    }

    // function displayTaxBasis() {
    //     var ele = document.getElementsByName('radioGroupTax');
    //     for (i = 0; i < ele.length; i++) {
    //         if (ele[i].checked)
    //             var check = ele[i].value;
    //     }
    //     return check;
    // }

    function displayCostChildren() {
        var ele = document.getElementsByName('radioCostChildren');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }


    function displayCostAdults() {
        var ele = document.getElementsByName('radioGroupAdult');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }

    /////////////////////////////////////////
    // model --> update form value to dB //////
    /////////////////////////////////////////
    $("#updateService").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        var ddlChooseLocality = $('#ddlChooseLocality').val();
        var descriptionInvoiceCostDetails = document.getElementById('descriptionInvoiceCostDetails').value;
        var textFieldDescriptionCostDetails = document.getElementById('textFieldDescriptionCostDetails').value;
        var textFieldCommentsCostDetails = document.getElementById('textFieldCommentsCostDetails').value;
        var minChildren = document.getElementById('minChildren').value;
        var maxChildren = document.getElementById('maxChildren').value;
        var minAdults = document.getElementById('minAdults').value;
        var maxAdults = document.getElementById('maxAdults').value;
        var timeDuration = dateManipulation();
        //var taxBasis = displayTaxBasis();
        var costChargedChildren = displayCostChildren();
        var costChargedAdults = displayCostAdults();

        var objUpdateService = {
            id: idCostDetails,
            locality_costdetails: ddlChooseLocality, //please make sure the names match in JS and PHP
            invoice_desciption_costdetails: descriptionInvoiceCostDetails,
            descriptionservice: textFieldDescriptionCostDetails,
            comments: textFieldCommentsCostDetails,
            duration_costdetails: timeDuration,
            //taxbasis_costdetails: taxBasis,
            charged_unit_children_costdetails: costChargedChildren,
            min_children_costdetails: minChildren,
            max_children_costdetails: maxChildren,
            charged_unit_adults_costdetails: costChargedAdults,
            min_adults_costdetails: minAdults,
            max_adults_costdetails: maxAdults
        };

        const url_update_costDetails = "php/api/bckoffservices/savecostdetails.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url: url_update_costDetails,
            method: "POST",
            data: objUpdateService,
            success: function (data) {
                //resetFormUpdatedService();
                callDataNewServiceGrid();
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function (error) {
                console.log('Error ${error}');
            }
        });   
        var calcNumberOfPersons = +maxAdults + +minAdults;
        getUnitsPopulate(calcNumberOfPersons);
    });
    // End click

    function getUnitsPopulate(data) {
        for (var count = 0; count < data; count++) {
            $("<div class='col-md-4'><input type='number' max='9999' min='1' class='form-control'></div>").appendTo("#paybreaksPopulate");
        }
    }

    /////////////////////////////////////////
    // model --> Update Notes to dB /////////
    /////////////////////////////////////////
    $("#updateNotes").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        var txtEditorService = $(".Editor-editor").html();
        var objNotes = {
            id:idCostDetails,
            services_notes: txtEditorService
        };
        const url_update_notes = "php/api/bckoffservices/savenotes.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url: url_update_notes,
            method: "POST",
            data: objNotes,
            success: function (data) {
                console.log('value', data);
                callDataNewServiceGrid();
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function (error) {
                console.log('Error ${error}');
            }
        }); 
    });

    
    function settingApplyToRadio() {
        var ele = document.getElementsByName('radioSettingApply');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }

    
    function crossSeasonsRatesRadio() {
        var ele = document.getElementsByName('radioCrossSeasonRates');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }

    /////////////////////////////////////////////
    // model --> Update Policies to dB //////////
    /////////////////////////////////////////////
    $("#updatePolicies").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        var settingApplyTo = settingApplyToRadio();
        var crossSeasonsRates = crossSeasonsRatesRadio();
        var checkBoxPickOff = document.getElementById("pickOffDropOff");        
        var infantMinAge = document.getElementById('infantMin').value;        
        var infantMaxAge = document.getElementById('infantMax').value;   
        var childMinAge = document.getElementById('childMin').value;
        var childMaxAge = document.getElementById('childMax').value;
        var teenMinAge = document.getElementById('teenMin').value;
        var teenMaxAge = document.getElementById('teenMax').value;
        var adultMinAge = document.getElementById('adultMin').value;
        var adultMaxAge = document.getElementById('adultMax').value;
        var checkBoxStartMonday = document.getElementById("chkMonday");
        var checkBoxStartTuesday = document.getElementById("chkTuesday");
        var checkBoxStartWednesday = document.getElementById("chkWednesday");
        var checkBoxStartThursday = document.getElementById("chkThursday");
        var checkBoxStartFriday = document.getElementById("chkFriday");
        var checkBoxStartSaturday = document.getElementById("chkSaturday");
        var checkBoxStartSunday = document.getElementById("chkSunday");

        var checkBoxIncludeMonday = document.getElementById("chkMondayInclude");
        var checkBoxIncludeTuesday = document.getElementById("chkTuesdayInclude");
        var checkBoxIncludeWednesday = document.getElementById("chkWednesdayInclude");
        var checkBoxIncludeThursday = document.getElementById("chkThursdayInclude");
        var checkBoxIncludeFriday = document.getElementById("chkFridayInclude");
        var checkBoxIncludeSaturday = document.getElementById("chkSaturdayInclude");
        var checkBoxIncludeSunday = document.getElementById("chkSundayInclude");

        if (checkBoxPickOff.checked == true){
            checkBoxPickOffT = 1;
        }
        if (checkBoxPickOff.checked == false) {
            checkBoxPickOffT = 0;
        }
        if (checkBoxStartMonday.checked == true){
            checkBoxStartMondayT = 1;
        }
        if (checkBoxStartMonday.checked == false) {
            checkBoxStartMondayT = 0;
        }
        if (checkBoxStartTuesday.checked == true){
            checkBoxStartTuesdayT = 1;
        }
        if (checkBoxStartTuesday.checked == false) {
            checkBoxStartTuesdayT = 0;
        }
        if (checkBoxStartWednesday.checked == true){
            checkBoxStartWednesdayT = 1;
        }
        if (checkBoxStartWednesday.checked == false) {
            checkBoxStartWednesdayT = 0;
        }
        if (checkBoxStartThursday.checked == true){
            checkBoxStartThursdayT = 1;
        }
        if (checkBoxStartThursday.checked == false) {
            checkBoxStartThursdayT = 0;
        }
        if (checkBoxStartFriday.checked == true){
            checkBoxStartFridayT = 1;
        }
        if (checkBoxStartFriday.checked == false) {
            checkBoxStartFridayT = 0;
        }        
        if (checkBoxStartSaturday.checked == true){
            checkBoxStartSaturdayT = 1;
        }
        if (checkBoxStartSaturday.checked == false) {
            checkBoxStartSaturdayT = 0;
        }
        if (checkBoxStartSunday.checked == true){
            checkBoxStartSundayT = 1;
        }
        if (checkBoxStartSunday.checked == false) {
            checkBoxStartSundayT = 0;
        }
        if (checkBoxIncludeMonday.checked == true){
            checkBoxIncludeMondayT = 1;
        }
        if (checkBoxIncludeMonday.checked == false) {
            checkBoxIncludeMondayT = 0;
        }
        if (checkBoxIncludeTuesday.checked == true){
            checkBoxIncludeTuesdayT = 1;
        }
        if (checkBoxIncludeTuesday.checked == false) {
            checkBoxIncludeTuesdayT = 0;
        }
        if (checkBoxIncludeWednesday.checked == true){
            checkBoxIncludeWednesdayT = 1;
        }
        if (checkBoxIncludeWednesday.checked == false) {
            checkBoxIncludeWednesdayT = 0;
        }
        if (checkBoxIncludeThursday.checked == true){
            checkBoxIncludeThursdayT = 1;
        }
        if (checkBoxIncludeThursday.checked == false) {
            checkBoxIncludeThursdayT = 0;
        }
        if (checkBoxIncludeFriday.checked == true){
            checkBoxIncludeFridayT = 1;
        }
        if (checkBoxIncludeFriday.checked == false) {
            checkBoxIncludeFridayT = 0;
        }
        if (checkBoxIncludeSaturday.checked == true){
            checkBoxIncludeSaturdayT = 1;
        }
        if (checkBoxIncludeSaturday.checked == false) {
            checkBoxIncludeSaturdayT = 0;
        }
        if (checkBoxIncludeSunday.checked == true){
            checkBoxIncludeSundayT = 1;
        }
        if (checkBoxIncludeSunday.checked == false) {
            checkBoxIncludeSundayT = 0;
        }

        var objPolicies = {
            id:idCostDetails,
            settingapplyto_policies: settingApplyTo,
            pickoffdropoff_policies: checkBoxPickOffT,
            crossseasonsrates_policies: crossSeasonsRates,
            infantmin_policies: infantMinAge,
            infantmax_policies: infantMaxAge,
            childmin_policies: childMinAge,            
            childmax_policies: childMaxAge,
            teenmin_policies: teenMinAge,            
            teenmax_policies: teenMaxAge,
            adultmin_policies: adultMinAge,            
            adultmax_policies: adultMaxAge,
            starton_monday_policies: checkBoxStartMondayT,            
            starton_tuesday_policies: checkBoxStartTuesdayT,
            starton_wednesday_policies: checkBoxStartWednesdayT,              
            starton_thursday_policies: checkBoxStartThursdayT,
            starton_friday_policies: checkBoxStartFridayT,            
            starton_saturday_policies: checkBoxStartSaturdayT, 
            starton_sunday_policies: checkBoxStartSundayT,
            mustinclude_monday_policies: checkBoxIncludeMondayT,            
            mustinclude_tuesday_policies: checkBoxIncludeTuesdayT,
            mustinclude_wednesday_policies: checkBoxIncludeWednesdayT,              
            mustinclude_thursday_policies: checkBoxIncludeThursdayT,
            mustinclude_friday_policies: checkBoxIncludeFridayT,            
            mustinclude_saturday_policies: checkBoxIncludeSaturdayT, 
            mustinclude_sunday_policies: checkBoxIncludeSundayT
        };
        const url_update_policies = "php/api/bckoffservices/savepolicies.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url: url_update_policies,
            method: "POST",
            data: objPolicies,
            success: function (data) {                
                callDataNewServiceGrid();
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function (error) {
                console.log('Error ${error}');
            }
        }); 
    });

    /////////////////////////////////////////////////////
    // model --> update Voucher details value to dB /////
    /////////////////////////////////////////////////////
    function voucherCreation() {
        var ele = document.getElementsByName('radioCreationVoucher');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }

    function voucherPrint() {
        var ele = document.getElementsByName('radioPrintVoucher');
        for (i = 0; i < ele.length; i++) {
            if (ele[i].checked)
                var check = ele[i].value;
        }
        return check;
    }

    $("#updateVoucherDetails").click(function () {
        var idCostDetails = document.getElementById('idBlock').innerHTML;
        var addressVoucherDetails = document.getElementById('addressVoucherDetails').value;        
        var countryVoucherDetails = document.getElementById('countryVoucherDetails').value;        
        var stateVoucherDetails = document.getElementById('stateVoucherDetails').value;
        var postCodeVoucherDetails = document.getElementById('postCodeVoucherDetails').value;
        var lineOneVoucherDetails = document.getElementById('line1').value;        
        var lineTwoVoucherDetails = document.getElementById('line2').value;        
        var lineThreeVoucherDetails = document.getElementById('line3').value;
        var lineFourVoucherDetails = document.getElementById('line4').value;
        var radioCreationVoucher = voucherCreation();
        var radioPrintVoucher = voucherPrint();

        var objUpdateVoucherDetailsService = {
            id: idCostDetails,
            address_voucherdetails: addressVoucherDetails,
            country_voucherdetails: countryVoucherDetails,
            state_voucherdetails: stateVoucherDetails,
            postcode_voucherdetails: postCodeVoucherDetails,
            vouchercreation_voucherdetails: radioCreationVoucher,
            printvoucher_voucherdetails: radioPrintVoucher,
            vouchertext1_voucherdetails: lineOneVoucherDetails,
            vouchertext2_voucherdetails: lineTwoVoucherDetails,
            vouchertext3_voucherdetails: lineThreeVoucherDetails,
            vouchertext4_voucherdetails: lineFourVoucherDetails
        };

        const url_update_voucherDetails = "php/api/bckoffservices/savevoucherdetails.php?t=" + encodeURIComponent(global_token);
        $.ajax({
            url: url_update_voucherDetails,
            method: "POST",
            data: objUpdateVoucherDetailsService,
            success: function (data) {
                console.log('value', data);
                callDataNewServiceGrid();
                $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
            },
            error: function (error) {
                console.log('Error ${error}');
            }
        });    
    });
    // End click  

function displayChargePerAdult() {
    var ele = document.getElementsByName('radioChargePer');
    for (i = 0; i < ele.length; i++) {
        if (ele[i].checked)
            var check = ele[i].value;
    }
    return check;
}

$('#btnCounter').click(function (event) {    
    var idCostDetails = document.getElementById('idBlock').innerHTML;
    var chargePer = displayChargePerAdult();
    var extraNameQuoteDetails = document.getElementById('addName').value;        
    var extraDecriptionQuoteDetails = document.getElementById('addDesc').value;     
    var objUpdateQuoteDetails = {
        id: -1,
        idservicesfk: idCostDetails,        
        extraname: extraNameQuoteDetails,        
        extradescription: extraDecriptionQuoteDetails,        
        chargeper: chargePer
    };
    const url_update_quoteDetails = "php/api/bckoffservices/savequotedetails.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_update_quoteDetails,
        method: "POST",
        data: objUpdateQuoteDetails,
        success: function (data) {
            quoteDetailsEditRows(objUpdateQuoteDetails, idCostDetails);
            $('.toast_updated').stop().fadeIn(400).delay(3000).fadeOut(500);
        },
        error: function (error) {
            console.log('Error ${error}');
        }
    });
});
