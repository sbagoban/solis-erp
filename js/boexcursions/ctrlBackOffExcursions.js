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
        $('.inputValidation').click(function() {
            var empty = false;
            $('.inputValidation').each(function() {
                if ($(this).val() == '') {
                    // set empty to true if ddl is empty
                    empty = true;
                    console.log($(this).val());
                }
                // if (empty == false) {
                //     var check = document.getElementById("chooseLocation");
                //     var check2 = document.getElementById("serviceType");
                //     if (check) {
                //         var element = document.getElementById("chooseLocation");
                //         element.classList.remove("has-warning");
                //     }

                //     if (check2) { 
                //         var element = document.getElementById("serviceType");
                //         element.classList.remove("has-warning");
                //     }
                // }
            });

            if (empty) {
                $('#createNewService').attr('disabled', 'disabled');
            } else {
                $('#createNewService').removeAttr('disabled');
            }
        });
        
});
