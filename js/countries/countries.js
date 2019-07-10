var countries_obj = new countries();

function countries()
{

    document.getElementById("aTitle").innerHTML = "COUNTRIES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_countries = main_layout.cells("a").attachGrid();
    grid_countries.setIconsPath('libraries/dhtmlx/imgs/');
    grid_countries.setHeader("Code2,Code3,Code Num,Country,Continent,Latitude,Longitude,Default,Used for Hotels");
    grid_countries.setColumnIds("countrycode_2,countrycode_3,country_numeric,country_name,continent_name,lat,lon,display_default,used_for_hotels");
    grid_countries.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ch");
    grid_countries.setInitWidths("70,70,70,*,200,90,90,90,80");
    grid_countries.setColAlign("center,center,center,left,left,right,right,center,center");
    grid_countries.setColSorting('str,str,int,str,str,int,int,str,int');
    grid_countries.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter,#select_filter");
    grid_countries.init();


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
            form_countries.clear();
            form_countries.setItemValue("id", "-1");
            popupwin_countries.setModal(true);
            popupwin_countries.center();
            popupwin_countries.show();
        } else if (id == "export")
        {
            grid_countries.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_countries.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsCountries.item(uid);
            form_countries.setFormData(data);

            popupwin_countries.setModal(true);
            popupwin_countries.center();
            popupwin_countries.show();


        } else if (id == "delete")
        {
            var gid = grid_countries.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Country",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/countries/deletecountry.php", params, function (loader) {

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
                                    grid_countries.deleteRow(gid);
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


    var dsCountries = new dhtmlXDataStore();
    dsCountries.load("php/api/countries/countrygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_countries.sync(dsCountries);

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

    var popupwin_countries = dhxWins.createWindow("popupwin_countries", 50, 50, 650, 400);
    popupwin_countries.setText("Country Details:");
    popupwin_countries.denyResize();
    popupwin_countries.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_countries"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "countrycode_2", label: "Code Alpha 2:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, maxLength: 2
        },
        {type: "input", name: "countrycode_3", label: "Country Code:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, maxLength: 3
        },
        {type: "input", name: "country_numeric", label: "Code Numeric:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true, maxLength: 4,
            validate: "ValidInteger"
        },
        {type: "input", name: "country_name", label: "Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "combo", name: "continent", label: "Continent:", labelWidth: "130",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
        },
        {type: "input", name: "lat", label: "Latitude:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidNumeric"
        },
        {type: "input", name: "lon", label: "Longitude:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidNumeric"
        },
        {type: "select", name: "default_selected", label: "Default:", labelHeight: "22", labelWidth: "130",
            inputLeft: "10", inputTop: "10", inputWidth: "100", required: true, options: [
                {text: "YES", value: "Y"},
                {text: "NO", value: "N"}
            ]
        },
        {type: "checkbox", name: "used_for_hotels", label: "Used for Hotels:", labelHeight: "22", labelWidth: "130",
            inputLeft: "10", inputTop: "0", inputWidth: "100"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var countrylayout = popupwin_countries.attachLayout("1C");

    countrylayout.cells("a").hideHeader();

    var form_countries = countrylayout.cells("a").attachForm(str_frm_ug);
    
    var cboContinent = form_countries.getCombo("continent");
    var dsContinent = new dhtmlXDataStore();
    dsContinent.load("php/api/combos/continent_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsContinent.dataCount(); i++)
        {
            var item = dsContinent.item(dsContinent.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboContinent.addOption([{value: value, text: txt, img_src: "images/continent.png"}]);
        }

        cboContinent.readonly(true);
    });


    form_countries.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_countries.setModal(false);
            popupwin_countries.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_countries.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Country",
                    callback: function () {
                    }
                });
                return;
            }


            countrylayout.cells("a").progressOn();

            form_countries.setItemValue("token", global_token);

            form_countries.send("php/api/countries/savecountry.php", "post", function (loader)
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
                        countrylayout.cells("a").progressOff();
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
                        countrylayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsCountries.clearAll();
                        grid_countries.clearAll();

                        dsCountries.load("php/api/countries/countrygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_countries.sync(dsCountries);
                            popupwin_countries.setModal(false);
                            popupwin_countries.hide();
                            countrylayout.cells("a").progressOff();

                            grid_countries.selectRowById(json_obj.ID, false, true, false);
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
                        countrylayout.cells("a").progressOff();
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


    popupwin_countries.hide();

}