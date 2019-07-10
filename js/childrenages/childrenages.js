var childrenages_obj = new childrenages();

function childrenages()
{


    document.getElementById("aTitle").innerHTML = "CHILDREN AGE";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("ChildrenAges");

    var grid_childrenages = main_layout.cells("a").attachGrid();
    grid_childrenages.setIconsPath('libraries/dhtmlx/imgs/');
    grid_childrenages.setHeader("From,To");
    grid_childrenages.setColumnIds("agefrom,ageto");
    grid_childrenages.setColTypes("ro,ro");
    grid_childrenages.setInitWidths("250,250");
    grid_childrenages.setColAlign("center,center");
    grid_childrenages.setColSorting('int,int');
    grid_childrenages.attachHeader("#text_filter,#text_filter");
    grid_childrenages.init();


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
            form_childrenages.clear();
            form_childrenages.setItemValue("id", "-1");

            popupwin_childrenages.setModal(true);
            popupwin_childrenages.center();
            popupwin_childrenages.show();


        } else if (id == "modify")
        {
            var uid = grid_childrenages.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsChildrenAges.item(uid);
            form_childrenages.setFormData(data);

            popupwin_childrenages.setModal(true);
            popupwin_childrenages.center();
            popupwin_childrenages.show();


        } else if (id == "delete")
        {
            var gid = grid_childrenages.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Airport",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/childrenages/deletechildrenage.php", params, function (loader) {

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
                                    grid_childrenages.deleteRow(gid);
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


    var dsChildrenAges = new dhtmlXDataStore();
    dsChildrenAges.load("php/api/childrenages/childrenagegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_childrenages.sync(dsChildrenAges);

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

    var popupwin_childrenages = dhxWins.createWindow("popupwin_childrenages", 50, 50, 500, 240);
    popupwin_childrenages.setText("Age Details:");
    popupwin_childrenages.denyResize();
    popupwin_childrenages.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_childrenages"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "agefrom", label: "Age From (0-17):", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidInteger"
        },
        {type: "input", name: "ageto", label: "Age To (0-17):", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidInteger"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var childrenageslayout = popupwin_childrenages.attachLayout("1C");

    childrenageslayout.cells("a").hideHeader();

    var form_childrenages = childrenageslayout.cells("a").attachForm(str_frm_ug);



    form_childrenages.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_childrenages.setModal(false);
            popupwin_childrenages.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_childrenages.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Ages",
                    callback: function () {
                    }
                });
                return;
            }

            //validate ages
            var from = form_childrenages.getItemValue("agefrom");
            var to = form_childrenages.getItemValue("ageto");
            
            if(!Between0_17(from) || !Between0_17(to))
            {
                dhtmlx.alert({
                    text: "Age From and To must be numeric and between 0-17!",
                    type: "alert-warning",
                    title: "Save Ages",
                    callback: function () {
                    }
                });
                
                return;
            }
            
            from = parseInt(from,10);
            to = parseInt(to,10);
            

            if (to < from)
            {
                dhtmlx.alert({
                    text: "Invalid Age Order!",
                    type: "alert-warning",
                    title: "Save Ages",
                    callback: function () {
                    }
                });
                return;
            }



            childrenageslayout.cells("a").progressOn();

            form_childrenages.setItemValue("token", global_token);

            form_childrenages.send("php/api/childrenages/savechildrenage.php", "post", function (loader)
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
                        childrenageslayout.cells("a").progressOff();
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
                        childrenageslayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsChildrenAges.clearAll();
                        grid_childrenages.clearAll();

                        dsChildrenAges.load("php/api/childrenages/childrenagegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_childrenages.sync(dsChildrenAges);
                            popupwin_childrenages.setModal(false);
                            popupwin_childrenages.hide();
                            childrenageslayout.cells("a").progressOff();

                            grid_childrenages.selectRowById(json_obj.ID, false, true, false);
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
                        childrenageslayout.cells("a").progressOff();
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

    function Between0_17(data)
    {
        if (!data)
        {
            return false;
        }

        if (isNaN(data))
        {
            return data;
        }

        if (data < 0)
        {
            return false;
        }
        if (data > 17)
        {
            return false;
        }
        return true;
    }

    popupwin_childrenages.hide();

}