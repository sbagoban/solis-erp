var usergroup_obj = new usergroups();

function usergroups()
{

    document.getElementById("aTitle").innerHTML = "USER GROUPS";
    
    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();



    var grid_groups = main_layout.cells("a").attachGrid();
    grid_groups.setIconsPath('libraries/dhtmlx/imgs/');
    grid_groups.setHeader("Name,Code,Active,Description");
    grid_groups.setColumnIds("ugroup,grpcode,grpactiveyn,description");
    grid_groups.setColTypes("ro,ro,ro,ro");
    grid_groups.setInitWidths("300,100,100,*");
    grid_groups.setColAlign("left,left,center,left");
    grid_groups.setColSorting('str,str,str,str');
    grid_groups.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.setIconSize(32);
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.addButton("share", 4, "Share Rights", "modules.png", "modules.png");
    toolbar.addButton("export", 5, "Export Excel", "excel.png");
    
    applyrights();
    
    
    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_ug.clear();
            form_ug.setItemValue("id", "-1");
            popupwin_ug.setModal(true);
            popupwin_ug.center();
            popupwin_ug.show();
        } else if (id == "modify")
        {
            var uid = grid_groups.getSelectedRowId();
            if (!uid)
            {
                return;
            }
            
            //cannot delete usergroup admin
            var data = dsGroups.item(uid);
            if(data.ugroup == "ADMIN")
            {
                dhtmlx.alert({
                    text: "Cannot modify ADMIN Group",
                    type: "alert-warning",
                    title: "Modify User Group",
                    callback: function () {
                    }
                });
                
                return;
            }
            
            var data = dsGroups.item(uid);
            form_ug.setFormData(data);


            popupwin_ug.setModal(true);
            popupwin_ug.center();
            popupwin_ug.show();


        } 
        else if(id == "export")
        {
            grid_groups.toExcel('php/api/grid-excel-php/generate.php');
        }
        else if (id == "delete")
        {
            var gid = grid_groups.getSelectedRowId();
            if (!gid)
            {
                return;
            }
            
            
            //cannot delete usergroup admin
            var data = dsGroups.item(gid);
            if(data.ugroup == "ADMIN")
            {
                dhtmlx.alert({
                    text: "Cannot delete ADMIN Group",
                    type: "alert-warning",
                    title: "Delete User Group",
                    callback: function () {
                    }
                });
                
                return;
            }
            
            
            dhtmlx.confirm({
                title: "Delete Group",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/usergroups/deletegroup.php", params, function (loader) {

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
                                    grid_groups.deleteRow(gid);
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


    var dsGroups = new dhtmlXDataStore();
    dsGroups.load("php/api/usergroups/uggrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_groups.sync(dsGroups);

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
            $("#main_body").width(x - 10);

            main_layout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(main_layout.cells("a"));
    
    var popupwin_ug = dhxWins.createWindow("popupwin_ug", 50, 50, 650, 240);
    popupwin_ug.setText("User Group Details:");
    popupwin_ug.denyResize();
    popupwin_ug.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_ug"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", label: "Group ID:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "hidden", name: "token"},
        {type: "input", name: "ugroup", label: "User Group:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "grpcode", label: "Code:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },

        {type: "combo", name: "grpactiveyn", label: "Active:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "Y", text: "Yes", selected: true, img_src: "images/yes.png"},
                {value: "N", text: "No", img_src: "images/no.png"}
            ]
        },
        {type: "input", name: "description", label: "Description:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var uglayout = popupwin_ug.attachLayout("1C");

    uglayout.cells("a").hideHeader();

    var form_ug = uglayout.cells("a").attachForm(str_frm_ug);



    form_ug.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_ug.setModal(false);
            popupwin_ug.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_ug.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save User Group",
                    callback: function () {
                    }
                });
                return;
            }


            uglayout.cells("a").progressOn();

            form_ug.setItemValue("token", global_token);

            form_ug.send("php/api/usergroups/savegroup.php", "post", function (loader)
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
                        uglayout.cells("a").progressOff();
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
                        uglayout.cells("a").progressOff();
                        return false;
                    }
                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });
                        
                        dsGroups.clearAll();
                        grid_groups.clearAll();

                        dsGroups.load("php/api/usergroups/uggrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_groups.sync(dsGroups);
                            popupwin_ug.setModal(false);
                            popupwin_ug.hide();
                            uglayout.cells("a").progressOff();
                            
                            grid_groups.selectRowById(json_obj.ID, false, true, false);
                            
                        });
                
                    } else
                    {
                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "DELETE",
                            callback: function () {
                            }
                        });
                        uglayout.cells("a").progressOff();
                    }

                }
            });


        }
    });
    
    function applyrights()
    {       
        for(var i = 0; i < json_rights.length; i++)
        {
            if(json_rights[i].PROCESSNAME == "ADD" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("new");
                toolbar.setItemToolTip("new", "Not Allowed");
            }
            else if(json_rights[i].PROCESSNAME == "MODIFY" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("modify");
                toolbar.setItemToolTip("modify", "Not Allowed");
            }
            else if(json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("delete");
                toolbar.setItemToolTip("delete", "Not Allowed");
            }
        }
    }
    

    popupwin_ug.hide();

}