var hotelcontracts_obj = new hotelcontracts();

function hotelcontracts()
{
    var popupwin = null;
    var pop_grid = null;
    var pop_layout = null;
    var pop_form = null;
    var pop_toolbar = null;

    var _last_grid_choice_id = "";
    var _last_popup_ids = "";
    var _last_popup_display_values = "";

    var _agecolwidth = 35;

    var _min_stay_id = -1;

    var _capacity_room_rw_id = -1;
    var _capacity_room_date_id = -1;
    var _capacity_room_date_rule_id = -1;
    var _capacity_room_date_rule_capacity_id = -1;

    var _adultpolicy_room_date_rule_id = -1;
    var _adultpolicy_room_date_rule_capacity_id = -1;
    var _adultpolicy_room_date_rule_capacity_value_id = -1;

    var _childpolicy_room_date_rule_id = -1;
    var _childpolicy_room_date_rule_capacity_id = -1;
    var _childpolicy_room_date_rule_capacity_value_id = -1;

    var _singleparentchildpolicy_room_date_rule_id = -1;
    var _singleparentchildpolicy_room_date_rule_capacity_id = -1;
    var _singleparentchildpolicy_room_date_rule_capacity_value_id = -1;

    var _meals_children_age_id = -1;
    var _meals_id = -1;
    var _extras_id = -1;
    var _extras_children_age_id = -1;
    var _checkinout_id = -1;
    var _cancellation_id = -1;
    var _taxcommi_room_rw_id = -1;
    var _taxcommi_settings_id = -1;
    var _taxcommi_settings_value_id = -1;
    var _exchange_rate_id = -1;
    var _currency_mapping_id = -1;


    var _dsCurrencies = new dhtmlXDataStore();
    var _dsRates = new dhtmlXDataStore();
    var _dsChildPolicy = new dhtmlXDataStore();
    var _dsDepartments = new dhtmlXDataStore();
    var _dsRooms = new dhtmlXDataStore();
    var _dsDatePeriods = new dhtmlXDataStore();
    var _dsTaxCommiItems = new dhtmlXDataStore();

    var _dsGridMealCombo = new dhtmlXDataStore();

    var _json_hotels = [];
    var _json_capacity = [];
    var _json_taxcommi = [];
    var _json_exchangerates = {exchange_rates: [], currency_mapping: []};


    var _arr_errors = [];
    resetError();


    loadPopupDs();
    loadHotel();


    document.getElementById("aTitle").innerHTML = "List of Contracts: ";

    var contractlayout = new dhtmlXLayoutObject("main_body", "1C");
    contractlayout.cells("a").hideHeader();

    var dsContracts = new dhtmlXDataStore();

    var grid_contracts = contractlayout.cells("a").attachGrid();
    grid_contracts.setIconsPath('libraries/dhtmlx/imgs/');
    grid_contracts.setHeader("ID,Contract Name,Active Internal,Active External,Validity From,Validity To,Meal Plan,Rooms,Tour Operators,Meal,Rates,Children,Currencies Buy,Currencies Sell");
    grid_contracts.setColumnIds("id,contractname,active_internal,active_external,active_from,active_to,meal,roomnames,tour_operator_names,mymeals,myrates,agerange,mycurrencies_buy,mycurrencies_sell");
    grid_contracts.setColTypes("ro,ro,ch,ch,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
    grid_contracts.setInitWidths("70,200,60,60,80,80,60,220,200,0,45,70,80,80");
    grid_contracts.setColAlign("center,left,center,center,center,center,center,center,center,center,center,center,center,center");
    grid_contracts.setColSorting('int,str,int,int,date,date,str,str,str,str,str,str,str');
    grid_contracts.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    grid_contracts.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
    grid_contracts.setEditable(false);
    grid_contracts.enableMultiline(true);
    grid_contracts.init();

    var toolbar_contract = contractlayout.cells("a").attachToolbar();
    toolbar_contract.setIconsPath("images/");
    toolbar_contract.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_contract.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_contract.addButton("copypaste", 3, "Copy Paste", "copypaste.png", "copypaste.png");
    toolbar_contract.addButton("delete", 4, "Delete", "delete.png", "delete.png");
    toolbar_contract.addButton("export", 5, "Export Excel", "excel.png");
    toolbar_contract.addSpacer("export");
    toolbar_contract.addButton("back", 6, "Back to Hotels", "exit.png", "exit.png");
    toolbar_contract.setIconSize(32);

    toolbar_contract.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        } else if (id == "new")
        {
            newContract();

        } else if (id == "modify")
        {
            var cid = grid_contracts.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            modifyContract(cid, false);
        } else if (id == "copypaste")
        {
            var cid = grid_contracts.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            modifyContract(cid, true);

        } else if (id == "export")
        {
            grid_contracts.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "delete")
        {
            var cid = grid_contracts.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            deleteContract(cid);
        }
    });


    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {
            if (json_rights[i].PROCESSNAME == "ADD CONTRACT" && json_rights[i].ALLOWED == "N")
            {
                toolbar_contract.disableItem("new");
                toolbar_contract.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY CONTRACT" && json_rights[i].ALLOWED == "N")
            {
                toolbar_contract.disableItem("modify");
                toolbar_contract.setItemToolTip("modify", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE CONTRACT" && json_rights[i].ALLOWED == "N")
            {

                toolbar_contract.disableItem("delete");
                toolbar_contract.setItemToolTip("delete", "Not Allowed");
            }
        }
    }

    contractlayout.cells("a").progressOn();
    var dsHotel = new dhtmlXDataStore();
    dsHotel.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
        document.getElementById("aTitle").innerHTML = "List of Contracts: <b>" + dsHotel.item(global_hotel_id).hotelname + "</b>";
        contractlayout.cells("a").progressOff();
        loadHotelContracts();
    });


    function loadHotelContracts(select_contract_id)
    {
        contractlayout.cells("a").progressOn();
        grid_contracts.clearAll();
        dsContracts = null;
        dsContracts = new dhtmlXDataStore();

        dsContracts.load("php/api/hotelcontracts/contractgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
            contractlayout.cells("a").progressOff();
            grid_contracts.sync(dsContracts);

            grid_contracts.forEachRow(function (rwid) {
                grid_contracts.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_contracts.setCellTextStyle(rwid, ind, cellstyle);
                });
            });

            if (select_contract_id != "")
            {
                grid_contracts.selectRowById(select_contract_id, false, true, false);
            }
        });
    }


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

            contractlayout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(contractlayout.cells("a"));

    var popupwin_contracts = dhxWins.createWindow("popupwin_contracts", 50, 50, 700, 440);
    popupwin_contracts.setText("Contract Details:");

    var x = $("#main_body").parent().width() - 20;
    var body = document.body,
            html = document.documentElement;
    var y = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
    y -= 170;

    popupwin_contracts.setDimension(x, y);

    popupwin_contracts.denyResize();
    popupwin_contracts.denyPark();
    popupwin_contracts.button("close").hide();

    //=============

    var popupwin_testtaxcomm = dhxWins.createWindow("popupwin_testtaxcomm", 50, 50, 700, 500);
    popupwin_testtaxcomm.setText("Test Tax Commission:");
    popupwin_testtaxcomm.denyResize();
    popupwin_testtaxcomm.denyPark();
    popupwin_testtaxcomm.button("close").hide();


    //=============

    var popupwin_profile_taxcomm = dhxWins.createWindow("popupwin_profile_taxcomm", 50, 50, 900, 500);
    popupwin_profile_taxcomm.setText("Profile Tax Commission:");
    popupwin_profile_taxcomm.denyResize();
    popupwin_profile_taxcomm.denyPark();
    popupwin_profile_taxcomm.button("close").hide();


    //=============

    var popupwin_capacitydates = dhxWins.createWindow("popupwin_capacitydates", 50, 50, 600, 200);
    popupwin_capacitydates.setText("Room Capacity Dates:");
    popupwin_capacitydates.denyResize();
    popupwin_capacitydates.denyPark();
    popupwin_capacitydates.button("close").hide();

    //=============

    var popupwin_loadperiods = dhxWins.createWindow("popupwin_loadperiods", 50, 50, 1000, 400);
    popupwin_loadperiods.setText("Load Periods:");
    popupwin_loadperiods.denyResize();
    popupwin_loadperiods.denyPark();
    popupwin_loadperiods.button("close").hide();

    //==============

    var popupwin_capacitycombinations = dhxWins.createWindow("popupwin_capacitycombinations", 50, 50, 900, 500);
    popupwin_capacitycombinations.setText("Room Combinations:");
    popupwin_capacitycombinations.denyResize();
    popupwin_capacitycombinations.denyPark();
    popupwin_capacitycombinations.button("close").hide();



    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {

        if (win.getId() == "popupwin")
        {
            popupwin.setModal(false);
            popupwin_contracts.setModal(true);
            return true;
        } else
        {
            win.setModal(false);
            win.hide();
        }

    });

    var detailslayout = popupwin_contracts.attachLayout("2U");

    detailslayout.cells("a").setWidth(160);
    detailslayout.cells("a").hideHeader();
    detailslayout.cells("b").hideHeader();

    var toolbar_details = detailslayout.cells("b").attachToolbar();
    toolbar_details.setIconsPath("images/");
    toolbar_details.addButton("save", 1, "Save Contract", "save.png", "save.png");
    toolbar_details.addButton("problem", 2, "Click for Errors", "problems.png", "problems.png");
    toolbar_details.addSpacer("problem");
    toolbar_details.addButton("exit", 3, "Exit", "exit.png", "exit.png");
    toolbar_details.setIconSize(32);
    toolbar_details.hideItem("problem");
    toolbar_details.attachEvent("onClick", function (id) {
        hidePopups();

        if (id == "exit")
        {

            popupwin_contracts.setModal(false);
            popupwin_contracts.hide();

        } else if (id == "save")
        {
            saveContract();

        } else if (id == "problem")
        {
            displayProblems();

        }
    });


    //================================================
    var popup_problem = new dhtmlXPopup({
        toolbar: toolbar_details,
        id: "problem"
    });
    popup_problem.setSkin("dhx_web");

    var grid_problem = popup_problem.attachGrid(800, 250);
    grid_problem.setIconsPath('libraries/dhtmlx/imgs/');
    grid_problem.setHeader(",,");
    grid_problem.setColumnIds("idx,problem,callback");
    grid_problem.setColTypes("ro,ro,ro");
    grid_problem.setInitWidths("40,740,0");
    grid_problem.setColAlign("center,left,left");
    grid_problem.setColSorting('na,na,na');
    grid_problem.attachEvent("onRowSelect", onProblemSelect);
    grid_problem.enableAlterCss("", "");
    grid_problem.enableMultiline(true);
    grid_problem.init();
    //================================================

    var grid_choices = detailslayout.cells("a").attachGrid();
    grid_choices.setIconsPath('libraries/dhtmlx/imgs/');
    grid_choices.setHeader(",");
    grid_choices.setColumnIds("tmpimg,choice");
    grid_choices.setColTypes("ro,ro");
    grid_choices.setInitWidths("35,90");
    grid_choices.setColAlign("center,left");
    grid_choices.setColSorting('str,str');
    grid_choices.attachEvent("onRowSelect", onChoicesSelect);
    grid_choices.setNoHeader(true);
    grid_choices.enableAlterCss("", "");
    grid_choices.enableMultiline(true);
    grid_choices.init();

    grid_choices.addRow("main", ["<img src=\"images/task.png\" width=\"30px\" height=\"30px\">", "1. MAIN"]);
    grid_choices.addRow("currency", ["<img src=\"images/currency_euro_sign.png\" width=\"30px\" height=\"30px\">", "2. CURRENCY"]);
    grid_choices.addRow("tax", ["<img src=\"images/abacus.png\" width=\"30px\" height=\"30px\">", "3. TAX COMM"]);
    grid_choices.addRow("rooms", ["<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">", "4. ROOMS"]);
    grid_choices.addRow("notes", ["<img src=\"images/notes_1.png\" width=\"30px\" height=\"30px\">", "5. NOTES"]);


    function onChoicesSelect(rid, cid)
    {
        hidePopups();

        toggleError(rid, "");

        var arrtabids = tabViews.getAllTabs();
        for (var i = 0; i < arrtabids.length; i++)
        {
            var tabid = arrtabids[i];

            if (tabid == rid)
            {
                tabViews.showTab(tabid);
                tabViews.setTabActive(tabid);

                //===========
                if (rid == "rooms") {

                    var err = validate_contract_dates();
                    if (err != "")
                    {
                        grid_choices.selectRowById("main", false, true, true);
                        form_main.setItemFocus("active_from");
                        form_main.validateItem("active_from");
                        form_main.validateItem("active_to");
                        dhtmlx.alert({
                            text: err,
                            type: "alert-warning",
                            title: "Validate Contract Dates",
                            callback: function () {
                            }
                        });

                        return false;
                    }
                    //====================================================
                    var mealplanfk = form_main.getItemValue("mealplan_fk");
                    if (mealplanfk == "" || !mealplanfk)
                    {
                        grid_choices.selectRowById("main", false, true, true);
                        form_main.setItemFocus("mealplan_fk");
                        form_main.validateItem("mealplan_fk");
                        dhtmlx.alert({
                            text: "Please select a Meal Plan",
                            type: "alert-warning",
                            title: "Meal Plan",
                            callback: function () {
                            }
                        });

                        return false;
                    }

                    //====================================================
                    var err = validate_children_ages();
                    if (err != "")
                    {
                        dhtmlx.alert({
                            text: err,
                            type: "alert-warning",
                            title: "Validate Children Ages",
                            callback: function () {
                                grid_choices.selectRowById("main", false, true, true);
                                showPopUp(form_main, "Children Ages", "children_ages_display", "children_ages_ids", _dsChildPolicy, "MULTIPLE", updateChildAges);
                            }
                        });
                        return false;
                    }

                    //======================================================

                    var arrerr = validate_currency();
                    var errstr = utils_trim(arrerr.map(e => e.msg).join("<br>"), " ");
                    if (errstr != "")
                    {

                        dhtmlx.alert({
                            text: errstr,
                            type: "alert-warning",
                            title: "Validate Contract Currency",
                            callback: function () {
                                grid_choices.selectRowById("currency", false, true, true);
                            }
                        });
                        return false;
                    }

                    //======================================================
                    arrerr = validate_currency_mapping();
                    errstr = utils_trim(arrerr.map(e => e.msg).join("<br>"), " ");
                    if (errstr != "")
                    {
                        dhtmlx.alert({
                            text: errstr,
                            type: "alert-warning",
                            title: "Validate Contract Currency",
                            callback: function () {
                                grid_choices.selectRowById("currency", false, true, true);
                            }
                        });
                        return false;
                    }

                    //======================================================

                    loadHotelContractDatePeriods();
                    grid_room_choices.selectRowById("minstay", false, true, true);

                }
                //===========
                else if (rid == "tax") {


                    //===========================
                    var err = validate_contract_dates();
                    if (err != "")
                    {
                        grid_choices.selectRowById("main", false, true, true);
                        form_main.setItemFocus("active_from");
                        form_main.validateItem("active_from");
                        form_main.validateItem("active_to");
                        dhtmlx.alert({
                            text: err,
                            type: "alert-warning",
                            title: "Validate Contract Dates",
                            callback: function () {
                            }
                        });

                        return false;
                    }
                    //===========================

                    var arrerr = validate_currency();
                    var errstr = utils_trim(arrerr.map(e => e.msg).join("<br>"), " ");
                    if (errstr != "")
                    {

                        dhtmlx.alert({
                            text: errstr,
                            type: "alert-warning",
                            title: "Validate Contract Currency",
                            callback: function () {
                                grid_choices.selectRowById("currency", false, true, true);
                            }
                        });
                        return false;
                    }


                    arrerr = validate_currency_mapping();
                    errstr = utils_trim(arrerr.map(e => e.msg).join("<br>"), " ");
                    if (errstr != "")
                    {
                        dhtmlx.alert({
                            text: errstr,
                            type: "alert-warning",
                            title: "Validate Contract Currency",
                            callback: function () {
                                grid_choices.selectRowById("currency", false, true, true);
                            }
                        });
                        return false;

                    }

                    loadTaxCommissionRoomTree();
                    loadDefaultTaxCommiValues();

                }
                //===========
                else if (rid == "currency") {


                    fillExchangeRatesGrid();
                    fillCurrencyMappingGrid();
                }
                //===========
            } else
            {
                tabViews.hideTab(tabid);
            }
        }
    }

    var tabViews = detailslayout.cells("b").attachTabbar();
    tabViews.addTab("main", "<b>Main Information</b>", "280px", '');
    tabViews.addTab("currency", "<b>Currency</b>", "280px", '');
    tabViews.addTab("notes", "<b>Notes</b>", "280px", '');
    tabViews.addTab("tax", "<b>Tax and Commission</b>", "280px", '');
    tabViews.addTab("rooms", "<b>Rooms</b>", "280px", '');


    //=====================================================================
    //LOAD PERIODS WINDOW
    var periodslayout = popupwin_loadperiods.attachLayout("4U");
    periodslayout.cells("a").setText("Select One or More Seasons");
    periodslayout.cells("b").setText("Settings");
    periodslayout.cells("c").setText("Apply to One or More Rooms");
    periodslayout.cells("d").hideHeader();

    periodslayout.cells("a").setWidth(500);
    periodslayout.cells("b").setWidth(200);
    periodslayout.cells("d").setHeight(45);

    periodslayout.cells("a").fixSize(true, false);
    periodslayout.cells("b").fixSize(true, false);
    periodslayout.cells("c").fixSize(true, false);
    periodslayout.cells("d").fixSize(false, true);

    var grid_loadperiods_seasons = periodslayout.cells("a").attachGrid();
    grid_loadperiods_seasons.setIconsPath('libraries/dhtmlx/imgs/');
    grid_loadperiods_seasons.setHeader(",Season,From,To");
    grid_loadperiods_seasons.setColumnIds("X,season,checkin_dmy,checkout_dmy");
    grid_loadperiods_seasons.setColTypes("ch,ro,ro,ro");
    grid_loadperiods_seasons.setInitWidths("35,250,80,80");
    grid_loadperiods_seasons.setColAlign("center,left,center,center");
    grid_loadperiods_seasons.setColSorting('int,str,date,date');
    grid_loadperiods_seasons.enableStableSorting(true);
    grid_loadperiods_seasons.attachHeader("#master_checkbox,#text_filter,#text_filter,#text_filter");
    grid_loadperiods_seasons.attachEvent("onRowSelect", function (rid, cid) {
        var selected = grid_loadperiods_seasons.cells(rid, grid_loadperiods_seasons.getColIndexById("X")).getValue();
        if (selected == 0)
        {
            selected = 1;
        } else
        {
            selected = 0;
        }

        grid_loadperiods_seasons.cells(rid, grid_loadperiods_seasons.getColIndexById("X")).setValue(selected);
    });
    grid_loadperiods_seasons.init();


    var str_frm_settings = [
        {type: "settings", position: "label-right", id: "form_loadperiods_settings"},

        {type: "radio", name: "settings", value: "overwrite", label: "Overwrite Existing Dates", checked: true},
        {type: "radio", name: "settings", value: "append", label: "Append to Existing Dates"}
    ];
    var form_loadperiods_settings = periodslayout.cells("b").attachForm(str_frm_settings);


    var grid_loadperiods_rooms = periodslayout.cells("c").attachGrid();
    grid_loadperiods_rooms.setIconsPath('libraries/dhtmlx/imgs/');
    grid_loadperiods_rooms.setHeader(",Select Room");
    grid_loadperiods_rooms.setColumnIds("X,room");
    grid_loadperiods_rooms.setColTypes("ch,ro");
    grid_loadperiods_rooms.setInitWidths("40,150");
    grid_loadperiods_rooms.setColAlign("center,left");
    grid_loadperiods_rooms.setColSorting('int,str');
    grid_loadperiods_rooms.enableStableSorting(true);
    grid_loadperiods_rooms.attachHeader("#master_checkbox,#text_filter");
    grid_loadperiods_rooms.attachEvent("onRowSelect", function (rid, cid) {
        var selected = grid_loadperiods_rooms.cells(rid, grid_loadperiods_rooms.getColIndexById("X")).getValue();
        if (selected == 0)
        {
            selected = 1;
        } else
        {
            selected = 0;
        }

        grid_loadperiods_rooms.cells(rid, grid_loadperiods_rooms.getColIndexById("X")).setValue(selected);
    });
    grid_loadperiods_rooms.init();



    var str_frm_loadperiods_actions = [
        {type: "settings", position: "label-right", id: "form_loadperiods_actions"},
        {type: "block", width: 800, list: [
                {type: "button",
                    name: "cmdLoad", value: "OK, Load Periods", width: "150", offsetLeft: 500},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}]}
    ];

    var form_loadperiods_actions = periodslayout.cells("d").attachForm(str_frm_loadperiods_actions);
    form_loadperiods_actions.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_loadperiods.setModal(false);
            popupwin_loadperiods.hide();
            popupwin_contracts.setModal(true);
        }
        if (name == "cmdLoad")
        {
            applyLoadPeriods();
        }
    });


    //=====================================================================
    //ROOMS TAB

    var roomslayout = tabViews.cells("rooms").attachLayout("3W");
    roomslayout.cells("a").hideHeader();
    roomslayout.cells("b").hideHeader();
    roomslayout.cells("c").hideHeader();


    roomslayout.cells("c").setWidth(800);
    roomslayout.cells("a").setWidth(300);
    roomslayout.cells("b").setWidth(135);


    roomslayout.cells("a").fixSize(true, false);
    roomslayout.cells("b").fixSize(true, false);
    roomslayout.cells("c").fixSize(true, false);


    var tree_roomdates = null;

    var toolbar_capacity_dates = roomslayout.cells("a").attachToolbar();
    toolbar_capacity_dates.setIconsPath("images/");
    toolbar_capacity_dates.setIconSize(32);

    var opts = Array(
            Array('new', 'obj', 'New Date', 'add.png'),
            Array('modify', 'obj', 'Modify Date', 'modify.png'),
            Array('delete', 'obj', 'Delete Date', 'delete.png'),
            Array('periods', 'obj', 'Load Periods', 'gantt_chart.png'),
            Array('combi', 'obj', 'Generate Combinations', 'combinations.png'),
            Array('sep1', 'sep', '', ''),
            Array('select_all_rooms', 'obj', 'Select all Rooms', 'bedchecked.png'),
            Array('unselect_all_rooms', 'obj', 'UnSelect all Rooms', 'bedunchecked.png'),
            Array('select_specific_period', 'obj', 'Select This Period in all Rooms', 'gantt_chart_checked.png'),
            Array('unselect_specific_period', 'obj', 'UnSelect This Period in all Rooms', 'gantt_chart_unchecked.png')
            );


    toolbar_capacity_dates.addButtonSelect("opts", 1, "", opts, "operate.png", "operate.png", null, true);
    toolbar_capacity_dates.addSpacer("opts");
    toolbar_capacity_dates.addText("text1", 2, "<div id='divCboFilter' style='width:120px;'></div>");
    toolbar_capacity_dates.addText("text", 3, "<div id='divCboVariant' style='width:80px;'></div>");

    toolbar_capacity_dates.setListOptionToolTip("opts", "new", "Add New Date Period");
    toolbar_capacity_dates.setListOptionToolTip("opts", "modify", "Modify Selected Date Period");
    toolbar_capacity_dates.setListOptionToolTip("opts", "delete", "Delete Selected Date Period");
    toolbar_capacity_dates.setListOptionToolTip("opts", "periods", "Load Date Periods");
    toolbar_capacity_dates.setListOptionToolTip("opts", "combi", "Generate Combinations");

    toolbar_capacity_dates.hideListOption("opts", "new");
    toolbar_capacity_dates.hideListOption("opts", "combi");
    toolbar_capacity_dates.hideListOption("opts", "modify");
    toolbar_capacity_dates.hideListOption("opts", "delete");
    toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
    toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");


    toolbar_capacity_dates.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "new")
        {
            var nodeid = tree_roomdates.getSelectedItemId();

            var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

            if (node == "ROOM")
            {

                form_capacitydates.clear();
                form_capacitydates.setItemValue("id", "-1");
                form_capacitydates.setItemValue("roomid", tree_roomdates.getUserData(nodeid, "ROOM_ROOMID"));


                form_capacitydates.getCalendar("override_dtfrom").clearSensitiveRange();
                form_capacitydates.getCalendar("override_dtto").clearSensitiveRange();
                form_capacitydates.getCalendar("override_dtfrom").setSensitiveRange(form_main.getItemValue("active_from"), form_main.getItemValue("active_to"));
                form_capacitydates.getCalendar("override_dtto").setSensitiveRange(form_main.getItemValue("active_from"), form_main.getItemValue("active_to"));

                form_capacitydates.setItemValue("contract_active_from", form_main.getItemValue("active_from", true));
                form_capacitydates.setItemValue("contract_active_to", form_main.getItemValue("active_to", true));

                popupwin_contracts.setModal(false);
                popupwin_capacitydates.center();
                popupwin_capacitydates.setModal(true);
                popupwin_capacitydates.show();

            }
        } else if (id == "combi")
        {
            var nodeid = tree_roomdates.getSelectedItemId();

            if (nodeid)
            {
                showRoomDateCombinations(nodeid);
            }

        } else if (id == "modify")
        {

            var nodeid = tree_roomdates.getSelectedItemId();

            if (nodeid)
            {
                modifyRoomDate(nodeid);
            }
        } else if (id == "delete")
        {
            var nodeid = tree_roomdates.getSelectedItemId();
            var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

            if (node == "DATE")
            {
                dhtmlx.confirm({
                    title: "Delete Capacity Dates",
                    type: "confirm",
                    text: "Confirm Deletion?",
                    callback: function (tf) {
                        if (tf)
                        {
                            var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                            var date_roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                            deleteCapacityDate(date_roomid, date_rwid);
                            tree_roomdates.deleteItem(nodeid, true);
                            toolbar_capacity_rules.hideItem("new");
                            toolbar_capacity_rules.hideItem("delete");
                            grid_capacity_age.clearAll();
                        }
                    }
                });
            }
        } else if (id == "periods")
        {
            loadPeriods();
        } else if (id == "select_all_rooms")
        {
            toggleSelectedAllRooms(true);

        } else if (id == "unselect_all_rooms")
        {
            toggleSelectedAllRooms(false);

        } else if (id == "select_specific_period")
        {

            toggleSelectedAllPeriods(true);

        } else if (id == "unselect_specific_period")
        {
            toggleSelectedAllPeriods(false);
        }
    });

    var comboVariant = new dhtmlXCombo({
        parent: "divCboVariant",
        type: "image"
    });
    comboVariant.addOption([
        {value: "PERSONS", text: "PAX", img_src: "images/family_three_24.png"},
        {value: "UNITS", text: "UNITS", img_src: "images/adult_24.png"}
    ]);

    comboVariant.readonly(true);
    comboVariant.show(false);
    comboVariant.attachEvent("onChange", onComboVariantChangefunction);


    var comboRoomFilter = new dhtmlXCombo({
        parent: "divCboFilter",
        type: "image"
    });
    comboRoomFilter.readonly(true);
    comboRoomFilter.attachEvent("onChange", onComboRoomFilterChangefunction);


    var grid_room_choices = roomslayout.cells("b").attachGrid();
    grid_room_choices.setIconsPath('libraries/dhtmlx/imgs/');
    grid_room_choices.setHeader(",");
    grid_room_choices.setColumnIds("tmpimg,choice");
    grid_room_choices.setColTypes("ro,ro");
    grid_room_choices.setInitWidths("35,90");
    grid_room_choices.setColAlign("center,left");
    grid_room_choices.setColSorting('str,str');
    grid_room_choices.attachEvent("onRowSelect", onRoomChoicesSelect);
    grid_room_choices.setNoHeader(true);
    grid_room_choices.enableAlterCss("", "");
    grid_room_choices.enableMultiline(true);
    grid_room_choices.init();

    grid_room_choices.addRow("minstay", ["<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">", "MIN STAY"]);
    grid_room_choices.addRow("capacity", ["<img src=\"images/pax.png\" width=\"30px\" height=\"30px\">", "CAPACITY"]);
    grid_room_choices.addRow("policies", ["<img src=\"images/front_desk.png\" width=\"30px\" height=\"30px\">", "POLICIES"]);
    grid_room_choices.addRow("meal", ["<img src=\"images/hot_chocolate.png\" width=\"30px\" height=\"30px\">", "MEAL"]);
    grid_room_choices.addRow("adult_policies", ["<img src=\"images/adult_72.png\" width=\"30px\" height=\"30px\">", "ADULT"]);
    grid_room_choices.addRow("child_policies", ["<img src=\"images/child_72.png\" width=\"30px\" height=\"30px\">", "CHILD"]);
    grid_room_choices.addRow("single_parent", ["<img src=\"images/interview.png\" width=\"30px\" height=\"30px\">", "SINGLE PARENT"]);




    function onRoomChoicesSelect(rid, cid)
    {

        hidePopups();

        //clear all checkboxes and enable all checkboxes
        //clear if
        //_last_grid_choice_id in (minstay,capacity,policies,meal) 
        //and new rid in (adult_policies,child_policies,single_parent)

        if ((_last_grid_choice_id == "minstay" || _last_grid_choice_id == "capacity" ||
                _last_grid_choice_id == "policies" || _last_grid_choice_id == "meal") &&
                (rid == "adult_policies" || rid == "child_policies" ||
                        rid == "single_parent"))
        {
            toggleSelectedAllRooms(false);
            enableAllRoomCheckboxes();
        }


        if (tree_roomdates)
        {
            var nodeid = tree_roomdates.getSelectedItemId();
            toggleRoomViews(nodeid, rid);
        }

        _last_grid_choice_id = rid;
    }

    function showHideRoomTabs(_tabid)
    {
        var arrtabids = tabRoomViews.getAllTabs();
        for (var i = 0; i < arrtabids.length; i++)
        {
            var tabid = arrtabids[i];

            if (tabid == _tabid)
            {
                tabRoomViews.showTab(tabid);
                tabRoomViews.setTabActive(tabid);
            } else
            {
                tabRoomViews.hideTab(tabid);
            }
        }
    }


    var tabRoomViews = roomslayout.cells("c").attachTabbar();
    tabRoomViews.addTab("minstay", "<b>Rooms Minimum Stay</b>", "280px", '');
    tabRoomViews.addTab("capacity", "<b>Capacity</b>", "280px", '');
    tabRoomViews.addTab("policies", "<b>Policies</b>", "280px", '');
    tabRoomViews.addTab("meal", "<b>Meal</b>", "280px", '');
    tabRoomViews.addTab("adult_policies", "<b>Adult Policies</b>", "280px", '');
    tabRoomViews.addTab("child_policies", "<b>Children Policies</b>", "280px", '');
    tabRoomViews.addTab("single_parent", "<b>Single Parent Policies</b>", "280px", '');


    //=====================================================================
    //ROOM.MINSTAY TAB
    var minstay_layout = tabRoomViews.cells("minstay").attachLayout("1C");
    minstay_layout.cells('a').hideHeader();

    var grid_minstay = minstay_layout.cells("a").attachGrid();
    grid_minstay.setIconsPath('libraries/dhtmlx/imgs/');
    grid_minstay.setHeader("Min Night(s) within Period,Description");
    grid_minstay.setColumnIds("minstay_duration,minstay_description");
    grid_minstay.setColTypes("edn,ed");
    grid_minstay.setInitWidths("100,200");
    grid_minstay.setColAlign("center,left");
    grid_minstay.setColSorting('str,str');
    grid_minstay.enableEditTabOnly(true);
    grid_minstay.enableEditEvents(true, true, true);
    grid_minstay.attachEvent("onEditCell", onGridMinStayEdit);
    grid_minstay.init();


    var toolbar_minstay = minstay_layout.cells("a").attachToolbar();
    toolbar_minstay.setIconsPath("images/");
    toolbar_minstay.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_minstay.addSpacer("new");
    toolbar_minstay.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_minstay.setIconSize(32);

    toolbar_minstay.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "new")
        {
            newMinStay();

        } else if (id == "delete")
        {
            var rid = grid_minstay.getSelectedRowId();
            if (rid)
            {
                deleteMinStay(rid);
            }

        }
    });

    toolbar_minstay.hideItem("new");
    toolbar_minstay.hideItem("delete");

    //===============================================================================
    //ROOM.CAPACITY

    var capacity_layout = tabRoomViews.cells("capacity").attachLayout("1C");
    capacity_layout.cells('a').hideHeader();

    var toolbar_capacity_rules = capacity_layout.cells("a").attachToolbar();
    toolbar_capacity_rules.setIconsPath("images/");
    toolbar_capacity_rules.addButton("new", 1, "Add Rule", "add.png", "add.png");
    toolbar_capacity_rules.addSpacer("new");
    toolbar_capacity_rules.addButton("delete", 3, "Delete Rule", "delete.png", "delete.png");
    toolbar_capacity_rules.setIconSize(32);

    toolbar_capacity_rules.hideItem("new");

    toolbar_capacity_rules.hideItem("delete");

    toolbar_capacity_rules.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "new")
        {
            addNewCapacityRule();
        } else if (id == "delete")
        {
            deleteCapacityRule();
        }
    });

    var grid_capacity_age = capacity_layout.cells("a").attachGrid();
    grid_capacity_age.setIconsPath('libraries/dhtmlx/imgs/');


    var str_frm_capacitydates = [
        {type: "settings", position: "label-left", id: "form_capacitydates"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "roomid", required: true},
        {type: "block", width: 550, list: [
                {type: "input", name: "contract_active_from", label: "Contract From:",
                    labelWidth: "120",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
                },
                {type: "newcolumn"},
                {type: "input", name: "contract_active_to", label: "Contract To:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
                }]},
        {type: "block", width: 550, list: [
                {type: "calendar", name: "override_dtfrom", label: "Override From:",
                    labelWidth: "120",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "calendar", name: "override_dtto", label: "Override To:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},
        {type: "block", width: 550, list: [
                {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}]}
    ];

    var form_capacitydates = popupwin_capacitydates.attachForm(str_frm_capacitydates);

    form_capacitydates.getInput("contract_active_from").style.backgroundColor = "#F3E2A9";
    form_capacitydates.getInput("contract_active_to").style.backgroundColor = "#F3E2A9";

    jQuery(function ($) {
        $("[name='override_dtfrom']").mask("99-99-9999");
        $("[name='override_dtto']").mask("99-99-9999");
    });

    form_capacitydates.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_capacitydates.setModal(false);
            popupwin_capacitydates.hide();
            popupwin_contracts.setModal(true);
        }
        if (name == "cmdSave")
        {
            saveCapacityDate();
        }
    });


    var layout_capacitycombii = popupwin_capacitycombinations.attachLayout("1C");
    layout_capacitycombii.cells("a").hideHeader();

    var toolbarCombi = layout_capacitycombii.attachToolbar();
    toolbarCombi.setIconsPath("images/");
    toolbarCombi.addText("text", 1, "");
    toolbarCombi.addSpacer("text");
    toolbarCombi.addButton("exit", 2, "Close", "exit.png", "exit.png");
    toolbarCombi.setIconSize(32);
    toolbarCombi.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "exit")
        {
            popupwin_capacitycombinations.setModal(false);
            popupwin_capacitycombinations.hide();

            popupwin_contracts.setModal(true);
        }
    });


    //=====================================================================
    //ROOM.POLICIES
    var accord_policy = tabRoomViews.cells("policies").attachAccordion();
    accord_policy.addItem("checkinout", "Check In & Check Out Policy");
    accord_policy.addItem("cancellation", "Cancellation Policy");
    //accord_policy.setEffect(true);
    accord_policy.cells("checkinout").open();
    accord_policy.cells("cancellation").close();

    var toolbar_checkinout = accord_policy.cells("checkinout").attachToolbar();
    toolbar_checkinout.setIconsPath("images/");
    toolbar_checkinout.addButton("new", 1, "New Policy", "add.png", "add.png");
    toolbar_checkinout.addButton("delete", 2, "Delete Policy", "delete.png", "delete.png");
    toolbar_checkinout.setItemToolTip("new", "Add New Check In & Check Out Policy");
    toolbar_checkinout.addSpacer("new");
    toolbar_checkinout.setItemToolTip("delete", "Delete Selected Check In & Check Out Policy");
    toolbar_checkinout.setIconSize(32);
    toolbar_checkinout.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            addNewCheckInOutRecord();
        } else if (id == "delete")
        {
            var rid = grid_checkinouts.getSelectedRowId();
            if (rid)
            {
                deleteCheckInOutRecord(rid);
            }
        }
    });



    var grid_checkinouts = accord_policy.cells("checkinout").attachGrid();
    grid_checkinouts.setIconsPath('libraries/dhtmlx/imgs/');

    var toolbar_cancellation = accord_policy.cells("cancellation").attachToolbar();
    toolbar_cancellation.setIconsPath("images/");
    toolbar_cancellation.addButton("new", 1, "New Cancellation", "add.png", "add.png");
    toolbar_cancellation.addButton("delete", 2, "Delete Cancellation", "delete.png", "delete.png");
    toolbar_cancellation.setItemToolTip("new", "Add New Cancellation Policy");
    toolbar_cancellation.addSpacer("new");
    toolbar_cancellation.setItemToolTip("delete", "Delete Selected Cancellation Policy");
    toolbar_cancellation.setIconSize(32);
    toolbar_cancellation.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            addNewCancellationRecord();
        } else if (id == "delete")
        {
            var rid = grid_cancellation.getSelectedRowId();
            if (rid)
            {
                deleteCancellationRecord(rid);
            }
        }
    });

    toolbar_cancellation.hideItem("new");
    toolbar_cancellation.hideItem("delete");

    var grid_cancellation = accord_policy.cells("cancellation").attachGrid();
    grid_cancellation.setIconsPath('libraries/dhtmlx/imgs/');

    //
    //=====================================================================
    //MEAL SUPPLEMENT

    var accord_meal = tabRoomViews.cells("meal").attachAccordion();
    accord_meal.addItem("meal", "Meal Supplements");
    accord_meal.addItem("extra", "Extra Supplements");
    //accord_meal.setEffect(true);
    accord_meal.cells("meal").open();
    accord_meal.cells("extra").close();

    var toolbar_meals = accord_meal.cells("meal").attachToolbar();
    toolbar_meals.setIconsPath("images/");
    toolbar_meals.addButton("new", 1, "New Supplement", "add.png", "add.png");
    toolbar_meals.addButton("delete", 2, "Delete Supplement", "delete.png", "delete.png");
    toolbar_meals.setItemToolTip("new", "Add New Meal Supplement");
    toolbar_meals.addSpacer("new");
    toolbar_meals.setItemToolTip("delete", "Delete Selected Meal Supplement");
    toolbar_meals.setIconSize(32);
    toolbar_meals.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            addNewMealRecord();
        } else if (id == "delete")
        {
            var rid = grid_meals.getSelectedRowId();
            if (rid)
            {


                deleteMealRecord(rid);
            }
        }
    });

    toolbar_meals.hideItem("new");
    toolbar_meals.hideItem("delete");

    var grid_meals = accord_meal.cells("meal").attachGrid();
    grid_meals.setIconsPath('libraries/dhtmlx/imgs/');

    var toolbar_meals_extra = accord_meal.cells("extra").attachToolbar();
    toolbar_meals_extra.setIconsPath("images/");
    toolbar_meals_extra.addButton("new", 1, "New Extra", "add.png", "add.png");
    toolbar_meals_extra.addButton("delete", 2, "Delete Extra", "delete.png", "delete.png");
    toolbar_meals_extra.setItemToolTip("new", "Add New Extra Supplement");
    toolbar_meals_extra.addSpacer("new");
    toolbar_meals_extra.setItemToolTip("delete", "Delete Selected Extra Supplement");
    toolbar_meals_extra.setIconSize(32);
    toolbar_meals_extra.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            addNewExtraRecord();
        } else if (id == "delete")
        {
            var rid = grid_extras.getSelectedRowId();
            if (rid)
            {


                deleteExtraRecord(rid);
            }
        }
    });

    toolbar_meals_extra.hideItem("new");
    toolbar_meals_extra.hideItem("delete");

    var grid_extras = accord_meal.cells("extra").attachGrid();
    grid_extras.setIconsPath('libraries/dhtmlx/imgs/');


    //=====================================================================
    //ADULT POLICY
    var adult_layout = tabRoomViews.cells("adult_policies").attachLayout("1C");
    adult_layout.cells('a').hideHeader();

    var grid_adultpolicy_age = null;
    initialiseAdultPolicyGrid();

    //=====================================================================

    //CHILD POLICY
    var child_layout = tabRoomViews.cells("child_policies").attachLayout("1C");
    child_layout.cells('a').hideHeader();

    var accord_child = child_layout.cells("a").attachAccordion();
    accord_child.addItem("sharing", "Children Sharing Room With Adult");
    accord_child.addItem("single", "Children in Own Room");
    //accord_child.setEffect(true);
    accord_child.cells("sharing").open();
    accord_child.cells("single").close();

    var grid_childpolicy_sharing_age = null;
    var grid_childpolicy_single_age = null;

    initialiseChildPolicySharingGrid();
    initialiseChildPolicySingleGrid();


    //=====================================================================

    //SINGLE PARENT POLICY
    var singleparent_layout = tabRoomViews.cells("single_parent").attachLayout("1C");
    singleparent_layout.cells('a').hideHeader();

    var toolbar_single_parent = singleparent_layout.cells('a').attachToolbar();
    toolbar_single_parent.setIconsPath("images/");
    toolbar_single_parent.setIconSize(32);
    toolbar_single_parent.addText("text1", 1, "Priority to Adults Min:1 Max:1; If NOT found, look for Adults Min:1");

    var grid_singleparentpolicy_age = null;

    initialiseSingleParentPolicyGrid();


    //=====================================================================
    //MAIN TAB

    var str_frm_main = [
        {type: "settings", position: "label-left", id: "form_main"},
        //{type: "hidden", name: "id", required: true},
        {type: "hidden", name: "hotelfk", required: true},
        {type: "block", width: 900, list: [
                {type: "input", name: "id", label: "Contract ID:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
                },
                {type: "input", name: "contractname", label: "Contract Name:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                }]},
        {type: "block", width: 900, list: [
                {type: "input", name: "invoice_text", label: "Invoice Text:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }]},
        {type: "block", width: 900, list: [
                {type: "checkbox", name: "active_internal", label: "Active Internal:", labelWidth: "90",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "0", inputTop: "0"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "checkbox", name: "active_external", label: "Active External:", labelWidth: "90",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "0", inputTop: "0"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "checkbox", name: "non_refundable", label: "Non Refundable:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "0", inputTop: "0"},
            ]},

        {type: "block", width: 900, list: [
                {type: "calendar", name: "date_received", label: "Date Received:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "service_code", label: "Service Code:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "80", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", value: "ACC"
                }]},
        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "calendar", name: "active_from", label: "Validity From:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "calendar", name: "active_to", label: "Validity To:", labelWidth: "80",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "mealplan_fk", label: "Meal Plan:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]},
        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "combo", name: "countryfk", label: "Country:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "combo", name: "areafk", label: "Area:", labelWidth: "45",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "combo", name: "coastfk", label: "Coast:", labelWidth: "50",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "input", name: "selected_rate_codes_display", label: "Rates Type:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "593", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 1
                },
                {type: "hidden", name: "selected_rate_codes_ids"},
                {type: "newcolumn"},
                {type: "button", id: "cmdLoadRates", name: "cmdLoadRates", tooltip: "Select Rates", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "combo", name: "cross_season", label: "Cross Season:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "120", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "combo", name: "rollover_basis", label: "Rollover Basis:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "input", name: "rollover_value", label: "Value", labelWidth: "50",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true}
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},
        {type: "block", width: 900, list: [
                {type: "input", name: "children_ages_display", label: "Children Policy:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    readonly: true, rows: 1
                },
                {type: "hidden", name: "children_ages_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadChildrenPolicy", tooltip: "Select Children Ages", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "input", name: "market_countries_display", label: "Countries:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 4
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "input", name: "tour_operators_display", label: "Tour Operators:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 4
                },
                {type: "hidden", name: "tour_operators_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadTourOperators", tooltip: "Select Tour Operators", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "input", name: "rooms_display", label: "Rooms:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "rooms_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadRooms", tooltip: "Select Rooms", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "label", label: "<b><hr></b>", labelWidth: "800"},

        {type: "block", width: 900, list: [
                {type: "input", name: "departments_display", label: "Departments:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 1
                },
                {type: "hidden", name: "departments_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoaddepartments", tooltip: "Select Departments", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]}
    ];




    var form_main = tabViews.cells("main").attachForm(str_frm_main);


    form_main.attachEvent("onChange", function (id, value) {
        if (id == "countryfk")
        {
            loadArea(value, "");
        } else if (id == "active_from")
        {
            checkMainRange(id, value);
            formatContractTitle();

        } else if (id == "active_to")
        {
            checkMainRange(id, value);
            formatContractTitle();
        } else if (id == "contractname")
        {
            formatContractTitle();
        } else if (id == "active_internal")
        {
            var chked = form_main.getItemValue("active_internal");
            if (chked == 0)
            {
                form_main.setItemValue("active_external", 0);
            }

        } else if (id == "active_external")
        {
            var ext_chked = form_main.getItemValue("active_external");
            var int_chked = form_main.getItemValue("active_internal");
            if (ext_chked == 1 && int_chked == 0)
            {
                form_main.setItemValue("active_external", 0);
            }
        }
    });

    form_main.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdLoadRates")
        {
            showPopUp(form_main, "Rates", "selected_rate_codes_display", "selected_rate_codes_ids", _dsRates, "MULTIPLE", null);
        } else if (name == "cmdLoadChildrenPolicy")
        {
            showPopUp(form_main, "Children Ages", "children_ages_display", "children_ages_ids", _dsChildPolicy, "MULTIPLE", updateChildAges);
        } else if (name == "cmdLoaddepartments")
        {
            showPopUp(form_main, "Departments", "departments_display", "departments_ids", _dsDepartments, "MULTIPLE", null);
        } else if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_main, "Countries", "market_countries_display", "market_countries_ids", "MULTIPLE", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_main, "Rooms", "rooms_display", "rooms_ids", _dsRooms, "MULTIPLE", updateRooms);
        } else if (name == "cmdLoadTourOperators")
        {
            showPopUpTourOperators(form_main, "Tour Operators", "tour_operators_display", "tour_operators_ids", "MULTIPLE", null);
        }
    });


    jQuery(function ($) {
        $("[name='date_received']").mask("99-99-9999");
        $("[name='active_from']").mask("99-99-9999");
        $("[name='active_to']").mask("99-99-9999");
    });

    form_main.getInput("service_code").style.fontWeight = "bold";
    form_main.getInput("service_code").style.textAlign = "center";
    form_main.getInput("service_code").style.backgroundColor = "#F3E2A9";

    form_main.getInput("id").style.fontWeight = "bold";
    form_main.getInput("id").style.textAlign = "center";
    form_main.getInput("id").style.backgroundColor = "#F3E2A9";


    var cboMealPlan = form_main.getCombo("mealplan_fk");
    cboMealPlan.enableOptionAutoPositioning(true);
    var dsMealPlan = new dhtmlXDataStore();
    dsMealPlan.load("php/api/hotelcontracts/mealplan_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsMealPlan.dataCount(); i++)
        {
            var item = dsMealPlan.item(dsMealPlan.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboMealPlan.addOption([{value: value, text: txt, img_src: "images/hot_chocolate.png_32x32.png"}]);
        }

        cboMealPlan.readonly(true);
    });


    var cboCountry = form_main.getCombo("countryfk");
    cboCountry.enableOptionAutoPositioning(true);
    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/bckoffhotels/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

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



    var cboArea = form_main.getCombo("areafk");
    var dsArea = new dhtmlXDataStore();
    cboArea.enableOptionAutoPositioning(true);


    var cboCoast = form_main.getCombo("coastfk");
    var dsCoast = new dhtmlXDataStore();
    cboCoast.enableOptionAutoPositioning(true);
    dsCoast.load("php/api/combos/coast_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCoast.dataCount(); i++)
        {
            var item = dsCoast.item(dsCoast.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboCoast.addOption([{value: value, text: txt, img_src: "images/coast.png"}]);
        }
        cboCoast.readonly(true);
        cboCoast.addOption([{value: "", text: "[SELECT]", img_src: "images/coast.png"}]);
    });

    var cboCrossSeason = form_main.getCombo("cross_season");
    cboCrossSeason.addOption([{value: "first_rate", text: "First Rate", img_src: "images/season.png"}]);
    cboCrossSeason.addOption([{value: "split", text: "Split", img_src: "images/season.png"}]);
    cboCrossSeason.readonly(true);

    var cboRollOverBasis = form_main.getCombo("rollover_basis");
    cboRollOverBasis.addOption([{value: "percentage", text: "Percentage", img_src: "images/rollover.png"}]);
    cboRollOverBasis.addOption([{value: "add_per_night", text: "Add Per Night", img_src: "images/rollover.png"}]);
    cboRollOverBasis.readonly(true);
    //=========================================================================

    //=====================================================================
    //CURRENCY TAB

    var currency_layout = tabViews.cells("currency").attachLayout("3T");
    currency_layout.cells("a").hideHeader();
    currency_layout.cells("b").setText("Exchange Rates");
    currency_layout.cells("c").setText("Currency Buy Sell Mapping");

    currency_layout.cells("a").setHeight(100);
    currency_layout.cells("b").setWidth(300);
    currency_layout.cells("c").setWidth(500);

    currency_layout.cells("a").fixSize(true, true);
    currency_layout.cells("b").fixSize(true, true);
    currency_layout.cells("c").fixSize(true, true);

    var str_frm_currency = [
        {type: "settings", position: "label-left", id: "form_currency"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "mycostprice_currencyfk", label: "Default Currency:",
                    labelWidth: "80",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/", note: {text: "Used as Cost Price"}
                }]},
        {type: "block", width: 900, list: [
                {type: "input", name: "selected_currency_buy_display",
                    label: "Buying:", labelWidth: "80",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 1
                },
                {type: "hidden", name: "selected_currency_buy_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCurrencyBuy", tooltip: "Select Currencies", value: "...", width: "30", height: "40", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "input", name: "selected_currency_sell_display", label: "Selling:",
                    labelWidth: "60",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 1
                },
                {type: "hidden", name: "selected_currency_sell_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCurrencySell", tooltip: "Select Currencies", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]}];

    var form_currency = currency_layout.cells("a").attachForm(str_frm_currency);

    form_currency.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdLoadCurrencySell")
        {
            showPopUp(form_currency, "Sell Currencies", "selected_currency_sell_display", "selected_currency_sell_ids", _dsCurrencies, "SINGLE", updateExchangeRatesJson);
        } else if (name == "cmdLoadCurrencyBuy")
        {
            showPopUp(form_currency, "Buy Currencies", "selected_currency_buy_display", "selected_currency_buy_ids", _dsCurrencies, "SINGLE", updateExchangeRatesJson);
        }
    });


    var grid_exchrates = currency_layout.cells("b").attachGrid();
    grid_exchrates.setIconsPath('libraries/dhtmlx/imgs/');
    grid_exchrates.setHeader("Currency From,Currency To,Exchange Rate");
    grid_exchrates.setColumnIds("currency_from,currency_to,rates_exchange_rate");
    grid_exchrates.setColTypes("ro,ro,edn");
    grid_exchrates.setInitWidths("100,100,100");
    grid_exchrates.setColAlign("center,center,right");
    grid_exchrates.setColSorting('str,str,int');
    grid_exchrates.enableEditTabOnly(true);
    grid_exchrates.enableEditEvents(true, true, true);
    grid_exchrates.enableStableSorting(true);
    grid_exchrates.attachEvent("onEditCell", onGridExgRateEdit);
    grid_exchrates.init();

    var grid_currencymap = currency_layout.cells("c").attachGrid();
    grid_currencymap.setIconsPath('libraries/dhtmlx/imgs/');
    grid_currencymap.setHeader("Currency Sell,Maps From Currency Buy");
    grid_currencymap.setColumnIds("currency_sell,currency_buy");
    grid_currencymap.setColTypes("ro,combo");
    grid_currencymap.setInitWidths("120,120");
    grid_currencymap.setColAlign("center,center");
    grid_currencymap.setColSorting('str,str');
    grid_currencymap.enableEditTabOnly(true);
    grid_currencymap.enableEditEvents(true, true, true);
    grid_currencymap.enableStableSorting(true);
    grid_currencymap.attachEvent("onEditCell", onGridCurrMapEdit);
    grid_currencymap.init();

    var cboCostPriceCurrency = form_currency.getCombo("mycostprice_currencyfk");
    cboCostPriceCurrency.enableOptionAutoPositioning(true);
    var dsCurrency = new dhtmlXDataStore();


    dsCurrency.load("php/api/combos/currency_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCurrency.dataCount(); i++)
        {
            var item = dsCurrency.item(dsCurrency.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            if (item.use_for_costprice == "1")
            {
                cboCostPriceCurrency.addOption([{value: value, text: txt, img_src: "images/currency.png"}]);
                cboCostPriceCurrency.setComboValue(value);
            }
        }
        cboCostPriceCurrency.readonly(true);
    });


    //=====================================================================
    //NOTES TAB
    var notes_layout = tabViews.cells("notes").attachLayout("1C");
    notes_layout.cells('a').hideHeader();

    var str_frm_notes = [
        {type: "settings", position: "label-left", id: "form_notes"},
        {type: "editor", name: "internal_notes", label: "Internal Notes:", labelWidth: "100",
            labelHeight: "22", inputWidth: "800", inputHeight: "170", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "editor", name: "external_notes", label: "External Notes:", labelWidth: "100",
            labelHeight: "22", inputWidth: "800", inputHeight: "170", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        }
    ];

    var form_notes = notes_layout.cells("a").attachForm(str_frm_notes);

    //=====================================================================
    //TAX AND COMMISSION
    var tax_layout = tabViews.cells("tax").attachLayout("2U");
    tax_layout.cells('a').hideHeader();
    tax_layout.cells('b').hideHeader();

    var accord_taxcommi = tax_layout.cells('b').attachAccordion();

    accord_taxcommi.addItem("buying", "Buying Settings");
    accord_taxcommi.addItem("selling", "Selling Settings");
    //accord_taxcommi.setEffect(true);
    accord_taxcommi.cells("buying").open();
    accord_taxcommi.cells("selling").close();

    tax_layout.cells('b').setWidth(1000);
    tax_layout.cells('a').setWidth(150);

    tax_layout.cells('a').fixSize(true, true);
    tax_layout.cells('b').fixSize(true, true);

    var toolbar_taxcommi_tree = tax_layout.cells("a").attachToolbar();
    toolbar_taxcommi_tree.setIconsPath("images/");
    toolbar_taxcommi_tree.setIconSize(32);
    var opts_taxcommi = Array(
            Array('new_exception', 'obj', 'Create Room Exception', 'add.png'),
            Array('test_settings', 'obj', 'Test Settings', 'exam_pass.png'),
            Array('settings_profile', 'obj', 'Setting Profiles', 'modules.png'),
            Array('sep1', 'sep'),
            Array('delete_exception', 'obj', 'Delete Room Exception', 'delete.png'));

    toolbar_taxcommi_tree.addButtonSelect("opts", 1, "", opts_taxcommi, "operate.png", "operate.png", null, true);

    toolbar_taxcommi_tree.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "new_exception")
        {
            addTaxCommiException();
        } else if (id == "delete_exception")
        {
            deleteTaxCommiException();
        } else if (id == "test_settings")
        {
            testTaxCommiSettings();
        } else if (id == "settings_profile")
        {
            taxCommiSettingsProfile();
        }
    });


    var tree_taxcomm = null;

    var grid_taxcomm_buy = accord_taxcommi.cells("buying").attachGrid();
    grid_taxcomm_buy.setIconsPath('libraries/dhtmlx/imgs/');


    var toolbar_taxcommi_buy = accord_taxcommi.cells("buying").attachToolbar();
    toolbar_taxcommi_buy.setIconsPath("images/");

    var opts_buy = Array(
            Array('additem', 'obj', 'Add Item', 'add.png'),
            Array('moveup', 'obj', 'Move Item Up', 'outbox.png'),
            Array('movedown', 'obj', 'Move Item Down', 'inbox.png'),
            Array('deleteitem', 'obj', 'Delete Item', 'delete.png'));
    toolbar_taxcommi_buy.addButtonSelect("opts", 1, "", opts_buy, "operate.png", "operate.png", null, true);
    toolbar_taxcommi_buy.setIconSize(32);


    toolbar_taxcommi_buy.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "additem")
        {

            showTaxCommItems(grid_taxcomm_buy, "BUYING");

        } else if (id == "deleteitem")
        {
            deleteTaxCommiItem(grid_taxcomm_buy, "BUYING");
        } else if (id == "moveup")
        {
            moveTaxCommiItem(grid_taxcomm_buy, "buying_settings", "UP");
        } else if (id == "movedown")
        {
            moveTaxCommiItem(grid_taxcomm_buy, "buying_settings", "DOWN");
        }
    });

    var grid_taxcomm_sell = accord_taxcommi.cells("selling").attachGrid();
    grid_taxcomm_sell.setIconsPath('libraries/dhtmlx/imgs/');


    var toolbar_taxcommi_sell = accord_taxcommi.cells("selling").attachToolbar();
    toolbar_taxcommi_sell.setIconsPath("images/");
    var opts_sell = Array(
            Array('additem', 'obj', 'Add Item', 'add.png'),
            Array('moveup', 'obj', 'Move Item Up', 'outbox.png'),
            Array('movedown', 'obj', 'Move Item Down', 'inbox.png'),
            Array('deleteitem', 'obj', 'Delete Item', 'delete.png'));
    toolbar_taxcommi_sell.addButtonSelect("opts", 1, "", opts_sell, "operate.png", "operate.png", null, true);
    toolbar_taxcommi_sell.setIconSize(32);


    toolbar_taxcommi_sell.attachEvent("onClick", function (id) {

        hidePopups();

        if (id == "additem")
        {
            showTaxCommItems(grid_taxcomm_sell, "SELLING");

        } else if (id == "deleteitem")
        {
            deleteTaxCommiItem(grid_taxcomm_sell, "SELLING");
        } else if (id == "moveup")
        {
            moveTaxCommiItem(grid_taxcomm_sell, "selling_settings", "UP");
        } else if (id == "movedown")
        {
            moveTaxCommiItem(grid_taxcomm_sell, "selling_settings", "DOWN");
        }
    });



    var test_taxcomm_layout = popupwin_testtaxcomm.attachLayout("1C");
    test_taxcomm_layout.cells("a").hideHeader();

    var grid_test_taxcomm = test_taxcomm_layout.cells("a").attachGrid();
    grid_test_taxcomm.setIconsPath('libraries/dhtmlx/imgs/');

    var toolbar_test_taxcomm = test_taxcomm_layout.cells("a").attachToolbar();
    toolbar_test_taxcomm.setIconsPath("images/");
    toolbar_test_taxcomm.addButton("exit", 1, "Exit", "exit.png", "exit.png");
    toolbar_test_taxcomm.setIconSize(32);
    toolbar_test_taxcomm.setAlign('right');

    toolbar_test_taxcomm.attachEvent("onClick", function (id) {
        if (id == "exit")
        {
            popupwin_testtaxcomm.setModal(false);
            popupwin_testtaxcomm.hide();
            popupwin_contracts.setModal(true);
        }
    });


    var profile_taxcomm_layout = popupwin_profile_taxcomm.attachLayout("1C");
    profile_taxcomm_layout.cells("a").hideHeader();

    var grid_profile_taxcomm = profile_taxcomm_layout.cells("a").attachGrid();
    grid_profile_taxcomm.setIconsPath('libraries/dhtmlx/imgs/');
    grid_profile_taxcomm.setHeader("Profile,Description");
    grid_profile_taxcomm.setColumnIds("profile_name,profile_description");
    grid_profile_taxcomm.setColTypes("ed,txt");
    grid_profile_taxcomm.setInitWidths("300,1000");
    grid_profile_taxcomm.setColAlign("left,left");
    grid_profile_taxcomm.setColSorting('str,str');
    grid_profile_taxcomm.enableUndoRedo();
    grid_profile_taxcomm.attachEvent("onEditCell", profileEdit);
    grid_profile_taxcomm.enableAlterCss("", "");
    grid_profile_taxcomm.enableMultiline(true);
    grid_profile_taxcomm.init();

    var toolbar_profile_taxcomm = profile_taxcomm_layout.cells("a").attachToolbar();
    toolbar_profile_taxcomm.setIconsPath("images/");

    var opts_profile_save = Array(
            Array('save_new', 'obj', 'Save as New Profile', 'save_new.png'),
            Array('over_save', 'obj', 'Overwrite Selected Profile', 'save.png')
            );

    toolbar_profile_taxcomm.addButton("load", 1, "Load Profile", "exam_pass.png", "exam_pass.png");
    toolbar_profile_taxcomm.addButtonSelect("save", 2, "Save...", opts_profile_save, "save.png", "save.png", null, true);
    toolbar_profile_taxcomm.addSpacer("save");
    toolbar_profile_taxcomm.addButton("delete", 3, "Delete Profile", "delete.png", "delete.png");
    toolbar_profile_taxcomm.addSpacer("delete");
    toolbar_profile_taxcomm.addButton("exit", 4, "Exit", "exit.png", "exit.png");
    toolbar_profile_taxcomm.setIconSize(32);


    toolbar_profile_taxcomm.attachEvent("onClick", function (id) {
        if (id == "exit")
        {
            popupwin_profile_taxcomm.setModal(false);
            popupwin_profile_taxcomm.hide();
            popupwin_contracts.setModal(true);
        } else if (id == "load")
        {
            loadSettingsProfile();
        } else if (id == "save_new")
        {
            rememberProfileState("-1");

        } else if (id == "over_save")
        {
            rememberOverProfileState();

        } else if (id == "delete")
        {
            deleteSettingsProfile();
        }
    });


    //===============================================================================

    applyrights();

    //==============================================================================

    function newMinStay()
    {
        var nodeid = tree_roomdates.getSelectedItemId();

        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");


        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var nodeid_room = "ROOM_" + roomid;


        //one max row allowed
        if (grid_minstay.getRowsNum() > 0)
        {
            dhtmlx.alert({
                text: "A maximum of <b>One</b> Minimum Stay Rule allowed per Room for the selected Date!",
                type: "alert-warning",
                title: "New Min Stay Rule",
                callback: function () {
                }
            });

            return false;
        }


        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _min_stay_id--;

                        var obj = {minstay_rwid: _min_stay_id,
                            minstay_description: "ONE NIGHT",
                            minstay_action: "INSERT",
                            minstay_duration: "1"};
                        arrdates[j].date_minstay_rules.push(obj);
                        grid_minstay.addRow(_min_stay_id, [0, ""]);
                        grid_minstay.setRowTextStyle(_min_stay_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        return;
                    }
                }
            }
        }
    }

    function validate_currency()
    {
        var error = [];

        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();
        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");


        if (costprice_currencyid == "" || !costprice_currencyid || costprice_currencyid == "-1")
        {
            error.push({msg: "<b>Currency:</b> Missing <b>Cost Price</b> Currency"});
        }

        if (selected_currency_buy_ids == "")
        {
            error.push({msg: "<b>Currency:</b> Missing <b>Buying</b> Currency"});
        }

        var arrbuyids = selected_currency_buy_ids.split(",");
        if (arrbuyids.length > 1)
        {
            error.push({msg: "<b>Currency:</b> Only ONE <b>Buying</b> Currency</b> can be selected"});
        }

        if (selected_currency_sell_ids == "")
        {
            error.push({msg: "<b>Currency:</b> Missing <b>Selling</b> Currency</b>"});
        }

        return error;
    }


    function validate_currency_mapping()
    {
        var error = [];

        //make sure that each currency sell has a currency buy mapped to it
        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            var obj = _json_exchangerates.currency_mapping[i];
            if (obj.mapping_action != "DELETE")
            {
                var itemCurrency = _dsCurrencies.item(obj.mapping_sell_currencyfk);
                var currencysell_code = itemCurrency.value;

                if (obj.mapping_buy_currencyfk == "")
                {
                    error.push({msg: "<b>Currency:</b> Missing Mapping Currency Buy for <b>" + currencysell_code + "</b>"});

                } else
                {
                    //make sure that currency buy is in the list of selected currency buys
                    var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
                    var arr_selected_buyids = selected_currency_buy_ids.split(",");
                    if (arr_selected_buyids.indexOf(obj.mapping_buy_currencyfk) == -1)
                    {
                        error.push({msg: "<b>Currency:</b> Missing Mapping Currency Buy for <b>" + currencysell_code + "</b>"});
                    }
                }
            }
        }

        return error;
    }


    function validate_contract_dates()
    {
        var dtfrom = form_main.getItemValue("active_from");
        var dtto = form_main.getItemValue("active_to");

        if (dtfrom == "" || dtto == "" || !dtfrom || !dtto)
        {
            return "<b>Validity date:</b> Required <b>date from</b> and <b>date to</b>";
        }

        if (dtto < dtfrom)
        {
            return "<b>Validity date:</b> Invalid dates order!";
        }
        return "";
    }


    function newContract()
    {
        grid_choices.selectRowById("main", false, true, true);

        form_main.clear();
        form_main.setItemValue("id", "-1");
        form_main.setItemValue("hotelfk", global_hotel_id);
        form_main.setItemValue("service_code", "ACC");
        form_main.getCalendar("active_from").clearSensitiveRange();
        form_main.getCalendar("active_to").clearSensitiveRange();


        form_main.setItemValue("service_code", "ACC");
        form_main.setItemValue("cross_season", "split");


        form_currency.clear();
        form_notes.clear();

        loadHotelLocations();
        loadTaxCommi("-1", null);
        resetVariables();

        popupwin_contracts.center();
        popupwin_contracts.show();
        popupwin_contracts.setModal(true);

    }


    function loadArea(countryid, selectAreaId)
    {
        if (countryid == "" || !countryid)
        {
            countryid = "-1";
        }


        cboArea.clearAll(true);
        dsArea = null;
        dsArea = new dhtmlXDataStore();
        cboArea.setComboText("Loading...");

        dsArea.load("php/api/combos/area_combo.php?t=" + encodeURIComponent(global_token) + "&cid=" + countryid, "json", function () {

            cboArea.setComboText("");

            for (var i = 0; i < dsArea.dataCount(); i++)
            {
                var item = dsArea.item(dsArea.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                cboArea.addOption([{value: value, text: txt, img_src: "images/area.png"}]);
            }

            if (selectAreaId != "")
            {
                cboArea.setComboValue(selectAreaId);
            }
            cboArea.readonly(false);
            cboArea.enableFilteringMode(true);
        });
    }


    //==========================
    popupwin_contracts.hide();


    function showPopUp(form, caller, inputdisplay, inputid, ds, single_multiple, callback)
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

        pop_grid.sync(ds);


        var selectedids = utils_trim(form.getItemValue(inputid), " ");
        var selectedvalues = utils_trim(form.getItemValue(inputdisplay), " ");

        _last_popup_ids = selectedids;
        _last_popup_display_values = selectedvalues;

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
        popupwin_contracts.setModal(false);
        popupwin.setModal(true);

    }


    //====================
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
        var countries_ids = form_main.getItemValue("market_countries_ids");
        _dsTOs.load("php/api/hotelcontracts/touroperatorgrid.php?t=" + encodeURIComponent(global_token) + "&countries_ids=" + encodeURIComponent(countries_ids), "json", function () {
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
        popupwin_contracts.setModal(false);
        popupwin.setModal(true);
    }

    //====================

    function showPopUpCountries(form, caller, inputdisplay, inputid)
    {
        var dim = popupwin_contracts.getDimension();
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
        pop_layout.cells("a").setHeight((height - 10));
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
                form.setItemValue("tour_operators_display","");
                form.setItemValue("tour_operators_ids","");
                
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
        pop_grid = pop_layout.cells("a").attachGrid(550, (height - 40));
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
        pop_grid.loadXML("php/api/hotelcontracts/marketgridxml.php?t=" + encodeURIComponent(global_token), function () {
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
        popupwin_contracts.setModal(false);
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

    function loadPopupDs()
    {

        _dsCurrencies.load("php/api/hotelcontracts/currencygrid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});
        _dsRates.load("php/api/hotelcontracts/rategrid.php?t=" + global_token, "json", function () {});
        _dsChildPolicy.load("php/api/hotelcontracts/childrenagegrid.php?t=" + global_token, "json", function () {});
        _dsDepartments.load("php/api/hotelcontracts/departmentgrid.php?t=" + global_token, "json", function () {});
        _dsRooms.load("php/api/hotelcontracts/hotelroomsgrid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});
        _dsGridMealCombo.load("php/api/hotelcontracts/mealplan_combo.php?t=" + global_token, "json", function () {});
        _dsTaxCommiItems.load("php/api/hotelcontracts/taxcommi_items.php?t=" + global_token, "json", function () {});
    }

    function loadExchangeRates(contractid, callback)
    {
        detailslayout.progressOn();

        _json_exchangerates = {exchange_rates: [], currency_mapping: []};

        var params = "cid=" + contractid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelcontracts/loadexchangerates.php", params, function (loader) {
            detailslayout.progressOff();
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD EXCHANGE RATES",
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
                        title: "LOAD EXCHANGE RATES",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    _json_exchangerates = json_obj.EXCHANGE_RATES;


                    if (callback)
                    {
                        callback();
                    }

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD EXCHANGE RATES",
                        callback: function () {
                        }
                    });
                }

            }
        });
    }


    function loadCapacity(contractid, callback)
    {
        detailslayout.progressOn();
        _json_capacity = null;

        var params = "cid=" + contractid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelcontracts/loadcapacity.php", params, function (loader) {
            detailslayout.progressOff();
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD CAPACITY",
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
                        title: "LOAD CAPACITY",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    _json_capacity = json_obj.CAPACITY;

                    console.log("loading contract:", _json_capacity);

                    if (callback)
                    {
                        callback();
                    }
                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD CAPACITY",
                        callback: function () {
                        }
                    });
                }

            }
        });
    }

    function loadTaxCommi(contractid, callback)
    {
        detailslayout.progressOn();
        _json_taxcommi = [];

        var params = "cid=" + contractid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelcontracts/loadtaxcommi.php", params, function (loader) {

            detailslayout.progressOff();
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD TAX AND COMMISSION",
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
                        title: "LOAD TAX AND COMMISSION",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    _json_taxcommi = json_obj.TAXCOMMI;
                    loadInitialRowsTaxCommi();
                    loadDefaultTaxCommiValues();

                    if (callback)
                    {
                        callback();
                    }

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD TAX AND COMMISSION",
                        callback: function () {
                        }
                    });
                }
            }
        });
    }

    function loadInitialRowsTaxCommi()
    {
        //if taxcommi.GENERAL is blank, then add a general node and place default rows
        for (var i = 0; i < _json_taxcommi.length; i++)
        {
            if (_json_taxcommi[i].room_id == "GENERAL")
            {
                var buying_settings = _json_taxcommi[i].buying_settings;
                var selling_settings = _json_taxcommi[i].selling_settings;

                if (buying_settings.length == 0)
                {
                    //add item cost price
                    var cp_item = lookupTaxCommiItem("CP");
                    if (cp_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "buying_settings",
                                "BUYING",
                                cp_item.id,
                                cp_item.item_name,
                                cp_item.abbrv,
                                cp_item.code,
                                cp_item.core_addon,
                                "", "", "ROUNDUP");
                    }


                    //add item markup
                    var markup_item = lookupTaxCommiItem("MKUP");
                    if (markup_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "buying_settings",
                                "BUYING",
                                markup_item.id,
                                markup_item.item_name,
                                markup_item.abbrv,
                                markup_item.code,
                                markup_item.core_addon,
                                "", "A", "ROUNDUP");
                    }


                    //add item transitional row
                    var transrow_item = lookupTaxCommiItem("TRSRW");
                    if (transrow_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "buying_settings",
                                "BUYING",
                                transrow_item.id,
                                transrow_item.item_name,
                                transrow_item.abbrv,
                                transrow_item.code,
                                transrow_item.core_addon,
                                "", "A + B", "ROUNDUP");
                    }


                    //========================================

                    resetTaxCommiRowIndex("GENERAL");
                }

                //===================================================
                //===================================================

                if (selling_settings.length == 0)
                {
                    //add item converted sellprice
                    //add item final sellprice

                    var convertedsp_item = lookupTaxCommiItem("CVSP");
                    if (convertedsp_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "selling_settings",
                                "SELLING",
                                convertedsp_item.id,
                                convertedsp_item.item_name,
                                convertedsp_item.abbrv,
                                convertedsp_item.code,
                                convertedsp_item.core_addon,
                                "", "C", "ROUNDUP");
                    }


                    //add item commission
                    var commi_item = lookupTaxCommiItem("COMMI");
                    if (commi_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "selling_settings",
                                "SELLING",
                                commi_item.id,
                                commi_item.item_name,
                                commi_item.abbrv,
                                commi_item.code,
                                commi_item.core_addon,
                                "", "D", "NOROUND");
                    }


                    var finalsp_item = lookupTaxCommiItem("FINALSP");
                    if (finalsp_item)
                    {
                        insertTaxCommiJsonNode("GENERAL", "selling_settings",
                                "SELLING",
                                finalsp_item.id,
                                finalsp_item.item_name,
                                finalsp_item.abbrv,
                                finalsp_item.code,
                                finalsp_item.core_addon,
                                "", "D + E", "ROUNDUP");
                    }

                    resetTaxCommiRowIndex("GENERAL");
                }
            }
        }

    }

    function lookupTaxCommiItem(itemcode)
    {
        for (var i = 0; i < _dsTaxCommiItems.dataCount(); i++)
        {
            var item = _dsTaxCommiItems.item(_dsTaxCommiItems.idByIndex(i));
            if (item.code == itemcode)
            {
                return item;
            }
        }

        return null;
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

    function deleteMinStay(rid)
    {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");

        grid_minstay.deleteRow(rid);
        updateJsonCapacityMinStay("minstay_action", roomid, daterwid, rid, "DELETE");
        updateAllCheckedDatesNode("date_minstay_rules");
    }



    function checkMainRange(name, value) {
        if (name == "active_to") {
            form_main.getCalendar("active_from").setSensitiveRange(null, value);
        } else {
            form_main.getCalendar("active_to").setSensitiveRange(value, null);
        }
    }


    function createExtrasGridStructure(nodeid)
    {
        var header_str = "Name*,Date*,Is Manda tory,Include Diner Rate for BB,HB Meal Plan,BB Meal Plan,Adults";
        var column_ids = "extra_name,extra_date,mandatory,include_diner_rate_bb,hb_mealplan_fk,bb_mealplan_fk,adult_count";
        var column_types = "ed,dhxCalendar,ch,ch,combo,combo,edn";
        var init_widths = "80,80,50,50,100,100," + (_agecolwidth + 30);
        var col_align = "left,center,center,center,left,left,center";
        var col_sorting = "str,date,int,int,str,str,int";

        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = item.agefrom;
                var ageto = item.ageto;

                header_str += ",Ch " + agefrom + "-" + ageto;
                column_ids += ",ch_" + agefrom + "_" + ageto;
                column_types += ",edn";
                init_widths += "," + (_agecolwidth + 30);
                col_align += ",center";
                col_sorting += ",int";
            }
        }


        grid_extras.clearAll();
        grid_extras = null;
        grid_extras = accord_meal.cells("extra").attachGrid();
        grid_extras.setIconsPath('libraries/dhtmlx/imgs/');
        grid_extras.setHeader(header_str);
        grid_extras.setColumnIds(column_ids);
        grid_extras.setColTypes(column_types);
        grid_extras.setInitWidths(init_widths);
        grid_extras.setColAlign(col_align);
        grid_extras.setColSorting(col_sorting);
        grid_extras.enableAlterCss("", "");
        grid_extras.enableEditTabOnly(true);
        grid_extras.enableEditEvents(true, true, true);
        grid_extras.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
        grid_extras.attachEvent("onEditCell", onGridExtraEdit);
        grid_extras.attachEvent("onCheck", onGridExtraCheck);
        grid_extras.init();

        grid_extras.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_extras.setEditable(true);
        }

        loadExtraGridCombos();

        return;

    }

    function createCancellationGridStructure(nodeid)
    {
        var header_attach = "#rspan,Method*,Value*,From,To,From,To";
        var header_str = "Type*,Charge,#cspan,Days Before Arrival,#cspan,Dates Before Arrival,#cspan";
        var column_ids = "cancellation_canceltype,cancellation_charge_method,cancellation_charge_value,cancellation_days_before_arrival_from,cancellation_days_before_arrival_to,cancellation_dates_before_arrival_from,cancellation_dates_before_arrival_to";
        var column_types = "combo,combo,edn,edn,edn,dhxCalendar,dhxCalendar";
        var init_widths = "110,110,70,60,60,90,90";
        var col_align = "left,left,center,center,center,center,center";
        var col_sorting = "str,str,int,int,int,date,date";


        grid_cancellation.clearAll();
        grid_cancellation = null;
        grid_cancellation = accord_policy.cells("cancellation").attachGrid();
        grid_cancellation.setIconsPath('libraries/dhtmlx/imgs/');
        grid_cancellation.setHeader(header_str);
        grid_cancellation.attachHeader(header_attach);
        grid_cancellation.setColumnIds(column_ids);
        grid_cancellation.setColTypes(column_types);
        grid_cancellation.setInitWidths(init_widths);
        grid_cancellation.setColAlign(col_align);
        grid_cancellation.setColSorting(col_sorting);
        grid_cancellation.enableAlterCss("", "");
        grid_cancellation.enableEditTabOnly(true);
        grid_cancellation.enableEditEvents(true, true, true);
        grid_cancellation.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
        grid_cancellation.attachEvent("onEditCell", onGridCancellationEdit);
        grid_cancellation.attachEvent("onKeyPress", onGridCancellationKeyPress);
        grid_cancellation.init();

        grid_cancellation.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_cancellation.setEditable(true);
        }

        loadCancellationGridCombos();

        return;
    }

    function createCheckInOutsGridStructure(nodeid)
    {
        var header_str = "Type*,Time Rule*,Check InOut Time*,Charge Type*,Charge Value*";
        var column_ids = "checkinout_policytype,checkinout_time_beforeafter,checkinout_checkinout_time,checkinout_charge_type,checkinout_charge_value";
        var column_types = "combo,combo,ed,combo,edn";
        var init_widths = "140,80,80,80,80";
        var col_align = "left,left,center,center,center";
        var col_sorting = "str,str,date,str,int";

        grid_checkinouts.clearAll();
        grid_checkinouts = null;
        grid_checkinouts = accord_policy.cells("checkinout").attachGrid();
        grid_checkinouts.setIconsPath('libraries/dhtmlx/imgs/');
        grid_checkinouts.setHeader(header_str);
        grid_checkinouts.setColumnIds(column_ids);
        grid_checkinouts.setColTypes(column_types);
        grid_checkinouts.setInitWidths(init_widths);
        grid_checkinouts.setColAlign(col_align);
        grid_checkinouts.setColSorting(col_sorting);
        grid_checkinouts.enableAlterCss("", "");
        grid_checkinouts.enableEditTabOnly(true);
        grid_checkinouts.enableEditEvents(true, true, true);
        grid_checkinouts.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
        grid_checkinouts.attachEvent("onEditCell", onGridCheckInOutEdit);
        grid_checkinouts.init();

        grid_checkinouts.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_checkinouts.setEditable(true);
        }

        loadCheckInOutGridCombos();

        return;
    }

    function createMealsGridStructure(nodeid)
    {
        var header_str = "MealPlan*,Is Main,Adults";
        var column_ids = "mealplanfk,is_main,adult_count";
        var column_types = "combo,ch,edn";
        var init_widths = "200,50," + (_agecolwidth + 30);
        var col_align = "left,center,center";
        var col_sorting = "str,int,int";

        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = item.agefrom;
                var ageto = item.ageto;

                header_str += ",Ch " + agefrom + "-" + ageto;
                column_ids += ",ch_" + agefrom + "_" + ageto;
                column_types += ",edn";
                init_widths += "," + (_agecolwidth + 30);
                col_align += ",center";
                col_sorting += ",int";
            }
        }



        grid_meals.clearAll();
        grid_meals = null;
        grid_meals = accord_meal.cells("meal").attachGrid();
        grid_meals.setIconsPath('libraries/dhtmlx/imgs/');
        grid_meals.setHeader(header_str);
        grid_meals.setColumnIds(column_ids);
        grid_meals.setColTypes(column_types);
        grid_meals.setInitWidths(init_widths);
        grid_meals.setColAlign(col_align);
        grid_meals.setColSorting(col_sorting);
        grid_meals.enableAlterCss("", "");
        grid_meals.enableEditTabOnly(true);
        grid_meals.enableEditEvents(true, true, true);
        grid_meals.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
        grid_meals.attachEvent("onEditCell", onGridMealEdit);
        grid_meals.attachEvent("onCheck", onGridMealCheck);
        grid_meals.init();

        grid_meals.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_meals.setEditable(true);
        }

        loadMealGridCombos();



        return;
    }

    function loadExtraGridCombos()
    {
        for (var i = 0; i < _dsGridMealCombo.dataCount(); i++)
        {
            var item = _dsGridMealCombo.item(_dsGridMealCombo.idByIndex(i));

            var cbo = grid_extras.getColumnCombo(grid_extras.getColIndexById("hb_mealplan_fk"));
            var cbo2 = grid_extras.getColumnCombo(grid_extras.getColIndexById("bb_mealplan_fk"));

            cbo.addOption([{value: item.value, text: item.text}]);
            cbo2.addOption([{value: item.value, text: item.text}]);

            cbo.readonly(true);
            cbo2.readonly(true);

        }
        return;
    }


    function loadCancellationGridCombos()
    {
        var cbo1 = grid_cancellation.getColumnCombo(grid_cancellation.getColIndexById("cancellation_canceltype"));
        cbo1.addOption([{value: "NS", text: "No Show"}]);
        cbo1.addOption([{value: "ED", text: "Early Departure"}]);
        cbo1.addOption([{value: "CC", text: "Cancellation"}]);
        cbo1.addOption([{value: "AA", text: "After Arrival Date"}]);
        cbo1.readonly(true);

        var cbo2 = grid_cancellation.getColumnCombo(grid_cancellation.getColIndexById("cancellation_charge_method"));
        cbo2.addOption([{value: "%D", text: "% Discount"}]);
        cbo2.addOption([{value: "%C", text: "% Charge"}]);
        cbo2.addOption([{value: "FLAT", text: "Flat"}]);
        cbo2.addOption([{value: "NIGHTS", text: "Nights"}]);
        cbo2.addOption([{value: "REMAINING NIGHTS", text: "Remaining Nights"}]);
        cbo2.readonly(true);
    }

    function loadCheckInOutGridCombos()
    {
        var cbo1 = grid_checkinouts.getColumnCombo(grid_checkinouts.getColIndexById("checkinout_policytype"));
        cbo1.addOption([{value: "ECI", text: "Early Check-In"}]);
        cbo1.addOption([{value: "LCO", text: "Late Check-Out"}]);
        cbo1.readonly(true);

        var cbo2 = grid_checkinouts.getColumnCombo(grid_checkinouts.getColIndexById("checkinout_time_beforeafter"));
        cbo2.addOption([{value: "BEFORE", text: "Before"}]);
        cbo2.addOption([{value: "AFTER", text: "After"}]);
        cbo2.readonly(true);

        var cbo3 = grid_checkinouts.getColumnCombo(grid_checkinouts.getColIndexById("checkinout_charge_type"));
        cbo3.addOption([{value: "%D", text: "% Disc"}]);
        cbo3.addOption([{value: "%C", text: "% Chrg"}]);
        cbo3.addOption([{value: "FLAT PNI", text: "Flat PNI"}]);
        cbo3.readonly(true);

        return;
    }

    function loadMealGridCombos()
    {

        var cbo1 = grid_meals.getColumnCombo(grid_meals.getColIndexById("mealplanfk"));

        for (var i = 0; i < _dsGridMealCombo.dataCount(); i++)
        {
            var item = _dsGridMealCombo.item(_dsGridMealCombo.idByIndex(i));
            cbo1.addOption([{value: item.value, text: item.text}]);
        }
        cbo1.readonly(true);
        return;
    }

    function populateExtraPlanGrid(roomid, date_rwid, dtfrom, dtto, variant)
    {

        grid_extras.clearAll();

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrextras = capacity_date_obj.date_mealextrasupplement_rules;


        for (var i = 0; i < arrextras.length; i++)
        {
            var extra_rwid = arrextras[i].extra_rwid;
            var extra_extra_name = arrextras[i].extra_extra_name;
            var extra_mandatory = arrextras[i].extra_mandatory;
            var extra_include_diner_rate_bb = arrextras[i].extra_include_diner_rate_bb;
            var extra_hb_mealplan_fk = arrextras[i].extra_hb_mealplan_fk;
            var extra_bb_mealplan_fk = arrextras[i].extra_bb_mealplan_fk;
            var extra_extra_date = arrextras[i].extra_extra_date;
            var extra_adult_count = arrextras[i].extra_adult_count;
            var extra_action = arrextras[i].extra_action;


            if (extra_action != "DELETE")
            {
                grid_extras.addRow(extra_rwid, "");
                grid_extras.setRowTextStyle(extra_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("extra_name")).setValue(extra_extra_name);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("mandatory")).setValue(extra_mandatory);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("include_diner_rate_bb")).setValue(extra_include_diner_rate_bb);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("hb_mealplan_fk")).setValue(extra_hb_mealplan_fk);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("bb_mealplan_fk")).setValue(extra_bb_mealplan_fk);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("extra_date")).setValue(extra_extra_date);
                grid_extras.cells(extra_rwid, grid_extras.getColIndexById("adult_count")).setValue(extra_adult_count);

                var arr_extra_children = arrextras[i].extra_children;
                for (var j = 0; j < arr_extra_children.length; j++)
                {
                    var child_rwid = arr_extra_children[j].child_rwid;
                    var child_agefrom = arr_extra_children[j].child_agefrom;
                    var child_ageto = arr_extra_children[j].child_ageto;
                    var child_count = arr_extra_children[j].child_count;

                    var child_colid = "ch_" + child_agefrom + "_" + child_ageto;
                    var colidx = grid_extras.getColIndexById(child_colid);

                    if (colidx)
                    {
                        grid_extras.cells(extra_rwid, colidx).setValue(child_count);
                    }
                }
            }
        }

        return;
    }



    function populateMealPlanGrid(roomid, date_rwid, dtfrom, dtto, variant)
    {
        grid_meals.clearAll();

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrmeals = capacity_date_obj.date_mealsupplement_rules;


        for (var i = 0; i < arrmeals.length; i++)
        {

            var meal_rwid = arrmeals[i].meal_rwid;
            var meal_mealplanfk = arrmeals[i].meal_mealplanfk;
            var meal_ismain = arrmeals[i].meal_ismain;
            var meal_adult_count = arrmeals[i].meal_adult_count;
            var meal_action = arrmeals[i].meal_action;

            if (meal_action != "DELETE")
            {
                grid_meals.addRow(meal_rwid, "");
                grid_meals.setRowTextStyle(meal_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                grid_meals.cells(meal_rwid, grid_meals.getColIndexById("mealplanfk")).setValue(meal_mealplanfk);
                grid_meals.cells(meal_rwid, grid_meals.getColIndexById("is_main")).setValue(meal_ismain);
                grid_meals.cells(meal_rwid, grid_meals.getColIndexById("adult_count")).setValue(meal_adult_count);

                var arr_meal_children = arrmeals[i].meal_children;
                for (var j = 0; j < arr_meal_children.length; j++)
                {
                    var child_rwid = arr_meal_children[j].child_rwid;
                    var child_agefrom = arr_meal_children[j].child_agefrom;
                    var child_ageto = arr_meal_children[j].child_ageto;
                    var child_count = arr_meal_children[j].child_count;

                    var child_colid = "ch_" + child_agefrom + "_" + child_ageto;
                    var colidx = grid_meals.getColIndexById(child_colid);

                    if (colidx)
                    {
                        grid_meals.cells(meal_rwid, colidx).setValue(child_count);
                    }
                }
            }
        }


        return;
    }

    function createCapacityGridStructure(variant, nodeid)
    {
        //get child ages selected

        var header_str = "";
        var header_attach = "";
        var column_ids = "";
        var column_types = "";
        var init_widths = "";
        var col_align = "";
        var col_sorting = "";


        if (variant == "UNITS")
        {
            header_str = "Standard Occupation (Ad + Ch),#cspan,Addtional Adults,#cspan";
            header_attach = "Min,Max,Min,Max";
            column_ids = "standardoccupation_Mi_0,standardoccupation_Mx_0,additionalpersons_Mi_0,additionalpersons_Mx_0";
            column_types = "edn,edn,edn,edn";
            init_widths = "70,70,70,70";
            col_align = "center,center,center,center";
            col_sorting = "na,na,na,na";


            var child_ages_ids = form_main.getItemValue("children_ages_ids");
            var arr_ids = child_ages_ids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {
                var id = arr_ids[i];
                if (id != "")
                {
                    var item = _dsChildPolicy.item(id);
                    var agefrom = item.agefrom;
                    var ageto = item.ageto;

                    header_str += ",Additional Ch <br>(" + agefrom + "-" + ageto + "),#cspan";
                    header_attach += ",Min,Max";
                    column_ids += ",Ch_Mi_" + agefrom + ",Ch_Mx_" + ageto;
                    column_types += ",edn,edn";
                    init_widths += "," + (_agecolwidth + 20) + "," + (_agecolwidth + 20);
                    col_align += ",center,center";
                    col_sorting += ",na,na";
                }
            }

        } else
        {
            //PERSONS

            header_str = "Adults,#cspan";
            header_attach = "Min,Max";
            column_ids = "Ad_Mi_0,Ad_Mx_0";
            column_types = "edn,edn";
            init_widths = _agecolwidth + "," + _agecolwidth;
            col_align = "center,center";
            col_sorting = "na,na";

            var child_ages_ids = form_main.getItemValue("children_ages_ids");
            var arr_ids = child_ages_ids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {
                var id = arr_ids[i];
                if (id != "")
                {
                    var item = _dsChildPolicy.item(id);
                    var agefrom = item.agefrom;
                    var ageto = item.ageto;

                    header_str += ",Ch " + agefrom + "-" + ageto + ",#cspan";
                    header_attach += ",Mi,Mx";
                    column_ids += ",Ch_Mi_" + agefrom + ",Ch_Mx_" + ageto;
                    column_types += ",edn,edn";
                    init_widths += "," + _agecolwidth + "," + _agecolwidth;
                    col_align += ",center,center";
                    col_sorting += ",na,na";
                }
            }

            //now generate mix ages
            //eg: got 0-1, 2-11, 12-17
            //generate 0-11, 0-17, 2-17

            for (var i = 0; i < arr_ids.length; i++)
            {
                var id_1 = arr_ids[i];
                var item_1 = _dsChildPolicy.item(id_1);
                if (item_1)
                {
                    var agefrom_1 = parseInt(item_1.agefrom, 10);

                    for (var j = 0; j < arr_ids.length; j++)
                    {
                        var id_2 = arr_ids[j];
                        var item_2 = _dsChildPolicy.item(id_2);

                        if (item_2)
                        {
                            var ageto_2 = parseInt(item_2.ageto, 10);

                            if (agefrom_1 < ageto_2 && id_1 != id_2)
                            {
                                header_str += ",Mix " + agefrom_1 + "-" + ageto_2 + ",#cspan";
                                header_attach += ",Mi,Mx";
                                column_ids += ",Ch_Mi_" + agefrom_1 + ",Ch_Mx_" + ageto_2;
                                column_types += ",edn,edn";
                                init_widths += "," + _agecolwidth + "," + _agecolwidth;
                                col_align += ",center,center";
                                col_sorting += ",na,na";
                            }
                        }
                    }
                }
            }
        }

        grid_capacity_age.clearAll();
        grid_capacity_age = null;
        grid_capacity_age = capacity_layout.cells("a").attachGrid();
        grid_capacity_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_capacity_age.setHeader(header_str);
        grid_capacity_age.setColumnIds(column_ids);
        grid_capacity_age.setColTypes(column_types);
        grid_capacity_age.setInitWidths(init_widths);
        grid_capacity_age.setColAlign(col_align);
        grid_capacity_age.setColSorting(col_sorting);
        grid_capacity_age.enableAlterCss("", "");
        grid_capacity_age.enableEditTabOnly(true);
        grid_capacity_age.enableEditEvents(true, true, true);
        grid_capacity_age.attachEvent("onEditCell", onGridCapacityAgeEdit);
        grid_capacity_age.attachHeader(header_attach);
        grid_capacity_age.init();

        grid_capacity_age.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_capacity_age.setEditable(true);
        }

        return;
    }


    function explode_ageranges(agefrom, ageto)
    {
        //explode the age range in the ranges defined in main
        //eg: main = 0-1, 2-11, 12-17 and here range is 0-17
        //return array 0-1, 2-11, 12-17

        var arr = [];

        agefrom = parseInt(agefrom, 10);
        ageto = parseInt(ageto, 10);

        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var _agefrom = parseInt(item.agefrom, 10);
                var _ageto = parseInt(item.ageto, 10);

                if (_agefrom >= agefrom && _ageto <= ageto)
                {
                    arr.push();
                    arr.push({age_from: _agefrom, age_to: _ageto});
                }
            }
        }


        return arr;
    }

    function is_age_in_mix(child_age_from, child_age_to)
    {
        //returns true if age is in mix ranges
        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");

        for (var i = 0; i < arr_ids.length; i++)
        {
            var id_1 = arr_ids[i];
            var item_1 = _dsChildPolicy.item(id_1);
            if (item_1)
            {
                var agefrom_1 = parseInt(item_1.agefrom, 10);

                for (var j = 0; j < arr_ids.length; j++)
                {
                    var id_2 = arr_ids[j];
                    var item_2 = _dsChildPolicy.item(id_2);

                    if (item_2)
                    {
                        var ageto_2 = parseInt(item_2.ageto, 10);

                        if (agefrom_1 < ageto_2 && id_1 != id_2)
                        {
                            if (agefrom_1 == child_age_from && ageto_2 == child_age_to)
                            {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    function is_age_in_main(agefrom, ageto)
    {
        //returns true if this age combination is selected in contract main

        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var _agefrom = item.agefrom;
                var _ageto = item.ageto;

                if (agefrom == _agefrom && ageto == _ageto)
                {
                    return true;
                }
            }
        }
        return false;
    }

    function loadGridCapacityAgeData(nodeid)
    {
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var nodeid_room = "ROOM_" + roomid;
        var variant = tree_roomdates.getUserData(nodeid_room, "ROOM_VARIANT");


        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    var date_action = arrdates[j].date_action;
                    var date_rwid = arrdates[j].date_rwid;

                    if (date_action != "DELETE" && daterwid == date_rwid)
                    {
                        var arrrules = arrdates[j].date_capacity_rules;
                        for (var k = 0; k < arrrules.length; k++)
                        {
                            var rule_action = arrrules[k].rule_action;
                            var rule_rwid = arrrules[k].rule_rwid;
                            var rule_rulecounter = arrrules[k].rule_rulecounter;

                            if (rule_action != "DELETE")
                            {
                                var arrcapactity = arrrules[k].rule_capacity;
                                grid_capacity_age.addRow(rule_rwid, "");
                                grid_capacity_age.setRowTextStyle(rule_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                                fillGridCapacityAgeAcross(rule_rwid, arrcapactity, variant);

                            }
                        }

                        return;
                    }
                }
            }
        }

        return;
    }


    function onGridTaxCommBuySelect(rid, cid)
    {
        var rwindex = grid_taxcomm_buy.getRowIndex(rid);
        var rwcount = grid_taxcomm_buy.getRowsNum();

        toolbar_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_taxcommi_buy.hideListOption("opts", "deleteitem");

        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        var rwobj = lookupTaxCommiRoomBuySellItem(roomid, "buying_settings", rid);

        if (!rwobj)
        {
            return;
        }


        if (rwobj.setting_core_addon == "ADDON")
        {
            toolbar_taxcommi_buy.showListOption("opts", "deleteitem");

            if (rwindex < rwcount - 1) //still room to move down
            {
                toolbar_taxcommi_buy.showListOption("opts", "movedown");
            }

            if (rwindex - 1 > 0) //an addon row cannot be at the top
            {
                toolbar_taxcommi_buy.showListOption("opts", "moveup");
            }
        } else if (rwobj.setting_core_addon == "CORE")
        {
            if (rwobj.setting_item_code != "CP")
            {
                //cannot delete Cost Price
                toolbar_taxcommi_buy.showListOption("opts", "deleteitem");
            }

            if (rwindex != 0) //cannot delete first core row
            {
                toolbar_taxcommi_buy.showListOption("opts", "moveup");
            }
            if (rwindex < rwcount - 1)
            {
                toolbar_taxcommi_buy.showListOption("opts", "movedown");
            }
        }
    }

    function onGridTaxCommSellSelect(rid, cid)
    {
        var rwindex = grid_taxcomm_sell.getRowIndex(rid);
        var rwcount = grid_taxcomm_sell.getRowsNum();

        toolbar_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_taxcommi_sell.hideListOption("opts", "deleteitem");

        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        var rwobj = lookupTaxCommiRoomBuySellItem(roomid, "selling_settings", rid);

        if (!rwobj)
        {
            return;
        }


        if (rwobj.setting_core_addon == "ADDON")
        {
            toolbar_taxcommi_sell.showListOption("opts", "deleteitem");

            if (rwindex < rwcount - 2) //still room to move down but not to last row
            {
                toolbar_taxcommi_sell.showListOption("opts", "movedown");
            }

            if (rwindex - 1 > 0) //an addon row cannot be at the top
            {
                toolbar_taxcommi_sell.showListOption("opts", "moveup");
            }
        } else if (rwobj.setting_core_addon == "CORE")
        {
            if (rwobj.setting_item_code != "CVSP" && rwobj.setting_item_code != "FINALSP")
            {
                toolbar_taxcommi_sell.showListOption("opts", "deleteitem");

                //cannot come to the first row
                if (rwindex - 1 > 0) //an addon row cannot be at the top
                {
                    toolbar_taxcommi_sell.showListOption("opts", "moveup");
                }

                //cannot come to the last row
                if (rwindex < rwcount - 2) //still room to move down but not to last row
                {
                    toolbar_taxcommi_sell.showListOption("opts", "movedown");
                }
            }
        }
    }

    function onTaxCommTreeNodeSelect(id)
    {
        grid_taxcomm_buy.clearAll();
        grid_taxcomm_sell.clearAll();

        toolbar_taxcommi_tree.hideListOption("opts", "new_exception");
        toolbar_taxcommi_tree.hideListOption("opts", "delete_exception");
        toolbar_taxcommi_tree.hideListOption("opts", "test_settings");
        toolbar_taxcommi_tree.hideListOption("opts", "settings_profile");


        toolbar_taxcommi_buy.hideListOption("opts", "additem");
        toolbar_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_taxcommi_buy.hideListOption("opts", "deleteitem");

        toolbar_taxcommi_sell.hideListOption("opts", "additem");
        toolbar_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_taxcommi_sell.hideListOption("opts", "deleteitem");


        var node = tree_taxcomm.getUserData(id, "ROOM_EXCEPTION");

        if (node == "GENERAL")
        {
            loadGridTaxCommXML(node,
                    {
                        selectrowid_buy: "",
                        selectrowid_sell: ""
                    });
            toolbar_taxcommi_buy.showListOption("opts", "additem");
            toolbar_taxcommi_sell.showListOption("opts", "additem");
            toolbar_taxcommi_tree.showListOption("opts", "test_settings");
            toolbar_taxcommi_tree.showListOption("opts", "settings_profile");


        } else if (node == "ROOM")
        {
            //show new exception button if node is childless
            if (tree_taxcomm.hasChildren(id) == 0)
            {
                toolbar_taxcommi_tree.showListOption("opts", "new_exception");
            }
        } else if (node == "EXCEPTION")
        {
            //there is an exception
            //show delete exception button
            toolbar_taxcommi_tree.showListOption("opts", "delete_exception");
            toolbar_taxcommi_tree.showListOption("opts", "test_settings");
            toolbar_taxcommi_tree.showListOption("opts", "settings_profile");

            var roomid = tree_taxcomm.getUserData(id, "ROOMID");
            loadGridTaxCommXML(roomid, {
                selectrowid_buy: "",
                selectrowid_sell: ""
            });

            toolbar_taxcommi_buy.showListOption("opts", "additem");
            toolbar_taxcommi_sell.showListOption("opts", "additem");
        }
    }


    function checkAgeRangeInArray(arr, agfrom, agto)
    {
        for (var x = 0; x < arr.length; x++)
        {
            if (arr[x].age_from == agfrom && arr[x].age_to == agto)
            {
                return true;
            }
        }
        return false;
    }

    function singleParentGetChildRanges(roomid, date_rwid)
    {
        //for each capacity rule, check if min_adult = 1 and max_adult = 1
        //      push child_ages applicable to rule + min_max child
        //next rule

        //if no rules found from min_adult = 1 and max_adult = 1 then
        //check for rules where min_adult = 1
        //      push child ages applicale to rule + min_max child
        //next rule


        var arr_result = [];
        var arr_main_childages = [];

        var dateobj = lookupCapacityRoomDateObj(roomid, date_rwid);
        var arrrulecounter = dateobj.date_capacity_rules;

        //===========================================================
        //CHECK 1

        for (var i = 0; i < dateobj.date_capacity_rules.length; i++)
        {
            var ruleobj = arrrulecounter[i];
            if (ruleobj.rule_action != "DELETE")
            {
                var arrrule_capacity = ruleobj.rule_capacity;
                for (var k = 0; k < arrrule_capacity.length; k++)
                {
                    var capacityobj = arrrule_capacity[k];
                    if (capacityobj.capacity_action != "DELETE")
                    {
                        if (capacityobj.capacity_category == "ADULT")
                        {
                            if (capacityobj.capacity_minpax == 1 && capacityobj.capacity_maxpax == 1)
                            {
                                //got a single parent obj
                                var xobj = pushCapacitySingleParentChilrenObj(ruleobj);
                                arr_result.push(xobj);
                            }
                        }
                    }
                }
            }
        }

        //===========================================================
        //CHECK 2
        if (arr_result.length == 0)
        {
            //check for records where capacity_minpax == 1 
            for (var i = 0; i < dateobj.date_capacity_rules.length; i++)
            {
                var ruleobj = arrrulecounter[i];
                if (ruleobj.rule_action != "DELETE")
                {
                    var arrrule_capacity = ruleobj.rule_capacity;
                    for (var k = 0; k < arrrule_capacity.length; k++)
                    {
                        var capacityobj = arrrule_capacity[k];
                        if (capacityobj.capacity_action != "DELETE")
                        {
                            if (capacityobj.capacity_category == "ADULT")
                            {
                                if (capacityobj.capacity_minpax == 1)
                                {
                                    //got a single parent obj
                                    var xobj = pushCapacitySingleParentChilrenObj(ruleobj);
                                    arr_result.push(xobj);
                                }
                            }
                        }
                    }
                }
            }
        }


        //===========================================================
        //===========================================================


        for (var i = 0; i < arr_result.length; i++)
        {
            var arr_ageranges = arr_result[i].children_ages;
            for (var j = 0; j < arr_ageranges.length; j++)
            {
                var capacity_child_agefrom = arr_ageranges[j]["capacity_child_agefrom"];
                var capacity_child_ageto = arr_ageranges[j]["capacity_child_ageto"];


                //combination must be based on contract.main
                //if not, then add it there

                if (is_age_in_main(capacity_child_agefrom, capacity_child_ageto))
                {
                    if (!checkAgeRangeInArray(arr_main_childages, capacity_child_agefrom, capacity_child_ageto))
                    {
                        arr_main_childages.push({age_from: capacity_child_agefrom, age_to: capacity_child_ageto});
                    }
                } else
                {
                    //explode the age range in the ranges defined in main
                    //eg: main = 0-1, 2-11, 12-17 and here range is 0-11
                    //return array 0-1, 2-11
                    var arr_explode = explode_ageranges(capacity_child_agefrom, capacity_child_ageto);
                    for (var x = 0; x < arr_explode.length; x++)
                    {
                        if (!checkAgeRangeInArray(arr_main_childages, arr_explode[x].age_from, arr_explode[x].age_to))
                        {
                            arr_main_childages.push({age_from: arr_explode[x].age_from,
                                age_to: arr_explode[x].age_to});
                        }
                    }
                }
            }
        }


        //===========================================================
        arr_main_childages.sort(function (a, b) {
            return parseFloat(a.age_from) - parseFloat(b.age_from);
        });
        //===========================================================

        return {RESULT: arr_result, MAIN_CHILD_AGES: arr_main_childages};
    }

    function loadGridSingleParentPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {

        //for each capacity rule, check if min_adult = 1 and max_adult = 1
        //      push child_ages applicable to rule + min_max child
        //next rule

        //if no rules found from min_adult = 1 and max_adult = 1 then
        //check for rules where min_adult = 1
        //      push child ages applicale to rule + min_max child
        //next rule



        var return_arr = singleParentGetChildRanges(roomid, date_rwid);
        var arr_result = return_arr.RESULT;
        var arr_main_childages = return_arr.MAIN_CHILD_AGES;



        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();


        grid_singleparentpolicy_age.clearAll(true);
        grid_singleparentpolicy_age = null;
        grid_singleparentpolicy_age = singleparent_layout.cells("a").attachGrid();
        grid_singleparentpolicy_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_singleparentpolicy_age.enableAlterCss("", "");
        grid_singleparentpolicy_age.enableEditTabOnly(true);
        grid_singleparentpolicy_age.enableEditEvents(true, true, true);
        grid_singleparentpolicy_age.attachEvent("onEditCell", onGridSingleParentPolicyEdit);
        grid_singleparentpolicy_age.enableColSpan(true);
        grid_singleparentpolicy_age.enableRowspan(true);


        var url = "php/api/hotelcontracts/grid_singleparentpolicy_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid +
                "&arr_main_childages=" + encodeURIComponent(JSON.stringify(arr_main_childages)) +
                "&arr_result=" + encodeURIComponent(JSON.stringify(arr_result)) +
                "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                "&costprice_currencyid=" + costprice_currencyid;



        singleparent_layout.cells("a").progressOn();
        grid_singleparentpolicy_age.loadXML(url, function () {
            singleparent_layout.cells("a").progressOff();

            //fill in values
            fillSingleParentPolicyGridValues(roomid, date_rwid);

            grid_singleparentpolicy_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_singleparentpolicy_age.setEditable(true);
            }
        });

    }

    function loadGridChildPolicySharing(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {

        var child_ages_ids = form_main.getItemValue("children_ages_ids");

        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();


        grid_childpolicy_sharing_age.clearAll(true);
        grid_childpolicy_sharing_age = null;
        grid_childpolicy_sharing_age = accord_child.cells("sharing").attachGrid();
        grid_childpolicy_sharing_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_childpolicy_sharing_age.enableAlterCss("", "");
        grid_childpolicy_sharing_age.enableEditTabOnly(true);
        grid_childpolicy_sharing_age.enableEditEvents(true, true, true);
        grid_childpolicy_sharing_age.attachEvent("onEditCell", onGridChildSharingPolicyEdit);
        grid_childpolicy_sharing_age.enableColSpan(true);


        var max_child_count = 0;
        var arr_childages_count = [];

        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = parseInt(item.agefrom, 10);
                var ageto = parseInt(item.ageto, 10);

                var child_stats = null;

                if (variant == "PERSONS")
                {
                    child_stats = getPersonsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto);
                } else if (variant == "UNITS")
                {
                    child_stats = getUnitsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto);
                }

                if (child_stats.sharing.max_child > 0)
                {
                    arr_childages_count.push(child_stats);

                    if (max_child_count < child_stats.sharing.max_child)
                    {
                        max_child_count = child_stats.sharing.max_child;
                    }
                }
            }
        }


        var file = "grid_childpolicy_xml";
        if (variant == "UNITS")
        {
            file = "grid_childpolicy_units_xml";
        }

        var url = "php/api/hotelcontracts/" + file + ".php?" +
                "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid +
                "&arr_childages_count=" + encodeURIComponent(JSON.stringify(arr_childages_count)) +
                "&max_child_count=" + max_child_count +
                "&child_mode=sharing" +
                "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                "&costprice_currencyid=" + costprice_currencyid;


        grid_childpolicy_sharing_age.loadXML(url, function () {
            //fill in values
            fillChildPolicyGridValues(roomid, date_rwid, grid_childpolicy_sharing_age, "SHARING");

            grid_childpolicy_sharing_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_childpolicy_sharing_age.setEditable(true);
            }
        });

    }

    function pushCapacitySingleParentChilrenObj(ruleobj)
    {
        var xobj = {children_ages: []};
        var arrrule_capacity = ruleobj.rule_capacity;
        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE" &&
                    capacityobj.capacity_category == "CHILD")
            {

                var capacity_maxpax = capacityobj.capacity_category;
                var capacity_minpax = capacityobj.capacity_minpax;

                if (capacity_maxpax == "" || isNaN(capacity_minpax))
                {
                    capacity_maxpax = 0;
                }
                if (capacity_minpax == "" || isNaN(capacity_minpax))
                {
                    capacity_minpax = 0;
                }

                capacity_maxpax = parseInt(capacity_maxpax, 10);
                capacity_minpax = parseInt(capacity_minpax, 10);

                if (capacity_maxpax != 0 && capacity_minpax != 0)
                {
                    xobj.children_ages.push(capacityobj);
                }


            }
        }

        return xobj;
    }




    function loadGridChildPolicySingle(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {
        var child_ages_ids = form_main.getItemValue("children_ages_ids");

        var max_child_count = 0;
        var arr_childages_count = [];
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = item.agefrom;
                var ageto = item.ageto;

                var child_stats = getPersonsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto);

                if (child_stats.single.max_child > 0)
                {
                    arr_childages_count.push(child_stats);

                    if (max_child_count < child_stats.single.max_child)
                    {
                        max_child_count = child_stats.single.max_child;
                    }
                }
            }
        }



        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();


        grid_childpolicy_single_age.clearAll(true);
        grid_childpolicy_single_age = null;
        grid_childpolicy_single_age = accord_child.cells("single").attachGrid();
        grid_childpolicy_single_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_childpolicy_single_age.enableAlterCss("", "");
        grid_childpolicy_single_age.enableEditTabOnly(true);
        grid_childpolicy_single_age.enableEditEvents(true, true, true);
        grid_childpolicy_single_age.attachEvent("onEditCell", onGridChildSinglePolicyEdit);
        grid_childpolicy_single_age.enableColSpan(true);



        var url = "php/api/hotelcontracts/grid_childpolicy_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid + "&arr_childages_count=" + encodeURIComponent(JSON.stringify(arr_childages_count)) +
                "&max_child_count=" + max_child_count +
                "&child_mode=single" +
                "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                "&costprice_currencyid=" + costprice_currencyid;


        grid_childpolicy_single_age.loadXML(url, function () {
            //fill in values
            fillChildPolicyGridValues(roomid, date_rwid, grid_childpolicy_single_age, "SINGLE");

            grid_childpolicy_single_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_childpolicy_single_age.setEditable(true);
            }

        });

    }


    function loadGridCheckInOut(roomid, date_rwid, dtfrom, dtto, variant)
    {
        grid_checkinouts.clearAll();

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrcheckinout = capacity_date_obj.date_policies_checkinout;

        for (var i = 0; i < arrcheckinout.length; i++)
        {
            var checkinout_rwid = arrcheckinout[i].checkinout_rwid;
            var checkinout_policytype = arrcheckinout[i].checkinout_policytype;
            var checkinout_time_beforeafter = arrcheckinout[i].checkinout_time_beforeafter;
            var checkinout_checkinout_time = arrcheckinout[i].checkinout_checkinout_time;
            var checkinout_charge_type = arrcheckinout[i].checkinout_charge_type;
            var checkinout_charge_value = arrcheckinout[i].checkinout_charge_value;
            var checkinout_action = arrcheckinout[i].checkinout_action;

            if (checkinout_action != "DELETE")
            {
                grid_checkinouts.addRow(checkinout_rwid, [checkinout_policytype, checkinout_time_beforeafter, checkinout_checkinout_time, checkinout_charge_type, checkinout_charge_value]);
                grid_checkinouts.setRowTextStyle(checkinout_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }
    }

    function loadGridCancellation(roomid, date_rwid, dtfrom, dtto, variant)
    {
        grid_cancellation.clearAll();

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrcancellation = capacity_date_obj.date_policies_cancellation;

        for (var i = 0; i < arrcancellation.length; i++)
        {

            var cancellation_rwid = arrcancellation[i].cancellation_rwid;
            var cancellation_canceltype = arrcancellation[i].cancellation_canceltype;
            var cancellation_charge_method = arrcancellation[i].cancellation_charge_method;
            var cancellation_charge_value = arrcancellation[i].cancellation_charge_value;
            var cancellation_days_before_arrival_from = arrcancellation[i].cancellation_days_before_arrival_from;
            var cancellation_days_before_arrival_to = arrcancellation[i].cancellation_days_before_arrival_to;
            var cancellation_dates_before_arrival_from = arrcancellation[i].cancellation_dates_before_arrival_from;
            var cancellation_dates_before_arrival_to = arrcancellation[i].cancellation_dates_before_arrival_to;
            var cancellation_action = arrcancellation[i].cancellation_action;

            if (cancellation_action != "DELETE")
            {
                grid_cancellation.addRow(cancellation_rwid, [cancellation_canceltype,
                    cancellation_charge_method, cancellation_charge_value,
                    cancellation_days_before_arrival_from,
                    cancellation_days_before_arrival_to,
                    cancellation_dates_before_arrival_from,
                    cancellation_dates_before_arrival_to]);
                grid_cancellation.setRowTextStyle(cancellation_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }
    }


    function updateJsonCapacityMinStay(attribute, roomid, date_rwid, rId, nValue)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrminstay = capacity_date_obj.date_minstay_rules;

        for (var i = 0; i < arrminstay.length; i++)
        {
            if (rId == arrminstay[i].minstay_rwid)
            {
                arrminstay[i][attribute] = nValue;
                return;
            }
        }
    }


    function loadGridMinStay(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {
        grid_minstay.clearAll();

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrminstay = capacity_date_obj.date_minstay_rules;

        for (var i = 0; i < arrminstay.length; i++)
        {
            var minstay_rwid = arrminstay[i].minstay_rwid;
            var minstay_description = arrminstay[i].minstay_description;
            var minstay_duration = arrminstay[i].minstay_duration;
            var minstay_action = arrminstay[i].minstay_action;

            if (minstay_action != "DELETE")
            {
                grid_minstay.addRow(minstay_rwid, [minstay_duration, minstay_description]);
                grid_minstay.setRowTextStyle(minstay_rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }

        grid_minstay.setEditable(false);
        if (tree_roomdates.isItemChecked(nodeid))
        {
            grid_minstay.setEditable(true);
        }

    }

    function loadGridAdultPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {



        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();

        //if variant == persons then show category and basis, value depending on  room
        //add rows for max adult count for that room

        //if variant == units then show Unit,Additional Adult


        grid_adultpolicy_age.clearAll(true);
        grid_adultpolicy_age = null;
        grid_adultpolicy_age = adult_layout.cells("a").attachGrid();
        grid_adultpolicy_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_adultpolicy_age.enableAlterCss("", "");
        grid_adultpolicy_age.enableEditTabOnly(true);
        grid_adultpolicy_age.enableEditEvents(true, true, true);
        grid_adultpolicy_age.attachEvent("onEditCell", onGridAdultPolicyEdit);
        grid_adultpolicy_age.enableColSpan(true);
        grid_adultpolicy_age.enableRowspan(true);

        adult_layout.cells("a").progressOn();

        var url = "";

        if (variant == "PERSONS")
        {
            var adult_max = getCapacityRoomMaxAdult(roomid, dtfrom, dtto, "ADULT");

            url = "php/api/hotelcontracts/grid_adultpolicy_persons_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&roomid=" + roomid +
                    "&adult_max=" + adult_max +
                    "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                    "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                    "&costprice_currencyid=" + costprice_currencyid;

        } else
        {
            var additional_adult_max = getCapacityRoomMaxAdult(roomid, dtfrom, dtto, "ADDITIONALPERSONS");
            //units
            url = "php/api/hotelcontracts/grid_adultpolicy_units_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&roomid=" + roomid +
                    "&child_ages_ids=" + child_ages_ids +
                    "&additional_adult_max=" + additional_adult_max +
                    "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                    "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                    "&costprice_currencyid=" + costprice_currencyid;
        }

        grid_adultpolicy_age.loadXML(url, function () {
            adult_layout.cells("a").progressOff();

            //fill in values
            fillAdultPolicyGridValues(roomid, date_rwid);

            grid_adultpolicy_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_adultpolicy_age.setEditable(true);
            }

        });
    }


    function getUnitsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto)
    {
        var statsobj = {
            age_from: agefrom,
            age_to: ageto,
            sharing: {
                min_child: 10000, max_child: 0
            }};

        var capacity_room_obj = lookupRoomObj(roomid);

        if (capacity_room_obj)
        {
            var arrcapacitydates = capacity_room_obj.room_dates;
            for (var i = 0; i < arrcapacitydates.length; i++)
            {
                if (arrcapacitydates[i].date_dtfrom == dtfrom &&
                        arrcapacitydates[i].date_dtto == dtto &&
                        arrcapacitydates[i].date_action != "DELETE")
                {
                    var arrrulecounter = arrcapacitydates[i].date_capacity_rules;
                    for (var j = 0; j < arrrulecounter.length; j++)
                    {
                        if (arrrulecounter[j].rule_action != "DELETE")
                        {
                            //for each rule, get the adult stats and children stats
                            var rule_stats = getCapacityRuleStats(arrrulecounter[j], agefrom, ageto, "ADULT", "CH");

                            //child sharing room
                            if (statsobj.sharing.min_child > rule_stats.min_child)
                            {
                                statsobj.sharing.min_child = rule_stats.min_child;
                            }
                            if (statsobj.sharing.max_child < rule_stats.max_child)
                            {
                                statsobj.sharing.max_child = rule_stats.max_child;
                            }

                        }
                    }
                }
            }
        }

        if (statsobj.sharing.min_child == 10000)
        {
            statsobj.sharing.min_child = 0;
        }

        return statsobj;
    }

    function getPersonsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto)
    {
        var statsobj = {
            age_from: agefrom,
            age_to: ageto,
            single: {
                min_child: 10000, max_child: 0
            },
            sharing: {
                min_child: 10000, max_child: 0
            }};

        var capacity_room_obj = lookupRoomObj(roomid);

        if (capacity_room_obj)
        {
            var arrcapacitydates = capacity_room_obj.room_dates;
            for (var i = 0; i < arrcapacitydates.length; i++)
            {
                if (arrcapacitydates[i].date_dtfrom == dtfrom &&
                        arrcapacitydates[i].date_dtto == dtto &&
                        arrcapacitydates[i].date_action != "DELETE")
                {
                    var arrrulecounter = arrcapacitydates[i].date_capacity_rules;
                    for (var j = 0; j < arrrulecounter.length; j++)
                    {
                        if (arrrulecounter[j].rule_action != "DELETE")
                        {
                            //for each rule, get the adult stats and children stats
                            var rule_stats = getCapacityRuleStats(arrrulecounter[j], agefrom, ageto, "ADULT", "CHILD");


                            if (rule_stats.max_adult == 0 && rule_stats.min_adult == 0)
                            {
                                //child single room
                                if (statsobj.single.min_child > rule_stats.min_child)
                                {
                                    statsobj.single.min_child = rule_stats.min_child;
                                }
                                if (statsobj.single.max_child < rule_stats.max_child)
                                {
                                    statsobj.single.max_child = rule_stats.max_child;
                                }
                            } else if (rule_stats.max_adult > 1 && rule_stats.min_adult >= 1)
                            {

                                //child sharing room
                                if (statsobj.sharing.min_child > rule_stats.min_child)
                                {
                                    statsobj.sharing.min_child = rule_stats.min_child;
                                }
                                if (statsobj.sharing.max_child < rule_stats.max_child)
                                {
                                    statsobj.sharing.max_child = rule_stats.max_child;
                                }
                            }
                        }
                    }
                }
            }
        }


        if (statsobj.single.min_child == 10000)
        {
            statsobj.single.min_child = 0;
        }
        if (statsobj.sharing.min_child == 10000)
        {
            statsobj.sharing.min_child = 0;
        }

        return statsobj;
    }

    function getCapacityRuleStats(ruleobj, agefrom, ageto, adult_category, child_category)
    {
        var max_adult = "";
        var min_adult = "";

        var max_child = 0;
        var min_child = 1000;

        var arrrule_capacity = ruleobj.rule_capacity;
        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            if (arrrule_capacity[k].capacity_action != "DELETE")
            {
                if (arrrule_capacity[k].capacity_category == adult_category)
                {
                    min_adult = parseInt(arrrule_capacity[k].capacity_minpax, 10);
                    max_adult = parseInt(arrrule_capacity[k].capacity_maxpax, 10);

                } else if (arrrule_capacity[k].capacity_category == child_category)

                {

                    if (parseInt(arrrule_capacity[k].capacity_child_agefrom, 10) <= agefrom &&
                            parseInt(arrrule_capacity[k].capacity_child_ageto, 10) >= ageto)
                    {
                        var _min_child = utils_parseInt(arrrule_capacity[k].capacity_minpax, 10);
                        var _max_child = utils_parseInt(arrrule_capacity[k].capacity_maxpax, 10);


                        if (_min_child != 0 || _max_child != 0)
                        {
                            if (_min_child < min_child)
                            {
                                min_child = _min_child;
                            }

                            if (_max_child > max_child)
                            {
                                max_child = _max_child;
                            }
                        }
                    }


                }
            }
        }

        return {max_adult: max_adult, min_adult: min_adult, max_child: max_child, min_child: min_child};
    }


    function getCapacityRoomMaxAdult(roomid, dtfrom, dtto, category)
    {
        var capacity_room_obj = lookupRoomObj(roomid);
        var max_adult_count = 0;

        if (capacity_room_obj)
        {
            var arrcapacitydates = capacity_room_obj.room_dates;
            for (var i = 0; i < arrcapacitydates.length; i++)
            {
                if (arrcapacitydates[i].date_dtfrom == dtfrom &&
                        arrcapacitydates[i].date_dtto == dtto &&
                        arrcapacitydates[i].date_action != "DELETE")
                {
                    var arrrulecounter = arrcapacitydates[i].date_capacity_rules;
                    for (var j = 0; j < arrrulecounter.length; j++)
                    {
                        if (arrrulecounter[j].rule_action != "DELETE")
                        {
                            var arrrule_capacity = arrrulecounter[j].rule_capacity;
                            for (var k = 0; k < arrrule_capacity.length; k++)
                            {
                                if (arrrule_capacity[k].capacity_action != "DELETE")
                                {
                                    var capacity_category = arrrule_capacity[k].capacity_category;
                                    var capacity_minpax = arrrule_capacity[k].capacity_minpax;
                                    var capacity_maxpax = arrrule_capacity[k].capacity_maxpax;

                                    if (capacity_category == category)
                                    {
                                        if (max_adult_count < capacity_minpax)
                                        {
                                            max_adult_count = capacity_minpax;
                                        }
                                        if (max_adult_count < capacity_maxpax)
                                        {
                                            max_adult_count = capacity_maxpax;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return max_adult_count;
    }




    function loadGridTaxCommXML(roomid, obj_select)
    {

        toolbar_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_taxcommi_buy.hideListOption("opts", "deleteitem");

        toolbar_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_taxcommi_sell.hideListOption("opts", "deleteitem");


        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");


        //get current taxcomm data from json object

        var arr_buying_settings = [];
        var arr_selling_settings = [];

        var obj = lookupTaxCommiRoomObj(roomid);
        if (obj)
        {
            arr_buying_settings = utils_deepCopy(obj.buying_settings);
            arr_selling_settings = utils_deepCopy(obj.selling_settings);
        }


        grid_taxcomm_buy.clearAll(true);
        grid_taxcomm_buy = null;
        grid_taxcomm_buy = accord_taxcommi.cells("buying").attachGrid();
        grid_taxcomm_buy.setIconsPath('libraries/dhtmlx/imgs/');
        grid_taxcomm_buy.enableAlterCss("", "");
        grid_taxcomm_buy.enableEditTabOnly(true);
        grid_taxcomm_buy.enableEditEvents(true, true, true);
        grid_taxcomm_buy.attachEvent("onRowSelect", onGridTaxCommBuySelect);
        grid_taxcomm_buy.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
            return onGridTaxCommBuySellEdit("BUYING", stage, rId, cInd, nValue, oValue);
        });


        grid_taxcomm_sell.clearAll(true);
        grid_taxcomm_sell = null;
        grid_taxcomm_sell = accord_taxcommi.cells("selling").attachGrid();
        grid_taxcomm_sell.setIconsPath('libraries/dhtmlx/imgs/');
        grid_taxcomm_sell.enableAlterCss("", "");
        grid_taxcomm_sell.enableEditTabOnly(true);
        grid_taxcomm_sell.enableEditEvents(true, true, true);
        grid_taxcomm_sell.attachEvent("onRowSelect", onGridTaxCommSellSelect);
        grid_taxcomm_sell.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
            return onGridTaxCommBuySellEdit("SELLING", stage, rId, cInd, nValue, oValue);
        });

        //=============
        var selectrowid_buy = "";
        var selectrowid_sell = "";



        if (obj_select)
        {
            selectrowid_buy = obj_select.selectrowid_buy;
            selectrowid_sell = obj_select.selectrowid_sell;
        }
        //=============
        var url = "php/api/hotelcontracts/grid_taxcomm_buy_xml.php?t=" +
                encodeURIComponent(global_token) +
                "&arr_settings_data=" + encodeURIComponent(JSON.stringify(arr_buying_settings)) +
                "&selected_currency_buy_ids=" + selected_currency_buy_ids;
        grid_taxcomm_buy.loadXML(url, function () {


            if (grid_taxcomm_buy.getRowsNum() > 0)
            {
                if (selectrowid_buy == "")
                {
                    //select first record
                    grid_taxcomm_buy.selectRowById(grid_taxcomm_buy.getRowId(0), false, true, true);
                } else
                {
                    //select specific record
                    grid_taxcomm_buy.selectRowById(selectrowid_buy, false, true, true);
                }
            }
        });


        //=============

        var url = "php/api/hotelcontracts/grid_taxcomm_sell_xml.php?t=" +
                encodeURIComponent(global_token) +
                "&arr_settings_data=" + encodeURIComponent(JSON.stringify(arr_selling_settings)) +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids;
        grid_taxcomm_sell.loadXML(url, function () {


            if (grid_taxcomm_sell.getRowsNum() > 0)
            {
                if (selectrowid_sell == "")
                {
                    //select first record
                    grid_taxcomm_sell.selectRowById(grid_taxcomm_sell.getRowId(0), false, true, true);
                } else
                {
                    grid_taxcomm_sell.selectRowById(selectrowid_sell, false, true, true);
                }
            }
        });
    }


    function onRoomTreeNodeChecked(id, state)
    {
        var selectednodeid = tree_roomdates.getSelectedItemId();
        if (selectednodeid)
        {
            onRoomTreeNodeSelect(selectednodeid);
        }

        //var node = tree_roomdates.getUserData(id, "ROOM_SEASON_DATE");


        //if tab is adult, child or single parent
        //check if the capacity structure of checked date nodes are still similar
        //if no, uncheck the newly checked item

        var tabid = tabRoomViews.getActiveTab();

        //======================================================================
        if ((tabid == "adult_policies" || tabid == "child_policies" || tabid == "single_parent"))
        {
            if (state)
            {
                if (!testSimilarCapacityStructure())
                {

                    dhtmlx.alert({
                        text: "Checked Date(s) <b>do not share the same</b> Capacity Definitions as the other checked dates. Its checkbox will be cleared..",
                        type: "alert-warning",
                        title: "Different Capacity Definitions",
                        callback: function () {
                            tree_roomdates.setCheck(id, false);
                        }
                    });

                    return;
                }
            }

            //======================================================================
            if (!state)
            {
                //if there are no items checked at all, then enable all
                var checkedids = utils_trim(tree_roomdates.getAllChecked(), " ");
                if (checkedids == "")
                {
                    var ids = tree_roomdates.getAllSubItems("0");
                    var arrids = ids.split(",");

                    for (var i = 0; i < arrids.length; i++)
                    {
                        var nodeid = utils_trim(arrids[i], " ");
                        if (nodeid != "")
                        {
                            tree_roomdates.disableCheckbox(nodeid, false);
                        }
                    }
                }
                return;
            }


            //======================================================================
            //run a check across the tree to disable all nodes that are not compatible 
            //to the one checked and enable all the ones that are compatible
            if (state)
            {
                var from_daterwid = tree_roomdates.getUserData(id, "DATE_RWID");
                var from_roomid = tree_roomdates.getUserData(id, "DATE_ROOMID");

                var ids = tree_roomdates.getAllSubItems("0");
                var arrids = ids.split(",");

                for (var i = 0; i < arrids.length; i++)
                {
                    var nodeid = utils_trim(arrids[i], " ");
                    if (nodeid != "" && nodeid != id)
                    {
                        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
                        if (node == "DATE")
                        {
                            var to_daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                            var to_roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

                            var tf1 = compareCapacityDatesObject(from_roomid, from_daterwid, to_roomid, to_daterwid);
                            var tf2 = compareCapacityDatesObject(to_roomid, to_daterwid, from_roomid, from_daterwid);

                            var flgtf = tf1 && tf2;
                            tree_roomdates.disableCheckbox(nodeid, !flgtf);
                        }
                    }
                }
            }
        }

        return;
    }


    function onRoomTreeNodeDblClick(id)
    {
        var node = tree_roomdates.getUserData(id, "ROOM_SEASON_DATE");
        toggleSelectedAllRooms(false);
        enableAllRoomCheckboxes();

        if (node == "ROOM")
        {
            return;
        } else if (node == "DATE")
        {
            var roomid = tree_roomdates.getUserData(id, "DATE_ROOMID");
            var date_rwid = tree_roomdates.getUserData(id, "DATE_RWID");
            var itemtext = tree_roomdates.getItemText(id);
            var arr_text = itemtext.split(":");
            var season = "";

            if (arr_text.length == 2)
            {
                season = utils_trim(arr_text[1], " ");
            }

            var ids = tree_roomdates.getAllSubItems("0");
            var arrids = ids.split(",");

            for (var i = 0; i < arrids.length; i++)
            {
                var nodeid = utils_trim(arrids[i], " ");
                if (nodeid != "")
                {
                    var _itemtext = tree_roomdates.getItemText(nodeid);
                    var _roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                    var _date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                    var _arr_text = _itemtext.split(":");

                    if (_roomid == roomid)
                    {
                        var _season = "";
                        if (_arr_text.length == 2)
                        {
                            _season = utils_trim(_arr_text[1], " ");


                            var tabid = tabRoomViews.getActiveTab();
                            if (tabid == "adult_policies" || tabid == "child_policies" || tabid == "single_parent")
                            {
                                var ckh = compareCapacityDatesObject(roomid, date_rwid, _roomid, _date_rwid);
                                if (_season == season && ckh)
                                {
                                    tree_roomdates.setCheck(nodeid, true);
                                } else
                                {
                                    tree_roomdates.disableCheckbox(nodeid, true);
                                }
                            } else if (tabid == "minstay" || tabid == "capacity" || tabid == "policies" || tabid == "meal")
                            {
                                if (_season == season)
                                {
                                    tree_roomdates.setCheck(nodeid, true);
                                }
                            }
                        }
                    } else
                    {
                        tree_roomdates.disableCheckbox(nodeid, true);
                    }
                }
            }
        }
    }

    function onRoomTreeNodeSelect(id)
    {
        var tabid = grid_room_choices.getSelectedRowId();
        toggleRoomViews(id, tabid);
    }

    function toggleRoomViews(nodeid, tabid)
    {
        toggleError("rooms", tabid);

        comboVariant.show(false);

        toolbar_capacity_dates.hideListOption("opts", "new");
        toolbar_capacity_dates.hideListOption("opts", "modify");
        toolbar_capacity_dates.hideListOption("opts", "delete");
        toolbar_capacity_dates.hideListOption("opts", "select_all_rooms");
        toolbar_capacity_dates.hideListOption("opts", "unselect_all_rooms");
        toolbar_capacity_dates.hideListOption("opts", "select_specific_period");
        toolbar_capacity_dates.hideListOption("opts", "unselect_specific_period");
        toolbar_capacity_dates.hideListOption("opts", "combi");



        toolbar_minstay.hideItem("new");
        toolbar_minstay.hideItem("delete");
        grid_minstay.clearAll();


        toolbar_capacity_dates.hideItem("new");
        toolbar_capacity_dates.hideItem("modify");
        toolbar_capacity_dates.hideItem("delete");
        toolbar_capacity_rules.hideItem("new");


        toolbar_capacity_rules.hideItem("delete");
        grid_capacity_age.clearAll();

        toolbar_checkinout.hideItem("new");
        toolbar_checkinout.hideItem("delete");
        grid_checkinouts.clearAll();

        toolbar_cancellation.hideItem("new");
        toolbar_cancellation.hideItem("delete");
        grid_cancellation.clearAll();

        toolbar_meals.hideItem("new");
        toolbar_meals.hideItem("delete");
        grid_meals.clearAll();

        toolbar_meals_extra.hideItem("new");
        toolbar_meals_extra.hideItem("delete");
        grid_extras.clearAll();

        grid_adultpolicy_age.clearAll();
        grid_childpolicy_sharing_age.clearAll();
        grid_childpolicy_single_age.clearAll();
        grid_singleparentpolicy_age.clearAll();



        var node = "";
        if (nodeid)
        {
            node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        } else
        {
            return;
        }



        //===============================

        showHideRoomTabs("");

        if (node == "ROOM")
        {
            comboVariant.show(true);
            var room_variant = tree_roomdates.getUserData(nodeid, "ROOM_VARIANT");
            comboVariant.setComboValue(room_variant);

            toolbar_capacity_dates.showListOption("opts", "new");



            //show hide tabs
            showHideRoomTabs("");

        } else if (node == "SEASON")
        {
            //show hide tabs
            showHideRoomTabs("");


        } else if (node == "DATE")
        {
            showHideRoomTabs(tabid);
            toolbar_capacity_dates.showListOption("opts", "modify");
            toolbar_capacity_dates.showListOption("opts", "delete");
            comboVariant.show(false);


        }


        //===========TABWISE SPECIFIC ==============
        //==========================================
        if (tabid == "minstay") {

            if (node == "ROOM")
            {

                toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
                toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");

            } else if (node == "DATE")
            {
                if (tree_roomdates.isItemChecked(nodeid))
                {
                    toolbar_minstay.showItem("new");
                    toolbar_minstay.showItem("delete");
                    toolbar_capacity_dates.showListOption("opts", "select_specific_period");
                    toolbar_capacity_dates.showListOption("opts", "unselect_specific_period");
                }

                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");

                loadGridMinStay(roomid, date_rwid, dtfrom, dtto, variant, nodeid);

            }
        }
        //==========================================
        else if (tabid == "capacity") {

            initialiseCapacityView();

            if (node == "ROOM")
            {
                toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
                toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");
                toolbar_capacity_dates.showListOption("opts", "combi");

                toolbar_capacity_dates.showItem("new");
                toolbar_capacity_dates.hideItem("modify");
                toolbar_capacity_dates.hideItem("delete");
                toolbar_capacity_rules.hideItem("new");

                toolbar_capacity_rules.hideItem("delete");

            } else if (node == "DATE")
            {
                toolbar_capacity_dates.hideItem("new");
                toolbar_capacity_dates.showItem("modify");
                toolbar_capacity_dates.showItem("delete");

                if (tree_roomdates.isItemChecked(nodeid))
                {
                    toolbar_capacity_rules.showItem("new");
                    toolbar_capacity_rules.showItem("delete");
                }

                toolbar_capacity_dates.showListOption("opts", "combi");
                toolbar_capacity_dates.showListOption("opts", "select_specific_period");
                toolbar_capacity_dates.showListOption("opts", "unselect_specific_period");

                var room_id = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var room_variant = tree_roomdates.getUserData("ROOM_" + room_id, "ROOM_VARIANT");

                //load grid for dates
                createCapacityGridStructure(room_variant, nodeid);
                loadGridCapacityAgeData(nodeid);
            }

        }
        //==========================================
        else if (tabid == "policies")
        {

            createCheckInOutsGridStructure(nodeid);
            createCancellationGridStructure(nodeid);

            if (node == "ROOM")
            {
                toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
                toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");
            } else if (node == "DATE")
            {
                toolbar_capacity_dates.showListOption("opts", "select_specific_period");
                toolbar_capacity_dates.showListOption("opts", "unselect_specific_period");

                if (tree_roomdates.isItemChecked(nodeid))
                {
                    toolbar_checkinout.showItem("new");
                    toolbar_checkinout.showItem("delete");

                    toolbar_cancellation.showItem("new");
                    toolbar_cancellation.showItem("delete");
                }


                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");


                loadGridCheckInOut(roomid, date_rwid, dtfrom, dtto, variant);
                loadGridCancellation(roomid, date_rwid, dtfrom, dtto, variant);
            }

        }
        //==========================================
        else if (tabid == "meal")
        {

            createMealsGridStructure(nodeid);
            createExtrasGridStructure(nodeid);

            if (node == "ROOM")
            {
                toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
                toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");
            } else if (node == "DATE")
            {
                if (tree_roomdates.isItemChecked(nodeid))
                {
                    toolbar_meals.showItem("new");
                    toolbar_meals.showItem("delete");

                    toolbar_meals_extra.showItem("new");
                    toolbar_meals_extra.showItem("delete");
                }


                toolbar_capacity_dates.showListOption("opts", "select_specific_period");
                toolbar_capacity_dates.showListOption("opts", "unselect_specific_period");

                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");


                populateMealPlanGrid(roomid, date_rwid, dtfrom, dtto, variant);
                populateExtraPlanGrid(roomid, date_rwid, dtfrom, dtto, variant);
            }

        }
        //==========================================
        else if (tabid == "adult_policies") {

            if (!testSimilarCapacityStructure())
            {

                dhtmlx.alert({
                    text: "The Checked Dates <b>do not share the same</b> Capacity Definitions. Checkboxes will be cleared..",
                    type: "alert-warning",
                    title: "Different Capacity Definitions",
                    callback: function () {
                        toggleSelectedAllRooms(false);
                    }
                });
            }

            initialiseAdultPolicyGrid();

            if (node == "DATE")
            {
                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");

                loadGridAdultPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid);
            }

        }
        //==========================================
        else if (tabid == "child_policies") {

            if (!testSimilarCapacityStructure())
            {

                dhtmlx.alert({
                    text: "The Checked Dates <b>do not share the same</b> Capacity Definitions. Checkboxes will be cleared..",
                    type: "alert-warning",
                    title: "Different Capacity Definitions",
                    callback: function () {
                        toggleSelectedAllRooms(false);
                    }
                });
            }

            initialiseChildPolicySharingGrid();
            initialiseChildPolicySingleGrid();


            if (node == "DATE")
            {
                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");

                //load child sharing for both PERSONS and UNITS rooms
                loadGridChildPolicySharing(roomid, date_rwid, dtfrom, dtto, variant, nodeid);

                if (variant == "PERSONS")
                {
                    //load single room only for PERSONS rooms
                    loadGridChildPolicySingle(roomid, date_rwid, dtfrom, dtto, variant, nodeid);
                }

            }

        }
        //==========================================
        else if (tabid == "single_parent") {


            if (!testSimilarCapacityStructure())
            {
                dhtmlx.alert({
                    text: "The Checked Dates <b>do not share the same</b> Capacity Definitions. Checkboxes will be cleared..",
                    type: "alert-warning",
                    title: "Different Capacity Definitions",
                    callback: function () {
                        toggleSelectedAllRooms(false);
                    }
                });
            }

            initialiseSingleParentPolicyGrid();


            if (node == "DATE")
            {
                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
                var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");
                var variant = tree_roomdates.getUserData("ROOM_" + roomid, "ROOM_VARIANT");

                if (variant == "PERSONS")
                {
                    loadGridSingleParentPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid);
                }
            }
        }
        //==========================================
    }



    function initialiseCapacityView()
    {
        toolbar_capacity_rules.hideItem("new");
        toolbar_capacity_rules.hideItem("delete");


        grid_capacity_age.clearAll();
        grid_capacity_age = null;
        grid_capacity_age = capacity_layout.cells("a").attachGrid();
        grid_capacity_age.setIconsPath('libraries/dhtmlx/imgs/');

        return;
    }

    function loadHotelContractDatePeriods()
    {
        _dsDatePeriods.clearAll();
        _dsDatePeriods = null;
        _dsDatePeriods = new dhtmlXDataStore();

        var contract_activefrom = form_main.getItemValue("active_from", true);
        var contract_activeto = form_main.getItemValue("active_to", true);

        //reformat from dd-mm-yyyy to yyyy-mm-dd
        contract_activefrom = utils_date_to_str(utils_createDateObjFromString(contract_activefrom, "dd-mm-yyyy"));
        contract_activeto = utils_date_to_str(utils_createDateObjFromString(contract_activeto, "dd-mm-yyyy"));

        contract_activefrom = encodeURIComponent(contract_activefrom);
        contract_activeto = encodeURIComponent(contract_activeto);


        var url = "php/api/hotelcontracts/loadhoteldateperiods.php?t=" + global_token + "&hid=" + global_hotel_id + "&contract_activefrom=" + contract_activefrom + "&contract_activeto=" + contract_activeto;

        roomslayout.cells("a").progressOn();
        _dsDatePeriods.load(url, "json", function () {
            roomslayout.cells("a").progressOff();
            populateRoomsTree();
            populateCboRoomsFilter();

            //===========================================
            //==== trigger validation of date gaps and all date related validations

            grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
            validateContract(["rooms_dates", "rooms_minstay", "rooms_capacity", "rooms_policies", "rooms_meal"]);
            parseErrors();
            //===========================================

        });
    }

    function loadTaxCommissionRoomTree()
    {
        tree_taxcomm = null;
        tree_taxcomm = tax_layout.cells("a").attachTree();
        tree_taxcomm.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_taxcomm.setIconSize('20px', '20px');
        tree_taxcomm.attachEvent("onSelect", onTaxCommTreeNodeSelect);



        //fill in the tree
        //add first general node
        insertTaxCommRoomNode("GENERAL", "General Setting", "");

        //get all the rooms selected in main tab
        var roomids = form_main.getItemValue("rooms_ids");

        if (roomids == "")
        {
            return;
        }

        var arr_ids = roomids.split(",");

        for (var i = 0; i < arr_ids.length; i++)
        {
            //get the details from _dsRooms            
            var room_id = arr_ids[i];
            var item = _dsRooms.item(room_id);

            if (item)
            {

                var room_name = _dsRooms.item(room_id).roomname;
                var room_numbedrooms = _dsRooms.item(room_id).numbedrooms;

                insertTaxCommRoomNode(room_id, room_name, room_numbedrooms);

                if (room_id != "GENERAL")
                {
                    //for each room, populate exception rules if any
                    appendTreeTaxCommiRoomExceptions(room_id);
                }
            }
        }

        //select general node
        tree_taxcomm.selectItem("GENERAL", true, false);
    }

    function populateCboRoomsFilter()
    {   
        comboRoomFilter.enableOptionAutoWidth(true);
        comboRoomFilter.clearAll(true);
        comboRoomFilter.addOption([{value: "-1", text: "--No Filter--", img_src: "images/room_32.png"}]);

        var roomids = form_main.getItemValue("rooms_ids");

        if (roomids == "")
        {
            return;
        }

        var arr_ids = roomids.split(",");

        for (var i = 0; i < arr_ids.length; i++)
        {
            //get the details from _dsRooms            
            var room_id = arr_ids[i];
            var item = _dsRooms.item(room_id);

            if (item)
            {
                var room_name = item.roomname;
                comboRoomFilter.addOption([{value: room_id, text: room_name, img_src: "images/room_32.png"}]);
            }
        }

        comboRoomFilter.setComboValue("-1");
    }

    function populateRoomsTree()
    {
        var roomid_filter = comboRoomFilter.getSelectedValue();


        toolbar_capacity_dates.hideListOption("menus", "new");
        toolbar_capacity_dates.hideListOption("menus", "modify");
        toolbar_capacity_dates.hideListOption("menus", "delete");
        comboVariant.show(false);

        tree_roomdates = null;
        tree_roomdates = roomslayout.cells("a").attachTree();
        tree_roomdates.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_roomdates.setIconSize('20px', '20px');
        tree_roomdates.attachEvent("onSelect", onRoomTreeNodeSelect);
        //tree_roomdates.attachEvent("onDblClick", onRoomTreeNodeDblClick);
        tree_roomdates.attachEvent("onCheck", onRoomTreeNodeChecked);
        tree_roomdates.enableCheckBoxes(true, false);
        tree_roomdates.enableThreeStateCheckboxes(true);

        //fill in the tree

        //get all the rooms selected in main tab
        var roomids = form_main.getItemValue("rooms_ids");
        var first_date_node_selected = "";

        if (roomids == "")
        {
            return;
        }

        var arr_seasons = groupSeasons(); //get an array of seasons for that contract

        var arr_ids = roomids.split(",");

        for (var i = 0; i < arr_ids.length; i++)
        {
            //get the details from _dsRooms            
            var room_id = arr_ids[i];
            var item = _dsRooms.item(room_id);

            if (item && (roomid_filter == "-1" || roomid_filter == room_id))
            {

                var room_name = item.roomname;
                var room_numbedrooms = item.numbedrooms;

                var obj = lookupRoomObj(room_id);
                if (!obj)
                {
                    //insert the room into the capacity json
                    obj = insertJsonRoom(room_id, room_name, room_numbedrooms, "PERSONS");
                }


                //append the room node to the tree
                appendTreeRoom(room_id, room_name, room_numbedrooms, obj.room_rwid, obj.room_variants)

                var arrdates = obj.room_dates;
                var arr_dates_ids_added = []; //will record a list of date ids recorded 

                //for that room, append the seasons where necessary


                for (var s = 0; s < arr_seasons.length; s++)
                {
                    var season = arr_seasons[s].season;
                    var seasonid = arr_seasons[s].seasonid;

                    //for that season, get all date nodes that fall within it

                    var arr_dateperiods = filterDatePeriodsBySeason(arr_seasons[s], arrdates);

                    //===== ok, does this season have dates within it =============
                    if (arr_dateperiods.length > 0)
                    {
                        appendTreeRoomSeason(room_id, season, seasonid);

                        //now insert all dates that belong to that season
                        for (var j = 0; j < arr_dateperiods.length; j++)
                        {
                            var date_rwid = arr_dateperiods[j].date_rwid;
                            var date_dtfrom = arr_dateperiods[j].date_dtfrom;
                            var date_dtto = arr_dateperiods[j].date_dtto;

                            arr_dates_ids_added.push(date_rwid); //recall that date rwid 

                            appendTreeRoomDateNode(room_id, date_rwid, date_dtfrom, date_dtto, seasonid);

                            if (first_date_node_selected == "")
                            {
                                first_date_node_selected = "DATE_" + date_rwid;
                            }

                        }
                    }
                }

                //now see if there are dates that should fall for custom season
                arr_dateperiods = filterDatePeriodsForCustom(arr_dates_ids_added, arrdates);

                //===== ok, there are custom dates =============
                if (arr_dateperiods.length > 0)
                {
                    appendTreeRoomSeason(room_id, "CUSTOM", "CUSTOM_SEASON_ID");

                    //now insert all dates that belong to CUSTOM
                    for (var j = 0; j < arr_dateperiods.length; j++)
                    {
                        var date_rwid = arr_dateperiods[j].date_rwid;
                        var date_dtfrom = arr_dateperiods[j].date_dtfrom;
                        var date_dtto = arr_dateperiods[j].date_dtto;

                        arr_dates_ids_added.push(date_rwid); //recall that date rwid 

                        appendTreeRoomDateNode(room_id, date_rwid, date_dtfrom, date_dtto, "CUSTOM_SEASON_ID");

                        if (first_date_node_selected == "")
                        {
                            first_date_node_selected = "DATE_" + date_rwid;
                        }

                    }
                }

                //==============

                //no dates added for the room 
                //add default contract dates: contract start to contract end
                if (arr_dates_ids_added.length == 0)
                {
                    _capacity_room_date_id--;
                    insertJsonDate(_capacity_room_date_id, room_id, "", "");
                    appendTreeRoomSeason(room_id, "CUSTOM", "CUSTOM_SEASON_ID");
                    appendTreeRoomDateNode(room_id, _capacity_room_date_id, "", "", "CUSTOM_SEASON_ID");

                    if (first_date_node_selected == "")
                    {
                        first_date_node_selected = "DATE_" + _capacity_room_date_id;
                    }
                }

                //==============

            }

        }

        //select the first date node
        tree_roomdates.selectItem(first_date_node_selected, true, false);

    }


    function insertTaxCommiJsonNode(roomid, arrbuysell,
            setting_buying_selling,
            setting_item_fk,
            setting_item_name,
            setting_item_abbrv,
            setting_item_code,
            setting_core_addon,
            setting_basis,
            setting_applyon_formula, setting_rounding)
    {

        var obj_room = lookupTaxCommiRoomObj(roomid);
        var arr = obj_room[arrbuysell];

        _taxcommi_settings_id--;

        var obj = {
            setting_rwid: _taxcommi_settings_id,
            setting_buying_selling: setting_buying_selling,
            setting_row_index: 100000, //will be changed below
            setting_item_fk: setting_item_fk,
            setting_item_name: setting_item_name,
            setting_item_abbrv: setting_item_abbrv,
            setting_item_code: setting_item_code,
            setting_core_addon: setting_core_addon,
            setting_basis: setting_basis,
            setting_applyon_formula: setting_applyon_formula,
            setting_rounding: setting_rounding,
            setting_action: "INSERT",
            setting_values: []
        };

        arr.push(obj);

        return;


    }

    function resetTaxCommiRowIndex(roomid)
    {
        var idx = 0;

        var obj_room = lookupTaxCommiRoomObj(roomid);
        var buying_settings = obj_room.buying_settings;
        var selling_settings = obj_room.selling_settings;

        for (var i = 0; i < buying_settings.length; i++)
        {
            if (buying_settings[i].setting_action != "DELETE")
            {
                buying_settings[i].setting_row_index = idx;
                idx++;
            }
        }

        for (var i = 0; i < selling_settings.length; i++)
        {
            if (selling_settings[i].setting_action != "DELETE")
            {
                selling_settings[i].setting_row_index = idx;
                idx++;
            }
        }

        return;
    }



    function insertTaxCommRoomNode(room_id, room_name, room_numbedrooms)
    {
        var room_rwid = "-1";

        var obj = lookupTaxCommiRoomObj(room_id); //check if room is already in _json_taxcommi

        if (obj)
        {
            room_rwid = obj.room_rwid;
        } else
        {
            _taxcommi_room_rw_id--;
            room_rwid = _taxcommi_room_rw_id;

            var obj = {
                room_rwid: _taxcommi_room_rw_id,
                room_id: room_id,
                room_name: room_name,
                room_numbedrooms: room_numbedrooms,
                room_action: "INSERT",
                room_hasexception: "NO",
                buying_settings: [],
                selling_settings: []};

            _json_taxcommi.push(obj);

        }

        var node_id = room_id;
        var room_setting_tag = "GENERAL";
        var node_style = "font-weight:bold; color:blue;";

        if (room_id != "GENERAL")
        {
            node_id = "ROOM_" + room_id;
            room_setting_tag = "ROOM";
            node_style = "font-weight:normal;";
        }


        var caption = room_name;

        tree_taxcomm.insertNewChild(0, node_id, caption, 0, 0, 0, 0, "CHILD");
        tree_taxcomm.setItemStyle(node_id, node_style);
        tree_taxcomm.setUserData(node_id, "ROOM_EXCEPTION", room_setting_tag);
        tree_taxcomm.setUserData(node_id, "ROOMID", room_id);
        tree_taxcomm.setUserData(node_id, "ROOM_NUMBEDROOMS", room_numbedrooms);
        tree_taxcomm.setUserData(node_id, "ROOM_RWID", room_rwid);

    }

    function insertJsonRoom(room_id, room_name, room_numbedrooms, room_variant)
    {

        _capacity_room_rw_id--;
        var obj = {
            room_rwid: _capacity_room_rw_id,
            room_id: room_id,
            room_name: room_name,
            room_numbedrooms: room_numbedrooms,
            room_variants: room_variant,
            room_action: "INSERT",
            room_dates: []};

        _json_capacity.push(obj);

        return obj;
    }


    function appendTreeRoom(roomid, room_name, room_numbedrooms, room_rwid, room_variant)
    {
        //creates the room node

        var node_id = "ROOM_" + roomid;

        var caption = room_name;

        tree_roomdates.insertNewChild(0, node_id, caption, "bed.png", "bed.png", "bed.png", 0, "CHILD");
        tree_roomdates.setItemStyle(node_id, "font-weight:bold;");
        tree_roomdates.setUserData(node_id, "ROOM_SEASON_DATE", "ROOM");
        tree_roomdates.setUserData(node_id, "ROOM_ROOMID", roomid);
        tree_roomdates.setUserData(node_id, "ROOM_NUMBEDROOMS", room_numbedrooms);
        tree_roomdates.setUserData(node_id, "ROOM_RWID", room_rwid);
        tree_roomdates.setUserData(node_id, "ROOM_VARIANT", room_variant);

        return;
    }


    function appendTreeTaxCommiRoomExceptions(room_id)
    {
        var obj = lookupTaxCommiRoomObj(room_id);
        if (obj)
        {
            if (obj.room_hasexception == "YES")
            {
                insertTreeTaxCommiExceptionNode(room_id);
                return;
            }
        }

    }


    function decideNodeDateCaption(date_dtfrom, date_dtto)
    {

        //get main contract dates first in dd-mm-yyyy
        var contract_activefrom = form_main.getItemValue("active_from", true);
        var contract_activeto = form_main.getItemValue("active_to", true);

        //reformat from dd-mm-yyyy to yyyy-mm-dd
        contract_activefrom = utils_date_to_str(utils_createDateObjFromString(contract_activefrom, "dd-mm-yyyy"));
        contract_activeto = utils_date_to_str(utils_createDateObjFromString(contract_activeto, "dd-mm-yyyy"));


        
        if (date_dtfrom == "")
        {
            date_dtfrom = contract_activefrom;
        }
        if (date_dtto == "")
        {
            date_dtto = contract_activeto;
        }

        var display_from = utils_formatDate(date_dtfrom, "DATE MON YY");
        var display_to = utils_formatDate(date_dtto, "DATE MON YY");

        
        if (display_from == "")
        {
            display_from = "Contract Start"; //contract_activefrom;
        }
        if (display_to == "")
        {
            display_to = "Contract End"; //contract_activeto;
        }

        var display_caption = display_from + " - " + display_to;
        
        
        return display_caption;
    }



    function updateDateNode(roomid, date_rwid, dtfrom, dtto)
    {
        var display_caption = decideNodeDateCaption(dtfrom, dtto);

        var node_id = "DATE_" + date_rwid;
        tree_roomdates.setItemText(node_id, display_caption, "");
        tree_roomdates.setUserData(node_id, "DATE_FROM", dtfrom);
        tree_roomdates.setUserData(node_id, "DATE_TO", dtto);

        //need to know in what season does the new date fall into
        var arr_seasons = groupSeasons(); //get an array of seasons for that contract

        dtfrom = utils_formatDate(dtfrom, "DD-MM-YYYY");
        dtto = utils_formatDate(dtto, "DD-MM-YYYY");

        var season_obj = getSeasonDateNodeWithin(dtfrom, dtto, arr_seasons)

        var current_seasonid = tree_roomdates.getUserData(node_id, "DATE_SEASONID");

        if (season_obj.seasonid == current_seasonid)
        {
            return; //all good, change in date did not imply change in season node
        } else
        {
            //there is a change in season
            //check if the new season is in the tree?
            var newseasonid = "ROOM:" + roomid + "_SEASON:" + season_obj.seasonid;
            var idx = tree_roomdates.getIndexById(newseasonid);
            if (!idx)
            {
                //season node not present, so append the date
                appendTreeRoomSeason(roomid, season_obj.season, season_obj.seasonid);
            }

            //move the date node from old season to new season node
            tree_roomdates.moveItem(node_id, "item_child", newseasonid);
            tree_roomdates.setUserData(node_id, "DATE_SEASONID", season_obj.seasonid)
        }

        return;
    }


    function insertTreeTaxCommiExceptionNode(room_id)
    {
        var display_caption = "Exception";

        var node_id = "EXCEPTION_" + room_id;

        tree_taxcomm.insertNewChild("ROOM_" + room_id, node_id, display_caption, 0, 0, 0, 0, "CHILD");
        tree_taxcomm.setUserData(node_id, "ROOM_EXCEPTION", "EXCEPTION");
        tree_taxcomm.setUserData(node_id, "ROOMID", room_id);

        return;
    }

    function appendTreeRoomSeason(room_id, season, seasonid)
    {
        var node_id = "ROOM:" + room_id + "_SEASON:" + seasonid;

        tree_roomdates.insertNewChild("ROOM_" + room_id, node_id, season, "season.png", "season.png", "season.png", 0, "CHILD");
        tree_roomdates.setItemStyle(node_id, "font-weight:bold;");
        tree_roomdates.setUserData(node_id, "ROOM_SEASON_DATE", "SEASON");
        tree_roomdates.setUserData(node_id, "SEASON_ROOMID", room_id);
        tree_roomdates.setUserData(node_id, "SEASON_SEASONID", seasonid);
        return;
    }

    function appendTreeRoomDateNode(room_id, date_rwid, date_dtfrom, date_dtto, seasonid)
    {
        var display_caption = decideNodeDateCaption(date_dtfrom, date_dtto);

        var parent_id = "ROOM:" + room_id + "_SEASON:" + seasonid;
        var node_id = "DATE_" + date_rwid;

        tree_roomdates.insertNewChild(parent_id, node_id, display_caption, 0, 0, 0, 0, "CHILD");
        tree_roomdates.setUserData(node_id, "ROOM_SEASON_DATE", "DATE");
        tree_roomdates.setUserData(node_id, "DATE_RWID", date_rwid);
        tree_roomdates.setUserData(node_id, "DATE_ROOMID", room_id);
        tree_roomdates.setUserData(node_id, "DATE_SEASONID", seasonid);
        tree_roomdates.setUserData(node_id, "DATE_FROM", date_dtfrom);
        tree_roomdates.setUserData(node_id, "DATE_TO", date_dtto);

        return;
    }


    function lookupCapacityRoomDateObj(room_id, dtrwid)
    {
        //returns capacity room date object

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == room_id &&
                    _json_capacity[i].room_action != "DELETE")
            {
                var arrdates = _json_capacity[i].room_dates;

                for (var j = 0; j < arrdates.length; j++)
                {
                    if (arrdates[j].date_rwid == dtrwid &&
                            arrdates[j].date_action != "DELETE")
                    {
                        return arrdates[j];
                    }
                }
            }
        }

        return null; //this room capacity has not been saved yet
    }

    function lookupSingleParentChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid)
    {
        var arrvalues_policy = policyrulecell.policy_values;
        for (var j = 0; j < arrvalues_policy.length; j++)
        {
            var valuecell = arrvalues_policy[j];
            if (valuecell.value_currencyfk == currencyid &&
                    valuecell.value_basis == context)
            {
                return valuecell;
            }
        }
        return null;
    }

    function lookupChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid)
    {
        var arrvalues_policy = policyrulecell.policy_values;
        for (var j = 0; j < arrvalues_policy.length; j++)
        {
            var valuecell = arrvalues_policy[j];
            if (valuecell.value_currencyfk == currencyid &&
                    valuecell.value_basis == context)
            {
                return valuecell;
            }
        }
        return null;
    }

    function lookupAdultPoliciesDateRuleCellValueObject(policyrulecell, basis, currencyid)
    {
        var arrvalues_policy = policyrulecell.policy_values;
        for (var j = 0; j < arrvalues_policy.length; j++)
        {
            var valuecell = arrvalues_policy[j];
            if (valuecell.value_currencyfk == currencyid &&
                    valuecell.value_basis == basis)
            {
                return valuecell;
            }
        }
        return null;
    }

    function lookupSingleParentChildPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto, adult_child)
    {
        var arrrule_policy = ruleobj.rule_policy;
        for (var i = 0; i < arrrule_policy.length; i++)
        {
            var policyrulecell = arrrule_policy[i];


            if (policyrulecell.policy_category == context &&
                    policyrulecell.policy_basis == child_index &&
                    policyrulecell.policy_adult_child == adult_child &&
                    policyrulecell.policy_child_agefrom == agefrom &&
                    policyrulecell.policy_child_ageto == ageto)
            {
                return policyrulecell;
            }
        }




        return null;
    }

    function lookupChildPoliciesDateRuleCellObject(ruleobj, context, number, agefrom, ageto)
    {
        var arrrule_policy = ruleobj.rule_policy;
        for (var i = 0; i < arrrule_policy.length; i++)
        {
            var policyrulecell = arrrule_policy[i];

            if (policyrulecell.policy_category == context &&
                    policyrulecell.policy_basis == number &&
                    policyrulecell.policy_units_additional_child_agefrom == agefrom &&
                    policyrulecell.policy_units_additional_child_ageto == ageto)
            {
                return policyrulecell;
            }
        }

        return null;
    }

    function lookupAdultPoliciesDateRuleCellObject(ruleobj, category, context, agefrom, ageto)
    {
        var arrrule_policy = ruleobj.rule_policy;
        for (var i = 0; i < arrrule_policy.length; i++)
        {
            var policyrulecell = arrrule_policy[i];


            if (policyrulecell.policy_category == category &&
                    policyrulecell.policy_basis == context &&
                    policyrulecell.policy_units_additional_child_agefrom == agefrom &&
                    policyrulecell.policy_units_additional_child_ageto == ageto)
            {
                return policyrulecell;
            }
        }

        return null;
    }

    function lookupSingleParentChildPoliciesDateRuleObject(dateobj, child_index, rule_ageranges)
    {
        //returns adult policy room object from a capacity date object

        var arrrules = dateobj.date_singleparentpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == child_index &&
                    arrrules[i].rule_ageranges == rule_ageranges &&
                    arrrules[i].rule_action != "DELETE")
            {
                return arrrules[i];
            }
        }

        return null;
    }

    function lookupChildPoliciesDateRuleObject(dateobj, number, sg_shr)
    {
        //returns adult policy room object from a capacity date object

        var arrrules = dateobj.date_childpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == number &&
                    arrrules[i].rule_sharing_single == sg_shr &&
                    arrrules[i].rule_action != "DELETE")
            {
                return arrrules[i];
            }
        }

        return null;
    }

    function lookupAdultPoliciesDateRuleObject(dateobj, category)
    {
        //returns adult policy room object from a capacity date object

        var arrrules = dateobj.date_adultpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == category &&
                    arrrules[i].rule_action != "DELETE")
            {
                return arrrules[i];
            }
        }

        return null;
    }

    function lookupTaxCommiRoomBuySellItem(room_id, buysel, itemrwid)
    {
        var roomobj = lookupTaxCommiRoomObj(room_id);
        if (roomobj)
        {
            var arr = roomobj[buysel];
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i].setting_rwid == itemrwid)
                {
                    return arr[i];
                }
            }
        }


        return null;
    }
    function lookupTaxCommiRoomObj(room_id)
    {
        for (var i = 0; i < _json_taxcommi.length; i++)
        {
            if (_json_taxcommi[i].room_id == room_id)
            {
                return _json_taxcommi[i];
            }
        }

        return null; //this room capacity has not been saved yet
    }

    function lookupRoomObj(room_id)
    {
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == room_id)
            {
                return _json_capacity[i];
            }
        }

        return null; //this room capacity has not been saved yet
    }


    function formatContractTitle()
    {
        var active_from = form_main.getItemValue("active_from", true);
        var active_to = form_main.getItemValue("active_to", true);
        var contractname = form_main.getItemValue("contractname");

        popupwin_contracts.setText("Contract Details: " + contractname + " " + active_from + " - " + active_to);
        form_main.setItemValue("invoice_text", contractname);
    }

    function saveCapacityDate()
    {

        var daterwid = form_capacitydates.getItemValue("id");
        var dtfrom = form_capacitydates.getItemValue("override_dtfrom", true);
        var dtto = form_capacitydates.getItemValue("override_dtto", true);
        var roomid = form_capacitydates.getItemValue("roomid");

        var msg = validateCapacityDates(dtfrom, dtto, roomid, daterwid);

        if (msg != "OK")
        {
            dhtmlx.alert({
                text: msg,
                type: "alert-warning",
                title: "Validate Capacity Dates",
                callback: function () {
                    form_capacitydates.setItemFocus("override_dtfrom");
                    form_capacitydates.validateItem("override_dtfrom");
                }
            });

            return;
        }

        if (dtfrom == form_main.getItemValue("active_from") || dtfrom == "")
        {
            dtfrom = "";
        } else
        {
            //reformat from dd-mm-yyyy to yyyy-mm-dd
            dtfrom = utils_date_to_str(utils_createDateObjFromString(dtfrom, "dd-mm-yyyy"));
        }


        if (dtto == form_main.getItemValue("active_to") || dtto == "")
        {
            dtto = "";
        } else
        {
            //reformat from dd-mm-yyyy to yyyy-mm-dd
            dtto = utils_date_to_str(utils_createDateObjFromString(dtto, "dd-mm-yyyy"));
        }



        if (daterwid == "-1")
        {
            //add new record
            addNewDateRecord(roomid, dtfrom, dtto);

        } else
        {
            //modify record
            updateJsonCapacityDateRecord(daterwid, roomid, dtfrom, dtto);
            updateDateNode(roomid, daterwid, dtfrom, dtto);
            tree_roomdates.selectItem("DATE_" + daterwid, true, false);
        }


        grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
        validateContract(["rooms_dates", "rooms_minstay", "rooms_capacity", "rooms_policies", "rooms_meal"]);
        parseErrors();

        popupwin_capacitydates.setModal(false);
        popupwin_capacitydates.hide();
        popupwin_contracts.setModal(true);

    }

    function deleteCapacityDate(roomid, date_rwid)
    {
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                for (var j = 0; j < _json_capacity[i].room_dates.length; j++)
                {
                    var dateid = _json_capacity[i].room_dates[j].date_rwid;

                    if (dateid == date_rwid)
                    {
                        _json_capacity[i].room_dates[j].date_action = "DELETE";

                        grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
                        validateContract(["rooms_dates", "rooms_minstay", "rooms_capacity", "rooms_policies", "rooms_meal"]);
                        parseErrors();

                        return true;
                    }
                }
            }
        }
    }



    function insertJsonDate(newid, roomid, dtfrom, dtto)
    {
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var obj = {date_rwid: newid,
                    date_dtfrom: dtfrom,
                    date_dtto: dtto,
                    date_action: "INSERT",
                    date_minstay_rules: [],
                    date_policies_checkinout: [],
                    date_policies_cancellation: [],
                    date_mealsupplement_rules: [],
                    date_mealextrasupplement_rules: [],
                    date_capacity_rules: [],
                    date_adultpolicies_rules: [],
                    date_childpolicies_rules: [],
                    date_singleparentpolicies_rules: []};

                //============================================
                //push default min stay value
                _min_stay_id--;
                var obj_minstay = {minstay_rwid: _min_stay_id,
                    minstay_description: "ONE NIGHT",
                    minstay_action: "INSERT",
                    minstay_duration: "1"};
                obj.date_minstay_rules.push(obj_minstay)
                //============================================

                //============================================
                //push default meal plan value
                _meals_id--;
                var obj_meals = {meal_rwid: _meals_id,
                    meal_mealplanfk: form_main.getItemValue("mealplan_fk"),
                    meal_ismain: "1",
                    meal_adult_count: "",
                    meal_action: "INSERT",
                    meal_children: []};
                obj.date_mealsupplement_rules.push(obj_meals)
                //============================================

                _json_capacity[i].room_dates.push(obj);
                return;
            }
        }
        return;
    }

    function updateJsonCapacityDateRecord(daterwid, roomid, dtfrom, dtto)
    {
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                for (var j = 0; j < _json_capacity[i].room_dates.length; j++)
                {
                    var dateid = _json_capacity[i].room_dates[j].date_rwid;

                    if (dateid == daterwid)
                    {
                        _json_capacity[i].room_dates[j].date_dtfrom = dtfrom;
                        _json_capacity[i].room_dates[j].date_dtto = dtto;
                        _json_capacity[i].room_dates[j].date_action = "UPDATE";
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function validateCapacityDates(capacity_from, capacity_to, roomid, daterwid)
    {

        //capacity.from must be between contract.from and contract.to
        //capacity.to must be between contract.from and contract.to
        //capacity.from <= capacity.to
        //no overlapping with existing capacity.from and capacity.to for same room

        if (!subDatesWithinContract(capacity_from, capacity_to))
        {
            return "Capacity Dates must be within Contract Dates " + form_main.getItemValue("active_from", true) + " and " + form_main.getItemValue("active_to", true) + "!";
        }

        if (utils_isDate(capacity_from) && utils_isDate(capacity_to))
            if (!utils_validateDateOrder(capacity_from, capacity_to))
            {

                return "Invalid Date Order!";
            }

        if (!validateCapacityRoomOverlap(capacity_from, capacity_to, roomid, daterwid))
        {
            return "Overlapping with Existing Dates of Room";
        }


        return "OK";
    }


    function validateCapacityRoomOverlap(dtfrom, dtto, roomid, daterwid)
    {
        //dtfrom,dtto in dd-mm-yyyy

        //iterate in _json_capacity
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                for (var j = 0; j < _json_capacity[i].room_dates.length; j++)
                {
                    var dateid = _json_capacity[i].room_dates[j].date_rwid;
                    var date_dtfrom = _json_capacity[i].room_dates[j].date_dtfrom;
                    var date_dtto = _json_capacity[i].room_dates[j].date_dtto;
                    var date_action = _json_capacity[i].room_dates[j].date_action;

                    date_dtfrom = utils_formatDate(date_dtfrom, "DD-MM-YYYY");
                    date_dtto = utils_formatDate(date_dtto, "DD-MM-YYYY");

                    if (dateid != daterwid && date_action != "DELETE")
                    {
                        var chk1 = utils_validateDateOrder(dtfrom, date_dtto);
                        var chk2 = utils_validateDateOrder(date_dtfrom, dtto);
                        if (chk1 && chk2)
                        {
                            return false;
                        }
                    }

                }
            }
        }

        return true;
    }

    function subDatesWithinContract(dtfrom, dtto)
    {
        var outcome = true;

        var active_from = form_main.getItemValue("active_from", true);
        var active_to = form_main.getItemValue("active_to", true);


        if (dtfrom == active_from)
        {
            dtfrom = "";
        }

        if (dtto == active_to)
        {
            dtto = "";
        }

        if (utils_isDate(dtfrom))
        {
            if (utils_validateDateOrder(dtfrom, active_from) ||
                    utils_validateDateOrder(active_to, dtfrom))
            {
                outcome = false;
            }
        }


        if (utils_isDate(dtto))
        {
            if (utils_validateDateOrder(dtto, active_from) ||
                    utils_validateDateOrder(active_to, dtto))
            {
                outcome = false;
            }
        }

        return outcome;
    }

    function deleteCapacityRule()
    {
        var ruleid = grid_capacity_age.getSelectedRowId();
        if (!ruleid)
        {
            return;
        }

        var nodeid = tree_roomdates.getSelectedItemId();

        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        var arrrules = arrdates[j].date_capacity_rules;
                        for (var k = 0; k < arrrules.length; k++)
                        {
                            if (arrrules[k].rule_rwid == ruleid)
                            {
                                arrrules[k].rule_action = "DELETE";
                                grid_capacity_age.deleteRow(ruleid);
                                updateAllCheckedDatesNode("date_capacity_rules");
                                return;
                            }
                        }
                    }
                }
            }
        }

        return;
    }

    function addNewCapacityRule()
    {
        var nodeid = tree_roomdates.getSelectedItemId();

        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");


        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var nodeid_room = "ROOM_" + roomid;
        var variant = tree_roomdates.getUserData(nodeid_room, "ROOM_VARIANT");

        if (variant == "UNITS")
        {
            //one max row allowed
            if (grid_capacity_age.getRowsNum() > 0)
            {
                dhtmlx.alert({
                    text: "A maximum of One rule allowed for Persons!",
                    type: "alert-warning",
                    title: "New Rule",
                    callback: function () {
                    }
                });

                return false;
            }
        }

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _capacity_room_date_rule_id--;

                        var obj = {rule_rwid: _capacity_room_date_rule_id,
                            rule_rulecounter: 0,
                            rule_action: "INSERT",
                            rule_capacity: []};
                        arrdates[j].date_capacity_rules.push(obj);
                        grid_capacity_age.addRow(_capacity_room_date_rule_id, "");
                        grid_capacity_age.setRowTextStyle(_capacity_room_date_rule_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


                        return;
                    }
                }
            }
        }

    }



    function onGridExtraCheck(rId, cInd, state) {


        if (state)
        {
            state = 1;
        } else
        {
            state = 0;
        }

        var colid = grid_extras.getColumnId(cInd);
        updateJsonExtras(rId, colid, state);
        updateAllCheckedDatesNode("date_mealextrasupplement_rules");

        if (colid == "include_diner_rate_bb" && state == 0)
        {
            //clear hb_mealplan_fk, bb_mealplan_fk

            updateJsonExtras(rId, "hb_mealplan_fk", "");
            updateJsonExtras(rId, "bb_mealplan_fk", "");
            updateAllCheckedDatesNode("date_mealextrasupplement_rules");
            grid_extras.cells(rId, grid_extras.getColIndexById("hb_mealplan_fk")).setValue("");
            grid_extras.cells(rId, grid_extras.getColIndexById("bb_mealplan_fk")).setValue("");
        }

        return true;
    }

    function onGridMealCheck(rId, cInd, state) {

        hidePopups();

        if (state)
        {
            state = 1;
        } else
        {
            state = 0;
        }

        var colid = grid_meals.getColumnId(cInd);
        updateJsonMeals(rId, colid, state);
        updateAllCheckedDatesNode("date_mealsupplement_rules");

        if (state == 1)
        {
            //set all over rows to 0
            for (var i = 0; i < grid_meals.getRowsNum(); i++)
            {
                var rowid = grid_meals.getRowId(i);
                if (rowid != rId)
                {
                    updateJsonMeals(rowid, colid, "0");
                    updateAllCheckedDatesNode("date_mealsupplement_rules");
                    grid_meals.cells(rowid, cInd).setValue("0");
                }
            }
        }

        return true;
    }




    function onGridExtraEdit(stage, rId, cInd, nValue, oValue)
    {
        hidePopups();

        var colid = grid_extras.getColumnId(cInd);

        if (stage == 0)
        {
            if (colid == "hb_mealplan_fk" || colid == "bb_mealplan_fk")
            {
                var value = grid_extras.cells(rId, grid_extras.getColIndexById("include_diner_rate_bb")).getValue();
                if (value == 0)
                {
                    return false;
                }
            }
        } else if (stage == 1)
        {
            if (grid_extras.editor && grid_extras.editor.obj)
            {
                grid_extras.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {

            if (nValue != oValue)
            {
                if (colid == "adult_count" || colid.indexOf("ch_") != -1)
                {
                    //validate if numeric >= 0
                    nValue = utils_trim(nValue, " ");


                    if (nValue != "")
                    {

                        if (isNaN(nValue))
                        {
                            return false;
                        }

                        nValue = parseInt(nValue, 10);
                        if (nValue < 0)
                        {
                            return false;
                        }
                    }
                }
            }

            //all clear to place value in _json_meals
            updateJsonExtras(rId, colid, nValue);
            updateAllCheckedDatesNode("date_mealextrasupplement_rules");
        }


        return true;
    }

    function onGridCancellationKeyPress(code, cFlag, sFlag)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");





        var rwid = grid_cancellation.getSelectedRowId();
        var ind = grid_cancellation.getSelectedCellIndex();
        if (rwid && ind)
        {
            if (code == 46) //delete key
            {
                var colid = grid_cancellation.getColumnId(ind);
                if (colid == "cancellation_dates_before_arrival_from")
                {
                    grid_cancellation.cells(rwid, ind).setValue("");
                    updateJsonCancellation(rwid, colid, "", roomid, daterwid);
                    updateAllCheckedDatesNode("date_policies_cancellation");

                } else if (colid == "cancellation_dates_before_arrival_to")
                {
                    grid_cancellation.cells(rwid, ind).setValue("");
                    updateJsonCancellation(rwid, colid, "", roomid, daterwid);
                    updateAllCheckedDatesNode("date_policies_cancellation");
                }
            }
        }

        return true;
    }


    function onGridSingleParentPolicyEdit(stage, rId, cInd, nValue, oValue)
    {

        var colid = grid_singleparentpolicy_age.getColumnId(cInd);

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }


        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


        hidePopups();

        if (stage == 1)
        {
            if (grid_singleparentpolicy_age.editor && grid_singleparentpolicy_age.editor.obj)
            {
                grid_singleparentpolicy_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {

            if (nValue != oValue)
            {
                var c = grid_singleparentpolicy_age.cells(rId, cInd);

                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");

                if (colid == "adult_category" || colid == "adult_basis")
                {
                    var arr_adultvalues = lookupDefaultSingleAdultRate(rId);
                    fillDefaultSingleAdultValues(rId, arr_adultvalues);

                } else if (colid.indexOf("child_category_") != -1 || colid.indexOf("child_basis_") != -1)
                {
                    var arr_childvalues = lookupDefaultSingleChildRate(rId, cInd);
                    fillDefaultSingleChildValues(rId, arr_childvalues, agefrom, ageto);

                } else if (type == "edn")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    //display updates sales price if buying price changed
                    var currencyinputid = c.getAttribute("currencyid");
                    calculateSingleParentPolicySalesPrice(rId, cInd, nValue, currencyinputid);
                }

                updateJsonSingleParentChildPoliciesValues(cInd, roomid, daterwid, rId, nValue);
                updateAllCheckedDatesNode("date_singleparentpolicies_rules");

                //if change is made in adult, then copy paste the value to all other rows
                //with same rule_index - just for aesthetic
                //if (colid.indexOf("adult_") != -1)
                //{
                //    duplicateSingleParentAdultValuesDisplay(rId, colid, nValue, roomid, daterwid);
                //}


            }
        }

        return true;
    }

    function duplicateSingleParentAdultValuesDisplay(rwid, colid, nValue, roomid, daterwid)
    {
        //get the rule_index of that cell
        var colidx = grid_singleparentpolicy_age.getColIndexById(colid);
        var c = grid_singleparentpolicy_age.cells(rwid, colidx);
        var rule_index = c.getAttribute("rule_index");

        //iterate across each cell with same colid and rule_index and paste the value nValue
        for (var i = 0; i < grid_singleparentpolicy_age.getRowsNum(); i++)
        {
            var rowid = grid_singleparentpolicy_age.getRowId(i);
            if (rowid != rwid)
            {
                var _c = grid_singleparentpolicy_age.cells(rowid, colidx);
                var _rule_index = _c.getAttribute("rule_index");
                if (rule_index == _rule_index)
                {
                    _c.setValue(nValue);

                    if (roomid && daterwid)
                    {
                        updateJsonSingleParentChildPoliciesValues(colidx, roomid, daterwid, rowid, nValue);
                    }
                }
            }
        }
    }

    function fillDefaultSingleChildValues(rwid, arr_childvalues, child_agefrom, child_ageto)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return;
        }
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


        var colNum = grid_singleparentpolicy_age.getColumnsNum();

        for (var cidx = 0; cidx < colNum; cidx++)
        {
            var c = grid_singleparentpolicy_age.cells(rwid, cidx);

            var context = c.getAttribute("context");
            var adult_child = c.getAttribute("adult_child");
            var currencyid = c.getAttribute("currencyid");
            var type = c.getAttribute("type");
            var agefrom = c.getAttribute("agefrom");
            var ageto = c.getAttribute("ageto");
            var buy_sell = c.getAttribute("buy_sell");

            if (type == "edn" && buy_sell == "buy" && context == "value" &&
                    adult_child == "CHILD" &&
                    agefrom == child_agefrom && ageto == child_ageto)
            {
                for (var i = 0; i < arr_childvalues.length; i++)
                {
                    var currencyfk = arr_childvalues[i].value_currencyfk;
                    var val = arr_childvalues[i].value_value;
                    var action = arr_childvalues[i].value_action;

                    if (action != "DELETE" && currencyfk == currencyid)
                    {

                        //override default value
                        c.setValue(val);
                        updateJsonSingleParentChildPoliciesValues(c.cell.cellIndex,
                                roomid, daterwid,
                                rwid, val);



                        //calculate SP where necessary
                        if (type == "edn" && buy_sell == "buy" && currencyid != "")
                        {
                            calculateSingleParentPolicySalesPrice(rwid,
                                    c.cell.cellIndex,
                                    val, currencyid);
                        }
                    }
                }
            }
        }



    }

    function fillDefaultSingleAdultValues(rwid, arr_adultvalues)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return;
        }
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");



        grid_singleparentpolicy_age.forEachCell(rwid, function (c) {

            var context = c.getAttribute("context");
            var adult_child = c.getAttribute("adult_child");
            var currencyid = c.getAttribute("currencyid");
            var type = c.getAttribute("type");
            var agefrom = c.getAttribute("agefrom");
            var ageto = c.getAttribute("ageto");
            var buy_sell = c.getAttribute("buy_sell");

            if (type == "edn" && buy_sell == "buy" && context == "value" &&
                    adult_child == "ADULT" && agefrom == "0" && ageto == "0")
            {
                for (var i = 0; i < arr_adultvalues.length; i++)
                {
                    var currencyfk = arr_adultvalues[i].value_currencyfk;
                    var val = arr_adultvalues[i].value_value;
                    var action = arr_adultvalues[i].value_action;

                    if (action != "DELETE" && currencyfk == currencyid)
                    {

                        //override default value
                        c.setValue(val);
                        updateJsonSingleParentChildPoliciesValues(c.cell.cellIndex,
                                roomid, daterwid,
                                rwid, val);


                        //duplicateSingleParentAdultValuesDisplay(rwid, grid_singleparentpolicy_age.getColumnId(c.cell.cellIndex), val, roomid, daterwid);


                        //calculate SP where necessary
                        if (type == "edn" && buy_sell == "buy" && currencyid != "")
                        {
                            calculateSingleParentPolicySalesPrice(rwid,
                                    c.cell.cellIndex,
                                    val, currencyid);
                        }

                    }
                }
            }
        });
    }

    function lookupDefaultSingleChildRate(rid, colidx)
    {
        //get child_category
        //get child_basis


        var child_index = grid_singleparentpolicy_age.cells(rid, colidx).getAttribute("child_index");
        var agefrom = grid_singleparentpolicy_age.cells(rid, colidx).getAttribute("agefrom");
        var ageto = grid_singleparentpolicy_age.cells(rid, colidx).getAttribute("ageto");

        var category_colidx = "child_category_" + agefrom + "_" + ageto;
        category_colidx = grid_singleparentpolicy_age.getColIndexById(category_colidx);
        var child_category = grid_singleparentpolicy_age.cells(rid, category_colidx).getValue();

        var basis_colidx = "child_basis_" + agefrom + "_" + ageto;
        basis_colidx = grid_singleparentpolicy_age.getColIndexById(basis_colidx);
        var child_basis = grid_singleparentpolicy_age.cells(rid, basis_colidx).getValue();


        if (child_basis != "FLAT")
        {
            return [];
        }

        //if child_category = SINGLE => lookup in same row Adult Single + FLAT
        //if child_category = 1/2 DOUBLE => lookup in same row Adult 1/2 DOUBLE + FLAT
        //if child_category = SHARING => lookup in child sharing: Child Index + FLAT

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return [];
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return [];
        }


        if (child_category == "SINGLE")
        {
            var arr = capacity_date_obj.date_adultpolicies_rules;
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i].rule_action != "DELETE" && arr[i].rule_category == "1")
                {
                    var arr_policy = arr[i].rule_policy;
                    for (var j = 0; j < arr_policy.length; j++)
                    {
                        if (arr_policy[j].policy_action != "DELETE")
                        {
                            var policy_category = arr_policy[j].policy_category;

                            if (policy_category == "1")
                            {
                                //extract values currency buy
                                return arr_policy[j].policy_values;
                            }
                        }
                    }
                }
            }
        } else if (child_category == "1/2 DBL")
        {
            var arr = capacity_date_obj.date_adultpolicies_rules;
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i].rule_action != "DELETE" && arr[i].rule_category == "2")
                {
                    var basis = _lookupRulePolicyBasisValue(arr[i].rule_policy);

                    if (basis == "1/n")
                    {
                        //that it! extract the value
                        return _returnRulePolicyArrayValue(arr[i].rule_policy);
                    }
                }
            }
        } else if (child_category == "SHARING")
        {
            return lookupFromSharingChildrenByIndex(child_index, capacity_date_obj.date_childpolicies_rules, agefrom, ageto);
        }

        return [];

    }


    function lookupFromSharingChildrenByIndex(child_index, arr_childpolicies_rules, agefrom, ageto)
    {

        for (var i = 0; i < arr_childpolicies_rules.length; i++)
        {
            if (arr_childpolicies_rules[i].rule_sharing_single == "SHARING" &&
                    arr_childpolicies_rules[i].rule_category == child_index &&
                    arr_childpolicies_rules[i].rule_action != "DELETE")
            {
                var arr_rule_policy = arr_childpolicies_rules[i].rule_policy;

                var basis = _lookupRuleChildPolicyCategoryBasisValue(arr_rule_policy, agefrom, ageto);

                if (basis == "FLAT")
                {
                    //that it! extract the value
                    return _returnRuleChildPolicyArrayValue(arr_rule_policy, agefrom, ageto);
                }
            }
        }

        return [];
    }

    function _returnRulePolicyArrayValue(arr_rule_policy, agefrom, ageto)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            if (arr_rule_policy[i].policy_action != "DELETE" &&
                    arr_rule_policy[i].policy_category == "value" &&
                    arr_rule_policy[i].agefrom == agefrom &&
                    arr_rule_policy[i].ageto == ageto)
            {

                return arr_rule_policy[i].policy_values;
            }
        }

        return [];
    }

    function _returnRuleChildPolicyArrayValue(arr_rule_policy, agefrom, ageto)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            if (arr_rule_policy[i].policy_action != "DELETE" &&
                    arr_rule_policy[i].policy_units_additional_child_agefrom == agefrom &&
                    arr_rule_policy[i].policy_units_additional_child_ageto == ageto &&
                    arr_rule_policy[i].policy_category == "value")
            {
                var arr_policy_values = arr_rule_policy[i].policy_values;

                for (var j = 0; j < arr_policy_values.length; j++)
                {
                    if (arr_policy_values[j].value_action != "DELETE" &&
                            arr_policy_values[j].value_basis == "value")
                    {
                        return arr_policy_values;
                    }
                }
            }
        }

        return "";
    }

    function _lookupRuleChildPolicyCategoryBasisValue(arr_rule_policy, agefrom, ageto)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            if (arr_rule_policy[i].policy_action != "DELETE" &&
                    arr_rule_policy[i].policy_units_additional_child_agefrom == agefrom &&
                    arr_rule_policy[i].policy_units_additional_child_ageto == ageto &&
                    arr_rule_policy[i].policy_category == "basis")
            {
                var arr_policy_values = arr_rule_policy[i].policy_values;

                for (var j = 0; j < arr_policy_values.length; j++)
                {
                    if (arr_policy_values[j].value_action != "DELETE" &&
                            arr_policy_values[j].value_basis == "basis")
                    {
                        return arr_policy_values[j].value_value;
                    }
                }
            }
        }

        return "";
    }


    function lookupDefaultSingleAdultRate(rid)
    {
        //get adultcategory
        //get adultbasis

        var adult_category = grid_singleparentpolicy_age.cells(rid, grid_singleparentpolicy_age.getColIndexById("adult_category")).getValue();
        var adult_basis = grid_singleparentpolicy_age.cells(rid, grid_singleparentpolicy_age.getColIndexById("adult_basis")).getValue();

        if (adult_basis != "FLAT")
        {
            return [];
        }

        //if adultcategory = SINGLE => lookup adult category = 1
        //if adultcategory = 1/2 DOUBLE=> lookup adult category = 2 + basis = 1/2

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return [];
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return [];
        }

        var arr = capacity_date_obj.date_adultpolicies_rules;

        //=========== easy for single ==================
        if (adult_category == "SINGLE")
        {
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i].rule_action != "DELETE" && arr[i].rule_category == "1")
                {
                    var arr_policy = arr[i].rule_policy;
                    for (var j = 0; j < arr_policy.length; j++)
                    {
                        if (arr_policy[j].policy_action != "DELETE")
                        {
                            var policy_category = arr_policy[j].policy_category;

                            if (policy_category == "1")
                            {
                                //extract values currency buy
                                return arr_policy[j].policy_values;
                            }
                        }
                    }
                }
            }
        }
        //now for 1/2 DOUBLE
        else if (adult_category == "1/2 DBL") {
            for (var i = 0; i < arr.length; i++)
            {
                if (arr[i].rule_action != "DELETE" && arr[i].rule_category == "2")
                {
                    var basis = _lookupRulePolicyBasisValue(arr[i].rule_policy);

                    if (basis == "1/n")
                    {
                        //that it! extract the value
                        return _returnRulePolicyArrayValue(arr[i].rule_policy);
                    }
                }
            }
        }


        return [];
    }



    function _returnRulePolicyArrayValue(arr_rule_policy)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            if (arr_rule_policy[i].policy_action != "DELETE" &&
                    arr_rule_policy[i].policy_basis == "value")
            {

                return arr_rule_policy[i].policy_values;
            }
        }

        return [];
    }

    function _lookupRulePolicyBasisValue(arr_rule_policy)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            if (arr_rule_policy[i].policy_action != "DELETE" &&
                    arr_rule_policy[i].policy_basis == "basis")
            {

                var arr_policy_values = arr_rule_policy[i].policy_values;

                for (var j = 0; j < arr_policy_values.length; j++)
                {
                    if (arr_policy_values[j].value_action != "DELETE" &&
                            arr_policy_values[j].value_basis == "basis")
                    {
                        return arr_policy_values[j].value_value;
                    }
                }

            }
        }

        return "";
    }

    function onGridChildSharingPolicyEdit(stage, rId, cInd, nValue, oValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }


        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


        hidePopups();

        if (stage == 1)
        {
            if (grid_childpolicy_sharing_age.editor && grid_childpolicy_sharing_age.editor.obj)
            {
                grid_childpolicy_sharing_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                var c = grid_childpolicy_sharing_age.cells(rId, cInd);

                var type = c.getAttribute("type");

                if (type == "edn")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    //display updates sales price if buying price changed
                    var currencyinputid = c.getAttribute("currencyid");
                    calculateChildPolicySalesPrice(grid_childpolicy_sharing_age, rId, cInd, nValue, currencyinputid);
                }

                updateJsonChildPoliciesValues(cInd, roomid, daterwid, rId, nValue, "SHARING", grid_childpolicy_sharing_age);
                updateAllCheckedDatesNode("date_childpolicies_rules");
            }
        }

        return true;
    }

    function onGridChildSinglePolicyEdit(stage, rId, cInd, nValue, oValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }


        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


        hidePopups();

        if (stage == 1)
        {
            if (grid_childpolicy_single_age.editor && grid_childpolicy_single_age.editor.obj)
            {
                grid_childpolicy_single_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                var c = grid_childpolicy_single_age.cells(rId, cInd);

                var type = c.getAttribute("type");

                if (type == "edn")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    //display updates sales price if buying price changed
                    var currencyinputid = c.getAttribute("currencyid");
                    calculateChildPolicySalesPrice(grid_childpolicy_single_age, rId, cInd, nValue, currencyinputid);
                }

                updateJsonChildPoliciesValues(cInd, roomid, daterwid, rId, nValue, "SINGLE", grid_childpolicy_single_age);
                updateAllCheckedDatesNode("date_childpolicies_rules");
            }
        }

        return true;
    }

    function onGridAdultPolicyEdit(stage, rId, cInd, nValue, oValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }


        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


        hidePopups();

        if (stage == 1)
        {
            if (grid_adultpolicy_age.editor && grid_adultpolicy_age.editor.obj)
            {
                grid_adultpolicy_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                var c = grid_adultpolicy_age.cells(rId, cInd);

                var type = c.getAttribute("type");

                if (type == "edn")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    //display updates sales price if buying price changed
                    var currencyinputid = c.getAttribute("currencyid");
                    calculateAdultPolicySalesPrice(rId, cInd, nValue, currencyinputid);

                }

                updateJsonAdultPoliciesValues(cInd, roomid, daterwid, rId, nValue);
                updateAllCheckedDatesNode("date_adultpolicies_rules");
            }
        }

        return true;
    }


    function updateJsonSingleParentChildPoliciesValues(cInd, roomid, date_rwid, rId, nValue)
    {


        var c = grid_singleparentpolicy_age.cells(rId, cInd);
        var context = c.getAttribute("context");
        var child_index = c.getAttribute("child_index");
        var currencyid = c.getAttribute("currencyid");
        var agefrom = c.getAttribute("agefrom");
        var ageto = c.getAttribute("ageto");
        var adult_child = c.getAttribute("adult_child");
        var rule_ageranges = c.getAttribute("rule_ageranges");


        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }


        var ruleobj = lookupSingleParentChildPoliciesDateRuleObject(capacity_date_obj, child_index, rule_ageranges);
        if (!ruleobj)
        {
            //insert rule object into json_capacity
            _singleparentchildpolicy_room_date_rule_id--;
            ruleobj = {rule_rwid: _singleparentchildpolicy_room_date_rule_id,
                rule_category: child_index,
                rule_rulecounter: 0,
                rule_ageranges: rule_ageranges,
                rule_action: "INSERT",
                rule_policy: []};

            capacity_date_obj.date_singleparentpolicies_rules.push(ruleobj);
        }

        var policyrulecell = lookupSingleParentChildPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto, adult_child);

        if (!policyrulecell)
        {
            _singleparentchildpolicy_room_date_rule_capacity_id--;
            policyrulecell = {
                policy_rwid: _singleparentchildpolicy_room_date_rule_capacity_id,
                policy_category: context,
                policy_basis: child_index,
                policy_adult_child: adult_child,
                policy_child_agefrom: agefrom,
                policy_child_ageto: ageto,
                policy_action: "INSERT",
                policy_values: []
            };

            ruleobj.rule_policy.push(policyrulecell);
        }


        var valuecell = lookupSingleParentChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
        if (!valuecell)
        {
            _singleparentchildpolicy_room_date_rule_capacity_value_id--;
            valuecell = {value_rwid: _singleparentchildpolicy_room_date_rule_capacity_value_id,
                value_currencyfk: currencyid,
                value_basis: context,
                value_value: nValue,
                value_action: "INSERT"};

            policyrulecell.policy_values.push(valuecell);
        }

        valuecell.value_value = nValue;

    }

    function updateJsonChildPoliciesValues(cInd, roomid, date_rwid, rId, nValue, sg_shr, grid)
    {
        var c = grid.cells(rId, cInd);
        var context = c.getAttribute("context");
        var number = c.getAttribute("number");
        var currencyid = c.getAttribute("currencyid");
        var agefrom = c.getAttribute("agefrom");
        var ageto = c.getAttribute("ageto");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var ruleobj = lookupChildPoliciesDateRuleObject(capacity_date_obj, number, sg_shr);
        if (!ruleobj)
        {
            //insert rule object into json_capacity
            _childpolicy_room_date_rule_id--;
            ruleobj = {rule_rwid: _childpolicy_room_date_rule_id,
                rule_category: number,
                rule_rulecounter: 0,
                rule_sharing_single: sg_shr,
                rule_action: "INSERT",
                rule_policy: []};

            capacity_date_obj.date_childpolicies_rules.push(ruleobj);
        }

        var policyrulecell = lookupChildPoliciesDateRuleCellObject(ruleobj, context, number, agefrom, ageto);

        if (!policyrulecell)
        {
            _childpolicy_room_date_rule_capacity_id--;
            policyrulecell = {
                policy_rwid: _childpolicy_room_date_rule_capacity_id,
                policy_category: context,
                policy_basis: number,
                policy_units_additional_child_agefrom: agefrom,
                policy_units_additional_child_ageto: ageto,
                policy_action: "INSERT",
                policy_values: []
            };

            ruleobj.rule_policy.push(policyrulecell);
        }


        var valuecell = lookupChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
        if (!valuecell)
        {
            _childpolicy_room_date_rule_capacity_value_id--;
            valuecell = {value_rwid: _childpolicy_room_date_rule_capacity_value_id,
                value_currencyfk: currencyid,
                value_basis: context,
                value_value: nValue,
                value_action: "INSERT"};

            policyrulecell.policy_values.push(valuecell);
        }

        valuecell.value_value = nValue;

        return;
    }

    function updateJsonAdultPoliciesValues(cInd, roomid, date_rwid, rId, nValue)
    {
        var c = grid_adultpolicy_age.cells(rId, cInd);
        var context = c.getAttribute("context");
        var variant = c.getAttribute("variant");
        var category = c.getAttribute("category");
        var currencyid = c.getAttribute("currencyid");
        var agefrom = c.getAttribute("agefrom");
        var ageto = c.getAttribute("ageto");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }


        //category = 1,2,3 ... n
        var ruleobj = lookupAdultPoliciesDateRuleObject(capacity_date_obj, category);
        if (!ruleobj)
        {
            //insert rule object into json_capacity
            _adultpolicy_room_date_rule_id--;
            ruleobj = {rule_rwid: _adultpolicy_room_date_rule_id,
                rule_rulecounter: 0,
                rule_category: category,
                rule_action: "INSERT",
                rule_policy: []};

            capacity_date_obj.date_adultpolicies_rules.push(ruleobj);
        }

        var policyrulecell = lookupAdultPoliciesDateRuleCellObject(ruleobj, category, context, agefrom, ageto);

        if (!policyrulecell)
        {
            _adultpolicy_room_date_rule_capacity_id--;
            policyrulecell = {
                policy_rwid: _adultpolicy_room_date_rule_capacity_id,
                policy_category: category,
                policy_basis: context,
                policy_units_additional_child_agefrom: agefrom,
                policy_units_additional_child_ageto: ageto,
                policy_action: "INSERT",
                policy_values: []
            };

            ruleobj.rule_policy.push(policyrulecell);
        }


        var valuecell = lookupAdultPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
        if (!valuecell)
        {
            _adultpolicy_room_date_rule_capacity_value_id--;
            valuecell = {value_rwid: _adultpolicy_room_date_rule_capacity_value_id,
                value_currencyfk: currencyid,
                value_basis: context,
                value_value: nValue,
                value_action: "INSERT"};

            policyrulecell.policy_values.push(valuecell);
        }

        valuecell.value_value = nValue;

        return;

    }

    function onGridCancellationEdit(stage, rId, cInd, nValue, oValue)
    {
        hidePopups();

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");

        var colid = grid_cancellation.getColumnId(cInd);

        if (stage == 1)
        {
            if (grid_cancellation.editor && grid_cancellation.editor.obj)
            {
                grid_cancellation.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                if (colid == "cancellation_charge_value" ||
                        colid == "cancellation_days_before_arrival_from" ||
                        colid == "cancellation_days_before_arrival_to")
                {
                    if (nValue != "")
                    {
                        nValue = utils_trim(nValue, " ");
                        if (nValue == "")
                        {
                            nValue = "0";
                        }

                        if (isNaN(nValue))
                        {
                            return false;
                        }

                        nValue = parseInt(nValue, 10);
                        if (nValue < 0)
                        {
                            return false;
                        }
                    }
                }


                updateJsonCancellation(rId, colid, nValue, roomid, daterwid);
                updateAllCheckedDatesNode("date_policies_cancellation");
            }

        }

        return true;
    }

    function onGridCheckInOutEdit(stage, rId, cInd, nValue, oValue)
    {
        hidePopups();

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var colid = grid_checkinouts.getColumnId(cInd);

        if (stage == 0)
        {
            if (colid == "checkinout_time_beforeafter")
            {
                return false; //do not allow editing
            }
            return true;
        } else if (stage == 1)
        {
            if (grid_checkinouts.editor && grid_checkinouts.editor.obj)
            {
                grid_checkinouts.editor.obj.select(); /* grid.editor.obj is the input object*/
            }

        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                if (colid == "checkinout_policytype")
                {
                    if (nValue == "ECI")
                    {
                        //set beforeafter to before
                        grid_checkinouts.cells(rId, grid_checkinouts.getColIndexById("checkinout_time_beforeafter")).setValue("BEFORE");
                        onGridCheckInOutEdit(2, rId, grid_checkinouts.getColIndexById("checkinout_time_beforeafter"), "BEFORE", "");
                    } else if (nValue == "LCO")
                    {
                        //set beforeafter to before
                        grid_checkinouts.cells(rId, grid_checkinouts.getColIndexById("checkinout_time_beforeafter")).setValue("AFTER");
                        onGridCheckInOutEdit(2, rId, grid_checkinouts.getColIndexById("checkinout_time_beforeafter"), "AFTER", "");
                    }
                } else if (colid == "checkinout_charge_value")
                {
                    if (nValue != "")
                    {
                        nValue = utils_trim(nValue, " ");
                        if (nValue == "")
                        {
                            nValue = "0";
                        }

                        if (isNaN(nValue))
                        {
                            return false;
                        }

                        nValue = parseInt(nValue, 10);
                        if (nValue < 0)
                        {
                            return false;
                        }
                    }
                } else if (colid == "checkinout_checkinout_time")
                {
                    if (nValue != "")
                    {
                        if (!utils_isValidTime(nValue))
                        {
                            return false;
                        }
                    }

                }

                //all clear to place value in _json_meals
                updateJsonCheckInOut(rId, colid, nValue, roomid, daterwid);
                updateAllCheckedDatesNode("date_policies_checkinout");

            }

        }

        return true;
    }


    function onGridMealEdit(stage, rId, cInd, nValue, oValue)
    {
        hidePopups();



        var colid = grid_meals.getColumnId(cInd);

        if (stage == 1)
        {
            if (grid_meals.editor && grid_meals.editor.obj)
            {
                grid_meals.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {


            if (nValue != oValue)
            {
                if (colid == "adult_count" || colid.indexOf("ch_") != -1)
                {
                    //validate if numeric >= 0
                    if (nValue != "")
                    {
                        nValue = utils_trim(nValue, " ");
                        if (nValue == "")
                        {
                            nValue = "0";
                        }

                        if (isNaN(nValue))
                        {
                            return false;
                        }

                        nValue = parseInt(nValue, 10);
                        if (nValue < 0)
                        {
                            return false;
                        }
                    }
                }
            }



            //all clear to place value in _json_meals
            updateJsonMeals(rId, colid, nValue);
            updateAllCheckedDatesNode("date_mealsupplement_rules");
        }

        return true;
    }




    function updateJsonExtras(rwid, colid, nValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrextra = capacity_date_obj.date_mealextrasupplement_rules;

        for (var i = 0; i < arrextra.length; i++)
        {
            var extra_rwid = arrextra[i].extra_rwid;
            if (extra_rwid == rwid)
            {
                if (colid == "extra_name")
                {
                    arrextra[i].extra_extra_name = nValue;
                } else if (colid == "mandatory")
                {
                    arrextra[i].extra_mandatory = nValue;
                } else if (colid == "include_diner_rate_bb")
                {
                    arrextra[i].extra_include_diner_rate_bb = nValue;
                } else if (colid == "hb_mealplan_fk")
                {
                    arrextra[i].extra_hb_mealplan_fk = nValue;
                } else if (colid == "bb_mealplan_fk")
                {
                    arrextra[i].extra_bb_mealplan_fk = nValue;
                } else if (colid == "extra_date")
                {
                    arrextra[i].extra_extra_date = nValue;
                } else if (colid == "adult_count")
                {
                    arrextra[i].extra_adult_count = nValue;
                } else if (colid.indexOf("ch_") != -1)
                {
                    //children update

                    var arr_age_data = colid.split("_");
                    var agfrom = arr_age_data[1];
                    var agto = arr_age_data[2];

                    var arr_extra_children = arrextra[i].extra_children;
                    for (var j = 0; j < arr_extra_children.length; j++)
                    {
                        if (arr_extra_children[j].child_agefrom == agfrom &&
                                arr_extra_children[j].child_ageto == agto)
                        {
                            arr_extra_children[j].child_count = nValue;
                            arr_extra_children[j].child_action = "UPDATE";

                            if (arrextra[i].extra_action != "DELETE")
                            {
                                arrextra[i].extra_action = "UPDATE";
                            }
                            return;
                        }
                    }

                    //if we are here, need to insert new children age node
                    _extras_children_age_id--;

                    var obj = {child_rwid: _extras_children_age_id,
                        child_agefrom: agfrom,
                        child_ageto: agto,
                        child_count: nValue,
                        child_action: "INSERT"};
                    arr_extra_children.push(obj);

                }

                if (arrextra[i].extra_action != "DELETE")
                {
                    arrextra[i].extra_action = "UPDATE";
                }
                return;
            }
        }
    }

    function updateJsonCancellation(rId, attribute, nValue, roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrcancellation = capacity_date_obj.date_policies_cancellation;

        for (var i = 0; i < arrcancellation.length; i++)
        {
            if (rId == arrcancellation[i].cancellation_rwid)
            {
                arrcancellation[i][attribute] = nValue;
                return;
            }
        }
    }

    function updateJsonCheckInOut(rId, attribute, nValue, roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrcheckinout = capacity_date_obj.date_policies_checkinout;

        for (var i = 0; i < arrcheckinout.length; i++)
        {
            if (rId == arrcheckinout[i].checkinout_rwid)
            {
                arrcheckinout[i][attribute] = nValue;
                return;
            }
        }
    }


    function updateJsonMeals(rwid, colid, nValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrmeals = capacity_date_obj.date_mealsupplement_rules;

        for (var i = 0; i < arrmeals.length; i++)
        {
            var meal_rwid = arrmeals[i].meal_rwid;
            if (meal_rwid == rwid)
            {
                if (colid == "mealplanfk")
                {
                    arrmeals[i].meal_mealplanfk = nValue;
                } else if (colid == "is_main")
                {
                    arrmeals[i].meal_ismain = nValue;
                } else if (colid == "adult_count")
                {
                    arrmeals[i].meal_adult_count = nValue;
                } else if (colid.indexOf("ch_") != -1)
                {
                    //children update

                    var arr_age_data = colid.split("_");
                    var agfrom = arr_age_data[1];
                    var agto = arr_age_data[2];

                    var arr_meal_children = arrmeals[i].meal_children;
                    for (var j = 0; j < arr_meal_children.length; j++)
                    {
                        if (arr_meal_children[j].child_agefrom == agfrom &&
                                arr_meal_children[j].child_ageto == agto)
                        {
                            arr_meal_children[j].child_count = nValue;
                            arr_meal_children[j].child_action = "UPDATE";
                            if (arrmeals[i].meal_action != "DELETE")
                            {
                                arrmeals[i].meal_action = "UPDATE";
                            }
                            return;
                        }
                    }

                    //if we are here, need to insert new children age node
                    _meals_children_age_id--;

                    var obj = {child_rwid: _meals_children_age_id,
                        child_agefrom: agfrom,
                        child_ageto: agto,
                        child_count: nValue,
                        child_action: "INSERT"};
                    arr_meal_children.push(obj);

                }

                if (arrmeals[i].meal_action != "DELETE")
                {
                    arrmeals[i].meal_action = "UPDATE";
                }

                return;
            }
        }
    }

    function onGridMinStayEdit(stage, rId, cInd, nValue, oValue) {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var colid = grid_minstay.getColumnId(cInd);

        if (stage == 1)
        {
            if (grid_minstay.editor && grid_minstay.editor.obj)
            {
                grid_minstay.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            //validate if numeric >= 0
            if (nValue != oValue)
            {
                if (colid == "minstay_duration")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    nValue = parseInt(nValue, 10);
                    if (nValue < 0)
                    {
                        return false;
                    }

                }

                //place value in _json_capacity
                updateJsonCapacityMinStay(colid, roomid, daterwid, rId, nValue);
                updateAllCheckedDatesNode("date_minstay_rules");
            }

        }
        return true;
    }

    function onGridCapacityAgeEdit(stage, rId, cInd, nValue, oValue) {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");

        var nodeid_room = "ROOM_" + roomid;
        var variant = tree_roomdates.getUserData(nodeid_room, "ROOM_VARIANT");


        if (stage == 1)
        {
            if (grid_capacity_age.editor && grid_capacity_age.editor.obj)
            {
                grid_capacity_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            //validate if numeric >= 0
            if (nValue != oValue)
            {
                nValue = utils_trim(nValue, " ");
                if (nValue == "")
                {
                    nValue = "0";
                }

                if (isNaN(nValue))
                {
                    return false;
                }

                nValue = parseInt(nValue, 10);

                if (nValue < 0)
                {
                    return false;
                }

                //place value in _json_capacity
                updateJsonCapacityCategoryAge(cInd, variant, roomid, daterwid, rId, nValue);
                cleanJsonCapacityCategoryAge(roomid, daterwid);
                updateAllCheckedDatesNode("date_capacity_rules");
            }

        }
        return true;
    }

    function cleanJsonCapacityCategoryAge(roomid, daterwid)
    {
        //delete nodes where capacity_maxpax == capacity_maxpax == 0

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        var arrrules = arrdates[j].date_capacity_rules;
                        for (var k = 0; k < arrrules.length; k++)
                        {
                            var arrcapacity = arrrules[k].rule_capacity;
                            for (var l = 0; l < arrcapacity.length; l++)
                            {
                                var capacity_obj = arrcapacity[l];
                                if (capacity_obj.capacity_action != "DELETE")
                                {
                                    if (capacity_obj.capacity_maxpax == 0 && capacity_obj.capacity_minpax == 0)
                                    {
                                        capacity_obj.capacity_action = "DELETE";
                                    } else
                                    {
                                        //check children ages
                                        var capacity_category = capacity_obj.capacity_category;
                                        var child_age_from = capacity_obj.capacity_child_agefrom;
                                        var child_age_to = capacity_obj.capacity_child_ageto;
                                        if (capacity_category == "CHILD" || capacity_category == "CH")
                                        {
                                            //make sure the age is valid
                                            var chk = is_age_in_main(child_age_from, child_age_to);
                                            if (!chk)
                                            {
                                                //make sure it is a valid mix
                                                chk = is_age_in_mix(child_age_from, child_age_to);
                                                if (!chk)
                                                {
                                                    capacity_obj.capacity_action = "DELETE";
                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function updateJsonCapacityCategoryAge(cInd, variant, roomid, daterwid, ruleid, nValue)
    {
        var obj = process_capacity_age_colid(cInd, variant);


        if (variant == "PERSONS" || variant == "UNITS")
        {
            var adult_child = obj.category; //ADULT, CHILD
            var min_max = obj.min_max; //Mi,Mx
            var child_age_value_from = obj.child_age_value_from;
            var child_age_value_to = obj.child_age_value_to;

            for (var i = 0; i < _json_capacity.length; i++)
            {
                if (_json_capacity[i].room_id == roomid)
                {
                    var arrdates = _json_capacity[i].room_dates;
                    for (var j = 0; j < arrdates.length; j++)
                    {
                        if (daterwid == arrdates[j].date_rwid)
                        {
                            var arrrules = arrdates[j].date_capacity_rules;
                            for (var k = 0; k < arrrules.length; k++)
                            {
                                if (arrrules[k].rule_rwid == ruleid)
                                {
                                    var updated_capacity = false;
                                    var arrcapacity = arrrules[k].rule_capacity;
                                    for (var l = 0; l < arrcapacity.length; l++)
                                    {
                                        var capacity_obj = arrcapacity[l];
                                        if (capacity_obj.capacity_category == adult_child &&
                                                capacity_obj.capacity_child_agefrom == child_age_value_from &&
                                                capacity_obj.capacity_child_ageto == child_age_value_to)
                                        {
                                            if (min_max == "Mi")
                                            {
                                                capacity_obj.capacity_minpax = nValue;
                                            } else
                                            {
                                                capacity_obj.capacity_maxpax = nValue;
                                            }

                                            capacity_obj.capacity_action = "UPDATE";
                                            updated_capacity = true;

                                            if (capacity_obj.capacity_minpax == "0" &&
                                                    capacity_obj.capacity_maxpax == "0")
                                            {
                                                capacity_obj.capacity_action = "DELETE";
                                            }
                                        }
                                    }

                                    //===
                                    if (!updated_capacity)
                                    {
                                        _capacity_room_date_rule_capacity_id--;
                                        var newobj = {capacity_rwid: _capacity_room_date_rule_capacity_id,
                                            capacity_category: adult_child,
                                            capacity_minpax: "", capacity_maxpax: "",
                                            capacity_child_agefrom: child_age_value_from,
                                            capacity_child_ageto: child_age_value_to,
                                            capacity_action: "INSERT"};

                                        if (min_max == "Mi")
                                        {
                                            newobj.capacity_minpax = nValue;
                                        } else
                                        {
                                            newobj.capacity_maxpax = nValue;
                                        }

                                        if (newobj.capacity_minpax == "0" &&
                                                newobj.capacity_maxpax == "0")
                                        {
                                            newobj.capacity_action = "DELETE";
                                        }

                                        arrrules[k].rule_capacity.push(newobj);
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }



    function process_capacity_age_colid(cInd, variant)
    {
        var colid = grid_capacity_age.getColumnId(cInd);
        var arr_age_data = colid.split("_");

        if (variant == "UNITS")
        {
            //standardoccupation_Mi,standardoccupation_Mx,additionalpersons_Mi,additionalpersons_Mx
            var category = arr_age_data[0];
            var min_max = arr_age_data[1]; //Mi,Mx
            var child_age_value_from = "0";
            var child_age_value_to = "0";

            if (category == "CH")
            {
                category = "CHILD";
            } else
            {
                category = category.toUpperCase();

                if (min_max == "Mi")
                {
                    child_age_value_from = arr_age_data[2];
                    cInd++;
                    colid = grid_capacity_age.getColumnId(cInd);
                    arr_age_data = colid.split("_");
                    child_age_value_to = arr_age_data[2];
                } else
                {
                    child_age_value_to = arr_age_data[2];
                    cInd--;
                    colid = grid_capacity_age.getColumnId(cInd);
                    arr_age_data = colid.split("_");
                    child_age_value_from = arr_age_data[2];
                }

            }

            return {category: category,
                min_max: min_max,
                child_age_value_from: child_age_value_from,
                child_age_value_to: child_age_value_to};

        } else
        {

            var adult_child = arr_age_data[0]; //Ad,Ch
            var min_max = arr_age_data[1]; //Mi,Mx
            var child_age_value_from = "0";
            var child_age_value_to = "0";

            if (adult_child == "Ad")
            {
                adult_child = "ADULT";

            } else
            {
                adult_child = "CHILD";

                if (min_max == "Mi")
                {
                    child_age_value_from = arr_age_data[2];
                    cInd++;
                    colid = grid_capacity_age.getColumnId(cInd);
                    arr_age_data = colid.split("_");
                    child_age_value_to = arr_age_data[2];
                } else
                {
                    child_age_value_to = arr_age_data[2];
                    cInd--;
                    colid = grid_capacity_age.getColumnId(cInd);
                    arr_age_data = colid.split("_");
                    child_age_value_from = arr_age_data[2];
                }
            }

            return {category: adult_child,
                min_max: min_max,
                child_age_value_from: child_age_value_from,
                child_age_value_to: child_age_value_to};
        }
    }



    function fillGridCapacityAgeAcross(rwid, arrcapactity, variant)
    {

        for (var i = 0; i < grid_capacity_age.getColumnsNum(); i++)
        {
            var obj = process_capacity_age_colid(i, variant);


            var adult_child = obj.category; //ADULT, CHILD
            var min_max = obj.min_max; //Mi,Mx
            var child_age_value_from = obj.child_age_value_from;
            var child_age_value_to = obj.child_age_value_to;

            for (var j = 0; j < arrcapactity.length; j++)
            {
                var age_obj = arrcapactity[j];
                if (age_obj.capacity_category == adult_child &&
                        age_obj.capacity_child_agefrom == child_age_value_from &&
                        age_obj.capacity_child_ageto == child_age_value_to)
                {
                    if (age_obj.capacity_minpax == 0 && age_obj.capacity_maxpax == 0)
                    {
                        //do nothing
                    } else
                    {
                        var placevalue = "0";
                        if (min_max == "Mi")
                        {
                            placevalue = age_obj.capacity_minpax;
                        } else
                        {
                            placevalue = age_obj.capacity_maxpax;
                        }

                        grid_capacity_age.cells(rwid, i).setValue(placevalue);
                    }
                }
            }
        }
    }


    function deleteCancellationRecord(rId)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");

        grid_cancellation.deleteRow(rId);
        updateJsonCancellation(rId, "cancellation_action", "DELETE", roomid, daterwid);
        updateAllCheckedDatesNode("date_policies_cancellation");
    }

    function deleteCheckInOutRecord(rId) {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");

        grid_checkinouts.deleteRow(rId);
        updateJsonCheckInOut(rId, "checkinout_action", "DELETE", roomid, daterwid);
        updateAllCheckedDatesNode("date_policies_checkinout");

    }

    function deleteMealRecord(rwid) {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrmeal = capacity_date_obj.date_mealsupplement_rules;

        for (var i = 0; i < arrmeal.length; i++)
        {
            if (arrmeal[i].meal_rwid == rwid)
            {
                arrmeal[i].meal_action = "DELETE";
                grid_meals.deleteRow(rwid);
                updateAllCheckedDatesNode("date_mealsupplement_rules");
                return;
            }
        }
    }

    function deleteExtraRecord(rwid) {


        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var arrextra = capacity_date_obj.date_mealextrasupplement_rules;

        for (var i = 0; i < arrextra.length; i++)
        {
            if (arrextra[i].extra_rwid == rwid)
            {
                arrextra[i].extra_action = "DELETE";
                grid_extras.deleteRow(rwid);
                updateAllCheckedDatesNode("date_mealextrasupplement_rules");
                return;
            }
        }
    }


    function addNewExtraRecord()
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _extras_id--;

                        var obj = {
                            extra_rwid: _extras_id,
                            extra_extra_name: "",
                            extra_mandatory: "0",
                            extra_include_diner_rate_bb: "0",
                            extra_hb_mealplan_fk: "",
                            extra_bb_mealplan_fk: "",
                            extra_extra_date: "",
                            extra_adult_count: "",
                            extra_action: "INSERT",
                            extra_children: []};
                        arrdates[j].date_mealextrasupplement_rules.push(obj);
                        grid_extras.addRow(_extras_id, ["", "", 0, 0]);
                        grid_extras.setRowTextStyle(_extras_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                        return;
                    }
                }
            }
        }
    }


    function addNewCancellationRecord()
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _cancellation_id--;

                        var obj = {cancellation_rwid: _cancellation_id,
                            cancellation_canceltype: "",
                            cancellation_charge_method: "",
                            cancellation_charge_value: "",
                            cancellation_days_before_arrival_from: "",
                            cancellation_days_before_arrival_to: "",
                            cancellation_dates_before_arrival_from: "",
                            cancellation_dates_before_arrival_to: "",
                            cancellation_action: "INSERT"};

                        arrdates[j].date_policies_cancellation.push(obj);
                        grid_cancellation.addRow(_cancellation_id, ["", ""]);
                        grid_cancellation.setRowTextStyle(_cancellation_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        return;
                    }
                }
            }
        }
    }


    function addNewCheckInOutRecord()
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _checkinout_id--;

                        var obj = {checkinout_rwid: _checkinout_id,
                            checkinout_policytype: "",
                            checkinout_time_beforeafter: "",
                            checkinout_checkinout_time: "",
                            checkinout_charge_type: "",
                            checkinout_charge_value: "",
                            checkinout_action: "INSERT"};

                        arrdates[j].date_policies_checkinout.push(obj);
                        grid_checkinouts.addRow(_checkinout_id, ["", ""]);
                        grid_checkinouts.setRowTextStyle(_checkinout_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        return;
                    }
                }
            }
        }
    }

    function addNewMealRecord()
    {

        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == roomid)
            {
                var arrdates = _json_capacity[i].room_dates;
                for (var j = 0; j < arrdates.length; j++)
                {
                    if (daterwid == arrdates[j].date_rwid)
                    {
                        _meals_id--;

                        var obj = {meal_rwid: _meals_id,
                            meal_mealplanfk: "",
                            meal_ismain: "0",
                            meal_adult_count: "",
                            meal_action: "INSERT",
                            meal_children: []};
                        arrdates[j].date_mealsupplement_rules.push(obj);
                        grid_meals.addRow(_meals_id, ["", 0]);
                        grid_meals.setRowTextStyle(_meals_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                        return;
                    }
                }
            }
        }
    }


    function showRoomDateCombinations(nodeid)
    {
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        var roomid = "";
        var dateid = "";

        if (node == "ROOM")
        {
            roomid = tree_roomdates.getUserData(nodeid, "ROOM_ROOMID");

        } else if (node == "DATE")
        {
            roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
            dateid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        }

        var arr_errors = validate_room_capacity(roomid);

        if (arr_errors.length > 0)
        {
            //select the date node

            return;
        }

        var params = "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid + "&dateid=" + dateid +
                "&json_capacity=" + encodeURIComponent(JSON.stringify(_json_capacity));


        popupwin_capacitycombinations.center();
        popupwin_capacitycombinations.show();
        popupwin_contracts.setModal(false);
        popupwin_capacitycombinations.setModal(true);

        layout_capacitycombii.cells("a").progressOn();


        dhtmlxAjax.post("php/api/hotelcontracts/generateroomcombinations.php", params, function (loader) {

            if (loader)
            {
                layout_capacitycombii.cells("a").progressOff();

                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "GENERATE COMBINATIONS",
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
                        title: "GENERATE COMBINATIONS",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    displayCombinations(json_obj.COMBINATIONS);
                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "GENERATE COMBINATIONS",
                        callback: function () {
                        }
                    });
                }
            }
        });

    }


    function modifyRoomDate(nodeid)
    {
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node == "DATE")
        {
            var active_from = form_main.getItemValue("active_from", true);
            var active_to = form_main.getItemValue("active_to", true);


            form_capacitydates.clear();
            form_capacitydates.setItemValue("id", tree_roomdates.getUserData(nodeid, "DATE_RWID"));
            form_capacitydates.setItemValue("roomid", tree_roomdates.getUserData(nodeid, "DATE_ROOMID"));
            form_capacitydates.setItemValue("contract_active_from", active_from);
            form_capacitydates.setItemValue("contract_active_to", active_to);

            var dtfrom = tree_roomdates.getUserData(nodeid, "DATE_FROM");
            var dtto = tree_roomdates.getUserData(nodeid, "DATE_TO");

            dtfrom = utils_formatDate(dtfrom, "DD-MM-YYYY");
            dtto = utils_formatDate(dtto, "DD-MM-YYYY");

            form_capacitydates.setItemValue("override_dtfrom", dtfrom);
            form_capacitydates.setItemValue("override_dtto", dtto);

            form_capacitydates.getCalendar("override_dtfrom").clearSensitiveRange();
            form_capacitydates.getCalendar("override_dtto").clearSensitiveRange();
            form_capacitydates.getCalendar("override_dtfrom").setSensitiveRange(form_main.getItemValue("active_from"), form_main.getItemValue("active_to"));
            form_capacitydates.getCalendar("override_dtto").setSensitiveRange(form_main.getItemValue("active_from"), form_main.getItemValue("active_to"));

            if (!subDatesWithinContract(dtfrom, dtto))
            {
                form_capacitydates.validateItem("override_dtfrom");
                form_capacitydates.validateItem("override_dtto");
                dhtmlx.alert({
                    text: "Careful, Capacity Dates seem outside Contract Dates!",
                    type: "alert-warning",
                    title: "Modify Capacity Dates",
                    callback: function () {
                    }
                });

            }

            popupwin_contracts.setModal(false);
            popupwin_capacitydates.center();
            popupwin_capacitydates.setModal(true);
            popupwin_capacitydates.show();
        }
    }

    function onComboRoomFilterChangefunction()
    {
        //show hide nodes where necessary
        populateRoomsTree();
    }

    function onComboVariantChangefunction()
    {
        var variant = comboVariant.getSelectedValue();

        var nodeid = tree_roomdates.getSelectedItemId();

        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node == "ROOM")
        {
            var existing_room_variant = tree_roomdates.getUserData(nodeid, "ROOM_VARIANT");

            if (existing_room_variant != variant)
            {
                tree_roomdates.setUserData(nodeid, "ROOM_VARIANT", variant);

                var roomid = tree_roomdates.getUserData(nodeid, "ROOM_ROOMID")

                //update into json
                for (var i = 0; i < _json_capacity.length; i++)
                {
                    if (_json_capacity[i].room_id == roomid)
                    {
                        _json_capacity[i].room_variants = variant;


                        var arrdates = _json_capacity[i].room_dates;
                        for (var j = 0; j < arrdates.length; j++)
                        {
                            if (arrdates[j].date_action != "DELETE")
                            {
                                //delete all room capacity rules for each date
                                var arr_date_capacity_rules = arrdates[j].date_capacity_rules;
                                for (var k = 0; k < arr_date_capacity_rules.length; k++)
                                {
                                    arr_date_capacity_rules[k].rule_action = "DELETE"
                                }

                                //delete all date_adultpolicies_rules
                                var arr_date_adultpolicies_rules = arrdates[j].date_adultpolicies_rules;
                                for (var k = 0; k < arr_date_adultpolicies_rules.length; k++)
                                {
                                    arr_date_adultpolicies_rules[k].rule_action = "DELETE"
                                }

                                //delete all date_childpolicies_rules
                                var arr_date_childpolicies_rules = arrdates[j].date_childpolicies_rules;
                                for (var k = 0; k < arr_date_childpolicies_rules.length; k++)
                                {
                                    arr_date_childpolicies_rules[k].rule_action = "DELETE"
                                }

                                //delete all date_singleparentpolicies_rules
                                var arr_date_singleparentpolicies_rules = arrdates[j].date_singleparentpolicies_rules;
                                for (var k = 0; k < arr_date_singleparentpolicies_rules.length; k++)
                                {
                                    arr_date_singleparentpolicies_rules[k].rule_action = "DELETE"
                                }
                            }
                        }



                        return;
                    }
                }
            }
        }
    }

    function hidePopups()
    {
        popup_problem.hide();
    }


    function addTaxCommiException()
    {

        var nodeid = tree_taxcomm.getSelectedItemId();
        var node = tree_taxcomm.getUserData(nodeid, "ROOM_EXCEPTION");

        if (node == "ROOM")
        {
            var room_id = tree_taxcomm.getUserData(nodeid, "ROOMID");
            insertTreeTaxCommiExceptionNode(room_id);
            copyInsertPasteTaxCommiException("GENERAL", room_id);
            tree_taxcomm.selectItem("EXCEPTION_" + room_id, true, false);
        }
    }


    function copyInsertPasteTaxCommiException(roomidfrom, roomidto)
    {
        var objfrom = lookupTaxCommiRoomObj(roomidfrom);
        var objto = lookupTaxCommiRoomObj(roomidto);

        objto.room_hasexception = "YES";

        var arrbuying_copy = utils_deepCopy(objfrom.buying_settings);
        var arrselling_copy = utils_deepCopy(objfrom.selling_settings);

        //reset the ids and value ids

        var array_of_array = [arrbuying_copy, arrselling_copy];

        for (var i = 0; i < array_of_array.length; i++)
        {
            var thearray = array_of_array[i];

            for (var j = 0; j < thearray.length; j++)
            {
                var _obj = thearray[j];
                if (_obj.setting_action != "DELETE")
                {
                    _taxcommi_settings_id--;
                    _obj.setting_rwid = _taxcommi_settings_id;

                    var arrvalues = _obj.setting_values;
                    for (var k = 0; k < arrvalues.length; k++)
                    {
                        if (arrvalues[k].value_action != "DELETE")
                        {
                            _taxcommi_settings_value_id--;
                            arrvalues[k].value_rwid = _taxcommi_settings_value_id;
                        }
                    }
                }
            }
        }

        objto.buying_settings = arrbuying_copy;
        objto.selling_settings = arrselling_copy;
    }

    function intersectCurrencyArrays(arr_currency_buy, arr_currency_sell)
    {
        //intersect arr_currency_buy and arr_currency_sell so that similar currency codes are together
        var arr_intersect = arr_currency_buy.filter(value => -1 !== arr_currency_sell.indexOf(value));

        var arr_buy = [];
        var arr_sell = [];

        for (var i = 0; i < arr_intersect.length; i++)
        {
            arr_buy.push(arr_intersect[i]);
            arr_sell.push(arr_intersect[i]);
        }

        //insert the remaining items from arr_currency_buy into arr_buy
        for (var i = 0; i < arr_currency_buy.length; i++)
        {
            if (arr_buy.indexOf(arr_currency_buy[i]) == -1)
            {
                arr_buy.push(arr_currency_buy[i]);
            }
        }

        //insert the remaining items from arr_currency_sell into arr_sell
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            if (arr_sell.indexOf(arr_currency_sell[i]) == -1)
            {
                arr_sell.push(arr_currency_sell[i]);
            }
        }


        return {buy: arr_buy, sell: arr_sell};
    }


    function validate_taxcommi_settings(room_id)
    {
        //if room_id is blank, then test all settings for general and room exceptions
        //otherwise test only for room passed on

        var arr_error = [];

        //validate all rooms        
        for (var i = 0; i < _json_taxcommi.length; i++)
        {
            if (_json_taxcommi[i].room_hasexception == "YES")
            {
                var my_room_id = _json_taxcommi[i].room_id;
                var my_room_name = _json_taxcommi[i].room_name;

                if (room_id == "" || room_id == my_room_id)
                {
                    var err = validateSpecificRoomTaxCommiSetting(my_room_id);
                    if (err != "")
                    {
                        arr_error.push({roomid: my_room_id, room_name: my_room_name, msg: err.msg, buysell: err.buysell});
                    }
                }
            }
        }


        return arr_error;
    }


    function validateSpecificRoomTaxCommiSetting(roomid)
    {
        //rules buying:
        //first item must be core
        //only forumula of first item can be blank

        //selling rules
        //formula cannot be blank

        //basis for ADDON items can be either PPPN or PN
        //can never have both PPPN and PN in the same room

        var roomobj = lookupTaxCommiRoomObj(roomid);
        if (!roomobj)
        {
            return "";
        }

        var addon_basis = "";
        var first = true;
        for (var i = 0; i < roomobj.buying_settings.length; i++)
        {
            var item = roomobj.buying_settings[i];
            if (item.setting_action != "DELETE")
            {

                var coreaddon = item.setting_core_addon;
                if (coreaddon != "CORE" && first)
                {
                    return {buysell: "buying", msg: "<b>Tax:</b> Initial Item in Buying Setting should be a <b>CORE</b> item!"};
                }

                if (!first)
                {
                    //all non first rows cannot have blank formula
                    var formula = utils_trim(item.setting_applyon_formula, " ");
                    if (formula == "")
                    {

                        return {buysell: "buying", msg: "<b>Tax:</b> <b>Buying Setting</b>: Formula cell cannot be Blank for Non-Initial Items!"};
                    }
                }


                if (coreaddon == "ADDON")
                {
                    //check basis PPPN or PN
                    var _basis = utils_trim(item.setting_basis, " ");
                    if (_basis == "")
                    {
                        return {buysell: "buying", msg: "<b>Tax:</b> <b>Buying Setting</b>: Basis cannot be Blank for Add-On Items!"};
                    }

                    if (_basis.includes("PNI"))
                    {
                        _basis = "PNI";
                    } else if (_basis.includes("PPPN"))
                    {
                        _basis = "PPPN";
                    }

                    //===================================
                    if (addon_basis == "")
                    {
                        addon_basis = _basis;
                    } else if (addon_basis != _basis)
                    {
                        return {buysell: "buying", msg: "<b>Tax:</b> <b>Buying Setting</b>: Basis cannot be both <b>PPPN</b> and <b>PNI</b> in the same Room!"};
                    }
                    //===================================
                    if (item.setting_item_code == "COMMI" ||
                            item.setting_item_code == "CCCOMMI" ||
                            item.setting_item_code == "SREPCOMMI")
                    {
                        //cannot allow flat values for commission
                        if (item.setting_basis.includes("FLAT"))
                        {
                            return {buysell: "buying", msg: "<b>Tax:</b> <b>Buying Setting</b>: Commission can only be % and not be FLAT"};
                        }
                    }
                    //===================================

                }
                first = false;
            }
        }


        //=========================================
        for (var i = 0; i < roomobj.selling_settings.length; i++)
        {
            var item = roomobj.selling_settings[i];
            if (item.setting_action != "DELETE")
            {
                var coreaddon = item.setting_core_addon;
                var formula = utils_trim(item.setting_applyon_formula, " ");
                if (formula == "")
                {
                    return {buysell: "selling", msg: "<b>Tax:</b> <b>Selling Setting</b>: Formula cell cannot be Blank for Selling Items!"};
                }

                if (coreaddon == "ADDON")
                {
                    //check basis PPPN or PN
                    var _basis = utils_trim(item.setting_basis, " ");
                    if (_basis == "")
                    {
                        return {buysell: "selling", msg: "<b>Tax:</b> <b>Selling Setting</b>: Basis cannot be Blank for Add-On Items!"};
                    }

                    if (_basis.includes("PNI"))
                    {
                        _basis = "PNI";
                    } else if (_basis.includes("PPPN"))
                    {
                        _basis = "PPPN";
                    }

                    //===================================
                    if (addon_basis == "")
                    {
                        addon_basis = _basis;
                    } else if (addon_basis != _basis)
                    {
                        return {buysell: "selling", msg: "<b>Tax:</b> <b>Selling Setting</b>: Basis cannot be both <b>PPPN</b> and <b>PNI</b> in the same Room!"};
                    }
                    //===================================

                    if (item.setting_item_code == "COMMI" ||
                            item.setting_item_code == "CCCOMMI" ||
                            item.setting_item_code == "SREPCOMMI")
                    {
                        //cannot allow flat values for commission
                        if (item.setting_basis.includes("FLAT"))
                        {
                            return {buysell: "selling", msg: "<b>Tax:</b> <b>Selling Setting</b>: Commission can only be % and not be FLAT"};
                        }
                    }

                }

            }
        }

        return "";
    }

    function testTaxCommiSettings()
    {
        var nodeid = tree_taxcomm.getSelectedItemId();
        var room_id = tree_taxcomm.getUserData(nodeid, "ROOMID");

        var arr_errors = validate_taxcommi_settings(room_id);
        if (arr_errors.length > 0)
        {
            var err_msg = "";
            for (var i = 0; i < arr_errors.length; i++)
            {
                err_msg += arr_errors[i].err + "<br>";
            }

            dhtmlx.alert({
                text: err_msg,
                type: "alert-warning",
                title: "Validate Tax Commission",
                callback: function () {
                    grid_choices.selectRowById("tax", false, true, true);
                }
            });

        }


        popupwin_contracts.setModal(false);
        popupwin_testtaxcomm.show();
        popupwin_testtaxcomm.center();
        popupwin_testtaxcomm.setModal(true);

        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");



        var obj_room = lookupTaxCommiRoomObj(room_id);

        var arr_buying = obj_room.buying_settings;
        var arr_selling = obj_room.selling_settings;

        var arr_currency_buy = selected_currency_buy_ids.split(",");
        var arr_currency_sell = selected_currency_sell_ids.split(",");

        var arr_currency_obj = intersectCurrencyArrays(arr_currency_buy, arr_currency_sell);
        arr_currency_buy = arr_currency_obj.buy;
        arr_currency_sell = arr_currency_obj.sell;



        grid_test_taxcomm.clearAll();
        grid_test_taxcomm = null;

        var header_str = "";
        var col_ids = "item_name";
        var col_types = "ro";
        var init_widths = "250";
        var col_align = "left";
        var col_sorting = "na";

        var cols_count = arr_currency_buy.length;
        if (arr_currency_sell.length > cols_count)
        {
            cols_count = arr_currency_sell.length;
        }

        for (var i = 0; i < cols_count; i++)
        {
            header_str += ",";
            col_ids += ",input_" + i;
            col_types += ",edn";
            init_widths += ",150";
            col_align += ",center";
            col_sorting += "na";
        }

        grid_test_taxcomm = test_taxcomm_layout.cells("a").attachGrid();
        grid_test_taxcomm.setIconsPath('libraries/dhtmlx/imgs/');
        grid_test_taxcomm.setHeader(header_str);
        grid_test_taxcomm.setColumnIds(col_ids);
        grid_test_taxcomm.setColTypes(col_types);
        grid_test_taxcomm.setInitWidths(init_widths);
        grid_test_taxcomm.setColAlign(col_align);
        grid_test_taxcomm.setColSorting(col_sorting);
        grid_test_taxcomm.enableAlterCss("", "");
        grid_test_taxcomm.enableColSpan(true);
        grid_test_taxcomm.enableRowspan(true);
        grid_test_taxcomm.attachEvent("onEditCell", onGridTestTaxCommEdit);
        grid_test_taxcomm.enableEditTabOnly(true);
        grid_test_taxcomm.enableEditEvents(true, true, true);
        grid_test_taxcomm.init();

        //======
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        var colspan_merge = arr_currency_sell.length;
        //======

        var arr_title = ["Buying Settings"];
        for (var j = 0; j < arr_currency_buy.length; j++)
        {
            var itemCurrency = _dsCurrencies.item(arr_currency_buy[j]);
            var currencycode = itemCurrency.value;
            arr_title.push(currencycode);
        }

        grid_test_taxcomm.addRow("buy_title", arr_title);
        grid_test_taxcomm.setRowTextStyle("buy_title", "background-color:#D2F6FB;font-weight:bold;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        grid_test_taxcomm.setColspan("buy_title", 1, colspan_merge);

        //=================================================

        var rwidx = 1;

        for (var i = 0; i < arr_buying.length; i++)
        {

            var item = arr_buying[i];
            if (item.setting_action != "DELETE")
            {
                if (item.setting_core_addon == "CORE")
                {
                    var rwid = "buy_" + item.setting_item_code + "_" + rwidx;
                    grid_test_taxcomm.addRow(rwid, [item.setting_item_name]);
                    grid_test_taxcomm.setColspan(rwid, 1, colspan_merge);
                    grid_test_taxcomm.setRowTextStyle(rwid, "color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


                } else if (item.setting_core_addon == "ADDON")
                {
                    var basis = item.setting_basis;
                    if (basis == "FLAT")
                    {
                        basis = "";
                    }

                    var setting_values = item.setting_values;

                    var rwid = "buy_" + item.setting_item_code + "_" + rwidx + "_r1";
                    var arr_vals = [item.setting_item_name];

                    for (var j = 0; j < arr_currency_buy.length; j++)
                    {
                        for (var k = 0; k < setting_values.length; k++)
                        {
                            if (arr_currency_buy[j] == setting_values[k].value_currency_fk)
                            {
                                arr_vals.push(setting_values[k].value_value + " " + basis);
                            }
                        }
                    }
                    grid_test_taxcomm.addRow(rwid, arr_vals);
                    grid_test_taxcomm.setRowTextStyle(rwid, "color:green;border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    grid_test_taxcomm.setColspan(rwid, 1, colspan_merge);

                    var rwid_2 = "buy_" + item.setting_item_code + "_" + rwidx + "_r2";
                    grid_test_taxcomm.addRow(rwid_2, [""]);
                    grid_test_taxcomm.setRowTextStyle(rwid_2, "color:green;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    grid_test_taxcomm.setColspan(rwid_2, 1, colspan_merge);
                    grid_test_taxcomm.setRowspan(rwid, 0, 2);
                }





                rwidx++;
            }
        }

        //===============================================================

        var arr_title = ["Selling Settings"];
        for (var j = 0; j < arr_currency_sell.length; j++)
        {
            var itemCurrency = _dsCurrencies.item(arr_currency_sell[j]);
            var currencycode = itemCurrency.value;
            arr_title.push(currencycode)
        }
        grid_test_taxcomm.addRow("sell_title", arr_title);
        grid_test_taxcomm.setRowTextStyle("sell_title", "background-color:#D2F6FB;font-weight:bold;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:2px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        for (var i = 0; i < arr_selling.length; i++)
        {
            var item = arr_selling[i];
            if (item.setting_action != "DELETE")
            {
                if (item.setting_core_addon == "CORE")
                {
                    var rwid = "sel_" + item.setting_item_code + "_" + rwidx;
                    grid_test_taxcomm.addRow(rwid, [item.setting_item_name]);

                    if (item.setting_item_code == "FINALSP")
                    {
                        grid_test_taxcomm.setRowTextStyle(rwid, "background-color:#FBF9D2; font-weight:bold; color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    } else
                    {
                        grid_test_taxcomm.setRowTextStyle(rwid, "color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    }



                } else if (item.setting_core_addon == "ADDON")
                {
                    var basis = item.setting_basis;
                    if (basis == "FLAT")
                    {
                        basis = "";
                    }

                    var setting_values = item.setting_values;

                    var rwid = "sel_" + item.setting_item_code + "_" + rwidx + "_r1";
                    var arr_vals = [item.setting_item_name];

                    for (var j = 0; j < arr_currency_sell.length; j++)
                    {
                        for (var k = 0; k < setting_values.length; k++)
                        {
                            if (arr_currency_sell[j] == setting_values[k].value_currency_fk)
                            {
                                arr_vals.push(setting_values[k].value_value + " " + basis);
                            }
                        }
                    }
                    grid_test_taxcomm.addRow(rwid, arr_vals);
                    grid_test_taxcomm.setRowTextStyle(rwid, "color:green;border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                    var rwid_2 = "sel_" + item.setting_item_code + "_" + rwidx + "_r2";
                    grid_test_taxcomm.addRow(rwid_2, []);
                    grid_test_taxcomm.setRowTextStyle(rwid_2, "color:green;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


                    grid_test_taxcomm.setRowspan(rwid, 0, 2);
                }

                rwidx++;
            }
        }

        return;
    }

    function placeTestGridValues(arr_arguements)
    {
        var sellcurrencyid = arr_arguements[2];
        var obj_calc = arr_arguements[3];

        //get the colindex for the currency
        var colidx = 0;
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            if (arr_currency_sell[i] == sellcurrencyid)
            {
                colidx = i + 1;
            }
        }


        var rwid = "";

        for (var i = 0; i < obj_calc.length; i++)
        {
            var itemcode = obj_calc[i].setting_item_code;
            var value = obj_calc[i].my_calculated_value;
            var addon_core = obj_calc[i].setting_core_addon;
            var buying_selling = obj_calc[i].setting_buying_selling.substring(0, 3).toLowerCase();

            if (addon_core == "CORE")
            {
                rwid = buying_selling + "_" + itemcode + "_" + (i + 1);
            } else
            {
                rwid = buying_selling + "_" + itemcode + "_" + (i + 1) + "_r2";
            }


            grid_test_taxcomm.cells(rwid, colidx).setValue(value);
        }
    }

    function deleteTaxCommiException()
    {
        var nodeid = tree_taxcomm.getSelectedItemId();
        var node = tree_taxcomm.getUserData(nodeid, "ROOM_EXCEPTION");
        if (node == "EXCEPTION")
        {
            var room_id = tree_taxcomm.getUserData(nodeid, "ROOMID");
            var obj_room = lookupTaxCommiRoomObj(room_id);
            obj_room.room_hasexception = "NO";
            obj_room.room_action = "UPDATE";
            obj_room.buying_settings = [];
            obj_room.selling_settings = [];
            tree_taxcomm.deleteItem(nodeid, true);
        }
    }


    function updateJsonTaxCommi(roomid, nValue, context, currencyid, itemrwid, buysel)
    {
        var itemobj = lookupTaxCommiRoomBuySellItem(roomid, buysel, itemrwid);
        if (!itemobj)
        {
            return false;
        }

        itemobj.setting_action = "UPDATE";

        if (context != "setting_values")
        {
            itemobj[context] = nValue;
        } else
        {
            var itemCurrency = _dsCurrencies.item(currencyid);
            var currencycode = itemCurrency.value;

            //update or insert currency node
            var arrvalues = itemobj.setting_values;
            for (var i = 0; i < arrvalues.length; i++)
            {
                if (arrvalues[i].value_currency_fk == currencyid)
                {
                    arrvalues[i].value_value = nValue;
                    arrvalues[i].value_action = "UPDATE";
                    return true;
                }
            }

            //if we are here, means that currency node has not been created
            _taxcommi_settings_value_id--;
            var obj = {
                value_rwid: _taxcommi_settings_value_id,
                value_currency_fk: currencyid,
                value_value: nValue,
                value_currency_code: currencycode,
                value_action: "INSERT"};

            arrvalues.push(obj);
        }

        return true;
    }


    function initialiseSingleParentPolicyGrid()
    {
        grid_singleparentpolicy_age = null;
        grid_singleparentpolicy_age = singleparent_layout.cells("a").attachGrid();
    }

    function initialiseChildPolicySharingGrid()
    {
        grid_childpolicy_sharing_age = null;
        grid_childpolicy_sharing_age = accord_child.cells("sharing").attachGrid();
        grid_childpolicy_sharing_age.setIconsPath('libraries/dhtmlx/imgs/');
    }

    function initialiseChildPolicySingleGrid()
    {
        grid_childpolicy_single_age = null;
        grid_childpolicy_single_age = accord_child.cells("single").attachGrid();
        grid_childpolicy_single_age.setIconsPath('libraries/dhtmlx/imgs/');
    }

    function initialiseAdultPolicyGrid()
    {
        grid_adultpolicy_age = adult_layout.cells("a").attachGrid();
        grid_adultpolicy_age.setIconsPath('libraries/dhtmlx/imgs/');
    }

    function fillSingleParentPolicyGridValues(roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        grid_singleparentpolicy_age.forEachRow(function (rwid) {
            grid_singleparentpolicy_age.forEachCell(rwid, function (c) {

                var child_index = c.getAttribute("child_index");
                var context = c.getAttribute("context");
                var adult_child = c.getAttribute("adult_child");
                var rule_ageranges = c.getAttribute("rule_ageranges");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");

                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {
                    var ruleobj = lookupSingleParentChildPoliciesDateRuleObject(capacity_date_obj, child_index, rule_ageranges);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupSingleParentChildPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto, adult_child);

                        if (policyrulecell)
                        {
                            var valuecell = lookupSingleParentChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateSingleParentPolicySalesPrice(rwid, c.cell.cellIndex, valuecell.value_value, currencyid);
                                }
                            }
                        }
                    }
                }
            });
        });
    }

    function fillChildPolicyGridValues(roomid, date_rwid, grid, sg_shr)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        grid.forEachRow(function (rwid) {
            grid.forEachCell(rwid, function (c) {

                var number = c.getAttribute("number");
                var context = c.getAttribute("context");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");

                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {
                    //context = category,basis,value
                    //category = 1,2,3 ... n

                    var ruleobj = lookupChildPoliciesDateRuleObject(capacity_date_obj, number, sg_shr);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupChildPoliciesDateRuleCellObject(ruleobj, context, number, agefrom, ageto);

                        if (policyrulecell)
                        {
                            var valuecell = lookupChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateChildPolicySalesPrice(grid, rwid,
                                            c.cell.cellIndex,
                                            valuecell.value_value,
                                            currencyid);
                                }
                            }
                        }
                    }
                }
            });
        });
    }



    function fillAdultPolicyGridValues(roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }


        grid_adultpolicy_age.forEachRow(function (rwid) {
            grid_adultpolicy_age.forEachCell(rwid, function (c) {

                var context = c.getAttribute("context");
                var variant = c.getAttribute("variant");
                var category = c.getAttribute("category");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");


                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {

                    //context = category,basis,value
                    //category = 1,2,3 ... n
                    var ruleobj = lookupAdultPoliciesDateRuleObject(capacity_date_obj, category);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupAdultPoliciesDateRuleCellObject(ruleobj, category, context, agefrom, ageto);

                        if (policyrulecell)
                        {
                            var valuecell = lookupAdultPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateAdultPolicySalesPrice(rwid, c.cell.cellIndex, valuecell.value_value, currencyid);
                                }
                            }
                        }
                    }
                }
            });
        });

        return;
    }


    function calculateSingleParentPolicySalesPrice(rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        //if room has exception taxcommi setting, then use this id
        //otherwise use GENERAL taxcommi setting

        var room_obj = lookupTaxCommiRoomObj(roomid);
        if (!room_obj)
        {
            roomid = "GENERAL"; //fallback to general 
        } else if (room_obj.room_hasexception == "NO")
        {
            roomid = "GENERAL"; //fallback to general 
        }

        //call the function for each selling currency
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateSellingPrice(roomid, newvalue, currencyinputid, sell_currency_id, callbackDisplaySingleParentPolicySalesPrice, [rwid, cindx, sell_currency_id]);
        }
    }

    function calculateChildPolicySalesPrice(grid, rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        //if room has exception taxcommi setting, then use this id
        //otherwise use GENERAL taxcommi setting

        var room_obj = lookupTaxCommiRoomObj(roomid);
        if (!room_obj)
        {
            roomid = "GENERAL"; //fallback to general 
        } else if (room_obj.room_hasexception == "NO")
        {
            roomid = "GENERAL"; //fallback to general 
        }

        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateSellingPrice(roomid, newvalue, currencyinputid, sell_currency_id, callbackDisplayChildPolicySalesPrice, [grid, rwid, cindx]);
        }
    }

    function calculateAdultPolicySalesPrice(rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        //if room has exception taxcommi setting, then use this id
        //otherwise use GENERAL taxcommi setting

        var room_obj = lookupTaxCommiRoomObj(roomid);
        if (!room_obj)
        {
            roomid = "GENERAL"; //fallback to general 
        } else if (room_obj.room_hasexception == "NO")
        {
            roomid = "GENERAL"; //fallback to general 
        }

        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateSellingPrice(roomid, newvalue, currencyinputid, sell_currency_id, callbackDisplayAdultPolicySalesPrice, [rwid, cindx]);
        }
    }


    function callbackDisplaySingleParentPolicySalesPrice(arr_arguements)
    {
        //[rwid, cindx, sell_currency_id, calc_obj]

        var taxcommi_item = getObjCalcItemCode(arr_arguements[3], "SELLING", "FINALSP");

        //extract the final sp value
        if (!taxcommi_item)
        {
            return;
        }

        var finalsp = taxcommi_item.my_calculated_value;
        var sp_currencyid = taxcommi_item.my_calculated_value_currencyid;

        //place the value in the grid
        var rwid = arr_arguements[0];
        var cellidx = arr_arguements[1];

        var cellObj = grid_singleparentpolicy_age.cells(rwid, cellidx);

        var context = cellObj.getAttribute("context");
        var agefrom = cellObj.getAttribute("agefrom");
        var ageto = cellObj.getAttribute("ageto");
        var child_index = cellObj.getAttribute("child_index");
        var rule_ageranges = cellObj.getAttribute("rule_ageranges");
        var adult_child = cellObj.getAttribute("adult_child");


        grid_singleparentpolicy_age.forEachCell(rwid, function (c) {

            if (c.getAttribute("context") == context &&
                    c.getAttribute("currencyid") == sp_currencyid &&
                    c.getAttribute("agefrom") == agefrom &&
                    c.getAttribute("ageto") == ageto &&
                    c.getAttribute("child_index") == child_index &&
                    c.getAttribute("rule_ageranges") == rule_ageranges &&
                    c.getAttribute("adult_child") == adult_child &&
                    c.getAttribute("buy_sell") == "sell")
            {
                c.setValue(finalsp);

                //if change is made in adult, then copy paste the value to all other rows
                //with same rule_index - just for aesthetic
                //var colid = grid_singleparentpolicy_age.getColumnId(c.cell.cellIndex);
                //if (colid.indexOf("adult_") != -1)
                //{
                //duplicateSingleParentAdultValuesDisplay(rwid, colid, finalsp, null, null);
                //}

            }
        });

    }

    function callbackDisplayChildPolicySalesPrice(arr_arguements)
    {
        //[grid, rwid, cindx, calc_obj]

        var taxcommi_item = getObjCalcItemCode(arr_arguements[3], "SELLING", "FINALSP");

        //extract the final sp value
        if (!taxcommi_item)
        {
            return;
        }

        var finalsp = taxcommi_item.my_calculated_value;
        var sp_currencyid = taxcommi_item.my_calculated_value_currencyid;


        //place the value in the grid
        var grid = arr_arguements[0];
        var rwid = arr_arguements[1];
        var cellidx = arr_arguements[2];

        var cellObj = grid.cells(rwid, cellidx);

        var context = cellObj.getAttribute("context");
        var agefrom = cellObj.getAttribute("agefrom");
        var ageto = cellObj.getAttribute("ageto");
        var number = cellObj.getAttribute("number");
        var max_count = cellObj.getAttribute("max_count");
        var min_count = cellObj.getAttribute("min_count");

        grid.forEachCell(rwid, function (c) {

            if (c.getAttribute("context") == context &&
                    c.getAttribute("currencyid") == sp_currencyid &&
                    c.getAttribute("agefrom") == agefrom &&
                    c.getAttribute("ageto") == ageto &&
                    c.getAttribute("number") == number &&
                    c.getAttribute("max_count") == max_count &&
                    c.getAttribute("min_count") == min_count &&
                    c.getAttribute("buy_sell") == "sell")
            {
                c.setValue(finalsp);
            }
        });

    }

    function callbackDisplayAdultPolicySalesPrice(arr_arguements)
    {
        var taxcommi_item = getObjCalcItemCode(arr_arguements[2], "SELLING", "FINALSP");

        //extract the final sp value
        if (!taxcommi_item)
        {
            return;
        }

        var finalsp = taxcommi_item.my_calculated_value;
        var sp_currencyid = taxcommi_item.my_calculated_value_currencyid;

        //place the value in the grid
        var rwid = arr_arguements[0];
        var cellidx = arr_arguements[1];

        var cellObj = grid_adultpolicy_age.cells(rwid, cellidx);

        var context = cellObj.getAttribute("context");
        var category = cellObj.getAttribute("category");
        var agefrom = cellObj.getAttribute("agefrom");
        var ageto = cellObj.getAttribute("ageto");

        grid_adultpolicy_age.forEachCell(rwid, function (c) {

            if (c.getAttribute("context") == context &&
                    c.getAttribute("category") == category &&
                    c.getAttribute("currencyid") == sp_currencyid &&
                    c.getAttribute("agefrom") == agefrom &&
                    c.getAttribute("ageto") == ageto &&
                    c.getAttribute("buy_sell") == "sell")
            {
                c.setValue(finalsp);
            }
        });

    }

    function getObjCalcItemCode(objcalc, buying_selling, itemcode)
    {
        for (var i = 0; i < objcalc.length; i++)
        {
            if (objcalc[i].setting_item_code == itemcode &&
                    objcalc[i].setting_buying_selling == buying_selling)
            {
                return objcalc[i];
            }
        }

        return null
    }


    function fillCurrencyMappingGrid()
    {
        grid_currencymap.clearAll();
        var cbo = grid_currencymap.getColumnCombo(grid_currencymap.getColIndexById("currency_buy"));
        cbo.clearAll();
        cbo.readonly(true);

        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var arr_currency_buy = selected_currency_buy_ids.split(",");
        for (var i = 0; i < arr_currency_buy.length; i++)
        {
            var currbuyid = arr_currency_buy[i];
            if (currbuyid != "")
            {
                var item = _dsCurrencies.item(currbuyid);
                var currenybuy_code = item.value;
                //grid_currencymap.getCombo(grid_currencymap.getColIndexById("currency_buy")).put(currbuyid, currenybuy_code);
                cbo.addOption([{value: currbuyid, text: currenybuy_code}]);
            }
        }

        //===================================================================

        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            var id = _json_exchangerates.currency_mapping[i].mapping_id;
            var mapping_buy_currencyfk = _json_exchangerates.currency_mapping[i].mapping_buy_currencyfk;
            var mapping_sell_currencyfk = _json_exchangerates.currency_mapping[i].mapping_sell_currencyfk;
            var mapping_action = _json_exchangerates.currency_mapping[i].mapping_action;


            if (mapping_action != "DELETE")
            {
                var item = _dsCurrencies.item(mapping_sell_currencyfk);
                var currenysell_code = item.value;

                grid_currencymap.addRow(id, [currenysell_code, mapping_buy_currencyfk]);
                grid_currencymap.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }
    }

    function fillExchangeRatesGrid()
    {
        grid_exchrates.clearAll();


        for (var i = 0; i < _json_exchangerates.exchange_rates.length; i++)
        {
            var id = _json_exchangerates.exchange_rates[i].rates_id;
            var currenyfromfk = _json_exchangerates.exchange_rates[i].rates_from_currencyfk;
            var currenytofk = _json_exchangerates.exchange_rates[i].rates_to_currencyfk;
            var exchangerate = _json_exchangerates.exchange_rates[i].rates_exchange_rate;
            var rates_action = _json_exchangerates.exchange_rates[i].rates_action;

            if (rates_action != "DELETE")
            {
                var item = _dsCurrencies.item(currenyfromfk);
                var currenyfrom = item.value;

                item = _dsCurrencies.item(currenytofk);
                var currenyto = item.value;

                grid_exchrates.addRow(id, [currenyfrom, currenyto, exchangerate]);
                grid_exchrates.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }

        grid_exchrates.sortRows(1, "str", "asc");
        grid_exchrates.sortRows(0, "str", "asc");
    }

    function validate_roll_over()
    {
        var rolloverbasis = form_main.getItemValue("rollover_basis");
        var rollovervalue = utils_trim(form_main.getItemValue("rollover_value"), " ");

        if (isNaN(rollovervalue) || rollovervalue == "")
        {
            return "<b>Rollover:</b> Missing Value";
        }

        if (rolloverbasis == "percentage")
        {
            rollovervalue = parseInt(rollovervalue, 10);
            if (rollovervalue < 0 || rollovervalue > 100)
            {
                return "<b>Rollover:</b>Percentage Value invalid";
            }
        }

        return "";
    }

    function validate_children_ages()
    {
        var child_ages_ids = form_main.getItemValue("children_ages_ids");
        var arr_ids = child_ages_ids.split(",");

        var arr_ages = [];

        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = parseInt(item.agefrom, 10);
                var ageto = parseInt(item.ageto, 10);
                arr_ages.push({id: id, agefrom: agefrom, ageto: ageto});
            }
        }

        //now check if there are overlapping ages in arr_ages
        for (var i = 0; i < arr_ages.length; i++)
        {
            var agfrom = arr_ages[i].agefrom;
            var agto = arr_ages[i].ageto;
            var id = arr_ages[i].id;

            //now check this item against the rest
            for (var j = 0; j < arr_ages.length; j++)
            {
                var agfrom_inner = arr_ages[j].agefrom;
                var agto_inner = arr_ages[j].ageto;
                var id_inner = arr_ages[j].id;

                if (id != id_inner)
                {
                    if (agfrom <= agto_inner && agfrom_inner <= agto)
                    {
                        return "<b>Children Ages:</b> Overlapping ages detected!";
                    }
                }
            }

        }

        return "";
    }

    function updateChildAges()
    {
        var err = validate_children_ages();
        if (err != "")
        {
            dhtmlx.alert({
                text: err,
                type: "alert-warning",
                title: "Validate Children Ages",
                callback: function () {

                    //revert back
                    form_main.setItemValue("children_ages_display", _last_popup_display_values);
                    form_main.setItemValue("children_ages_ids", _last_popup_ids);

                    grid_choices.selectRowById("main", false, true, true);
                    showPopUp(form_main, "Children Ages", "children_ages_display", "children_ages_ids", _dsChildPolicy, "MULTIPLE", updateChildAges);
                }
            });
            return;
        }

        var newids = utils_trim(form_main.getItemValue("children_ages_ids"), " ");
        if (newids != _last_popup_ids)
        {
            //there has been a change in children ages: 
            //this will delete all rates with children ages
            //confirm with user:

            dhtmlx.confirm({
                title: "Reset Rates",
                type: "confirm",
                text: "Change detected children age policy. <br>Rates with children ages will be <b>RESET</b>.<BR>Proceed?",
                callback: function (tf) {
                    if (tf)
                    {
                        cleanJsonCapacityFromRoomsAndAges();
                        cleanJsonChildren("sharing");
                        cleanJsonChildren("single");

                        //============ DRASTIC! ================
                        for (var i = 0; i < _json_capacity.length; i++)
                        {
                            if (_json_capacity[i].room_action != "DELETE")
                            {
                                var room_dates = _json_capacity[i].room_dates;

                                for (var d = 0; d < room_dates.length; d++)
                                {
                                    var date_action = room_dates[d].date_action;
                                    var date_single_rules = room_dates[d].date_singleparentpolicies_rules;

                                    if (date_action != "DELETE")
                                    {
                                        for (var ad = 0; ad < date_single_rules.length; ad++)
                                        {
                                            date_single_rules[ad].rule_action = "DELETE";

                                        }
                                    }
                                }

                            }
                        }
                    } else
                    {
                        //revert back
                        form_main.setItemValue("children_ages_display", _last_popup_display_values);
                        form_main.setItemValue("children_ages_ids", _last_popup_ids);
                    }
                }});
        }


    }


    function updateRooms()
    {
        var roomids = form_main.getItemValue("rooms_ids");
        var arrroomids = roomids.split(",");


        //===================== TAX COMMISSION =======================================
        for (var i = 0; i < _json_taxcommi.length; i++)
        {
            var rmid = _json_taxcommi[i].room_id;
            if (rmid != "GENERAL")
            {
                if (arrroomids.indexOf(rmid) == -1)
                {
                    //delete room setting if created
                    for (var j = 0; j < _json_taxcommi[i].room_exception.length; j++)
                    {
                        //indeed exception created
                        _json_taxcommi[i].room_exception[j].setting_action = "DELETE";
                    }

                }
            }
        }

        //===================== CAPACITY ==============================================

        for (var i = 0; i < _json_capacity.length; i++)
        {
            var rmid = _json_capacity[i].room_id;
            if (arrroomids.indexOf(rmid) == -1)
            {
                _json_capacity[i].room_action = "DELETE";
            } else
            {
                _json_capacity[i].room_action = "INSERT";
            }
        }


        return;
    }


    function updateExchangeRatesJson()
    {

        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();
        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");

        if (costprice_currencyid == "" || !costprice_currencyid || costprice_currencyid == "-1")
        {
            return;
        }

        //================= exchange_rates first ===============================
        for (var i = 0; i < _json_exchangerates.exchange_rates.length; i++)
        {
            _json_exchangerates.exchange_rates[i].rates_action = ""; // clear all settings
        }

        var arr_selected_buyids = selected_currency_buy_ids.split(",");
        var arr_selected_sellids = selected_currency_sell_ids.split(",");
        var currencyid = -1;


        for (var i = 0; i < arr_selected_buyids.length; i++)
        {
            if (arr_selected_buyids[i] != "")
            {
                currencyid = arr_selected_buyids[i];

                if (costprice_currencyid != currencyid)
                {
                    //check if combii costprice_currencyid --> currencyid exists
                    //if not, then insert
                    var exhobj = getCurrencyExgRateFromJson(costprice_currencyid, currencyid);
                    if (!exhobj)
                    {
                        _exchange_rate_id--;
                        var obj = {rates_from_currencyfk: costprice_currencyid,
                            rates_to_currencyfk: currencyid,
                            rates_id: _exchange_rate_id,
                            rates_exchange_rate: "0.0000",
                            rates_action: "INSERT"};
                        _json_exchangerates.exchange_rates.push(obj);
                    } else
                    {
                        exhobj.rates_action = "UPDATE";
                    }
                }
            }
        }

        for (var i = 0; i < arr_selected_sellids.length; i++)
        {
            if (arr_selected_sellids[i] != "")
            {
                currencyid = arr_selected_sellids[i];

                if (costprice_currencyid != currencyid)
                {
                    //check if combii costprice_currencyid->currencyid exists
                    //if not, then insert
                    var exhobj = getCurrencyExgRateFromJson(costprice_currencyid, currencyid);
                    if (!exhobj)
                    {
                        _exchange_rate_id--;
                        var obj = {rates_from_currencyfk: costprice_currencyid,
                            rates_to_currencyfk: currencyid,
                            rates_id: _exchange_rate_id,
                            rates_exchange_rate: "0.0000",
                            rates_action: "INSERT"};
                        _json_exchangerates.exchange_rates.push(obj);
                    } else
                    {
                        exhobj.rates_action = "UPDATE";
                    }
                }
            }
        }

        //=========
        //delete the rest
        for (var i = 0; i < _json_exchangerates.exchange_rates.length; i++)
        {
            if (_json_exchangerates.exchange_rates[i].rates_action == "")
            {
                _json_exchangerates.exchange_rates[i].rates_action = "DELETE";
            }
        }

        //======================================================================
        //================= currency_mapping after =============================
        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            _json_exchangerates.currency_mapping[i].mapping_action = ""; // clear all settings
        }


        for (var i = 0; i < arr_selected_sellids.length; i++)
        {
            if (arr_selected_sellids[i] != "")
            {
                currencyid = arr_selected_sellids[i];

                //check if currency sell exists in currency_mapping
                //if not, then insert
                var mapobj = getCurrencyMappingFromJson(currencyid);
                if (!mapobj)
                {
                    _currency_mapping_id--;
                    var obj = {mapping_buy_currencyfk: getMappingSimilarCurrencyBuy(currencyid),
                        mapping_sell_currencyfk: currencyid,
                        mapping_id: _currency_mapping_id,
                        mapping_action: "INSERT"};
                    _json_exchangerates.currency_mapping.push(obj);
                } else
                {
                    mapobj.mapping_action = "UPDATE";
                    //check if the currency buy to which it is mapped is in array arr_selected_buyids

                    if (arr_selected_buyids.indexOf(mapobj.mapping_buy_currencyfk) == -1)
                    {
                        //currency buy has been removed from the initial list
                        mapobj.mapping_buy_currencyfk = ""; //so clear it
                        updateCurrMapJson(mapobj.mapping_id, "");
                    }
                }
            }
        }


        //=========
        //delete the rest
        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            if (_json_exchangerates.currency_mapping[i].mapping_action == "")
            {
                _json_exchangerates.currency_mapping[i].mapping_action = "DELETE";
            }
        }


        fillExchangeRatesGrid();
        fillCurrencyMappingGrid();

        return;
    }


    function getMappingSimilarCurrencyBuy(currencysellid)
    {
        //check if there is a similar currency buy in list of buying currencies
        //if yes, then return itself otherwise, return ""
        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
        var arr_selected_buyids = selected_currency_buy_ids.split(",");
        if (arr_selected_buyids.indexOf(currencysellid) != -1)
        {
            return currencysellid;
        }

        return "";
    }

    function getCurrencyExgRateFromJson(currencyfrom, currencyto)
    {
        for (var i = 0; i < _json_exchangerates.exchange_rates.length; i++)
        {
            if (_json_exchangerates.exchange_rates[i].rates_from_currencyfk == currencyfrom &&
                    _json_exchangerates.exchange_rates[i].rates_to_currencyfk == currencyto)
            {
                return _json_exchangerates.exchange_rates[i];
            }
        }

        return null;
    }

    function getCurrencyMappingFromJson(currencysell)
    {
        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            if (_json_exchangerates.currency_mapping[i].mapping_sell_currencyfk == currencysell)
            {
                return _json_exchangerates.currency_mapping[i];
            }
        }

        return null;
    }

    function updateCurrMapJson(rId, nValue)
    {
        for (var i = 0; i < _json_exchangerates.currency_mapping.length; i++)
        {
            if (_json_exchangerates.currency_mapping[i].mapping_id == rId)
            {
                _json_exchangerates.currency_mapping[i]["mapping_buy_currencyfk"] = nValue;
                _json_exchangerates.currency_mapping[i]["mapping_action"] = "UPDATE";
                return;
            }
        }
    }

    function updateExgRateJson(rId, nValue, colid)
    {
        for (var i = 0; i < _json_exchangerates.exchange_rates.length; i++)
        {
            if (_json_exchangerates.exchange_rates[i].rates_id == rId)
            {
                _json_exchangerates.exchange_rates[i][colid] = nValue;
                _json_exchangerates.exchange_rates[i]["rates_action"] = "UPDATE";
                return;
            }
        }
    }

    function onGridCurrMapEdit(stage, rId, cInd, nValue, oValue)
    {
        if (stage == 2)
        {
            if (nValue != oValue)
            {
                updateCurrMapJson(rId, nValue);
            }
        }

        return true;
    }

    function onGridExgRateEdit(stage, rId, cInd, nValue, oValue)
    {
        var colid = grid_exchrates.getColumnId(cInd);

        //rates_exchange_rate

        if (stage == 1)
        {
            if (grid_exchrates.editor && grid_exchrates.editor.obj)
            {
                grid_exchrates.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                if (colid == "rates_exchange_rate")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }

                    if (parseInt(nValue, 10) < 0)
                    {
                        return false;
                    }
                }

                updateExgRateJson(rId, nValue, colid);
            }
        }
        return true;
    }

    function displayCombinations(json_combii)
    {
        var combii = "<div style='width:100%;height:100%;overflow:auto;'>" +
                "<style>p.big {line-height: 2;}</style><p class='big'>";

        var hotelname = dsHotel.item(global_hotel_id).hotelname;
        var roomname = json_combii.room_name;
        var variant = json_combii.room_variants;

        popupwin_capacitycombinations.setText("COMBINATIONS - " + hotelname);

        toolbarCombi.setItemText("text", "<b>COMBINATIONS FOR " + roomname + " (" + variant + ")</b>");

        for (var j = 0; j < json_combii.room_combinations.length; j++)
        {
            var dtfrom = json_combii.room_combinations[j].dtfrom;
            var dtto = json_combii.room_combinations[j].dtto;

            combii += "<b>DATE: " + decideNodeDateCaption(dtfrom, dtto) + "</b><br>";

            var combinations_array = json_combii.room_combinations[j].combinations_array;


            var myarr = [];
            var arr_keys = Object.keys(combinations_array);
            for (var k = 0; k < arr_keys.length; k++)
            {
                var key = arr_keys[k];
                myarr.push(combinations_array[key]);
            }


            var rowsnum = myarr.length;
            var lhs = 0;
            var rhs = 0;

            //split into 2
            if (rowsnum % 2 == 0)
            {
                lhs = parseInt((rowsnum / 2), 10);
                rhs = rowsnum - lhs;
            } else
            {
                lhs = parseInt((rowsnum / 2), 10);
                rhs = rowsnum - lhs - 1;
            }

            combii += "<table width='100%'><tr><td width='50%'>" +
                    displayCombinationsColumn(myarr, 0, lhs) +
                    "</td><td width='50%'>" +
                    displayCombinationsColumn(myarr, rhs, rowsnum) +
                    "</td></tr></table>";

            combii += "<hr>";
            //end dates
        }

        combii += "</p></div>";

        layout_capacitycombii.cells("a").attachHTMLString(combii);
    }

    function displayCombinationsColumn(combinations_array, from, to)
    {
        var aico = "<img src='images/adult_24.png'  />";
        var cico = "<img src='images/child_24.png' height='20px' />";

        var combii = "<p class='big'>";

        for (var k = from; k < to; k++)
        {
            //start row 

            var arr_combii_nodes = combinations_array[k];
            var first = true;

            for (var l = 0; l < arr_combii_nodes.length; l++)
            {
                var agefrom = arr_combii_nodes[l].AGEFROM;
                var ageto = arr_combii_nodes[l].AGETO;
                var no = arr_combii_nodes[l].No;


                if (no > 0)
                {
                    if (agefrom == -1 && ageto == -1)
                    {
                        //adult
                        while (no > 0)
                        {
                            combii += aico;
                            no--;
                        }

                        first = false;
                    } else
                    {
                        //children

                        while (no > 0)
                        {
                            if (!first)
                            {
                                combii += "  +  ";
                            }

                            combii += "(";
                            combii += cico;
                            combii += " " + agefrom + " - " + ageto;
                            combii += ")";

                            no--;

                            first = false;
                        }
                    }
                }

            }

            combii += "<br>";
            //end row 
        }

        combii += "</p>";

        return combii;
    }



    function saveContract()
    {
        try
        {
            //===========================================
            //validations first to come
            var maintabid = tabViews.getActiveTab();
            var subtabid = "";
            if (maintabid == "rooms")
            {
                subtabid = tabRoomViews.getActiveTab();
            }

            //===========================================

            var arr_validations = ["main_details", "main_contractdates", "main_rollover",
                "main_childrenages",
                "currency_details", "currency_mapping", "tax", "rooms_dates",
                "rooms_minstay", "rooms_capacity", "rooms_policies",
                "rooms_meal", "rooms_adult", "rooms_child", "rooms_singleparent"];


            resetError();
            validateContract(arr_validations);

            if (!parseErrors())
            {
                dhtmlx.alert({
                    text: "<b>Errors</b> detected in Contract. Please address them before Saving...",
                    type: "alert-warning",
                    title: "SAVE",
                    callback: function () {
                        grid_choices.selectRowById(maintabid, false, true, true);
                        if (subtabid != "")
                        {
                            grid_room_choices.selectRowById(subtabid, false, true, true);
                        }
                    }
                });

                return;
            } else
            {
                grid_choices.selectRowById("main", false, true, true);
            }

            //=========================================================================
            //clean up unnecessary rooms and children ages from
            //1. meal supplement
            //2. extra supplement
            //3. sharing and single children

            cleanJsonCapacityFromRoomsAndAges();

            //==========================================================================
            //
            cleanJsonAdults();
            cleanJsonChildren("sharing");
            cleanJsonChildren("single");
            cleanJsonSingleParent();

            //then save
            saveContractCore();

        } catch (err) {
            console.log(err.message);
            dhtmlx.alert({
                text: err.message,
                type: "alert-warning",
                title: "Save Contract",
                callback: function () {
                }
            });
        }


    }

    function cleanJsonSingleParent()
    {
        //get array group by rule_ageranges
        var arr_ruleranges = getSingleParentRuleRanges();


        //now for each ruleranges, assess if they are outside or within scope
        for (var i = 0; i < arr_ruleranges.length; i++)
        {

            cleanSingleParentRuleRange(arr_ruleranges[i].rule_ageranges,
                    arr_ruleranges[i].room_id,
                    arr_ruleranges[i].date_rwid);

            decideDeleteSingleParentRuleRange(arr_ruleranges[i].rule_ageranges);
        }
    }

    function decideDeleteSingleParentRuleRange(rule_ageranges)
    {
        var arr_rules = getSingleParentRulesByRuleRange(rule_ageranges);

        for (var i = 0; i < arr_rules.length; i++)
        {
            var flg_delete = true;

            var arr_rule_policy = arr_rules[i].rule_policy;
            for (var j = 0; j < arr_rule_policy.length; j++)
            {
                var policy_adult_child = arr_rule_policy[j].policy_adult_child;
                var agfrom = arr_rule_policy[j].policy_child_agefrom;
                var agto = arr_rule_policy[j].policy_child_ageto;
                var policy_action = arr_rule_policy[j].policy_action;

                if (policy_adult_child == "CHILD" && policy_action != "DELETE")
                {
                    var arrvalues = arr_rule_policy[j].policy_values;
                    for (var k = 0; k < arrvalues.length; k++)
                    {
                        var val = arrvalues[k].value_value;
                        val = utils_trim(val, " ");
                        val = utils_trim(val, String.fromCharCode(160));
                        if (val != "")
                        {
                            flg_delete = false;
                        }
                    }
                }
            }

            if (flg_delete)
            {
                arr_rules[i].rule_action = "DELETE";
            }
        }
    }

    function singleParentChildrenAgesInCategory(children_ages, rule_ageranges)
    {
        var copy_children_ages = utils_deepCopy(children_ages);


        //rule_ageranges example: ;0_1;2_3;
        //explode rule_ageranges and check if each of the ages are in children_ages

        var arr_age_ranges = rule_ageranges.split(";");
        for (var i = 0; i < arr_age_ranges.length; i++)
        {
            var age_value = arr_age_ranges[i];
            if (utils_trim(age_value, " ") != "")
            {
                var arr_age_from_to = age_value.split("_");
                var age_from = arr_age_from_to[0];
                var age_to = arr_age_from_to[1];
                var found = false;

                //now for this age range, search into copy_children_ages
                var j = copy_children_ages.length;

                while (j--) {

                    if (copy_children_ages[j].capacity_child_agefrom == age_from &&
                            copy_children_ages[j].capacity_child_ageto == age_to)
                    {
                        copy_children_ages.splice(j, 1);
                        found = true;
                    }
                }

                if (!found)
                {
                    return false;
                }
            }
        }

        //now check if array is empty
        if (copy_children_ages.length == 0)
        {
            return true;
        } else
        {
            return false;
        }
    }
    function cleanSingleParentRuleRange(rule_ageranges, roomid, date_rwid)
    {

        //get all rule lines for that _rulerange
        var arr_rules = getSingleParentRulesByRuleRange(rule_ageranges);


        var return_arr = singleParentGetChildRanges(roomid, date_rwid);
        var arr_result = return_arr.RESULT;
        //var arr_main_childages = return_arr.MAIN_CHILD_AGES;

        //for each result
        for (var r = 0; r < arr_result.length; r++)
        {
            if (singleParentChildrenAgesInCategory(arr_result[r].children_ages, rule_ageranges))
            {
                for (var i = 0; i < arr_result[r].children_ages.length; i++)
                {
                    var ag_from = arr_result[r].children_ages[i].capacity_child_agefrom;
                    var ag_to = arr_result[r].children_ages[i].capacity_child_ageto;
                    var max_pax = arr_result[r].children_ages[i].capacity_maxpax;

                    enforceMaxPaxChildrenSingleParent(arr_rules, ag_from, ag_to, max_pax);
                }
            }
        }

    }

    function enforceMaxPaxChildrenSingleParent(arr_rules, ag_from, ag_to, max_pax)
    {
        for (var i = 0; i < arr_rules.length; i++)
        {
            var rule_category = arr_rules[i].rule_category;
            var arr_rule_policy = arr_rules[i].rule_policy;
            for (var j = 0; j < arr_rule_policy.length; j++)
            {
                var policy_adult_child = arr_rule_policy[j].policy_adult_child;
                var agfrom = arr_rule_policy[j].policy_child_agefrom;
                var agto = arr_rule_policy[j].policy_child_ageto;

                if (policy_adult_child == "CHILD" && ag_from == agfrom && ag_to == agto)
                {
                    if (parseInt(rule_category, 10) > parseInt(max_pax, 10))
                    {
                        arr_rule_policy[j].policy_action = "DELETE";
                    }
                }
            }
        }
    }


    function getSingleParentRulesByRuleRange(rulerange)
    {
        var arr = [];

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE")
            {
                var room_id = _json_capacity[i].room_id;
                var room_variants = _json_capacity[i].room_variants;
                var room_dates = _json_capacity[i].room_dates;
                if (room_variants == "PERSONS")
                {
                    for (var d = 0; d < room_dates.length; d++)
                    {
                        var date_action = room_dates[d].date_action;
                        var date_dtfrom = room_dates[d].date_dtfrom;
                        var date_dtto = room_dates[d].date_dtto;

                        if (date_action != "DELETE")
                        {
                            var date_single_rules = room_dates[d].date_singleparentpolicies_rules;
                            for (var ad = 0; ad < date_single_rules.length; ad++)
                            {
                                if (date_single_rules[ad].rule_action != "DELETE")
                                {
                                    var rule_ageranges = date_single_rules[ad].rule_ageranges;
                                    if (rulerange == rule_ageranges)
                                    {
                                        arr.push(date_single_rules[ad]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return arr;
    }

    function getSingleParentRuleRanges()
    {
        var arr = [];

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE")
            {
                var room_id = _json_capacity[i].room_id;
                var room_variants = _json_capacity[i].room_variants;
                var room_dates = _json_capacity[i].room_dates;

                if (room_variants == "PERSONS")
                {
                    for (var d = 0; d < room_dates.length; d++)
                    {
                        var date_action = room_dates[d].date_action;
                        var date_rwid = room_dates[d].date_rwid;

                        if (date_action != "DELETE")
                        {
                            var date_single_rules = room_dates[d].date_singleparentpolicies_rules;
                            for (var ad = 0; ad < date_single_rules.length; ad++)
                            {
                                if (date_single_rules[ad].rule_action != "DELETE")
                                {
                                    var rule_ageranges = date_single_rules[ad].rule_ageranges;
                                    var pos = arr.map(function (e) {
                                        return e.rule_ageranges;
                                    }).indexOf(rule_ageranges);

                                    if (pos == -1)
                                    {
                                        arr.push({room_id: room_id, date_rwid: date_rwid, rule_ageranges: rule_ageranges});
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return arr;
    }

    function cleanJsonChildren(sharing_single)
    {

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE")
            {
                var room_id = _json_capacity[i].room_id;
                var room_variants = _json_capacity[i].room_variants;
                var room_dates = _json_capacity[i].room_dates;

                for (var d = 0; d < room_dates.length; d++)
                {
                    var date_action = room_dates[d].date_action;
                    var date_dtfrom = room_dates[d].date_dtfrom;
                    var date_dtto = room_dates[d].date_dtto;
                    var date_childpolicies_rules = room_dates[d].date_childpolicies_rules;

                    if (date_action != "DELETE")
                    {
                        if (room_variants == "PERSONS")
                        {
                            cleanJsonChildren_by_date(sharing_single, room_id, date_dtfrom, date_dtto, date_childpolicies_rules, room_variants);
                        } else if (room_variants == "UNITS" && sharing_single == "sharing")
                        {
                            cleanJsonChildren_by_date(sharing_single, room_id, date_dtfrom, date_dtto, date_childpolicies_rules, room_variants);
                        }
                    }
                }

            }
        }
    }

    function cleanJsonChildren_by_date(sharing_single, roomid, dtfrom, dtto, arr_rules, room_variants)
    {
        var child_ages_ids = form_main.getItemValue("children_ages_ids");

        //get count of children for each date first

        var arr_childages_count = [];
        var arr_ids = child_ages_ids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            var id = arr_ids[i];
            if (id != "")
            {
                var item = _dsChildPolicy.item(id);
                var agefrom = parseInt(item.agefrom, 10);
                var ageto = parseInt(item.ageto, 10);
                var child_stats = null;

                if (room_variants == "PERSONS")
                {
                    child_stats = getPersonsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto);
                } else if (room_variants == "UNITS")
                {
                    child_stats = getUnitsCapacityRoomChildrenStats(roomid, dtfrom, dtto, agefrom, ageto);
                }


                if (child_stats[sharing_single].max_child > 0)
                {
                    arr_childages_count.push(child_stats);
                }
            }
        }


        //now for each rule, check if the index by age is respected
        for (var i = 0; i < arr_rules.length; i++)
        {
            if (arr_rules[i].rule_action != "DELETE")
            {
                var rulecategory = arr_rules[i].rule_category;
                var rule_sharing_single = arr_rules[i].rule_sharing_single;

                if (rule_sharing_single == sharing_single.toUpperCase())
                {
                    var flg_delete = true;

                    var rulepolicy = arr_rules[i].rule_policy;
                    for (var j = 0; j < rulepolicy.length; j++)
                    {
                        if (rulepolicy[j].policy_action != "DELETE")
                        {
                            var policy_action = rulepolicy[j].policy_action;
                            var agfrom = rulepolicy[j].policy_units_additional_child_agefrom;
                            var agto = rulepolicy[j].policy_units_additional_child_ageto;

                            if (policy_action != "DELETE")
                            {
                                //get the max children count from arr_childages_count
                                //for that age from and to
                                //if the max_children_count < rulecategory then delete this
                                //policy

                                var max = getMaxChildCountFromArrChildAges(arr_childages_count, agfrom, agto, sharing_single);
                                if (max < rulecategory)
                                {
                                    rulepolicy[j].policy_action = "DELETE";
                                } else
                                {
                                    flg_delete = false;
                                }
                            }
                        }
                    }

                    if (flg_delete)
                    {
                        arr_rules[i].rule_action = "DELETE";
                    }
                }
            }
        }
    }

    function getMaxChildCountFromArrChildAges(arr_childages_count, agfrom, agto, sharing_single)
    {

        agfrom = parseInt(agfrom, 10);
        agto = parseInt(agto, 10);

        for (var i = 0; i < arr_childages_count.length; i++)
        {
            var age_from = arr_childages_count[i].age_from;
            var age_to = arr_childages_count[i].age_to;
            if (age_from == agfrom && age_to == agto)
            {
                return arr_childages_count[i][sharing_single].max_child;
            }
        }

        return 0;
    }

    function cleanJsonAdults()
    {
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE")
            {
                var room_id = _json_capacity[i].room_id;
                var room_variants = _json_capacity[i].room_variants;
                var room_dates = _json_capacity[i].room_dates;
                if (room_variants == "PERSONS")
                {
                    for (var d = 0; d < room_dates.length; d++)
                    {
                        var date_action = room_dates[d].date_action;
                        var date_dtfrom = room_dates[d].date_dtfrom;
                        var date_dtto = room_dates[d].date_dtto;

                        if (date_action != "DELETE")
                        {
                            var adult_max = getCapacityRoomMaxAdult(room_id, date_dtfrom, date_dtto, "ADULT");
                            var date_adultpolicies_rules = room_dates[d].date_adultpolicies_rules;
                            for (var ad = 0; ad < date_adultpolicies_rules.length; ad++)
                            {
                                var ruleaction = date_adultpolicies_rules[ad].rule_action;
                                var rule_category = parseInt(date_adultpolicies_rules[ad].rule_category, 10);

                                if (ruleaction != "DELETE" && rule_category > adult_max)
                                {
                                    date_adultpolicies_rules[ad].rule_action = "DELETE";
                                }
                            }
                        }
                    }
                } else if (room_variants == "UNITS")
                {
                    for (var d = 0; d < room_dates.length; d++)
                    {
                        var date_action = room_dates[d].date_action;
                        var date_dtfrom = room_dates[d].date_dtfrom;
                        var date_dtto = room_dates[d].date_dtto;

                        if (date_action != "DELETE")
                        {
                            var adult_max = getCapacityRoomMaxAdult(room_id, date_dtfrom, date_dtto, "ADDITIONALPERSONS");
                            var date_adultpolicies_rules = room_dates[d].date_adultpolicies_rules;
                            for (var ad = 0; ad < date_adultpolicies_rules.length; ad++)
                            {
                                var ruleaction = date_adultpolicies_rules[ad].rule_action;
                                var rule_category = parseInt(date_adultpolicies_rules[ad].rule_category, 10);

                                if (ruleaction != "DELETE" && rule_category > adult_max)
                                {
                                    date_adultpolicies_rules[ad].rule_action = "DELETE";
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function cleanJsonCapacityFromRoomsAndAges()
    {
        var roomids = form_main.getItemValue("rooms_ids");
        var arrroomids = roomids.split(",");

        //start with mealsupplements
        //check if there are date ranges that are not selected
        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE")
            {
                var room_id = _json_capacity[i].room_id;
                if (arrroomids.indexOf(room_id) == -1)
                {
                    //room is no longer selected
                    _json_capacity[i].room_action = "DELETE";
                } else
                {


                    var roomdates = _json_capacity[i].room_dates;
                    for (var d = 0; d < roomdates.length; d++)
                    {
                        if (roomdates[d].date_action != "DELETE")
                        {

                            cleanJsonCapacityCategoryAge(room_id, roomdates[d].date_rwid);

                            //=========== clean meal supplements from unnecessary ages =============
                            var mealsupp = roomdates[d].date_mealsupplement_rules;
                            for (var m = 0; m < mealsupp.length; m++)
                            {
                                if (mealsupp[m].meal_action != "DELETE")
                                {
                                    var mealchildren = mealsupp[m].meal_children;
                                    for (var mc = 0; mc < mealchildren.length; mc++)
                                    {
                                        if (mealchildren[mc].child_action != "DELETE")
                                        {
                                            var agfrom = mealchildren[mc].child_agefrom;
                                            var agto = mealchildren[mc].child_ageto;
                                            if (!is_age_in_main(agfrom, agto))
                                            {
                                                mealchildren[mc].child_action = "DELETE"
                                            }
                                        }
                                    }
                                }
                            }

                            //=========== clean meal extra supplements from unnecessary ages =============
                            var mealextrasupp = roomdates[d].date_mealextrasupplement_rules;
                            for (var m = 0; m < mealextrasupp.length; m++)
                            {
                                if (mealextrasupp[m].extra_action != "DELETE")
                                {
                                    var extrachildren = mealextrasupp[m].extra_children;
                                    for (var mc = 0; mc < extrachildren.length; mc++)
                                    {
                                        if (extrachildren[mc].child_action != "DELETE")
                                        {
                                            var agfrom = extrachildren[mc].child_agefrom;
                                            var agto = extrachildren[mc].child_ageto;
                                            if (!is_age_in_main(agfrom, agto))
                                            {
                                                extrachildren[mc].child_action = "DELETE"
                                            }
                                        }
                                    }
                                }
                            }

                            //=========== clean children sharing and single =============
                            var childpolicies_rules = roomdates[d].date_childpolicies_rules;
                            for (var cr = 0; cr < childpolicies_rules.length; cr++)
                            {
                                var rule_action = childpolicies_rules[cr].rule_action;
                                var flg_delete_rule = true;

                                if (rule_action != "DELETE")
                                {
                                    var rulepolicy = childpolicies_rules[cr].rule_policy;
                                    for (var rp = 0; rp < rulepolicy.length; rp++)
                                    {
                                        if (rulepolicy[rp].policy_action != "DELETE")
                                        {
                                            var agfrom = rulepolicy[rp].policy_units_additional_child_agefrom;
                                            var agto = rulepolicy[rp].policy_units_additional_child_ageto;
                                            if (!is_age_in_main(agfrom, agto))
                                            {
                                                rulepolicy[rp].policy_action = "DELETE"
                                            } else
                                            {
                                                flg_delete_rule = false;
                                            }
                                        }
                                    }

                                    if (flg_delete_rule)
                                    {
                                        //delete rule since all children nodes are deleted
                                        childpolicies_rules[cr].rule_action = "DELETE";
                                    }
                                }
                            }

                        }
                    }
                }

            }
        }
    }


    function saveContractCore()
    {
        //=========================================================================
        try
        {

            var params = "token=" + encodeURIComponent(global_token);

            //main
            var main_details = form_main.getFormData();
            params += "&main_details=" + encodeURIComponent(JSON.stringify(main_details));

            //currency
            var currency_details = form_currency.getFormData();
            params += "&currency_details=" + encodeURIComponent(JSON.stringify(currency_details));
            params += "&currency_exchrates=" + encodeURIComponent(JSON.stringify(_json_exchangerates));

            //taxcomm
            params += "&taxcomm=" + encodeURIComponent(JSON.stringify(_json_taxcommi));

            //minstay, meal, extra, checkinout, cancellation, capacity, adult, child, single parent
            params += "&capacity=" + encodeURIComponent(JSON.stringify(_json_capacity));

            //notes
            var notes_details = form_notes.getFormData();
            params += "&notes_details=" + encodeURIComponent(JSON.stringify(notes_details));

            detailslayout.progressOn();

            dhtmlxAjax.post("php/api/hotelcontracts/savecontract.php", params, function (loader) {
                detailslayout.progressOff();
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

                        form_main.setItemValue("id", json_obj.ID);
                        loadHotelContracts(json_obj.ID);
                        loadCapacity(json_obj.ID, null);

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

        } catch (err) {
            detailslayout.progressOff();
            console.log(err.message);
            dhtmlx.alert({
                text: err.message,
                type: "alert-warning",
                title: "Save Contract Core",
                callback: function () {
                }
            });
        }
    }


    function deleteContract(cid)
    {

        dhtmlx.confirm({
            title: "Delete Contract",
            type: "confirm",
            text: "Confirm Deletion?",
            callback: function (tf) {
                if (tf)
                {
                    contractlayout.progressOn();
                    var params = "id=" + cid + "&t=" + encodeURIComponent(global_token);
                    dhtmlxAjax.post("php/api/hotelcontracts/deletecontract.php", params, function (loader) {

                        contractlayout.progressOff();

                        if (loader)
                        {
                            if (loader.xmlDoc.responseURL == "")
                            {
                                dhtmlx.alert({
                                    text: "Connection Lost!",
                                    type: "alert-warning",
                                    title: "DELETE CONTRACT",
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
                                    title: "DELETE CONTRACT",
                                    callback: function () {
                                    }
                                });
                                return false;
                            }
                            if (json_obj.OUTCOME == "OK")
                            {
                                grid_contracts.deleteRow(cid);
                            } else
                            {
                                dhtmlx.alert({
                                    text: json_obj.OUTCOME,
                                    type: "alert-warning",
                                    title: "DELETE CONTRACT",
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

    function modifyContract(cid, flg_copypaste)
    {
        //load values

        form_main.clear();
        form_main.setItemValue("id", cid);
        form_main.setItemValue("hotelfk", global_hotel_id);
        form_main.getCalendar("active_from").clearSensitiveRange();
        form_main.getCalendar("active_to").clearSensitiveRange();

        grid_exchrates.clearAll();

        var data = dsContracts.item(cid);

        form_main.setFormData(data);
        form_currency.setFormData(data);
        form_notes.setFormData(data);

        loadArea(data.countryfk, data.areafk);


        loadCapacity(cid, function () {
            loadExchangeRates(cid, function () {
                loadTaxCommi(cid, function () {

                    //trigger validation
                    var arr_validations = ["main_details", "main_contractdates", "main_rollover",
                        "main_childrenages",
                        "currency_details", "currency_mapping", "tax", "rooms_dates",
                        "rooms_minstay", "rooms_capacity", "rooms_policies",
                        "rooms_meal", "rooms_adult", "rooms_child", "rooms_singleparent"];

                    resetError();
                    validateContract(arr_validations);
                    parseErrors();
                    grid_choices.selectRowById("main", false, true, true);

                    if (flg_copypaste)
                    {
                        resetContractIds();
                    }
                });
            });
        });



        //==============================================
        var countries_ids = form_main.getItemValue("market_countries_ids");
        var countries_names = form_main.getItemValue("market_countries_display");
        var arr_countries_ids = countries_ids.split(",")
        $("[name='market_countries_display']").prop('title', arr_countries_ids.length + " selected => " + countries_names);


        var room_names = form_main.getItemValue("rooms_display");
        $("[name='rooms_display']").prop('title', room_names);

        //==============================================
        grid_choices.selectRowById("main", false, true, true);



        popupwin_contracts.center();
        popupwin_contracts.show();
        popupwin_contracts.setModal(true);
    }


    function loadHotel()
    {

        var params = "hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelcontracts/loadhotel.php", params, function (loader) {

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD HOTELS",
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
                        title: "LOAD HOTELS",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    _json_hotels = json_obj.HOTELS;

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD HOTELS",
                        callback: function () {
                        }
                    });
                }

            }
        });
    }

    function loadHotelLocations()
    {
        //set default hotel location values 

        var hotelobj = _json_hotels[0];
        var areafk = hotelobj.areafk;
        var coastfk = hotelobj.coastfk;
        var phy_countryfk = hotelobj.phy_countryfk;

        cboCountry.setComboValue(phy_countryfk);
        loadArea(phy_countryfk, areafk);
        cboCoast.setComboValue(coastfk);

        return;
    }

    function validate_checkLoadSeasonGaps(roomid)
    {

        //check for date gaps in that room from contractstart to contractend

        var ranges = [];
        var missing_gaps = [];

        //=================================================================
        //push the contract start date into the array 
        var active_from = form_main.getItemValue("active_from", true);

        var dtfrom_obj = utils_createDateObjFromString(active_from, "dd-mm-yyyy");
        var dtto_obj = utils_createDateObjFromString(active_from, "dd-mm-yyyy");
        dtfrom_obj.setDate(dtfrom_obj.getDate() - 1);
        dtto_obj.setDate(dtto_obj.getDate() - 1);

        var dtfrom_ts = (+dtfrom_obj / 1000);
        var dtto_ts = (+dtto_obj / 1000);

        var dtfrom_dmy = active_from;
        var dtto_dmy = active_to;


        ranges.push({from_ts: dtfrom_ts, until_ts: dtto_ts,
            from_obj: dtfrom_obj, until_obj: dtto_obj,
            from_dmy: dtfrom_dmy, until_dmy: dtto_dmy});

        //=================================================================

        var roomobj = lookupRoomObj(roomid);

        if (!roomobj)
        {
            return missing_gaps;
        }

        for (var j = 0; j < roomobj.room_dates.length; j++)
        {
            if (roomobj.room_dates[j].date_action != "DELETE")
            {
                //push all dates attached to the room
                var dtfrom = roomobj.room_dates[j].date_dtfrom;
                var dtto = roomobj.room_dates[j].date_dtto;

                dtfrom_obj = utils_createDateObjFromString(dtfrom, "yyyy-mm-dd");
                dtto_obj = utils_createDateObjFromString(dtto, "yyyy-mm-dd");

                dtfrom_ts = (+dtfrom_obj / 1000);
                dtto_ts = (+dtto_obj / 1000);

                dtfrom_dmy = utils_formatDate(utils_date_to_str(dtfrom_obj), "DD-MM-YYYY");
                dtto_dmy = utils_formatDate(utils_date_to_str(dtto_obj), "DD-MM-YYYY");


                ranges.push({from_ts: dtfrom_ts, until_ts: dtto_ts,
                    from_obj: dtfrom_obj, until_obj: dtto_obj,
                    from_dmy: dtfrom_dmy, until_dmy: dtto_dmy});
            }
        }

        //=================================================================
        //push the contract end date
        var active_to = form_main.getItemValue("active_to", true);

        dtfrom_obj = utils_createDateObjFromString(active_to, "dd-mm-yyyy");
        dtto_obj = utils_createDateObjFromString(active_to, "dd-mm-yyyy");
        dtfrom_obj.setDate(dtfrom_obj.getDate() + 1);
        dtto_obj.setDate(dtto_obj.getDate() + 1);

        dtfrom_ts = (+dtfrom_obj / 1000);
        dtto_ts = (+dtto_obj / 1000);

        dtfrom_dmy = active_from;
        dtto_dmy = active_to;


        ranges.push({from_ts: dtfrom_ts, until_ts: dtto_ts,
            from_obj: dtfrom_obj, until_obj: dtto_obj,
            from_dmy: dtfrom_dmy, until_dmy: dtto_dmy});

        //=================================================================
        //sort the array
        ranges.sort(function (a, b) {
            return a.from_ts - b.from_ts;
        });


        //=================================================================
        //find the gaps

        var missing_gaps = [];

        for (var i = 1; i < ranges.length; i++) {
            var beginningOfHole_obj = ranges[i - 1].until_obj;
            beginningOfHole_obj.setDate(beginningOfHole_obj.getDate() + 1);

            var endOfHole_obj = ranges[i].from_obj;
            endOfHole_obj.setDate(endOfHole_obj.getDate() - 1);

            if (Number(beginningOfHole_obj) <= Number(endOfHole_obj)) {

                missing_gaps.push({from: utils_formatDate(utils_date_to_str(beginningOfHole_obj), "DD-MM-YYYY"),
                    until: utils_formatDate(utils_date_to_str(endOfHole_obj), "DD-MM-YYYY")});
            }
        }

        return missing_gaps;
    }

    function loadPeriods()
    {
        grid_loadperiods_seasons.clearAll();
        grid_loadperiods_rooms.clearAll();

        for (var i = 0; i < _dsDatePeriods.dataCount(); i++)
        {
            var item = _dsDatePeriods.item(_dsDatePeriods.idByIndex(i));
            var id = item.id;
            var checkin = item.checkin_dmy;
            var checkout = item.checkout_dmy;
            var season = item.season;
            var scode = item.scode;
            var caption = "<b>" + scode + "</b> : " + season;
            grid_loadperiods_seasons.addRow(id, [1, caption, checkin, checkout]);
        }

        //========================================

        var roomids = utils_trim(form_main.getItemValue("rooms_ids"), " ");

        if (roomids == "")
        {
            dhtmlx.alert({
                text: "Please Select at Least one Room!",
                type: "alert-warning",
                title: "Load Period",
                callback: function () {
                    grid_choices.selectRowById("main", false, true, true);
                }
            });

            return;
        }

        var arr_ids = roomids.split(",");
        for (var i = 0; i < arr_ids.length; i++)
        {
            //get the details from _dsRooms            
            var room_id = utils_trim(arr_ids[i], " ");
            if (room_id != "")
            {
                var item = _dsRooms.item(room_id);
                if (item)
                {
                    var room_name = item.roomname;
                    grid_loadperiods_rooms.addRow(room_id, [1, room_name]);
                }
            }
        }


        popupwin_contracts.setModal(false);
        popupwin_loadperiods.setModal(true);
        popupwin_loadperiods.center();
        popupwin_loadperiods.show();
    }

    function applyLoadPeriods()
    {
        var periodids = grid_loadperiods_seasons.getCheckedRows(grid_loadperiods_seasons.getColIndexById("X"));
        var roomids = grid_loadperiods_rooms.getCheckedRows(grid_loadperiods_rooms.getColIndexById("X"));

        if (periodids == "")
        {
            dhtmlx.alert({
                text: "Please Select at Least one Period!",
                type: "alert-warning",
                title: "Load Period",
                callback: function () {
                }
            });

            return;
        }

        if (roomids == "")
        {
            dhtmlx.alert({
                text: "Please Select at Least one Room!",
                type: "alert-warning",
                title: "Load Period",
                callback: function () {
                }
            });

            return;
        }


        //======================================================

        var active_from = form_main.getItemValue("active_from", true);
        var active_to = form_main.getItemValue("active_to", true);
        var settings = form_loadperiods_settings.getCheckedValue("settings");
        var overlapcount = 0;

        var arr_period_ids = periodids.split(",");
        var arr_room_ids = roomids.split(",");

        for (var i = 0; i < arr_room_ids.length; i++)
        {

            var roomid = arr_room_ids[i];

            if (roomid != "")
            {
                //==============================================
                if (settings == "overwrite")
                {
                    //set all dates to delete
                    var roomobj = lookupRoomObj(roomid);

                    for (var j = 0; j < roomobj.room_dates.length; j++)
                    {
                        roomobj.room_dates[j].date_action = "DELETE";
                    }
                }
                //==============================================

                for (var j = 0; j < arr_period_ids.length; j++)
                {
                    var periodid = arr_period_ids[j];

                    if (periodid != "")
                    {
                        var item = _dsDatePeriods.item(periodid);
                        var checkin = item.checkin;
                        var checkout = item.checkout;

                        var checkin_dmy = item.checkin_dmy;
                        var checkout_dmy = item.checkout_dmy;

                        //if checkin < contractstart then checkin = contractstart
                        //if contractend < checkout  then checkout = contractend
                        if (utils_validateDateOrder(checkin_dmy, active_from))
                        {
                            checkin = utils_date_to_str(utils_createDateObjFromString(active_from, "dd-mm-yyyy"));
                            checkin_dmy = active_from;
                        }

                        if (utils_validateDateOrder(active_to, checkout_dmy))
                        {
                            checkout = utils_date_to_str(utils_createDateObjFromString(active_to, "dd-mm-yyyy"));
                            checkout_dmy = active_to;
                        }

                        //============================================

                        if (validateCapacityDates(checkin_dmy, checkout_dmy, roomid, null) == "OK")
                        {
                            addNewDateRecord(roomid, checkin, checkout);
                        } else
                        {
                            overlapcount++;
                        }
                    }
                }
            }
        }

        populateRoomsTree();
        popupwin_loadperiods.setModal(false);
        popupwin_contracts.setModal(true);
        popupwin_loadperiods.hide();

        if (overlapcount > 0)
        {
            dhtmlx.alert({
                text: overlapcount + " <b>Overlapping Dates</b> Skipped!",
                type: "alert-warning",
                title: "Load Periods",
                callback: function () {
                }
            });
        }


        //validate rooms gaps
        validateContract(["rooms_dates"]);
        if (!parseErrors())
        {
            dhtmlx.alert({
                text: "Room(s)</b> with <b>Gaps</b> in dates detected!",
                type: "alert-warning",
                title: "Load Periods",
                callback: function () {
                }
            });
        }
        return;
    }


    function validate_room_capacity(roomid)
    {
        //if roomid is blank, means validate all rooms, else validate for specific room

        var arr_errors = [];

        //validates the minmax ranges

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE" &&
                    (roomid == "" || roomid == _json_capacity[i].room_id)) {

                var room_dates = _json_capacity[i].room_dates;
                var err_room_id = _json_capacity[i].room_id;

                for (var j = 0; j < room_dates.length; j++)
                {
                    if (room_dates[j].date_action != "DELETE")
                    {
                        var err_room_date_rwid = room_dates[j].date_rwid;

                        var date_capacity_rules = room_dates[j].date_capacity_rules;
                        for (var k = 0; k < date_capacity_rules.length; k++)
                        {
                            if (date_capacity_rules[k].rule_action != "DELETE")
                            {
                                //for each rule

                                var err_room_date_rule_rwid = date_capacity_rules[k].rule_rwid;

                                var rule_capacity = date_capacity_rules[k].rule_capacity;
                                for (var l = 0; l < rule_capacity.length; l++)
                                {
                                    if (rule_capacity[l].capacity_action != "DELETE")
                                    {
                                        var min = rule_capacity[l].capacity_minpax;
                                        var max = rule_capacity[l].capacity_maxpax;


                                        if (min == "") {
                                            min = 0;
                                        }
                                        if (max == "") {
                                            max = 0;
                                        }

                                        if (min > max)
                                        {
                                            arr_errors.push({roomid: err_room_id,
                                                daterwid: err_room_date_rwid,
                                                rwid: err_room_date_rule_rwid,
                                                msg: "Minimum cannot be greater than Maximum"});
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return arr_errors;
    }


    function validate_room_meals()
    {
        //get the main meal plan from the contact.main
        var main_mealplan_fk = form_main.getItemValue("mealplan_fk");

        var arr_errors = [];

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE") {

                var room_dates = _json_capacity[i].room_dates;
                var err_room_id = _json_capacity[i].room_id;

                for (var j = 0; j < room_dates.length; j++)
                {
                    if (room_dates[j].date_action != "DELETE")
                    {
                        var err_room_date_rwid = room_dates[j].date_rwid;

                        //========================================================================
                        //MEALS
                        var date_mealsupplement_rules = room_dates[j].date_mealsupplement_rules;
                        var seenismain = false;
                        for (var k = 0; k < date_mealsupplement_rules.length; k++)
                        {
                            if (date_mealsupplement_rules[k].meal_action != "DELETE")
                            {
                                var err_meal_rwid = date_mealsupplement_rules[k].meal_rwid;
                                var err_meal_mealplanfk = date_mealsupplement_rules[k].meal_mealplanfk;
                                var err_meal_ismain = date_mealsupplement_rules[k].meal_ismain;

                                if (err_meal_mealplanfk == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_meal_rwid,
                                        mealtype: "meal",
                                        msg: "Missing meal plan"});
                                }
                                if (err_meal_ismain == "1")
                                {
                                    seenismain = true;
                                    if (main_mealplan_fk != err_meal_mealplanfk)
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_meal_rwid,
                                            mealtype: "meal",
                                            msg: "Main Meal Plan does not reflect Contract meal plan"});
                                    }
                                }
                            }
                        }

                        if (!seenismain)
                        {
                            arr_errors.push({roomid: err_room_id,
                                daterwid: err_room_date_rwid,
                                rwid: "",
                                mealtype: "meal",
                                msg: "Missing main meal plan"});

                        }

                        //========================================================================
                        //EXTRAS
                        var date_mealextrasupplement_rules = room_dates[j].date_mealextrasupplement_rules;
                        for (var k = 0; k < date_mealextrasupplement_rules.length; k++)
                        {
                            if (date_mealextrasupplement_rules[k].extra_action != "DELETE")
                            {
                                var err_extra_rwid = date_mealextrasupplement_rules[k].extra_rwid;
                                var err_extra_extra_name = date_mealextrasupplement_rules[k].extra_extra_name;
                                var err_extra_mandatory = date_mealextrasupplement_rules[k].extra_mandatory;
                                var err_extra_include_diner_rate_bb = date_mealextrasupplement_rules[k].extra_include_diner_rate_bb;
                                var err_extra_hb_mealplan_fk = date_mealextrasupplement_rules[k].extra_hb_mealplan_fk;
                                var err_extra_bb_mealplan_fk = date_mealextrasupplement_rules[k].extra_bb_mealplan_fk;
                                var err_extra_extra_date = date_mealextrasupplement_rules[k].extra_extra_date;


                                if (err_extra_extra_name == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_extra_rwid,
                                        mealtype: "extra",
                                        msg: "Missing extra meal name"});
                                }

                                if (err_extra_extra_date == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_extra_rwid,
                                        mealtype: "extra",
                                        msg: "Missing extra meal date"});
                                }

                                var dtfrom = utils_formatDate(err_extra_extra_date, "DD-MM-YYYY");
                                if (!subDatesWithinContract(dtfrom, ""))
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_extra_rwid,
                                        mealtype: "extra",
                                        msg: "Extra meal date must be within Contract Dates " + form_main.getItemValue("active_from", true) + " and " + form_main.getItemValue("active_to", true)});
                                }

                                if (err_extra_include_diner_rate_bb == "1")
                                {
                                    if (err_extra_hb_mealplan_fk == "")
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_extra_rwid,
                                            mealtype: "extra",
                                            msg: "Missing HB Meal Plan"});
                                    }

                                    if (err_extra_bb_mealplan_fk == "")
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_extra_rwid,
                                            mealtype: "extra",
                                            msg: "Missing BB Meal Plan"});
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        return arr_errors;

    }

    function validate_room_policycheckinout()
    {

        var arr_errors = [];

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_action != "DELETE") {

                var room_dates = _json_capacity[i].room_dates;
                var err_room_id = _json_capacity[i].room_id;

                for (var j = 0; j < room_dates.length; j++)
                {
                    if (room_dates[j].date_action != "DELETE")
                    {
                        var err_room_date_rwid = room_dates[j].date_rwid;

                        //========================================================================
                        //CHECK IN OUT
                        var date_policies_checkinout = room_dates[j].date_policies_checkinout;
                        for (var k = 0; k < date_policies_checkinout.length; k++)
                        {
                            if (date_policies_checkinout[k].checkinout_action != "DELETE")
                            {
                                var err_checkinout_rwid = date_policies_checkinout[k].checkinout_rwid;
                                var err_checkinout_charge_type = date_policies_checkinout[k].checkinout_charge_type;
                                var err_checkinout_charge_value = date_policies_checkinout[k].checkinout_charge_value;
                                var err_checkinout_checkinout_time = date_policies_checkinout[k].checkinout_checkinout_time;
                                var err_checkinout_policytype = date_policies_checkinout[k].checkinout_policytype;
                                var err_checkinout_time_beforeafter = date_policies_checkinout[k].checkinout_time_beforeafter;

                                if (err_checkinout_policytype == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Missing policy type"});
                                }
                                if (err_checkinout_time_beforeafter == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Missing Time Rule"});
                                }


                                if (err_checkinout_policytype == "ECI" &&
                                        err_checkinout_time_beforeafter == "AFTER")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Early Check In cannot be used with AFTER"});
                                }

                                if (err_checkinout_policytype == "LCO" &&
                                        err_checkinout_time_beforeafter == "BEFORE")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Late Check Out cannot be used with BEFORE"});
                                }


                                if (err_checkinout_checkinout_time == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Missing Check In Out Time"});
                                }
                                if (err_checkinout_charge_type == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Missing charge type"});
                                }
                                if (err_checkinout_charge_value == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_checkinout_rwid,
                                        policytype: "checkinout",
                                        msg: "Missing charge value"});
                                }
                            }
                        }
                        //========================================================================
                        //CANCELLATION
                        var date_policies_cancellation = room_dates[j].date_policies_cancellation;
                        for (var k = 0; k < date_policies_cancellation.length; k++)
                        {
                            if (date_policies_cancellation[k].cancellation_action != "DELETE")
                            {

                                var err_cancellation_rwid = date_policies_cancellation[k].cancellation_rwid;
                                var err_cancellation_canceltype = date_policies_cancellation[k].cancellation_canceltype;
                                var err_cancellation_charge_method = date_policies_cancellation[k].cancellation_charge_method;
                                var err_cancellation_charge_value = date_policies_cancellation[k].cancellation_charge_value;
                                var err_cancellation_dates_before_arrival_from = utils_trim(date_policies_cancellation[k].cancellation_dates_before_arrival_from, " ");
                                var err_cancellation_dates_before_arrival_to = utils_trim(date_policies_cancellation[k].cancellation_dates_before_arrival_to, " ");
                                var err_cancellation_days_before_arrival_from = utils_trim(date_policies_cancellation[k].cancellation_days_before_arrival_from, " ");
                                var err_cancellation_days_before_arrival_to = utils_trim(date_policies_cancellation[k].cancellation_days_before_arrival_to, " ");


                                if (err_cancellation_canceltype == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_cancellation_rwid,
                                        policytype: "cancellation",
                                        msg: "Missing Cancellation cancel type"});
                                }
                                if (err_cancellation_charge_method == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_cancellation_rwid,
                                        policytype: "cancellation",
                                        msg: "Missing Cancellation charge method"});
                                }
                                if (err_cancellation_charge_value == "")
                                {
                                    arr_errors.push({roomid: err_room_id,
                                        daterwid: err_room_date_rwid,
                                        rwid: err_cancellation_rwid,
                                        policytype: "cancellation",
                                        msg: "Missing Cancellation charge value"});
                                }


                                if (err_cancellation_days_before_arrival_from != "")
                                {
                                    err_cancellation_days_before_arrival_from = parseInt(err_cancellation_days_before_arrival_from, 10);
                                }

                                if (err_cancellation_days_before_arrival_to != "")
                                {
                                    err_cancellation_days_before_arrival_to = parseInt(err_cancellation_days_before_arrival_to, 10);
                                }


                                if (err_cancellation_days_before_arrival_from != "" &&
                                        err_cancellation_days_before_arrival_to != "")
                                {
                                    if (err_cancellation_days_before_arrival_from > err_cancellation_days_before_arrival_to)
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_cancellation_rwid,
                                            policytype: "cancellation",
                                            msg: "Invalid Cancellation Days Before Arrival- From To order"});
                                    }

                                }

                                if (err_cancellation_dates_before_arrival_from != "")
                                {

                                    var dtfrom = utils_formatDate(err_cancellation_dates_before_arrival_from, "DD-MM-YYYY");
                                    if (!subDatesWithinContract(dtfrom, ""))
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_cancellation_rwid,
                                            policytype: "cancellation",
                                            msg: "Cancellation Date Before Arrival From must be within Contract Dates " + form_main.getItemValue("active_from", true) + " and " + form_main.getItemValue("active_to", true)});
                                    }
                                }
                                if (err_cancellation_dates_before_arrival_to != "")
                                {
                                    var dtto = utils_formatDate(err_cancellation_dates_before_arrival_to, "DD-MM-YYYY");
                                    if (!subDatesWithinContract("", dtto))
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_cancellation_rwid,
                                            policytype: "cancellation",
                                            msg: "Cancellation Date Before Arrival To must be within Contract Dates " + form_main.getItemValue("active_from", true) + " and " + form_main.getItemValue("active_to", true)});
                                    }
                                }

                                if (err_cancellation_dates_before_arrival_from != "" && err_cancellation_dates_before_arrival_to != "")
                                {
                                    var dtfrom = utils_formatDate(err_cancellation_dates_before_arrival_from, "DD-MM-YYYY");
                                    var dtto = utils_formatDate(err_cancellation_dates_before_arrival_to, "DD-MM-YYYY");

                                    if (!utils_validateDateOrder(dtfrom, dtto))
                                    {
                                        arr_errors.push({roomid: err_room_id,
                                            daterwid: err_room_date_rwid,
                                            rwid: err_cancellation_rwid,
                                            policytype: "cancellation",
                                            msg: "Invalid Cancellation Date Before Arrival From To order"});
                                    }
                                }
                            }
                        }
                        //====================================================================
                    }
                }
            }
        }
        return arr_errors;
    }


    function validate_room_minstay()
    {
        //for each room 
        //  for each date
        //      validate minstay 
        //  next date
        //next room


        var arr_errors = [];

        for (var i = 0; i < _json_capacity.length; i++)
        {
            var roomid = _json_capacity[i].room_id;
            var roomaction = _json_capacity[i].room_action;
            if (roomaction != "DELETE")
            {
                var arr_dates = _json_capacity[i].room_dates;
                for (var j = 0; j < arr_dates.length; j++)
                {
                    var date_rwid = arr_dates[j].date_rwid;
                    var date_action = arr_dates[j].date_action;
                    if (date_action != "DELETE")
                    {
                        var arrminstayrules = arr_dates[j].date_minstay_rules;
                        for (var k = 0; k < arrminstayrules.length; k++)
                        {
                            var minstay_rwid = arrminstayrules[k].minstay_rwid;
                            var minstay_action = arrminstayrules[k].minstay_action;
                            var minstay_duration = arrminstayrules[k].minstay_duration;

                            if (minstay_action != "DELETE")
                            {
                                if (minstay_duration == "")
                                {
                                    arr_errors.push({roomid: roomid, daterwid: date_rwid,
                                        minstayrwid: minstay_rwid,
                                        msg: "Please state a Minimum Duration value"});
                                }
                            }
                        }
                    }
                }
            }
        }

        return arr_errors;
    }

    function highlighterrordatenodes(arr_errors)
    {
        if (!tree_roomdates)
        {
            return;
        }


        for (var i = 0; i < arr_errors.length; i++)
        {
            var daterwid = arr_errors[i].daterwid;
            var node_id = "DATE_" + daterwid;
            tree_roomdates.setItemImage2(node_id, "problems.png", "problems.png", "problems.png");
        }

        return;
    }


    function validate_roomdate_gaps()
    {
        var _errors = [];

        //for each room test if there are gaps in the dates
        for (var i = 0; i < _json_capacity.length; i++)
        {
            var roomid = _json_capacity[i].room_id;
            var roomaction = _json_capacity[i].room_action;
            if (roomaction != "DELETE")
            {
                var arrgaps = validate_checkLoadSeasonGaps(roomid);
                if (arrgaps.length > 0)
                {
                    _errors.push({roomid: roomid, gaps: arrgaps});
                }
            }
        }


        return _errors;

    }

    function addNewDateRecord(roomid, dtfrom, dtto)
    {
        //dtfrom, dtto in yyyy-mm-dd

        //lookup the season that the dates fall into
        //if there is a season then 
        //  check if season node is present for that room
        //  if season not node present then
        //      insert season node
        //  end if
        //  append date node to that season
        //
        //else it is a custom date
        //  check if custom season node is present for that room
        //  if custom node not present then
        //      insert custom node
        //  end if
        //  append date node to custom season
        //end if

        _capacity_room_date_id--;
        insertJsonDate(_capacity_room_date_id, roomid, dtfrom, dtto);


        var arr_seasons = groupSeasons(); //get an array of seasons for that contract
        var season_obj = getSeasonDateNodeWithin(dtfrom, dtto, arr_seasons)

        //date falls into a season
        //next, check if season is present in tree
        var node_id = "ROOM:" + roomid + "_SEASON:" + season_obj.seasonid;
        var idx = tree_roomdates.getIndexById(node_id);
        if (!idx)
        {
            //season node not present, so append the date
            appendTreeRoomSeason(roomid, season_obj.season, season_obj.seasonid);
        }

        //append the date to the season node
        appendTreeRoomDateNode(roomid, _capacity_room_date_id, dtfrom, dtto, season_obj.seasonid);
        tree_roomdates.selectItem("DATE_" + _capacity_room_date_id, true, false);
        return;
    }



    function enableAllRoomCheckboxes()
    {
        if (!tree_roomdates)
        {
            return;
        }

        //enable all the checkboxes in the tree
        var ids = tree_roomdates.getAllSubItems("0");
        var arrids = ids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var nodeid = utils_trim(arrids[i], " ");
            if (nodeid != "")
            {
                tree_roomdates.disableCheckbox(nodeid, false);
            }
        }
    }

    function toggleSelectedAllRooms(tfselect)
    {
        if (!tree_roomdates)
        {
            return;
        }

        //iterate across the tree and check all room nodes
        var ids = tree_roomdates.getAllSubItems("0");
        var arrids = ids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var nodeid = utils_trim(arrids[i], " ");
            if (nodeid != "")
            {
                tree_roomdates.setCheck(nodeid, tfselect);
            }
        }

    }



    function toggleSelectedAllPeriods(tfselect)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, daterwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var dtfrom = capacity_date_obj.date_dtfrom;
        var dtto = capacity_date_obj.date_dtto;

        for (var i = 0; i < _json_capacity.length; i++)
        {
            var arrdates = _json_capacity[i].room_dates;
            for (var j = 0; j < arrdates.length; j++)
            {
                if (arrdates[j].date_dtfrom == dtfrom &&
                        arrdates[j].date_dtto == dtto &&
                        arrdates[j].date_action != "DELETE")
                {
                    var date_rwid = arrdates[j].date_rwid;
                    var nodeid = "DATE_" + date_rwid;
                    tree_roomdates.setCheck(nodeid, tfselect);
                }
            }
        }
        return;
    }


    function updateAllCheckedDatesNode(context)
    {
        //from the update done to the current date node,
        //check if there are other date nodes that are checked
        //if yes, then overwrite their minstay nodes

        var from_id = tree_roomdates.getSelectedItemId();
        var from_node_type = tree_roomdates.getUserData(from_id, "ROOM_SEASON_DATE");
        var from_daterwid = tree_roomdates.getUserData(from_id, "DATE_RWID");
        var from_roomid = tree_roomdates.getUserData(from_id, "DATE_ROOMID");

        if (from_node_type != "DATE")
        {
            return;
        }

        var checkedids = tree_roomdates.getAllChecked();
        var arrids = checkedids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var to_id = arrids[i];
            if (to_id != "" && to_id != from_id)
            {
                var to_node_type = tree_roomdates.getUserData(to_id, "ROOM_SEASON_DATE");
                var to_daterwid = tree_roomdates.getUserData(to_id, "DATE_RWID");
                var to_roomid = tree_roomdates.getUserData(to_id, "DATE_ROOMID");

                if (to_node_type == "DATE")
                {
                    copyPasteTreeNodeToNode(from_roomid, from_daterwid, to_roomid, to_daterwid, context)
                }
            }
        }

        return;
    }

    function copyPasteTreeNodeToNode(roomidfrom, daterwidfrom, roomidto, daterwidto, context)
    {
        var date_objfrom = lookupCapacityRoomDateObj(roomidfrom, daterwidfrom);
        var date_objto = lookupCapacityRoomDateObj(roomidto, daterwidto);

        var arrfrom = date_objfrom[context];
        var arrto = date_objto[context];

        if (context == "date_minstay_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].minstay_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.minstay_action != "DELETE")
                {
                    _min_stay_id--;
                    _obj.minstay_rwid = _min_stay_id;
                    _obj.minstay_action = "INSERT";
                    arrto.push(_obj);
                }
            }
        } else if (context == "date_capacity_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].rule_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.rule_action != "DELETE")
                {
                    _capacity_room_date_rule_id--;
                    _obj.rule_rwid = _capacity_room_date_rule_id;
                    _obj.rule_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.rule_capacity.length; a++)
                    {
                        if (_obj.rule_capacity[a].capacity_action != "DELETE")
                        {
                            _capacity_room_date_rule_capacity_id--;
                            _obj.rule_capacity[a].capacity_rwid = _capacity_room_date_rule_capacity_id;
                            _obj.rule_capacity[a].capacity_action = "INSERT";
                        }
                    }

                    arrto.push(_obj);
                }
            }
        } else if (context == "date_policies_checkinout")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].checkinout_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.checkinout_action != "DELETE")
                {
                    _checkinout_id--;
                    _obj.checkinout_rwid = _checkinout_id;
                    _obj.checkinout_action = "INSERT";
                    arrto.push(_obj);
                }
            }
        } else if (context == "date_policies_cancellation")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].cancellation_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.cancellation_action != "DELETE")
                {
                    _cancellation_id--;
                    _obj.cancellation_rwid = _cancellation_id;
                    _obj.cancellation_action = "INSERT";
                    arrto.push(_obj);
                }
            }
        } else if (context == "date_mealsupplement_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].meal_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.meal_action != "DELETE")
                {
                    _meals_id--;
                    _obj.meal_rwid = _meals_id;
                    _obj.meal_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.meal_children.length; a++)
                    {
                        if (_obj.meal_children[a].child_action != "DELETE")
                        {
                            _meals_children_age_id--;
                            _obj.meal_children[a].child_rwid = _meals_children_age_id;
                            _obj.meal_children[a].child_action = "INSERT";
                        }
                    }

                    arrto.push(_obj);
                }
            }
        } else if (context == "date_mealextrasupplement_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].extra_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.extra_action != "DELETE")
                {
                    _extras_id--;
                    _obj.extra_rwid = _extras_id;
                    _obj.extra_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.extra_children.length; a++)
                    {
                        if (_obj.extra_children[a].child_action != "DELETE")
                        {
                            _extras_children_age_id--;
                            _obj.extra_children[a].child_rwid = _extras_children_age_id;
                            _obj.extra_children[a].child_action = "INSERT";
                        }
                    }

                    arrto.push(_obj);
                }
            }
        } else if (context == "date_adultpolicies_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].rule_action = "DELETE";
            }


            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.rule_action != "DELETE")
                {
                    _adultpolicy_room_date_rule_id--;
                    _obj.rule_rwid = _adultpolicy_room_date_rule_id;
                    _obj.rule_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.rule_policy.length; a++)
                    {
                        if (_obj.rule_policy[a].policy_action != "DELETE")
                        {
                            _adultpolicy_room_date_rule_capacity_id--;
                            _obj.rule_policy[a].policy_rwid = _adultpolicy_room_date_rule_capacity_id;
                            _obj.rule_policy[a].policy_action = "INSERT";

                            for (var b = 0; b < _obj.rule_policy[a].policy_values.length; b++)
                            {
                                if (_obj.rule_policy[a].policy_values[b].value_action != "DELETE")
                                {
                                    _adultpolicy_room_date_rule_capacity_value_id--;
                                    _obj.rule_policy[a].policy_values[b].value_rwid = _adultpolicy_room_date_rule_capacity_value_id;
                                    _obj.rule_policy[a].policy_values[b].value_action = "INSERT";

                                }
                            }
                        }
                    }

                    arrto.push(_obj);
                }
            }

        } else if (context == "date_childpolicies_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].rule_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.rule_action != "DELETE")
                {
                    _childpolicy_room_date_rule_id--;
                    _obj.rule_rwid = _childpolicy_room_date_rule_id;
                    _obj.rule_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.rule_policy.length; a++)
                    {
                        if (_obj.rule_policy[a].policy_action != "DELETE")
                        {
                            _childpolicy_room_date_rule_capacity_id--;
                            _obj.rule_policy[a].policy_rwid = _childpolicy_room_date_rule_capacity_id;
                            _obj.rule_policy[a].policy_action = "INSERT";

                            for (var b = 0; b < _obj.rule_policy[a].policy_values.length; b++)
                            {
                                if (_obj.rule_policy[a].policy_values[b].value_action != "DELETE")
                                {
                                    _childpolicy_room_date_rule_capacity_value_id--;
                                    _obj.rule_policy[a].policy_values[b].value_rwid = _childpolicy_room_date_rule_capacity_value_id;
                                    _obj.rule_policy[a].policy_values[b].value_action = "INSERT";
                                }
                            }
                        }
                    }

                    arrto.push(_obj);
                }
            }
        } else if (context == "date_singleparentpolicies_rules")
        {
            //1. delete all existing items in object to
            for (var i = 0; i < arrto.length; i++)
            {
                arrto[i].rule_action = "DELETE";
            }

            //2. insert all items from date_objfrom into date_objto
            for (var i = 0; i < arrfrom.length; i++)
            {
                var _obj = utils_deepCopy(arrfrom[i]);

                if (_obj.rule_action != "DELETE")
                {
                    _singleparentchildpolicy_room_date_rule_id--;
                    _obj.rule_rwid = _singleparentchildpolicy_room_date_rule_id;
                    _obj.rule_action = "INSERT";

                    //need to update all inner nodes
                    for (var a = 0; a < _obj.rule_policy.length; a++)
                    {
                        if (_obj.rule_policy[a].policy_action != "DELETE")
                        {
                            _singleparentchildpolicy_room_date_rule_capacity_id--;
                            _obj.rule_policy[a].policy_rwid = _singleparentchildpolicy_room_date_rule_capacity_id;
                            _obj.rule_policy[a].policy_action = "INSERT";

                            for (var b = 0; b < _obj.rule_policy[a].policy_values.length; b++)
                            {
                                if (_obj.rule_policy[a].policy_values[b].value_action != "DELETE")
                                {
                                    _singleparentchildpolicy_room_date_rule_capacity_value_id--;
                                    _obj.rule_policy[a].policy_values[b].value_rwid = _singleparentchildpolicy_room_date_rule_capacity_value_id;
                                    _obj.rule_policy[a].policy_values[b].value_action = "INSERT";
                                }
                            }
                        }
                    }

                    arrto.push(_obj);
                }
            }
        }
    }


    function testSimilarCapacityStructure()
    {
        //check if all dates nodes checked have the same capacity structure

        var checkedids = tree_roomdates.getAllChecked();
        var arrids = checkedids.split(",");

        for (var h = 0; h < arrids.length; h++)
        {
            var from_id = arrids[h];
            var from_node_type = tree_roomdates.getUserData(from_id, "ROOM_SEASON_DATE");
            var from_daterwid = tree_roomdates.getUserData(from_id, "DATE_RWID");
            var from_roomid = tree_roomdates.getUserData(from_id, "DATE_ROOMID");

            if (from_node_type == "DATE")
            {
                for (var i = 0; i < arrids.length; i++)
                {
                    var to_id = arrids[i];
                    if (to_id != "" && to_id != from_id)
                    {
                        var to_node_type = tree_roomdates.getUserData(to_id, "ROOM_SEASON_DATE");
                        var to_daterwid = tree_roomdates.getUserData(to_id, "DATE_RWID");
                        var to_roomid = tree_roomdates.getUserData(to_id, "DATE_ROOMID");

                        if (to_node_type == "DATE")
                        {
                            var chk1 = compareCapacityDatesObject(from_roomid, from_daterwid, to_roomid, to_daterwid);
                            var chk2 = compareCapacityDatesObject(to_roomid, to_daterwid, from_roomid, from_daterwid);

                            if (!(chk1 && chk2))
                            {
                                return false;
                            }
                        }
                    }
                }
            }
        }

        return true;
    }


    function compareCapacityDatesObject(roomidfrom, daterwidfrom, roomidto, daterwidto)
    {
        //compares two capacity dates objects and returns true if same
        //false otherwise

        var date_objfrom = lookupCapacityRoomDateObj(roomidfrom, daterwidfrom);
        var date_objto = lookupCapacityRoomDateObj(roomidto, daterwidto);

        var arrfrom = utils_deepCopy(date_objfrom.date_capacity_rules);
        var arrto = utils_deepCopy(date_objto.date_capacity_rules);


        for (var i = 0; i < arrfrom.length; i++)
        {
            var ruleobj = arrfrom[i];
            if (ruleobj.rule_action != "DELETE")
            {
                //check if this rule has found its copy in arrto
                if (!isRuleObjInArray(ruleobj, arrto))
                {
                    return false;
                }
            }
        }

        return true;
    }


    function isRuleObjInArray(ruleobj, arrto)
    {
        //looks for another rule object in arrto with all capacity nodes

        for (var i = 0; i < arrto.length; i++)
        {
            var ruleobj_compareto = arrto[i];
            if (ruleobj_compareto.rule_action != "DELETE")
            {
                if (isRuleObjSimilar(ruleobj, ruleobj_compareto))
                {
                    ruleobj_compareto.rule_action = "DELETE";
                    return true;
                }
            }
        }

        return false;
    }


    function isRuleObjSimilar(ruleobj_source, ruleobj_object)
    {
        for (var i = 0; i < ruleobj_source.rule_capacity.length; i++)
        {
            var obj_capacity_source = ruleobj_source.rule_capacity[i];

            if (!capacityObjInRuleObject(obj_capacity_source, ruleobj_object))
            {
                return false;
            }
        }

        return true;
    }

    function capacityObjInRuleObject(obj_capacity_source, ruleobj_object)
    {
        //no need to check if obj_capacity_source.category is child and
        //obj_capacity_source.agefrom and ageto is a mix age combo
        var chk = is_age_in_main(obj_capacity_source.capacity_child_agefrom, obj_capacity_source.capacity_child_ageto);
        if (obj_capacity_source.capacity_category == "CHILD" && !chk)
        {
            return true;
        }



        for (var i = 0; i < ruleobj_object.rule_capacity.length; i++)
        {
            var obj_capacity_object = ruleobj_object.rule_capacity[i];

            if (obj_capacity_object.capacity_action != "DELETE")
            {

                if (obj_capacity_source.capacity_category == obj_capacity_object.capacity_category &&
                        obj_capacity_source.capacity_minpax == obj_capacity_object.capacity_minpax &&
                        obj_capacity_source.capacity_maxpax == obj_capacity_object.capacity_maxpax &&
                        obj_capacity_source.capacity_child_agefrom == obj_capacity_object.capacity_child_agefrom &&
                        obj_capacity_source.capacity_child_ageto == obj_capacity_object.capacity_child_ageto)
                {
                    return true;
                }

            }
        }

        return false;
    }

    function deleteTaxCommiItem(grid_caller, buying_selling)
    {
        var itemrwid = grid_caller.getSelectedRowId();
        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        var rwobj = null;

        if (buying_selling == "BUYING")
        {
            rwobj = lookupTaxCommiRoomBuySellItem(roomid, "buying_settings", itemrwid);
        } else if (buying_selling == "SELLING")
        {
            rwobj = lookupTaxCommiRoomBuySellItem(roomid, "selling_settings", itemrwid);
        }

        if (!rwobj)
        {
            alert("item not found!");
            return;
        }

        rwobj.setting_action = "DELETE";

        resetTaxCommiRowIndex(roomid);

        loadGridTaxCommXML(roomid, {
            selectrowid_buy: "",
            selectrowid_sell: ""
        });

        return;
    }

    function showTaxCommItems(grid_caller, buying_selling)
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

                var nodeid = tree_taxcomm.getSelectedItemId();
                var node = tree_taxcomm.getUserData(nodeid, "ROOM_EXCEPTION");
                var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

                if (node != "GENERAL" && node != "EXCEPTION")
                {
                    dhtmlx.alert({
                        text: "The General Setting or an Exception Node should have been selected!",
                        type: "alert-warning",
                        title: "Select Items",
                        callback: function () {
                        }
                    });

                    popupwin.close();
                }


                var checkedids = pop_grid.getCheckedRows(pop_grid.getColIndexById("X"));

                if (checkedids != "")
                {
                    var arr_ids = checkedids.split(",");

                    for (var i = 0; i < arr_ids.length; i++)
                    {
                        var id = arr_ids[i];

                        var item = _dsTaxCommiItems.item(id);

                        if (buying_selling == "BUYING")
                        {
                            //append selected items
                            insertTaxCommiJsonNode(roomid, "buying_settings",
                                    "BUYING",
                                    item.id,
                                    item.item_name,
                                    item.abbrv,
                                    item.code,
                                    item.core_addon,
                                    "", "", "ROUNDUP");
                        } else
                        {
                            //selling grid
                            //insert selected items between conversp and finalsp

                            var obj_room = lookupTaxCommiRoomObj(roomid);
                            var arr_temp = [];
                            var arrsell_dispensable = utils_deepCopy(obj_room.selling_settings);

                            for (var j = 0; j < arrsell_dispensable.length; j++)
                            {
                                var obj = arrsell_dispensable[j];
                                var setting_item_code = obj.setting_item_code;
                                if (setting_item_code != "FINALSP")
                                {
                                    arr_temp.push(obj);
                                }
                            }

                            //now insert the new item
                            obj_room.selling_settings = arr_temp;

                            insertTaxCommiJsonNode(roomid, "selling_settings",
                                    "SELLING",
                                    item.id,
                                    item.item_name,
                                    item.abbrv,
                                    item.code,
                                    item.core_addon,
                                    "", "", "ROUNDUP");


                            //now insert the last item: finalsp   
                            var finalsp_obj = arrsell_dispensable[arrsell_dispensable.length - 1];
                            obj_room.selling_settings.push(finalsp_obj);
                        }
                    }

                    //apply default values in case commi or markup selected
                    resetTaxCommiRowIndex(roomid);

                    loadDefaultTaxCommiValues(function () {
                        loadGridTaxCommXML(roomid, {
                            selectrowid_buy: "",
                            selectrowid_sell: ""
                        });
                    });
                }

                popupwin.close();
            }
        });


        pop_grid = null;
        pop_grid = pop_layout.cells("a").attachGrid(300, 200);
        pop_grid.setIconsPath('libraries/dhtmlx/imgs/');
        pop_grid.setHeader(",Item Code,Item Name,Core/Addon");
        pop_grid.setColumnIds("X,code,item_name,core_addon");
        pop_grid.setColTypes("ch,ro,ro,ro");
        pop_grid.setInitWidths("40,100,200,0");
        pop_grid.setColAlign("center,left,left,left");
        pop_grid.setColSorting('int,str,str,str');
        pop_grid.enableStableSorting(true);
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

        for (var i = 0; i < _dsTaxCommiItems.dataCount(); i++)
        {
            var item = _dsTaxCommiItems.item(_dsTaxCommiItems.idByIndex(i));
            var itemid = item.id;
            var item_name = item.item_name;
            var item_code = item.code;
            var item_abbrv = item.abbrv;
            var item_buying_selling = item.buying_selling;
            var item_user_choices = item.user_choices;
            var item_core_addon = item.core_addon;

            if (item_user_choices == 1 &&
                    (item_buying_selling == buying_selling || item_buying_selling == "BOTH"))
            {
                pop_grid.addRow(itemid, [0, item_code, item_name, item_core_addon]);
            }
        }

        pop_grid.groupBy(pop_grid.getColIndexById("core_addon"));

        popupwin.show();
        popupwin.center();
        popupwin.setText("Select Items");
        popupwin_contracts.setModal(false);
        popupwin.setModal(true);

    }


    function loadDefaultTaxCommiValues(callback)
    {
        //get the default hotel commission rates based on the contract active date

        var contract_activefrom = form_main.getItemValue("active_from", true);
        if (contract_activefrom == "")
        {
            return;
        }

        //reformat from dd-mm-yyyy to yyyy-mm-dd
        contract_activefrom = utils_date_to_str(utils_createDateObjFromString(contract_activefrom, "dd-mm-yyyy"));

        var params = "hid=" + global_hotel_id +
                "&t=" + encodeURIComponent(global_token) +
                "&activefrom=" + encodeURIComponent(contract_activefrom);

        dhtmlxAjax.post("php/api/hotelcontracts/lookupdefaulcommission.php", params, function (loader) {

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD DEFAULT COMMISSION",
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
                        title: "LOAD DEFAULT COMMISSION",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    var _json_default_commission = json_obj.DEFAULT_COMMISSION;
                    var obj = lookupTaxCommiRoomObj("GENERAL");
                    if (obj)
                    {


                        var selected_currency_buy_ids = form_currency.getItemValue("selected_currency_buy_ids");
                        var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
                        var arr_buying_settings = obj.buying_settings;
                        var arr_selling_settings = obj.selling_settings;

                        applyDefaultTaxCommiValues(selected_currency_buy_ids, arr_buying_settings, ["COMMI", "MKUP"], _json_default_commission);
                        applyDefaultTaxCommiValues(selected_currency_sell_ids, arr_selling_settings, ["COMMI", "MKUP"], _json_default_commission);

                        if (callback)
                        {
                            callback();
                        }
                    }

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD DEFAULT COMMISSION",
                        callback: function () {
                        }
                    });
                }
            }
        });

    }


    function applyDefaultTaxCommiValues(selected_currency_ids, arrbuysell, arrItemCodes, _json_default_commission)
    {

        var arrcurrencyids = selected_currency_ids.split(",");

        for (var i = 0; i < arrbuysell.length; i++)
        {
            var itemobj = arrbuysell[i];
            if (itemobj.setting_action != "DELETE")
            {
                for (var c = 0; c < arrItemCodes.length; c++)
                {
                    var itemcode = arrItemCodes[c];

                    if (itemobj.setting_item_code == itemcode)
                    {
                        if (itemobj.setting_values.length == 0)
                        {
                            for (var j = 0; j < arrcurrencyids.length; j++)
                            {
                                var currencyid = arrcurrencyids[j];
                                if (currencyid != "")
                                {

                                    var currencycode = "";
                                    var itemCurrency = _dsCurrencies.item(currencyid);

                                    if (itemCurrency && _json_default_commission[itemcode] != "")
                                    {
                                        currencycode = itemCurrency.value;
                                        _taxcommi_settings_value_id--;

                                        itemobj.setting_values.push({
                                            value_rwid: _taxcommi_settings_value_id,
                                            value_currency_fk: currencyid,
                                            value_value: _json_default_commission[itemcode],
                                            value_currency_code: currencycode,
                                            value_action: "INSERT"
                                        });

                                        itemobj.setting_basis = "%";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    function moveTaxCommiItem(grid, buysel, up_down)
    {
        var rid = grid.getSelectedRowId();
        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        if (up_down == "UP")
        {
            grid.moveRowUp(rid);
        } else
        {
            grid.moveRowDown(rid);
        }


        //=============================================================
        //rerun grid row by row to reupdate the index
        var idx = 0;
        for (var i = 0; i < grid_taxcomm_buy.getRowsNum(); i++)
        {
            var obj = lookupTaxCommiRoomBuySellItem(roomid, "buying_settings", grid_taxcomm_buy.getRowId(i));
            if (obj)
            {
                obj.setting_row_index = idx;
            }
            idx++;
        }

        for (var i = 0; i < grid_taxcomm_sell.getRowsNum(); i++)
        {
            var obj = lookupTaxCommiRoomBuySellItem(roomid, "selling_settings", grid_taxcomm_sell.getRowId(i));
            if (obj)
            {
                obj.setting_row_index = idx;
            }
            idx++;
        }
        //=============================================================


        //sort the array of buy or sell as per setting_row_index
        var roomobj = lookupTaxCommiRoomObj(roomid);
        var arr = roomobj[buysel];
        arr.sort(function (a, b) {
            return a.setting_row_index - b.setting_row_index;
        });


        //=============================================================
        var selectrowid_buy = "";
        var selectrowid_sell = "";

        if (buysel == "buying_settings")
        {
            selectrowid_buy = rid;
        } else if (buysel == "selling_settings")
        {
            selectrowid_sell = rid;
        }

        //reload the grid
        loadGridTaxCommXML(roomid, {
            selectrowid_buy: selectrowid_buy,
            selectrowid_sell: selectrowid_sell
        });

    }


    function onGridTaxCommBuySellEdit(buying_selling, stage, rId, cInd, nValue, oValue)
    {
        var grid = null;
        var buy_sell = "";
        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        if (buying_selling == "BUYING")
        {
            grid = grid_taxcomm_buy;
            buy_sell = "buying_settings";
        } else {
            grid = grid_taxcomm_sell;
            buy_sell = "selling_settings";
        }

        if (stage == 2)
        {
            if (nValue != oValue)
            {
                var type = grid.cells(rId, cInd).getAttribute("type");
                var context = grid.cells(rId, cInd).getAttribute("context");
                var currencyid = grid.cells(rId, cInd).getAttribute("currencyfk");

                if (type == "edn")
                {
                    nValue = utils_trim(nValue, " ");
                    if (nValue == "")
                    {
                        nValue = "0";
                    }

                    if (isNaN(nValue))
                    {
                        return false;
                    }
                }

                if (context == "setting_applyon_formula")
                {
                    //deny lower case letters in formula
                    if ((/[a-z]/.test(nValue)))
                    {
                        dhtmlx.alert({
                            text: "Only <b>Upper Case</b> Letters allowed in Formula",
                            type: "alert-warning",
                            title: "Formula",
                            callback: function () {
                            }
                        });
                        return false;
                    }
                }

                return updateJsonTaxCommi(roomid, nValue, context, currencyid, rId, buy_sell);
            }
        }

        return true;

    }

    function calculateSellingPrice(roomid, value_input, currencyid_input, currencyid_sell, callback, arr_arguements)
    {
        var room_obj = lookupTaxCommiRoomObj(roomid);

        var arr_buy = room_obj.buying_settings;
        var arr_sell = room_obj.selling_settings;

        var arr_items = ["ROOM"];
        var params = "t=" + encodeURIComponent(global_token) +
                "&value_input=" + encodeURIComponent(value_input) +
                "&currencyid_input=" + encodeURIComponent(currencyid_input) +
                "&currencyid_sell=" + encodeURIComponent(currencyid_sell) +
                "&arr_buy=" + encodeURIComponent(JSON.stringify(arr_buy)) +
                "&arr_sell=" + encodeURIComponent(JSON.stringify(arr_sell)) +
                "&exchgrates=" + encodeURIComponent(JSON.stringify(_json_exchangerates)) +
                "&items=" + encodeURIComponent(JSON.stringify(arr_items));


        dhtmlxAjax.post("php/api/hotelcontracts/calculatesp.php", params, function (loader) {

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "CALCULATE SELLING PRICE",
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
                        title: "CALCULATE SELLING PRICE",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    var obj_calc = json_obj.CALCULATIONS;

                    if (callback)
                    {
                        arr_arguements.push(obj_calc);
                        callback(arr_arguements);
                    }
                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "CALCULATE SELLING PRICE",
                        callback: function () {
                        }
                    });
                }
            }
        });
    }


    function onGridTestTaxCommEdit(stage, rId, cInd, nValue, oValue)
    {
        if (stage == 0)
        {
            if (grid_test_taxcomm.getRowIndex(rId) != 1)
            {
                return false; //only entry row allows editing
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                nValue = utils_trim(nValue, " ");


                if (nValue == "")
                {
                    nValue = 0;
                }

                if (isNaN(nValue))
                {
                    return false;
                }

                nValue = parseInt(nValue, 10);
                if (nValue < 0)
                {
                    return false;
                }


                var currencycode = grid_test_taxcomm.cells("buy_title", cInd).getValue();
                var currencyid = "-1";
                for (var i = 0; i < _dsCurrencies.dataCount(); i++)
                {
                    var item = _dsCurrencies.item(_dsCurrencies.idByIndex(i));
                    if (item.value == currencycode)
                    {
                        currencyid = item.id;
                    }
                }

                var nodeid = tree_taxcomm.getSelectedItemId();
                var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

                //call the function for each selling currency
                var selected_currency_sell_ids = form_currency.getItemValue("selected_currency_sell_ids");
                var arr_currency_sell = selected_currency_sell_ids.split(",");
                for (var i = 0; i < arr_currency_sell.length; i++)
                {
                    var sell_currency_id = arr_currency_sell[i];
                    calculateSellingPrice(roomid, nValue, currencyid, sell_currency_id, placeTestGridValues, [rId, cInd, sell_currency_id]);
                }
            }
        }

        return true;
    }


    function resetVariables()
    {
        grid_minstay.clearAll();
        grid_exchrates.clearAll();
        grid_currencymap.clearAll();
        grid_meals.clearAll();
        grid_extras.clearAll();
        grid_checkinouts.clearAll();
        grid_cancellation.clearAll();

        _min_stay_id = -1;

        _capacity_room_rw_id = -1;
        _capacity_room_date_id = -1;
        _capacity_room_date_rule_id = -1;
        _capacity_room_date_rule_capacity_id = -1;

        _adultpolicy_room_date_rule_id = -1;
        _adultpolicy_room_date_rule_capacity_id = -1;
        _adultpolicy_room_date_rule_capacity_value_id = -1;

        _childpolicy_room_date_rule_id = -1;
        _childpolicy_room_date_rule_capacity_id = -1;
        _childpolicy_room_date_rule_capacity_value_id = -1;

        _singleparentchildpolicy_room_date_rule_id = -1;
        _singleparentchildpolicy_room_date_rule_capacity_id = -1;
        _singleparentchildpolicy_room_date_rule_capacity_value_id = -1;

        _meals_children_age_id = -1;
        _meals_id = -1;
        _extras_id = -1;
        _extras_children_age_id = -1;
        _checkinout_id = -1;
        _cancellation_id = -1;
        _taxcommi_room_rw_id = -1;
        _taxcommi_settings_id = -1;
        _taxcommi_settings_value_id = -1;
        _exchange_rate_id = -1;
        _currency_mapping_id = -1;

        _json_capacity = [];
        _json_taxcommi = [];
        _json_exchangerates = {exchange_rates: [], currency_mapping: []};

        resetError();
    }


    function parseErrors()
    {
        var allok = true;

        for (var i = 0; i < _arr_errors.length; i++)
        {
            var tabid = _arr_errors[i].tabid;

            //=======================================================================
            //=======================================================================
            if (tabid == "rooms")
            {
                //===============================================
                //check if errors in room date gaps
                if (_arr_errors[i].date_gaps.length > 0)
                {
                    allok = false;
                    grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed_err.png\" width=\"30px\" height=\"30px\">");

                    for (var j = 0; j < _arr_errors[i].date_gaps.length; j++)
                    {
                        //flag each room icon as error
                        if (tree_roomdates)
                        {
                            var roomid = _arr_errors[i].date_gaps[j].roomid;
                            var node_id = "ROOM_" + roomid;
                            tree_roomdates.setItemImage2(node_id, "problems.png", "problems.png", "problems.png");
                        }
                    }
                }

                //===============================================
                //now check each indvidual tabs in rooms
                var arrroom_tabs = _arr_errors[i].tabs;
                for (var j = 0; j < arrroom_tabs.length; j++)
                {
                    var roomtabid = arrroom_tabs[j].tabid;

                    if (arrroom_tabs[j].err.length > 0)
                    {
                        grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed_err.png\" width=\"30px\" height=\"30px\">");
                        highlighterrordatenodes(arrroom_tabs[j].err);
                        allok = false;
                    }
                }
            }
            //=======================================================================
            //=======================================================================
            else if (tabid == "main" && _arr_errors[i].err.length > 0)
            {
                grid_choices.cells("main", 0).setValue("<img src=\"images/task_err.png\" width=\"30px\" height=\"30px\">");
                allok = false;
            }
            //=======================================================================
            //=======================================================================
            else if (tabid == "currency" && _arr_errors[i].err.length > 0)
            {
                grid_choices.cells("currency", 0).setValue("<img src=\"images/currency_euro_sign_err.png\" width=\"30px\" height=\"30px\">");
                allok = false;
            }
            //=======================================================================
            //=======================================================================
            else if (tabid == "tax" && _arr_errors[i].err.length > 0)
            {
                grid_choices.cells("tax", 0).setValue("<img src=\"images/abacus_err.png\" width=\"30px\" height=\"30px\">");
                allok = false;
            }
            //=======================================================================
            //=======================================================================
        }

        return allok;
    }

    function resetError()
    {
        _arr_errors = [{tabid: "main", err: []},
            {tabid: "currency", err: []},
            {tabid: "tax", err: []},
            {tabid: "rooms",
                date_gaps: [],
                tabs: [
                    {tabid: "minstay", err: []},
                    {tabid: "capacity", err: []},
                    {tabid: "policies", err: []},
                    {tabid: "meal", err: []},
                    {tabid: "adult_policies", err: []},
                    {tabid: "child_policies", err: []},
                    {tabid: "single_parent", err: []}
                ]},
            {tabid: "notes", err: []}];

        //==============================================================

        if (grid_choices)
        {
            grid_choices.cells("main", 0).setValue("<img src=\"images/task.png\" width=\"30px\" height=\"30px\">");
            grid_choices.cells("currency", 0).setValue("<img src=\"images/currency_euro_sign.png\" width=\"30px\" height=\"30px\">");
            grid_choices.cells("tax", 0).setValue("<img src=\"images/abacus.png\" width=\"30px\" height=\"30px\">");
            grid_choices.cells("rooms", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
            grid_choices.cells("notes", 0).setValue("<img src=\"images/notes_1.png\" width=\"30px\" height=\"30px\">");

        }

        //==============================================================

        if (grid_room_choices)
        {
            grid_room_choices.cells("minstay", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("capacity", 0).setValue("<img src=\"images/pax.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("policies", 0).setValue("<img src=\"images/front_desk.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("meal", 0).setValue("<img src=\"images/hot_chocolate.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("adult_policies", 0).setValue("<img src=\"images/adult_72.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("child_policies", 0).setValue("<img src=\"images/child_72.png\" width=\"30px\" height=\"30px\">");
            grid_room_choices.cells("single_parent", 0).setValue("<img src=\"images/interview.png\" width=\"30px\" height=\"30px\">");

        }


        //==============================================================
        //reset all room and date icons in the tree

        if (tree_roomdates)
        {
            var ids = tree_roomdates.getAllSubItems("0");
            var arrids = ids.split(",");

            for (var i = 0; i < arrids.length; i++)
            {
                var nodeid = utils_trim(arrids[i], " ");
                if (nodeid != "")
                {
                    var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
                    if (node == "ROOM")
                    {
                        tree_roomdates.setItemImage(nodeid, "folderClosed.gif", "folderOpen.gif");
                    } else if (node == "DATE")
                    {
                        tree_roomdates.setItemImage(nodeid, "leaf.gif", "leaf.gif");
                    }
                }
            }
        }

        return;
    }


    function toggleError(maintabid, subtabid)
    {
        toolbar_details.hideItem("problem");

        var obj = getErrorTab(maintabid);

        resetRoomTabsIcons();

        if (maintabid == "main" || maintabid == "tax" || maintabid == "currency")
        {
            if (obj.err.length > 0)
            {
                toolbar_details.showItem("problem");
            }
        } else if (maintabid == "rooms")
        {

            var nodeid = null;

            if (tree_roomdates)
            {
                nodeid = tree_roomdates.getSelectedItemId();
            }

            if (!nodeid)
            {
                return;
            }

            var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");


            if (node == "ROOM")
            {
                var roomid = tree_roomdates.getUserData(nodeid, "ROOM_ROOMID");
                var date_gaps = obj.date_gaps;
                //check if room has date gaps

                for (var i = 0; i < date_gaps.length; i++)
                {
                    if (date_gaps[i].roomid == roomid)
                    {
                        toolbar_details.showItem("problem");
                    }
                }

            } else if (node == "DATE")
            {
                //check if there is an error for the selected date

                var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");
                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");

                var arrtabs = obj.tabs;
                for (var i = 0; i < arrtabs.length; i++)
                {
                    var err_subtabid = arrtabs[i].tabid;
                    var err_errarray = arrtabs[i].err;

                    for (var j = 0; j < err_errarray.length; j++)
                    {
                        if (err_errarray[j].roomid == roomid &&
                                err_errarray[j].daterwid == daterwid)
                        {

                            if (err_subtabid == subtabid)
                            {
                                toolbar_details.showItem("problem");
                            }

                            //change the icon for the grid
                            if (err_subtabid == "minstay")
                            {
                                grid_room_choices.cells("minstay", 0).setValue("<img src=\"images/bed_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "capacity")
                            {
                                grid_room_choices.cells("capacity", 0).setValue("<img src=\"images/pax_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "policies")
                            {
                                grid_room_choices.cells("policies", 0).setValue("<img src=\"images/front_desk_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "meal")
                            {
                                grid_room_choices.cells("meal", 0).setValue("<img src=\"images/hot_chocolate_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "adult_policies")
                            {
                                grid_room_choices.cells("adult_policies", 0).setValue("<img src=\"images/adult_72_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "child_policies")
                            {
                                grid_room_choices.cells("child_policies", 0).setValue("<img src=\"images/child_72_err.png\" width=\"30px\" height=\"30px\">");
                            } else if (err_subtabid == "single_parent")
                            {
                                grid_room_choices.cells("single_parent", 0).setValue("<img src=\"images/interview_err.png\" width=\"30px\" height=\"30px\">");
                            }
                        }
                    }
                }
            }

        }
    }

    function validateContract(arr_validations)
    {
        //"main_details","main_contractdates","main_rollover"
        for (var i = 0; i < arr_validations.length; i++)
        {
            var validation_code = arr_validations[i];

            if (validation_code == "main_details")
            {
                if (!form_main.validate())
                {
                    var msg = "<b>Main:</b> Fill in missing fields";
                    var obj = getErrorTab("main");
                    if (obj)
                    {
                        obj.err.push({msg: msg});
                    }
                }
            } else if (validation_code == "main_contractdates")
            {
                var msg = validate_contract_dates();
                var obj = getErrorTab("main");
                if (obj && msg != "")
                {
                    obj.err.push({msg: msg});
                }

            } else if (validation_code == "main_rollover")
            {
                var msg = validate_roll_over();
                var obj = getErrorTab("main");
                if (obj && msg != "")
                {
                    obj.err.push({msg: msg});
                }
            } else if (validation_code == "main_childrenages")
            {
                var msg = validate_children_ages();
                var obj = getErrorTab("main");
                if (obj && msg != "")
                {
                    obj.err.push({msg: msg});
                }

            } else if (validation_code == "currency_details")
            {
                var err = validate_currency();
                var obj = getErrorTab("currency");
                if (obj && err.length > 0)
                {
                    obj.err = obj.err.concat(err);
                }

            } else if (validation_code == "currency_mapping")
            {
                var err = validate_currency_mapping();
                var obj = getErrorTab("currency");
                if (obj)
                {
                    obj.err = obj.err.concat(err);
                }

            } else if (validation_code == "tax")
            {
                var arrerr = validate_taxcommi_settings("");
                var obj = getErrorTab("tax");
                if (obj)
                {
                    obj.err = arrerr;
                }

            } else if (validation_code == "rooms_dates")
            {
                //check for overlapping and gaps
                var arrgaperrors = validate_roomdate_gaps();
                var obj = getErrorTab("rooms");
                if (obj)
                {
                    obj.date_gaps = arrgaperrors;
                }
            } else if (validation_code == "rooms_minstay")
            {
                var arrminstayerrors = validate_room_minstay();
                var obj = getErrorRoomTab("minstay");
                if (obj)
                {
                    obj.err = arrminstayerrors;
                }
            } else if (validation_code == "rooms_capacity")
            {
                var arrcapacityerrors = validate_room_capacity("");
                var obj = getErrorRoomTab("capacity");
                if (obj)
                {
                    obj.err = arrcapacityerrors;
                }
            } else if (validation_code == "rooms_policies")
            {
                var arrpolicyerrors = validate_room_policycheckinout();
                obj = getErrorRoomTab("policies");
                if (obj)
                {
                    obj.err = arrpolicyerrors;
                }
            } else if (validation_code == "rooms_meal")
            {
                var arrmealerrors = validate_room_meals();
                obj = getErrorRoomTab("meal");
                if (obj)
                {
                    obj.err = arrmealerrors;
                }
            } else if (validation_code == "adult_policies")
            {

            } else if (validation_code == "child_policies")
            {

            } else if (validation_code == "single_parent")
            {

            }
        }

    }

    function getErrorTab(tabid)
    {
        for (var i = 0; i < _arr_errors.length; i++)
        {
            if (tabid == _arr_errors[i].tabid)
            {
                return _arr_errors[i];
            }
        }

        return null;

    }

    function getErrorRoomTab(tabid)
    {
        for (var i = 0; i < _arr_errors.length; i++)
        {
            if (_arr_errors[i].tabid == "rooms")
            {
                for (var j = 0; j < _arr_errors[i].tabs.length; j++)
                {
                    if (_arr_errors[i].tabs[j].tabid == tabid)
                    {
                        return _arr_errors[i].tabs[j];
                    }
                }
            }
        }

        return null;
    }

    function resetRoomTabsIcons()
    {
        grid_room_choices.cells("minstay", 0).setValue("<img src=\"images/bed.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("capacity", 0).setValue("<img src=\"images/pax.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("policies", 0).setValue("<img src=\"images/front_desk.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("meal", 0).setValue("<img src=\"images/hot_chocolate.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("adult_policies", 0).setValue("<img src=\"images/adult_72.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("child_policies", 0).setValue("<img src=\"images/child_72.png\" width=\"30px\" height=\"30px\">");
        grid_room_choices.cells("single_parent", 0).setValue("<img src=\"images/interview.png\" width=\"30px\" height=\"30px\">");

    }

    function displayProblems()
    {
        //get the current tab
        //if is room tab, get tree node selectedl
        //  if treenode is room, then display room related gap errors
        //  if treenode is date, then get inner tab and display room.innertab related errors
        //end if

        grid_problem.clearAll();

        var maintabid = tabViews.getActiveTab();

        //===================================================
        if (maintabid == "rooms")
        {
            var subtabid = tabRoomViews.getActiveTab();

            var nodeid = null;

            if (tree_roomdates)
            {
                nodeid = tree_roomdates.getSelectedItemId();
            }

            if (!nodeid)
            {
                return;
            }

            var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
            if (node == "ROOM")
            {
                var obj = getErrorTab(maintabid);
                var roomid = tree_roomdates.getUserData(nodeid, "ROOM_ROOMID");

                //get date gaps associated with the room
                var arrdategaps = obj.date_gaps;
                for (var i = 0; i < arrdategaps.length; i++)
                {
                    var errroomid = arrdategaps[i].roomid;
                    if (errroomid == roomid)
                    {
                        var gaps = arrdategaps[i].gaps;
                        for (var j = 0; j < gaps.length; j++)
                        {
                            var rwid = j + 1;

                            //reformat from dd-mm-yyyy to yyyy-mm-dd
                            var dtfrom = utils_date_to_str(utils_createDateObjFromString(gaps[j].from, "dd-mm-yyyy"));
                            dtfrom = utils_formatDate(dtfrom, "DATE MON YEAR");

                            //reformat from dd-mm-yyyy to yyyy-mm-dd
                            var dtto = utils_date_to_str(utils_createDateObjFromString(gaps[j].until, "dd-mm-yyyy"));
                            dtto = utils_formatDate(dtto, "DATE MON YEAR");

                            var txt = "Gap : <b>" + dtfrom + "</b> - <b>" + dtto + "</b>";
                            grid_problem.addRow(rwid, [rwid, txt]);
                            grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        }
                    }
                }

            } else if (node == "DATE")
            {
                var obj = getErrorRoomTab(subtabid);
                var arrerr = obj.err;
                var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
                var date_rwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");


                if (subtabid == "meal")
                {
                    for (var i = 0; i < arrerr.length; i++)
                    {
                        //select date node in callback
                        var callback = "";
                        callback = "tree_roomdates.selectItem(\"DATE_" + date_rwid + "\", true, false);";

                        var mealtype = arrerr[i].mealtype;
                        var msg = arrerr[i].msg;
                        var daterwid = arrerr[i].daterwid;
                        var rwid = arrerr[i].rwid;

                        if (daterwid == date_rwid)
                        {
                            if (mealtype == "meal")
                            {
                                callback += "accord_meal.cells(\"meal\").open(); grid_meals.selectRowById(\"" + rwid + "\");";
                            } else if (mealtype == "extra")
                            {
                                callback += "accord_meal.cells(\"extra\").open(); grid_extras.selectRowById(\"" + rwid + "\");";
                            }

                            var rwid = i + 1;
                            grid_problem.addRow(rwid, [rwid, "<b>" + mealtype + ":</b> " + msg, callback]);
                            grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        }


                    }
                } else if (subtabid == "minstay")
                {
                    for (var i = 0; i < arrerr.length; i++)
                    {
                        //select date node in callback
                        var callback = "";
                        callback = "tree_roomdates.selectItem(\"DATE_" + date_rwid + "\", true, false);";

                        var msg = arrerr[i].msg;
                        var minstayrwid = arrerr[i].minstayrwid;
                        var daterwid = arrerr[i].daterwid;

                        if (daterwid == date_rwid)
                        {
                            var rwid = i + 1;
                            grid_problem.addRow(rwid, [rwid, msg, "grid_minstay.selectRowById(\"" + minstayrwid + "\");", callback]);
                            grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        }

                    }
                } else if (subtabid == "policies")
                {
                    for (var i = 0; i < arrerr.length; i++)
                    {
                        //select date node in callback
                        var callback = "";
                        callback = "tree_roomdates.selectItem(\"DATE_" + date_rwid + "\", true, false);";

                        var policytype = arrerr[i].policytype;
                        var msg = arrerr[i].msg;
                        var rwid = arrerr[i].rwid;
                        var daterwid = arrerr[i].daterwid;

                        if (daterwid == date_rwid)
                        {
                            if (policytype == "checkinout")
                            {
                                callback += "accord_policy.cells(\"checkinout\").open(); grid_checkinouts.selectRowById(\"" + rwid + "\");";
                            } else if (policytype == "cancellation")
                            {
                                callback += "accord_policy.cells(\"cancellation\").open(); grid_cancellation.selectRowById(\"" + rwid + "\");";
                            }

                            var rwid = i + 1;
                            grid_problem.addRow(rwid, [rwid, "<b>" + policytype + ":</b> " + msg, callback]);
                            grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        }

                    }
                } else if (subtabid == "capacity")
                {
                    for (var i = 0; i < arrerr.length; i++)
                    {
                        //select date node in callback
                        var callback = "";
                        callback = "tree_roomdates.selectItem(\"DATE_" + date_rwid + "\", true, false);";

                        var msg = arrerr[i].msg;
                        var rwid = arrerr[i].rwid;
                        var daterwid = arrerr[i].daterwid;

                        if (daterwid == date_rwid)
                        {
                            callback += "grid_capacity_age.selectRowById(\"" + rwid + "\");";

                            var rwid = i + 1;
                            grid_problem.addRow(rwid, [rwid, msg, callback]);
                            grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                        }

                    }
                }
            }
        }
        //===================================================
        else if (maintabid == "tax")
        {
            var obj = getErrorTab(maintabid);
            if (obj)
            {
                var arr = obj.err;
                for (var i = 0; i < arr.length; i++)
                {

                    var nodeid = "";

                    //select the room node
                    if (arr[i].roomid == "GENERAL")
                    {
                        nodeid = arr[i].roomid;
                    } else
                    {
                        nodeid = "EXCEPTION_" + arr[i].roomid;
                    }

                    var callback = "";
                    callback = "accord_taxcommi.cells(\"" + arr[i].buysell + "\").open();";
                    callback += "tree_taxcomm.selectItem(\"" + nodeid + "\", true, false);";

                    var rwid = i + 1;
                    grid_problem.addRow(rwid, [rwid, arr[i].msg, callback]);
                    grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                }
            }


        }
        //===================================================
        else
        {
            var obj = getErrorTab(maintabid);
            if (obj)
            {
                var arr = obj.err;
                for (var i = 0; i < arr.length; i++)
                {
                    var rwid = i + 1;
                    grid_problem.addRow(rwid, [rwid, arr[i].msg]);
                    grid_problem.setRowTextStyle(rwid, "color:red; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                }
            }
        }
    }

    function onProblemSelect(rid, cid)
    {
        var callback = grid_problem.cells(rid, grid_problem.getColIndexById("callback")).getValue();
        if (!callback || callback == "")
        {
            return;
        }

        eval(callback);
    }

    function filterDatePeriodsForCustom(arr_dates_ids_added, arr_dateperiods)
    {
        var arr = [];

        //get all date periods from arr_dateperiods where dates from and to are within checkin and checkout
        for (var j = 0; j < arr_dateperiods.length; j++)
        {
            var date_rwid = arr_dateperiods[j].date_rwid;
            var date_action = arr_dateperiods[j].date_action;
            if (date_action != "DELETE")
            {
                if (!arr_dates_ids_added.includes(date_rwid))
                {
                    arr.push(arr_dateperiods[j]);
                }

            }
        }

        return arr;
    }

    function filterDatePeriodsBySeason(season_obj, arr_dateperiods)
    {
        var arr = [];

        //get all date periods from arr_dateperiods where dates from and to are within checkin and checkout
        for (var j = 0; j < arr_dateperiods.length; j++)
        {
            var date_dtfrom = utils_formatDate(arr_dateperiods[j].date_dtfrom, "DD-MM-YYYY");
            var date_dtto = utils_formatDate(arr_dateperiods[j].date_dtto, "DD-MM-YYYY");

            if (date_dtfrom == "")
            {
                //set to contract start
                date_dtfrom = form_main.getItemValue("active_from", true);

            }
            if (date_dtto == "")
            {
                //set to contract end
                date_dtto = form_main.getItemValue("active_to", true);
            }

            var date_action = arr_dateperiods[j].date_action;
            if (date_action != "DELETE")
            {
                //now get each checkin and checkout dates for that season
                for (var k = 0; k < season_obj.arr_season_dates.length; k++)
                {
                    var checkin = season_obj.arr_season_dates[k].checkin;
                    var checkout = season_obj.arr_season_dates[k].checkout;

                    if (utils_validateDateOrder(checkin, date_dtfrom) &&
                            utils_validateDateOrder(date_dtto, checkout))
                    {
                        arr.push(arr_dateperiods[j]);
                    }

                }

            }
        }

        return arr;
    }


    function getSeasonDateNodeWithin(dtfrom, dtto, arr_seasons)
    {
        //get the season id where dtfrom and dtto fall within
        //return CUSTOM season if date outside season ranges
        //dtfrom and dtto in dd-mm-yyyy format

        if (dtfrom == "")
        {
            dtfrom = form_main.getItemValue("active_from", true);
        }

        if (dtto == "")
        {
            dtto = form_main.getItemValue("active_to", true);
        }


        for (var s = 0; s < arr_seasons.length; s++)
        {
            var season = arr_seasons[s].season;
            var seasonid = arr_seasons[s].seasonid;


            for (var k = 0; k < arr_seasons[s].arr_season_dates.length; k++)
            {
                var checkin = arr_seasons[s].arr_season_dates[k].checkin;
                var checkout = arr_seasons[s].arr_season_dates[k].checkout;

                if (utils_validateDateOrder(checkin, dtfrom) &&
                        utils_validateDateOrder(dtto, checkout))
                {
                    return {seasonid: seasonid, season: season,
                        checkin: checkin, checkout: checkout};
                }
            }
        }

        //no seasons found for the date
        //return custom season
        return {seasonid: "CUSTOM_SEASON_ID", season: "CUSTOM",
            checkin: "", checkout: ""};

    }

    function groupSeasons()
    {
        var arr = [];

        for (var i = 0; i < _dsDatePeriods.dataCount(); i++)
        {
            var item = _dsDatePeriods.item(_dsDatePeriods.idByIndex(i));
            var season = item.season;
            var seasonid = item.seasonfk;
            var checkin = item.checkin_dmy;
            var checkout = item.checkout_dmy;

            var season_obj = lookupseason(arr, seasonid);

            if (!season_obj)
            {
                var obj = {season: season, seasonid: seasonid, arr_season_dates: []};
                obj.arr_season_dates.push({checkin: checkin, checkout: checkout});
                arr.push(obj)
            } else
            {
                season_obj.arr_season_dates.push({checkin: checkin, checkout: checkout});
            }
        }

        arr.sort(function (a, b) {
            if (a.season < b.season)
                return -1;
            if (a.season > b.season)
                return 1;
            return 0;
        });

        return arr;
    }

    function lookupseason(arr, seasonid)
    {
        for (var i = 0; i < arr.length; i++)
        {
            if (arr[i].seasonid == seasonid)
            {
                return arr[i];
            }
        }
        return null;
    }

    function resetContractIds()
    {

        form_main.setItemValue("id", "-1");
        form_main.setItemValue("contractname", form_main.getItemValue("contractname") + " - COPY");
        form_main.setItemValue("invoice_text", form_main.getItemValue("invoice_text") + " - COPY");

        //===============================================================
        var currency_mapping = _json_exchangerates.currency_mapping;
        for (var i = 0; i < currency_mapping.length; i++)
        {
            if (currency_mapping[i].mapping_id > 0)
            {
                currency_mapping[i].mapping_id *= -1; //make it negative to be treated a new addition
            }
        }

        //===============================================================
        var exchange_rates = _json_exchangerates.exchange_rates;
        for (var i = 0; i < exchange_rates.length; i++)
        {
            if (exchange_rates[i].rates_id > 0)
            {
                exchange_rates[i].rates_id *= -1; //make it negative to be treated a new addition
            }
        }

        //===============================================================
        for (var i = 0; i < _json_taxcommi.length; i++)
        {
            var buying_settings = _json_taxcommi[i].buying_settings;
            var selling_settings = _json_taxcommi[i].selling_settings;

            for (var j = 0; j < buying_settings.length; j++)
            {
                if (buying_settings[j].setting_rwid > 0)
                {
                    buying_settings[j].setting_rwid *= -1;
                }
            }

            for (var j = 0; j < selling_settings.length; j++)
            {
                if (selling_settings[j].setting_rwid > 0)
                {
                    selling_settings[j].setting_rwid *= -1;
                }
            }
        }

        //===============================================================



        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_rwid > 0)
            {
                _json_capacity[i].room_rwid *= -1;
            }

            var arr_dates = _json_capacity[i].room_dates;
            for (var j = 0; j < arr_dates.length; j++)
            {
                if (arr_dates[j].date_rwid > 0)
                {
                    arr_dates[j].date_rwid *= -1;
                }

                //==================================================================
                var date_adultpolicies_rules = arr_dates[j].date_adultpolicies_rules;
                for (var a = 0; a < date_adultpolicies_rules.length; a++)
                {
                    if (date_adultpolicies_rules[a].rule_rwid > 0)
                    {
                        date_adultpolicies_rules[a].rule_rwid *= -1;
                    }

                    var rule_policy = date_adultpolicies_rules[a].rule_policy;
                    for (var r = 0; r < rule_policy.length; r++)
                    {
                        if (rule_policy[r].policy_rwid > 0)
                        {
                            rule_policy[r].policy_rwid *= -1;
                        }

                        var policy_values = rule_policy[r].policy_values;
                        for (var v = 0; v < policy_values.length; v++)
                        {
                            if (policy_values[v].value_rwid > 0)
                            {
                                policy_values[v].value_rwid *= -1;
                            }
                        }
                    }
                }

                //==================================================================
                var date_capacity_rules = arr_dates[j].date_capacity_rules;
                for (var a = 0; a < date_capacity_rules.length; a++)
                {
                    if (date_capacity_rules[a].rule_rwid > 0)
                    {
                        date_capacity_rules[a].rule_rwid *= -1;
                    }

                    var rule_capacity = date_capacity_rules[a].rule_capacity;
                    for (var c = 0; c < rule_capacity.length; c++)
                    {
                        if (rule_capacity[c].capacity_rwid > 0)
                        {
                            rule_capacity[c].capacity_rwid *= -1;
                        }
                    }
                }

                //==================================================================
                var date_childpolicies_rules = arr_dates[j].date_childpolicies_rules;
                for (var a = 0; a < date_childpolicies_rules.length; a++)
                {
                    if (date_childpolicies_rules[a].rule_rwid > 0)
                    {
                        date_childpolicies_rules[a].rule_rwid *= -1;
                    }

                    var rule_policy = date_childpolicies_rules[a].rule_policy;
                    for (var r = 0; r < rule_policy.length; r++)
                    {
                        if (rule_policy[r].policy_rwid > 0)
                        {
                            rule_policy[r].policy_rwid *= -1;
                        }

                        var policy_values = rule_policy[r].policy_values;
                        for (var v = 0; v < policy_values.length; v++)
                        {
                            if (policy_values[v].value_rwid > 0)
                            {
                                policy_values[v].value_rwid *= -1;
                            }
                        }
                    }
                }

                //==================================================================
                var date_mealextrasupplement_rules = arr_dates[j].date_mealextrasupplement_rules;
                for (var m = 0; m < date_mealextrasupplement_rules.length; m++)
                {
                    if (date_mealextrasupplement_rules[m].extra_rwid > 0)
                    {
                        date_mealextrasupplement_rules[m].extra_rwid *= -1;
                    }

                    var extra_children = date_mealextrasupplement_rules[m].extra_children;
                    for (var e = 0; e < extra_children.length; e++)
                    {
                        if (extra_children[e].child_rwid > 0)
                        {
                            extra_children[e].child_rwid *= -1;
                        }
                    }
                }

                //==================================================================
                var date_mealsupplement_rules = arr_dates[j].date_mealsupplement_rules;
                for (var m = 0; m < date_mealsupplement_rules.length; m++)
                {
                    if (date_mealsupplement_rules[m].meal_rwid > 0)
                    {
                        date_mealsupplement_rules[m].meal_rwid *= -1;
                    }

                    var meal_children = date_mealsupplement_rules[m].meal_children;
                    for (var e = 0; e < meal_children.length; e++)
                    {
                        if (meal_children[e].child_rwid > 0)
                        {
                            meal_children[e].child_rwid *= -1;
                        }
                    }
                }


                //==================================================================
                var date_minstay_rules = arr_dates[j].date_minstay_rules;
                for (var m = 0; m < date_minstay_rules.length; m++)
                {
                    if (date_minstay_rules[m].minstay_rwid > 0)
                    {
                        date_minstay_rules[m].minstay_rwid *= -1;
                    }
                }

                //==================================================================
                var date_policies_checkinout = arr_dates[j].date_policies_checkinout;
                for (var m = 0; m < date_policies_checkinout.length; m++)
                {
                    if (date_policies_checkinout[m].checkinout_rwid > 0)
                    {
                        date_policies_checkinout[m].checkinout_rwid *= -1;
                    }
                }

                //==================================================================
                var date_policies_cancellation = arr_dates[j].date_policies_cancellation;
                for (var m = 0; m < date_policies_cancellation.length; m++)
                {
                    if (date_policies_cancellation[m].cancellation_rwid > 0)
                    {
                        date_policies_cancellation[m].cancellation_rwid *= -1;
                    }
                }

                //==================================================================
                var date_singleparentpolicies_rules = arr_dates[j].date_singleparentpolicies_rules;
                for (var m = 0; m < date_singleparentpolicies_rules.length; m++)
                {
                    if (date_singleparentpolicies_rules[m].rule_rwid > 0)
                    {
                        date_singleparentpolicies_rules[m].rule_rwid *= -1;
                    }

                    var rule_policy = date_singleparentpolicies_rules[m].rule_policy;
                    for (var r = 0; r < rule_policy.length; r++)
                    {
                        if (rule_policy[r].policy_rwid > 0)
                        {
                            rule_policy[r].policy_rwid *= -1;
                        }

                        var policy_values = rule_policy[r].policy_values;
                        for (var v = 0; v < policy_values.length; v++)
                        {
                            if (policy_values[v].value_rwid > 0)
                            {
                                policy_values[v].value_rwid *= -1;
                            }
                        }
                    }
                }
            }
        }


    }

    function taxCommiSettingsProfile()
    {
        popupwin_contracts.setModal(false);
        popupwin_profile_taxcomm.show();
        popupwin_profile_taxcomm.center();
        popupwin_profile_taxcomm.setModal(true);

        loadTaxCommiProfile("", false);

    }

    function loadTaxCommiProfile(selectid, openeditor)
    {
        profile_taxcomm_layout.cells("a").progressOn();
        grid_profile_taxcomm.clearAll();

        var dsProfile = new dhtmlXDataStore();

        dsProfile.load("php/api/hotelcontracts/profilegrid.php?t=" + encodeURIComponent(global_token), "json", function () {
            profile_taxcomm_layout.cells("a").progressOff();

            grid_profile_taxcomm.sync(dsProfile);

            grid_profile_taxcomm.forEachRow(function (rwid) {
                grid_profile_taxcomm.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_profile_taxcomm.setCellTextStyle(rwid, ind, cellstyle);
                });
            });

            if (selectid != "")
            {
                grid_profile_taxcomm.selectRowById(selectid, false, true, true);

                if (openeditor)
                {
                    grid_profile_taxcomm.selectCell(grid_profile_taxcomm.getRowIndex(selectid), 0, false, false, true, true);
                }
            }
        });
    }

    function deleteSettingsProfile()
    {

        var pid = grid_profile_taxcomm.getSelectedRowId();
        if (!pid)
        {
            return;
        }

        dhtmlx.confirm({
            title: "Delete Profile",
            type: "confirm",
            text: "Confirm Deletion?",
            callback: function (tf) {
                if (tf)
                {
                    profile_taxcomm_layout.progressOn();
                    var params = "pid=" + pid + "&t=" + encodeURIComponent(global_token);
                    dhtmlxAjax.post("php/api/hotelcontracts/deleteprofile.php", params, function (loader) {
                        profile_taxcomm_layout.progressOff();
                        if (loader)
                        {
                            if (loader.xmlDoc.responseURL == "")
                            {
                                dhtmlx.alert({
                                    text: "Connection Lost!",
                                    type: "alert-warning",
                                    title: "DELETE PROFILE",
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
                                    title: "DELETE PROFILE",
                                    callback: function () {
                                    }
                                });
                                return false;
                            }
                            if (json_obj.OUTCOME == "OK")
                            {
                                grid_profile_taxcomm.deleteRow(pid);

                            } else
                            {
                                dhtmlx.alert({
                                    text: json_obj.OUTCOME,
                                    type: "alert-warning",
                                    title: "DELETE PROFILE",
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

    function profileEdit(stage, rId, cInd, nValue, oValue) {
        if (stage == 2)
        {
            if (nValue != oValue)
            {
                var colid = grid_profile_taxcomm.getColumnId(cInd);
                nValue = utils_trim(nValue, " ");
                if (nValue == "")
                {
                    return false;
                }

                profile_taxcomm_layout.progressOn();
                var params = "id=" + rId + "&colid=" + colid + "&val=" + encodeURIComponent(nValue) + "&t=" + encodeURIComponent(global_token);
                dhtmlxAjax.post("php/api/hotelcontracts/updateprofile.php", params, function (loader) {
                    profile_taxcomm_layout.progressOff();
                    if (loader)
                    {
                        if (loader.xmlDoc.responseURL == "")
                        {
                            dhtmlx.alert({
                                text: "Connection Lost!",
                                type: "alert-warning",
                                title: "UPDATE PROFILE",
                                callback: function () {
                                    grid_profile_taxcomm.doUndo();
                                }
                            });
                            return;
                        }

                        var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

                        if (!json_obj)
                        {
                            dhtmlx.alert({
                                text: loader.xmlDoc.responseText,
                                type: "alert-warning",
                                title: "UPDATE PROFILE",
                                callback: function () {
                                    grid_profile_taxcomm.doUndo();
                                }
                            });
                            return;
                        }
                        if (json_obj.OUTCOME != "OK")
                        {

                            dhtmlx.alert({
                                text: json_obj.OUTCOME,
                                type: "alert-warning",
                                title: "UPDATE PROFILE",
                                callback: function () {
                                    grid_profile_taxcomm.doUndo();
                                }
                            });
                        }
                    }
                });
            }
        }

        return true;
    }

    function rememberProfileState(profileid)
    {
        var nodeid = tree_taxcomm.getSelectedItemId();
        var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

        var obj = lookupTaxCommiRoomObj(roomid);
        if (obj)
        {
            var buying_settings = JSON.stringify(obj.buying_settings);
            var selling_settings = JSON.stringify(obj.selling_settings);

            profile_taxcomm_layout.progressOn();
            var params = "id=" + profileid +
                    "&buying_settings=" + encodeURIComponent(buying_settings) +
                    "&selling_settings=" + encodeURIComponent(selling_settings) +
                    "&t=" + encodeURIComponent(global_token);

            dhtmlxAjax.post("php/api/hotelcontracts/saveprofile.php", params, function (loader) {
                profile_taxcomm_layout.progressOff();
                if (loader)
                {
                    if (loader.xmlDoc.responseURL == "")
                    {
                        dhtmlx.alert({
                            text: "Connection Lost!",
                            type: "alert-warning",
                            title: "SAVE PROFILE",
                            callback: function () {
                            }
                        });
                        return;
                    }

                    var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

                    if (!json_obj)
                    {
                        dhtmlx.alert({
                            text: loader.xmlDoc.responseText,
                            type: "alert-warning",
                            title: "SAVE PROFILE",
                            callback: function () {
                            }
                        });
                        return;
                    }
                    if (json_obj.OUTCOME == "OK")
                    {
                        var select_id = json_obj.ID;
                        var openeditor = false;
                        if (profileid == "-1")
                        {
                            openeditor = true;
                        }
                        loadTaxCommiProfile(select_id, openeditor);
                    } else
                    {

                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "SAVE PROFILE",
                            callback: function () {
                            }
                        });
                    }
                }
            });
        }
    }

    function rememberOverProfileState()
    {
        var pid = grid_profile_taxcomm.getSelectedRowId();
        if (!pid)
        {
            return;
        }

        dhtmlx.confirm({
            title: "Overwrite Save Profile",
            type: "confirm",
            text: "Confirm <b>overwriting</b> currently selected Profile?",
            callback: function (tf) {
                if (tf)
                {
                    rememberProfileState(pid);
                }
            }
        });

    }


    function loadSettingsProfile()
    {
        var pid = grid_profile_taxcomm.getSelectedRowId();
        if (!pid)
        {
            return;
        }

        profile_taxcomm_layout.progressOn();
        var params = "id=" + pid +
                "&t=" + encodeURIComponent(global_token);

        dhtmlxAjax.post("php/api/hotelcontracts/loadprofile.php", params, function (loader) {
            profile_taxcomm_layout.progressOff();
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD PROFILE",
                        callback: function () {
                        }
                    });
                    return;
                }

                var json_obj = utils_response_extract_jsonobj(loader, false, "", "");

                if (!json_obj)
                {
                    dhtmlx.alert({
                        text: loader.xmlDoc.responseText,
                        type: "alert-warning",
                        title: "LOAD PROFILE",
                        callback: function () {
                        }
                    });
                    return;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    var arr_buys = json_obj.BUY;
                    var arr_sells = json_obj.SELL;

                    var nodeid = tree_taxcomm.getSelectedItemId();
                    var roomid = tree_taxcomm.getUserData(nodeid, "ROOMID");

                    var obj = lookupTaxCommiRoomObj(roomid);
                    if (obj)
                    {
                        obj.buying_settings = []; //clear array
                        obj.selling_settings = []; //clear array
                    }


                    pushProfileTaxCommiItems(arr_buys, "BUYING", obj.buying_settings);
                    pushProfileTaxCommiItems(arr_sells, "SELLING", obj.selling_settings);

                    onTaxCommTreeNodeSelect(nodeid);

                    popupwin_profile_taxcomm.setModal(false);
                    popupwin_profile_taxcomm.hide();
                    popupwin_contracts.setModal(true);

                } else
                {

                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD PROFILE",
                        callback: function () {
                        }
                    });
                }
            }
        });


    }

    function pushProfileTaxCommiItems(arr_buys_sells, buying_selling, arr_to_push)
    {
        for (var i = 0; i < arr_buys_sells.length; i++)
        {

            _taxcommi_settings_id--;
            var obj = {
                setting_rwid: _taxcommi_settings_id,
                setting_buying_selling: buying_selling,
                setting_row_index: arr_buys_sells[i].row_index,
                setting_item_fk: arr_buys_sells[i].item_fk,
                setting_item_name: arr_buys_sells[i].item_name,
                setting_item_abbrv: arr_buys_sells[i].abbrv,
                setting_item_code: arr_buys_sells[i].code,
                setting_core_addon: arr_buys_sells[i].core_addon,
                setting_basis: arr_buys_sells[i].basis,
                setting_applyon_formula: arr_buys_sells[i].formula,
                setting_rounding: arr_buys_sells[i].rounding,
                setting_action: "INSERT",
                setting_values: []
            };

            arr_to_push.push(obj);
        }

    }

    grid_choices.selectRowById("currency", false, true, true);
    popupwin_capacitydates.hide();
    popupwin_loadperiods.hide();
    popupwin_capacitycombinations.hide();
    popupwin_testtaxcomm.hide();
    popupwin_profile_taxcomm.hide();

}