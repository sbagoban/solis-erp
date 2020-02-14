$(document).ready(function () {
    document.getElementById("aTitle").innerHTML = "";
    productCtrl();
    // $('#btn-saveProductServices').attr('disabled', 'disabled');
    $("#age_inf_from").prop("readonly", true);
    $("#age_inf_to").prop("readonly", true);
    $("#age_child_from").prop("readonly", true);
    $("#age_child_to").prop("readonly", true);
    $("#age_teen_from").prop("readonly", true);
    $("#age_teen_to").prop("readonly", true);
    $("#min_age").prop("readonly", true);
    $("#max_age").prop("readonly", true);

    $('#closure_date').daterangepicker({
		"showDropdowns": true,
		"autoApply": true,
		"opens": "center",
		locale: {
					format: 'DD/MM/YYYY'
				}
    });

    // Validation For On approved an on_api
    var on_approved_chk = $('#on_approved').prop('checked');
    var on_api_chk = $('#on_api').prop('checked');

    $('#on_approved').on('change', function() {
        var liveOnChange = $(this).prop('checked');
        if(liveOnChange == true) {
            $('#on_api').removeAttr("disabled");
            $('.toggle').removeAttr("disabled");
            $(".toggle:eq(1)").removeClass("add_disabled");
        } else {
            $('.toggle').prop('disabled', "disabled");
            $('#on_api').prop("disabled", true);
            $('.toggle:eq(1)').addClass('btn-default off').removeClass('btn-success');
            $('#on_api').prop('checked', false);
            $(".toggle:eq(1)").addClass("add_disabled");
        }
    });
});

function productCtrl() {
    // Disabled button by default
    $('#btnSaveProduct').attr('disabled', 'disabled');
    $('#productName').change(function(){
        if($(this).val().length !=0)
            $('#btnSaveProduct').attr('disabled', false);            
        else
            $('#btnSaveProduct').attr('disabled',true);
    }); 
    
}

function onkeyupCtrl() {
    //var service_name = document.getElementById("service_name").value;
    var id_creditor = document.getElementById("id_creditor").value;
    
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    // var servicetype = urlParams.get("servicetype"); 
    // if (servicetype == 'ACTIVITY') {
    //     if(service_name.length > 0) {
    //         $('#btn-saveProductServices').attr('disabled', false); 
    //     }          
    //     else {
    //         $('#btn-saveProductServices').attr('disabled',true);
    //     }
    // } else if (servicetype == 'TRANSFER') {
    //     $('#btn-saveProductServices').attr('disabled', false);
    // }
}

$('#btn-saveProductServicesCost').click(function (e) {
    var isValid = true;
    $('#ps_adult_cost,#ps_teen_cost,#ps_child_cost,#ps_infant_cost, #daterangeServiceFromTo, #id_currency').each(function () {
        if ($.trim($(this).val()) == '') {
            isValid = false;
            $(this).css({
                "border": "1px solid red",
                "background": "#FFCECE"
            });
        } 
        else {
            $(this).css({
                "border": "",
                "background": ""
            });
        }


    });
    if (isValid == false) {
        e.preventDefault();
    }

    // Control save cost - check if url send value for_infant, for_teen, for_adult, for_child
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var for_adult_url = urlParams.get("for_adult");
    var for_child_url = urlParams.get("for_child");
    var for_infant_url = urlParams.get("for_infant");
    var for_teen_url = urlParams.get("for_teen");
    if (for_adult_url == 0) { 
        $('#ps_adult_cost').val(0);
    } else if (for_child_url == 0) {
        $('#ps_child_cost').val(0);
    } else if (for_infant_url == 0) {
        $('#ps_infant_cost').val(0);
    } else if (for_teen_url == 0) {
        $('#ps_teen_cost').val(0);
    }
    saveCost();
});

$( "#ddlType" ).change(function () {
    $( "#ddlType option:selected" ).each(function() {
        service_name = $( this ).text();
        if (service_name == "Others" || service_name == "Transfer") {
            console.log('OK');
            $('#ddlProductType').val('4');
            $("#ddlProductType option[value='1']").hide();
            $("#ddlProductType option[value='2']").hide();
            $("#ddlProductType option[value='3']").hide();
        } else { 
            $("#ddlProductType option[value='1']").show();
            $("#ddlProductType option[value='2']").show();
            $("#ddlProductType option[value='3']").show();
            $('#ddlProductType').val('1');
        } 
    });
}).change();

$('#btn-saveProductServices').click(function () {
    var allParams = window.location.href.split('data=').pop();
    const urlParams = new URLSearchParams(allParams);
    var servicetype = urlParams.get("servicetype");

    var service_name = $('#service_name').val();
    var id_creditor = $('#id_creditor').val();
    var id_coast = $('#id_coast').val();
    var id_dept = $('#id_dept').val();
    var id_country = $('#id_country').val();
    var id_tax = $('#id_tax').val();
    var charge = $('#charge').val();
    var is_pakage = $('#is_pakage').val();
    var countPackage = $("#services_cost :selected").length;
    var service_name_transfer = $('#service_name_transfer').val();
    var special_name_transfer = $('#special_name_transfer').val();

    if (servicetype == "ACTIVITY" || servicetype =="OTHER") { 
        if (is_pakage == "Y" && countPackage <= 1) {
            alert("Please Choose atleast two services");
        } 
        
        else if (charge == null || charge == "") {
            alert("Please choose a Charge Pax / Unit");
            document.getElementById('id_tax').style.border ="1px solid #aaa";
            document.getElementById('charge').style.border ="1px solid red";
        } 
        
        else if (id_dept == null || id_dept == "") {
            alert("Please choose a Department");
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_dept').style.border ="1px solid red";
        } 
        
        else if (id_country == null || id_country == "") {
            alert("Please choose a Location");
            document.getElementById('id_dept').style.border ="1px solid #aaa";
            document.getElementById('id_country').style.border ="1px solid red";
        }
        
        else if (service_name == "") {
            document.getElementById('service_name').style.border ="1px solid red";
            alert("Please add a service name");
        } 

        else if (service_name.trim() == "OTHER COAST") {
            alert("Please Note that 'OTHER COAST' is a reserved word.");
        } 

        else if (service_name.trim() == "INTER HOTEL") {
            alert("Please Note that 'INTER HOTEL' is a reserved word.");
        } 

        else if (service_name.trim() == "SOUTH EAST") {
            alert("Please Note that 'SOUTH EAST' is a reserved word.");
        } 
        
        else if (id_creditor == "") {
            alert("Please choose a supplier");
            document.getElementById('service_name').style.border ="1px solid #aaa";
            document.getElementById('id_creditor').style.border ="1px solid red";
        } 
        
        else if (id_coast == null || id_coast == "") {
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_coast').style.border ="1px solid red";
            alert("Please choose a coast");
        } 
        
        else if (id_tax == null || id_tax == "") {
            alert("Please choose Tax Option");
            document.getElementById('id_country').style.border ="1px solid #aaa";
            document.getElementById('id_tax').style.border ="1px solid red";
        } 
        
        else if ($('.requiredChkDate:checked').length == 0) {
            alert("Please choose an operation day");
        }  
        
        else if ($('.requiredChkApplyFor:checked').length == 0) {
            alert("Please Apply atleast one type of Pax");
        } 
        
        else if ($('.requiredChkApplyFor:checked').length > 0) {
            validateAgePolicy();
        } 
        
        else {
            saveService();
        }
    } 
    
    if (servicetype == "TRANSFER") {
        var isVisible = document.getElementById("special_name_transfer").style.display == "block";
        var min_pax = document.getElementById("min_pax").value;
        var max_pax = document.getElementById("max_pax").value;
        
        if (charge == null || charge == "") {
            alert("Please choose a Charge Pax / Unit");
            document.getElementById('id_tax').style.border ="1px solid #aaa";
            document.getElementById('charge').style.border ="1px solid red";
        } 
        
        else if (id_dept == null || id_dept == "") {
            alert("Please choose a Department");
            document.getElementById('id_creditor').style.border ="1px solid #aaa";
            document.getElementById('id_dept').style.border ="1px solid red";
        } 
        
        else if (id_country == null || id_country == "") {
            alert("Please choose a Location");
            document.getElementById('id_dept').style.border ="1px solid #aaa";
            document.getElementById('id_country').style.border ="1px solid red";
        }
        
        else if (service_name_transfer == null || service_name_transfer == "") {
            alert("Please select a service name");
            document.getElementById('service_name_transfer').style.border ="1px solid red";
        } 
        
        else if (((special_name_transfer == null) || (special_name_transfer == "")) && (isVisible == true)) {
            alert("Please select a special name");
            document.getElementById('service_name_transfer').style.border ="1px solid #aaa";
            document.getElementById('service_name_transfer').style.border ="1px solid red";
        } 
            
        else if (min_pax == 0 || min_pax == null) {
                alert('Please Fill in the age "Min Pax" for Adult.');
        }   
        
        else if (max_pax == 0 || max_pax == null) {
                alert('Please Fill in the age "Max Pax" for Adult.');
        }
            
        else {
            saveService();
        }
    } 
}); 

function validateAgePolicy() {
    var chkinfant = document.getElementById("for_infant");
    var chkchild = document.getElementById("for_child");
    var chkteen = document.getElementById("for_teen");
    var chkadult = document.getElementById("for_adult");

    var age_inf_to = document.getElementById("age_inf_to").value;
    var age_inf_from = document.getElementById("age_inf_from").value;
    var age_child_to = document.getElementById("age_child_to").value;

    var age_teen_from = document.getElementById("age_teen_from").value;
    var age_teen_to = document.getElementById("age_teen_to").value;

    var min_age = document.getElementById("min_age").valueAsNumber;
    var max_age_product = document.getElementById("max_age").valueAsNumber;

    var inf_age = $('#age_inf_to').val();
    var child_age_chk = ++inf_age;

    var child_age = $('#age_child_to').val();
    var teen_age_chk = ++child_age;
    
    var age_teen_to_chk = document.getElementById("age_teen_to").valueAsNumber;
    var age_teen_from_chk = document.getElementById("age_teen_from").valueAsNumber;
    
    var age_child_to_chk = document.getElementById("age_child_to").valueAsNumber;
    var age_child_from_chk = document.getElementById("age_child_from").valueAsNumber;

    console.log(min_age > max_age_product);

    if (chkinfant.checked && (age_inf_to == 0 || age_inf_to == null)) {
            alert('Please Fill in the age "to" for infant.');
    }   

    else if (chkchild.checked && (age_child_to == 0 || age_child_to == null)) {
            alert('Please Fill in the age "to" for child.');
    }   
    
    else if (chkteen.checked && (age_teen_to == 0 || age_teen_to == null)) {
            alert('Please Fill in the age "to" for teen.');
    }   
    
    else if (chkteen.checked && (age_teen_from == 0 || age_teen_from == null)) {
            alert('Please Fill in the age "from" for teen.');
    }   
    
    // else if (chkadult.checked && (min_age == 0 || min_age == null)) {
    //         alert('Please Fill in the age "Min Age" for Adult.');
    // }   
    
    // else if (chkadult.checked && (max_age_product == 0 || max_age_product == null|| max_age_product === '0')) {
    //         alert('Please Fill in the age "Max Age" for Adult.');
    // }
    
    else if (($('#age_child_from').val()) != child_age_chk && (chkchild.checked) && (chkinfant.checked)) {
            alert('Child age "From" should be : ' + child_age_chk);
    }   
    
    else if (($('#age_teen_from').val()) != teen_age_chk && (chkteen.checked) && (chkchild.checked)) {
            alert('Teen age "From" should be : ' + teen_age_chk);
    }   
    
    else if (min_age > max_age_product) { 
        alert('Max Adult age should be greater than Min Age');
    }

    else if (chkinfant.checked &&  chkadult.checked &&  (age_inf_from > max_age_product)) { 
        alert('Please note that Infant age "From" should be less than Max Age');
    }

    else if (chkinfant.checked && chkadult.checked && (age_inf_to > max_age_product)) { 
        alert('Please note that Infant age "To" should be less than Max Age');
    }

    else if (chkchild.checked &&  chkadult.checked &&  (age_child_from > max_age_product)) { 
        alert('Please note that Child age "From" should be less than Max Age');
    }

    
    else if (chkteen.checked &&  chkadult.checked &&  (age_teen_to > max_age_product)) { 
        alert('Please note that Teen age "To" should be less than Max Age');
    }

    else if (chkteen.checked &&  chkadult.checked &&  (age_teen_from > max_age_product)) { 
        alert('Please note that Teen age "From" should be less than Max Age');
    }

    else if (($('#age_inf_from').val()) > ($('#age_inf_to').val())) { 
        alert('Infant "To" age should be greater than Infant "From" Age');
    }

    else if (age_child_from_chk > age_child_to_chk) { 
        alert('Child "To" age should be greater than Child "From" Age');
    }

    else if (age_teen_from_chk > age_teen_to_chk) {
        alert('Teen "To" age should be greater than Teen "From" Age');
    }

    else if (!chkadult.checked) {
        if(chkinfant.checked) {
            $('#max_age').val(age_inf_to);
            $('#min_age').val(age_inf_from);
            saveService();
        } 
        if (chkchild.checked) {
            $('#max_age').val(age_child_to);
            $('#min_age').val(age_child_from);
            saveService();
        }
        if (chkteen.checked) {
            $('#max_age').val(age_teen_to);
            $('#min_age').val(age_teen_from);
            saveService();
        } 
        // else {
        // }
    }
    
    else {
        saveService();
    }
}