function test_connection() {

    var timer = null;
    var error_flg = false;
    var flg_msg_is_onscreen = false;

    this.startTest = function ()
    {
        this.timer = setInterval(function () {

            dhtmlxAjax.post("php/test_connection/test_connection.php", "", function (loader) {

                if (loader.xmlDoc.responseURL == "")
                {
                    //no connection to web server

                    if (!flg_msg_is_onscreen)
                    {
                        flg_msg_is_onscreen = true;
                        dhtmlx.message({
                            title: "No Connection",
                            type: "alert-warning",
                            text: "<font color='red'><b>CAREFUL:</b> Connection to Web Server is <b>LOST</b>!</font>",
                            callback: function () {
                                flg_msg_is_onscreen = false;
                            }
                        });
                    }


                    console.log("No Connection to Web Server");

                    error_flg = true;
                } else
                {
                    var jsonObj = loader.xmlDoc.responseText;
                    jsonObj = JSON.parse(loader.xmlDoc.responseText);
                    if (jsonObj.OUTCOME == "ERR_NO_LOG_IN")
                    {
                        if (!flg_msg_is_onscreen)
                        {

                            flg_msg_is_onscreen = true;
                            dhtmlx.message({
                                title: "No Connection",
                                type: "alert-warning",
                                text: "<font color='red'><b>CAREFUL:</b> User <b>NO LONGER</b> Logged In!</font>",
                                callback: function () {
                                    flg_msg_is_onscreen = false;
                                    document.location.reload();
                                }
                            });
                        }

                        console.log("No Longer Logged In");
                        error_flg = true;                        

                    } else if (jsonObj.OUTCOME == "ERR_NO_DB_CONNECTION")
                    {
                       
                        if (!flg_msg_is_onscreen)
                        {
                            flg_msg_is_onscreen = true;
                            dhtmlx.message({
                                title: "No Connection",
                                type: "alert-warning",
                                text: "<font color='red'><b>CAREFUL:</b> Connection to Database Server is <b>LOST</b>!</font>",
                                callback: function () {
                                    flg_msg_is_onscreen = false;
                                    document.location.reload();
                                }
                            });
                        }

                        console.log("No Connection to DB");

                        error_flg = true;
                    } else if (jsonObj.OUTCOME == "OK")
                    {
                        if (error_flg)
                        {
                            dhtmlx.message({
                                text: "<font color='green'>And we are <b>BACK</b>...</font>",
                                expire: 3000,
                                type: "myCss"
                            });

                            console.log("We are back...");
                        }
                        error_flg = false;
                    }
                }
            });
        }, 5000); //5 secs tests
    };

    this.endTest = function ()
    {
        clearInterval(this.timer);
    };
}