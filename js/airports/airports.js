var airports_obj = new airports();

function airports()
{
    

    document.getElementById("aTitle").innerHTML = "AIRPORTS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Airports");

    var grid_airports = main_layout.cells("a").attachGrid();
    grid_airports.setIconsPath('libraries/dhtmlx/imgs/');
    grid_airports.setHeader("Name,Country,Used for Transfer Rates");
    grid_airports.setColumnIds("airportname,country_name,usedfortransferrates");
    grid_airports.setColTypes("ro,ro,ch");
    grid_airports.setInitWidths("*,250,100");
    grid_airports.setColAlign("left,left,center");
    grid_airports.setColSorting('str,str,int');
    grid_airports.attachHeader("#text_filter,#text_filter,#select_filter");
    grid_airports.setEditable(false);
    grid_airports.init();


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
            form_airports.clear();
            form_airports.setItemValue("id", "-1");
            
            if(default_country_id != "-1")
            {
                cboCountry.setComboValue(default_country_id);
            }
            
            popupwin_airports.setModal(true);
            popupwin_airports.center();
            popupwin_airports.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_airports.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsAirports.item(uid);
            form_airports.setFormData(data);

            popupwin_airports.setModal(true);
            popupwin_airports.center();
            popupwin_airports.show();


        } else if (id == "delete")
        {
            var gid = grid_airports.getSelectedRowId();
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
                        dhtmlxAjax.post("php/api/airports/deleteairport.php", params, function (loader) {

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
                                    grid_airports.deleteRow(gid);
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


    var dsAirports = new dhtmlXDataStore();
    dsAirports.load("php/api/airports/airportgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_airports.sync(dsAirports);

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
    
    var popupwin_airports = dhxWins.createWindow("popupwin_airports", 50, 50, 500, 240);
    popupwin_airports.setText("Airport Details:");
    popupwin_airports.denyResize();
    popupwin_airports.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_airports"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "airportname", label: "Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "combo", name: "countryfk", label: "Country:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/",
        },
        {type: "checkbox", name: "usedfortransferrates", label: "Used for Transfer Rates:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
            
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var airportlayout = popupwin_airports.attachLayout("1C");

    airportlayout.cells("a").hideHeader();

    var form_airports = airportlayout.cells("a").attachForm(str_frm_ug);



    form_airports.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_airports.setModal(false);
            popupwin_airports.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_airports.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Airport",
                    callback: function () {
                    }
                });
                return;
            }
            
            if(!utils_validate_autocompletecombo(cboCountry))
            {
                dhtmlx.alert({
                    text: "Please select a valid Country!",
                    type: "alert-warning",
                    title: "Save Airport",
                    callback: function () {
                        cboCountry.openSelect();
                    }
                });
                return;
            }


            airportlayout.cells("a").progressOn();

            form_airports.setItemValue("token", global_token);

            form_airports.send("php/api/airports/saveairport.php", "post", function (loader)
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
                        airportlayout.cells("a").progressOff();
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
                        airportlayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsAirports.clearAll();
                        grid_airports.clearAll();

                        dsAirports.load("php/api/airports/airportgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_airports.sync(dsAirports);
                            popupwin_airports.setModal(false);
                            popupwin_airports.hide();
                            airportlayout.cells("a").progressOff();

                            grid_airports.selectRowById(json_obj.ID, false, true, false);
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
                        airportlayout.cells("a").progressOff();
                    }
                }
            });
        }
    });

    var cboCountry = form_airports.getCombo("countryfk");
    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/combos/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCountry.dataCount(); i++)
        {
            var item = dsCountry.item(dsCountry.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboCountry.addOption([{value: value, text: txt, img_src: "images/country.png"}]);
        }

        cboCountry.readonly(false);
        cboCountry.enableFilteringMode(true);
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

    popupwin_airports.hide();

}