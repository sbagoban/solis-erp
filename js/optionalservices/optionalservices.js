var optionalservices_obj = new optionalservices();

function optionalservices()
{


    document.getElementById("aTitle").innerHTML = "OPTIONAL SERVICES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Optional Services");

    var grid_optionalservices = main_layout.cells("a").attachGrid();
    grid_optionalservices.setIconsPath('libraries/dhtmlx/imgs/');
    grid_optionalservices.setHeader("Name,Notes,Used With Activities,Used With Transfer,Used As Checkbos,Applied For");
    grid_optionalservices.setColumnIds("optname,optnotes,usedwithactivities,usedwithtransfer,useascheckbox,appliedfor");
    grid_optionalservices.setColTypes("ro,ro,ch,ch,ch,ro");
    grid_optionalservices.setInitWidths("150,250,80,80,80,*");
    grid_optionalservices.setColAlign("left,left,center,center,center,left");
    grid_optionalservices.setColSorting('str,str,int,int,int,str');
    grid_optionalservices.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter");
    grid_optionalservices.setEditable(false);
    grid_optionalservices.init();



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
            form_optionalservices.clear();
            form_optionalservices.setItemValue("id", "-1");

            popupwin_optionalservices.setModal(true);
            popupwin_optionalservices.center();
            popupwin_optionalservices.show();


        } else if (id == "export")
        {
            grid_optionalservices.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_optionalservices.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsOptionalServices.item(uid);
            form_optionalservices.setFormData(data);

            popupwin_optionalservices.setModal(true);
            popupwin_optionalservices.center();
            popupwin_optionalservices.show();


        } else if (id == "delete")
        {
            var gid = grid_optionalservices.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Optional Service",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/optionalservices/deleteoptionalservice.php", params, function (loader) {

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
                                    grid_optionalservices.deleteRow(gid);
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


    var dsOptionalServices = new dhtmlXDataStore();
    dsOptionalServices.load("php/api/optionalservices/optionalservicegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_optionalservices.sync(dsOptionalServices);

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

    var popupwin_optionalservices = dhxWins.createWindow("popupwin_optionalservices", 50, 50, 680, 470);
    popupwin_optionalservices.setText("Optional Service Details:");
    popupwin_optionalservices.denyResize();
    popupwin_optionalservices.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_optionalservices"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "optname", label: "Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "editor", name: "optnotes", label: "Notes:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "190", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "checkbox", name: "usedwithactivities", label: "Used with Activities:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "usedwithtransfer", label: "Used with Transfer:",
            labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "useascheckbox", label: "Used as Checkbox:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "combo", name: "appliedfor", label: "Applied For:", labelWidth: "130",
            labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "ADULTS", text: "ADULTS", selected: true, img_src: "images/adult_24.png"},
                {value: "CHILDREN", text: "CHILDREN", img_src: "images/child_24.png"},
                {value: "BOTH", text: "BOTH", img_src: "images/family_three_24.png"}
            ]
        },

        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var optionalserviceslayout = popupwin_optionalservices.attachLayout("1C");

    optionalserviceslayout.cells("a").hideHeader();

    var form_optionalservices = optionalserviceslayout.cells("a").attachForm(str_frm_ug);



    form_optionalservices.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_optionalservices.setModal(false);
            popupwin_optionalservices.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_optionalservices.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Meal Plan",
                    callback: function () {
                    }
                });
                return;
            }


            optionalserviceslayout.cells("a").progressOn();

            form_optionalservices.setItemValue("token", global_token);

            form_optionalservices.send("php/api/optionalservices/saveoptionalservice.php", "post", function (loader)
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
                        optionalserviceslayout.cells("a").progressOff();
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
                        optionalserviceslayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsOptionalServices.clearAll();
                        grid_optionalservices.clearAll();

                        dsOptionalServices.load("php/api/optionalservices/optionalservicegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_optionalservices.sync(dsOptionalServices);
                            popupwin_optionalservices.setModal(false);
                            popupwin_optionalservices.hide();
                            optionalserviceslayout.cells("a").progressOff();

                            grid_optionalservices.selectRowById(json_obj.ID, false, true, false);
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
                        optionalserviceslayout.cells("a").progressOff();
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

    popupwin_optionalservices.hide();

}