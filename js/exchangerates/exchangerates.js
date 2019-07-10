var exchangerates_obj = new exchangerates();

function exchangerates()
{


    document.getElementById("aTitle").innerHTML = "EXCHANGE RATES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");
    main_layout.cells("a").hideHeader();

    var inner_layout = main_layout.cells("a").attachLayout("2U");


    inner_layout.cells('a').setText("Currency");
    inner_layout.cells('a').setWidth(600);
    inner_layout.cells('b').setText("Rates");

    var grid_currency = inner_layout.cells("a").attachGrid();
    grid_currency.setIconsPath('libraries/dhtmlx/imgs/');
    grid_currency.setHeader("Code,Currency,Use For Cost Price,Use for Base Calculations");
    grid_currency.setColumnIds("currency_code,currency_name,use_for_costprice,use_for_base_conversions");
    grid_currency.setColTypes("ro,ro,ch,ch");
    grid_currency.setInitWidths("100,*,100,100");
    grid_currency.setColAlign("center,left,center,center");
    grid_currency.setColSorting('str,str,int,int');
    grid_currency.attachEvent("onRowSelect", onCurrencySelect);
    grid_currency.setEditable(false);
    grid_currency.init();


    var toolbar_currency = inner_layout.cells("a").attachToolbar();
    toolbar_currency.setIconsPath("images/");
    toolbar_currency.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_currency.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_currency.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_currency.setIconSize(32);



    var grid_rate = inner_layout.cells("b").attachGrid();
    grid_rate.setIconsPath('libraries/dhtmlx/imgs/');
    grid_rate.setHeader("From,To,Rate");
    grid_rate.setColumnIds("dtfrom_disp,dtto_disp,exchange_rate");
    grid_rate.setColTypes("ro,ro,ro");
    grid_rate.setInitWidths("130,130,*");
    grid_rate.setColAlign("left,left,left");
    grid_rate.setColSorting('date,date,int');
    grid_rate.init();

    var toolbar_rate = inner_layout.cells("b").attachToolbar();
    toolbar_rate.setIconsPath("images/");
    toolbar_rate.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_rate.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_rate.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_rate.setIconSize(32);

    applyrights();


    toolbar_currency.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_currency.clear();
            form_currency.setItemValue("id", "-1");

            popupwin_currency.setModal(true);
            popupwin_currency.center();
            popupwin_currency.show();


        } else if (id == "modify")
        {
            var uid = grid_currency.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsCurrency.item(uid);
            form_currency.setFormData(data);

            popupwin_currency.setModal(true);
            popupwin_currency.center();
            popupwin_currency.show();


        } else if (id == "delete")
        {
            var gid = grid_currency.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Currency",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/exchangerates/deletecurrency.php", params, function (loader) {

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
                                    grid_currency.deleteRow(gid);
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


    toolbar_rate.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            var cid = grid_currency.getSelectedRowId();
            if (!cid)
            {
                dhtmlx.alert({
                    text: "Please select a Currency first!",
                    type: "alert-warning",
                    title: "New Rate",
                    callback: function () {
                    }
                });
                return;
            }

            var code = dsCurrency.item(cid).currency_code;

            form_rate.clear();
            form_rate.setItemValue("id", "-1");
            form_rate.setItemValue("ratefk", cid);
            form_rate.setItemValue("code", code);

            form_rate.getCalendar("dtfrom").clearSensitiveRange();
            form_rate.getCalendar("dtto").clearSensitiveRange();

            popupwin_rate.setModal(true);
            popupwin_rate.center();
            popupwin_rate.show();


        } else if (id == "modify")
        {
            var uid = grid_rate.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var cid = grid_currency.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            var code = dsCurrency.item(cid).currency_code;

            var data = dsRate.item(uid);
            form_rate.setFormData(data);
            form_rate.setItemValue("code", code);

            checkRange("dtfrom", form_rate.getItemValue("dtfrom"));
            checkRange("dtto", form_rate.getItemValue("dtto"));

            popupwin_rate.setModal(true);
            popupwin_rate.center();
            popupwin_rate.show();

        } else if (id == "delete")
        {
            var gid = grid_rate.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Rate",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/exchangerates/deleterate.php", params, function (loader) {

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
                                    grid_rate.deleteRow(gid);
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



    var dsCurrency = new dhtmlXDataStore();
    dsCurrency.load("php/api/exchangerates/currencygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_currency.sync(dsCurrency);

    });


    var dsRate = new dhtmlXDataStore();


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

    var popupwin_currency = dhxWins.createWindow("popupwin_currency", 50, 50, 500, 250);
    popupwin_currency.setText("Currency Details:");
    popupwin_currency.denyResize();
    popupwin_currency.denyPark();


    var popupwin_rate = dhxWins.createWindow("popupwin_rate", 50, 50, 500, 280);
    popupwin_rate.setText("Rate Details:");
    popupwin_rate.denyResize();
    popupwin_rate.denyPark();


    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_currency"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "currency_code", label: "Code:", labelWidth: "160",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "currency_name", label: "Name:", labelWidth: "160",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "checkbox", name: "use_for_costprice", label: "Use For Cost Price:",
            labelWidth: "160",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "0", inputTop: "0"},
        {type: "checkbox", name: "use_for_base_conversions", label: "Use for Base Calculations:",
            labelWidth: "160",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "0", inputTop: "0"},
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var currencylayout = popupwin_currency.attachLayout("1C");

    currencylayout.cells("a").hideHeader();

    var form_currency = currencylayout.cells("a").attachForm(str_frm_ug);



    form_currency.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_currency.setModal(false);
            popupwin_currency.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_currency.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Currency",
                    callback: function () {
                    }
                });
                return;
            }


            currencylayout.cells("a").progressOn();

            form_currency.setItemValue("token", global_token);

            form_currency.send("php/api/exchangerates/savecurrency.php", "post", function (loader)
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
                        currencylayout.cells("a").progressOff();
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
                        currencylayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsCurrency.clearAll();
                        grid_currency.clearAll();

                        dsCurrency.load("php/api/exchangerates/currencygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_currency.sync(dsCurrency);
                            popupwin_currency.setModal(false);
                            popupwin_currency.hide();
                            currencylayout.cells("a").progressOff();

                            grid_currency.selectRowById(json_obj.ID, false, true, false);
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
                        currencylayout.cells("a").progressOff();
                    }
                }
            });
        }
    });


    var str_frm_rt = [
        {type: "settings", position: "label-left", id: "form_currency"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "hidden", name: "ratefk"},
        {type: "input", name: "code", label: "Code:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
        },
        {type: "calendar", name: "dtfrom", label: "Valid From:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        },
        {type: "calendar", name: "dtto", label: "Valid To:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        },
        {type: "input", name: "exchange_rate", label: "Rate:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidCurrency"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var ratelayout = popupwin_rate.attachLayout("1C");

    ratelayout.cells("a").hideHeader();

    var form_rate = ratelayout.cells("a").attachForm(str_frm_rt);

    form_rate.getInput("code").style.fontWeight = "bold";
    form_rate.getInput("code").style.backgroundColor = "#F3E2A9";

    jQuery(function ($) {
        $("[name='dtto']").mask("99-99-9999");
    });

    jQuery(function ($) {
        $("[name='dtfrom']").mask("99-99-9999");
    });

    form_rate.attachEvent("onChange", function (name, value) {
        if (name == "dtfrom" || name == "dtto")
            checkRange(name, value);
    });

    form_rate.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_rate.setModal(false);
            popupwin_rate.hide();
        }
        if (name == "cmdSave")
        {

            if (!form_rate.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Rate",
                    callback: function () {
                    }
                });
                return;
            }

            if (!utils_isDate(form_rate.getItemValue("dtfrom", true)))
            {
                dhtmlx.alert({
                    text: "Please Enter a valid Date From in dd-mm-yyyy format!",
                    type: "alert-warning",
                    title: "Save",
                    callback: function () {
                        form_rate.setItemFocus("dtfrom");
                    }
                });
                return;
            }

            if (!utils_isDate(form_rate.getItemValue("dtto", true)))
            {
                dhtmlx.alert({
                    text: "Please Enter a valid Date To in dd-mm-yyyy format!",
                    type: "alert-warning",
                    title: "Save",
                    callback: function () {
                        form_rate.setItemFocus("dtto");
                    }
                });
                return;
            }


            ratelayout.cells("a").progressOn();

            form_rate.setItemValue("token", global_token);

            form_rate.send("php/api/exchangerates/saverate.php", "post", function (loader)
            {
                ratelayout.cells("a").progressOff();


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
                        ratelayout.cells("a").progressOff();
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
                        ratelayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        grid_rate.clearAll();
                        dsRate.clearAll();
                        dsRate = null;
                        dsRate = new dhtmlXDataStore();

                        inner_layout.cells("b").progressOn();

                        popupwin_rate.setModal(false);
                        popupwin_rate.hide();
                        dsRate.load("php/api/exchangerates/rategrid.php?t=" + encodeURIComponent(global_token) + "&cid=" + grid_currency.getSelectedRowId(), "json", function () {
                            grid_rate.sync(dsRate);
                            inner_layout.cells("b").progressOff();
                            grid_rate.selectRowById(json_obj.ID, false, true, false);
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
                        ratelayout.cells("a").progressOff();
                    }
                }
            });
        }
    });




    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {
            if (json_rights[i].PROCESSNAME == "ADD CURRENCY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_currency.disableItem("new");
                toolbar_currency.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY CURRENCY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_currency.disableItem("modify");
                toolbar_currency.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE CURRENCY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_currency.disableItem("delete");
                toolbar_currency.setItemToolTip("delete", "Not Allowed");
            }

            if (json_rights[i].PROCESSNAME == "ADD RATE" && json_rights[i].ALLOWED == "N")
            {
                toolbar_rate.disableItem("new");
                toolbar_rate.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY RATE" && json_rights[i].ALLOWED == "N")
            {
                toolbar_rate.disableItem("modify");
                toolbar_rate.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE RATE" && json_rights[i].ALLOWED == "N")
            {
                toolbar_rate.disableItem("delete");
                toolbar_rate.setItemToolTip("delete", "Not Allowed");
            }

        }
    }


    function onCurrencySelect(rid, cid)
    {
        grid_rate.clearAll();
        dsRate.clearAll();
        dsRate = null;
        dsRate = new dhtmlXDataStore();

        inner_layout.cells("b").progressOn();
        dsRate.load("php/api/exchangerates/rategrid.php?t=" + encodeURIComponent(global_token) + "&cid=" + rid, "json", function () {
            grid_rate.sync(dsRate);
            inner_layout.cells("b").progressOff();

        });
    }

    function checkRange(name, value) {
        if (name == "dtto") {
            form_rate.getCalendar("dtfrom").setSensitiveRange(null, value);
        } else {
            form_rate.getCalendar("dtto").setSensitiveRange(value, null);
        }
    }

    //==============
    popupwin_currency.hide();
    popupwin_rate.hide();

}