var grphotels_obj = new grphotels();

function grphotels()
{
    

    document.getElementById("aTitle").innerHTML = "GROUP HOTELS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_grphotels = main_layout.cells("a").attachGrid();
    grid_grphotels.setIconsPath('libraries/dhtmlx/imgs/');
    grid_grphotels.setHeader("Name,Address,Phone,Fax,Email");
    grid_grphotels.setColumnIds("grpname,address,phone,fax,email");
    grid_grphotels.setColTypes("ro,ro,ro,ro,ro");
    grid_grphotels.setInitWidths("250,*,100,100,100");
    grid_grphotels.setColAlign("left,left,left,left,left");
    grid_grphotels.setColSorting('str,str,str,str,str');
    grid_grphotels.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    grid_grphotels.init();


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
            form_grphotels.clear();
            form_grphotels.setItemValue("id", "-1");
            
            popupwin_grphotels.setModal(true);
            popupwin_grphotels.center();
            popupwin_grphotels.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_grphotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsGrpHotels.item(uid);
            form_grphotels.setFormData(data);

            popupwin_grphotels.setModal(true);
            popupwin_grphotels.center();
            popupwin_grphotels.show();


        } else if (id == "delete")
        {
            var gid = grid_grphotels.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete GrpHotels",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/grphotels/deletegrphotel.php", params, function (loader) {

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
                                    grid_grphotels.deleteRow(gid);
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


    var dsGrpHotels = new dhtmlXDataStore();
    dsGrpHotels.load("php/api/grphotels/grphotelgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_grphotels.sync(dsGrpHotels);

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
    
    var popupwin_grphotels = dhxWins.createWindow("popupwin_grphotels", 50, 50, 550, 280);
    popupwin_grphotels.setText("Group Hotels Details:");
    popupwin_grphotels.denyResize();
    popupwin_grphotels.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_grphotels"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "grpname", label: "Group Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "address", label: "Address:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "phone", label: "Phone:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "fax", label: "Fax:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
       
        {type: "input", name: "email", label: "Email:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var grphotelslayout = popupwin_grphotels.attachLayout("1C");

    grphotelslayout.cells("a").hideHeader();

    var form_grphotels = grphotelslayout.cells("a").attachForm(str_frm_ug);



    form_grphotels.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_grphotels.setModal(false);
            popupwin_grphotels.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_grphotels.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Group Hotel",
                    callback: function () {
                    }
                });
                return;
            }


            grphotelslayout.cells("a").progressOn();

            form_grphotels.setItemValue("token", global_token);

            form_grphotels.send("php/api/grphotels/savegrphotel.php", "post", function (loader)
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
                        grphotelslayout.cells("a").progressOff();
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
                        grphotelslayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsGrpHotels.clearAll();
                        grid_grphotels.clearAll();

                        dsGrpHotels.load("php/api/grphotels/grphotelgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_grphotels.sync(dsGrpHotels);
                            popupwin_grphotels.setModal(false);
                            popupwin_grphotels.hide();
                            grphotelslayout.cells("a").progressOff();

                            grid_grphotels.selectRowById(json_obj.ID, false, true, false);
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
                        grphotelslayout.cells("a").progressOff();
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

    popupwin_grphotels.hide();

}