var coasts_obj = new coasts();

function coasts()
{
    
    document.getElementById("aTitle").innerHTML = "COASTS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");

    main_layout.cells('a').setText("Coasts");

    var grid_coasts = main_layout.cells("a").attachGrid();
    grid_coasts.setIconsPath('libraries/dhtmlx/imgs/');
    grid_coasts.setHeader("Coast");
    grid_coasts.setColumnIds("coast");
    grid_coasts.setColTypes("ro");
    grid_coasts.setInitWidths("*");
    grid_coasts.setColAlign("lef");
    grid_coasts.setColSorting('str');
    grid_coasts.attachHeader("#text_filter");
    grid_coasts.init();


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
            form_coasts.clear();
            form_coasts.setItemValue("id", "-1");
            
            popupwin_coasts.setModal(true);
            popupwin_coasts.center();
            popupwin_coasts.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_coasts.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsCoasts.item(uid);
            form_coasts.setFormData(data);

            popupwin_coasts.setModal(true);
            popupwin_coasts.center();
            popupwin_coasts.show();


        } else if (id == "delete")
        {
            var gid = grid_coasts.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Coast",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/coasts/deletecoast.php", params, function (loader) {

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
                                    grid_coasts.deleteRow(gid);
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


    var dsCoasts = new dhtmlXDataStore();
    dsCoasts.load("php/api/coasts/coastgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_coasts.sync(dsCoasts);

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

    var popupwin_coasts = dhxWins.createWindow("popupwin_coasts", 50, 50, 500, 180);
    popupwin_coasts.setText("Coast Details:");
    popupwin_coasts.denyResize();
    popupwin_coasts.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_coasts"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "coast", label: "Coast:", labelWidth: "80",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var coastlayout = popupwin_coasts.attachLayout("1C");

    coastlayout.cells("a").hideHeader();

    var form_coasts = coastlayout.cells("a").attachForm(str_frm_ug);



    form_coasts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_coasts.setModal(false);
            popupwin_coasts.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_coasts.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Coast",
                    callback: function () {
                    }
                });
                return;
            }


            coastlayout.cells("a").progressOn();

            form_coasts.setItemValue("token", global_token);

            form_coasts.send("php/api/coasts/savecoast.php", "post", function (loader)
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
                        coastlayout.cells("a").progressOff();
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
                        coastlayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsCoasts.clearAll();
                        grid_coasts.clearAll();

                        dsCoasts.load("php/api/coasts/coastgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_coasts.sync(dsCoasts);
                            popupwin_coasts.setModal(false);
                            popupwin_coasts.hide();
                            coastlayout.cells("a").progressOff();

                            grid_coasts.selectRowById(json_obj.ID, false, true, false);
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
                        coastlayout.cells("a").progressOff();
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



    popupwin_coasts.hide();

}