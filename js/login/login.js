$(function () {



    $('button[type="submit"]').click(function (e) {
        e.preventDefault();

        var uname = $("input[name=uname]");
        var pwd = $("input[name=pwd]");

        uname = utils_trim(uname.val(), " ");
        pwd = utils_trim(pwd.val(), " ");


        if (uname == "" || pwd == "")
        {
            $("#output").removeClass('alert alert-success');
            $("#output").addClass("alert alert-danger animated fadeInUp").html("Missing username or password!");

            return;
        } else
        {
            //validate email
            if (!utils_validateEmail(uname))
            {
                $("#output").removeClass('alert alert-success');
                $("#output").addClass("alert alert-danger animated fadeInUp").html("Invalid email address!");
                return;
            }

            dhtmlxAjax.post("php/api/token/gentoken.php", "", function (loader) {

                if (utils_response_connected(loader, "Lost Connection to Server", "Authenticate User"))
                {
                    var token = loader.xmlDoc.responseText;
                    login_logmeIn(uname, pwd, token);
                } else
                {
                    $('button[type="submit"]').prop('disabled', false);
                    $('button[type="submit"]').html("Sign In");
                }
            });


            //change button text 
            $('button[type="submit"]').prop('disabled', true);
            $('button[type="submit"]').html("Authenticating...");
        }
    });
});

function login_logmeIn(u, p, t)
{
    var url = "php/api/login/authenticate.php";

    var params = "email=" + encodeURIComponent(u) +
            "&password=" + encodeURIComponent(md5(p)) +
            "&token=" + encodeURIComponent(t);


    dhtmlxAjax.post(url, params, function (loader) {

        if (!utils_response_connected(loader, "Lost Connection to Server", "Authenticate User"))
        {
            $('button[type="submit"]').prop('disabled', false);
            $('button[type="submit"]').html("Sign In");

            return;
        }

        var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

        if (!json_obj)
        {
            $("#output").removeClass('alert alert-success');
            $("#output").addClass("alert alert-danger animated fadeInUp").html("There is something wrong here!");

            $('button[type="submit"]').prop('disabled', false);
            $('button[type="submit"]').html("Sign In");

            return;
        }
        
        if (json_obj.OUTCOME == "OK")
        {
            //successful login
            //stamp datetime
            $('button[type="submit"]').html("Loading...");
            
            var url = "php/api/login/stamp_details.php";

            var params = "id=" + encodeURIComponent(json_obj.UID) +
                         "&t=" + encodeURIComponent(t);


            dhtmlxAjax.post(url, params, function (loader) {
                
            });
            
            //redirect index.php
            window.location.reload(true);
        } 
        else if(json_obj.OUTCOME == "Already Logged In")
        {
            window.location.reload(true);
        }
        else
        {
            $("#output").removeClass('alert alert-success');
            $("#output").addClass("alert alert-danger animated fadeInUp").html(json_obj.OUTCOME);

            //invalid login
            $('button[type="submit"]').prop('disabled', false);
            $('button[type="submit"]').html("Sign In");
        }
    });

}
