var ratescalculator_obj = new ratescalculator();

function ratescalculator() {

    var _children_id = 1;
    var _adult_id = 1;

    document.getElementById("aTitle").innerHTML = "RATES CALCULATOR";

    var main_layout = new dhtmlXLayoutObject("main_body", "2E");
    main_layout.cells("a").hideHeader();
    main_layout.cells("b").hideHeader();

    
    var tabViews = main_layout.cells("a").attachTabbar();
    tabViews.addTab("params", "Contract and SPO Parameters", "200px", '');
    tabViews.addTab("results", "Calculator Results", "180px", '');
    tabViews.setTabActive("params");

    var param_layout = tabViews.cells("params").attachLayout("3W");
    param_layout.cells("a").hideHeader();
    param_layout.cells("a").setWidth(900);
    param_layout.cells("b").setWidth(300);
    //param_layout.cells("c").setWidth(100);

    param_layout.cells("b").setText("Adults");
    param_layout.cells("c").setText("Children");

    var grid_adult = param_layout.cells("b").attachGrid();
    grid_adult.setIconsPath('libraries/dhtmlx/imgs/');
    grid_adult.setHeader("Index,Age,Bride/Groom");
    grid_adult.setColumnIds("count,age,bride_groom");
    grid_adult.setColTypes("ro,edn,combo");
    grid_adult.setInitWidths("50,50,100");
    grid_adult.setColAlign("center,center,center");
    grid_adult.setColSorting('na,na,na');
    grid_adult.enableAlterCss("", "");
    grid_adult.enableEditTabOnly(true);
    grid_adult.enableEditEvents(true, true, true);
    grid_adult.init();

    var cbo_bride_groom = grid_adult.getColumnCombo(grid_adult.getColIndexById("bride_groom"));
    cbo_bride_groom.addOption([{value: "", text: "---"}, {value: "BRIDE", text: "BRIDE"}, {value: "GROOM", text: "GROOM"}]);
    cbo_bride_groom.readonly(true);

    var toolbar_adult = param_layout.cells("b").attachToolbar();
    toolbar_adult.setIconsPath("images/");
    toolbar_adult.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_adult.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_adult.setIconSize(32);

    toolbar_adult.attachEvent("onClick", function (id) {

        if (id == "new")
        {
            _adult_id++;
            grid_adult.addRow(_adult_id, ["", "", "---"]);
            grid_adult.setRowTextStyle(_adult_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            resetAdultChildrenCounter();
        } else if (id == "delete")
        {
            var rid = grid_adult.getSelectedRowId();
            if (rid)
            {
                grid_adult.deleteRow(rid);

                resetAdultChildrenCounter();
            }
        }
    });


    var grid_children = param_layout.cells("c").attachGrid();
    grid_children.setIconsPath('libraries/dhtmlx/imgs/');
    grid_children.setHeader("Index,Age,Sharing/Own Room");
    grid_children.setColumnIds("count,age,sharing_own");
    grid_children.setColTypes("ro,edn,combo");
    grid_children.setInitWidths("50,50,100");
    grid_children.setColAlign("center,center,center");
    grid_children.setColSorting('na,na,na');
    grid_children.enableAlterCss("", "");
    grid_children.enableEditTabOnly(true);
    grid_children.enableEditEvents(true, true, true);
    grid_children.init();

    var cbo_sharing_own = grid_children.getColumnCombo(grid_children.getColIndexById("sharing_own"));
    cbo_sharing_own.addOption([{value: "SHARING", text: "SHARING"}, {value: "OWN", text: "OWN"}]);
    cbo_sharing_own.readonly(true);

    var toolbar_children = param_layout.cells("c").attachToolbar();
    toolbar_children.setIconsPath("images/");
    toolbar_children.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_children.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_children.setIconSize(32);

    toolbar_children.attachEvent("onClick", function (id) {

        if (id == "new")
        {
            _children_id++;
            grid_children.addRow(_children_id, ["", "", "SHARING"]);
            grid_children.setRowTextStyle(_children_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            resetAdultChildrenCounter();
        } else if (id == "delete")
        {
            var rid = grid_children.getSelectedRowId();
            if (rid)
            {
                grid_children.deleteRow(rid);

                resetAdultChildrenCounter();
            }
        }
    });

    //cancellation??
    //extra supplement??
    var str_frm_exec = [
        {type: "settings", position: "label-left", id: "form_exec"},
        {type: "button", name: "cmdTest", value: "Launch Rates Calculator", width: "300", offsetLeft: 200}
    ];

    var form_exec = main_layout.cells("b").attachForm(str_frm_exec);

    form_exec.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdTest")
        {
            testRatesCalculator();
        }
    });

    var accordConSPO = param_layout.cells("a").attachAccordion();
    accordConSPO.addItem("contract", "Contract");
    accordConSPO.addItem("spo", "SPO");
    accordConSPO.openItem("contract");


    var str_frm_calc = [
        {type: "settings", position: "label-left", id: "form_calculator"},

        {type: "block", width: 400, list: [
                {type: "calendar", name: "checkin_date", label: "Check In:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "input", name: "checkin_time", label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    note: {
                        text: "HH:mm"
                    }
                }]},
        {type: "block", width: 400, list: [
                {type: "calendar", name: "checkout_date", label: "Check Out:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "122",
                    dateFormat: "%d-%m-%Y", required: true,
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "input", name: "checkout_time", label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    note: {
                        text: "HH:mm"
                    }
                }]},
        {type: "combo", name: "mealplan", label: "Main Meal Plan:", labelWidth: "150",
            labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "supp_mealplan", label: "Supp Meal Plan:", labelWidth: "150",
            labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "country", label: "Country (Market):", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "touroperator", label: "Tour Operator:", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "rate", label: "Rate:", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "hotel", label: "Hotel:", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "hotelroom", label: "Hotel Room:", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },

        {type: "input", name: "contractids", label: "Test with Contracts:",
            labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            note: {
                text: "Comma separated Contract Ids"
            }

        },
        {type: "combo", name: "extra_mealsupp", label: "Extra Meal Supplement:", labelWidth: "150",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            comboType: "checkbox",
            comboImagePath: "../../images/"
        }
    ];

    var form_calc = accordConSPO.cells("contract").attachForm(str_frm_calc);

    form_calc.attachEvent("onChange", function (id, value) {
        if (id == "checkin_date" || id == "checkout_date")
        {
            lookup_daily_contract_ids();
        }
    });

    form_calc.getCombo("country").attachEvent("onChange", onCountryChange);
    form_calc.getCombo("touroperator").attachEvent("onChange", onTOChange);
    form_calc.getCombo("hotel").attachEvent("onChange", onHotelChange);


    form_calc.getCombo("mealplan").enableOptionAutoPositioning(true);
    form_calc.getCombo("supp_mealplan").enableOptionAutoPositioning(true);

    var dsMealPlan = new dhtmlXDataStore();
    dsMealPlan.load("php/api/combos/mealplan_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        form_calc.getCombo("supp_mealplan").addOption([{value: "", text: "-- NONE --", img_src: "images/hot_chocolate.png_32x32.png"}]);

        for (var i = 0; i < dsMealPlan.dataCount(); i++)
        {
            var item = dsMealPlan.item(dsMealPlan.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            form_calc.getCombo("mealplan").addOption([{value: value, text: txt, img_src: "images/hot_chocolate.png_32x32.png"}]);
            form_calc.getCombo("supp_mealplan").addOption([{value: value, text: txt, img_src: "images/hot_chocolate.png_32x32.png"}]);
        }

        form_calc.getCombo("supp_mealplan").setComboValue("");
        form_calc.getCombo("mealplan").readonly(true);
        form_calc.getCombo("supp_mealplan").readonly(true);
    });


    form_calc.getCombo("country").enableOptionAutoPositioning(true);
    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/combos/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCountry.dataCount(); i++)
        {
            var item = dsCountry.item(dsCountry.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            form_calc.getCombo("country").addOption([{value: value, text: txt, img_src: "images/country.png"}]);
        }

        form_calc.getCombo("country").readonly(false);
        form_calc.getCombo("country").enableFilteringMode(true);

    });


    form_calc.getCombo("hotel").enableOptionAutoPositioning(true);
    var dsHotel = new dhtmlXDataStore();
    dsHotel.load("php/api/ratescalculator/hotel_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsHotel.dataCount(); i++)
        {
            var item = dsHotel.item(dsHotel.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            form_calc.getCombo("hotel").addOption([{value: value, text: txt, img_src: "images/hotel.png"}]);
        }

        form_calc.getCombo("hotel").readonly(false);
        form_calc.getCombo("hotel").enableFilteringMode(true);
    });


    jQuery(function ($) {
        $("[name='checkin_date']").mask("99-99-9999");
        $("[name='checkout_date']").mask("99-99-9999");
        $("[name='checkin_time']").mask("99:99");
        $("[name='checkout_time']").mask("99:99");
    });
    
    main_layout.cells("a").setHeight(900);
    main_layout.cells("b").setHeight(50);
    main_layout.cells("a").fixSize(true, true);
    resizeLayout();
    //=======================================================================


    var str_frm_spo = [
        {type: "settings", position: "label-left", id: "form_spo"},

        {type: "combo", name: "spo_type", label: "SPO Type:", labelWidth: "170",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },

        {type: "calendar", name: "spo_booking_date", label: "Booking Date:",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "170",
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        },
        {type: "calendar", name: "spo_travel_date", label: "Trave Date:",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", labelWidth: "170",
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        },

        {type: "input", name: "spo_party_pax", label: "Additional of Pax in Party:", labelWidth: "170",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
        },
        {type: "checkbox", name: "spo_discount", label: "Apply Test Discount", width: 800, list: [
                {type: "input", name: "spo_discount_room_percentage", label: "Room Only (%)", labelWidth: "150",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
                },
                {type: "input", name: "spo_discount_all_percentage", label: "All (%)", labelWidth: "150",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
                },
                {type: "input", name: "spo_discount_PPPN", label: "Flat PPPN", labelWidth: "150",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
                },
                {type: "input", name: "spo_discount_PNI", label: "Flat PNI", labelWidth: "150",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
                }]},
        {type: "checkbox", name: "spo_free_nights", label: "Apply Free Nights", width: 800, list: [
                {type: "input", name: "spo_free_nights_num_nights", label: "No Free Nights", labelWidth: "170",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",validate: "ValidNumeric"
                },
                
                {type: "combo", name: "spo_free_nights_start_end", label: "Start/End:", labelWidth: "170",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                }]},
        {type: "block", width: 800, list: [
                {type: "checkbox", name: "spo_ishoneymoon", label: "Is Honey Moon"},
                {type: "newcolumn"},
                {type: "checkbox", name: "spo_isweddingparty", label: "Is Wedding Party"},
                {type: "newcolumn"},
                {type: "checkbox", name: "spo_isweddinganniv", label: "Is Wedding Anniversary"}
            ]}
    ];

    var form_spo = accordConSPO.cells("spo").attachForm(str_frm_spo);
    
    form_spo.getCombo("spo_type").addOption([{value: "contractual", text: "CONTRACTUAL"}, {value: "tactical", text: "TACTICAL"}, {value: "BOTH", text: "BOTH"}]);
    form_spo.getCombo("spo_type").readonly(true);
    form_spo.getCombo("spo_type").setComboValue("BOTH");
    
    form_spo.getCombo("spo_free_nights_start_end").addOption([{value: "START", text: "START"}, {value: "END", text: "END"},{value: "LOWEST", text: "LOWEST NIGHTS"}]);
    form_spo.getCombo("spo_free_nights_start_end").readonly(true);
    form_spo.getCombo("spo_free_nights_start_end").setComboValue("START");
    
    
       
    
    //======================================================================
    var results_layout = tabViews.cells("results").attachLayout("1C");
    results_layout.cells("a").hideHeader();



    var grid_results = results_layout.cells("a").attachGrid();
    grid_results.setIconsPath('libraries/dhtmlx/imgs/');
    grid_results.init();

    var toolbarResults = results_layout.cells("a").attachToolbar();
    toolbarResults.setIconsPath("images/");
    toolbarResults.addText("text", 1, "");
    toolbarResults.addText("timer", 2, "");
    toolbarResults.addSpacer("text");
    toolbarResults.setIconSize(32);


    //========================================================================



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

            //====================

            $("#main_body").height(y - 25);
            $("#main_body").width(x - 20);

            main_layout.setSizes(true);

        }, 1);
    }


    function onHotelChange()
    {
        var hoid = form_calc.getCombo("hotel").getSelectedValue();
        form_calc.getCombo("hotelroom").clearAll(true);
        form_calc.getCombo("hotelroom").setComboText("Loading...");

        var dsRate = new dhtmlXDataStore();

        dsRate.load("php/api/ratescalculator/hotelroom_combo.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {

            form_calc.getCombo("hotelroom").setComboText("");

            for (var i = 0; i < dsRate.dataCount(); i++)
            {
                var item = dsRate.item(dsRate.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                form_calc.getCombo("hotelroom").addOption([{value: value, text: txt, img_src: "images/room_32.png"}]);
            }

            form_calc.getCombo("hotelroom").readonly(false);
            form_calc.getCombo("hotelroom").enableFilteringMode(true);
        });
    }

    function onTOChange()
    {
        var toid = form_calc.getCombo("touroperator").getSelectedValue();
        form_calc.getCombo("rate").clearAll(true);
        form_calc.getCombo("rate").setComboText("Loading...");

        var dsRate = new dhtmlXDataStore();

        dsRate.load("php/api/ratescalculator/rate_combo.php?t=" + encodeURIComponent(global_token) + "&toid=" + toid, "json", function () {

            form_calc.getCombo("rate").setComboText("");

            for (var i = 0; i < dsRate.dataCount(); i++)
            {
                var item = dsRate.item(dsRate.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                form_calc.getCombo("rate").addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
            }

            form_calc.getCombo("rate").readonly(true);

        });

    }

    function onCountryChange()
    {
        var countryid = form_calc.getCombo("country").getSelectedValue();

        form_calc.getCombo("touroperator").clearAll(true);
        form_calc.getCombo("rate").clearAll(true);
        form_calc.getCombo("touroperator").setComboText("Loading...");
        form_calc.getCombo("rate").setComboText("");

        var dsTO = new dhtmlXDataStore();

        dsTO.load("php/api/ratescalculator/to_combo.php?t=" + encodeURIComponent(global_token) + "&countryfk=" + countryid, "json", function () {

            form_calc.getCombo("touroperator").setComboText("");

            for (var i = 0; i < dsTO.dataCount(); i++)
            {
                var item = dsTO.item(dsTO.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                form_calc.getCombo("touroperator").addOption([{value: value, text: txt, img_src: "images/area.png"}]);
            }


            form_calc.getCombo("touroperator").readonly(false);
            form_calc.getCombo("touroperator").enableFilteringMode(true);
        });
    }


    function resetAdultChildrenCounter()
    {
        for (var i = 0; i < grid_adult.getRowsNum(); i++)
        {
            var rowid = grid_adult.getRowId(i);
            grid_adult.cells(rowid, grid_adult.getColIndexById("count")).setValue((i + 1));
        }

        for (var i = 0; i < grid_children.getRowsNum(); i++)
        {
            var rowid = grid_children.getRowId(i);
            grid_children.cells(rowid, grid_children.getColIndexById("count")).setValue((i + 1));
        }
    }


    function testRatesCalculator()
    {
        toolbarResults.setItemText("text", "");
        toolbarResults.setItemText("timer", "");

        if (!form_calc.validate())
        {
            tabViews.setTabActive("params");
            accordConSPO.openItem("contract");
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Test Rates Calculator",
                callback: function () {
                }
            });
            return;
        }



        if (grid_adult.getRowsNum() == 0 && grid_children.getRowsNum() == 0)
        {
            tabViews.setTabActive("params");
            accordConSPO.openItem("contract");
            dhtmlx.alert({
                text: "Please enter at least ONE Adult or Child",
                type: "alert-warning",
                title: "Test Rates Calculator",
                callback: function () {
                }
            });
            return;
        }

        if (!validate_filter_contractids())
        {

            tabViews.setTabActive("params");
            accordConSPO.openItem("contract");
            dhtmlx.alert({
                text: "Please enter proper <b>Contract ID</b> (comma separated if needed)",
                type: "alert-warning",
                title: "Test Rates Calculator",
                callback: function () {
                    form_calc.setItemFocus("contractids");
                }
            });
            return;
        }

        if (!validate_spos())
        {
            spo_discount
        }

        //=================================
        //collect values
        var main_details = form_calc.getFormData();

        main_details.checkin_date = utils_date_to_str(utils_createDateObjFromString(form_calc.getItemValue("checkin_date", true), "dd-mm-yyyy"));
        main_details.checkout_date = utils_date_to_str(utils_createDateObjFromString(form_calc.getItemValue("checkout_date", true), "dd-mm-yyyy"));

        var spo_details = form_spo.getFormData();
        //=================================
        var arradults = [];
        for (var i = 0; i < grid_adult.getRowsNum(); i++)
        {
            var rowid = grid_adult.getRowId(i);
            var count = grid_adult.cells(rowid, grid_adult.getColIndexById("count")).getValue();
            var age = grid_adult.cells(rowid, grid_adult.getColIndexById("age")).getValue();
            var bride_groom = grid_adult.cells(rowid, grid_adult.getColIndexById("bride_groom")).getValue();

            arradults.push({count: count, age: age, bride_groom: bride_groom});
        }
        main_details.adults = arradults;

        //=================================
        var arrchildren = [];
        for (var i = 0; i < grid_children.getRowsNum(); i++)
        {
            var rowid = grid_children.getRowId(i);
            var count = grid_children.cells(rowid, grid_children.getColIndexById("count")).getValue();
            var age = grid_children.cells(rowid, grid_children.getColIndexById("age")).getValue();
            var sharing_own = grid_children.cells(rowid, grid_children.getColIndexById("sharing_own")).getValue();

            arrchildren.push({count: count, age: age, sharing_own: sharing_own});
        }
        main_details.children = arrchildren;

        //=================================


        grid_results.clearAll();


        param_layout.progressOn();
        var params = "params=" + encodeURIComponent(JSON.stringify(main_details)) +
                "&spo_params=" + encodeURIComponent(JSON.stringify(spo_details)) +
                "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/ratescalculator/testrates.php", params, function (loader) {
            param_layout.progressOff();
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "Test Rates Calculator",
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
                        title: "Test Rates Calculator",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    console.log(json_obj);

                    //all fine! contract without errors!
                    populateResultsGrid(json_obj);

                    tabViews.setTabActive("results");


                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "Test Rates Calculator",
                        callback: function () {
                        }
                    });
                }
            }
        });

    }


    function populateResultsGrid(json_obj)
    {
        try {

            results_layout.cells("a").progressOn();

            toolbarResults.setItemText("timer", "Searched in " + json_obj.RESULT.EXEC_TIME + " second(s)");

            var final_status = true;
            var arr = json_obj.RESULT.DAILY;
            var columns = json_obj.RESULT.COLUMNS;
            var currency_sell_code = "";
            var currency_buy_code = "";
            var rwid = 1;
            var parent_rwid = 1;

            //create the columns necessary in the results grid
            initialiseResultGrid(columns);
            var arr_grand_total = initialiseGrandTotal(columns);


            //now display for each date by date
            for (var i = 0; i < arr.length; i++)
            {
                currency_sell_code = arr[i].CURRENCY_SELL_CODE;
                currency_buy_code = arr[i].CURRENCY_BUY_CODE;
                var colspan = 1;

                var status = arr[i].STATUS;
                var date = arr[i].DATE;

                rwid++;
                parent_rwid = rwid;
                grid_results.addRow(rwid, [utils_formatDate(date, "DD-MM-YYYY")]);

                grid_results.cells(rwid, grid_results.getColIndexById("status")).setValue((status == "OK") ? "<b><font color='green'>" + status + "</font></b>" : "<b><font color='red'>" + status + "</font></b>");
                grid_results.setRowTextStyle(rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:3px solid black; border-right:1px solid #A4A4A4;");

                //and now for each workings and costings of that date
                var arr_costings_workings = arr[i].COSTINGS_WORKINGS;
                for (var j = 0; j < arr_costings_workings.length; j++)
                {
                    var message = arr_costings_workings[j].MSG;
                    var row_style = "";
                    if (message.includes("TOTAL"))
                    {
                        row_style = "font-weight:bold; background-color: #F9EEA6;"
                    }

                    if (j > 0)
                    {
                        //add a new row
                        colspan++;
                        rwid++;
                        grid_results.addRow(rwid, "");
                        grid_results.setRowTextStyle(rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;" + row_style);

                    }

                    grid_results.cells(rwid, grid_results.getColIndexById("comments")).setValue(message);

                    //now for each column
                    var columns = arr_costings_workings[j].COSTINGS;
                    for (var c = 0; c < columns.length; c++)
                    {
                        var colid = c + "_colid";
                        var value = columns[c].VALUE;
                        grid_results.cells(rwid, grid_results.getColIndexById(colid)).setValue(value);
                        if (message.includes("TOTAL"))
                        {
                            arr_grand_total[c] += parseFloat(value);
                        }
                    }
                }

                grid_results.setRowspan(parent_rwid, 0, colspan);
                grid_results.setRowspan(parent_rwid, 1, colspan);

                if (status != "OK")
                {
                    final_status = false;
                }
            }

            //=======================================================================
            //SUMMATION VALUES
            //DISPLAY GRAND TOTAL
            rwid++;
            row_style = "font-weight:bold; background-color: #F9EEA6;"
            grid_results.addRow(rwid, "");
            grid_results.setRowTextStyle(rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;" + row_style);
            grid_results.cells(rwid, grid_results.getColIndexById("comments")).setValue("GRAND TOTAL");

            //now for each column
            for (var c = 0; c < arr_grand_total.length; c++)
            {
                var colid = c + "_colid";
                var value = arr_grand_total[c].toFixed(2);
                grid_results.cells(rwid, grid_results.getColIndexById(colid)).setValue(value);

            }

            //=======================================================================

            if (!final_status)
            {
                //there has been an error during the search of rates!
                //there is no valid rates therefore!
                toolbarResults.setItemText("text", "<img height='24px' width='24px' src='images/cross.png'><b><font color='red'>UNSUCCESSFUL LOOKUP!</font></b>");

            } else
            {
                toolbarResults.setItemText("text", "<img src='images/tick.png'><b><font color='green'>SUCCESSFUL LOOKUP!</font></b>");
            }


            results_layout.cells("a").progressOff();

        } catch (err) {
            results_layout.cells("a").progressOff();
            console.log(err.message);
            dhtmlx.alert({
                text: err.message,
                type: "alert-warning",
                title: "Error Loading Results",
                callback: function () {
                }
            });
        }
    }


    this.destroy = function () {

    };

    function lookup_daily_contract_ids()
    {
        form_calc.getCombo("extra_mealsupp").clearAll(true);

        var checkindate = form_calc.getItemValue("checkin_date", true);
        var checkoutdate = form_calc.getItemValue("checkout_date", true);

        if (utils_isDate(checkindate) && utils_isDate(checkoutdate))
        {
            if (!utils_validateDateOrder(checkindate, checkoutdate))
            {

                return;
            }
        }

        //form_calc.getCombo("extra_mealsupp").addOption([{value: "INTERNAL", text: "INTERNAL"}, {value: "EXTERNAL", text: "EXTERNAL"}, {value: "BOTH", text: "BOTH"}]);

    }

    function validate_filter_contractids()
    {
        //make sure that contract ids are numeric and comma separated

        var filter_contract_ids = utils_trim(form_calc.getItemValue("contractids"), " ");
        if (filter_contract_ids != "")
        {
            var arr_ids = filter_contract_ids.split(",");

            for (var i = 0; i < arr_ids.length; i++)
            {
                var conid = utils_trim(arr_ids[i], " ");
                if (isNaN(conid) || conid == "")
                {
                    return false;
                }
            }

        }

        return true;
    }

    function initialiseGrandTotal(columns)
    {
        var arr = [];
        for (var i = 0; i < columns.length; i++)
        {
            arr[i] = 0;
        }

        return arr;

    }
    function initialiseResultGrid(columns)
    {
        var header = "Date,Status,Comments";
        var colids = "date,status,comments";
        var coltypes = "ro,ro,ro";
        var initwidth = "80,100,400";
        var colalign = "center,center,left";
        var colsorting = "na,na,na";

        for (var i = 0; i < columns.length; i++)
        {
            var caption = utils_toTitleCase(columns[i].CAPTION);
            var currency_code = columns[i].CURRENCY_CODE;
            var colid = i + "_colid";

            header += "," + caption + " (" + currency_code + ")";
            colids += "," + colid;
            coltypes += ",ro";
            initwidth += ",80";
            colalign += ",right";
            colsorting += ",na";

        }

        grid_results.clearAll(true);
        grid_results = null;
        grid_results = results_layout.cells("a").attachGrid();
        grid_results.setIconsPath('libraries/dhtmlx/imgs/');
        grid_results.setHeader(header);
        grid_results.setColumnIds(colids);
        grid_results.setColTypes(coltypes);
        grid_results.setInitWidths(initwidth);
        grid_results.setColAlign(colalign);
        grid_results.setColSorting(colsorting);
        grid_results.enableAlterCss("", "");
        grid_results.enableMultiline(true);
        grid_results.enableColSpan(true);
        grid_results.enableRowspan(true);
        grid_results.init();

        return;
    }

    function validate_spos()
    {   
        if (!form_spo.validate())
        {
            tabViews.setTabActive("params");
            accordConSPO.openItem("spo");
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Test Rates Calculator",
                callback: function () {
                }
            });
            return false;
        }
        

        if (form_spo.isItemChecked("spo_discount"))
        {         
            
            if(utils_trim(form_spo.getItemValue("spo_discount_room_percentage"), " ") == "")
            {
                form_spo.setItemValue("spo_discount_room_percentage","0");
            }
            if(utils_trim(form_spo.getItemValue("spo_discount_all_percentage"), " ") == "")
            {
                form_spo.setItemValue("spo_discount_all_percentage","0");
            }
            if(utils_trim(form_spo.getItemValue("spo_discount_flat"), " ") == "")
            {
                form_spo.setItemValue("spo_discount_flat","0");
            }
            if(utils_trim(form_spo.getItemValue("spo_discount_PPPN"), " ") == "")
            {
                form_spo.setItemValue("spo_discount_PPPN","0");
            }
            
            if(utils_trim(form_spo.getItemValue("spo_discount_PNI"), " ") == "")
            {
                form_spo.setItemValue("spo_discount_PNI","0");
            }
            
            var room_percentage = parseFloat(form_spo.getItemValue("spo_discount_room_percentage"));
            var all_percentage = parseFloat(form_spo.getItemValue("spo_discount_all_percentage"));
            var flat_pppn = parseFloat(form_spo.getItemValue("spo_discount_PPPN"));
            var flat_pni = parseFloat(form_spo.getItemValue("spo_discount_PNI"));
            
            if(room_percentage < 0 || room_percentage > 100)
            {
                tabViews.setTabActive("params");
                accordConSPO.openItem("spo");
                dhtmlx.alert({
                    text: "SPO Room Percentage Discount MUST be between 0 and 100 inclusive",
                    type: "alert-warning",
                    title: "Test Rates Calculator",
                    callback: function () {
                        form_spo.setItemFocus("spo_discount_room_percentage");
                    }
                });
                return false;
            }
            
            if(all_percentage < 0 || all_percentage > 100)
            {
                tabViews.setTabActive("params");
                accordConSPO.openItem("spo");
                dhtmlx.alert({
                    text: "SPO All Percentage Discount MUST be between 0 and 100 inclusive",
                    type: "alert-warning",
                    title: "Test Rates Calculator",
                    callback: function () {
                        form_spo.setItemFocus("spo_discount_all_percentage");
                    }
                });
                return false;
            }
            
            if(flat_pppn < 0)
            {
                tabViews.setTabActive("params");
                accordConSPO.openItem("spo");
                dhtmlx.alert({
                    text: "SPO Flat Discount MUST cannot be less than ZERO",
                    type: "alert-warning",
                    title: "Test Rates Calculator",
                    callback: function () {
                        form_spo.setItemFocus("spo_discount_PPPN");
                    }
                });
                return false;
            }
            
            if(flat_pni < 0)
            {
                tabViews.setTabActive("params");
                accordConSPO.openItem("spo");
                dhtmlx.alert({
                    text: "SPO Flat Discount MUST cannot be less than ZERO",
                    type: "alert-warning",
                    title: "Test Rates Calculator",
                    callback: function () {
                        form_spo.setItemFocus("spo_discount_PNI");
                    }
                });
                return false;
            }
            
        }

        return true;
    }




}


