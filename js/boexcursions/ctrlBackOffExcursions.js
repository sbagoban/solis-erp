$(document).ready(function(){
    document.getElementById('createNewService').setAttribute('disabled', 'true');

    var ddl = document.getElementById("ddlChooseLocation");
    var selectedValue = ddl.options[ddl.selectedIndex].value;
    console.log(selectedValue);
    if (selectedValue == "selectedCountry") {
        alert("Please select a card type");
    }
});
