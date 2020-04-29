$('#btn-saveAccom').click(function()	{
    const url_send_mail = "php/api/phpMailer/bookingSendMail.php?t=" + encodeURIComponent(global_token);
    $.ajax({
        url: url_send_mail,
        method: "POST",
        dataType: "json",
        success: function (clientData) {
            console.log("Mail Sent", clientData);
        }, error: function (error) {
            console.log('Error ${error}');
        }
    });
});