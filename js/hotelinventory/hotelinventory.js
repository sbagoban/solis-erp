var hotelinventory_obj = new hotelinventory();

function hotelinventory()
{
    var _arr_dates = [];

    var popupwin = null;
    var pop_grid = null;
    var pop_layout = null;
    var pop_form = null;
    var pop_toolbar = null;

    var _last_search_inventory_params = "";
    var _last_search_allotment_params = "";

    var icon_size = "25px";

    var _dsRooms = new dhtmlXDataStore();
    var dsAllotments = new dhtmlXDataStore();

    document.getElementById("aTitle").innerHTML = "Inventory: ";

    var dsHotel = new dhtmlXDataStore();
    dsHotel.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
        document.getElementById("aTitle").innerHTML = "Inventory: <b>" + dsHotel.item(global_hotel_id).hotelname + "</b>";

    });

    var inventoryMainlayout = new dhtmlXLayoutObject("main_body", "1C");
    inventoryMainlayout.cells("a").hideHeader();


    var tabInventory = inventoryMainlayout.cells("a").attachTabbar();
    tabInventory.addTab("inventory", "<b>Inventory</b>", "280px", '');
    tabInventory.addTab("allotment", "<b>Allotment</b>", "280px", '');
    tabInventory.setTabActive("inventory");


    //=================================== ALLOTMENT TAB ==========================
    var allotmentsearchlayout = tabInventory.cells("allotment").attachLayout("2E");
    allotmentsearchlayout.cells("a").hideHeader();
    allotmentsearchlayout.cells("b").hideHeader();
    allotmentsearchlayout.cells("a").setHeight(50);
    allotmentsearchlayout.cells("a").fixSize(true, true);

    var allotmentinnersearchlayout = allotmentsearchlayout.cells("b").attachLayout("2U");
    allotmentinnersearchlayout.cells("a").setText("Search Allotments");
    allotmentinnersearchlayout.cells("b").setText("My Allotments");
    allotmentinnersearchlayout.cells("b").setWidth(1000);
    allotmentinnersearchlayout.cells("a").setWidth(450);

    var grid_allotments = allotmentinnersearchlayout.cells("b").attachGrid();
    grid_allotments.setIconsPath('libraries/dhtmlx/imgs/');
    grid_allotments.setHeader("Date From,Date To,Priority,Release Type,Units,Rooms,Tour Operators,Comment");
    grid_allotments.setColumnIds("date_from,date_to,priority,release_type,units,roomnames,tour_operator_names,comment");
    grid_allotments.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");
    grid_allotments.setInitWidths("70,70,80,80,50,200,200,1000");
    grid_allotments.setColAlign("center,center,center,center,center,left,left,left");
    grid_allotments.setColSorting('date,date,str,str,int,str,str,str');
    grid_allotments.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    grid_allotments.init();

    var str_search_allotment_details = [
        {type: "settings", position: "label-left", id: "str_search_allotment_details"},
        {type: "hidden", name: "hotelfk", value: global_hotel_id},
        {type: "block", width: 420, list: [
                {type: "calendar", name: "dtfrom", label: "Date From:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},
        {type: "block", width: 420, list: [
                {type: "calendar", name: "dtto", label: "Date To:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},
        {type: "block", width: 420, list: [
                {type: "combo", name: "priority", label: "Priority:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},

        {type: "block", width: 420, list: [
                {type: "input", name: "rooms_display", label: "Rooms:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "rooms_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadRooms", tooltip: "Select Hotel Rooms", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "input", name: "market_countries_display", label: "Countries:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "input", name: "to_display", label: "Tour Operators:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "to_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadTOs", tooltip: "Select Tour Operators", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "button", name: "cmdSearch", tooltip: "Search Allotment", value: "Search Allotment", width: "130", height: "40", offsetLeft: 0}
            ]}
    ];


    var form_search_allotment_details = allotmentinnersearchlayout.cells("a").attachForm(str_search_allotment_details);
    form_search_allotment_details.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_search_allotment_details, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_search_allotment_details, "Rooms", "rooms_display", "rooms_ids", _dsRooms, null);
        } else if (name == "cmdLoadTOs")
        {
            showPopUpTourOperators(form_search_allotment_details, "Tour Operators", "to_display", "to_ids", "MULTIPLE", null);
        } else if (name == "cmdSearch")
        {
            searchAllotment();
        }
    });

    var cboAllotmentPriority = form_search_allotment_details.getCombo("priority");
    cboAllotmentPriority.enableOptionAutoPositioning(true);
    cboAllotmentPriority.readonly(true);
    cboAllotmentPriority.addOption([{value: "all", text: "ALL", img_src: "images/solution.png"}]);
    cboAllotmentPriority.addOption([{value: "touroperator", text: "TOUR OPERATOR", img_src: "images/solution.png"}]);
    cboAllotmentPriority.addOption([{value: "market", text: "MARKET", img_src: "images/solution.png"}]);
    cboAllotmentPriority.addOption([{value: "company", text: "COMPANY", img_src: "images/solution.png"}]);
    cboAllotmentPriority.setComboValue("all");


    var toolbar_allotment = allotmentsearchlayout.cells("a").attachToolbar();
    toolbar_allotment.setIconsPath("images/");
    toolbar_allotment.addButton("new_allot", 1, "Create Allotment Dates", "add.png", "add.png");
    toolbar_allotment.addButton("modify_allot", 2, "Modify Allotments", "modify.png", "modify.png");
    toolbar_allotment.addButton("delete_allot", 3, "Delete Allotments", "delete.png", "delete.png");
    toolbar_allotment.addSpacer("delete_allot");
    toolbar_allotment.addButton("back", 4, "Back to Hotels", "exit.png", "exit.png");
    toolbar_allotment.setIconSize(32);

    toolbar_allotment.attachEvent("onClick", function (id) {

        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        } else if (id == "new_allot")
        {
            newAllotment();

        } else if (id == "modify_allot")
        {
            modifyAllotment();
        }
        else if (id == "delete_allot")
        {
            deleteAllotment();
        }
    });



    //=================================== INVENTORY TAB ==========================
    var inventorylayout = tabInventory.cells("inventory").attachLayout("2E");
    inventorylayout.cells("a").hideHeader();
    inventorylayout.cells("b").hideHeader();
    inventorylayout.cells("a").setHeight(50);
    inventorylayout.cells("a").fixSize(true, true);

    var inventorySearchLayout = inventorylayout.cells("b").attachLayout("2U");
    inventorySearchLayout.cells("a").setText("Search Inventory");
    inventorySearchLayout.cells("b").setText("Inventory Calendar");
    inventorySearchLayout.cells("b").setWidth(1000);
    inventorySearchLayout.cells("a").setWidth(450);

    var grid_inventory_dates = inventorySearchLayout.cells("b").attachGrid();
    grid_inventory_dates.setIconsPath('libraries/dhtmlx/imgs/');
    grid_inventory_dates.init();

    var str_search_details = [
        {type: "settings", position: "label-left", id: "str_details"},
        {type: "hidden", name: "hotelfk", value: global_hotel_id},
        {type: "block", width: 420, list: [
                {type: "calendar", name: "dtfrom", label: "Date From:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},
        {type: "block", width: 420, list: [
                {type: "calendar", name: "dtto", label: "Date To:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},
        {type: "block", width: 420, list: [
                {type: "combo", name: "inventory_type", label: "Status:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},

        {type: "block", width: 420, list: [
                {type: "input", name: "rooms_display", label: "Rooms:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "rooms_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadRooms", tooltip: "Select Hotel Rooms", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "input", name: "market_countries_display", label: "Countries:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "input", name: "to_display", label: "Tour Operators:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "to_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadTOs", tooltip: "Select Tour Operators", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 420, list: [
                {type: "button", name: "cmdSearch", tooltip: "Search Inventory", value: "Search Inventory", width: "130", height: "40", offsetLeft: 0}
            ]}
    ];


    var form_search_details = inventorySearchLayout.cells("a").attachForm(str_search_details);
    form_search_details.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_search_details, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_search_details, "Rooms", "rooms_display", "rooms_ids", _dsRooms, null);
        } else if (name == "cmdLoadTOs")
        {
            showPopUpTourOperators(form_search_details, "Tour Operators", "to_display", "to_ids", "MULTIPLE", null);
        } else if (name == "cmdSearch")
        {
            searchInventory();
        }
    });

    var cboSearchType = form_search_details.getCombo("inventory_type");
    cboSearchType.enableOptionAutoPositioning(true);
    cboSearchType.readonly(true);
    cboSearchType.addOption([{value: "all", text: "ALL STATUSES", img_src: "images/solution.png"}]);
    cboSearchType.addOption([{value: "free_sales", text: "Free Sales", img_src: "images/solution.png"}]);
    cboSearchType.addOption([{value: "stop_sales", text: "Stop Sales", img_src: "images/solution.png"}]);
    cboSearchType.addOption([{value: "on_request", text: "On Request", img_src: "images/solution.png"}]);
    cboSearchType.addOption([{value: "renovation", text: "Renovation", img_src: "images/solution.png"}]);
    cboSearchType.setComboValue("all");




    var toolbar_inventory = inventorylayout.cells("a").attachToolbar();
    toolbar_inventory.setIconsPath("images/");
    toolbar_inventory.addButton("new", 1, "Set Inventory Dates Status", "add.png", "add.png");
    toolbar_inventory.addButton("clear", 2, "Clear Inventory Status", "delete.png", "delete.png");
    toolbar_inventory.addSpacer("clear");
    toolbar_inventory.addButton("back", 3, "Back to Hotels", "exit.png", "exit.png");
    toolbar_inventory.setIconSize(32);

    toolbar_inventory.attachEvent("onClick", function (id) {

        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        } else if (id == "new")
        {
            newInventory();
        } else if (id == "clear")
        {
            clearInventory();
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
            }
            if (json_rights[i].PROCESSNAME == "CLEAR INVENTORY" && json_rights[i].ALLOWED == "N")
            {
                toolbar_inventory.disableItem("clear");
                toolbar_inventory.setItemToolTip("clear", "Not Allowed");
            }
            if (json_rights[i].PROCESSNAME == "CREATE ALLOTMENTS" && json_rights[i].ALLOWED == "N")
            {
                toolbar_allotment.disableItem("new_allot");
                toolbar_allotment.setItemToolTip("new_allot", "Not Allowed");
            }
            if (json_rights[i].PROCESSNAME == "MODIFY ALLOTMENTS" && json_rights[i].ALLOWED == "N")
            {
                toolbar_allotment.disableItem("modify_allot");
                toolbar_allotment.setItemToolTip("modify_allot", "Not Allowed");
            }
            if (json_rights[i].PROCESSNAME == "DELETE ALLOTMENTS" && json_rights[i].ALLOWED == "N")
            {
                toolbar_allotment.disableItem("delete_allot");
                toolbar_allotment.setItemToolTip("delete_allot", "Not Allowed");
            }
        }
    }

    applyrights();


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

            inventoryMainlayout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(inventoryMainlayout.cells("a"));


    //==================================================================================

    var popupwin_allotments = dhxWins.createWindow("popupwin_allotments", 50, 50, 700, 440);
    popupwin_allotments.setText("Allotment Details:");

    var x = $("#main_body").parent().width() - 20;
    var body = document.body,
            html = document.documentElement;
    var y = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
    y -= 170;

    popupwin_allotments.setDimension(x, y);

    popupwin_allotments.denyResize();
    popupwin_allotments.denyPark();
    popupwin_allotments.button("close").hide();

    var allotmentLayout = popupwin_allotments.attachLayout("2E");
    allotmentLayout.cells("a").hideHeader();
    allotmentLayout.cells("b").hideHeader();

    allotmentLayout.cells("a").setHeight(800);
    allotmentLayout.cells("b").setHeight(50);

    allotmentLayout.cells("a").fixSize(true, true);
    allotmentLayout.cells("b").fixSize(true, true);

    var str_allotment_details = [
        {type: "settings", position: "label-left", id: "str_allotment_details"},

        {type: "hidden", name: "hotelfk"},
        {type: "hidden", name: "id"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "release_type", label: "Release Type:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},
        {type: "block", width: 900, list: [
                {type: "combo", name: "priority", label: "Priority:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},
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
                {type: "input", name: "date_from", label: "Date From:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "date_to", label: "Date To:",
                    labelWidth: "70",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                }
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "units", label: "Units:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidInteger", required: true
                }
            ]},

        {type: "block", width: 900, list: [
                {type: "input", name: "specific_no_days", label: "Release No Days:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidInteger"
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "specific_date", label: "Release Specific Date:",
                    labelWidth: "150",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]},
        {type: "block", width: 900, list: [
                {type: "editor", name: "comment", label: "Comments:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "700", inputHeight: "150", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]}
    ];


    var form_allotment_details = allotmentLayout.cells("a").attachForm(str_allotment_details);
    form_allotment_details.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdLoadRooms")
        {
            showPopUp(form_allotment_details, "Rooms", "rooms_display", "rooms_ids", _dsRooms, null);
        }
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_allotment_details, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadTOs")
        {
            showPopUpTourOperators(form_allotment_details, "Tour Operators", "to_display", "to_ids", "MULTIPLE", null);
        }
    });

    jQuery(function ($) {
        $("[name='specific_date']").mask("99-99-9999");
        $("[name='date_from']").mask("99-99-9999");
        $("[name='date_to']").mask("99-99-9999");
    });

    var cboPriority = form_allotment_details.getCombo("priority");
    cboPriority.enableOptionAutoPositioning(true);
    cboPriority.readonly(true);
    cboPriority.addOption([{value: "touroperator", text: "TOUR OPERATOR", img_src: "images/solution.png"}]);
    cboPriority.addOption([{value: "market", text: "MARKET", img_src: "images/solution.png"}]);
    cboPriority.addOption([{value: "company", text: "COMPANY", img_src: "images/solution.png"}]);



    var cboReleaseType = form_allotment_details.getCombo("release_type");
    cboReleaseType.enableOptionAutoPositioning(true);
    cboReleaseType.readonly(true);
    cboReleaseType.addOption([{value: "specific_date", text: "Specific Date", img_src: "images/solution.png"}]);
    cboReleaseType.addOption([{value: "specific_days", text: "Specific Days", img_src: "images/solution.png"}]);

    var str_form_allotment_details_buttons = [
        {type: "settings", position: "label-left", id: "form_allotment_details_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Inventory", value: "Return to Inventory", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Allotments", value: "Save Allotments", width: "230", height: "60", offsetLeft: 0}
            ]}];

    var form_allotment_details_buttons = allotmentLayout.cells("b").attachForm(str_form_allotment_details_buttons);
    form_allotment_details_buttons.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdClose")
        {
            popupwin_allotments.hide();
            popupwin_allotments.setModal(false);
        } else if (name == "cmdSave")
        {
            saveAllotment();
        }
    });

    //==================================================================================

    var popupwin_clear_inventory = dhxWins.createWindow("popupwin_clear_inventory", 50, 50, 960, 400);
    popupwin_clear_inventory.setText("Clear Inventory Status:");

    popupwin_clear_inventory.denyResize();
    popupwin_clear_inventory.denyPark();
    popupwin_clear_inventory.button("close").hide();

    var detailsClearLayout = popupwin_clear_inventory.attachLayout("2E");
    detailsClearLayout.cells("a").hideHeader();
    detailsClearLayout.cells("b").hideHeader();

    detailsClearLayout.cells("a").setHeight(800);
    detailsClearLayout.cells("b").setHeight(50);

    detailsClearLayout.cells("a").fixSize(true, true);
    detailsClearLayout.cells("b").fixSize(true, true);

    var str_clear_details = [
        {type: "settings", position: "label-left", id: "str_clear_details"},
        {type: "hidden", name: "hotelfk"},
        {type: "block", width: 800, list: [
                {type: "input", name: "date_from", label: "Date From:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "date_to", label: "Date To:",
                    labelWidth: "70",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                }
            ]},
        {type: "block", width: 800, list: [
                {type: "combo", name: "specific_to", label: "Specific To:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]},
        {type: "block", width: 800, list: [
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
        {type: "block", width: 800, list: [
                {type: "input", name: "rooms_display", label: "Rooms:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "rooms_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadRooms", tooltip: "Select Hotel Rooms", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 800, list: [
                {type: "input", name: "to_display", label: "Tour Operators:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "to_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadTOs", tooltip: "Select Tour Operators", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]}
    ];


    var form_clear_details = detailsClearLayout.cells("a").attachForm(str_clear_details);
    form_clear_details.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_clear_details, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_clear_details, "Rooms", "rooms_display", "rooms_ids", _dsRooms, null);
        } else if (name == "cmdLoadTOs")
        {
            showPopUpTourOperators(form_clear_details, "Tour Operators", "to_display", "to_ids", "MULTIPLE", null);
        }
    });


    var cboClearSpecific = form_clear_details.getCombo("specific_to");
    cboClearSpecific.enableOptionAutoPositioning(true);
    cboClearSpecific.readonly(true);
    cboClearSpecific.addOption([{value: "A", text: "Tour Operator (A)", img_src: "images/rate_32.png"}]);
    cboClearSpecific.addOption([{value: "B", text: "World Wide (B)", img_src: "images/rate_32.png"}]);
    cboClearSpecific.addOption([{value: "C", text: "Market (C)", img_src: "images/rate_32.png"}]);



    var str_form_clear_details_buttons = [
        {type: "settings", position: "label-left", id: "form_details_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Inventory", value: "Return to Inventory", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdClear", tooltip: "Clear Inventory", value: "Clear Inventory Status", width: "230", height: "60", offsetLeft: 0}
            ]}];

    var form_clear_details_buttons = detailsClearLayout.cells("b").attachForm(str_form_clear_details_buttons);
    form_clear_details_buttons.attachEvent("onButtonClick", function (name, command) {
        hidePopUps();
        if (name == "cmdClose")
        {
            popupwin_clear_inventory.hide();
            popupwin_clear_inventory.setModal(false);
        } else if (name == "cmdClear")
        {
            dhtmlx.confirm({
                title: "Clear Status?",
                type: "confirm",
                text: "Confirm Clearing the Status for the selected Tour Operators, Dates and Rooms?",
                callback: function (tf) {
                    if (tf)
                    {
                        clearInventoryStatus();
                    }
                }
            });
        }
    });

    //==================================================================================

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
                {type: "combo", name: "specific_to", label: "Specific To:",
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
            showPopUpTourOperators(form_details, "Tour Operators", "to_display", "to_ids", "MULTIPLE", null);
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
    cboType.addOption([{value: "on_request", text: "On Request", img_src: "images/solution.png"}]);
    cboType.addOption([{value: "renovation", text: "Renovation", img_src: "images/solution.png"}]);

    var cboSpecific = form_details.getCombo("specific_to");
    cboSpecific.enableOptionAutoPositioning(true);
    cboSpecific.readonly(true);
    cboSpecific.addOption([{value: "A", text: "Tour Operator (A)", img_src: "images/rate_32.png"}]);
    cboSpecific.addOption([{value: "B", text: "World Wide (B)", img_src: "images/rate_32.png"}]);
    cboSpecific.addOption([{value: "C", text: "Market (C)", img_src: "images/rate_32.png"}]);



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

    //==================================================================================
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
                    "&myicon=" + decideImage() +
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
                        "&myicon=" + decideImage() +
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

    //==================================================================================
    
    function deleteAllotment()
    {
        var aid = grid_allotments.getSelectedRowId();
        if (!aid)
        {
            return;
        }
        
        dhtmlx.confirm({
            title: "Delete Allotment?",
            type: "confirm",
            text: "Confirm Deletion of selected Allotment?",
            callback: function (tf) {
                if (tf)
                {
                    deleteTheAllotment(aid);
                }
            }
        });
    }
    
    
    function deleteTheAllotment(aid)
    {
        var params = "token=" + encodeURIComponent(global_token);
        params += "&aid=" + encodeURIComponent(aid);


        allotmentinnersearchlayout.progressOn();

        dhtmlxAjax.post("php/api/hotelinventory/deleteallotment.php", params, function (loader) {
            allotmentinnersearchlayout.progressOff();

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
                    dhtmlx.alert({
                        text: "Deletion Successful",
                        type: "alert",
                        title: "DELETE",
                        callback: function () {
                        }
                    });
                    
                    grid_allotments.deleteRow(aid);
                   
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
    
    function modifyAllotment()
    {
        var aid = grid_allotments.getSelectedRowId();
        if (!aid)
        {
            return;
        }

        form_allotment_details.clear();
        form_allotment_details.setItemValue("id", aid);
        form_allotment_details.setItemValue("hotelfk", global_hotel_id);

        var data = dsAllotments.item(aid);

        form_allotment_details.setFormData(data);

        popupwin_allotments.center();
        popupwin_allotments.setModal(true);
        popupwin_allotments.show();
        popupwin_allotments.setText("Modify Allotments Details:");

    }
    function newAllotment()
    {
        popupwin_allotments.center();
        popupwin_allotments.setModal(true);
        popupwin_allotments.show();
        popupwin_allotments.setText("New Allotments Details:");

        form_allotment_details.clear();
        form_allotment_details.setItemValue("id", "-1");
        form_allotment_details.setItemValue("hotelfk", global_hotel_id);
        cboPriority.setComboValue("COMPANY");
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

    function clearInventoryStatus()
    {
        if (!form_clear_details.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Clear Inventory",
                callback: function () {
                }
            });
            return;
        }

        var date_from = utils_trim(form_clear_details.getItemValue("date_from"), " ");
        var date_to = utils_trim(form_clear_details.getItemValue("date_to"), " ");

        if (!utils_isDate(date_from)) {
            form_clear_details.setItemValue("date_from", "");
        }

        if (!utils_isDate(date_to)) {
            form_clear_details.setItemValue("date_to", "");
        }


        if (!utils_validateDateOrder(date_from, date_to))
        {
            dhtmlx.alert({
                text: "Invalid Date From and To Order!",
                type: "alert-warning",
                title: "Clear Inventory",
                callback: function () {
                    form_clear_details.setItemFocus("date_from");
                }
            });

            return false;
        }


        var params = "token=" + encodeURIComponent(global_token);

        //details
        var details = form_clear_details.getFormData();
        params += "&details=" + encodeURIComponent(JSON.stringify(details));


        detailsClearLayout.progressOn();

        dhtmlxAjax.post("php/api/hotelinventory/deleteinventory.php", params, function (loader) {
            detailsClearLayout.progressOff();

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
                    dhtmlx.alert({
                        text: "Deletion Successful",
                        type: "alert",
                        title: "DELETE",
                        callback: function () {
                        }
                    });

                    popupwin_clear_inventory.hide();
                    popupwin_clear_inventory.setModal(false);

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

    function clearInventory()
    {
        popupwin_clear_inventory.center();
        popupwin_clear_inventory.setModal(true);
        popupwin_clear_inventory.show();
        popupwin_clear_inventory.setText("Clear Inventory Status:");

        form_clear_details.clear();
        form_clear_details.setItemValue("hotelfk", global_hotel_id);
    }



    function saveAllotment()
    {
        if (!validateAllotment())
        {
            return;
        }

        var params = "token=" + encodeURIComponent(global_token);

        //details
        var details = form_allotment_details.getFormData();
        params += "&details=" + encodeURIComponent(JSON.stringify(details));


        allotmentLayout.progressOn();

        dhtmlxAjax.post("php/api/hotelinventory/saveallotment.php", params, function (loader) {
            allotmentLayout.progressOff();

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

                    //populate the allotment to select the current record
                    var id = json_obj.ID;
                    var url = "php/api/hotelinventory/grid_search_allotment.php?" +
                            "t=" + encodeURIComponent(global_token) +
                            "&hotelfk=" + encodeURIComponent(global_hotel_id) +
                            "&allotmentid=" + encodeURIComponent(id);
                    populateAllotmentGrid(url);



                    popupwin_allotments.hide();
                    popupwin_allotments.setModal(false);

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

                    popupwin_inventory.hide();
                    popupwin_inventory.setModal(false);

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

                //clear tour operators because they are linked to countries
                form.setItemValue("to_display", "");
                form.setItemValue("to_ids", "");

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
                "&myicon=" + decideImage() +
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


    function validateAllotment()
    {
        var resa_date_from = utils_trim(form_allotment_details.getItemValue("date_from"), " ");
        var resa_date_to = utils_trim(form_allotment_details.getItemValue("date_to"), " ");

        if (!utils_isDate(resa_date_from)) {
            form_allotment_details.setItemValue("date_from", "");
        }

        if (!utils_isDate(resa_date_to)) {
            form_allotment_details.setItemValue("date_to", "");
        }



        if (!form_allotment_details.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Save Allotments",
                callback: function () {
                }
            });
            return false;
        }


        if (!utils_validateDateOrder(resa_date_from, resa_date_to))
        {
            dhtmlx.alert({
                text: "Invalid Date From and To Order!",
                type: "alert-warning",
                title: "Save Allotments",
                callback: function () {
                    form_allotment_details.setItemFocus("date_from");
                }
            });

            return false;
        }


        var units = utils_trim(form_allotment_details.getItemValue("units"), " ");
        units = parseInt(units, 10);
        if (units <= 0)
        {
            dhtmlx.alert({
                text: "Units MUST be >= 1!",
                type: "alert-warning",
                title: "Save Allotments",
                callback: function () {
                    form_allotment_details.setItemFocus("units");
                }
            });

            return false;
        }

        var specific_date = utils_trim(form_details.getItemValue("specific_date"), " ");
        if (!utils_isDate(specific_date)) {
            specific_date = "";
            form_allotment_details.setItemValue("specific_date", "");
        }

        return true;

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
            form_details.setItemValue("autho_reserve_date_from", "");
        }
        if (!utils_isDate(resa_date_to)) {
            resa_date_to = "";
            form_details.setItemValue("autho_reserve_date_to", "");
        }

        var resa_time_from = utils_trim(form_details.getItemValue("autho_reserve_time_from"), " ");
        var resa_time_to = utils_trim(form_details.getItemValue("autho_reserve_time_to"), " ");
        if (!utils_isValidTime(resa_time_from)) {
            resa_time_from = "";
            form_details.setItemValue("autho_reserve_time_from", "");
        }
        if (!utils_isValidTime(resa_time_to)) {
            resa_time_to = "";
            form_details.setItemValue("autho_reserve_time_to", "");
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

        if (type == "free_sales")
        {
            return "tick.png";
        } else if (type == "stop_sales")
        {
            return "cross.png";
        } else if (type == "on_request")
        {
            return "RQ.png";
        } else if (type == "renovation")
        {
            return "RN.png";
        }
    }


    function showPopUpTourOperators(form, caller, inputdisplay, inputid, single_multiple, callback)
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
        pop_form.attachEvent("onButtonClick", function (name, command) {
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
                    callback();
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

            if (single_multiple == "SINGLE")
            {
                //set all over rows to 0
                for (var i = 0; i < pop_grid.getRowsNum(); i++)
                {
                    var rowid = pop_grid.getRowId(i);
                    if (rowid != rid)
                    {
                        pop_grid.cells(rowid, pop_grid.getColIndexById("X")).setValue("0");
                    }
                }
            }
        });


        if (single_multiple == "SINGLE")
        {
            pop_grid.attachEvent("onCheck", function (rId, cInd, state) {
                if (state)
                {
                    state = 1;
                } else
                {
                    state = 0;
                }

                if (state == 1)
                {
                    //set all over rows to 0
                    for (var i = 0; i < pop_grid.getRowsNum(); i++)
                    {
                        var rowid = pop_grid.getRowId(i);
                        if (rowid != rId)
                        {
                            pop_grid.cells(rowid, cInd).setValue("0");
                        }
                    }
                }

                return true;
            });
        }

        pop_grid.init();

        //load all TOs that belong to the selected countries
        pop_layout.progressOn();
        var _dsTOs = new dhtmlXDataStore();
        var countries_ids = form.getItemValue("market_countries_ids");
        _dsTOs.load("php/api/hotelinventory/to_grid.php?t=" + encodeURIComponent(global_token) + "&countries_ids=" + encodeURIComponent(countries_ids), "json", function () {
            pop_layout.progressOff();
            pop_grid.sync(_dsTOs);


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

        });

        popupwin.show();
        popupwin.center();
        popupwin.setText(caller);
        popupwin_inventory.setModal(false);
        popupwin.setModal(true);
    }


    function searchAllotment()
    {
        if (!form_search_allotment_details.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Search Allotment",
                callback: function () {
                }
            });
            return;
        }

        var dtfrom = form_search_allotment_details.getItemValue("dtfrom");
        var dtto = form_search_allotment_details.getItemValue("dtto");

        if (dtto < dtfrom)
        {
            dhtmlx.alert({
                text: "Invalid Date From and To order!",
                type: "alert-warning",
                title: "Search Allotment",
                callback: function () {
                }
            });
            return;
        }


        grid_allotments.clearAll();

        _last_search_allotment_params = form_search_allotment_details.getFormData();

        delete _last_search_allotment_params.rooms_display;
        delete _last_search_allotment_params.market_countries_display;
        delete _last_search_allotment_params.to_display;


        var url = "php/api/hotelinventory/grid_search_allotment.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&params=" + encodeURIComponent(JSON.stringify(_last_search_allotment_params));

        populateAllotmentGrid(url);

    }

    function populateAllotmentGrid(url)
    {
        allotmentinnersearchlayout.cells("b").progressOn();

        dsAllotments = new dhtmlXDataStore();

        console.log(url);

        dsAllotments.load(url, "json", function () {

            allotmentinnersearchlayout.cells("b").progressOff();

            grid_allotments.sync(dsAllotments);

            grid_allotments.forEachRow(function (rwid) {
                grid_allotments.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_allotments.setCellTextStyle(rwid, ind, cellstyle);
                });
            });

        });
    }

    function searchInventory()
    {
        if (!form_search_details.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Search Inventory",
                callback: function () {
                }
            });
            return;
        }

        var dtfrom = form_search_details.getItemValue("dtfrom");
        var dtto = form_search_details.getItemValue("dtto");

        if (dtto < dtfrom)
        {
            dhtmlx.alert({
                text: "Invalid Date From and To order!",
                type: "alert-warning",
                title: "Search Inventory",
                callback: function () {
                }
            });
            return;
        }


        grid_inventory_dates.clearAll(true);
        grid_inventory_dates = null;
        grid_inventory_dates = inventorySearchLayout.cells("b").attachGrid();
        grid_inventory_dates.setIconsPath('libraries/dhtmlx/imgs/');
        grid_inventory_dates.enableAlterCss("", "");
        grid_inventory_dates.enableColSpan(true);
        grid_inventory_dates.setEditable(false);
        grid_inventory_dates.init();

        _last_search_inventory_params = form_search_details.getFormData();

        delete _last_search_inventory_params.rooms_display;
        delete _last_search_inventory_params.market_countries_display;
        delete _last_search_inventory_params.to_display;


        var url = "php/api/hotelinventory/grid_search_calendarview_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&myicon_size=" + icon_size +
                "&params=" + encodeURIComponent(JSON.stringify(_last_search_inventory_params));

        console.log(url);

        inventorySearchLayout.progressOn();
        grid_inventory_dates.loadXML(url, function () {
            inventorySearchLayout.progressOff();
        });
    }


    this.toggleSearchCalendarNextPrev = function (np, month, year)
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


        var url = "php/api/hotelinventory/grid_search_calendarview_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&myicon_size=" + icon_size +
                "&params=" + encodeURIComponent(JSON.stringify(_last_search_inventory_params)) +
                "&focus_month=" + month + "&focus_year=" + year;

        inventorySearchLayout.progressOn();
        grid_inventory_dates.loadXML(url, function () {
            inventorySearchLayout.progressOff();
        });
    };

    //===============================================================
    loadPopupDs();
    popupwin_inventory.hide();
    popupwin_allotments.hide();
    popupwin_clear_inventory.hide();

}
