var user_obj = new users();

function users()
{

    document.getElementById("aTitle").innerHTML = "USERS";

    var sublayout = new dhtmlXLayoutObject("main_body", "1C");


    sublayout.cells('a').hideHeader();


    /*==variable declaration==*/

    var grid_users = null;

    var dsUsers = new dhtmlXDataStore();

    sublayout.cells("a").setText("Users");

    var grid_users = sublayout.cells("a").attachGrid();
    grid_users.setIconsPath('libraries/dhtmlx/imgs/');
    grid_users.setHeader("Internal / External,Username,Email,User Group,Gender,Status,Departments,Tour Operator,Full Name,Created,Date Activated,Modified,Last Login");
    grid_users.setColumnIds("intern_extern,uname,email,ugroup,gender,status,selected_depts_codes,toname,ufullname,date_created,date_activated,date_modified,date_lastlogin_success");
    grid_users.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
    grid_users.setInitWidths("60,100,150,0,70,100,150,150,150,120,120,120,120");
    grid_users.setColAlign("left,left,left,left,center,center,left,left,left,center,center,center,center");
    grid_users.setColSorting('str,str,str,str,str,str,str,str,str,date,date,date,date');
    grid_users.attachHeader("#select_filter,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    grid_users.setEditable(false);
    grid_users.enableMultiselect(true);
    grid_users.init();

    var toolbar = sublayout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.setIconSize(32);
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");


    var opts = Array(
            Array('modify', 'obj', 'Modify Selected Users', 'modify.png'),
            Array('delete', 'obj', 'Delete Selected Users', 'delete.png'),
            Array('activate', 'obj', 'Activate Selected Users', 'online.png'),
            Array('deactivate', 'obj', 'Deactivate Selected Users', 'offline.png')
            );
    toolbar.addButtonSelect("opts", 2, "Operate", opts, "operate.png", "operate.png", null, true);

    toolbar.addButton("export", 3, "Export Excel", "excel.png");


    applyrights();

    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_user.clear();

            form_user.setItemValue("id", "-1");
            form_user.showItem("status");
            form_user.hideItem("cmdCancelReset");
            form_user.hideItem("cmdReset");
            
            grid_depts.checkAll(false)
            toggleTOCbo();

            popupwin_user.setModal(true);
            popupwin_user.center();
            popupwin_user.show();


        } else if (id == "activate")
        {
            var uid = grid_users.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            dhtmlx.confirm({
                title: "Activate User",
                type: "confirm",
                text: "Confirm Activation of selected Users?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "uid=" + uid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/users/activateusers.php", params, function (loader) {

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "ACTIVATE",
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
                                        title: "ACTIVATE",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    sublayout.cells("a").progressOn();
                                    dsUsers.load("php/api/users/usergrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                                        sublayout.cells("a").progressOff();
                                        grid_users.sync(dsUsers);
                                        grid_users.groupBy(grid_users.getColIndexById("ugroup"));
                                    });
                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "ACTIVATE",
                                        callback: function () {
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });

        } else if (id == "deactivate")
        {
            var uid = grid_users.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            dhtmlx.confirm({
                title: "De-activate User",
                type: "confirm",
                text: "Confirm de-activation of selected Users?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "uid=" + uid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/users/deactivateusers.php", params, function (loader) {

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DEACTIVATE",
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
                                        title: "DEACTIVATE",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    sublayout.cells("a").progressOn();
                                    dsUsers.load("php/api/users/usergrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                                        sublayout.cells("a").progressOff();
                                        grid_users.sync(dsUsers);
                                        grid_users.groupBy(grid_users.getColIndexById("ugroup"));
                                    });
                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DEACTIVATE",
                                        callback: function () {
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });

        } else if (id == "export")
        {
            exportToExcel();
        } else if (id == "modify")
        {
            var uid = grid_users.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            //only for single selection
            var arr_ids = uid.split(",");
            if (arr_ids.length > 1)
            {
                dhtmlx.alert({
                    text: "Please select only one User!",
                    type: "alert",
                    title: "Modify User",
                    callback: function () {
                    }
                });
            }

            form_user.hideItem("status");
            form_user.hideItem("cmdCancelReset");
            form_user.showItem("cmdReset");

            var data = dsUsers.item(uid);
            form_user.setFormData(data);

            form_user.setItemValue("resetpassword", "");
            
            
            grid_depts.checkAll(false)
            var selected_depts_ids = data.selected_depts_ids;
            if (selected_depts_ids)
            {
                var arr_ids = selected_depts_ids.split(",");
                for (var i = 0; i < arr_ids.length; i++)
                {
                    var id = arr_ids[i];
                    grid_depts.cells(id, 0).setValue(1);
                }
            }
            
            
            toggleTOCbo();
            loadCboTO(data.tofk);


            popupwin_user.setModal(true);
            popupwin_user.center();
            popupwin_user.show();


        } else if (id == "delete")
        {
            var uid = grid_users.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            //only for single selection
            var arr_ids = uid.split(",");
            if (arr_ids.length > 1)
            {
                dhtmlx.alert({
                    text: "Please select only one User!",
                    type: "alert",
                    title: "Modify User",
                    callback: function () {
                    }
                });
            }

            dhtmlx.confirm({
                title: "Delete User",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "uid=" + uid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/users/deleteuser.php", params, function (loader) {

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
                                    grid_users.deleteRow(uid);
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


    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(sublayout.cells("a"));

    var popupwin_user = dhxWins.createWindow("popupwin_user", 50, 50, 650, 480);
    popupwin_user.setText("User Details:");
    popupwin_user.denyResize();
    popupwin_user.denyPark();


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

            sublayout.setSizes(true);

        }, 1);
    }

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_user = [
        {type: "settings", position: "label-left", id: "form_user"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id"},
        {type: "hidden", name: "resetpassword"},
        {type: "hidden", name: "token"},
        {type: "hidden", name: "tofk"},
        {type: "input", name: "uname", label: "User Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "email", label: "Email:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidEmail",
            required: true
        },
        {type: "password", name: "upass", label: "Password:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdCancelReset", value: "X", width: "50",
            position: "absolute", inputTop: "71", inputLeft: "440"
        },
        {type: "button", name: "cmdReset", value: "Reset Password", width: "300",
            position: "absolute", inputTop: "71", inputLeft: "132"
        },
        {type: "combo", name: "ugrpid", label: "User Group:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/",
        },
        {type: "input", name: "ufullname", label: "Full Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            required: true
        },
        {type: "combo", name: "gender", label: "Gender:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "M", text: "MALE", selected: true, img_src: "images/gender.png"},
                {value: "F", text: "FEMALE", img_src: "images/gender.png"}
            ]
        },
        {type: "combo", name: "status", label: "Status:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "ACTIVE", text: "ACTIVE", selected: true, img_src: "images/yes.png"},
                {value: "DEACTIVATED", text: "DEACTIVATED", img_src: "images/no.png"}
            ]
        },
        {type: "combo", name: "intern_extern", label: "Internal/External:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "INT", text: "INTERNAL", selected: true, img_src: "images/internal_32.png"},
                {value: "EXT", text: "EXTERNAL", img_src: "images/external_32.png"}
            ]
        },
        {type: "combo", name: "_cboTO", label: "Tour Operator:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            comboType: "image",
            comboImagePath: "../../images/",
        },
        {type: "container", name: "grdDepts", label: "Departments:",
            labelWidth: "130",
            inputWidth: 300, inputHeight: 300},
        {type: "hidden", name: "selected_depts_ids"},
        {type: "block", blockOffset: 0, list: [
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "newcolumn"},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
        ]}
    ];

    var userlayout = popupwin_user.attachLayout("1C");

    userlayout.cells("a").hideHeader();

    var form_user = userlayout.cells("a").attachForm(str_frm_user);
    
    var grid_depts = new dhtmlXGridObject(form_user.getContainer("grdDepts"));
    grid_depts.setIconsPath('libraries/dhtmlx/imgs/');
    grid_depts.setHeader(",Dept Code,Dept Name");
    grid_depts.setColumnIds("X,deptcode,deptname");
    grid_depts.setColTypes("ch,ro,ro");
    grid_depts.setInitWidths("40,100,*");
    grid_depts.setColAlign("center,left,left");
    grid_depts.setColSorting('int,str,str');
    grid_depts.attachHeader("#master_checkbox,#text_filter,#text_filter");
    grid_depts.init();



    form_user.attachEvent("onChange", function (id, value) {
        if (id == "intern_extern")
        {
            toggleTOCbo();

            var intext = form_user.getItemValue("intern_extern");
            if (intext == "EXT")
            {
                loadCboTO(form_user.getItemValue("tofk"));
            }
        }
    });

    form_user.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_user.setModal(false);
            popupwin_user.hide();
        } else if (name == "cmdReset")
        {
            form_user.showItem("cmdCancelReset");
            form_user.hideItem("cmdReset");
            form_user.setItemValue("upass", "");
            form_user.setItemValue("resetpassword", "YES");

        } else if (name == "cmdCancelReset")
        {
            form_user.hideItem("cmdCancelReset");
            form_user.showItem("cmdReset");
            form_user.setItemValue("resetpassword", "");
        } else if (name == "cmdSave")
        {
            if (!form_user.validate())
            {
                dhtmlx.alert({
                    text: "Please fill in highlighted fields correctly",
                    type: "alert-warning",
                    title: "Save User",
                    callback: function () {
                    }
                });
                return;
            }
            
            
            form_user.setItemValue("selected_depts_ids","");
            if (form_user.getItemValue("intern_extern") == "INT")
            {
                var checkedids = grid_depts.getCheckedRows(0);
                
                if(checkedids == ""){
                    dhtmlx.alert({
                        text: "Please select at least one Department for the User!",
                        type: "alert-warning",
                        title: "Save User",
                        callback: function () {

                        }
                    });
                    return;
                }
                
                form_user.setItemValue("selected_depts_ids",checkedids);
            }
            
            
            
            //====================================
            var upass = utils_trim(form_user.getItemValue("upass"), " ");

            if (form_user.getItemValue("id") == "-1" && upass == "")
            {
                //new user! password compulsory
                dhtmlx.alert({
                    text: "Please specify New User's Password",
                    type: "alert-warning",
                    title: "Save User",
                    callback: function () {
                        form_user.setItemFocus("upass");
                    }
                });
                return;

            } else
            {
                //modify password
                if (form_user.getItemValue("resetpassword") == "YES" && upass == "")
                {
                    //modify user with reset password compulsory
                    dhtmlx.alert({
                        text: "Please specify Reset Password",
                        type: "alert-warning",
                        title: "Save User",
                        callback: function () {
                            form_user.setItemFocus("upass");
                        }
                    });
                    return;
                }
            }

            //====================================


            form_user.setItemValue("token", global_token);

            userlayout.cells("a").progressOn();

            form_user.send("php/api/users/saveuser.php", "post", function (loader, response)
            {
                if (loader)
                {
                    userlayout.cells("a").progressOff();

                    if (loader.xmlDoc.responseURL == "")
                    {
                        dhtmlx.alert({
                            text: "Connection Lost!",
                            type: "alert-warning",
                            title: "SAVE",
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
                            title: "SAVE",
                            callback: function () {
                            }
                        });
                        return false;
                    }
                    if (json_obj.OUTCOME == "OK")
                    {
                        if (json_obj.PWD_RESET == "YES")
                        {
                            dhtmlx.alert({
                                text: "Password Reset!",
                                type: "alert",
                                title: "SAVE",
                                callback: function () {
                                }
                            });
                        }

                        dsUsers.clearAll();
                        grid_users.clearAll();


                        userlayout.cells("a").progressOn();
                        dsUsers.load("php/api/users/usergrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_users.sync(dsUsers);
                            grid_users.groupBy(grid_users.getColIndexById("ugroup"));
                            popupwin_user.setModal(false);
                            popupwin_user.hide();
                            userlayout.cells("a").progressOff();

                            grid_users.selectRowById(json_obj.ID, false, true, false);
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
                    }

                }
            });
        }
    });

    var cboTO = form_user.getCombo("_cboTO");
    cboTO.readonly(false);
    cboTO.enableFilteringMode(true);

    var cboUserGrp = form_user.getCombo("ugrpid");
    var dsUGrp = new dhtmlXDataStore();
    dsUGrp.load("php/api/combos/user_group_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsUGrp.dataCount(); i++)
        {
            var item = dsUGrp.item(dsUGrp.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboUserGrp.addOption([{value: value, text: txt, img_src: "images/usergroups_small.png"}]);
        }

        cboUserGrp.readonly(true);
    });


    function exportToExcel()
    {
        grid_users.toExcel('php/api/grid-excel-php/generate.php');
    }

    popupwin_user.hide();
    /*==Clear the tab ==*/
    this.destroy = function () {

    };


    sublayout.cells("a").progressOn();
    dsUsers.load("php/api/users/usergrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        sublayout.cells("a").progressOff();
        grid_users.sync(dsUsers);
        grid_users.groupBy(grid_users.getColIndexById("ugroup"));
    });
    
    
    var dsDepts = new dhtmlXDataStore();
    userlayout.cells("a").progressOn();
    dsDepts.load("php/api/users/deptgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        userlayout.cells("a").progressOff();
        grid_depts.sync(dsDepts);
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
                toolbar.disableListOption("opts", "modify");
                toolbar.setListOptionToolTip("opts", "modify", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "delete");
                toolbar.setListOptionToolTip("opts", "delete", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "ACTIVATE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "activate");
                toolbar.setListOptionToolTip("opts", "activate", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DEACTIVATE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "deactivate");
                toolbar.setListOptionToolTip("opts", "deactivate", "Not Allowed");
            }
        }
    }

    function toggleTOCbo()
    {
        var intext = form_user.getItemValue("intern_extern");

        if (intext == "INT")
        {
            form_user.hideItem("_cboTO");
            form_user.showItem("grdDepts");
        } else
        {
            form_user.showItem("_cboTO");
            form_user.hideItem("grdDepts");
        }
    }

    function loadCboTO(toid)
    {
        cboTO.clearAll(true);

        if (!toid || toid == "")
        {
            toid = "-1";
        }

        //load all active TOs including the one with id=toid        
        var dsTO = new dhtmlXDataStore();
        userlayout.cells("a").progressOn();
        dsTO.load("php/api/users/to_combo.php?t=" + encodeURIComponent(global_token) + "&selected_id=" + toid, "json", function () {

            userlayout.cells("a").progressOff();

            for (var i = 0; i < dsTO.dataCount(); i++)
            {
                var item = dsTO.item(dsTO.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                cboTO.addOption([{value: value, text: txt, img_src: "images/to.png"}]);
            }

            cboTO.setComboValue(null);
            cboTO.setComboText("");

            if (toid != "-1")
            {
                cboTO.setComboValue(toid);
            }

        });

    }


}