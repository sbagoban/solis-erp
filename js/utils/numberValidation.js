// JavaScript Document
/*Date : 2019, 22 November
Function: Work with text filed value to accept number only
Developer : slouis@solis360.com*/
$(".numberNoDecimal").on("keypress keyup blur",function (event) 
{    
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) 
    {
        event.preventDefault();
    }
});

 $(".numberWithDecimal").on("keypress keyup blur",function (event) 
{
    $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) 
    {
        event.preventDefault();
    }
});
