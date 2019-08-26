var companytype_obj = new companytypes();

function companytypes()
{
    

    document.getElementById("aTitle").innerHTML = "COMPANY TYPES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("CompanyTypes");

    var grid_companytypes = main_layout.cells("a").attachGrid();
    grid_companytypes.setIconsPath('libraries/dhtmlx/imgs/');
    grid_companytypes.setHeader("CompanyType");
    grid_companytypes.setColumnIds("comptype");
    grid_companytypes.setColTypes("ro");
    grid_companytypes.setInitWidths("300");
    grid_companytypes.setColAlign("left");
    grid_companytypes.setColSorting('str');
    grid_companytypes.init();


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
            form_companytypes.clear();
            form_companytypes.setItemValue("id", "-1");
                        
            popupwin_companytypes.setModal(true);
            popupwin_companytypes.center();
            popupwin_companytypes.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_companytypes.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsCompanyTypes.item(uid);
            form_companytypes.setFormData(data);

            popupwin_companytypes.setModal(true);
            popupwin_companytypes.center();
            popupwin_companytypes.show();


        } else if (id == "delete")
        {
            var gid = grid_companytypes.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Company Type",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/companytypes/deletecompanytype.php", params, function (loader) {

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
                                    grid_companytypes.deleteRow(gid);
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


    var dsCompanyTypes = new dhtmlXDataStore();
    dsCompanyTypes.load("php/api/companytypes/companytypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_companytypes.sync(dsCompanyTypes);

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
    
    var popupwin_companytypes = dhxWins.createWindow("popupwin_companytypes", 50, 50, 500, 250);
    popupwin_companytypes.setText("CompanyType Details:");
    popupwin_companytypes.denyResize();
    popupwin_companytypes.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_companytypes"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "comptype", label: "CompanyType:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var companytypelayout = popupwin_companytypes.attachLayout("1C");

    companytypelayout.cells("a").hideHeader();

    var form_companytypes = companytypelayout.cells("a").attachForm(str_frm_ug);



    form_companytypes.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_companytypes.setModal(false);
            popupwin_companytypes.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_companytypes.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Company Type",
                    callback: function () {
                    }
                });
                return;
            }
            
           
            companytypelayout.cells("a").progressOn();

            form_companytypes.setItemValue("token", global_token);

            form_companytypes.send("php/api/companytypes/savecompanytype.php", "post", function (loader)
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
                        companytypelayout.cells("a").progressOff();
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
                        companytypelayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsCompanyTypes.clearAll();
                        grid_companytypes.clearAll();

                        dsCompanyTypes.load("php/api/companytypes/companytypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_companytypes.sync(dsCompanyTypes);
                            popupwin_companytypes.setModal(false);
                            popupwin_companytypes.hide();
                            companytypelayout.cells("a").progressOff();

                            grid_companytypes.selectRowById(json_obj.ID, false, true, false);
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
                        companytypelayout.cells("a").progressOff();
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

    popupwin_companytypes.hide();

}