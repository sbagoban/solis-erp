$(document).ready(function(){
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
    $('#updateService').attr('disabled', 'disabled');

        $('.inputValidationCostDetails').click(function() {
            var check = false;
            $('.inputValidationCostDetails').each(function() {
                if ($(this).val() == '') {
                    check = true;
                }
            });
            if (check == true) {
                $('#updateService').attr('disabled', 'disabled');
            } else {
                $('#updateService').removeAttr('disabled');
            }
        });

        $('.inputValidation').click(function() {
            var empty = false;
            $('.inputValidation').each(function() {
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
