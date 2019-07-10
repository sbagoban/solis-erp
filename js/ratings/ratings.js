var ratings_obj = new ratings();

function ratings()
{
    

    document.getElementById("aTitle").innerHTML = "RATINGS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Ratings");

    var grid_ratings = main_layout.cells("a").attachGrid();
    grid_ratings.setIconsPath('libraries/dhtmlx/imgs/');
    grid_ratings.setHeader("Rating,Description");
    grid_ratings.setColumnIds("num_stars,description");
    grid_ratings.setColTypes("ro,ro");
    grid_ratings.setInitWidths("200,*");
    grid_ratings.setColAlign("left,left");
    grid_ratings.setColSorting('str,str');
    grid_ratings.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.setIconSize(32);
    
    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_ratings.clear();
            form_ratings.setItemValue("id", "-1");
                   
            popupwin_ratings.setModal(true);
            popupwin_ratings.center();
            popupwin_ratings.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_ratings.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsRatings.item(uid);
            form_ratings.setFormData(data);

            popupwin_ratings.setModal(true);
            popupwin_ratings.center();
            popupwin_ratings.show();


        } else if (id == "delete")
        {
            var gid = grid_ratings.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Rating",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/ratings/deleterating.php", params, function (loader) {

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DELETE",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }

                                var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

                                if (!json_obj)
                                {
                                    dhtmlx.alert({
                                        text: loader.xmlDoc.responseText,
                                        type: "alert-warning",
                                        title: "DELETE",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    grid_ratings.deleteRow(gid);
                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DELETE",
                                        callback: function () {
                                        }
                                    });
                                }

                            }
                        });
                    }
                }
            });
        }
    });


    var dsRatings = new dhtmlXDataStore();
    dsRatings.load("php/api/ratings/ratinggrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_ratings.sync(dsRatings);

    });


    resizeLayout();


    if (window.attachEvent)
        window.attachEvent("onresize", resizeLayout);
    else
        window.addEventListener("resize", resizeLayout, false);

    var t;
    function resizeLayout() {
        window.clearTimeout(t);
        t = window.setTimeout(function () {

            var x = $("#main_body").parent().width();
            //====================
            var body = document.body,
            html = document.documentElement;
            var y = Math.max( body.scrollHeight, body.offsetHeight, 
                       html.clientHeight, html.scrollHeight, html.offsetHeight );
            y -= 150;

            $("#main_body").height(y - 25);
            $("#main_body").width(x - 20);

            main_layout.setSizes(true);

        }, 1);
    }
    
    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(main_layout.cells("a"));
    
    var popupwin_ratings = dhxWins.createWindow("popupwin_ratings", 50, 50, 500, 250);
    popupwin_ratings.setText("Rating Details:");
    popupwin_ratings.denyResize();
    popupwin_ratings.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_ratings"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "num_stars", label: "Rating:", labelWidth: "130",
            labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
      
        {type: "input", name: "description", label: "Description:", labelWidth: "130",
            labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var ratinglayout = popupwin_ratings.attachLayout("1C");

    ratinglayout.cells("a").hideHeader();

    var form_ratings = ratinglayout.cells("a").attachForm(str_frm_ug);



    form_ratings.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_ratings.setModal(false);
            popupwin_ratings.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_ratings.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Rating",
                    callback: function () {
                    }
                });
                return;
            }
            
           
            ratinglayout.cells("a").progressOn();

            form_ratings.setItemValue("token", global_token);

            form_ratings.send("php/api/ratings/saverating.php", "post", function (loader)
            {
                if (loader)
                {
                    if (loader.xmlDoc.responseURL == "")
                    {
                        dhtmlx.alert({
                            text: "Connection Lost!",
                            type: "alert-warning",
                            title: "SAVE",
                            callback: function () {
                            }
                        });
                        ratinglayout.cells("a").progressOff();
                        return false;
                    }

                    var json_obj = utils_response_extract_jsonobj(loader, false, "", "");


                    if (!json_obj)
                    {
                        dhtmlx.alert({
                            text: loader.xmlDoc.responseText,
                            type: "alert-warning",
                            title: "SAVE",
                            callback: function () {
                            }
                        });
                        ratinglayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsRatings.clearAll();
                        grid_ratings.clearAll();

                        dsRatings.load("php/api/ratings/ratinggrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_ratings.sync(dsRatings);
                            popupwin_ratings.setModal(false);
                            popupwin_ratings.hide();
                            ratinglayout.cells("a").progressOff();

                            grid_ratings.selectRowById(json_obj.ID, false, true, false);
                        });

                    } else
                    {
                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "SAVE",
                            callback: function () {
                            }
                        });
                        ratinglayout.cells("a").progressOff();
                    }
                }
            });
        }
    });

    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {
            if (json_rights[i].PROCESSNAME == "ADD" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("new");
                toolbar.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("modify");
                toolbar.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("delete");
                toolbar.setItemToolTip("delete", "Not Allowed");
            }
        }
    }

    popupwin_ratings.hide();

}