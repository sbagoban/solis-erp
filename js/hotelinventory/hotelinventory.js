var hotelinventory_obj = new hotelinventory();

function hotelinventory()
{
    var _arr_dates = [];

    var popupwin = null;
    var pop_grid = null;
    var pop_layout = null;
    var pop_form = null;
    var pop_toolbar = null;
    
    var icon_size = "25px";

    var inventorylayout = new dhtmlXLayoutObject("main_body", "1C");
    inventorylayout.cells("a").hideHeader();

    var dsInventory = new dhtmlXDataStore();

    var _dsRooms = new dhtmlXDataStore();
    var _dsTO = new dhtmlXDataStore();

    var grid_inventory = inventorylayout.cells("a").attachGrid();
    grid_inventory.setIconsPath('libraries/dhtmlx/imgs/');
    grid_inventory.setHeader("Type,Market Title,Rooms,Tour Operator,Dates");
    grid_inventory.setColumnIds("inventory_type,title,rooms,toname,dates");
    grid_inventory.setColTypes("ro,ro,ro,ro,ro");
    grid_inventory.setInitWidths("100,400,200,200,150");
    grid_inventory.setColAlign("center,center,center,center,center");
    grid_inventory.setColSorting("str,str,str,str,str");
    grid_inventory.attachHeader("#select_filter,#text_filter,#text_filter,#select_filter,#text_filter");
    grid_inventory.setEditable(false);
    grid_inventory.enableMultiline(true);
    grid_inventory.init();

    var toolbar_inventory = inventorylayout.cells("a").attachToolbar();
    toolbar_inventory.setIconsPath("images/");
    toolbar_inventory.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_inventory.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_inventory.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_inventory.addSpacer("delete");
    toolbar_inventory.addButton("back", 5, "Back to Hotels", "exit.png", "exit.png");
    toolbar_inventory.setIconSize(32);

    toolbar_inventory.attachEvent("onClick", function (id) {

        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        } else if (id == "new")
        {
            newInventory();
        } else if (id == "modify")
        {
            var cid = grid_inventory.getSelectedRowId();
            if (!cid)
            {
                return;
            }


            modifyInventory(cid);

        } else if (id == "delete")
        {
            var cid = grid_inventory.getSelectedRowId();
            if (!cid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Inventory?",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "id=" + cid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);

                        inventorylayout.cells("a").progressOn();
                        dhtmlxAjax.post("php/api/hotelinventory/deleteinventory.php", params, function (loader) {
                            inventorylayout.cells("a").progressOff();

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DELETE INVENTORY",
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
                                        title: "DELETE INVENTORY",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    grid_inventory.deleteRow(cid);

                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DELETE INVENTORY",
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

    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {
            if (json_rights[i].PROCESSNAME == "ADD INVENTORY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_inventory.disableItem("new");
                toolbar_inventory.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY INVENTORY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_inventory.disableItem("modify");
                toolbar_inventory.setItemToolTip("modify", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE INVENTORY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_inventory.disableItem("delete");
                toolbar_inventory.setItemToolTip("delete", "Not Allowed");
            }
        }
    }

    applyrights();
    loadHotelInventory("");

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
            var y = Math.max(body.scrollHeight, body.offsetHeight,
                    html.clientHeight, html.scrollHeight, html.offsetHeight);
            y -= 150;

            $("#main_body").height(y - 25);
            $("#main_body").width(x - 20);

            inventorylayout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(inventorylayout.cells("a"));

    var popupwin_inventory = dhxWins.createWindow("popupwin_inventory", 50, 50, 700, 440);
    popupwin_inventory.setText("Inventory Details:");

    var x = $("#main_body").parent().width() - 20;
    var body = document.body,
            html = document.documentElement;
    var y = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
    y -= 170;

    popupwin_inventory.setDimension(x, y);

    popupwin_inventory.denyResize();
    popupwin_inventory.denyPark();
    popupwin_inventory.button("close").hide();

    var detailsLayout = popupwin_inventory.attachLayout("2E");
    detailsLayout.cells("a").hideHeader();
    detailsLayout.cells("b").hideHeader();

    detailsLayout.cells("a").setHeight(800);
    detailsLayout.cells("b").setHeight(50);

    detailsLayout.cells("a").fixSize(true, true);
    detailsLayout.cells("b").fixSize(true, true);


    var tab_inventory = detailsLayout.cells("a").attachTabbar();
    tab_inventory.addTab("details", "<b>Details</b>", "180px", '');
    tab_inventory.addTab("dates", "<b>Dates</b>", "180px", '');
    tab_inventory.setTabActive("details");

    tab_inventory.attachEvent("onTabClick", function (id, last_id) {
        hidePopUps();
        if (id == "dates")
        {
            cboView.setComboValue("calendar");
            loadView("calendar");
        }
        return true;
    });

    var str_details = [
        {type: "settings", position: "label-left", id: "str_details"},

        {type: "hidden", name: "hotelfk"},
        {type: "hidden", name: "id"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "inventory_type", label: "Type:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},
        {type: "block", width: 900, list: [
                {type: "input", name: "title", label: "Market Title:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }]},

        {type: "block", width: 900, list: [
                {type: "input", name: "market_countries_display", label: "Countries:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "rooms_display", label: "Rooms:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "rooms_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadRooms", tooltip: "Select Hotel Rooms", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "to_display", label: "Tour Operators:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "to_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadTOs", tooltip: "Select Tour Operators", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "release_days_value", label: "Release Days:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidInteger"
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "release_date_value", label: "Release Date:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]},

        {type: "block", width: 700, list: [
                {type: "fieldset", width: 700, label: "Authorisation Reservation", list: [
                        {type: "block", width: 700, list: [
                                {type: "input", name: "autho_reserve_days_from", label: "Days From:",
                                    labelWidth: "110",
                                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    validate: "ValidInteger"
                                },
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "input", name: "autho_reserve_days_to", label: "Days To:",
                                    labelWidth: "80",
                                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    validate: "ValidInteger"
                                }
                            ]},

                        {type: "block", width: 900, list: [
                                {type: "input", name: "autho_reserve_date_from", label: "Date From:",
                                    labelWidth: "110",
                                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "autho_reserve_time_from", label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"
                                },
                                {type: "newcolumn"},
                                {type: "newcolumn"},
                                {type: "input", name: "autho_reserve_date_to", label: "Date To:",
                                    labelWidth: "80",
                                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "autho_reserve_time_to", label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"
                                },
                            ]}

                    ]}
            ]},

        {type: "block", width: 900, list: [
                {type: "editor", name: "note", label: "Notes:", labelWidth: "50",
                    labelHeight: "22", inputWidth: "700", inputHeight: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]}
    ];


    var form_details = tab_inventory.cells("details").attachForm(str_details);
    form_details.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_details, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_details, "Rooms", "rooms_display", "rooms_ids", _dsRooms, null);
        } else if (name == "cmdLoadTOs")
        {
            showPopUp(form_details, "Tour Operators", "to_display", "to_ids", _dsTO, null);
        }
    });

    jQuery(function ($) {
        $("[name='release_date_value']").mask("99-99-9999");
        $("[name='autho_reserve_date_from']").mask("99-99-9999");
        $("[name='autho_reserve_date_to']").mask("99-99-9999");
        $("[name='autho_reserve_time_from']").mask("99:99");
        $("[name='autho_reserve_time_to']").mask("99:99");
    });


    var cboType = form_details.getCombo("inventory_type");
    cboType.enableOptionAutoPositioning(true);
    cboType.readonly(true);
    cboType.addOption([{value: "free_sales", text: "Free Sales", img_src: "images/solution.png"}]);
    cboType.addOption([{value: "stop_sales", text: "Stop Sales", img_src: "images/solution.png"}]);
    cboType.addOption([{value: "allotments", text: "Allotments", img_src: "images/solution.png"}]);
    cboType.addOption([{value: "on_request", text: "On Request", img_src: "images/solution.png"}]);
    cboType.addOption([{value: "renovation", text: "Renovation", img_src: "images/solution.png"}]);

    var str_form_details_buttons = [
        {type: "settings", position: "label-left", id: "form_details_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Inventory", value: "Return to Inventory", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Inventory", value: "Save Inventory", width: "230", height: "60", offsetLeft: 0}
            ]}];

    var form_details_buttons = detailsLayout.cells("b").attachForm(str_form_details_buttons);
    form_details_buttons.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdClose")
        {
            popupwin_inventory.hide();
            popupwin_inventory.setModal(false);
        } else if (name == "cmdSave")
        {
            saveInventory();
        }
    });


    var datesLayout = tab_inventory.cells("dates").attachLayout("2E");
    datesLayout.cells("a").hideHeader();
    datesLayout.cells("b").hideHeader();

    datesLayout.cells("b").setHeight(800);
    datesLayout.cells("a").setHeight(35);

    datesLayout.cells("a").fixSize(true, true);
    datesLayout.cells("b").fixSize(true, true);

    var str_dates_combo = [
        {type: "settings", position: "label-left", id: "str_dates_combo"},
        {type: "combo", name: "cboview", label: "View Mode:",
            labelWidth: "100",
            labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        }
    ];


    var form_dates_view = datesLayout.cells("a").attachForm(str_dates_combo);

    var cboView = form_dates_view.getCombo("cboview");
    cboView.enableOptionAutoPositioning(true);
    cboView.readonly(true);
    cboView.addOption([{value: "calendar", text: "Calendar View", img_src: "images/view_mode_icons_24.png"}]);
    cboView.addOption([{value: "grid", text: "Grid View", img_src: "images/view_mode_list_24.png"}]);
    cboView.attachEvent("onChange", onViewModeChange);

    var grid_dates = datesLayout.cells("b").attachGrid();
    grid_dates.setIconsPath('libraries/dhtmlx/imgs/');
    grid_dates.init();

    var toolbar_dates = datesLayout.cells("b").attachToolbar();
    toolbar_dates.setIconsPath("images/");
    toolbar_dates.addButton("add", 1, "Add Dates Range", "add.png", "add.png");
    toolbar_dates.addButton("remove", 2, "Remove Dates Range", "delete.png", "delete.png");
    toolbar_dates.addButton("delete", 3, "Delete Selected Date(s)", "delete.png", "delete.png");
    toolbar_dates.setIconSize(32);

    toolbar_dates.attachEvent("onClick", function (id) {

        hidePopUps();
        if (id == "add")
        {
            pop_addrange.show();
        } else if (id == "remove")
        {
            pop_removerange.show();
        } else if (id == "delete")
        {
            var date = grid_dates.getSelectedRowId();
            if (!date)
            {
                return;
            }

            dhtmlx.confirm({
                title: "Delete Date?",
                type: "confirm",
                text: "Confirm Date Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        removeDateFromArray(date);
                        grid_dates.deleteRow(date);
                    }
                }
            });
        }
    });

    var pop_addrange = new dhtmlXPopup({
        toolbar: toolbar_dates,
        id: "add"
    });

    var form_addrange = pop_addrange.attachForm([
        {type: "block", width: 400, list: [
                {type: "calendar", name: "dtfrom", label: "Date From:",
                    labelWidth: "70", dateFormat: "%d-%m-%Y",
                    labelHeight: "22", inputWidth: "90", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "newcolumn"},
                {type: "calendar", name: "dtto", label: "Date To:",
                    labelWidth: "60", dateFormat: "%d-%m-%Y",
                    labelHeight: "22", inputWidth: "90", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]},
        {type: "block", width: 400, list: [
                {type: "button", name: "cmdCreate", tooltip: "Create Date Ranges", value: "Create Dates", width: "200", height: "60", offsetLeft: 0}
            ]}
    ]);

    form_addrange.attachEvent("onButtonClick", function (name) {
        if (name == "cmdCreate")
        {
            //create dates between ranges
            var dtfrom = form_addrange.getItemValue("dtfrom", true);
            var dtto = form_addrange.getItemValue("dtto", true);

            if (!utils_isDate(dtfrom))
            {
                dhtmlx.alert({
                    text: "Invalid Date From!",
                    type: "alert-warning",
                    title: "Create Dates",
                    callback: function () {
                        form_addrange.setItemFocus("dtfrom");
                    }
                });

                return;
            }

            if (!utils_isDate(dtto))
            {
                dhtmlx.alert({
                    text: "Invalid Date To!",
                    type: "alert-warning",
                    title: "Create Dates",
                    callback: function () {
                        form_addrange.setItemFocus("dtto");
                    }
                });

                return;
            }


            if (!utils_validateDateOrder(dtfrom, dtto))
            {
                dhtmlx.alert({
                    text: "Invalid Date Order!",
                    type: "alert-warning",
                    title: "Create Dates",
                    callback: function () {
                        form_addrange.setItemFocus("dtto");
                    }
                });

                return;
            }

            dhtmlx.confirm({
                title: "Create Dates?",
                type: "confirm",
                text: "Confirm Dates Creation?",
                callback: function (tf) {
                    if (tf)
                    {
                        createDates(dtfrom, dtto);
                    }
                }
            });


        }
    });

    //=========

    var pop_removerange = new dhtmlXPopup({
        toolbar: toolbar_dates,
        id: "remove"
    });

    var form_removerange = pop_removerange.attachForm([
        {type: "block", width: 400, list: [
                {type: "calendar", name: "dtfrom", label: "Date From:",
                    labelWidth: "70", dateFormat: "%d-%m-%Y",
                    labelHeight: "22", inputWidth: "90", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "newcolumn"},
                {type: "calendar", name: "dtto", label: "Date To:",
                    labelWidth: "60", dateFormat: "%d-%m-%Y",
                    labelHeight: "22", inputWidth: "90", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]},
        {type: "block", width: 400, list: [
                {type: "button", name: "cmdRemove", tooltip: "Remove Date within Range", value: "Remove Dates", width: "200", height: "60", offsetLeft: 0}
            ]}
    ]);

    form_removerange.attachEvent("onButtonClick", function (name) {
        if (name == "cmdCreate")
        {
            //create dates between ranges
            var dtfrom = form_removerange.getItemValue("dtfrom", true);
            var dtto = form_removerange.getItemValue("dtto", true);

            if (!utils_isDate(dtfrom))
            {
                dhtmlx.alert({
                    text: "Invalid Date From!",
                    type: "alert-warning",
                    title: "Remove Dates",
                    callback: function () {
                        form_removerange.setItemFocus("dtfrom");
                    }
                });

                return;
            }

            if (!utils_isDate(dtto))
            {
                dhtmlx.alert({
                    text: "Invalid Date To!",
                    type: "alert-warning",
                    title: "Remove Dates",
                    callback: function () {
                        form_removerange.setItemFocus("dtto");
                    }
                });

                return;
            }


            if (!utils_validateDateOrder(dtfrom, dtto))
            {
                dhtmlx.alert({
                    text: "Invalid Date Order!",
                    type: "alert-warning",
                    title: "Remove Dates",
                    callback: function () {
                        form_removerange.setItemFocus("dtto");
                    }
                });

                return;
            }

            dhtmlx.confirm({
                title: "Remove Dates?",
                type: "confirm",
                text: "Confirm Dates Removal?",
                callback: function (tf) {
                    if (tf)
                    {
                        removeDates(dtfrom, dtto);
                    }
                }
            });
        }
    });

    //===============================================================

    function onViewModeChange()
    {
        hidePopUps();
        var view = cboView.getSelectedValue();
        loadView(view);
    }

    function loadView(view)
    {
        hidePopUps();
        grid_dates.clearAll(true);
        grid_dates = null;
        grid_dates = datesLayout.cells("b").attachGrid();
        grid_dates.setIconsPath('libraries/dhtmlx/imgs/');
        grid_dates.enableAlterCss("", "");
        grid_dates.enableColSpan(true);
        grid_dates.enableEditTabOnly(true);
        grid_dates.enableEditEvents(true, true, true);
        grid_dates.attachEvent("onRowSelect", onGridDatesRowSelect);
        grid_dates.attachEvent("onEditCell", onGridDatesEdit);
        grid_dates.init();

        if (view == "calendar")
        {
            grid_dates.enableMultiselect(false);
            toolbar_dates.hideItem("delete");
            var url = "php/api/hotelinventory/grid_calendarview_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&myicon=" +  decideImage() +
                    "&myicon_size=" + icon_size + 
                    "&dates=" + encodeURIComponent(JSON.stringify(_arr_dates));

            datesLayout.progressOn();
            grid_dates.loadXML(url, function () {
                datesLayout.progressOff();
            });
        } else
        {
            grid_dates.enableMultiselect(true);
            toolbar_dates.showItem("delete");
            var url = "php/api/hotelinventory/grid_gridview_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&dates=" + encodeURIComponent(JSON.stringify(_arr_dates));

            datesLayout.progressOn();
            grid_dates.loadXML(url, function () {
                datesLayout.progressOff();
                grid_dates.groupBy(grid_dates.getColIndexById("month"));
            });
        }


    }

    function onGridDatesEdit(stage, rId, cInd, nValue, oValue) {
        hidePopUps();
        if (stage == 2)
        {
            if (nValue != oValue)
            {
                //reload the calendar
                var month = grid_dates.cells("rw_title", grid_dates.getColIndexById("Wed")).getValue();
                var year = grid_dates.cells("rw_title", grid_dates.getColIndexById("Fri")).getValue();
                var url = "php/api/hotelinventory/grid_calendarview_xml.php?" +
                        "t=" + encodeURIComponent(global_token) +
                        "&myicon=" +  decideImage() +
                        "&dates=" + encodeURIComponent(JSON.stringify(_arr_dates)) +
                        "&focus_month=" + month + "&focus_year=" + year;

                datesLayout.progressOn();
                grid_dates.loadXML(url, function () {
                    datesLayout.progressOff();
                });
            }
        }
        return true;
    }


    function modifyInventory(invid)
    {
        popupwin_inventory.center();
        popupwin_inventory.setModal(true);
        popupwin_inventory.show();

        tab_inventory.setTabActive("details");

        form_details.clear();
        _arr_dates = [];

        form_details.setItemValue("id", invid);
        form_details.setItemValue("hotelfk", global_hotel_id);

        //load the spo
        detailsLayout.progressOn();
        var params = "invid=" + invid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelinventory/loadinventory.php", params, function (loader) {
            detailsLayout.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD INVENTORY",
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
                        title: "LOAD INVENTORY",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {

                    console.log(json_obj);
                    form_details.setFormData(json_obj.INVENTORY.DETAILS);
                    _arr_dates = json_obj.INVENTORY.DATES;

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD INVENTORY",
                        callback: function () {
                        }
                    });
                }

            }
        });

    }

    function newInventory()
    {
        popupwin_inventory.center();
        popupwin_inventory.setModal(true);
        popupwin_inventory.show();
        popupwin_inventory.setText("Inventory Details:");

        form_details.clear();
        form_details.setItemValue("id", "-1");
        form_details.setItemValue("hotelfk", global_hotel_id);
        _arr_dates = [];
    }

    function saveInventory()
    {
        if (!validateInventory())
        {
            return;
        }


        var params = "token=" + encodeURIComponent(global_token);

        //details
        var details = form_details.getFormData();
        params += "&details=" + encodeURIComponent(JSON.stringify(details));
        params += "&dates=" + encodeURIComponent(JSON.stringify(_arr_dates));

        tab_inventory.setTabActive("details");

        detailsLayout.progressOn();

        dhtmlxAjax.post("php/api/hotelinventory/saveinventory.php", params, function (loader) {
            detailsLayout.progressOff();

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
                    dhtmlx.alert({
                        text: "Save Successful",
                        type: "alert",
                        title: "SAVE",
                        callback: function () {
                        }
                    });

                    form_details.setItemValue("id", json_obj.ID);
                    loadHotelInventory(json_obj.ID);
                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                        }
                    });
                }
            }
        });


    }

    function loadPopupDs()
    {
        _dsRooms.load("php/api/hotelinventory/hotelroomsgrid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});
        _dsTO.load("php/api/hotelinventory/to_grid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});

    }

    function showPopUp(form, caller, inputdisplay, inputid, ds, callback)
    {

        popupwin = null;
        popupwin = dhxWins.createWindow("popupwin", 50, 50, 400, 400);
        popupwin.setText("");
        popupwin.denyResize();
        popupwin.denyPark();

        pop_layout = null;
        pop_layout = popupwin.attachLayout("2E");
        pop_layout.cells("a").hideHeader();
        pop_layout.cells("b").hideHeader();
        pop_layout.cells("a").setHeight(300);
        pop_layout.cells("a").fixSize(true, true);
        pop_layout.cells("b").fixSize(true, true);

        pop_form = null;
        pop_form = pop_layout.cells("b").attachForm([
            {type: "settings", position: "label-center", id: "pop_form"},
            {type: "button", name: "cmdApply", tooltip: "Select Checked Items", value: "Select Checked Items", width: "200", height: "40"}

        ]);
        pop_form.attachEvent("onButtonClick", function (name) {
            if (name == "cmdApply")
            {
                for (var i = 0; i < pop_grid.getColumnCount(); i++) {
                    var filter = pop_grid.getFilterElement(i);
                    if (filter)
                        filter.value = '';
                }
                pop_grid.filterByAll();


                var checkedids = pop_grid.getCheckedRows(pop_grid.getColIndexById("X"));
                var strvalues = "";

                if (checkedids != "")
                {
                    var first = true;
                    var arr_ids = checkedids.split(",");

                    for (var i = 0; i < arr_ids.length; i++)
                    {
                        var id = arr_ids[i];

                        var value = pop_grid.cells(id, pop_grid.getColIndexById("value")).getValue();
                        if (!first)
                        {
                            strvalues += " , ";
                        }
                        strvalues += value;

                        first = false;
                    }

                }

                form.setItemValue(inputdisplay, strvalues);
                form.setItemValue(inputid, checkedids);
                $("[name='" + inputdisplay + "']").prop('title', strvalues);
                popupwin.close();

                if (callback)
                {
                    callback(form, inputdisplay, inputid);
                }
            }
        });

        pop_grid = null;
        pop_grid = pop_layout.cells("a").attachGrid(300, 200);
        pop_grid.setIconsPath('libraries/dhtmlx/imgs/');
        pop_grid.setHeader(",Select Items");
        pop_grid.setColumnIds("X,value");
        pop_grid.setColTypes("ch,ro");
        pop_grid.setInitWidths("40,300");
        pop_grid.setColAlign("center,left");
        pop_grid.setColSorting('int,str');
        pop_grid.enableStableSorting(true);
        pop_grid.attachHeader("#master_checkbox,#text_filter");
        pop_grid.attachEvent("onRowSelect", function (rid, cid) {
            var selected = pop_grid.cells(rid, pop_grid.getColIndexById("X")).getValue();
            if (selected == 0)
            {
                selected = 1;
            } else
            {
                selected = 0;
            }

            pop_grid.cells(rid, pop_grid.getColIndexById("X")).setValue(selected);
        });
        pop_grid.init();

        pop_grid.sync(ds);


        var selectedids = utils_trim(form.getItemValue(inputid), " ");
        var arr_ids = selectedids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "" && pop_grid.getRowIndex(id) != "-1")
            {
                pop_grid.cells(id, pop_grid.getColIndexById("X")).setValue(1);
            }
        }
        pop_grid.sortRows(1, "int", "asc");
        pop_grid.sortRows(0, "int", "des");

        popupwin.show();
        popupwin.center();
        popupwin.setText(caller);
        popupwin_inventory.setModal(false);
        popupwin.setModal(true);
    }


    function showPopUpCountries(form, caller, inputdisplay, inputid)
    {
        var dim = popupwin_inventory.getDimension();
        var height = dim[1];

        popupwin = null;
        popupwin = dhxWins.createWindow("popupwin", 50, 50, 500, height);
        popupwin.setText("");
        popupwin.denyResize();
        popupwin.denyPark();

        pop_layout = null;
        pop_layout = popupwin.attachLayout("2E");
        pop_layout.cells("a").hideHeader();
        pop_layout.cells("b").hideHeader();
        pop_layout.cells("a").setHeight((height - 40));
        pop_layout.cells("a").fixSize(true, true);
        pop_layout.cells("b").fixSize(true, true);

        pop_form = null;
        pop_form = pop_layout.cells("b").attachForm([
            {type: "settings", position: "label-center", id: "pop_form"},
            {type: "button", name: "cmdApply", tooltip: "Select Checked Items", value: "Select Checked Items", width: "200", height: "40"}

        ]);
        pop_form.attachEvent("onButtonClick", function (name, command) {
            if (name == "cmdApply")
            {
                for (var i = 0; i < pop_grid.getColumnCount(); i++) {
                    var filter = pop_grid.getFilterElement(i);
                    if (filter)
                        filter.value = '';
                }
                pop_grid.filterByAll();

                //expland all rows
                for (var i = 0; i < pop_grid.getRowsNum(); i++) {
                    var rwid = pop_grid.getRowId(i);
                    pop_grid.openItem(rwid);
                }

                var checkedids = pop_grid.getCheckedRows(pop_grid.getColIndexById("X"));

                var strvalues = "";
                var strids = "";

                if (checkedids != "")
                {
                    var first = true;
                    var arr_ids = checkedids.split(",");
                    var count_selected = 0;

                    for (var i = 0; i < arr_ids.length; i++)
                    {
                        var id = arr_ids[i];

                        if (id.indexOf("market_") == -1)
                        {
                            var value = pop_grid.cells(id, pop_grid.getColIndexById("value")).getValue();

                            if (!first)
                            {
                                strvalues += " , ";
                                strids += ",";
                            }
                            strvalues += value;
                            strids += id;
                            count_selected++;
                            first = false;
                        }
                    }
                }

                form.setItemValue(inputdisplay, strvalues);
                form.setItemValue(inputid, strids);
                $("[name='market_countries_display']").prop('title', count_selected + " selected => " + strvalues);
                popupwin.close();
            }
        });

        pop_toolbar = null;
        pop_toolbar = pop_layout.cells("a").attachToolbar();
        pop_toolbar.setIconsPath("images/");
        pop_toolbar.setIconSize(32);
        pop_toolbar.addButton("selall", 1, "Select All", "zoom_in.png", "zoom_in.png");
        pop_toolbar.addButton("unselall", 2, "Unselect All", "zoom_out.png", "zoom_out.png");
        pop_toolbar.addSpacer("unselall");
        pop_toolbar.addButton("filter", 3, "Filter Selected", "zoom.png", "zoom.png");
        pop_toolbar.addButton("filter_clear", 4, "Clear Filter", "zoom_clear.png", "zoom_clear.png");
        pop_toolbar.setItemToolTip("selall", "Select All Countries");
        pop_toolbar.setItemToolTip("unselall", "Unselect All Countries");
        pop_toolbar.setItemToolTip("filter", "Show Only Selected Countries");
        pop_toolbar.setItemToolTip("filter_clear", "Clear Filter of Selected Countries");
        pop_toolbar.attachEvent("onClick", function (id) {
            if (id == "filter")
            {
                pop_grid.expandAll();
                pop_grid.filterByAll();
                pop_grid.filterTreeBy(pop_grid.getColIndexById("X"), 1, false);
            } else if (id == "filter_clear")
            {
                pop_grid.filterByAll();
            } else if (id == "selall")
            {
                pop_grid.expandAll();
                pop_grid.checkAll(true);
                pop_grid.collapseAll();
            } else if (id == "unselall")
            {
                pop_grid.expandAll();
                pop_grid.filterByAll();
                pop_grid.checkAll(false);
                pop_grid.collapseAll();
            }
        });

        pop_grid = null;
        pop_grid = pop_layout.cells("a").attachGrid(550, (height - 20));
        pop_grid.setIconsPath("libraries/dhtmlx/imgs/csh_vista/");
        pop_grid.setHeader("Select Markets or Countries,");
        pop_grid.setColumnIds("value,X");
        pop_grid.setColTypes("tree,ch");
        pop_grid.setInitWidths("400,40");
        pop_grid.setColAlign("left,center");
        pop_grid.setColSorting('str,int');
        pop_grid.attachHeader("#text_filter,");
        pop_grid.enableSmartXMLParsing(true);
        pop_grid.enableTreeGridLines();

        pop_grid.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
            if (cInd == 1)
            {
                return true;
            }
            return false;
        });

        pop_grid.enableAlterCss("", "");
        pop_grid.attachEvent("onFilterEnd", function (elements) {
            pop_grid.forEachRowA(function (id) {
                if (pop_grid.hasChildren(id))
                {
                    pop_grid.openItem(id);
                }
            });
        });
        pop_grid.attachEvent("onRowSelect", function (rid, cid) {
            if (pop_grid.hasChildren(rid))
            {
                var openstate = pop_grid.getOpenState(rid);
                if (!openstate)
                {
                    pop_grid.openItem(rid);
                } else
                {
                    pop_grid.closeItem(rid);
                }

            } else
            {
                var selected = pop_grid.cells(rid, pop_grid.getColIndexById("X")).getValue();
                if (selected == 0)
                {
                    selected = 1;
                } else
                {
                    selected = 0;
                }

                pop_grid.cells(rid, pop_grid.getColIndexById("X")).setValue(selected);
                popupcountries_checked(rid, cid, selected);
            }
            return true;
        });

        pop_grid.attachEvent("onCheck", function (rId, cInd, state) {
            popupcountries_checked(rId, cInd, state);
        });


        pop_grid.init();


        //load countries
        pop_layout.cells("a").progressOn();
        pop_grid.loadXML("php/api/hotelinventory/marketgridxml.php?t=" + encodeURIComponent(global_token), function () {
            pop_layout.cells("a").progressOff();

            var selectedids = utils_trim(form.getItemValue(inputid), " ");
            var arr_ids = selectedids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {
                var id = arr_ids[i];
                if (id != "" && pop_grid.getRowIndex(id) != "-1")
                {

                    pop_grid.cells(id, pop_grid.getColIndexById("X")).setValue(1);
                }
            }

            //check parent row if all children rows checked
            checkGridParents(pop_grid);

            pop_grid.collapseAll();
        });



        popupwin.show()
        popupwin.center();
        popupwin.setText(caller);
        popupwin_inventory.setModal(false);
        popupwin.setModal(true);


        function popupcountries_checked(rId, cInd, state)
        {
            //check all children records if any
            var rowids = pop_grid.getAllSubItems(rId);

            var arr_ids = rowids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {
                var id = arr_ids[i];
                if (id != "")
                {
                    pop_grid.cells(id, pop_grid.getColIndexById("X")).setValue(state);
                }
            }

            return true;
        }
    }


    function checkGridParents(thegrid)
    {
        //check parent row if all children checked

        for (var i = 0; i < thegrid.getRowsNum(); i++) {
            var rwid = pop_grid.getRowId(i);

            if (rwid.indexOf("market_") != -1)
            {
                if (isAllChildrenChecked(rwid))
                {
                    pop_grid.cells(rwid, pop_grid.getColIndexById("X")).setValue(1);
                }
            }
        }
    }


    function isAllChildrenChecked(parentid)
    {
        //see if all children records are checked or not

        var sub_rowids = pop_grid.getAllSubItems(parentid);

        var arr_ids = sub_rowids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];

            if (id.indexOf("market_") != -1)
            {
                if (!isAllChildrenChecked(id))
                {
                    return false;
                } else
                {
                    pop_grid.cells(id, pop_grid.getColIndexById("X")).setValue(1);
                }
            } else
            {
                if (pop_grid.cells(id, pop_grid.getColIndexById("X")).getValue() != "1")
                {
                    return false;
                }
            }
        }

        return true;
    }

    this.toggleCalendarNextPrev = function (np, month, year)
    {
        hidePopUps();
        month = parseInt(month, 10);
        year = parseInt(year, 10);

        if (np == "NEXT")
        {
            month++;
            if (month >= 13)
            {
                month = 1;
                year++;
            }
        } else if (np == "PREV")
        {
            month--;
            if (month <= 0)
            {
                month = 12;
                year--;
            }
        }

        var url = "php/api/hotelinventory/grid_calendarview_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&myicon=" +  decideImage() +
                "&dates=" + encodeURIComponent(JSON.stringify(_arr_dates)) +
                "&focus_month=" + month + "&focus_year=" + year;

        datesLayout.progressOn();
        grid_dates.loadXML(url, function () {
            datesLayout.progressOff();
        });
    };

    function onGridDatesRowSelect(id, cind)
    {
        hidePopUps();
        var selected = grid_dates.cells(id, cind).getAttribute("selected");
        var date = utils_trim(grid_dates.cells(id, cind).getAttribute("date"), " ");
        if (date == "")
        {
            //it was the header row that was selected
            return;
        }

        var arrdate = date.split("-");
        var dt = arrdate[2];
        dt = parseInt(dt, 10);

        if (selected != "")
        {
            //clear tick
            grid_dates.cells(id, cind).setValue(dt);
            grid_dates.cells(id, cind).setAttribute("selected", "");

            //remove date from array
            removeDateFromArray(date);

        } else
        {
            //place tick
            var img = decideImage();
            grid_dates.cells(id, cind).setValue(dt + "<br><img height='" + icon_size + "' width='" + icon_size + "' src='images/" + img + "'>");
            grid_dates.cells(id, cind).setAttribute("selected", "1");

            //push date into array
            pushDateIntoArray(date);
        }

    }

    function hidePopUps()
    {
        pop_removerange.hide();
        pop_addrange.hide();

    }

    function validateInventory()
    {
        if (!form_details.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Save Inventory",
                callback: function () {
                }
            });


            return false;
        }

        var release_date = utils_trim(form_details.getItemValue("release_date_value"), " ");
        if (release_date != "")
        {
            if (!utils_isDate(release_date))
            {
                dhtmlx.alert({
                    text: "Please enter a valid Release Date!",
                    type: "alert-warning",
                    title: "Save Inventory",
                    callback: function () {
                        form_details.setItemFocus("release_date_value");
                    }
                });
                return false;
            }
        }


        var resa_days_from = utils_trim(form_details.getItemValue("autho_reserve_days_from"), " ");
        var resa_days_to = utils_trim(form_details.getItemValue("autho_reserve_days_to"), " ");

        if (isNaN(resa_days_from)) {
            resa_days_from = 0;
        }
        if (isNaN(resa_days_to)) {
            resa_days_to = 0;
        }

        resa_days_to = parseInt(resa_days_to, 10);
        resa_days_to = parseInt(resa_days_to, 10);

        if (resa_days_to > resa_days_to)
        {
            dhtmlx.alert({
                text: "Invalid Authorisation Reservation Days Order!",
                type: "alert-warning",
                title: "Save Inventory",
                callback: function () {
                    form_details.setItemFocus("autho_reserve_days_to");
                }
            });
            return false;
        }


        var resa_date_from = utils_trim(form_details.getItemValue("autho_reserve_date_from"), " ");
        var resa_date_to = utils_trim(form_details.getItemValue("autho_reserve_date_to"), " ");
        if (!utils_isDate(resa_date_from)) {
            resa_date_from = "";
            form_details.setItemValue("autho_reserve_date_from","");
        }
        if (!utils_isDate(resa_date_to)) {
            resa_date_to = "";
            form_details.setItemValue("autho_reserve_date_to","");
        }

        var resa_time_from = utils_trim(form_details.getItemValue("autho_reserve_time_from"), " ");
        var resa_time_to = utils_trim(form_details.getItemValue("autho_reserve_time_to"), " ");
        if (!utils_isValidTime(resa_time_from)) {
            resa_time_from = "";
            form_details.setItemValue("autho_reserve_time_from","");
        }
        if (!utils_isValidTime(resa_time_to)) {
            resa_time_to = "";
            form_details.setItemValue("autho_reserve_time_to","");
        }


        if ((resa_date_from != "" && resa_time_from == "") ||
                resa_date_from == "" && resa_time_from != "")
        {
            dhtmlx.alert({
                text: "Please enter both Authorisation Reservation Date From and Time From",
                type: "alert-warning",
                title: "Save Inventory",
                callback: function () {
                    form_details.setItemFocus("autho_reserve_date_from");
                }
            });
            return false;
        }

        if ((resa_date_to != "" && resa_time_to == "") ||
                resa_date_to == "" && resa_time_to != "")
        {
            dhtmlx.alert({
                text: "Please enter both Authorisation Reservation Date To and Time To",
                type: "alert-warning",
                title: "Save Inventory",
                callback: function () {
                    form_details.setItemFocus("autho_reserve_date_to");
                }
            });
            return false;
        }


        if (utils_isDate(resa_date_from) && utils_isDate(resa_date_to))
        {
            if (!utils_validateDateOrder(resa_date_from, resa_date_to))
            {
                dhtmlx.alert({
                    text: "Invalid Authorisation Reservation Date Order!",
                    type: "alert-warning",
                    title: "Save Inventory",
                    callback: function () {
                        form_details.setItemFocus("autho_reserve_date_to");
                    }
                });

                return false;
            }

            if (resa_date_from == resa_date_to)
            {
                if (utils_isValidTime(resa_time_from) && utils_isValidTime(resa_time_to))
                {
                    if (!utils_validateTimeOrder(resa_time_from, resa_time_to))
                    {
                        dhtmlx.alert({
                            text: "Invalid Authorisation Reservation Time Order!",
                            type: "alert-warning",
                            title: "Save Inventory",
                            callback: function () {
                                form_details.setItemFocus("autho_reserve_time_to");
                            }
                        });

                        return false;
                    }
                }
            }
        }


        if (_arr_dates.length == 0)
        {
            dhtmlx.alert({
                text: "No Dates have been Selected",
                type: "alert-warning",
                title: "Save Inventory",
                callback: function () {
                    tab_inventory.setTabActive("dates");
                }
            });

            return false;
        }

        return true;
    }

    function loadHotelInventory(selected_id)
    {
        inventorylayout.cells("a").progressOn();
        grid_inventory.clearAll();
        dsInventory = null;
        dsInventory = new dhtmlXDataStore();

        var dsHotel = new dhtmlXDataStore();
        dsHotel.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
            document.getElementById("aTitle").innerHTML = "List of Inventory: <b>" + dsHotel.item(global_hotel_id).hotelname + "</b>";
        });


        dsInventory.load("php/api/hotelinventory/inventorygrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
            inventorylayout.cells("a").progressOff();
            grid_inventory.sync(dsInventory);

            grid_inventory.forEachRow(function (rwid) {
                grid_inventory.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_inventory.setCellTextStyle(rwid, ind, cellstyle);
                });
            });

            if (selected_id != "")
            {
                grid_inventory.selectRowById(selected_id, false, true, false);
            }
        });

    }


    function createDates(dtfrom, dtto)
    {

        var arrfrom = dtfrom.split("-");
        var dt_from = new Date(arrfrom[2], arrfrom[1] - 1, arrfrom[0]);

        var arrto = dtto.split("-");
        var dt_to = new Date(arrto[2], arrto[1] - 1, arrto[0]);

        for (let day = dt_from; day <= dt_to; day.setDate(day.getDate() + 1)) {
            pushDateIntoArray(utils_date_to_str(day));
        }

        onViewModeChange();
    }

    function removeDates(dtfrom, dtto)
    {
        var arrfrom = dtfrom.split("-");
        var dt_from = new Date(arrfrom[2], arrfrom[1] - 1, arrfrom[0]);

        var arrto = dtto.split("-");
        var dt_to = new Date(arrto[2], arrto[1] - 1, arrto[0]);

        for (let day = dt_from; day <= dt_to; day.setDate(day.getDate() + 1)) {
            removeDateFromArray(utils_date_to_str(day));
        }

        onViewModeChange();
    }

    function pushDateIntoArray(date)
    {
        if (_arr_dates.indexOf(date) == -1)
        {
            _arr_dates.push(date);
        }
    }

    function removeDateFromArray(date)
    {
        var idx = _arr_dates.indexOf(date);
        if (idx >= 0)
        {
            _arr_dates.splice(idx, 1);
        }
    }
    
    function decideImage()
    {
      var type = cboType.getSelectedValue();
      
       if(type == "free_sales")
       {
           return "tick.png";
       }
       else if(type == "stop_sales")
       {
           return "cross.png";
       }
       else if(type == "allotments")
       {
           return "AL.png";
       }
       else if(type == "on_request")
       {
           return "RQ.png";
       }
       else if(type == "renovation")
       {
           return "RN.png";
       }
    }



    //===============================================================
    loadPopupDs();
    popupwin_inventory.hide();

}
