// Use flag 001 to the paxbreak single line values 
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

// Paxbreak Multi price list modal
function multiplePriceCost() { 
    var multiple_price_cost = document.getElementById("multiple_price_cost"); 
    if (multiple_price_cost.checked == true) {
        var multiple_price_chk = 1;        
        document.getElementById("ps_adult_cost").disabled = true;
        document.getElementById("ps_teen_cost").disabled = true;
        document.getElementById("ps_child_cost").disabled = true;
        document.getElementById("ps_infant_cost").disabled = true;

        var flagPaxBreak = pad('1', 3);
        $("#ps_adult_cost").val(flagPaxBreak); 
        $("#ps_teen_cost").val(flagPaxBreak); 
        $("#ps_child_cost").val(flagPaxBreak); 
        $("#ps_infant_cost").val(flagPaxBreak); 
    } else {
        var multiple_price_chk = 0;        
        document.getElementById("ps_adult_cost").disabled = false;
        document.getElementById("ps_teen_cost").disabled = false;
        document.getElementById("ps_child_cost").disabled = false;
        document.getElementById("ps_infant_cost").disabled = false;
        $("#ps_adult_cost").attr("placeholder", "Teen");
        $("#ps_teen_cost").attr("placeholder", "Infant");
        $("#ps_child_cost").attr("placeholder", "Child");
        $("#ps_infant_cost").attr("placeholder", "Adult");
    }
}
