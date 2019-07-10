var vehicletypes_obj = new vehicletypes();

function vehicletypes()
{

    var new_luggage_row_id = 0;

    document.getElementById("aTitle").innerHTML = "VEHICLE CATEGORIES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Vehicle Categories");

    var grid_vehicletypes = main_layout.cells("a").attachGrid();
    grid_vehicletypes.setIconsPath('libraries/dhtmlx/imgs/');
    grid_vehicletypes.setHeader("Vehicle Type,Description,Type,Per Way,Per Seat,Used for Activities,Allowed for Surcharge,Used for default accomodation,Max Capacity,Adult,Children");
    grid_vehicletypes.setColumnIds("vehname,description,vehtype,perway,perseat,activities,allowed_surcharge,default_accomodation_transfer,max_capacity,adult_count,children_count");
    grid_vehicletypes.setColTypes("ro,ro,ro,ch,ch,ch,ch,ch,ro,ro,ro");
    grid_vehicletypes.setInitWidths("*,0,100,70,70,70,70,70,70,70,70");
    grid_vehicletypes.setColAlign("left,left,left,center,center,center,center,center,center,center,center");
    grid_vehicletypes.setColSorting('str,str,str,int,int,int,int,int,int,int,int');
    grid_vehicletypes.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter");
    grid_vehicletypes.setEditable(false);
    grid_vehicletypes.init();


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
            form_vehicletypes.clear();
            form_vehicletypes.setItemValue("id", "-1");
            popupwin_vehicletypes.setModal(true);
            popupwin_vehicletypes.center();
            popupwin_vehicletypes.show();

            grid_luggages.clearAll();

        } else if (id == "export")
        {
            grid_vehicletypes.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_vehicletypes.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsVehicleTypes.item(uid);
            form_vehicletypes.setFormData(data);

            loadLuggages(uid);

            popupwin_vehicletypes.setModal(true);
            popupwin_vehicletypes.center();
            popupwin_vehicletypes.show();


        } else if (id == "delete")
        {
            var gid = grid_vehicletypes.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Vehicle Type",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/vehicletypes/deletevehicletype.php", params, function (loader) {

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
                                    grid_vehicletypes.deleteRow(gid);
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


    var dsVehicleTypes = new dhtmlXDataStore();
    dsVehicleTypes.load("php/api/vehicletypes/vehicletypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_vehicletypes.sync(dsVehicleTypes);

    });

    var dsLuggages = new dhtmlXDataStore();


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

    var popupwin_vehicletypes = dhxWins.createWindow("popupwin_vehicletypes", 50, 50, 700, 440);
    popupwin_vehicletypes.setText("VehicleType Details:");
    popupwin_vehicletypes.denyResize();
    popupwin_vehicletypes.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_vehicletypes"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "vehname", label: "Category:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "editor", name: "description", label: "Description:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "100", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "combo", name: "vehtype", label: "Vehicle Type:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, readonly: true,
            comboType: "image",
            comboImagePath: "../../images/",
            options: [
                {value: "CAR", text: "CAR", selected: true, img_src: "images/car.png"},
                {value: "MINIBUS", text: "MINIBUS", img_src: "images/minibus.png"},
                {value: "BUS", text: "BUS", img_src: "images/bus.png"},
                {value: "HELICOPTER", text: "HELICOPTER", img_src: "images/helicopter.png"}
            ]
        },
        {type: "checkbox", name: "perway", label: "Allowed for <br>using as Per way:",
            labelWidth: "150",
            labelHeight: "44", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "perseat", label: "Allowed for <br>using as Seat in coach:",
            labelWidth: "150",
            labelHeight: "44", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "activities", label: "Allowed for <br>using for activities:",
            labelWidth: "150",
            labelHeight: "44", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "allowed_surcharge", label: "Allowed for surcharge:",
            labelWidth: "150",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "checkbox", name: "default_accomodation_transfer",
            label: "Default for transfer <br>for accommodation:",
            labelWidth: "150",
            labelHeight: "44", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "max_capacity", label: "Max Pax:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidInteger"
        },
        {type: "input", name: "adult_count", label: "Adult:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidInteger"
        },
        {type: "input", name: "children_count", label: "Children:", labelWidth: "150",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidInteger"
        },
        {type: "container", name: "grid_luggage", label: "Luggages:",
            labelWidth: "150",
            inputWidth: 400, inputHeight: 200},
        {type: "button", name: "cmdAdd",
            value: "Add", width: "50",
            position: "absolute", inputTop: "518", inputLeft: "555", height: "30"
        },
        {type: "button", name: "cmdDelete",
            value: "Delete Row", width: "50",
            position: "absolute", inputTop: "548", inputLeft: "555", height: "30"
        },
        {type: "hidden", name: "luggages"},
        {type: "block", blockOffset: 0, list: [
                {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
            ]}
    ];

    var vehicletypelayout = popupwin_vehicletypes.attachLayout("1C");

    vehicletypelayout.cells("a").hideHeader();

    var form_vehicletypes = vehicletypelayout.cells("a").attachForm(str_frm_ug);

    var grid_luggages = new dhtmlXGridObject(form_vehicletypes.getContainer("grid_luggage"));
    grid_luggages.setIconsPath('libraries/dhtmlx/imgs/');
    grid_luggages.setHeader("Standard size suitcases,Hand bags,Golf bags,Action,Valid");
    grid_luggages.setColumnIds("suitcase,handbag,golfbag,action,valid");
    grid_luggages.setColTypes("edn,edn,edn,ro,ro");
    grid_luggages.setInitWidths("100,100,100,0,0");
    grid_luggages.setColAlign("center,center,center,center,center");
    grid_luggages.setColSorting('int,int,int,int,int');
    grid_luggages.enableEditEvents(true, false, true);
    grid_luggages.enableValidation(true);
    grid_luggages.setColValidators("ValidInteger,ValidInteger,ValidInteger,,");

    grid_luggages.init();

    grid_luggages.attachEvent("onValidationError", function (id, ind, value) {
        grid_luggages.cells(id, grid_luggages.getColIndexById("valid")).setValue("ERR");
        return true;
    });

    grid_luggages.attachEvent("onValidationCorrect", function (id, ind, value) {
        grid_luggages.cells(id, grid_luggages.getColIndexById("valid")).setValue("");
        return true;
    });


    form_vehicletypes.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_vehicletypes.setModal(false);
            popupwin_vehicletypes.hide();
        } else if (name == "cmdSave")
        {
            if (!form_vehicletypes.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Vehicle Type",
                    callback: function () {
                    }
                });
                return;
            }


            //validate luggage grid

            if (!validateLuggages())
            {
                return;
            }

            var json_luggages = utils_dhxSerializeGridToJson(grid_luggages);
            form_vehicletypes.setItemValue("luggages", json_luggages);

            vehicletypelayout.cells("a").progressOn();

            form_vehicletypes.setItemValue("token", global_token);

            form_vehicletypes.send("php/api/vehicletypes/savevehicletype.php", "post", function (loader)
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
                        vehicletypelayout.cells("a").progressOff();
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
                        vehicletypelayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsVehicleTypes.clearAll();
                        grid_vehicletypes.clearAll();

                        dsVehicleTypes = null;
                        dsVehicleTypes = new dhtmlXDataStore();

                        dsVehicleTypes.load("php/api/vehicletypes/vehicletypegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_vehicletypes.sync(dsVehicleTypes);
                            popupwin_vehicletypes.setModal(false);
                            popupwin_vehicletypes.hide();
                            vehicletypelayout.cells("a").progressOff();

                            grid_vehicletypes.selectRowById(json_obj.ID, false, true, false);
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
                        vehicletypelayout.cells("a").progressOff();
                    }
                }
            });
        } else if (name == "cmdAdd")
        {
            new_luggage_row_id--;
            grid_luggages.addRow(new_luggage_row_id, ["0", "0", "0", "ADD"]);
        } else if (name == "cmdDelete")
        {
            var rid = grid_luggages.getSelectedRowId();
            if (rid)
            {
                if (parseInt(rid, 10) < 0)
                {
                    grid_luggages.deleteRow(rid);
                } else
                {
                    grid_luggages.cells(rid, grid_luggages.getColIndexById("action")).setValue("DELETE");
                    grid_luggages.setRowHidden(rid, true);
                }
            }
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


    function validateLuggages()
    {
        //all validation ok?
        var searchResult = grid_luggages.findCell("ERR", grid_luggages.getColIndexById("valid"), true);
        if (searchResult.length > 0)
        {
            dhtmlx.alert({
                text: "Please correct luggage entries!",
                type: "alert-warning",
                title: "SAVE",
                callback: function () {
                }
            });
            return false;
        }

        for (var i = 0; i < grid_luggages.getRowsNum(); i++) {


            var id = grid_luggages.getRowId(i);


            var suit = grid_luggages.cells(id, grid_luggages.getColIndexById("suitcase")).getValue();
            var hb = grid_luggages.cells(id, grid_luggages.getColIndexById("handbag")).getValue();
            var gb = grid_luggages.cells(id, grid_luggages.getColIndexById("golfbag")).getValue();

            if (suit == "" || hb == "" || gb == "")
            {
                dhtmlx.alert({
                    text: "Please enter a numeric luggage entry!",
                    type: "alert-warning",
                    title: "SAVE",
                    callback: function () {
                        grid_luggages.selectRowById(id, false, true, false);

                    }
                });
                return false;
            }


            if (isNaN(suit) || isNaN(hb) || isNaN(gb))
            {
                dhtmlx.alert({
                    text: "Please enter a numeric luggage entry!",
                    type: "alert-warning",
                    title: "SAVE",
                    callback: function () {
                        grid_luggages.selectRowById(id, false, true, false);

                    }
                });
                return false;
            }

            suit = parseInt(suit, 10);
            hb = parseInt(hb, 10);
            gb = parseInt(gb, 10);

            if ((suit == 0 && hb == 0 && gb == 0) || (suit < 0 || hb < 0 || gb < 0))
            {

                dhtmlx.alert({
                    text: "Please correct luggage entries!",
                    type: "alert-warning",
                    title: "SAVE",
                    callback: function () {
                        grid_luggages.selectRowById(id, false, true, false);
                    }
                });
                return false;
            }
        }


        return true;
    }

    function loadLuggages(vid)
    {   
        grid_luggages.clearAll();
        dsLuggages.clearAll();
        dsLuggages = null;
        dsLuggages = new dhtmlXDataStore();
        
        vehicletypelayout.cells("a").progressOn();
        dsLuggages.load("php/api/vehicletypes/luggagegrid.php?t=" + encodeURIComponent(global_token) + "&vid=" + vid, "json", function () {
            vehicletypelayout.cells("a").progressOff();
            grid_luggages.sync(dsLuggages);

        });
    }



    popupwin_vehicletypes.hide();

}