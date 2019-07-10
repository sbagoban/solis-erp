var markets_obj = new markets();

function markets()
{


    document.getElementById("aTitle").innerHTML = "MARKETS";

    var outer_layout = new dhtmlXLayoutObject("main_body", "1C");
    outer_layout.cells("a").hideHeader();

    resizeLayout();

    var main_layout = outer_layout.cells("a").attachLayout("2U");

    main_layout.cells('a').setText("Markets");
    main_layout.cells('b').setText("Countries");


    var grid_markets = main_layout.cells("a").attachGrid();
    grid_markets.setIconsPath('libraries/dhtmlx/imgs/');
    grid_markets.setHeader("Name,Active,Description");
    grid_markets.setColumnIds("market_name,active,market_description");
    grid_markets.setColTypes("tree,ch,ro");
    grid_markets.setInitWidths("200,80,300");
    grid_markets.setColAlign("left,center,left");
    grid_markets.setColSorting('str,int,str');
    grid_markets.attachHeader("#text_filter,#select_filter,#text_filter");
    grid_markets.setEditable(false);
    grid_markets.enableSmartXMLParsing(true);
    grid_markets.enableTreeGridLines();
    grid_markets.attachEvent("onRowSelect", onMarketSelect);
    grid_markets.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    var opts = Array(
            Array('new', 'obj', 'Add New Market', 'add.png'),
            Array('new_child', 'obj', 'Add New Sub Market', 'add.png')
            );
    toolbar.addButtonSelect("opts", 1, "New", opts, "operate.png", "operate.png", null, true);
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.setIconSize(32);

    var grid_countries = main_layout.cells("b").attachGrid();
    grid_countries.setIconsPath('libraries/dhtmlx/imgs/');
    grid_countries.setHeader(",Country,Continent");
    grid_countries.setColumnIds("X,country_name,continent");
    grid_countries.setColTypes("ch,ro,ro");
    grid_countries.setInitWidths("50,*,200");
    grid_countries.setColAlign("center,left,left");
    grid_countries.setColSorting('int,str,str');
    grid_countries.attachHeader("#master_checkbox,#text_filter,#select_filter");
    grid_countries.init();


    var toolbar_countries = main_layout.cells("b").attachToolbar();
    toolbar_countries.setIconsPath("images/");
    toolbar_countries.addButton("attach", 1, "Attach Countries", "add.png", "add.png");
    toolbar_countries.addButton("remove", 2, "Remove Countries", "delete.png", "delete.png");
    toolbar_countries.setIconSize(32);


    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_markets.clear();
            form_markets.setItemValue("id", "-1");
            form_markets.setItemValue("market_parent_fk", "");
            form_markets.setItemValue("market_parent", "");
            form_markets.hideItem("market_parent");
            form_markets.setItemValue("active", "1");

            popupwin_markets.setModal(true);
            popupwin_markets.center();
            popupwin_markets.show();


        } else if (id == "new_child")
        {
            var marketid = grid_markets.getSelectedRowId();

            if (!marketid)
            {
                dhtmlx.alert({
                    text: "Please select a Market!",
                    type: "alert-warning",
                    title: "New Sub Market",
                    callback: function () {
                    }
                });
                return;
            }

            var parent_market_name = dsMarkets.item(marketid).market_name;

            form_markets.clear();
            form_markets.setItemValue("id", "-1");
            form_markets.setItemValue("market_parent_fk", marketid);
            form_markets.setItemValue("market_parent", parent_market_name);
            form_markets.showItem("market_parent");
            form_markets.setItemValue("active", "1");

            popupwin_markets.setModal(true);
            popupwin_markets.center();
            popupwin_markets.show();


        } else if (id == "modify")
        {
            var uid = grid_markets.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsMarkets.item(uid);
            form_markets.setFormData(data);

            popupwin_markets.setModal(true);
            popupwin_markets.center();
            popupwin_markets.show();


        } else if (id == "delete")
        {
            var gid = grid_markets.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Market",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/markets/deletemarket.php", params, function (loader) {

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
                                    grid_markets.deleteRow(gid);
                                    grid_countries.clearAll();
                                    dsCountries.clearAll();
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


    toolbar_countries.attachEvent("onClick", function (id) {
        if (id == "attach")
        {
            var marketid = grid_markets.getSelectedRowId();

            if (!marketid)
            {
                dhtmlx.alert({
                    text: "Please select a Market!",
                    type: "alert-warning",
                    title: "ATTACH",
                    callback: function () {
                    }
                });
                return;
            }

            attachlayout.cells("a").progressOn();
            dsAttach.clearAll();
            grid_attach.clearAll();

            dsAttach = null;
            dsAttach = new dhtmlXDataStore();

            dsAttach.load("php/api/markets/attachgrid.php?t=" + encodeURIComponent(global_token) + "&marketid=" + marketid, "json", function () {
                attachlayout.cells("a").progressOff();
                grid_attach.sync(dsAttach);
            });

            popupwin_countries.setModal(true);
            popupwin_countries.center();
            popupwin_countries.show();


        } else if (id == "remove")
        {

            var checkedids = grid_countries.getCheckedRows(0);
            if (checkedids == "")
            {
                dhtmlx.alert({
                    text: "Please select at least one Country!",
                    type: "alert-warning",
                    title: "Remove Country",
                    callback: function () {

                    }
                });
                return;
            }


            dhtmlx.confirm({
                title: "Remove Country",
                type: "confirm",
                text: "Confirm Removal?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "checkedids=" + checkedids + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/markets/removecountry.php", params, function (loader) {

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "REMOVE",
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
                                        title: "REMOVE",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    grid_markets.selectRowById(grid_markets.getSelectedRowId(), false, true, true);
                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "REMOVE",
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

    main_layout.cells("a").progressOn();

    var dsMarkets = new dhtmlXDataStore();

    loadMarkets("");


    function loadMarkets(selectmarketid)
    {
        grid_markets.clearAll();
        grid_countries.clearAll();
        dsMarkets.clearAll();
        dsMarkets = null;
        dsMarkets = new dhtmlXDataStore();
        dsMarkets.load("php/api/markets/marketgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
            grid_markets.loadXML("php/api/markets/marketgridxml.php?t=" + encodeURIComponent(global_token), function () {
                main_layout.cells("a").progressOff();

                if (selectmarketid != "")
                {   
                    var parentrwid = grid_markets.getParentId(selectmarketid);
                    grid_markets.openItem(parentrwid);
                    
                    grid_markets.selectRowById(selectmarketid, false, true, true);
                    
                } else if (grid_markets.getRowsNum() > 0) {
                    //select first row
                    grid_markets.selectRow(0, true, false, true);
                }
            });

        });
    }


    var dsCountries = new dhtmlXDataStore();

    function onMarketSelect(id, ind) {
        dsCountries.clearAll();
        grid_countries.clearAll();

        dsCountries = null;
        dsCountries = new dhtmlXDataStore();

        var marketname = dsMarkets.item(id).market_name;

        main_layout.cells("b").progressOn();
        dsCountries.load("php/api/markets/countrygrid.php?t=" + encodeURIComponent(global_token) + "&marketid=" + id, "json", function () {
            main_layout.cells("b").progressOff();
            main_layout.cells("b").setText("Countries for Market: " + marketname);
            grid_countries.sync(dsCountries);

        });
    }



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

            outer_layout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(outer_layout.cells("a"));

    var popupwin_markets = dhxWins.createWindow("popupwin_markets", 50, 50, 700, 420);
    popupwin_markets.setText("Market Details:");
    popupwin_markets.denyResize();
    popupwin_markets.denyPark();


    var popupwin_countries = dhxWins.createWindow("popupwin_countries", 50, 50, 500, 500);
    popupwin_countries.setText("Attach Countries:");
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
        {type: "settings", position: "label-left", id: "form_markets"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "hidden", name: "market_parent_fk"},
        {type: "input", name: "market_name", label: "Name:", labelWidth: "100",
            labelHeight: "22", inputWidth: "500", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "market_parent", label: "Parent Market:", labelWidth: "100",
            labelHeight: "22", inputWidth: "500", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
        },
        {type: "editor", name: "market_description", label: "Description:", labelWidth: "100",
            labelHeight: "22", inputWidth: "500", inputHeight: "200", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "checkbox", name: "active", label: "Active:", labelWidth: "100",
            labelHeight: "22", inputWidth: "500", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var marketlayout = popupwin_markets.attachLayout("1C");

    marketlayout.cells("a").hideHeader();

    var form_markets = marketlayout.cells("a").attachForm(str_frm_ug);
    
    form_markets.getInput("market_parent").style.backgroundColor = "#F3E2A9";


    form_markets.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_markets.setModal(false);
            popupwin_markets.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_markets.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Market",
                    callback: function () {
                    }
                });
                return;
            }


            marketlayout.cells("a").progressOn();

            form_markets.setItemValue("token", global_token);

            form_markets.send("php/api/markets/savemarket.php", "post", function (loader)
            {
                marketlayout.cells("a").progressOff();

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
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });


                        loadMarkets(json_obj.ID);
                        popupwin_markets.setModal(false);
                        popupwin_markets.hide();



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
    });


    var dsAttach = new dhtmlXDataStore();

    var attachlayout = popupwin_countries.attachLayout("1C");
    attachlayout.cells("a").hideHeader();

    var grid_attach = attachlayout.cells("a").attachGrid();
    grid_attach.setIconsPath('libraries/dhtmlx/imgs/');
    grid_attach.setHeader(",Country,Continent");
    grid_attach.setColumnIds("X,country_name,continent");
    grid_attach.setColTypes("ch,ro,ro");
    grid_attach.setInitWidths("50,*,200");
    grid_attach.setColAlign("center,left,left");
    grid_attach.setColSorting('int,str,str');
    grid_attach.attachHeader("#master_checkbox,#text_filter,#select_filter");
    grid_attach.init();


    var toolbar_attach = attachlayout.cells("a").attachToolbar();
    toolbar_attach.setIconsPath("images/");
    toolbar_attach.addButton("attach", 1, "Attach Countries", "add.png", "add.png");
    toolbar_attach.setIconSize(32);


    toolbar_attach.attachEvent("onClick", function (id) {
        if (id == "attach")
        {
            //clear all filters
            for (var i = 0; i < grid_attach.getColumnCount(); i++) {
                var filter = grid_attach.getFilterElement(i);
                if (filter)
                    filter.value = '';
            }
            grid_attach.filterByAll();


            var checked = grid_attach.getCheckedRows(0);
            if (checked == "")
            {
                dhtmlx.alert({
                    text: "Please select at least one Country",
                    type: "alert-warning",
                    title: "ATTACH",
                    callback: function () {
                    }
                });

                return;
            }

            var params = "countryids=" + checked + "&t=" + encodeURIComponent(global_token) + "&marketid=" + grid_markets.getSelectedRowId();
            dhtmlxAjax.post("php/api/markets/attachcountries.php", params, function (loader) {

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
                        popupwin_countries.setModal(false);
                        popupwin_countries.hide();
                        grid_markets.selectRowById(grid_markets.getSelectedRowId(), false, true, true);

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




    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {
            if (json_rights[i].PROCESSNAME == "ADD" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableListOption("opts", "new");
                toolbar.disableListOption("opts", "new_child");

                toolbar.setListOptionToolTip("opts", "new", "Not Allowed");
                toolbar.setListOptionToolTip("opts", "new_child", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("delete");
                toolbar.setItemToolTip("delete", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "ATTACH" && json_rights[i].ALLOWED == "N")
            {
                toolbar_countries.disableItem("attach");
                toolbar_countries.setItemToolTip("attach", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "REMOVE" && json_rights[i].ALLOWED == "N")
            {
                toolbar_countries.disableItem("remove");
                toolbar_countries.setItemToolTip("remove", "Not Allowed");
            }
            else if (json_rights[i].PROCESSNAME == "MODIFY" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("modify");
                toolbar.setItemToolTip("modify", "Not Allowed");
            }
        }
    }

    applyrights();

    popupwin_markets.hide();
    popupwin_countries.hide();



    //main_layout.cells('b').setWidth(2200);
    //main_layout.cells('a').setWidth(400);

}