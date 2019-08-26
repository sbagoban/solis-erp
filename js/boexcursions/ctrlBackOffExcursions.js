$(document).ready(function () {
            // Disabled button by default    
            var element = document.getElementById("chooseLocation");
            element.classList.add("has-warning");

            var element = document.getElementById("serviceType");
            element.classList.add("has-warning");

            var element = document.getElementById("supplier");
            element.classList.add("has-warning");

            var element = document.getElementById("optionCode");
            element.classList.add("has-warning");

            $('#createNewService').attr('disabled', 'disabled');
            //$('#updateService').attr('disabled', 'disabled');

            $('.inputValidationCostDetails').click(function () {
                var check = false;
                $('.inputValidationCostDetails').each(function () {
                    if ($(this).val() == '') {
                        check = true;
                    }
                });
                // if (check == true) {
                //     $('#updateService').attr('disabled', 'disabled');
                // } else {
                //     $('#updateService').removeAttr('disabled');
                // }
            });

            $('.inputValidation').click(function () {
                var empty = false;
                $('.inputValidation').each(function () {
                    if ($(this).val() == '') {
                        // set empty to true if ddl is empty
                        empty = true;
                        console.log($(this).val());
                    }
                });
                if (empty == true) {
                    $('#createNewService').attr('disabled', 'disabled');
                } else {
                    $('#createNewService').removeAttr('disabled');
                }
            });
});

// function validateQuoteDetailsForm() {
//     var a = document.forms["Form"]["addName"].value;
//     var b = document.forms["Form"]["addDesc"].value;
//     if (a == null || a == "", b == null || b == "") {
//         alert("Please Fill All Required Field");
//         return false;
//     } else {        
//         $('#btnCounter').removeAttr('disabled');
//         addNewExtraService();
//     }


//     // $('.tax-wrap input').blur(function () {
//     //     if (!$(this).val()) {
//     //         $('.hide-tax').show('slow');
//     //     }
//     // });

//     // // ExtraService Form Validation -
//     // $(".addMain input").focus(function () {

//     //     //return false;
//     // });


//     // $('.addPrefer input').blur(function () {
//     //     if (!$(this).val()) {

//     //     }
//     // });
// }