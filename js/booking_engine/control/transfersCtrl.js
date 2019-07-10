$('.alert').hide();
$('.submitTranfers').click(function () {
 // console.log('sdfsd',$('.submit').click());
  // set variable that counts how many input fields are left blank
  var emptyInputs = $(this).closest('#transfers_bkeng').find('input').filter(function(){
                        return !$(this).val();
                    }).length;
  // if none of the input fields are left blank
  if (emptyInputs != 0) { 
     // within this panel, find all inputs
    $(this).closest('#transfers_bkeng').find("input").each(function () {
      var element = $(this);
      // if any input is empty  
      if (element.val() == "") {
        // add the has-error class to the .form-group parent div
        $(element).closest('.form-group').addClass('has-error');
        // and show alert box
        $(element).closest('#transfers_bkeng').find('.alert').fadeIn(500).delay(2000);
      } 
    });
  } 
  else {
   // accept data and hide
    $(this).closest('#transfers_bkeng').hide();
  }
});
