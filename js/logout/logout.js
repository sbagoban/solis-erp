function logout_logout()
{

    dhtmlxAjax.post("php/api/token/gentoken.php", "", function (loader) {

        if (utils_response_connected(loader, "Lost Connection to Server", "Logout User"))
        {
            var token = loader.xmlDoc.responseText;
            logout_logmeOut(token);
        }
    });

}

function logout_logmeOut(token)
{

    dhtmlxAjax.post("php/api/logout/logout.php", "token=" + encodeURIComponent(token), function (logout_ajax) {

        if (!utils_response_connected(logout_ajax, "Lost Connection to Server", "Logout User"))
        {
            return;
        }

        var json_obj = utils_response_extract_jsonobj(logout_ajax, true, "Logout User", "");

        if (json_obj)
        {
            if (json_obj.OUTCOME == "OK")
            {
                window.location.reload(true);
            }
        }

    });

}


