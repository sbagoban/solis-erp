// JavaScript Document
/*Date : 2019, 17 October
Application : Client - Booking System
Developer : slouis@solis360.com*/

$(function(){
    
	// Client Section
    $('#client_dob').datepicker(
        {
            format: 'dd/mm/yyyy',
            endDate: '-1d'
        });
	// .Client Section
	$('#client_dob').change(function()
	{
        if ($('#client_dob').val() != '')
        {
            var client_dob = $("#client_dob").val();
            var client_arrival = $("#booking_travelDate").data('daterangepicker').startDate.format('YYYY-MM-DD');

            var dateOfBirth = client_dob.split("/");
            var dateOfBirth_Y = dateOfBirth[2];
            var dateOfBirth_M = dateOfBirth[1];
            var dateOfBirth_D = dateOfBirth[0];
            var dateArrival = client_arrival.split("-");
            var dateArrival_Y = dateArrival[0];
            var dateArrival_M = dateArrival[1];
            var dateArrival_D = dateArrival[2];

            if (dateArrival_Y == dateOfBirth_Y)
                {
                    if (dateArrival_M > dateOfBirth_M)  
                        {
                            var yearMth = "MONTH";
                            if (dateArrival_D == dateOfBirth_D)//
                                {
                                    var age = parseInt(dateArrival_M) - parseInt(dateOfBirth_M);
                                }
                           else if (dateArrival_D > dateOfBirth_D)//
                                {
                                    var age = parseInt(dateArrival_M) - parseInt(dateOfBirth_M);
                                }
                            else //
                                {
                                    var age = parseInt(dateArrival_M) - parseInt(dateOfBirth_M) - 1;
                                }
                        }
                    else if(dateArrival_M == dateOfBirth_M)
                        {
                            var yearMth = "MONTH";
                            var age = parseInt(dateArrival_M) - parseInt(dateOfBirth_M);
                        }
                }
            else if (dateArrival_Y > dateOfBirth_Y)  
                {
                    if (dateArrival_M > dateOfBirth_M) 
                        {
                            var age = parseInt(dateArrival_Y) - parseInt(dateOfBirth_Y) - 1;
                            if (age > 2 && age > 0)
                                {
                                    var yearMth = "YEAR";
                                    var age = age;
                                }
                            else if (age < 3 && age > 0)
                                {
                                    var yearMth = "MONTH";
                                    if (dateArrival_D > dateOfBirth_D)
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                    else if (dateArrival_D == dateOfBirth_D)
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                    else if (dateArrival_D < dateOfBirth_D)
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                }
                            else if (age < 0 || age == 0 )
                                {
                                    var yearMth = "MONTH";
                                    if (dateArrival_D > dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                        }
                                    else if (dateArrival_D == dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                        }
                                    else if (dateArrival_D < dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                        }
                                }
                        }
                    else if (dateArrival_M < dateOfBirth_M)
                        {
                            if (dateArrival_D > dateOfBirth_D)
                                {
                                    var age = parseInt(dateArrival_Y) - parseInt(dateOfBirth_Y) - 1;
                                    if (age > 2 && age > 0)
                                        {
                                            var yearMth = "YEAR";
                                            var age = age;
                                        }
                                    else if (age < 3 && age > 0)
                                        {
                                            var yearMth = "MONTH";
                                            var month = age * 12;
                                            var monthDiFF = 12 + parseInt(dateArrival_M) - parseInt(dateOfBirth_M);
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                        }
                                    else if (age < 0||age == 0) 
                                        {
                                            var yearMth = "MONTH";
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                        }
                                }
                            else if (dateArrival_D < dateOfBirth_D)
                                {
                                    var age = parseInt(dateArrival_Y) - parseInt(dateOfBirth_Y) - 1;
                                    if (age > 2 && age > 0)
                                        {
                                            var yearMth = "YEAR";
                                            var age = age;
                                        }
                                    else if (age < 3 && age > 0)
                                        {
                                            var yearMth = "MONTH";
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                        }
                                    else if (age < 0||age == 0) 
                                        {
                                            var yearMth = "MONTH";
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                        }

                                }
                            else if (dateArrival_D == dateOfBirth_D)
                                {
                                    var age = parseInt(dateArrival_Y) - parseInt(dateOfBirth_Y) - 1;
                                    if (age > 2 && age > 0)
                                        {
                                            var yearMth = "YEAR";
                                            var age = age;
                                        }
                                    else if (age < 3 && age > 0)
                                        {
                                            var yearMth = "MONTH";
                                            var month = age * 12;
                                            var monthDiFF = 12 + parseInt(dateArrival_M) - parseInt(dateOfBirth_M);
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                        }
                                    else if (age < 0||age == 0)
                                        {
                                            var yearMth = "MONTH";
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                        }
                                }
                        }
                    else if (dateArrival_M == dateOfBirth_M)
                        {
                            var age = parseInt(dateArrival_Y) - parseInt(dateOfBirth_Y) - 1;
                            if (age > 2 && age > 0)
                                {
                                    var yearMth = "YEAR";
                                     if (dateArrival_D > dateOfBirth_D) 
                                        {
                                            var age = age + 1;
                                        }
                                    else if (dateArrival_D < dateOfBirth_D) 
                                        {
                                            var age = age;
                                        }
                                    else if (dateArrival_D == dateOfBirth_D) 
                                        {
                                            var age = age +1;
                                        }
                                }
                            else if (age < 3 && age > 0) 
                                {
                                    if (dateArrival_D > dateOfBirth_D) 
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) ;
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                    else if (dateArrival_D == dateOfBirth_D)
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) ;
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                    else if (dateArrival_D < dateOfBirth_D) 
                                        {
                                            var month = age * 12;
                                            var monthDiFF = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                            var age = parseInt(month) + parseInt(monthDiFF);
                                            if (age < 36)
                                                {
                                                     var yearMth = "MONTH";
                                                     var age = age;
                                                }
                                            else
                                                {
                                                     var yearMth = "YEAR";
                                                     var age =  parseInt(age/12) ;
                                                }
                                        }
                                }
                            else if (age < 0||age ==0)
                                {
                                    var yearMth = "MONTH";
                                    if (dateArrival_D > dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) ;
                                        }
                                    else if (dateArrival_D == dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M);
                                        }
                                    else if (dateArrival_D < dateOfBirth_D)
                                        {
                                            var age = (12 - parseInt(dateOfBirth_M)) + parseInt(dateArrival_M) - 1;
                                        }
                                }
                        }
                }
            else if (dateArrival_Y < dateOfBirth_Y)
                {
    console.log("ERROR IN DATE OF BIRTH");				
                }

            $("#client_years").val(age);
            $("#client_yearMonth").val(yearMth);
            $('#client_yearMonth').select2().trigger('change');  
        }
		
	});
	
	$('#client_years').change(function()
	{
		$('#client_dob').val("");
		$("#client_yearMonth").val();
		$('#client_yearMonth').select2().trigger('change');
	});

});

