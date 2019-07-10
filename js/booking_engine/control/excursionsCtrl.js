$(function () {
  $('.alert').hide();
  $('.submitExcursions').click(function () {
    // set variable that counts how many input fields are left blank
    var emptyInputs = $(this).closest('#excursions_bkeng').find('input').filter(function(){
                          return !$(this).val();
                      }).length;
    // if none of the input fields are left blank
    if (emptyInputs != 0) { 
       // within this panel, find all inputs
      $(this).closest('#excursions_bkeng').find("input").each(function () {
        var element = $(this);
        // if any input is empty  
        if (element.val() == "") {
          // add the has-error class to the .form-group parent div
          $(element).closest('.form-group').addClass('has-error');
          // and show alert box
          $(element).closest('#excursions_bkeng').find('.alert').fadeIn(500).delay(2000);
        } 
      });
    } 
    else {
     // accept data and hide
      $(this).closest('#excursions_bkeng').hide();
    }
  });

    // Fucntion to display modal based on quantity of children
    $("#ddlChildren").change(function () {
        $("#newFields").empty();
        $('#btnsave').prop('disabled', true);
        $("#myModalNumberChildren").modal({
              backdrop: 'static',
              keyboard: false
        });

        var newFieldsChildrenAge = $(this).val();
        var newFields = $('');

        var removeField = newFields.slice(newFieldsChildrenAge).remove();
        newFields = newFields.not(removeField);

        // Check if Children selected is greater than > 0 
        if ($(this).val() < 0) {
          $("#myModalNumberChildren").modal("hide");
        } 
        else {
          $("#myModalNumberChildren").modal("show");
            // Loop in array to check the number of children selected.
            for (var i = newFieldsChildrenAge.length; i < newFieldsChildrenAge; i++) {
              console.log('check this', newFieldsChildrenAge);
              var input = $('<input type="number" id="childrenAge" min="0" max="17" class="form-control" placeholder="Age"><br>');
              var newInput = input.clone();
              newFields = newFields.add(input);
              input.appendTo("#newFields");
            }

            $('#childrenAge').keyup(function () {
                if ($(this).val() == '') {
                    $('#btnSaveChildrenAge').prop('disabled', true);
                } else {
                    $('#btnSaveChildrenAge').prop('disabled', false);
                }
            });

            // If Button quit is checked witout filling the Ages - Set DropDown List Children to 0
            $('#btnQuit').on("click", function (){
              var ddlChildenValue = document.getElementById('ddlChildren');
              ddlChildenValue.value = 0;
            });
        }
    });

    $('#excursionCheckboxResult :checkbox').change(function(event) {
      // this will contain a reference to the checkbox   
      if (this.checked) {
        console.log('sdfsd', $(this).val());
          // the checkbox is now checked 
      } else {
          // the checkbox is now no longer checked
      }
    });
});