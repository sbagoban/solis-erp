var servicetypes_obj = new servicetypes();

function servicetypes()
{

    document.getElementById("aTitle").innerHTML = "SERVICE CATEGORIES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_servicetypes = main_layout.cells("a").attachGrid();
    grid_servicetypes.setIconsPath('libraries/dhtmlx/imgs/');
    grid_servicetypes.setHeader("Code,Type,Is Accomodation,Is Excursion,Is Transfer");
    grid_servicetypes.setColumnIds("servicecode,servicetype,isaccomodation,isexcursion,istransfer");
    grid_servicetypes.setColTypes("ro,ro,ch,ch,ch");
    grid_servicetypes.setInitWidths("100,*,100,100,100");
    grid_servicetypes.setColAlign("left,left,center,center,center");
    grid_servicetypes.setColSorting('str,str,int,int,int');
    grid_servicetypes.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter");
    grid_servicetypes.setEditable(false);
    grid_servicetypes.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.addButton("export", 4, "Export Excel", "excel.png");
    toolbar.setIconSize(32);

    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_servicetypes.clear();
            form_servicetypes.setItemValue("id", "-1");
            popupwin_servicetypes.setModal(true);
            popupwin_servicetypes.center();
            popupwin_servicetypes.show();


        } else if (id == "export")
        {
            grid_servicetypes.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_servicetypes.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsServiceTypes.item(uid);
            form_servicetypes.setFormData(data);

            popupwin_servicetypes.setModal(true);
            popupwin_servicetypes.center();
            popupwin_servicetypes.show();


        } else if (id == "delete")
        {
            var gid = grid_servicetypes.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Service Type",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/servicetypes/deleteservicetype.php", params, function (loader) {

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
                                    grid_servicetypes.deleteRow(gid);
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


    var dsServiceTypes = new dhtmlXDataStore();
    dsServiceTypes.load("php/api/servicetypes/servicetypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_servicetypes.sync(dsServiceTypes);

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

    var popupwin_servicetypes = dhxWins.createWindow("popupwin_servicetypes", 50, 50, 700, 340);
    popupwin_servicetypes.setText("ServiceType Details:");
    popupwin_servicetypes.denyResize();
    popupwin_servicetypes.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_servicetypes"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "servicecode", label: "Code:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            maxLength: 3
        },
        {type: "input", name: "servicetype", label: "Type:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },

        {type: "checkbox", name: "isaccomodation", label: "Is Accomodation",
            labelWidth: "150",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "isexcursion", label: "Is Excursion",
            labelWidth: "150",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "istransfer", label: "Is Transfer",
            labelWidth: "150",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "block", blockOffset: 0, list: [
                {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
            ]}
    ];

    var servicetypelayout = popupwin_servicetypes.attachLayout("1C");

    servicetypelayout.cells("a").hideHeader();

    var form_servicetypes = servicetypelayout.cells("a").attachForm(str_frm_ug);

    form_servicetypes.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_servicetypes.setModal(false);
            popupwin_servicetypes.hide();
        } else if (name == "cmdSave")
        {
            if (!form_servicetypes.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Service Type",
                    callback: function () {
                    }
                });
                return;
            }

            servicetypelayout.cells("a").progressOn();

            form_servicetypes.setItemValue("token", global_token);

            form_servicetypes.send("php/api/servicetypes/saveservicetype.php", "post", function (loader)
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
                        servicetypelayout.cells("a").progressOff();
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
                        servicetypelayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {

                        dsServiceTypes.clearAll();
                        grid_servicetypes.clearAll();

                        dsServiceTypes = null;
                        dsServiceTypes = new dhtmlXDataStore();

                        dsServiceTypes.load("php/api/servicetypes/servicetypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_servicetypes.sync(dsServiceTypes);
                            popupwin_servicetypes.setModal(false);
                            popupwin_servicetypes.hide();
                            servicetypelayout.cells("a").progressOff();

                            grid_servicetypes.selectRowById(json_obj.ID, false, true, false);
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
                        servicetypelayout.cells("a").progressOff();
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

    popupwin_servicetypes.hide();

}