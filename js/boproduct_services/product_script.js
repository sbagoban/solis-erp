
$('#tbl-product, #tbl-productServices').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false
});

//Date picker
$('#valid_from').datepicker({		
    autoclose: true,
    format: 'yyyy-mm-dd'
});
$('#valid_to').datepicker({		
    autoclose: true,
    format: 'yyyy-mm-dd'
});
$('.select2').select2();

$('#duration').durationPicker({
    showDays : false
});

/////////////////////////////////////////
// model --> fetch Api Currency sell ////
/////////////////////////////////////////
const url_currency_buy = "php/api/backofficeproduct/currency_combo_rates.php?t=" + encodeURIComponent(global_token); 

$.ajax({
    url : url_currency_buy,
    type : "GET",
    success : function(data) {
        helpersDropdownCurrency.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_currency'),
            'Select an option'
        );
    }, 
    error:function(error) {
        console.log('Error ${error}');
    }
});
var helpersDropdownCurrency = {
    buildDropdown: function(result, dropdown, emptyMessage) {
        // Remove current options
        dropdown.html('');
        // Add the empty option with the empty message
        dropdown.append('<option value="">' + emptyMessage + '</option>');
        // Check result isnt empty
        if(result != '') {
            // Loop through each of the results and append the option to the dropdown
            $.each(result, function(data, result) {
                dropdown.append('<option value="' + result.value + '"name="' + result.text + '">' + result.text + '</option>');
            });
        }
    }
}



/////////////////////////////////////////
// model --> fetch Api Service //////////
/////////////////////////////////////////
const url_supplier = "php/api/backofficeproduct/combocreditor.php?t=" + encodeURIComponent(global_token); 

$.ajax({
    url : url_supplier,
    type : "GET",
    success : function(data) {
        helpersDropdownSupplier.buildDropdown(
            jQuery.parseJSON(data),
            $('#id_creditor'),
            'Select an option'
        );
    }, 
    error:function(error) {
        console.log('Error ${error}');
    }
});
var helpersDropdownSupplier = {
    buildDropdown: function(result, dropdown, emptyMessage) {
        // Remove current options
        dropdown.html('');
        // Add the empty option with the empty message
        dropdown.append('<option value="">' + emptyMessage + '</option>');
        // Check result isnt empty
        if(result != '') {
            // Loop through each of the results and append the option to the dropdown
            $.each(result, function(data, result) {
                dropdown.append('<option value="' + result.id_creditor + '">' + result.creditor_name + '</option>');
            });
        }
    }
}

$(document).ready(function(){
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");

    applyFor();
    if (servicetype == "TRANSFER") {
        $("#id_coast_label").css("display", "none");
        $("#id_coast").css("display", "none");        
        $("#id_service_1").css("display", "none");
        $("#id_service_2").css("display", "block");
        $("#duration1").css("display", "none");
        $("#duration2").css("display", "none");
        $("#duration_label").css("display", "none");
        $("#chk_operation").css("display", "none");
        $("#ageActivity").css("display", "none");
        $("#adultActivity").css("display", "none");
        $("#infantActivity").css("display", "none");
        $("#childActivity").css("display", "none");
        $("#id_creditor_blk").css("display", "none");
        $("#special_name_all").css("display", "none");
        $("#special_name_transfer_blk").css("display", "block");
        $("#id_tax_blk").css("display", "none");
        $("#age_inf_from").val('0');
        $("#age_inf_to").val('2');
        $("#age_child_from").val('3');
        $("#age_child_to").val('12');
        $("#is_package_blk").css("display", "none");
        $("#teenActivity").css("display", "none");
        $("#applyForLabel").css("display", "none");
        $("#blckAgePolicy").css("display", "none");
    
        $( "#service_name_transfer" ).change(function () {
            $( "#service_name_transfer option:selected" ).each(function() {
                service_name = $( this ).text();
                if (service_name == "SOUTH EAST" || service_name == "OTHER COAST") {
                    $('#special_name_transfer').css('display', 'block');  
                    $('#special_name_transfer').val('Airport');
                    $("#special_name_transfer option[value='Drop on']").hide();
                    $("#special_name_transfer option[value='Drop Off']").hide();
                    $("#special_name_transfer option[value='Full Day']").hide();
                    $("#special_name_transfer option[value='Half Day']").hide();
                    $("#special_name_transfer option[value='Night Tour']").hide();
                    $("#special_name_transfer option[value='Airport']").show();
                    $("#special_name_transfer option[value='Port']").show();
                } else if (service_name == "INTER HOTEL") {                     
                    $('#special_name_transfer').css('display', 'none');
                } else if (service_name == "ACTIVITY") {        
                    $('#special_name_transfer').css('display', 'block');              
                    $("#special_name_transfer option[value='Airport']").hide();
                    $("#special_name_transfer option[value='Port']").hide();                    
                    $("#special_name_transfer option[value='Drop on']").show();
                    $("#special_name_transfer option[value='Drop Off']").show();
                    $("#special_name_transfer option[value='Full Day']").show();
                    $("#special_name_transfer option[value='Half Day']").show();
                    $("#special_name_transfer option[value='Night Tour']").show();
                    $('#special_name_transfer').val('Select an Option');
                }
            });
        }).change();

    } if(servicetype == "ACTIVITY" || servicetype == "OTHER") {
        $("#id_creditor_blk").css("display", "block");
        $("#id_tax_blk").css("display", "block");
        $("#special_name_all").css("display", "block");
        $("#special_name_transfer_blk").css("display", "none");
        var for_adult = urlParams.get("for_adult");
        var for_child = urlParams.get("for_child");
        var for_infant = urlParams.get("for_infant");
        var for_teen = urlParams.get("for_teen");

        if (for_infant > 0) { 
            $("#ps_infant_cost").css("display", "block");
            $("#ps_infant_cost").attr("placeholder", "Infant");
        } if (for_infant <= 0) { 
            $("#ps_infant_cost_addon").css("display", "none");
            $("#ps_infant_cost").css("display", "none");
        }

        if (for_teen > 0) { 
            $("#ps_teen_cost").css("display", "block");
            $("#ps_teen_cost").attr("placeholder", "Teen");
        } if (for_teen <= 0) { 
            $("#ps_teen_cost_addon").css("display", "none");
            $("#ps_teen_cost").css("display", "none");
        }

        if (for_child > 0) { 
            $("#ps_child_cost").css("display", "block");
            $("#ps_child_cost").attr("placeholder", "Child");
        } if (for_child <= 0) { 
            $("#ps_child_cost_addon").css("display", "none");
            $("#ps_child_cost").css("display", "none");
        }

        if (for_adult > 0) { 
            $("#ps_adult_cost").css("display", "block");
            $("#ps_adult_cost").attr("placeholder", "Adult");
        } if (for_adult <= 0) { 
            $("#ps_adult_cost_addon").css("display", "none");
            $("#ps_adult_cost").css("display", "none");
        }
    }
});


function applyFor() {
    var chkinfant = document.getElementById("for_infant");
    var chkchild = document.getElementById("for_child");
    var chkteen = document.getElementById("for_teen");
    var chkadult = document.getElementById("for_adult");

    $('input').on('click',function () {
        if (chkinfant.checked) {
            $("#age_inf_from").prop("readonly", false);
            $("#age_inf_to").prop("readonly", false);
        } if (chkinfant.checked == false) {
            $("#age_inf_from").prop("readonly", true);
            $("#age_inf_to").prop("readonly", true);
        }

        if (chkchild.checked) {
            $("#age_child_from").prop("readonly", false);
            $("#age_child_to").prop("readonly", false);
        } if (chkchild.checked == false) {
            $("#age_child_from").prop("readonly", true);
            $("#age_child_to").prop("readonly", true);
        }

        if (chkteen.checked) {
            $("#age_teen_from").prop("readonly", false);
            $("#age_teen_to").prop("readonly", false);
        } if (chkteen.checked == false) {
            $("#age_teen_from").prop("readonly", true);
            $("#age_teen_to").prop("readonly", true);
        }

        if (chkadult.checked && chkteen.checked == false && chkchild.checked == false && chkinfant.checked == false) {
            $('#min_age').css("border", "2px solid orange");
            $('#max_age').css("border", "2px solid orange");

            var minage = Number ($('#min_age').val());
            var maxage = Number ($('#max_age').val());
            if (minage == '' || maxage == '') {
                $('#btn-saveProductServices').attr('disabled', true); 
            }
        }
        if (chkadult.checked == false || chkteen.checked || chkchild.checked || chkinfant.checked) {
            $('#min_age').css("border", "1px solid black");
            $('#max_age').css("border", "1px solid black");
        }
    });
}

$('#max_age').change(function(){
    var minage = Number ($('#min_age').val());
    var maxage = Number ($('#max_age').val());
    if (minage > maxage){        
        $('#max_age').css("border", "2px solid orange");
        alert ('Choose a number greater than ' + minage );
    }
});

$('#min_age').change(function(){
    var minage = Number ($('#min_age').val());
    if (minage != ''){
        $('#btn-saveProductServices').attr('disabled', false); 
    }
});