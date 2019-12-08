var touroperators_obj = new touroperators();

function touroperators()
{

    var popupwin = null;
    var pop_grid = null;
    var pop_layout = null;
    var pop_form = null;
    var pop_toolbar = null;

    var old_countries = "";

    document.getElementById("aTitle").innerHTML = "TOUR OPERATORS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();


    var grid_touroperators = main_layout.cells("a").attachGrid();
    grid_touroperators.setIconsPath('libraries/dhtmlx/imgs/');
    grid_touroperators.setHeader("Name,Company Type,Active,Countries,Linked Companies");
    grid_touroperators.setColumnIds("toname,comptype,active,countries,selected_companies");
    grid_touroperators.setColTypes("ro,ro,ch,ro,ro");
    grid_touroperators.setInitWidths("*,120,60,200,200");
    grid_touroperators.setColAlign("left,left,center,left,left");
    grid_touroperators.setColSorting('str,str,int,str,str');
    grid_touroperators.attachHeader("#text_filter,#select_filter,#select_filter,#text_filter,#text_filter");
    grid_touroperators.setEditable(false);
    grid_touroperators.enableMultiline(true);
    grid_touroperators.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");

    toolbar.setIconSize(32);

    var opts = Array(
            Array('modify', 'obj', 'Modify Selected Tour Operator', 'modify.png'),
            Array('delete', 'obj', 'Delete Selected Tour Operator', 'delete.png'),
            Array('users', 'obj', 'View Tour Operator Users', 'users.png')
            );
    toolbar.addButtonSelect("opts", 2, "Operate", opts, "operate.png", "operate.png", null, true);

    toolbar.addButton("export", 3, "Export Excel", "excel.png");


    toolbar.attachEvent("onClick", function (id) {

        if (id == "new")
        {
            newTO();

        } else if (id == "export")
        {
            grid_touroperators.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "users")
        {
            var uid = grid_touroperators.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            loadUsers(uid);
            popupwin_users.setModal(true);
            popupwin_users.center();
            popupwin_users.show();

        } else if (id == "modify")
        {
            var uid = grid_touroperators.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            modifyTO(uid);



        } else if (id == "delete")
        {
            var gid = grid_touroperators.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Tour Operator",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/touroperators/deletetouroperator.php", params, function (loader) {

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
                                    grid_touroperators.deleteRow(gid);
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


    var dsTourOperators = new dhtmlXDataStore();
    main_layout.cells("a").progressOn();
    dsTourOperators.load("php/api/touroperators/touroperatorgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        main_layout.cells("a").progressOff();
        grid_touroperators.sync(dsTourOperators);

        grid_touroperators.forEachRow(function (rwid) {
            grid_touroperators.forEachCell(rwid, function (c, ind) {
                var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                grid_touroperators.setCellTextStyle(rwid, ind, cellstyle);
            });
        });
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
            var y = Math.max(body.scrollHeight, body.offsetHeight,
                    html.clientHeight, html.scrollHeight, html.offsetHeight);
            y -= 150;

            $("#main_body").height(y - 25);
            $("#main_body").width(x - 20);

            main_layout.setSizes(true);

        }, 1);
    }


    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(main_layout.cells("a"));

    var popupwin_touroperators = dhxWins.createWindow("popupwin_touroperators", 50, 50, 700, 440);
    popupwin_touroperators.setText("Tour Operator Details:");

    var x = $("#main_body").parent().width() - 20;
    var body = document.body,
            html = document.documentElement;
    var y = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
    y -= 170;

    popupwin_touroperators.setDimension(x, y);

    popupwin_touroperators.denyResize();
    popupwin_touroperators.denyPark();
    popupwin_touroperators.button("close").hide();

    var popupwin_users = dhxWins.createWindow("popupwin_users", 50, 50, 850, 440);
    popupwin_users.setText("Tour Operator Users:");
    popupwin_users.denyResize();
    popupwin_users.denyPark();

    var popupwin_users_attach = dhxWins.createWindow("popupwin_users_attach", 50, 50, 700, 420);
    popupwin_users_attach.setText("Attach Users (EXTERNAL + ACTIVE + IN A GROUP):");
    popupwin_users_attach.denyResize();
    popupwin_users_attach.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {

        if (win.getId() == "popupwin_users_attach")
        {
            popupwin_users_attach.setModal(false);
            popupwin_users_attach.hide();
            popupwin_users.setModal(true);
        } else
        {
            win.setModal(false);
            win.hide();
        }
    });


    var touroperatorlayout = popupwin_touroperators.attachLayout("2U");

    touroperatorlayout.cells("a").setWidth(130);
    touroperatorlayout.cells("a").hideHeader();
    touroperatorlayout.cells("b").hideHeader();

    var toolbar_details = touroperatorlayout.cells("b").attachToolbar();
    toolbar_details.setIconsPath("images/");
    toolbar_details.addButton("save", 1, "Save", "save.png", "save.png");
    toolbar_details.addSpacer("save");
    toolbar_details.addButton("exit", 2, "Exit", "exit.png", "exit.png");
    toolbar_details.setIconSize(32);
    toolbar_details.attachEvent("onClick", function (id) {
        if (id == "exit")
        {
            popupwin_touroperators.setModal(false);
            popupwin_touroperators.hide();

        } else if (id == "save")
        {
            saveTO();

        }
    });

    var tree_menu = null;


    var tabViews = touroperatorlayout.cells("b").attachTabbar();
    tabViews.addTab("details", "Details", "180px", '');
    tabViews.addTab("markets", "Markets", "180px", '');
    tabViews.addTab("currencies", "Currencies", "180px", '');
    tabViews.addTab("pricecodes", "Price Codes", "180px", '');
    tabViews.addTab("allocations", "Allocations", "180px", '');
    tabViews.addTab("internet", "Internet", "180px", '');
    tabViews.addTab("notes", "Notes", "180px", '');
    tabViews.addTab("companies", "Companies", "180px", '');
    tabViews.addTab("api", "API", "180px", '');
    tabViews.addTab("balances", "Balances", "180px", '');
    tabViews.addTab("transactions", "Transactions", "180px", '');
    tabViews.addTab("contacts", "Contacts", "180px", '');
    tabViews.addTab("message", "Message", "180px", '');

    hideShowTab("-1");
    hideShowTab("details");

    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_touroperators"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},

        {type: "checkbox", name: "active", label: "Active:", labelWidth: "50",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"},

        {type: "fieldset", label: "Basic", width: 700, list: [

                {type: "input", name: "toname", label: "Name:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },

                {type: "combo", name: "companytypefk", label: "Company Type:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]},

        {type: "fieldset", label: "Address", width: 700, list: [

                {type: "label", label: "Physical Address"},
                {type: "input", name: "phy_address", label: "Street 1:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "phy_address2", label: "Street 2:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "phy_city", label: "City:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "phy_postcode", label: "Postcode:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "combo", name: "phy_countryfk", label: "Country:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "button", name: "cmdMail", value: "Copy to Mailing >", width: "120", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "label", label: "Mail Address"},
                {type: "input", name: "mail_address", label: "Street 1:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "mail_address2", label: "Street 2:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "mail_city", label: "City:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", 
                },
                {type: "input", name: "mail_postcode", label: "Postcode:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "combo", name: "mail_countryfk", label: "Country:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "button", name: "cmdPhysical", value: "< Copy to Physical", width: "120", offsetLeft: 0}]},

        {type: "fieldset", label: "Miscellaneous", width: 700, list: [

                {type: "combo", name: "taxindicatorfk", label: "Tax Indicator:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "input", name: "commission", label: "Commission %:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidNumeric",
                    required: true, value: "0.00"

                },
                {type: "input", name: "markup", label: "MarkUp %:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidNumeric",
                    required: true, value: "0.00"
                },
                {type: "input", name: "iata_code", label: "IATA Code:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },

                {type: "newcolumn"},
                {type: "combo", name: "ratecode", label: "Standard Rate:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "specialratecode", label: "Special Rate:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "transferratecode", label: "Transfer Rate:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "id_vat", label: "Tax:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]}
    ];

    var form_touroperators = tabViews.cells("details").attachForm(str_frm_ug);

    //===============================================================================

    var str_frm_markets = [
        {type: "settings", position: "label-left", id: "form_markets"},
        {type: "block", width: 900, list: [
                {type: "input", name: "market_countries_display", label: "Country:", labelWidth: "80",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 5
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]}
    ];
    var form_markets = tabViews.cells("markets").attachForm(str_frm_markets);

    form_markets.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_markets, "Countries", "market_countries_display", "market_countries_ids", null);
        }
    });

    //===============================================================================
    var str_frm_api = [
        {type: "settings", position: "label-left", id: "form_touroperators"},
        {type: "hidden", name: "id", required: true},
        {type: "checkbox", name: "api_active", label: "Active:", labelWidth: "50",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"},
        {type: "input", name: "api_token", label: "Token:", labelWidth: "50",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
        }
    ];

    var form_api = tabViews.cells("api").attachForm(str_frm_api);


    //===============================================================================
    var str_frm_companies = [
        {type: "settings", position: "label-left", id: "form_touroperators"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "container", name: "myGrid", label: "",
            labelWidth: "100",
            inputWidth: 500, inputHeight: 300},
        {type: "hidden", name: "selected_companies_ids"}
    ];

    var form_companies = tabViews.cells("companies").attachForm(str_frm_companies);

    var grid_companies = new dhtmlXGridObject(form_companies.getContainer("myGrid"));
    grid_companies.setIconsPath('libraries/dhtmlx/imgs/');
    grid_companies.setHeader(",Company Name");
    grid_companies.setColumnIds("X,companyname");
    grid_companies.setColTypes("ch,ro");
    grid_companies.setInitWidths("50,*");
    grid_companies.setColAlign("center,left");
    grid_companies.setColSorting('int,str');
    grid_companies.attachHeader("#master_checkbox,#text_filter");
    grid_companies.init();


    var dsCompanies = new dhtmlXDataStore();
    dsCompanies.load("php/api/touroperators/companygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_companies.sync(dsCompanies);
    });

    form_touroperators.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdMail")
        {
            //copy values from physical to mail
            copyAddress("phy", "mail");
        } else if (name == "cmdPhysical")
        {
            //copy values from mail to physical
            copyAddress("mail", "phy");
        }
    });

    var cboPhyCountry = form_touroperators.getCombo("phy_countryfk");
    var cboMailCountry = form_touroperators.getCombo("mail_countryfk");

    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/combos/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCountry.dataCount(); i++)
        {
            var item = dsCountry.item(dsCountry.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboPhyCountry.addOption([{value: value, text: txt, img_src: "images/country.png"}]);
            cboMailCountry.addOption([{value: value, text: txt, img_src: "images/country.png"}]);
        }

        cboPhyCountry.readonly(false);
        cboPhyCountry.enableFilteringMode(true);

        cboMailCountry.readonly(false);
        cboMailCountry.enableFilteringMode(true);
    });

    //==========================

    var cboTaxIndic = form_touroperators.getCombo("taxindicatorfk");
    var dsTaxIndic = new dhtmlXDataStore();

    dsTaxIndic.load("php/api/touroperators/taxindic_combo.php?t=" + encodeURIComponent(global_token), "json", function () {
        for (var i = 0; i < dsTaxIndic.dataCount(); i++)
        {
            var item = dsTaxIndic.item(dsTaxIndic.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboTaxIndic.addOption([{value: value, text: txt, img_src: "images/tax.png"}]);
        }
        cboTaxIndic.readonly(true);
    });

    //==========================

    var cboCompanyType = form_touroperators.getCombo("companytypefk");
    var dsCompanyType = new dhtmlXDataStore();
    dsCompanyType.load("php/api/combos/companytype_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCompanyType.dataCount(); i++)
        {
            var item = dsCompanyType.item(dsCompanyType.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboCompanyType.addOption([{value: value, text: txt, img_src: "images/companytype_32.png"}]);
        }

        cboCompanyType.readonly(true);
    });


    var cboRateCode = form_touroperators.getCombo("ratecode");
    var cboSpecialRateCode = form_touroperators.getCombo("specialratecode");
    var cboTransferRateCode = form_touroperators.getCombo("transferratecode");
    var cboTax = form_touroperators.getCombo("id_vat");

    cboRateCode.addOption([{value: "-", text: "None", img_src: "images/rate_32.png"}]);
    cboSpecialRateCode.addOption([{value: "-", text: "None", img_src: "images/rate_32.png"}]);
    cboTransferRateCode.addOption([{value: "-", text: "None", img_src: "images/rate_32.png"}]);
    cboTax.addOption([{value: "", text: "None", img_src: "images/rate_32.png"}]);
    
    var dsTax = new dhtmlXDataStore();
    dsTax.load("php/api/combos/taxcode_combo.php?t=" + global_token, "json", function () {
        for (var i = 0; i < dsTax.dataCount(); i++)
        {
            var item = dsTax.item(dsTax.idByIndex(i));
            cboTax.addOption([{value: item.value, text: item.description, img_src: "images/rate_32.png"}]);
        }
        cboTax.readonly(true);
        cboTax.enableOptionAutoPositioning(true);
    });
        
    var dsRateCode = new dhtmlXDataStore();
    dsRateCode.load("php/api/combos/rate_code_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsRateCode.dataCount(); i++)
        {
            var item = dsRateCode.item(dsRateCode.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboRateCode.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
            cboSpecialRateCode.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
            cboTransferRateCode.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
        }

        cboRateCode.readonly(true);
        cboRateCode.enableFilteringMode(false);

        cboSpecialRateCode.readonly(true);
        cboSpecialRateCode.enableFilteringMode(false);

        cboTransferRateCode.readonly(true);
        cboTransferRateCode.enableFilteringMode(false);

        cboRateCode.setComboValue("B"); //B -> STANDARD
        cboSpecialRateCode.setComboValue("A"); //A -> SPECIAL
        cboTransferRateCode.setComboValue("-");
    });


    //===========================================

    var str_frm_notes = [
        {type: "settings", position: "label-left", id: "form_notes"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},

        {type: "editor", name: "description_private", label: "Private Notes:", labelWidth: "100",
            labelHeight: "22", inputWidth: "550", inputHeight: "200", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "editor", name: "description_public", label: "Public Notes:", labelWidth: "100",
            labelHeight: "22", inputWidth: "550", inputHeight: "200", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"}
    ];

    var form_notes = tabViews.cells("notes").attachForm(str_frm_notes);

    //==============================================================

    var currencylayout = tabViews.cells("currencies").attachLayout("2U");
    currencylayout.cells("a").hideHeader();
    currencylayout.cells("b").hideHeader();

    currencylayout.cells("a").setWidth(150);

    var grid_currency_from = currencylayout.cells("a").attachGrid();
    grid_currency_from.setIconsPath('libraries/dhtmlx/imgs/');
    grid_currency_from.setHeader(",Currency");
    grid_currency_from.setColumnIds("X,currency_code");
    grid_currency_from.setColTypes("ch,ro");
    grid_currency_from.setInitWidths("50,65");
    grid_currency_from.setColAlign("center,center");
    grid_currency_from.setColSorting('int,str');
    grid_currency_from.attachHeader("#master_checkbox,#select_filter");
    grid_currency_from.init();

    var toolbar_currency_from = currencylayout.cells("a").attachToolbar();
    toolbar_currency_from.setIconsPath("images/");
    toolbar_currency_from.addButton("add", 1, "Add >>", "add.png", "add.png");
    toolbar_currency_from.setIconSize(32);
    toolbar_currency_from.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            var checkedids = grid_currency_from.getCheckedRows(0);
            if (checkedids == "")
            {
                dhtmlx.alert({
                    text: "Please select at least one Currency!",
                    type: "alert-warning",
                    title: "Attach Currency",
                    callback: function () {

                    }
                });
                return;
            }

            var arr_ids = checkedids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {


                var currencyid = arr_ids[i];

                grid_currency_from.setRowHidden(currencyid, true);
                grid_currency_from.uncheckAll();

                new_currency_row_id--;
                grid_currency_to.addRow(new_currency_row_id, [currencyid, "0", "", "0", "", "0.00", "", "", "", "ADD", ""]);
                grid_currency_to.selectRowById(new_currency_row_id, false, true, false);
                grid_currency_to.setRowTextStyle(new_currency_row_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            }
        }
    });

    var new_currency_row_id = 0;

    var grid_currency_to = currencylayout.cells("b").attachGrid();
    grid_currency_to.setIconsPath('libraries/dhtmlx/imgs/');
    grid_currency_to.setHeader("Currency,Default,Tax,Terms Value,Terms Code,Cr Limit,Bank,Bank Acc,Acc Name,Action,Valid");
    grid_currency_to.setColumnIds("currencyid,use_default,tax_code,terms_value,terms_code,credit_limit,bankfk,bankaccount,accountname,action,valid");
    grid_currency_to.setColTypes("coro,ch,coro,edn,coro,edn,coro,ed,ed,ro,ro");
    grid_currency_to.setInitWidths("80,60,120,60,120,100,100,100,100,100,100");
    grid_currency_to.setColAlign("left,center,left,right,left,right,left,left,left");
    grid_currency_to.setColSorting('str,int,str,int,str,str,str,str,str,str,str');
    grid_currency_to.setEditable(true);
    grid_currency_to.enableValidation(true);
    grid_currency_to.setColValidators(",,NotEmpty,ValidNumeric,NotEmpty,ValidNumeric,,,,,");
    grid_currency_to.enableEditEvents(true, false, true);
    grid_currency_to.init();
    grid_currency_to.setNumberFormat("0,000.00", grid_currency_to.getColIndexById("credit_limit"));

    grid_currency_to.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
        if (stage == 0)
        {
            if (grid_currency_to.getColIndexById("currencyid") == cInd)
            {
                return false; //readonly column
            }
        }

        return true;
    });

    var dsCurrencyGrid = new dhtmlXDataStore();
    var dsCurrencyFromGrid = new dhtmlXDataStore();

    loadCurrencyGridCombos(false, "-1");

    var toolbar_currency = currencylayout.cells("b").attachToolbar();
    toolbar_currency.setIconsPath("images/");
    toolbar_currency.addButton("delete", 1, "<< Delete", "delete.png", "delete.png");
    toolbar_currency.setIconSize(32);

    toolbar_currency.attachEvent("onClick", function (id) {

        if (id == "delete")
        {
            var rid = grid_currency_to.getSelectedRowId();
            if (rid)
            {
                dhtmlx.confirm({
                    title: "Delete Currency",
                    type: "confirm",
                    text: "Confirm Deletion?",
                    callback: function (tf) {
                        if (tf)
                        {
                            var currencyid = grid_currency_to.cells(rid, grid_currency_to.getColIndexById("currencyid")).getValue();
                            grid_currency_from.setRowHidden(currencyid, false);
                            grid_currency_from.uncheckAll();

                            if (parseInt(rid, 10) < 0)
                            {
                                grid_currency_to.deleteRow(rid);
                            } else
                            {
                                grid_currency_to.cells(rid, grid_currency_to.getColIndexById("action")).setValue("DELETE");
                                grid_currency_to.setRowHidden(rid, true);
                            }
                        }
                    }});

            }
        }
    });


    //==============================================================

    var contactlayout = tabViews.cells("contacts").attachLayout("1C");
    contactlayout.cells("a").hideHeader();

    var new_contact_row_id = 0;

    var grid_contact = contactlayout.cells("a").attachGrid();
    grid_contact.setIconsPath('libraries/dhtmlx/imgs/');
    grid_contact.setHeader("Department,Name,Default,Phone,Mobile,Fax,Email,Web Address,Action,Valid");
    grid_contact.setColumnIds("deptfk,contact_name,dept_default,contact_phone,contact_mobile,contact_fax,contact_email,contact_webaddress,action,valid");
    grid_contact.setColTypes("coro,ed,ch,ed,ed,ed,ed,ed,ro,ro");
    grid_contact.setInitWidths("120,150,70,100,100,100,100,200,100,100");
    grid_contact.setColAlign("left,left,center,left,left,left,left,left,left");
    grid_contact.setColSorting('str,str,int,str,str,str,str,str,str,str');
    grid_contact.setEditable(true);
    grid_contact.enableValidation(true);
    grid_contact.setColValidators("NotEmpty,NotEmpty,,,,,ValidEmail,,,");
    grid_contact.enableEditEvents(true, false, true);
    grid_contact.init();

    var dsContactGrid = new dhtmlXDataStore();
    loadContactGridCombos(true, "-1");

    var toolbar_contact = contactlayout.cells("a").attachToolbar();
    toolbar_contact.setIconsPath("images/");
    toolbar_contact.addButton("add", 1, "Add Contact", "add.png", "add.png");
    toolbar_contact.addButton("delete", 2, "Delete Contact", "delete.png", "delete.png");
    toolbar_contact.setIconSize(32);

    toolbar_contact.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            new_contact_row_id--;
            grid_contact.addRow(new_contact_row_id, ["", "", "0", "", "", "", "", "", "ADD", ""]);
            grid_contact.selectRowById(new_contact_row_id, false, true, false);
            grid_contact.setRowTextStyle(new_contact_row_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


        } else if (id == "delete")
        {
            var rid = grid_contact.getSelectedRowId();
            if (rid)
            {
                dhtmlx.confirm({
                    title: "Delete Contact",
                    type: "confirm",
                    text: "Confirm Deletion?",
                    callback: function (tf) {
                        if (tf)
                        {
                            grid_contact.cells(rid, grid_contact.getColIndexById("action")).setValue("DELETE");
                            grid_contact.setRowHidden(rid, true);
                        }
                    }});


            }
        }
    });


    //=========================================================

    var userslayout = popupwin_users.attachLayout("1C");
    userslayout.cells("a").hideHeader();

    var dsUsers = new dhtmlXDataStore();

    var grid_users = userslayout.cells("a").attachGrid();
    grid_users.setIconsPath('libraries/dhtmlx/imgs/');
    grid_users.setHeader("User Name,Email,Group,Status,Date Created,Date Activated");
    grid_users.setColumnIds("uname,email,ugroup,status,date_created,date_activated");
    grid_users.setColTypes("ro,ro,ro,ro,ro,ro");
    grid_users.setInitWidths("100,150,120,120,150,*");
    grid_users.setColAlign("left,left,left,center,left,left");
    grid_users.setColSorting('str,str,str,str,date,date');
    grid_users.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter");
    grid_users.enableMultiselect(true);
    grid_users.init();

    var sbUsers = userslayout.cells("a").attachStatusBar();

    var toolbar_users = userslayout.cells("a").attachToolbar();
    toolbar_users.setIconsPath("images/");
    toolbar_users.addButton("attach", 1, "Attach Users", "add.png", "add.png");
    toolbar_users.addButton("remove", 2, "Remove Users", "delete.png", "delete.png");
    toolbar_users.setIconSize(32);

    toolbar_users.attachEvent("onClick", function (id) {

        if (id == "attach")
        {
            var uid = grid_touroperators.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            popupwin_users.setModal(false);

            loadAttachUsers();
            popupwin_users_attach.setModal(true);
            popupwin_users_attach.center();
            popupwin_users_attach.show();

        } else if (id == "remove")
        {
            var uid = grid_users.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            dhtmlx.confirm({
                title: "Detach Users",
                type: "confirm",
                text: "Confirm removal of selected Users?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "uid=" + uid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/touroperators/detachusers.php", params, function (loader) {

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DETACH",
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
                                        title: "DETACH",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    loadUsers(grid_touroperators.getSelectedRowId());
                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DETACH",
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
            if (json_rights[i].PROCESSNAME == "ADD" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("new");
                toolbar.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "modify");
                toolbar.setListOptionToolTip("opts", "modify", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {

                toolbar.disableListOption("opts", "delete");
                toolbar.setListOptionToolTip("opts", "delete", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "VIEW USERS" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "users");
                toolbar.setListOptionToolTip("opts", "users", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "ATTACH USERS" && json_rights[i].ALLOWED == "N")
            {
                toolbar_users.disableItem("attach");
                toolbar_users.setItemToolTip("attach", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DETACH USERS" && json_rights[i].ALLOWED == "N")
            {
                toolbar_users.disableItem("remove");
                toolbar_users.setItemToolTip("remove", "Not Allowed");
            }

        }
    }

    function loadUsers(toid)
    {
        dsUsers.clearAll();
        dsUsers = null;
        dsUsers = new dhtmlXDataStore();
        userslayout.cells("a").progressOn();
        grid_users.clearAll();
        dsUsers.load("php/api/touroperators/usersgrid.php?t=" + encodeURIComponent(global_token) + "&toid=" + toid, "json", function () {
            userslayout.cells("a").progressOff();
            grid_users.sync(dsUsers);
            sbUsers.setText("No of Users: " + grid_users.getRowsNum());
        });
    }

    //===============================================================
    var attachlayout = popupwin_users_attach.attachLayout("1C");
    attachlayout.cells("a").hideHeader();

    var dsUsersAttach = new dhtmlXDataStore();

    var grid_attach = attachlayout.cells("a").attachGrid();
    grid_attach.setIconsPath('libraries/dhtmlx/imgs/');
    grid_attach.setHeader(",User Name,Email,Group,Status,Date Created,Date Activated");
    grid_attach.setColumnIds("X,uname,email,ugroup,status,date_created,date_activated");
    grid_attach.setColTypes("ch,ro,ro,ro,ro,ro,ro");
    grid_attach.setInitWidths("50,100,150,120,0,150,*");
    grid_attach.setColAlign("center,left,left,left,center,left,left");
    grid_attach.setColSorting('int,str,str,str,str,date,date');
    grid_attach.attachHeader("#master_checkbox,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter");
    grid_attach.init();

    var toolbar_users_attact = attachlayout.cells("a").attachToolbar();
    toolbar_users_attact.setIconsPath("images/");
    toolbar_users_attact.addButton("attach_selected", 1, "Attach Selected Users", "add.png", "add.png");
    toolbar_users_attact.setIconSize(32);

    toolbar_users_attact.attachEvent("onClick", function (id) {

        if (id == "attach_selected")
        {
            var checkedids = grid_attach.getCheckedRows(0);
            if (checkedids == "")
            {
                dhtmlx.alert({
                    text: "Please select at least one User!",
                    type: "alert-warning",
                    title: "Attach User",
                    callback: function () {

                    }
                });
                return;
            }

            var params = "uids=" + checkedids + "&t=" + encodeURIComponent(global_token) +
                    "&toid=" + grid_touroperators.getSelectedRowId();
            dhtmlxAjax.post("php/api/touroperators/attachusers.php", params, function (loader) {

                if (loader)
                {
                    if (loader.xmlDoc.responseURL == "")
                    {
                        dhtmlx.alert({
                            text: "Connection Lost!",
                            type: "alert-warning",
                            title: "ATTACH",
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
                            title: "ATTACH",
                            callback: function () {
                            }
                        });
                        return false;
                    }
                    if (json_obj.OUTCOME == "OK")
                    {
                        loadUsers(grid_touroperators.getSelectedRowId());
                        popupwin_users_attach.setModal(false);
                        popupwin_users_attach.hide();
                        popupwin_users.setModal(true);
                    } else
                    {
                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "ATTACH",
                            callback: function () {
                            }
                        });
                    }
                }
            });


        }
    });

    function loadAttachUsers()
    {
        dsUsersAttach.clearAll();
        dsUsersAttach = null;
        dsUsersAttach = new dhtmlXDataStore();
        attachlayout.cells("a").progressOn();
        grid_attach.clearAll();
        dsUsersAttach.load("php/api/touroperators/attachusersgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
            attachlayout.cells("a").progressOff();
            grid_attach.sync(dsUsersAttach);
        });
    }

    //===============================================================

    function hideShowTab(tabid)
    {
        if (tabid == "setup")
        {
            return false;
        }

        var arrTabids = tabViews.getAllTabs();

        for (var i = 0; i < arrTabids.length; i++)
        {
            if (tabid == arrTabids[i])
            {
                tabViews.showTab(tabid);
                tabViews.setTabActive(tabid);
            } else
            {
                tabViews.hideTab(arrTabids[i]);
            }
        }


    }


    function loadContactGridCombos(tf_load_contact_grid, toid)
    {
        var ds = new dhtmlXDataStore();

        ds = null;
        ds = new dhtmlXDataStore();
        ds.load("php/api/combos/contactdepartment_combo.php?t=" + global_token, "json", function () {
            grid_contact.getCombo(grid_contact.getColIndexById("deptfk")).put("", " ");
            for (var i = 0; i < ds.dataCount(); i++)
            {
                var item = ds.item(ds.idByIndex(i));
                grid_contact.getCombo(grid_contact.getColIndexById("deptfk")).put(item.value, item.description);
            }

            //finally load grid data
            if (tf_load_contact_grid)
            {
                loadContactGrid(toid);
            }

        });

    }

    function loadCurrencyGridCombos(tf_load_currency_grid, toid)
    {


        //load currency
        var ds = new dhtmlXDataStore();
        ds.load("php/api/combos/currency_combo.php?t=" + global_token, "json", function () {
            grid_currency_to.getCombo(grid_currency_to.getColIndexById("currencyid")).put("", " ");
            for (var i = 0; i < ds.dataCount(); i++)
            {
                var item = ds.item(ds.idByIndex(i));
                grid_currency_to.getCombo(grid_currency_to.getColIndexById("currencyid")).put(item.value, item.text);
            }
        });


        var dstc = new dhtmlXDataStore();


        //load tax code
        dstc.load("php/api/combos/taxcode_combo.php?t=" + global_token, "json", function () {
            grid_currency_to.getCombo(grid_currency_to.getColIndexById("tax_code")).put("", " ");
            for (var i = 0; i < dstc.dataCount(); i++)
            {
                var item = dstc.item(dstc.idByIndex(i));
                grid_currency_to.getCombo(grid_currency_to.getColIndexById("tax_code")).put(item.text, item.description);
            }
        });

        var dspt = new dhtmlXDataStore();

        //load payment terms
        dspt.load("php/api/combos/paymentterms_combo.php?t=" + global_token, "json", function () {
            grid_currency_to.getCombo(grid_currency_to.getColIndexById("terms_code")).put("", " ");
            for (var i = 0; i < dspt.dataCount(); i++)
            {
                var item = dspt.item(dspt.idByIndex(i));
                grid_currency_to.getCombo(grid_currency_to.getColIndexById("terms_code")).put(item.text, item.description);
            }
        });

        //load banks
        var dsbnk = new dhtmlXDataStore();
        dsbnk.load("php/api/combos/bank_combo.php?t=" + global_token, "json", function () {
            grid_currency_to.getCombo(grid_currency_to.getColIndexById("bankfk")).put("", " ");
            for (var i = 0; i < dsbnk.dataCount(); i++)
            {
                var item = dsbnk.item(dsbnk.idByIndex(i));
                grid_currency_to.getCombo(grid_currency_to.getColIndexById("bankfk")).put(item.value, item.text);
            }
        });

        //finally load grid data
        if (tf_load_currency_grid)
        {
            loadCurrencyFromGrid(toid);
        }


    }


    function loadContactGrid(toid)
    {
        dsContactGrid.clearAll();
        dsContactGrid = null;
        dsContactGrid = new dhtmlXDataStore();
        contactlayout.cells("a").progressOn();
        grid_contact.clearAll();

        dsContactGrid.load("php/api/touroperators/contactgrid.php?t=" + encodeURIComponent(global_token) + "&toid=" + toid, "json", function () {
            contactlayout.cells("a").progressOff();
            grid_contact.sync(dsContactGrid);

            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_contact, css);

        });
    }

    function loadCurrencyFromGrid(toid)
    {
        dsCurrencyFromGrid.clearAll();
        dsCurrencyFromGrid = null;
        dsCurrencyFromGrid = new dhtmlXDataStore();
        currencylayout.cells("a").progressOn();
        grid_currency_from.clearAll();
        dsCurrencyFromGrid.load("php/api/touroperators/currencyfromgrid.php?t=" + encodeURIComponent(global_token) + "&toid=" + toid, "json", function () {
            currencylayout.cells("a").progressOff();
            grid_currency_from.sync(dsCurrencyFromGrid);

            loadCurrencyGrid(toid);
        });
    }

    function loadCurrencyGrid(toid)
    {
        dsCurrencyGrid.clearAll();
        dsCurrencyGrid = null;
        dsCurrencyGrid = new dhtmlXDataStore();

        currencylayout.cells("b").progressOn();

        grid_currency_to.clearAll();

        dsCurrencyGrid.load("php/api/touroperators/currencygrid.php?t=" + encodeURIComponent(global_token) + "&toid=" + toid, "json", function () {
            currencylayout.cells("b").progressOff();
            grid_currency_to.sync(dsCurrencyGrid);

            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_currency_to, css)


            //hide currencies already in grid_currency_to from grid_currency_from
            for (var i = 0; i < grid_currency_to.getRowsNum(); i++) {

                var id = grid_currency_to.getRowId(i);
                var currencyid = grid_currency_to.cells(id, grid_currency_to.getColIndexById("currencyid")).getValue();
                grid_currency_from.setRowHidden(currencyid, true);
            }


        });
    }

    function copyAddress(from, to)
    {
        var arrfields = ["address", "address2", "city", "postcode"];

        for (var i = 0; i < arrfields.length; i++)
        {
            var fldname_from = from + "_" + arrfields[i];
            var fldname_to = to + "_" + arrfields[i];

            var from_val = form_touroperators.getItemValue(fldname_from);

            form_touroperators.setItemValue(fldname_to, from_val);
        }

        if (from == "phy")
        {
            cboMailCountry.setComboValue(cboPhyCountry.getSelectedValue());
        } else
        {
            cboPhyCountry.setComboValue(cboMailCountry.getSelectedValue());
        }

    }

    function modifyTO(toid)
    {
        tree_menu = null;
        tree_menu = touroperatorlayout.cells("a").attachTree();
        tree_menu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_menu.attachEvent("onSelect", function (id) {
            hideShowTab(id);
        });

        main_layout.cells("a").progressOn();
        tree_menu.loadXML("php/api/touroperators/tree_menu.php?t=" + global_token + "&action=MODIFY", function () {
            main_layout.cells("a").progressOff();

            form_touroperators.clear();
            form_api.clear();
            form_notes.clear();
            form_companies.clear();
            form_markets.clear();

            form_touroperators.setItemValue("id", toid);

            cboCompanyType.setComboValue(null);
            cboCompanyType.setComboText("");

            cboPhyCountry.setComboValue(null);
            cboPhyCountry.setComboText("");

            cboMailCountry.setComboValue(null);
            cboMailCountry.setComboText("");

            cboTaxIndic.setComboValue("");
            cboRateCode.setComboValue("-");
            cboSpecialRateCode.setComboValue("-");
            cboTransferRateCode.setComboValue("-");


            var data = dsTourOperators.item(toid);
            form_touroperators.setFormData(data);
            form_api.setFormData(data);
            form_notes.setFormData(data);
            form_notes.setFormData(data);
            form_markets.setFormData(data);

            old_countries = form_markets.getItemValue("market_countries_ids");

            //load companies
            grid_companies.checkAll(false)
            var selected_companies_ids = data.selected_companies_ids;
            if (selected_companies_ids)
            {
                var arr_ids = selected_companies_ids.split(",");
                for (var i = 0; i < arr_ids.length; i++)
                {
                    var id = arr_ids[i];

                    grid_companies.cells(id, 0).setValue(1);
                }
            }


            loadCurrencyGridCombos(true, toid);
            loadContactGridCombos(true, toid);

            popupwin_touroperators.setModal(true);
            popupwin_touroperators.center();
            popupwin_touroperators.show();

            tree_menu.selectItem("currencies", true, false);
            tree_menu.selectItem("details", true, false);
        });
    }


    function newTO()
    {
        tree_menu = null;
        tree_menu = touroperatorlayout.cells("a").attachTree();
        tree_menu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_menu.attachEvent("onSelect", function (id) {
            hideShowTab(id);
        });

        main_layout.cells("a").progressOn();
        tree_menu.loadXML("php/api/touroperators/tree_menu.php?t=" + global_token + "&action=NEW", function () {
            main_layout.cells("a").progressOff();

            form_touroperators.clear();
            form_api.clear();
            form_notes.clear();
            form_companies.clear();
            form_markets.clear();

            old_countries = "";

            form_touroperators.setItemValue("id", "-1");
            form_touroperators.setItemValue("commission", "0.00");
            form_touroperators.setItemValue("markup", "0.00");

            form_touroperators.setItemFocus("toname");

            cboCompanyType.setComboValue(null);
            cboCompanyType.setComboText("");

            cboPhyCountry.setComboValue(null);
            cboPhyCountry.setComboText("");

            cboMailCountry.setComboValue(null);
            cboMailCountry.setComboText("");

            cboTaxIndic.setComboValue("");
            cboRateCode.setComboValue("B"); //12 -> B standard
            cboSpecialRateCode.setComboValue("A"); //A -> special
            cboTransferRateCode.setComboValue("-");


            loadCurrencyGridCombos(true, "-1");
            loadContactGridCombos(true, "-1");

            popupwin_touroperators.setModal(true);
            popupwin_touroperators.center();
            popupwin_touroperators.show();

            tabViews.showTab("currencies");
            tabViews.setTabActive("currencies");
            tabViews.hideTab("currencies");
            tabViews.setTabActive("details");
            tree_menu.selectItem("details", true, false);

        });
    }

    function saveTO()
    {

        var id = form_touroperators.getItemValue("id");


        if (!validateDetails())
        {
            return;
        }


        //is it modification of TO?
        if (id != "-1")
        {
            if (!validateMarket())
            {
                return;
            }

            if (!validateCurrencyGrid())
            {
                return;
            }

            if (!validateCompanies())
            {
                return;
            }

            if (!validateContactGrid())
            {
                return;
            }

            if (!validateAPI())
            {
                return;
            }
        }

        //determine if there has been a change in the country?
        //if yes then 
        //  remove the TO from all Contracts, SPO and Inventory that do not feature the old country
        //  attach the TO all Contracts, SPO and Inventory with rates B that feature the new country


        touroperatorlayout.cells("b").progressOn();

        var data_details = form_touroperators.getFormData();
        var data_notes = form_notes.getFormData();
        var data_api = form_api.getFormData();
        var data_market = form_markets.getFormData();
        var companyids = form_companies.getItemValue("selected_companies_ids");
        var json_currencies = utils_dhxSerializeGridToJson(grid_currency_to);
        var json_contacts = utils_dhxSerializeGridToJson(grid_contact);

        var params = "token=" + encodeURIComponent(global_token) +
                "&data_details=" + encodeURIComponent(JSON.stringify(data_details)) +
                "&data_notes=" + encodeURIComponent(JSON.stringify(data_notes)) +
                "&data_api=" + encodeURIComponent(JSON.stringify(data_api)) +
                "&data_market=" + encodeURIComponent(JSON.stringify(data_market)) +
                "&companyids=" + encodeURIComponent(companyids) +
                "&oldcountryid=" + encodeURIComponent(old_countries) +
                "&json_currencies=" + encodeURIComponent(json_currencies) +
                "&json_contacts=" + encodeURIComponent(json_contacts);


        dhtmlxAjax.post("php/api/touroperators/savetouroperator.php", params, function (loader) {
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("details", true, false);
                        }
                    });
                    touroperatorlayout.cells("b").progressOff();
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
                            tree_menu.selectItem("details", true, false);
                        }
                    });
                    touroperatorlayout.cells("b").progressOff();
                    return false;
                }

                if (json_obj.OUTCOME == "OK")
                {
                    dhtmlx.message({
                        text: "<b><font color='green'>Save Successful!</font></b>",
                        expire: 1500
                    });


                    tree_menu.selectItem("details", true, false);

                    dsTourOperators.clearAll();
                    grid_touroperators.clearAll();

                    dsTourOperators.load("php/api/touroperators/touroperatorgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                        grid_touroperators.sync(dsTourOperators);

                        touroperatorlayout.cells("b").progressOff();

                        grid_touroperators.forEachRow(function (rwid) {
                            grid_touroperators.forEachCell(rwid, function (c, ind) {
                                var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                                grid_touroperators.setCellTextStyle(rwid, ind, cellstyle);
                            });
                        });


                        grid_touroperators.selectRowById(json_obj.ID, false, true, false);

                        modifyTO(json_obj.ID);

                        /*
                         
                         
                         if (id == "-1")
                         {
                         form_touroperators.setItemValue("id", json_obj.ID);
                         
                         //reload tree with full options
                         tree_menu = null;
                         tree_menu = touroperatorlayout.cells("a").attachTree();
                         tree_menu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
                         tree_menu.attachEvent("onSelect", function (id) {
                         hideShowTab(id);
                         });
                         
                         main_layout.cells("a").progressOn();
                         tree_menu.loadXML("php/api/touroperators/tree_menu.php?t=" + global_token + "&action=MODIFY", function () {
                         main_layout.cells("a").progressOff();
                         });
                         }
                         */
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
                    touroperatorlayout.cells("b").progressOff();
                }
            }
        });
    }

    function validateCurrencyGrid()
    {
        //make sure at least one currency entered
        //make sure all records are valid
        //make sure at most one currency is default

        if (grid_currency_to.getRowsNum() == 0)
        {
            dhtmlx.alert({
                text: "Please add at least one Currency",
                type: "alert-warning",
                title: "SAVE",
                callback: function () {
                    tree_menu.selectItem("currencies", true, false);
                }
            });
            return false;
        }

        var default_count = 0;

        for (var i = 0; i < grid_currency_to.getRowsNum(); i++) {


            var id = grid_currency_to.getRowId(i);
            var currencyid = grid_currency_to.cells(id, grid_currency_to.getColIndexById("currencyid")).getValue();
            var tax_code = grid_currency_to.cells(id, grid_currency_to.getColIndexById("tax_code")).getValue();
            var terms_code = grid_currency_to.cells(id, grid_currency_to.getColIndexById("terms_code")).getValue();
            var terms_value = grid_currency_to.cells(id, grid_currency_to.getColIndexById("terms_value")).getValue();
            var use_default = grid_currency_to.cells(id, grid_currency_to.getColIndexById("use_default")).getValue();
            var credit_limit = grid_currency_to.cells(id, grid_currency_to.getColIndexById("credit_limit")).getValue();
            var action = grid_currency_to.cells(id, grid_currency_to.getColIndexById("action")).getValue();

            if (action != "DELETE")
            {
                if (use_default == "1")
                {
                    default_count++;
                }

                if (currencyid == "")
                {
                    dhtmlx.alert({
                        text: "Please select a Currency",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("currencies", true, false);
                            grid_currency_to.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
                if (tax_code == "")
                {
                    dhtmlx.alert({
                        text: "Please select a Tax Code",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("currencies", true, false);
                            grid_currency_to.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
                if (terms_code == "")
                {
                    dhtmlx.alert({
                        text: "Please select a Terms Code",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("currencies", true, false);
                            grid_currency_to.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
                if (terms_value == "" || isNaN(terms_value))
                {
                    dhtmlx.alert({
                        text: "Please enter a valid numeric Terms Value",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("currencies", true, false);
                            grid_currency_to.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (credit_limit == "" || isNaN(credit_limit))
                {
                    dhtmlx.alert({
                        text: "Please enter a valid numeric Credit Limit",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("currencies", true, false);
                            grid_currency_to.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
            }

        }

        if (default_count == 0 || default_count > 1)
        {
            dhtmlx.alert({
                text: "Please set a Currency as Default",
                type: "alert-warning",
                title: "SAVE",
                callback: function () {

                    tree_menu.selectItem("currencies", true, false);
                }
            });
            return false;
        }

        return true;
    }



    function validateDetails()
    {
        if (!form_touroperators.validate())
        {
            dhtmlx.alert({
                text: "Please fill highlighted fields correctly!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {

                    tree_menu.selectItem("details", true, false);
                }
            });
            return false;
        }


        if (!utils_validate_autocompletecombo(cboPhyCountry))
        {
            dhtmlx.alert({
                text: "Please select a Physical Country!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboPhyCountry.openSelect();
                }
            });
            return false;
        }

        if (!utils_validate_autocompletecombo(cboMailCountry))
        {
            dhtmlx.alert({
                text: "Please select a Mailing Country!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboMailCountry.openSelect();
                }
            });
            return false;
        }

        if (!utils_validate_autocompletecombo(cboRateCode))
        {
            dhtmlx.alert({
                text: "Please select a valid Rate Code!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboRateCode.openSelect();
                }
            });
            return false;
        }

        if (!utils_validate_autocompletecombo(cboSpecialRateCode))
        {
            dhtmlx.alert({
                text: "Please select a valid Special Rate Code!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboSpecialRateCode.openSelect();
                }
            });
            return false;
        }

        if (!utils_validate_autocompletecombo(cboTransferRateCode))
        {
            dhtmlx.alert({
                text: "Please select a valid Transfer Rate Code!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboTransferRateCode.openSelect();
                }
            });
            return false;
        }

        return true;
    }

    function validateMarket()
    {
        if (!form_markets.validate())
        {
            dhtmlx.alert({
                text: "Please select one Country for the Market!",
                type: "alert-warning",
                title: "Save Company",
                callback: function () {
                    tree_menu.selectItem("markets", true, false);
                }
            });
            return false;
        }
        return true;
    }

    function validateCompanies()
    {
        var checkedids = grid_companies.getCheckedRows(0);
        if (checkedids == "")
        {
            dhtmlx.alert({
                text: "Please select at least one attached Company!",
                type: "alert-warning",
                title: "Save Company",
                callback: function () {
                    tree_menu.selectItem("companies", true, false);
                }
            });
            return false;
        }

        form_companies.setItemValue("selected_companies_ids", checkedids);

        return true;
    }


    function validateAPI()
    {
        if (form_api.isItemChecked("api_active"))
        {
            if (utils_trim(form_api.getItemValue("api_token"), " ") == "")
            {
                dhtmlx.alert({
                    text: "Please enter an API Token!",
                    type: "alert-warning",
                    title: "Save",
                    callback: function () {
                        tree_menu.selectItem("api", true, false);
                        form_api.setItemFocus("api_token");
                    }
                });
                return false;
            }
        }

        return true;
    }

    function validateContactGrid()
    {
        for (var i = 0; i < grid_contact.getRowsNum(); i++) {


            var id = grid_contact.getRowId(i);
            var deptfk = grid_contact.cells(id, grid_contact.getColIndexById("deptfk")).getValue();
            var contact_name = grid_contact.cells(id, grid_contact.getColIndexById("contact_name")).getValue();
            var contact_email = grid_contact.cells(id, grid_contact.getColIndexById("contact_email")).getValue();
            var action = grid_contact.cells(id, grid_contact.getColIndexById("action")).getValue();

            if (action != "DELETE")
            {
                if (deptfk == "")
                {
                    dhtmlx.alert({
                        text: "Please select a Contact Department",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("contacts", true, false);
                            grid_contact.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (contact_name == "")
                {
                    dhtmlx.alert({
                        text: "Please enter a Contact Name",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("contacts", true, false);
                            grid_contact.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (contact_email == "" || !utils_validateEmail(contact_email))
                {
                    dhtmlx.alert({
                        text: "Please enter a valid Email Address",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("contacts", true, false);
                            grid_contact.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
            }
        }

        return true;
    }


    function showPopUpCountries(form, caller, inputdisplay, inputid)
    {
        var dim = popupwin_touroperators.getDimension();
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

                    //============================================================
                    if (arr_ids.length > 1)
                    {
                        dhtmlx.alert({
                            text: "Only ONE Country is to be Selected!",
                            type: "alert-warning",
                            title: "Select Market Country",
                            callback: function () {
                            }
                        });
                        return;
                    }
                    //============================================================

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
        pop_grid.loadXML("php/api/touroperators/marketgridxml.php?t=" + encodeURIComponent(global_token), function () {
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
        popupwin_touroperators.setModal(false);
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

    //==============================================

    popupwin_touroperators.hide();
    popupwin_users.hide();
    popupwin_users_attach.hide();

    applyrights();
}