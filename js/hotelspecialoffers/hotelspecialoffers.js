var hotelspecialoffers_obj = new hotelspecialoffers();

function hotelspecialoffers()
{
    var popupwin = null;
    var pop_grid = null;
    var pop_layout = null;
    var pop_form = null;
    var pop_toolbar = null;

    var _last_popup_ids = "";
    var _last_popup_display_values = "";

    var _agecolwidth = 35;

    var _dsRooms = new dhtmlXDataStore();
    var _dsRatesCombo = new dhtmlXDataStore();
    var _dsDatePeriods = new dhtmlXDataStore();
    var _dsMealPlans = new dhtmlXDataStore();
    var _dsChildPolicy = new dhtmlXDataStore();
    var _dsCurrencies = new dhtmlXDataStore();
    var _dsTaxCommiItems = new dhtmlXDataStore();


    var _period_id = -1;
    var _free_nights_id = -1;
    var _free_nights_period_id = -1;
    var _family_discounts_child_discount = -1;
    var _upgrade_id = -1;
    var _meal_upgrade_id = -1;
    var _flat_rate_validity_period_id = -1;
    var _flat_rate_supplement_id = -1;
    var _flat_rate_checkinout_id = -1;
    var _flat_rate_cancellation_id = -1;
    var _flat_rate_exchange_rate_id = -1;
    var _flat_rate_currency_map_id = -1;
    var _flat_rate_taxcommi_settings_id = -1;
    var _flat_rate_taxcommi_settings_value_id = -1;

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

    var _last_grid_choice_id = "";

    var _flat_rate_tax_commi_obj = {buying_settings: [], selling_settings: []};
    var _json_capacity = [];


    //load the templates into the array
    var arr_templates = [];


    /*
     var arr_templates = [{value: "early_booking", text: "Early Booking", tabs: ["name", "periods", "conditions", "applicable", "discounts"]}, //--
     {value: "long_stay", text: "Long Stay", tabs: ["name", "periods", "conditions", "applicable", "discounts"]}, //--
     {value: "honeymoon", text: "Honeymoon", tabs: ["name", "periods", "conditions", "applicable", "wedding_discounts"]}, //--
     {value: "free_nights", text: "Free Nights", tabs: ["name", "periods", "conditions", "applicable", "free_nights"]}, //--
     {value: "flat_rate", text: "Flat Rate", tabs: ["name", "periods", "conditions", "flat_rate_periods", "flat_rate_policies", "flat_rate_currency", "flat_rate_commission", "flat_rate_rates"]},
     {value: "free_upgrade", text: "Free Upgrade", tabs: ["name", "periods", "conditions", "applicable", "upgrade"]}, //--
     {value: "wedding_anniversary", text: "Wedding Anniversary", tabs: ["name", "periods", "conditions", "applicable", "wedding_anniversary"]}, //--
     {value: "family_offer", text: "Family Offer", tabs: ["name", "periods", "conditions", "applicable", "family_discount"]}, //--
     {value: "wedding_party", text: "Wedding Party", tabs: ["name", "periods", "conditions", "applicable", "wedding_party"]}, //--
     {value: "senior_offer", text: "Senior Offer", tabs: ["name", "periods", "conditions", "applicable", "senior"]}, //--
     {value: "meals_upgrade", text: "Meals Upgrade", tabs: ["name", "periods", "conditions", "applicable", "meals_upgrade"]}, //--
     {value: "discount", text: "Discount", tabs: ["name", "periods", "conditions", "applicable", "discounts"]}]; //--
     
     arr_templates.sort((a, b) => (a.text > b.text) ? 1 : ((b.text > a.text) ? -1 : 0));
     
     */

    document.getElementById("aTitle").innerHTML = "List of Special Offers: ";

    var spolayout = new dhtmlXLayoutObject("main_body", "1C");
    spolayout.cells("a").hideHeader();

    var grid_spo = spolayout.cells("a").attachGrid();
    grid_spo.setIconsPath('libraries/dhtmlx/imgs/');
    grid_spo.setHeader(",ID,SPO Name,Active Internal,Active External,Type,Code,Template,Tour Operators,Countries,Rate,Valid Dates");
    grid_spo.setColumnIds("subgrid,id,sponame,active_internal,active_external,spo_type,spocode,template,tour_operator_names,countries,ratecodes,validities");
    grid_spo.setColTypes("sub_row_grid,ro,ro,ch,ch,ro,ro,ro,ro,ro,ro,ro");
    grid_spo.setInitWidths("30,70,250,55,55,100,100,120,1000,1000,50,200");
    grid_spo.setColAlign("center,center,left,center,center,center,center,center,center,center,center,center");
    grid_spo.setColSorting("str,int,str,int,int,str,str,str,str,str,date");
    grid_spo.attachHeader(",#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter,#text_filter");
    grid_spo.setEditable(false);
    grid_spo.enableMultiline(true);
    grid_spo.enableAlterCss("", "");
    grid_spo.init();

    var toolbar_spo = spolayout.cells("a").attachToolbar();
    toolbar_spo.setIconsPath("images/");
    toolbar_spo.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_spo.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_spo.addButton("copypaste", 3, "Copy Paste", "copypaste.png", "copypaste.png");
    toolbar_spo.addButton("linking", 4, "Linking", "network.png", "network.png");
    toolbar_spo.addButton("delete", 5, "Delete", "delete.png", "delete.png");
    toolbar_spo.addButton("export", 6, "Export Excel", "excel.png");
    toolbar_spo.addSpacer("export");
    toolbar_spo.addButton("back", 7, "Back to Hotels", "exit.png", "exit.png");
    toolbar_spo.setIconSize(32);

    toolbar_spo.attachEvent("onClick", function (id) {

        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        } else if (id == "new")
        {
            newSPO();

        } else if (id == "linking")
        {
            linkSPO();

        } else if (id == "copypaste")
        {
            var cid = grid_spo.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            modifySPO(cid, true);
        } else if (id == "modify")
        {
            var cid = grid_spo.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            modifySPO(cid, false);
        } else if (id == "export")
        {
            grid_spo.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "delete")
        {
            var cid = grid_spo.getSelectedRowId();
            if (!cid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Offer?",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "id=" + cid + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);

                        spolayout.cells("a").progressOn();
                        dhtmlxAjax.post("php/api/hotelspecialoffers/deletespo.php", params, function (loader) {
                            spolayout.cells("a").progressOff();

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DELETE OFFER",
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
                                        title: "DELETE OFFER",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    grid_spo.deleteRow(cid);

                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DELETE OFFER",
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
            if (json_rights[i].PROCESSNAME == "ADD SPECIAL OFFER" && json_rights[i].ALLOWED == "N")
            {
                toolbar_spo.disableItem("new");
                toolbar_spo.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY SPECIAL OFFER" && json_rights[i].ALLOWED == "N")
            {
                toolbar_spo.disableItem("modify");
                toolbar_spo.setItemToolTip("modify", "Not Allowed");

            } else if (json_rights[i].PROCESSNAME == "DELETE SPECIAL OFFER" && json_rights[i].ALLOWED == "N")
            {
                toolbar_spo.disableItem("delete");
                toolbar_spo.setItemToolTip("delete", "Not Allowed");
            }
        }
    }




    function loadHotelSPOs(select_spo_id)
    {
        spolayout.cells("a").progressOn();
        grid_spo.clearAll();

        var dsHotel = new dhtmlXDataStore();
        dsHotel.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
            document.getElementById("aTitle").innerHTML = "List of Special Offers: <b>" + dsHotel.item(global_hotel_id).hotelname + "</b>";
        });


        grid_spo.loadXML("php/api/hotelspecialoffers/specialoffergrid_xml.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, function () {

            spolayout.cells("a").progressOff();
            grid_spo.forEachRow(function (rwid) {
                grid_spo.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_spo.setCellTextStyle(rwid, ind, cellstyle);
                });
            });

            if (select_spo_id != "")
            {
                grid_spo.selectRowById(select_spo_id, false, true, false);
            }
        });
    }

    function modifySPO(spo_id, flg_copypaste)
    {

        popupwin_spo.center();
        popupwin_spo.setModal(true);
        popupwin_spo.show();

        tabSpo.setTabActive("name");

        clearSPO();

        form_name.setItemValue("id", spo_id);
        form_name.setItemValue("hotel_fk", global_hotel_id);

        tabSpo.hideTab("discounts");
        tabSpo.hideTab("applicable");
        tabSpo.hideTab("wedding_discounts");
        tabSpo.hideTab("upgrade");
        tabSpo.hideTab("meals_upgrade");
        tabSpo.hideTab("family_discount");
        tabSpo.hideTab("wedding_party");
        tabSpo.hideTab("wedding_anniversary");
        tabSpo.hideTab("senior");
        tabSpo.hideTab("free_nights");
        tabSpo.hideTab("flat_rate_periods");
        tabSpo.hideTab("flat_rate_policies");
        tabSpo.hideTab("flat_rate_currency");
        tabSpo.hideTab("flat_rate_commission");
        tabSpo.hideTab("flat_rate_rates");

        //load the spo
        winspo_layout.progressOn();
        var params = "spoid=" + spo_id + "&hid=" + global_hotel_id + "&t=" + encodeURIComponent(global_token);
        dhtmlxAjax.post("php/api/hotelspecialoffers/loadspo.php", params, function (loader) {
            winspo_layout.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "LOAD OFFER",
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
                        title: "LOAD OFFER",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {

                    form_name.setFormData(json_obj.SPO.GENERAL);
                    form_periods.setFormData(json_obj.SPO.GENERAL);
                    form_conditions.setFormData(json_obj.SPO.GENERAL);
                    form_applicable.setFormData(json_obj.SPO.GENERAL);
                    form_wedding_anniversary_discounts.setFormData(json_obj.SPO.GENERAL);
                    form_wedding_discounts.setFormData(json_obj.SPO.GENERAL);
                    form_wedding_party_discounts.setFormData(json_obj.SPO.GENERAL);
                    form_discounts.setFormData(json_obj.SPO.GENERAL);
                    form_senior_discounts.setFormData(json_obj.SPO.GENERAL);
                    form_familydiscounts.setFormData(json_obj.SPO.GENERAL);
                    form_free_nights.setFormData(json_obj.SPO.GENERAL);

                    triggerConditionsChange("min_stay_priority", "");

                    toggleSPOTabsVisible(json_obj.SPO.GENERAL.template);
                    toggleSPOTabsEnable("name");

                    form_name.disableItem("template");

                    loadPeriodsGrid(json_obj.SPO.VALIDITY_PERIODS);

                    if (json_obj.SPO.GENERAL.template == "free_nights")
                    {
                        loadFreeNightsValidityGrid(json_obj.SPO.FREE_NIGHTS_VALIDITY);
                        loadFreeNightsGrid(json_obj.SPO.FREE_NIGHTS);

                    } else if (json_obj.SPO.GENERAL.template == "free_upgrade")
                    {
                        loadRoomUpgradeGrid(json_obj.SPO.UPGRADE_ROOMS);
                    } else if (json_obj.SPO.GENERAL.template == "meals_upgrade")
                    {
                        loadMealUpgradeGrid(json_obj.SPO.UPGRADE_MEALS);
                    } else if (json_obj.SPO.GENERAL.template == "family_offer")
                    {
                        loadFamilyOfferChildAgeGrid(json_obj.SPO.FAMILY_OFFER_CHLDRENAGE);
                    } else if (json_obj.SPO.GENERAL.template == "flat_rate")
                    {

                        _json_capacity = json_obj.SPO.FLAT_RATES_CAPACITY;
                        _flat_rate_tax_commi_obj = json_obj.SPO.FLAT_RATES_TAX_COMMI;

                        loadFlatRateGroupValidity(json_obj.SPO.FLAT_RATES_VALIDY_PERIOD_GROUP);
                        loadFlatRateSupplements(json_obj.SPO.FLAT_RATES_SUPPLEMENTS);
                        loadFlatRateCheckInOut(json_obj.SPO.FLAT_RATES_CHECKINOUT);
                        loadFlatRateCancellation(json_obj.SPO.FLAT_RATES_CANCELLATION);
                        loadFlatRateCurrency(json_obj.SPO.FLAT_RATES_CURRENCY_BUY, json_obj.SPO.FLAT_RATES_CURRENCY_SELL, json_obj.SPO.GENERAL);
                        loadFlatRateExgRates(json_obj.SPO.FLAT_RATES_EXCHANGE_RATES);
                        loadFlatRateMapping(json_obj.SPO.FLAT_RATES_MAPPING);

                    }

                    var template_name = cboTemplate.getComboText();
                    popupwin_spo.setText("Special Offer Details: <b>" + template_name + "</b>");

                    if (flg_copypaste)
                    {
                        resetIds();
                    }
                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "LOAD OFFER",
                        callback: function () {
                        }
                    });
                }
            }
        });
    }

    function newSPO()
    {
        popupwin_spo.center();
        popupwin_spo.setModal(true);
        popupwin_spo.show();
        popupwin_spo.setText("Special Offer Details:");

        tabSpo.setTabActive("name");

        clearSPO();

        form_name.setItemValue("id", "-1");
        form_name.setItemValue("hotel_fk", global_hotel_id);
        form_name.enableItem("template");

        tabSpo.hideTab("discounts");
        tabSpo.hideTab("applicable");
        tabSpo.hideTab("wedding_discounts");
        tabSpo.hideTab("upgrade");
        tabSpo.hideTab("meals_upgrade");
        tabSpo.hideTab("family_discount");
        tabSpo.hideTab("wedding_party");
        tabSpo.hideTab("wedding_anniversary");
        tabSpo.hideTab("senior");
        tabSpo.hideTab("free_nights");
        tabSpo.hideTab("flat_rate_periods");
        tabSpo.hideTab("flat_rate_policies");
        tabSpo.hideTab("flat_rate_currency");
        tabSpo.hideTab("flat_rate_commission");
        tabSpo.hideTab("flat_rate_rates");

        toggleSPOTabsEnable("name");

        //=============================================


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

            spolayout.setSizes(true);

        }, 1);
    }

    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(spolayout.cells("a"));

    var popupwin_spo = dhxWins.createWindow("popupwin_spo", 50, 50, 700, 440);
    popupwin_spo.setText("Special Offer Details:");

    var x = $("#main_body").parent().width() - 20;
    var body = document.body,
            html = document.documentElement;
    var y = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
    y -= 170;

    popupwin_spo.setDimension(x, y);

    popupwin_spo.denyResize();
    popupwin_spo.denyPark();
    popupwin_spo.button("close").hide();

    //===============================================================================
    var popupwin_link = dhxWins.createWindow("popupwin_link", 50, 50, 700, 440);
    popupwin_link.setText("Special Offer Linking:");
    popupwin_link.setDimension(x, y);
    popupwin_link.denyResize();
    popupwin_link.denyPark();
    popupwin_link.button("close").hide();

    var spolayout_link = popupwin_link.attachLayout("1C");
    spolayout_link.cells("a").hideHeader();

    var grid_spo_link = spolayout_link.cells("a").attachGrid();
    grid_spo_link.setIconsPath('libraries/dhtmlx/imgs/');
    grid_spo_link.setHeader("LINK,#cspan,SPECIAL OFFER,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan,#cspan");
    grid_spo_link.setColumnIds("description,active,template,spocode,sponame,cumulative,active_internal,active_external,spo_type,linkid,linklineid,spoid");
    grid_spo_link.setColTypes("ed,ch,ro,ro,ro,ch,ro,ro,ro,ro,ro,ro");
    grid_spo_link.setInitWidths("200,50,200,200,200,80,80,80,100,0,0,0");
    grid_spo_link.setColAlign("left,center,left,left,left,center,center,center,center,center,center,center");
    grid_spo_link.setColSorting("str,int,str,str,str,int,str,str,str,int,int,int");
    grid_spo_link.attachHeader("Description,Active,Template,Code,Name,Cumulative,Active Internal,Active External,Type,LinkId,LinkLineId,Spoid");
    grid_spo_link.attachHeader("#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,,,");
    grid_spo_link.enableMultiline(true);
    grid_spo_link.enableColSpan(true);
    grid_spo_link.enableRowspan(true);
    grid_spo_link.enableUndoRedo();
    grid_spo_link.enableAlterCss("", "");
    grid_spo_link.attachEvent("onEditCell", onGridSpoLinkEdit);
    grid_spo_link.attachEvent("onCheck", onGridSpoLinkCheck);
    grid_spo_link.init();

    var toolbar_spo_link = spolayout_link.cells("a").attachToolbar();
    toolbar_spo_link.setIconsPath("images/");
    toolbar_spo_link.addButton("new_link", 1, "Create New Link", "new_network.png", "new_network.png");
    toolbar_spo_link.addButton("attach_spo", 2, "Attach SPO", "add.png", "add.png");
    toolbar_spo_link.addSeparator("sep1", 3);
    toolbar_spo_link.addButton("delete_link", 4, "Delete Link", "delete.png", "delete.png");
    toolbar_spo_link.addButton("remove_spo", 5, "Remove SPO", "delete.png", "delete.png");
    toolbar_spo_link.addSpacer("remove_spo");
    toolbar_spo_link.addButton("back", 6, "Back to SPO", "exit.png", "exit.png");
    toolbar_spo_link.setIconSize(32);

    toolbar_spo_link.attachEvent("onClick", function (id) {

        if (id == "new_link")
        {
            newSPOLink();

        } else if (id == "attach_spo")
        {
            var cid = grid_spo_link.getSelectedRowId();
            if (!cid)
            {
                return;
            }


            var linkid = grid_spo_link.cells(cid, grid_spo_link.getColIndexById("linkid")).getValue();


            attachSPO(linkid);

        } else if (id == "back")
        {
            popupwin_link.setModal(false);
            popupwin_link.hide();
        } else if (id == "delete_link")
        {
            var cid = grid_spo_link.getSelectedRowId();
            if (!cid)
            {
                return;
            }


            var linkid = grid_spo_link.cells(cid, grid_spo_link.getColIndexById("linkid")).getValue();

            dhtmlx.confirm({
                title: "Delete Whole Link?",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "linkid=" + linkid + "&t=" + encodeURIComponent(global_token);

                        spolayout_link.cells("a").progressOn();
                        dhtmlxAjax.post("php/api/hotelspecialoffers/deletespolink.php", params, function (loader) {
                            spolayout_link.cells("a").progressOff();

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "DELETE LINK",
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
                                        title: "DELETE LINK",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    //delete row with  rows with linkid belonging to the one deleted
                                    grid_spo_link.forEachRow(function (rid) {
                                        var _linkid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("linkid")).getValue();

                                        if (_linkid == linkid)
                                        {
                                            grid_spo_link.deleteRow(rid);
                                        }
                                    });

                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "DELETE LINK",
                                        callback: function () {
                                        }
                                    });
                                }

                            }
                        });
                    }
                }
            });
        } else if (id == "remove_spo")
        {
            var cid = grid_spo_link.getSelectedRowId();
            if (!cid)
            {
                return;
            }

            var linklineid = grid_spo_link.cells(cid, grid_spo_link.getColIndexById("linklineid")).getValue();
            var linkid = grid_spo_link.cells(cid, grid_spo_link.getColIndexById("linkid")).getValue();

            if (linklineid == "")
            {
                return;
            }


            dhtmlx.confirm({
                title: "Remove SPO from Link?",
                type: "confirm",
                text: "Confirm Removal?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "linklineid=" + linklineid + "&t=" + encodeURIComponent(global_token);

                        spolayout_link.cells("a").progressOn();
                        dhtmlxAjax.post("php/api/hotelspecialoffers/removespofromlink.php", params, function (loader) {
                            spolayout_link.cells("a").progressOff();

                            if (loader)
                            {
                                if (loader.xmlDoc.responseURL == "")
                                {
                                    dhtmlx.alert({
                                        text: "Connection Lost!",
                                        type: "alert-warning",
                                        title: "REMOVE SPO",
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
                                        title: "REMOVE SPO",
                                        callback: function () {
                                        }
                                    });
                                    return false;
                                }
                                if (json_obj.OUTCOME == "OK")
                                {
                                    //reload grid and select the link
                                    loadSpoLinkGrid(selectSPOLink_link, linkid);

                                } else
                                {
                                    dhtmlx.alert({
                                        text: json_obj.OUTCOME,
                                        type: "alert-warning",
                                        title: "REMOVE SPO",
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


    //===============================================================================
    var popupwin_loadperiods = dhxWins.createWindow("popupwin_loadperiods", 50, 50, 600, 500);
    popupwin_loadperiods.setText("Load Periods:");
    popupwin_loadperiods.denyResize();
    popupwin_loadperiods.denyPark();
    popupwin_loadperiods.button("close").hide();

    //===============================================================================
    var popupwin_loadslinkspo = dhxWins.createWindow("popupwin_loadslinkspo", 50, 50, x, 500);
    popupwin_loadslinkspo.setText("Link SPOs:");
    popupwin_loadslinkspo.denyResize();
    popupwin_loadslinkspo.denyPark();
    popupwin_loadslinkspo.button("close").hide();

    var spoattachlayout_link = popupwin_loadslinkspo.attachLayout("2E");
    spoattachlayout_link.cells("a").hideHeader();
    spoattachlayout_link.cells("b").hideHeader();
    spoattachlayout_link.cells("a").setHeight(500);
    spoattachlayout_link.cells("b").setHeight(50);

    var grid_attach_spo_link = spoattachlayout_link.cells("a").attachGrid();
    grid_attach_spo_link.setIconsPath('libraries/dhtmlx/imgs/');
    grid_attach_spo_link.setHeader(",SPO Name,Active Internal,Active External,Type,Code,Template,Tour Operators,Rate,Valid Dates");
    grid_attach_spo_link.setColumnIds("X,sponame,active_internal,active_external,spo_type,spocode,template,touroperatornames,ratecodes,validities");
    grid_attach_spo_link.setColTypes("ch,ro,ro,ro,ro,ro,ro,ro,ro,ro");
    grid_attach_spo_link.setInitWidths("50,250,60,60,160,200,200,200,50,200");
    grid_attach_spo_link.setColAlign("center,left,center,center,center,left,left,center,center,center");
    grid_attach_spo_link.setColSorting("str,str,int,int,str,str,str,str,str,date");
    grid_attach_spo_link.attachHeader("#master_checkbox,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#select_filter,#text_filter");
    grid_attach_spo_link.enableMultiline(true);
    grid_attach_spo_link.attachEvent("onRowSelect", function (rid, cid) {
        var selected = grid_attach_spo_link.cells(rid, grid_attach_spo_link.getColIndexById("X")).getValue();
        if (selected == 0)
        {
            selected = 1;
        } else
        {
            selected = 0;
        }

        grid_attach_spo_link.cells(rid, grid_attach_spo_link.getColIndexById("X")).setValue(selected);
    });
    grid_attach_spo_link.init();


    var str_frm_attach_spo_link = [
        {type: "settings", position: "label-left", id: "form_spo_attach_link"},
        {type: "block", width: 600, list: [
                {type: "button", name: "cmdCancel", value: "Cancel", width: "100", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", value: "Attach Selected SPOs", width: "200", height: "60", offsetLeft: 0}

            ]}];

    var form_spo_attach_link = spoattachlayout_link.cells("b").attachForm(str_frm_attach_spo_link);
    form_spo_attach_link.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_loadslinkspo.hide();
            popupwin_loadslinkspo.setModal(false);
            popupwin_link.setModal(true);

        } else if (name == "cmdSave")
        {
            attachSpoToLink();
        }
    });



    //===============================================================================
    var popupwin_newspolink = dhxWins.createWindow("popupwin_newspolink", 50, 50, 600, 150);
    popupwin_newspolink.setText("New SPO Link:");
    popupwin_newspolink.denyResize();
    popupwin_newspolink.denyPark();
    popupwin_newspolink.button("close").hide();

    var str_frm_spo_link = [
        {type: "settings", position: "label-left", id: "form_spo_link"},
        {type: "input", name: "description", label: "Description:",
            labelWidth: "90",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "checkbox", name: "active", label: "Active"},
        {type: "block", width: 300, list: [
                {type: "button", name: "cmdCancel", value: "Cancel", width: "100", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", value: "Save", width: "100", height: "60", offsetLeft: 0}

            ]}];

    var form_spo_link = popupwin_newspolink.attachForm(str_frm_spo_link);
    form_spo_link.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_newspolink.hide();
            popupwin_newspolink.setModal(false);
            popupwin_link.setModal(true);

        } else if (name == "cmdSave")
        {
            saveLink();
        }
    });

    //===============================================================================
    var popupwin_capacitycombinations = dhxWins.createWindow("popupwin_capacitycombinations", 50, 50, 900, 500);
    popupwin_capacitycombinations.setText("Room Combinations:");
    popupwin_capacitycombinations.denyResize();
    popupwin_capacitycombinations.denyPark();
    popupwin_capacitycombinations.button("close").hide();

    var layout_flatrate_capacitycombii = popupwin_capacitycombinations.attachLayout("1C");
    layout_flatrate_capacitycombii.cells("a").hideHeader();

    var toolbarFlatRateCombii = layout_flatrate_capacitycombii.attachToolbar();
    toolbarFlatRateCombii.setIconsPath("images/");
    toolbarFlatRateCombii.addText("text", 1, "");
    toolbarFlatRateCombii.addSpacer("text");
    toolbarFlatRateCombii.addButton("exit", 2, "Close", "exit.png", "exit.png");
    toolbarFlatRateCombii.setIconSize(32);
    toolbarFlatRateCombii.attachEvent("onClick", function (id) {

        if (id == "exit")
        {
            popupwin_capacitycombinations.setModal(false);
            popupwin_capacitycombinations.hide();
            popupwin_spo.setModal(true);
        }
    });



    //===============================================================================
    //SPECIAL OFFER DETAILS 
    var winspo_layout = popupwin_spo.attachLayout("1C");
    winspo_layout.cells("a").hideHeader();

    var tabSpo = winspo_layout.cells("a").attachTabbar();
    tabSpo.addTab("name", "<b>1. Name</b>", "180px", '');
    tabSpo.addTab("periods", "<b>2. Periods</b>", "180px", '');
    tabSpo.addTab("conditions", "<b>3. Conditions</b>", "180px", '');
    tabSpo.addTab("applicable", "<b>4. Applicable</b>", "180px", '');
    tabSpo.addTab("discounts", "<b>4. Discounts</b>", "180px", '');
    tabSpo.addTab("wedding_discounts", "<b>4. Wedding Discounts</b>", "200px", '');
    tabSpo.addTab("upgrade", "<b>5. Upgrade</b>", "180px", '');
    tabSpo.addTab("meals_upgrade", "<b>5. Meals Upgrade</b>", "180px", '');
    tabSpo.addTab("family_discount", "<b>4. Discounts</b>", "180px", '');
    tabSpo.addTab("wedding_party", "<b>4. Discounts</b>", "180px", '');
    tabSpo.addTab("wedding_anniversary", "<b>4. Discounts</b>", "180px", '');
    tabSpo.addTab("senior", "<b>4. Discounts</b>", "180px", '');
    tabSpo.addTab("free_nights", "<b>4. Free Nights</b>", "180px", '');
    tabSpo.addTab("flat_rate_periods", "<b>4. Validity Periods by Groups</b>", "180px", '');
    tabSpo.addTab("flat_rate_policies", "<b>5. Policies</b>", "180px", '');
    tabSpo.addTab("flat_rate_currency", "<b>6. Currency</b>", "180px", '');
    tabSpo.addTab("flat_rate_commission", "<b>7. Commission</b>", "180px", '');
    tabSpo.addTab("flat_rate_rates", "<b>8. Capacity</b>", "180px", '');


    //=====================================================================================
    //NAME TAB
    var str_frm_settings_name = [
        {type: "settings", position: "label-left", id: "form_name"},
        {type: "hidden", name: "hotel_fk"},
        {type: "block", width: 900, list: [
                {type: "checkbox", name: "active_internal", label: "Active Internal"},
                {type: "newcolumn"},
                {type: "checkbox", name: "active_external", label: "Active External"},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "combo", name: "spo_type", label: "Type:",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "checkbox", name: "non_refundable", label: "Non Refundable"},
                {type: "newcolumn"},
                {type: "input", name: "id", label: "SPO ID:",
                    labelHeight: "22", inputWidth: "70", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
                }
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "sponame", label: "Name:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "600", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },
                {type: "input", name: "spocode", label: "Code:",
                    labelWidth: "110",
                    labelHeight: "22", inputWidth: "600", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },
                {type: "combo", name: "template", label: "Template:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
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
                {type: "input", name: "market_countries_display", label: "Countries:", labelWidth: "110",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "market_countries_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadCountries", tooltip: "Select Market Countries", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

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

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}

    ];
    var form_name = tabSpo.cells("name").attachForm(str_frm_settings_name);
    form_name.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdNext")
        {
            nextStepName();
        } else if (name == "cmdLoadCountries")
        {
            showPopUpCountries(form_name, "Countries", "market_countries_display", "market_countries_ids", null);
        } else if (name == "cmdLoadRooms")
        {
            showPopUp(form_name, "Rooms", "rooms_display", "rooms_ids", _dsRooms, "MULTIPLE", null);
        } else if (name == "cmdLoadTourOperators")
        {
            showPopUpTourOperators(form_name, "Tour Operators", "tour_operators_display", "tour_operators_ids", "MULTIPLE", null);
        }
    });

    form_name.attachEvent("onChange", function (id, value) {
        if (id == "active_internal")
        {
            var chked = form_name.getItemValue("active_internal");
            if (chked == 0)
            {
                form_name.setItemValue("active_external", 0);
            }

        } else if (id == "active_external")
        {
            var ext_chked = form_name.getItemValue("active_external");
            var int_chked = form_name.getItemValue("active_internal");
            if (ext_chked == 1 && int_chked == 0)
            {
                form_name.setItemValue("active_external", 0);
            }
        }
    });

    var cboTemplate = form_name.getCombo("template");
    cboTemplate.enableOptionAutoPositioning(true);
    cboTemplate.readonly(true);

    var cboSpoType = form_name.getCombo("spo_type");
    cboSpoType.enableOptionAutoPositioning(true);
    cboSpoType.readonly(true);

    loadSpoCombos();

    //=====================================================================================
    //PERIODS TAB
    var spo_period_layout = tabSpo.cells("periods").attachLayout("3U");
    spo_period_layout.cells("a").hideHeader();
    spo_period_layout.cells("c").hideHeader();
    spo_period_layout.cells("c").setHeight(45);
    spo_period_layout.cells("a").setWidth(530);
    spo_period_layout.cells("b").setText("Validity Periods");

    spo_period_layout.cells("a").fixSize(true, true);
    spo_period_layout.cells("c").fixSize(true, true);

    var str_frm_settings_periods = [
        {type: "settings", position: "label-left", id: "form_periods"},
        {type: "block", width: 500, list: [
                {type: "combo", name: "rate_fk", label: "Rate Type:",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]},
        {type: "label", label: "<hr>"},
        {type: "checkbox", label: "Booking Period Date Before", name: "booking_before_date",
            width: 500, list: [
                {type: "calendar", name: "booking_before_date_from", label: "From:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                },
                {type: "newcolumn"},
                {type: "calendar", name: "booking_before_date_to", label: "To:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    dateFormat: "%d-%m-%Y",
                    note: {
                        text: "Format: dd-mm-yyyy"
                    }
                }
            ]},
        {type: "checkbox", label: "Booking Period Days Before", width: 500, name: "booking_before_days", list: [
                {type: "input", name: "booking_before_days_from", label: "From:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "input", name: "booking_before_days_to", label: "To:",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"}
            ]}
    ];
    var form_periods = spo_period_layout.cells("a").attachForm(str_frm_settings_periods);
    var cboRates = form_periods.getCombo("rate_fk");

    form_periods.attachEvent("onChange", function (id, value) {
        if (id == "booking_before_date")
        {
            if (!form_periods.isItemChecked("booking_before_date"))
            {
                form_periods.setItemValue("booking_before_date_from", "");
                form_periods.setItemValue("booking_before_date_to", "");
            }
        } else if (id == "booking_before_days")
        {
            if (!form_periods.isItemChecked("booking_before_days"))
            {
                form_periods.setItemValue("booking_before_days_from", "");
                form_periods.setItemValue("booking_before_days_to", "");
            }
        }
    });


    var toolbar_period = spo_period_layout.cells("b").attachToolbar();
    toolbar_period.setIconsPath("images/");
    toolbar_period.addButton("load", 1, "Load Seasons", "gantt_chart.png", "gantt_chart.png");
    toolbar_period.addButton("new", 2, "Add New", "add.png", "add.png");
    toolbar_period.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_period.setIconSize(32);
    toolbar_period.attachEvent("onClick", function (id) {
        if (id == "load")
        {
            loadPeriods()

        } else if (id == "new")
        {
            _period_id--;
            grid_period.addRow(_period_id, "");
            grid_period.setRowTextStyle(_period_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_period.selectRowById(_period_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_period.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_period.deleteRow(rid);
        }
    });


    var grid_period = spo_period_layout.cells("b").attachGrid();
    grid_period.setIconsPath('libraries/dhtmlx/imgs/');
    grid_period.setHeader("Season,Valid From,Valid To");
    grid_period.setColumnIds("season,valid_from,valid_to");
    grid_period.setColTypes("combo,dhxCalendar,dhxCalendar");
    grid_period.setInitWidths("200,100,100");
    grid_period.setColAlign("left,center,center");
    grid_period.setColSorting('str,date,date');
    grid_period.enableAlterCss("", "");
    grid_period.enableEditTabOnly(true);
    grid_period.enableEditEvents(true, true, true);
    grid_period.setDateFormat("%d-%m-%Y", "%Y-%m-%d");
    grid_period.attachEvent("onEditCell", onGridPeriodEdit);
    grid_period.enableUndoRedo();
    grid_period.init();

    grid_period.customGroupFormat = function (name, count) {

        var arr_seasons = groupSeasons(); //get an array of seasons for that spo
        for (var s = 0; s < arr_seasons.length; s++)
        {
            var season = arr_seasons[s].season;
            var seasonid = arr_seasons[s].seasonid;
            if (name == seasonid)
            {
                return season;
            }
        }

        return "CUSTOM";

    }

    var str_form_period_buttons = [
        {type: "settings", position: "label-left", id: "form_period_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_period_buttons = spo_period_layout.cells("c").attachForm(str_form_period_buttons);
    form_period_buttons.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("name");
            toggleSPOTabsEnable("name");

        } else if (name == "cmdNext")
        {
            nextStepPeriods();
        }
    });

    //=====================================================================
    //LOAD PERIODS WINDOW
    var periodslayout = popupwin_loadperiods.attachLayout("2E");
    periodslayout.cells("a").hideHeader();
    periodslayout.cells("b").hideHeader();

    periodslayout.cells("a").setHeight(500);
    periodslayout.cells("b").setHeight(50);

    periodslayout.cells("a").fixSize(true, true);
    periodslayout.cells("b").fixSize(true, true);

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

    var str_frm_loadperiods_actions = [
        {type: "settings", position: "label-right", id: "form_loadperiods_actions"},
        {type: "block", width: 500, list: [
                {type: "button",
                    name: "cmdLoad", value: "OK, Load Periods", width: "150", offsetLeft: 200},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}]}
    ];

    var form_loadperiods_actions = periodslayout.cells("b").attachForm(str_frm_loadperiods_actions);
    form_loadperiods_actions.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_loadperiods.setModal(false);
            popupwin_loadperiods.hide();
            popupwin_spo.setModal(true);
        }
        if (name == "cmdLoad")
        {
            applyLoadPeriods();
        }
    });


    //=====================================================================================
    //CONDITIONS TAB
    var spo_condition_layout = tabSpo.cells("conditions").attachLayout("2E");
    spo_condition_layout.cells("a").hideHeader();
    spo_condition_layout.cells("b").hideHeader();
    spo_condition_layout.cells("b").setHeight(45);
    spo_condition_layout.cells("a").fixSize(true, true);
    spo_condition_layout.cells("b").fixSize(true, true);


    var str_frm_settings_condtion = [
        {type: "settings", position: "label-left", id: "form_condition"},
        {type: "block", width: 900, list: [

                {type: "input", name: "meal_display", label: "Meal Plan:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "568", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "meals_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadMeals", tooltip: "Select Meal Plans", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "combo", name: "min_stay_priority", label: "Min Stay Priority:",
                    labelWidth: "130",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "input", name: "min_stay_from", label: "Min Stay From:",
                    labelWidth: "100",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidNumeric"
                },
                {type: "newcolumn"},
                {type: "input", name: "min_stay_to", label: "Min Stay To:",
                    labelWidth: "80",
                    labelHeight: "22", inputWidth: "50", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidNumeric"
                }
            ]},

        {type: "block", width: 900, list: [
                {type: "input", name: "adult_min",
                    label: "Min Adults:", labelWidth: "100",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "40", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "input", name: "adult_max",
                    label: "Max Adults:", labelWidth: "100",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "40", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "adult_max_category", label: "Max Limit/Applicable:",
                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/",
                    note: {
                        text: "Limit = Ignore SPO for everyone if Max is exceeded. Appicable = Apply SPO to Pax within Max range only"
                    }
                }
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "children_min",
                    label: "Min Children:", labelWidth: "100",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "40", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "input", name: "children_max",
                    label: "Max Children:", labelWidth: "100",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "40", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "children_max_category", label: "Max Limit/Applicable:",
                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/",
                    note: {
                        text: "Limit = Ignore SPO for everyone if Max is exceeded. Appicable = Apply SPO to Pax within Max range only"
                    }
                }
            ]},

        {type: "block", width: 900, list: [
                {type: "editor", name: "conditions_text", label: "Conditions:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "700", inputHeight: "170", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "editor", name: "added_values_text", label: "Added Values:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "700", inputHeight: "170", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                }
            ]}];


    var form_conditions = spo_condition_layout.cells("a").attachForm(str_frm_settings_condtion);
    form_conditions.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdLoadMeals")
        {
            showPopUp(form_conditions, "Meals", "meal_display", "meals_ids", _dsMealPlans, "MULTIPLE", null);
        }
    });

    var cboMinStay = form_conditions.getCombo("min_stay_priority");
    cboMinStay.addOption([{value: "NONE", text: "NONE", img_src: "images/priority.png"}]);
    cboMinStay.addOption([{value: "SPO ONLY", text: "SPO ONLY", img_src: "images/priority.png"}]);
    cboMinStay.addOption([{value: "SPO THEN CONTRACT", text: "SPO THEN CONTRACT", img_src: "images/priority.png"}]);
    cboMinStay.readonly(true);

    var cboAdMaxCategory = form_conditions.getCombo("adult_max_category");
    cboAdMaxCategory.addOption([{value: "LIMIT", text: "LIMIT", img_src: "images/supply.png"}]);
    cboAdMaxCategory.addOption([{value: "APPLICABLE", text: "APPLICABLE", img_src: "images/supply.png"}]);
    cboAdMaxCategory.readonly(true);

    var cboChMaxCategory = form_conditions.getCombo("children_max_category");
    cboChMaxCategory.addOption([{value: "LIMIT", text: "LIMIT", img_src: "images/supply.png"}]);
    cboChMaxCategory.addOption([{value: "APPLICABLE", text: "APPLICABLE", img_src: "images/supply.png"}]);
    cboChMaxCategory.readonly(true);

    form_conditions.attachEvent("onChange", function (id, value) {
        triggerConditionsChange(id, value);
    });


    var str_frm_settings_condtion_buttons = [
        {type: "settings", position: "label-left", id: "form_condition_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_conditions_buttons = spo_condition_layout.cells("b").attachForm(str_frm_settings_condtion_buttons);
    form_conditions_buttons.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("periods");
            toggleSPOTabsEnable("periods");

        } else if (name == "cmdNext")
        {
            nextStepConditions();
        }
    });

    //=====================================================================================
    //APPLICABLE TAB
    var str_frm_settings_name = [
        {type: "settings", position: "label-left", id: "form_applicable"},
        {type: "block", width: 900, list: [
                {type: "input", name: "child_supp_sharing",
                    label: "Child Sharing Parents Room:", labelWidth: "280",
                    labelHeight: "22", inputWidth: "380", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "child_supp_sharing_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadAgesSharing", tooltip: "Select Children Ages", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},

        {type: "block", width: 900, list: [
                {type: "input", name: "child_supp_own",
                    label: "Child in Own Room:", labelWidth: "280",
                    labelHeight: "22", inputWidth: "380", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    readonly: true, rows: 3
                },
                {type: "hidden", name: "child_supp_own_ids"},
                {type: "newcolumn"},
                {type: "button", name: "cmdLoadAgesOwn", tooltip: "Select Children Ages", value: "...", width: "30", height: "40", offsetLeft: 0}
            ]},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_applicable = tabSpo.cells("applicable").attachForm(str_frm_settings_name);
    form_applicable.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("conditions");
            toggleSPOTabsEnable("conditions");
        } else if (name == "cmdNext")
        {

            nextStepApplicable();

        } else if (name == "cmdLoadAgesSharing")
        {
            showPopUp(form_applicable, "Children Ages", "child_supp_sharing", "child_supp_sharing_ids", _dsChildPolicy, "MULTIPLE", childCallBack);
        } else if (name == "cmdLoadAgesOwn")
        {
            showPopUp(form_applicable, "Children Ages", "child_supp_own", "child_supp_own_ids", _dsChildPolicy, "MULTIPLE", childCallBack);
        }
    });

    //=====================================================================================
    //FLAT RATES VALIDITY PERIODS TAB
    var spo_flat_rate_condition_layout = tabSpo.cells("flat_rate_periods").attachLayout("2E");
    spo_flat_rate_condition_layout.cells("a").hideHeader("Validity Periods by Group:");
    spo_flat_rate_condition_layout.cells("b").hideHeader();
    spo_flat_rate_condition_layout.cells("b").setHeight(45);
    spo_flat_rate_condition_layout.cells("a").fixSize(true, true);
    spo_flat_rate_condition_layout.cells("b").fixSize(true, true);

    var toolbar_flatrate_validity = spo_flat_rate_condition_layout.cells("a").attachToolbar();
    toolbar_flatrate_validity.setIconsPath("images/");
    toolbar_flatrate_validity.addButton("selectall", 1, "Select All Periods", "selectall.png", "selectall.png");
    toolbar_flatrate_validity.setIconSize(32);
    toolbar_flatrate_validity.attachEvent("onClick", function (id) {
        if (id == "selectall")
        {
            grid_flat_rate_validity.checkAll(true);
        }
    });

    var grid_flat_rate_validity = spo_flat_rate_condition_layout.cells("a").attachGrid();
    grid_flat_rate_validity.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flat_rate_validity.setHeader(",Validity From,Valid To,Group No,Children Policy,");
    grid_flat_rate_validity.setColumnIds("X,dt_from,dt_to,group_no,children_ages_display,children_ages_ids");
    grid_flat_rate_validity.setColTypes("ch,ro,ro,edn,ro,ro");
    grid_flat_rate_validity.setInitWidths("50,150,150,100,200,0");
    grid_flat_rate_validity.setColAlign("center,center,center,center,left,center");
    grid_flat_rate_validity.setColSorting('int,date,date,int,str,date');
    grid_flat_rate_validity.enableAlterCss("", "");
    grid_flat_rate_validity.enableEditTabOnly(true);
    grid_flat_rate_validity.enableEditEvents(true, true, true);
    grid_flat_rate_validity.attachEvent("onCheck", onGridFlatRateValidityCheck);
    grid_flat_rate_validity.attachEvent("onRowSelect", onGridFlatRateValiditySelect);
    grid_flat_rate_validity.attachEvent("onEditCell", onGridFlatRateValidityEdit);
    grid_flat_rate_validity.attachEvent("onCellChanged", onGridFlatRateValidityChanged);
    grid_flat_rate_validity.enableStableSorting(true);
    grid_flat_rate_validity.init();
    grid_flat_rate_validity.customGroupFormat = function (name, count) {
        if (name == "")
        {
            return "";
        } else
        {
            return "Grouping No: " + name;
        }
    };

    var str_frm_settings_flat_rate_validity_buttons = [
        {type: "settings", position: "label-left", id: "form_flat_rate_validity_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_flat_rate_validity_buttons = spo_flat_rate_condition_layout.cells("b").attachForm(str_frm_settings_flat_rate_validity_buttons);
    form_flat_rate_validity_buttons.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("conditions");
            toggleSPOTabsEnable("conditions");

        } else if (name == "cmdNext")
        {
            gotoFlatRatePolicies();
        }
    });

    //=====================================================================================
    //DISCOUNT TAB: DISCOUNT, EARLYBOOKING, LONGSTAY

    var str_frm_settings_discounts = [
        {type: "settings", position: "label-left", id: "form_discounts"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "discount_basis", label: "Basis:",
                    labelWidth: "50",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "input", name: "discount_value", label: "", labelWidth: "0",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    validate: "ValidNumeric"}
            ]},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_discounts = tabSpo.cells("discounts").attachForm(str_frm_settings_discounts);
    form_discounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("conditions");
            toggleSPOTabsEnable("conditions");

        } else if (name == "cmdSave")
        {
            saveSPO_discounts();
        }
    });

    var cboRollOverBasis = form_discounts.getCombo("discount_basis");
    cboRollOverBasis.addOption([{value: "%ROOM", text: "Percentage Room", img_src: "images/discount.png"}]);
    cboRollOverBasis.addOption([{value: "%ALL", text: "Percentage All", img_src: "images/discount.png"}]);
    cboRollOverBasis.addOption([{value: "FLAT_PNI", text: "Flat PNI", img_src: "images/discount.png"}]);
    cboRollOverBasis.addOption([{value: "FLAT_PPPN", text: "Flat PPPN", img_src: "images/discount.png"}]);
    cboRollOverBasis.readonly(true);

    //=====================================================================================
    //WEDDING DISCOUNT TAB: HONEYMOON

    var str_frm_settings_wedding_discounts = [
        {type: "settings", position: "label-left", id: "form_wedding_discounts"},
        {type: "block", width: 900, list: [
                {type: "input", name: "wedding_certificate_exceed_limit_value",
                    label: "Wedding Certificate must not exceed:", labelWidth: "250",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "wedding_certificate_exceed_limit_basis",
                    label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]},
        {type: "block", width: 900, list: [
                //===============
                {type: "checkbox", name: "wedding_apply_discount_both", label: "Apply Discount for Both",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_both_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_both_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_both_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidNumeric"}
                            ]}
                    ]},

                //===============


                {type: "checkbox", name: "wedding_apply_discount_groom", label: "Apply Discount for Groom",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_groom_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_groom_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_groom_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidNumeric"}
                            ]}
                    ]},
                //===============

                {type: "checkbox", name: "wedding_apply_discount_bride", label: "Apply Discount for Bride",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_bride_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_bride_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_bride_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", validate: "ValidNumeric"}
                            ]}
                    ]}
            ]},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_wedding_discounts = tabSpo.cells("wedding_discounts").attachForm(str_frm_settings_wedding_discounts);
    form_wedding_discounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("conditions");
            toggleSPOTabsEnable("conditions");

        } else if (name == "cmdSave")
        {
            saveSPO_wedding_discounts();
        }
    });


    var cboWeddingCertBasis = form_wedding_discounts.getCombo("wedding_certificate_exceed_limit_basis");
    cboWeddingCertBasis.addOption([{value: "days", text: "Days", img_src: "images/rollover.png"}]);
    cboWeddingCertBasis.addOption([{value: "months", text: "Months", img_src: "images/rollover.png"}]);
    cboWeddingCertBasis.addOption([{value: "years", text: "Years", img_src: "images/rollover.png"}]);
    cboWeddingCertBasis.readonly(true);

    var cboWeddingDiscountBothBasis = form_wedding_discounts.getCombo("wedding_apply_discount_both_basis");
    cboWeddingDiscountBothBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingDiscountBothBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingDiscountBothBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingDiscountBothBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingDiscountBothBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN (Both as Sum)", img_src: "images/rate_32.png"}]);
    cboWeddingDiscountBothBasis.readonly(true);

    var cboWeddingDiscountGroomBasis = form_wedding_discounts.getCombo("wedding_apply_discount_groom_basis");
    cboWeddingDiscountGroomBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingDiscountGroomBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingDiscountGroomBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingDiscountGroomBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingDiscountGroomBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingDiscountGroomBasis.readonly(true);

    var cboWeddingDiscountBrideBasis = form_wedding_discounts.getCombo("wedding_apply_discount_bride_basis");
    cboWeddingDiscountBrideBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingDiscountBrideBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingDiscountBrideBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingDiscountBrideBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingDiscountBrideBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingDiscountBrideBasis.readonly(true);



    var cboWeddingDiscountBothSnglDbl = form_wedding_discounts.getCombo("wedding_apply_discount_both_sngl_dbl");
    cboWeddingDiscountBothSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingDiscountBothSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingDiscountBothSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingDiscountBothSnglDbl.readonly(true);

    var cboWeddingDiscountGroomSnglDbl = form_wedding_discounts.getCombo("wedding_apply_discount_groom_sngl_dbl");
    cboWeddingDiscountGroomSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingDiscountGroomSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingDiscountGroomSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingDiscountGroomSnglDbl.readonly(true);

    var cboWeddingDiscountBrideSnglDbl = form_wedding_discounts.getCombo("wedding_apply_discount_bride_sngl_dbl");
    cboWeddingDiscountBrideSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingDiscountBrideSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingDiscountBrideSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingDiscountBrideSnglDbl.readonly(true);


    form_wedding_discounts.attachEvent("onChange", function (id, value) {
        onFormWeddingDiscountsChanged(id, value);
    });


    //===========================================================
    //UPGRADE TAB
    var upgrade_layout = tabSpo.cells("upgrade").attachLayout("2E");
    upgrade_layout.cells("a").hideHeader();
    upgrade_layout.cells("b").hideHeader();
    upgrade_layout.cells("b").setHeight(45);
    upgrade_layout.cells("a").fixSize(true, true);
    upgrade_layout.cells("b").fixSize(true, true);

    var toolbar_upgrade = upgrade_layout.cells("a").attachToolbar();
    toolbar_upgrade.setIconsPath("images/");
    toolbar_upgrade.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_upgrade.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_upgrade.setIconSize(32);
    toolbar_upgrade.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _upgrade_id--;
            grid_upgrade.addRow(_upgrade_id, "");
            grid_upgrade.setRowTextStyle(_upgrade_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_upgrade.selectRowById(_upgrade_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_upgrade.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_upgrade.deleteRow(rid);
        }
    });


    var grid_upgrade = upgrade_layout.cells("a").attachGrid();
    grid_upgrade.setIconsPath('libraries/dhtmlx/imgs/');
    grid_upgrade.setHeader("Room From,Room To");
    grid_upgrade.setColumnIds("room_from_fk,room_to_fk");
    grid_upgrade.setColTypes("combo,combo");
    grid_upgrade.setInitWidths("300,300");
    grid_upgrade.setColAlign("left,left");
    grid_upgrade.setColSorting('str,str');
    grid_upgrade.enableAlterCss("", "");
    grid_upgrade.enableEditTabOnly(true);
    grid_upgrade.enableEditEvents(true, true, true);
    grid_upgrade.init();

    var str_frm_settings_upgrade = [
        {type: "settings", position: "label-left", id: "form_upgrade"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_upgrade = upgrade_layout.cells("b").attachForm(str_frm_settings_upgrade);
    form_upgrade.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_upgrade();
        }
    });


    //===========================================================
    //MEALS UPGRADE TAB
    var meal_upgrade_layout = tabSpo.cells("meals_upgrade").attachLayout("2E");
    meal_upgrade_layout.cells("a").hideHeader();
    meal_upgrade_layout.cells("b").hideHeader();
    meal_upgrade_layout.cells("b").setHeight(45);
    meal_upgrade_layout.cells("a").fixSize(true, true);
    meal_upgrade_layout.cells("b").fixSize(true, true);


    var toolbar_meal_upgrade = meal_upgrade_layout.cells("a").attachToolbar();
    toolbar_meal_upgrade.setIconsPath("images/");
    toolbar_meal_upgrade.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_meal_upgrade.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_meal_upgrade.setIconSize(32);
    toolbar_meal_upgrade.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _meal_upgrade_id--;
            grid_meal_upgrade.addRow(_meal_upgrade_id, "");
            grid_meal_upgrade.setRowTextStyle(_meal_upgrade_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_meal_upgrade.selectRowById(_meal_upgrade_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_meal_upgrade.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_meal_upgrade.deleteRow(rid);
        }
    });

    var grid_meal_upgrade = meal_upgrade_layout.cells("a").attachGrid();
    grid_meal_upgrade.setIconsPath('libraries/dhtmlx/imgs/');
    grid_meal_upgrade.setHeader("Meal From,Meal To");
    grid_meal_upgrade.setColumnIds("meal_from_fk,meal_to_fk");
    grid_meal_upgrade.setColTypes("combo,combo");
    grid_meal_upgrade.setInitWidths("300,300");
    grid_meal_upgrade.setColAlign("center,center");
    grid_meal_upgrade.setColSorting('str,str');
    grid_meal_upgrade.enableEditTabOnly(true);
    grid_meal_upgrade.enableEditEvents(true, true, true);
    grid_meal_upgrade.enableAlterCss("", "");
    grid_meal_upgrade.init();

    var str_frm_settings_meal_upgrade = [
        {type: "settings", position: "label-left", id: "form_meal_upgrade"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_meal_upgrade = meal_upgrade_layout.cells("b").attachForm(str_frm_settings_meal_upgrade);
    form_meal_upgrade.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_meal_upgrade();
        }
    });

    //===========================================================
    //FAMILY OFFER DISCOUNTS
    var family_discount_layout = tabSpo.cells("family_discount").attachLayout("3E");
    family_discount_layout.cells("a").hideHeader();
    family_discount_layout.cells("c").hideHeader();
    family_discount_layout.cells("a").setHeight(100);
    family_discount_layout.cells("c").setHeight(45);
    family_discount_layout.cells("b").setText("Children Age Discount");
    family_discount_layout.cells("a").fixSize(true, true);
    family_discount_layout.cells("b").fixSize(true, true);
    family_discount_layout.cells("c").fixSize(true, true);


    var str_frm_familydiscount = [
        {type: "settings", position: "label-left", id: "form_familydiscounts"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "family_offer_room_applicable", label: "Applicable on Room:",
                    labelWidth: "140",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]}];

    var form_familydiscounts = family_discount_layout.cells("a").attachForm(str_frm_familydiscount);

    var cboFamilyDiscountAppRoom = form_familydiscounts.getCombo("family_offer_room_applicable");
    cboFamilyDiscountAppRoom.addOption([{value: "both", text: "Both", img_src: "images/room_32.png"}]);
    cboFamilyDiscountAppRoom.addOption([{value: "share", text: "Share", img_src: "images/room_32.png"}]);
    cboFamilyDiscountAppRoom.addOption([{value: "own", text: "Own", img_src: "images/room_32.png"}]);
    cboFamilyDiscountAppRoom.readonly(true);



    var toolbar_family_discount_childrenage = family_discount_layout.cells("b").attachToolbar();
    toolbar_family_discount_childrenage.setIconsPath("images/");
    toolbar_family_discount_childrenage.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_family_discount_childrenage.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_family_discount_childrenage.setIconSize(32);
    toolbar_family_discount_childrenage.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _family_discounts_child_discount--;
            grid_family_discount_childrenage.addRow(_family_discounts_child_discount, "");
            grid_family_discount_childrenage.setRowTextStyle(_family_discounts_child_discount, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_family_discount_childrenage.selectRowById(_family_discounts_child_discount, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_family_discount_childrenage.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_family_discount_childrenage.deleteRow(rid);
        }
    });


    var grid_family_discount_childrenage = family_discount_layout.cells("b").attachGrid();
    grid_family_discount_childrenage.setIconsPath('libraries/dhtmlx/imgs/');
    grid_family_discount_childrenage.setHeader("Age From,Age To,Discount Basis,Discount Value");
    grid_family_discount_childrenage.setColumnIds("child_age_from,child_age_to,discount_percentage_value,discount_value");
    grid_family_discount_childrenage.setColTypes("edn,edn,combo,edn");
    grid_family_discount_childrenage.setInitWidths("100,100,200,100");
    grid_family_discount_childrenage.setColAlign("center,center,center,center");
    grid_family_discount_childrenage.setColSorting('int,int,str,int');
    grid_family_discount_childrenage.enableAlterCss("", "");
    grid_family_discount_childrenage.enableEditTabOnly(true);
    grid_family_discount_childrenage.enableEditEvents(true, true, true);
    grid_family_discount_childrenage.attachEvent("onEditCell", onGridFamilyDiscountEdit);
    grid_family_discount_childrenage.init();
    loadFamilyDiscountGridCombos();

    var str_frm_settings_familydiscount_buttons = [
        {type: "settings", position: "label-left", id: "form_familydiscount_buttons"},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_familydiscount_buttons = family_discount_layout.cells("c").attachForm(str_frm_settings_familydiscount_buttons);
    form_familydiscount_buttons.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_family_discounts();
        }
    });


    //=====================================================================================
    //WEDDING PARTY TAB

    var str_frm_settings_wedding_party_discounts = [
        {type: "settings", position: "label-left", id: "form_wedding_party_discounts"},
        {type: "block", width: 900, list: [
                {type: "input", name: "wedding_min_guests",
                    label: "Minimum Guests:", labelWidth: "120",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "input", name: "wedding_max_guests",
                    label: "Maximum Guests:", labelWidth: "120",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"}
            ]},
        {type: "block", width: 900, list: [
                //===============
                {type: "checkbox", name: "wedding_apply_discount_both", label: "Apply Discount for Both",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_both_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_both_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_both_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"}
                            ]}
                    ]},

                //===============
                {type: "checkbox", name: "wedding_apply_discount_groom", label: "Apply Discount for Groom",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_groom_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_groom_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_groom_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"}
                            ]}
                    ]},
                //===============
                {type: "checkbox", name: "wedding_apply_discount_bride", label: "Apply Discount for Bride",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_bride_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_bride_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10",
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_bride_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10"}
                            ]}
                    ]}
            ]},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_wedding_party_discounts = tabSpo.cells("wedding_party").attachForm(str_frm_settings_wedding_party_discounts);
    form_wedding_party_discounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_wedding_party();
        }
    });

    var cboWeddingPartyDiscountBothBasis = form_wedding_party_discounts.getCombo("wedding_apply_discount_both_basis");
    cboWeddingPartyDiscountBothBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBothBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBothBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBothBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBothBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN (Both as Sum)", img_src: "images/rate_32.png"}]);
    cboWeddingPartyDiscountBothBasis.readonly(true);

    var cboWeddingPartyDiscountGroomBasis = form_wedding_party_discounts.getCombo("wedding_apply_discount_groom_basis");
    cboWeddingPartyDiscountGroomBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountGroomBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountGroomBasis.addOption([{value: "FLAT_PNI", text: "Flat PNI", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountGroomBasis.addOption([{value: "FLAT_PPPN", text: "Flat PPPN", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountGroomBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingPartyDiscountGroomBasis.readonly(true);

    var cboWeddingPartyDiscountBrideBasis = form_wedding_party_discounts.getCombo("wedding_apply_discount_bride_basis");
    cboWeddingPartyDiscountBrideBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBrideBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBrideBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBrideBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingPartyDiscountBrideBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingPartyDiscountBrideBasis.readonly(true);



    var cboWeddingPartyDiscountBothSnglDbl = form_wedding_party_discounts.getCombo("wedding_apply_discount_both_sngl_dbl");
    cboWeddingPartyDiscountBothSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingPartyDiscountBothSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingPartyDiscountBothSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingPartyDiscountBothSnglDbl.readonly(true);

    var cboWeddingPartyDiscountGroomSnglDbl = form_wedding_party_discounts.getCombo("wedding_apply_discount_groom_sngl_dbl");
    cboWeddingPartyDiscountGroomSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingPartyDiscountGroomSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingPartyDiscountGroomSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingPartyDiscountGroomSnglDbl.readonly(true);

    var cboWeddingPartyDiscountBrideSnglDbl = form_wedding_party_discounts.getCombo("wedding_apply_discount_bride_sngl_dbl");
    cboWeddingPartyDiscountBrideSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingPartyDiscountBrideSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingPartyDiscountBrideSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingPartyDiscountBrideSnglDbl.readonly(true);

    form_wedding_party_discounts.attachEvent("onChange", function (id, value) {
        onFormWeddingPartyChanged(id, value);
    });

    //=====================================================================================
    //WEDDING ANNIVERSARY TAB

    var str_frm_settings_wedding_anniversary_discounts = [
        {type: "settings", position: "label-left", id: "form_wedding_anniversary_discounts"},
        {type: "block", width: 900, list: [
                {type: "input", name: "wedding_date_before_value",
                    label: "Wedding Date Before:", labelWidth: "150",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "wedding_date_before_basis",
                    label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }

            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "wedding_date_after_value",
                    label: "Wedding Date After:", labelWidth: "150",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "wedding_date_after_basis",
                    label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }

            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "wedding_anniversary_applicable_value",
                    label: "Applicable for each:", labelWidth: "150",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28",
                    labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"},
                {type: "newcolumn"},
                {type: "combo", name: "wedding_anniversary_applicable_basis",
                    label: "",
                    labelWidth: "0",
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }

            ]},
        {type: "block", width: 900, list: [
                //===============
                {type: "checkbox", name: "wedding_apply_discount_both", label: "Apply Discount for Both",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_both_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_both_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_both_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true}
                            ]}
                    ]},

                //===============
                {type: "checkbox", name: "wedding_apply_discount_groom", label: "Apply Discount for Groom",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_groom_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_groom_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_groom_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true}
                            ]}
                    ]},
                //===============
                {type: "checkbox", name: "wedding_apply_discount_bride", label: "Apply Discount for Bride",
                    list: [
                        {type: "block", width: 900, list: [

                                {type: "combo", name: "wedding_apply_discount_bride_basis",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "combo", name: "wedding_apply_discount_bride_sngl_dbl",
                                    label: "",
                                    labelWidth: "0",
                                    labelHeight: "22", inputWidth: "180", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                                    comboType: "image",
                                    comboImagePath: "../../images/"
                                },
                                {type: "newcolumn"},
                                {type: "input", name: "wedding_apply_discount_bride_value",
                                    label: "", labelWidth: "250",
                                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true}
                            ]}
                    ]}
            ]},
        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_wedding_anniversary_discounts = tabSpo.cells("wedding_anniversary").attachForm(str_frm_settings_wedding_anniversary_discounts);
    form_wedding_anniversary_discounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("conditions");
            toggleSPOTabsEnable("conditions");

        } else if (name == "cmdSave")
        {
            saveSPO_wedding_anniversary_discounts();
        }
    });


    var cboWeddingAnniversaryDiscountBothBasis = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_both_basis");
    cboWeddingAnniversaryDiscountBothBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBothBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBothBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBothBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBothBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN (Both as Sum)", img_src: "images/rate_32.png"}]);
    cboWeddingAnniversaryDiscountBothBasis.readonly(true);

    var cboWeddingAnniversaryDiscountGroomBasis = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_groom_basis");
    cboWeddingAnniversaryDiscountGroomBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountGroomBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountGroomBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountGroomBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountGroomBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingAnniversaryDiscountGroomBasis.readonly(true);

    var cboWeddingAnniversaryDiscountBrideBasis = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_bride_basis");
    cboWeddingAnniversaryDiscountBrideBasis.addOption([{value: "%ROOM", text: "Percentage Discount on Room", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBrideBasis.addOption([{value: "%ALL", text: "Percentage Discount on All", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBrideBasis.addOption([{value: "FLAT_PNI", text: "Flat Discount PNI", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBrideBasis.addOption([{value: "FLAT_PPPN", text: "Flat Discount PPPN", img_src: "images/discount.png"}]);
    cboWeddingAnniversaryDiscountBrideBasis.addOption([{value: "FLAT_RATE_PPPN", text: "Flat Rate PPPN", img_src: "images/rate_32.png"}]);
    cboWeddingAnniversaryDiscountBrideBasis.readonly(true);



    var cboWeddingAnniversaryDiscountBothSnglDbl = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_both_sngl_dbl");
    cboWeddingAnniversaryDiscountBothSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingAnniversaryDiscountBothSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingAnniversaryDiscountBothSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingAnniversaryDiscountBothSnglDbl.readonly(true);

    var cboWeddingAnniversaryDiscountGroomSnglDbl = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_groom_sngl_dbl");
    cboWeddingAnniversaryDiscountGroomSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingAnniversaryDiscountGroomSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingAnniversaryDiscountGroomSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingAnniversaryDiscountGroomSnglDbl.readonly(true);

    var cboWeddingAnniversaryDiscountBrideSnglDbl = form_wedding_anniversary_discounts.getCombo("wedding_apply_discount_bride_sngl_dbl");
    cboWeddingAnniversaryDiscountBrideSnglDbl.addOption([{value: "single", text: "On Single Specifically", img_src: "images/adult_24.png"}]);
    cboWeddingAnniversaryDiscountBrideSnglDbl.addOption([{value: "double", text: "On Double Specifically", img_src: "images/family_three_24.png"}]);
    cboWeddingAnniversaryDiscountBrideSnglDbl.addOption([{value: "system", text: "On System Decided", img_src: "images/system_24.png"}]);
    cboWeddingAnniversaryDiscountBrideSnglDbl.readonly(true);

    var cboWeddingAnniversaryDateBeforeBasis = form_wedding_anniversary_discounts.getCombo("wedding_date_before_basis");
    cboWeddingAnniversaryDateBeforeBasis.addOption([{value: "days", text: "Days", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateBeforeBasis.addOption([{value: "months", text: "Months", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateBeforeBasis.addOption([{value: "years", text: "Years", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateBeforeBasis.readonly(true);

    var cboWeddingAnniversaryDateAfterBasis = form_wedding_anniversary_discounts.getCombo("wedding_date_after_basis");
    cboWeddingAnniversaryDateAfterBasis.addOption([{value: "days", text: "Days", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateAfterBasis.addOption([{value: "months", text: "Months", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateAfterBasis.addOption([{value: "years", text: "Years", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryDateAfterBasis.readonly(true);

    var cboWeddingAnniversaryApplicableBasis = form_wedding_anniversary_discounts.getCombo("wedding_anniversary_applicable_basis");
    cboWeddingAnniversaryApplicableBasis.addOption([{value: "days", text: "Days", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryApplicableBasis.addOption([{value: "months", text: "Months", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryApplicableBasis.addOption([{value: "years", text: "Years", img_src: "images/rollover.png"}]);
    cboWeddingAnniversaryApplicableBasis.readonly(true);

    form_wedding_anniversary_discounts.attachEvent("onChange", function (id, value) {
        onFormWeddingAnniversaryChanged(id, value);
    });

    //=====================================================================================
    //SENIOR TAB

    var str_frm_settings_senior_discounts = [
        {type: "settings", position: "label-left", id: "form_senior_discounts"},
        {type: "block", width: 900, list: [
                {type: "input", name: "senior_guests_aged_from", label: "Guests Aged From",
                    labelWidth: "130",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"}
            ]},
        {type: "block", width: 900, list: [
                {type: "input", name: "senior_min_guests", label: "Minimum Guests",
                    labelWidth: "130",
                    validate: "ValidNumeric",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"}
            ]},
        {type: "block", width: 900, list: [
                {type: "combo", name: "senior_discount_basis", label: "Discount:",
                    labelWidth: "130", required: true,
                    labelHeight: "22", inputWidth: "150", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "input", name: "senior_discount_value", label: "", labelWidth: "0",
                    labelHeight: "22", inputWidth: "60", inputHeight: "28", labelLeft: "0",
                    validate: "ValidNumeric", required: true,
                    labelTop: "10", inputLeft: "10", inputTop: "10"}
            ]},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}];

    var form_senior_discounts = tabSpo.cells("senior").attachForm(str_frm_settings_senior_discounts);
    form_senior_discounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_senior_offer();
        }
    });

    var cboSeniorBasis = form_senior_discounts.getCombo("senior_discount_basis");
    cboSeniorBasis.addOption([{value: "%ROOM", text: "Percentage Room", img_src: "images/discount.png"}]);
    cboSeniorBasis.addOption([{value: "%ALL", text: "Percentage All", img_src: "images/discount.png"}]);
    cboSeniorBasis.addOption([{value: "FLAT_PNI", text: "Flat PNI", img_src: "images/discount.png"}]);
    cboSeniorBasis.addOption([{value: "FLAT_PPPN", text: "Flat PPPN", img_src: "images/discount.png"}]);
    cboSeniorBasis.readonly(true);

    //===========================================================
    //FREE NIGHTS
    var freenights_layout = tabSpo.cells("free_nights").attachLayout("4I");
    freenights_layout.cells("a").hideHeader();
    freenights_layout.cells("b").setText("Validity Periods");
    freenights_layout.cells("c").setText("Free Nights");
    freenights_layout.cells("d").hideHeader();

    freenights_layout.cells("a").setHeight(100);
    freenights_layout.cells("d").setHeight(45);

    freenights_layout.cells("a").fixSize(true, true);
    freenights_layout.cells("b").fixSize(true, true);
    freenights_layout.cells("c").fixSize(true, true);
    freenights_layout.cells("d").fixSize(true, true);


    //quick fix to hide validity period because not applicable
    freenights_layout.cells("b").hideHeader();
    freenights_layout.cells("b").collapse();



    var str_frm_settings_free_nights = [
        {type: "settings", position: "label-left", id: "form_free_nights"},
        {type: "block", width: 900, list: [
                //===============
                {type: "checkbox", name: "free_nights_cumulative",
                    label: "<b>Option:</b> Cumulative"}
            ]},
        {type: "block", width: 900, list: [
                //===============
                {type: "combo", name: "free_nights_placed_at",
                    label: "Place Free Nights at",
                    labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                }
            ]}];

    var form_free_nights = freenights_layout.cells("a").attachForm(str_frm_settings_free_nights);

    var cboFreeNightsPlaceAt = form_free_nights.getCombo("free_nights_placed_at");
    cboFreeNightsPlaceAt.addOption([{value: "end", text: "End", img_src: "images/discount.png"}]);
    cboFreeNightsPlaceAt.addOption([{value: "start", text: "Start", img_src: "images/discount.png"}]);
    cboFreeNightsPlaceAt.addOption([{value: "lowest", text: "Nights with Lowest Rates", img_src: "images/discount.png"}]);
    cboFreeNightsPlaceAt.readonly(true);


    var grid_free_nights_validity = freenights_layout.cells("b").attachGrid();
    grid_free_nights_validity.setIconsPath('libraries/dhtmlx/imgs/');
    grid_free_nights_validity.setHeader(",Season,Free Valid From,Valid To");
    grid_free_nights_validity.setColumnIds("X,season,valid_from,valid_to");
    grid_free_nights_validity.attachHeader("#master_checkbox,,,");
    grid_free_nights_validity.setColTypes("ch,ro,ro,ro");
    grid_free_nights_validity.setInitWidths("50,200,100,100");
    grid_free_nights_validity.setColAlign("center,left,center,center");
    grid_free_nights_validity.setColSorting('int,str,date,date');
    grid_free_nights_validity.enableAlterCss("", "");
    grid_free_nights_validity.setDateFormat("%d-%m-%Y", "%Y-%m-%d");
    grid_free_nights_validity.init();
    grid_free_nights_validity.attachEvent("onRowSelect", function (rid, cid) {
        var selected = grid_free_nights_validity.cells(rid, grid_free_nights_validity.getColIndexById("X")).getValue();
        if (selected == 0)
        {
            selected = 1;
        } else
        {
            selected = 0;
        }

        grid_free_nights_validity.cells(rid, grid_free_nights_validity.getColIndexById("X")).setValue(selected);
    });



    var toolbar_free_nights = freenights_layout.cells("c").attachToolbar();
    toolbar_free_nights.setIconsPath("images/");
    toolbar_free_nights.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_free_nights.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_free_nights.addSpacer("delete");
    toolbar_free_nights.addText("lbltest", 3, "Night Stays:");
    toolbar_free_nights.addInput("txtstays", 4, "0", "100");
    toolbar_free_nights.addButton("test", 5, "Test Me!", "exam_pass.png", "exam_pass.png");
    toolbar_free_nights.setIconSize(32);
    toolbar_free_nights.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _free_nights_id--;
            grid_free_nights.addRow(_free_nights_id, "");
            grid_free_nights.setRowTextStyle(_free_nights_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_free_nights.selectRowById(_free_nights_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_free_nights.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_free_nights.deleteRow(rid);
        } else if (id == "test")
        {
            testFreeNights();
        }
    });

    var grid_free_nights = freenights_layout.cells("c").attachGrid();
    grid_free_nights.setIconsPath('libraries/dhtmlx/imgs/');
    grid_free_nights.setHeader("Stay Nights,Pay Nights,Free Nights");
    grid_free_nights.setColumnIds("stay_nights,pay_nights,free_nights");
    grid_free_nights.setColTypes("edn,edn,ro");
    grid_free_nights.setInitWidths("200,200,200");
    grid_free_nights.setColAlign("center,center,center");
    grid_free_nights.setColSorting('int,int,int');
    grid_free_nights.enableAlterCss("", "");
    grid_free_nights.enableEditTabOnly(true);
    grid_free_nights.enableEditEvents(true, true, true);
    grid_free_nights.attachEvent("onEditCell", onGridFreeNightsEdit);
    grid_free_nights.init();


    var str_frm_settings_free_nights_save = [
        {type: "settings", position: "label-left", id: "form_free_nights_save"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_free_nights_save = freenights_layout.cells("d").attachForm(str_frm_settings_free_nights_save);
    form_free_nights_save.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");

        } else if (name == "cmdSave")
        {
            saveSPO_free_nights();
        }
    });


    //===========================================================
    //FLAT RATE
    var flatrate_policies_layout = tabSpo.cells("flat_rate_policies").attachLayout("2E");
    flatrate_policies_layout.cells("a").setText("Validity Periods by Groups");
    flatrate_policies_layout.cells("b").hideHeader();
    flatrate_policies_layout.cells("b").setHeight(45);
    flatrate_policies_layout.cells("a").fixSize(true, true);
    flatrate_policies_layout.cells("b").fixSize(true, true);



    var tab_flatrate_policies = flatrate_policies_layout.cells("a").attachTabbar();
    tab_flatrate_policies.addTab("supplements", "<b>Add Supplements</b>", "180px", '');
    tab_flatrate_policies.addTab("checkinout", "<b>Check In Out Policies</b>", "180px", '');
    tab_flatrate_policies.addTab("cancellation", "<b>Cancellation Policies</b>", "180px", '');
    tab_flatrate_policies.setTabActive("supplements");

    var toolbar_flatrate_supp = tab_flatrate_policies.cells("supplements").attachToolbar();
    toolbar_flatrate_supp.setIconsPath("images/");
    toolbar_flatrate_supp.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_flatrate_supp.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_flatrate_supp.setIconSize(32);
    toolbar_flatrate_supp.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _flat_rate_supplement_id--;
            grid_flatrate_supp.addRow(_flat_rate_supplement_id, ["", 0]);
            grid_flatrate_supp.setRowTextStyle(_flat_rate_supplement_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_flatrate_supp.selectRowById(_flat_rate_supplement_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_flatrate_supp.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_flatrate_supp.deleteRow(rid);
        }
    });


    var grid_flatrate_supp = tab_flatrate_policies.cells("supplements").attachGrid();
    grid_flatrate_supp.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flatrate_supp.init();


    var toolbar_flatrate_chkinout = tab_flatrate_policies.cells("checkinout").attachToolbar();
    toolbar_flatrate_chkinout.setIconsPath("images/");
    toolbar_flatrate_chkinout.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_flatrate_chkinout.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_flatrate_chkinout.setIconSize(32);
    toolbar_flatrate_chkinout.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _flat_rate_checkinout_id--;
            grid_flatrate_checkinout.addRow(_flat_rate_checkinout_id, "");
            grid_flatrate_checkinout.setRowTextStyle(_flat_rate_checkinout_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_flatrate_checkinout.selectRowById(_flat_rate_checkinout_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_flatrate_checkinout.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_flatrate_checkinout.deleteRow(rid);
        }
    });

    var grid_flatrate_checkinout = tab_flatrate_policies.cells("checkinout").attachGrid();
    grid_flatrate_checkinout.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flatrate_checkinout.setHeader("Type,Time,#cspan,Charge,#cspan,Rooms,Roomids,Date Periods");
    grid_flatrate_checkinout.setColumnIds("checkinout_type,time_before_after,time_checkinout,charge_basis,charge_value,rooms,rooms_ids,dateperiods");
    grid_flatrate_checkinout.setColTypes("combo,combo,ed,combo,edn,ro,ro,ro");
    grid_flatrate_checkinout.setInitWidths("150,80,80,80,70,400,0,200");
    grid_flatrate_checkinout.setColAlign("center,center,center,center,center,left,center,center");
    grid_flatrate_checkinout.setColSorting("str,str,int,str,int,str,str,str");
    grid_flatrate_checkinout.enableAlterCss("", "");
    grid_flatrate_checkinout.enableEditTabOnly(true);
    grid_flatrate_checkinout.enableEditEvents(true, true, true);
    grid_flatrate_checkinout.attachEvent("onRowSelect", onGridFlatRateCheckinoutSelect);
    grid_flatrate_checkinout.attachEvent("onEditCell", onGridFlatRateCheckinoutEdit);
    grid_flatrate_checkinout.init();
    load_flat_rate_checkinout_combo();


    var toolbar_flatrate_cancellation = tab_flatrate_policies.cells("cancellation").attachToolbar();
    toolbar_flatrate_cancellation.setIconsPath("images/");
    toolbar_flatrate_cancellation.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_flatrate_cancellation.addButton("delete", 2, "Delete", "delete.png", "delete.png");
    toolbar_flatrate_cancellation.setIconSize(32);
    toolbar_flatrate_cancellation.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            _flat_rate_cancellation_id--;
            grid_flatrate_cancellation.addRow(_flat_rate_cancellation_id, "");
            grid_flatrate_cancellation.setRowTextStyle(_flat_rate_cancellation_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
            grid_flatrate_cancellation.selectRowById(_flat_rate_cancellation_id, false, true, true);

        } else if (id == "delete")
        {
            var rid = grid_flatrate_cancellation.getSelectedRowId();
            if (!rid)
            {
                return;
            }
            grid_flatrate_cancellation.deleteRow(rid);
        }
    });

    var grid_flatrate_cancellation = tab_flatrate_policies.cells("cancellation").attachGrid();
    grid_flatrate_cancellation.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flatrate_cancellation.setHeader("Type,Charge,#cspan,Days Before Arrival From-To,#cspan,Dates Before Arrival From-To,#cspan,Rooms,Rooms Ids,Date Periods");
    grid_flatrate_cancellation.setColumnIds("cancellation_type,charge_basis,charge_value,days_before_arrival_from,days_before_arrival_to,date_before_arrival_from,date_before_arrival_to,rooms,rooms_ids,dateperiods");
    grid_flatrate_cancellation.setColTypes("combo,combo,edn,edn,edn,dhxCalendar,dhxCalendar,ro,ro,ro");
    grid_flatrate_cancellation.setInitWidths("150,150,60,60,60,80,80,300,0,200");
    grid_flatrate_cancellation.setColAlign("center,center,center,center,center,center,center,left,center,center");
    grid_flatrate_cancellation.setColSorting("str,str,int,int,int,date,date,str,str,str");
    grid_flatrate_cancellation.enableEditTabOnly(true);
    grid_flatrate_cancellation.enableEditEvents(true, true, true);
    grid_flatrate_cancellation.attachEvent("onRowSelect", onGridFlatRateCancellationSelect);
    grid_flatrate_cancellation.attachEvent("onEditCell", onGridFlatRateCancellationEdit);
    grid_flatrate_cancellation.attachEvent("onKeyPress", onGridFlatRateCancellationKeyPress);
    grid_flatrate_cancellation.init();
    load_flat_rate_cancellation_combo();

    var str_frm_settings_flatrate_supp = [
        {type: "settings", position: "label-left", id: "form_flatrate_supp"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];

    var form_flatrate_supp = flatrate_policies_layout.cells("b").attachForm(str_frm_settings_flatrate_supp);
    form_flatrate_supp.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {
            tabSpo.setTabActive("flat_rate_periods");
            toggleSPOTabsEnable("flat_rate_periods");

        } else if (name == "cmdNext")
        {
            gotoFlatRateCurrency();
        }
    });


    //==========================================================
    //FLAT RATE CURRENCY 

    var flat_rate_currency_layout = tabSpo.cells("flat_rate_currency").attachLayout("4I");
    flat_rate_currency_layout.cells("a").setText("Currency Cost/Buy/Sell");
    flat_rate_currency_layout.cells("b").setText("Exchange Rates");
    flat_rate_currency_layout.cells("c").setText("Currency Buy Sell Mapping");
    flat_rate_currency_layout.cells("d").hideHeader();

    flat_rate_currency_layout.cells("a").setHeight(150);
    flat_rate_currency_layout.cells("b").setWidth(400);
    flat_rate_currency_layout.cells("b").setHeight(200);
    flat_rate_currency_layout.cells("d").setHeight(50);

    flat_rate_currency_layout.cells("a").fixSize(true, true);
    flat_rate_currency_layout.cells("b").fixSize(true, true);
    flat_rate_currency_layout.cells("c").fixSize(true, true);
    flat_rate_currency_layout.cells("d").fixSize(true, true);


    var str_frm_currency = [
        {type: "settings", position: "label-left", id: "form_flat_rate_currency"},
        {type: "block", width: 900, list: [
                {type: "combo", name: "mycostprice_currencyfk", label: "Cost Price:",
                    labelWidth: "80",
                    labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
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


    var form_flat_rate_currency = flat_rate_currency_layout.cells("a").attachForm(str_frm_currency);

    form_flat_rate_currency.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdLoadCurrencySell")
        {
            showPopUp(form_flat_rate_currency, "Sell Currencies", "selected_currency_sell_display", "selected_currency_sell_ids", _dsCurrencies, "SINGLE", updateFlatRateExchangeRatesJson);
        } else if (name == "cmdLoadCurrencyBuy")
        {
            showPopUp(form_flat_rate_currency, "Buy Currencies", "selected_currency_buy_display", "selected_currency_buy_ids", _dsCurrencies, "SINGLE", updateFlatRateExchangeRatesJson);
        }
    });


    var grid_flat_rate_exchrates = flat_rate_currency_layout.cells("b").attachGrid();
    grid_flat_rate_exchrates.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flat_rate_exchrates.setHeader("Currency From,Currency To,Exchange Rate,Action,CurrencyIdFrom,CurrencyIdTo");
    grid_flat_rate_exchrates.setColumnIds("currency_from,currency_to,rates_exchange_rate,action,currency_id_from,currency_id_to");
    grid_flat_rate_exchrates.setColTypes("ro,ro,edn,ro,ro,ro");
    grid_flat_rate_exchrates.setInitWidths("100,100,100,0,0,0");
    grid_flat_rate_exchrates.setColAlign("center,center,right,center,center,center");
    grid_flat_rate_exchrates.setColSorting('str,str,int,str,int,int');
    grid_flat_rate_exchrates.enableEditTabOnly(true);
    grid_flat_rate_exchrates.enableEditEvents(true, true, true);
    grid_flat_rate_exchrates.enableStableSorting(true);
    grid_flat_rate_exchrates.init();

    var grid_flat_rate_currencymap = flat_rate_currency_layout.cells("c").attachGrid();
    grid_flat_rate_currencymap.setIconsPath('libraries/dhtmlx/imgs/');
    grid_flat_rate_currencymap.setHeader("Currency Sell,Maps From Currency Buy,Action,CurrencySellId");
    grid_flat_rate_currencymap.setColumnIds("currency_sell,currency_buy,action,currency_id_sell");
    grid_flat_rate_currencymap.setColTypes("ro,combo,ro,ro");
    grid_flat_rate_currencymap.setInitWidths("120,120,0,0");
    grid_flat_rate_currencymap.setColAlign("center,center,center,center");
    grid_flat_rate_currencymap.setColSorting('str,str,str,int');
    grid_flat_rate_currencymap.enableEditTabOnly(true);
    grid_flat_rate_currencymap.enableEditEvents(true, true, true);
    grid_flat_rate_currencymap.enableStableSorting(true);
    grid_flat_rate_currencymap.init();

    var cboCostPriceCurrency = form_flat_rate_currency.getCombo("mycostprice_currencyfk");
    cboCostPriceCurrency.enableOptionAutoPositioning(true);
    var dsFlatRateCurrency = new dhtmlXDataStore();

    dsFlatRateCurrency.load("php/api/combos/currency_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsFlatRateCurrency.dataCount(); i++)
        {
            var item = dsFlatRateCurrency.item(dsFlatRateCurrency.idByIndex(i));
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

    var str_frm_settings_flatrate_currency_toggle = [
        {type: "settings", position: "label-left", id: "form_flatrate_currency_toggle"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];


    var form_flatrate_currency_toggle = flat_rate_currency_layout.cells("d").attachForm(str_frm_settings_flatrate_currency_toggle);
    form_flatrate_currency_toggle.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {

            tabSpo.setTabActive("flat_rate_policies");
            toggleSPOTabsEnable("flat_rate_policies");

        } else if (name == "cmdNext")
        {
            gotoFlatRateCommission();
        }
    });

    //==========================================================
    //FLAT RATE COMMISSION 
    var flat_rate_commission_layout = tabSpo.cells("flat_rate_commission").attachLayout("2E");
    flat_rate_commission_layout.cells("a").hideHeader();
    flat_rate_commission_layout.cells("b").hideHeader();

    flat_rate_commission_layout.cells("a").setHeight(550);
    flat_rate_commission_layout.cells("b").setHeight(50);

    flat_rate_commission_layout.cells("a").fixSize(true, true);
    flat_rate_commission_layout.cells("b").fixSize(true, true);

    var accord_flat_rate_taxcommi = flat_rate_commission_layout.cells('a').attachAccordion();

    accord_flat_rate_taxcommi.addItem("buying", "Buying Settings");
    accord_flat_rate_taxcommi.addItem("selling", "Selling Settings");
    accord_flat_rate_taxcommi.cells("buying").open();
    accord_flat_rate_taxcommi.cells("selling").close();

    var grid_flat_rate_taxcomm_buy = accord_flat_rate_taxcommi.cells("buying").attachGrid();
    grid_flat_rate_taxcomm_buy.setIconsPath('libraries/dhtmlx/imgs/');


    var toolbar_flat_rate_taxcommi_buy = accord_flat_rate_taxcommi.cells("buying").attachToolbar();
    toolbar_flat_rate_taxcommi_buy.setIconsPath("images/");

    var opts_buy = Array(
            Array('test_settings', 'obj', 'Test Settings', 'exam_pass.png'),
            Array('additem', 'obj', 'Add Item', 'add.png'),
            Array('moveup', 'obj', 'Move Item Up', 'outbox.png'),
            Array('movedown', 'obj', 'Move Item Down', 'inbox.png'),
            Array('deleteitem', 'obj', 'Delete Item', 'delete.png'));
    toolbar_flat_rate_taxcommi_buy.addButtonSelect("opts", 1, "", opts_buy, "operate.png", "operate.png", null, true);
    toolbar_flat_rate_taxcommi_buy.setIconSize(32);

    toolbar_flat_rate_taxcommi_buy.attachEvent("onClick", function (id) {

        if (id == "additem")
        {

            showFlatRateTaxCommItems("BUYING");

        } else if (id == "deleteitem")
        {
            deleteFlatRateTaxCommiItem(grid_flat_rate_taxcomm_buy, "BUYING");
        } else if (id == "moveup")
        {
            moveFlatRateTaxCommiItem(grid_flat_rate_taxcomm_buy, "buying_settings", "UP");
        } else if (id == "movedown")
        {
            moveFlatRateTaxCommiItem(grid_flat_rate_taxcomm_buy, "buying_settings", "DOWN");
        } else if (id == "test_settings")
        {
            testTaxCommiSettings();
        }
    });


    var grid_flat_rate_taxcomm_sell = accord_flat_rate_taxcommi.cells("selling").attachGrid();
    grid_flat_rate_taxcomm_sell.setIconsPath('libraries/dhtmlx/imgs/');


    var toolbar_flat_rate_taxcommi_sell = accord_flat_rate_taxcommi.cells("selling").attachToolbar();
    toolbar_flat_rate_taxcommi_sell.setIconsPath("images/");
    var opts_sell = Array(
            Array('test_settings', 'obj', 'Test Settings', 'exam_pass.png'),
            Array('additem', 'obj', 'Add Item', 'add.png'),
            Array('moveup', 'obj', 'Move Item Up', 'outbox.png'),
            Array('movedown', 'obj', 'Move Item Down', 'inbox.png'),
            Array('deleteitem', 'obj', 'Delete Item', 'delete.png'));
    toolbar_flat_rate_taxcommi_sell.addButtonSelect("opts", 1, "", opts_sell, "operate.png", "operate.png", null, true);
    toolbar_flat_rate_taxcommi_sell.setIconSize(32);


    toolbar_flat_rate_taxcommi_sell.attachEvent("onClick", function (id) {
        if (id == "additem")
        {
            showFlatRateTaxCommItems("SELLING");

        } else if (id == "deleteitem")
        {
            deleteFlatRateTaxCommiItem(grid_flat_rate_taxcomm_sell, "SELLING");
        } else if (id == "moveup")
        {
            moveFlatRateTaxCommiItem(grid_flat_rate_taxcomm_sell, "selling_settings", "UP");
        } else if (id == "movedown")
        {
            moveFlatRateTaxCommiItem(grid_flat_rate_taxcomm_sell, "selling_settings", "DOWN");
        } else if (id == "test_settings")
        {
            testTaxCommiSettings();
        }
    });

    var popupwin_flat_rate_testtaxcomm = dhxWins.createWindow("popupwin_flat_rate_testtaxcomm", 50, 50, 700, 500);
    popupwin_flat_rate_testtaxcomm.setText("Test Tax Commission:");
    popupwin_flat_rate_testtaxcomm.denyResize();
    popupwin_flat_rate_testtaxcomm.denyPark();
    popupwin_flat_rate_testtaxcomm.button("close").hide();


    var test_taxcomm_layout = popupwin_flat_rate_testtaxcomm.attachLayout("1C");
    test_taxcomm_layout.cells("a").hideHeader();

    var grid_flat_rate_test_taxcomm = test_taxcomm_layout.cells("a").attachGrid();
    grid_flat_rate_test_taxcomm.setIconsPath('libraries/dhtmlx/imgs/');

    var toolbar_test_taxcomm = test_taxcomm_layout.cells("a").attachToolbar();
    toolbar_test_taxcomm.setIconsPath("images/");
    toolbar_test_taxcomm.addButton("exit", 1, "Exit", "exit.png", "exit.png");
    toolbar_test_taxcomm.setIconSize(32);
    toolbar_test_taxcomm.setAlign('right');

    toolbar_test_taxcomm.attachEvent("onClick", function (id) {
        if (id == "exit")
        {
            popupwin_flat_rate_testtaxcomm.setModal(false);
            popupwin_flat_rate_testtaxcomm.hide();
            popupwin_spo.setModal(true);
        }
    });

    var str_frm_settings_flatrate_commission = [
        {type: "settings", position: "label-left", id: "form_flatrate_commission"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdNext", tooltip: "Go to Next Step", value: "Next Step >>", width: "230", height: "60", offsetLeft: 0}

            ]}
    ];


    var form_flatrate_commission = flat_rate_commission_layout.cells("b").attachForm(str_frm_settings_flatrate_commission);
    form_flatrate_commission.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {

            tabSpo.setTabActive("flat_rate_currency");
            toggleSPOTabsEnable("flat_rate_currency");

        } else if (name == "cmdNext")
        {
            gotoFlatRateRates();
        }
    });

    //===========================================================
    //FLAT RATES RATES

    var tree_roomdates = null;



    var flat_rate_rates_layout = tabSpo.cells("flat_rate_rates").attachLayout("2E");
    flat_rate_rates_layout.cells("a").hideHeader();
    flat_rate_rates_layout.cells("b").hideHeader();

    flat_rate_rates_layout.cells("a").setHeight(550);
    flat_rate_rates_layout.cells("b").setHeight(50);

    flat_rate_rates_layout.cells("a").fixSize(true, true);
    flat_rate_rates_layout.cells("b").fixSize(true, true);

    var flat_rate_roomslayout = flat_rate_rates_layout.cells("a").attachLayout("3W");
    flat_rate_roomslayout.cells("a").hideHeader();
    flat_rate_roomslayout.cells("b").hideHeader();
    flat_rate_roomslayout.cells("c").hideHeader();


    flat_rate_roomslayout.cells("c").setWidth(800);
    flat_rate_roomslayout.cells("a").setWidth(300);
    flat_rate_roomslayout.cells("b").setWidth(135);


    flat_rate_roomslayout.cells("a").fixSize(true, false);
    flat_rate_roomslayout.cells("b").fixSize(true, false);
    flat_rate_roomslayout.cells("c").fixSize(true, false);

    var toolbar_capacity_dates = flat_rate_roomslayout.cells("a").attachToolbar();
    toolbar_capacity_dates.setIconsPath("images/");
    toolbar_capacity_dates.setIconSize(32);
    var opts = Array(
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

    toolbar_capacity_dates.hideListOption("opts", "combi");
    toolbar_capacity_dates.showListOption("opts", "select_all_rooms");
    toolbar_capacity_dates.showListOption("opts", "unselect_all_rooms");

    toolbar_capacity_dates.attachEvent("onClick", function (id) {

        if (id == "combi")
        {
            var nodeid = tree_roomdates.getSelectedItemId();

            if (nodeid)
            {
                showRoomDateCombinations(nodeid);
            }

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


    var grid_room_choices = flat_rate_roomslayout.cells("b").attachGrid();
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

    grid_room_choices.addRow("capacity", ["<img src=\"images/pax.png\" width=\"30px\" height=\"30px\">", "CAPACITY"]);
    grid_room_choices.addRow("adult_policies", ["<img src=\"images/adult_72.png\" width=\"30px\" height=\"30px\">", "ADULT"]);
    grid_room_choices.addRow("child_policies", ["<img src=\"images/child_72.png\" width=\"30px\" height=\"30px\">", "CHILD"]);
    grid_room_choices.addRow("single_parent", ["<img src=\"images/interview.png\" width=\"30px\" height=\"30px\">", "SINGLE PARENT"]);


    var tabRoomViews = flat_rate_roomslayout.cells("c").attachTabbar();
    tabRoomViews.addTab("capacity", "<b>Capacity</b>", "280px", '');
    tabRoomViews.addTab("adult_policies", "<b>Adult Policies</b>", "280px", '');
    tabRoomViews.addTab("child_policies", "<b>Children Policies</b>", "280px", '');
    tabRoomViews.addTab("single_parent", "<b>Single Parent Policies</b>", "280px", '');


    var str_frm_settings_flatrate_rates = [
        {type: "settings", position: "label-left", id: "form_flatrate_commission"},

        {type: "block", width: 900, list: [
                {type: "button", name: "cmdClose", tooltip: "Return to Special Offers", value: "Return to Special Offers", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdBack", tooltip: "Go to Previous Step", value: "<< Previous Step", width: "230", height: "60", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdSave", tooltip: "Save Special Offer", value: "Save Offer", width: "230", height: "60", offsetLeft: 0}
            ]}
    ];


    var form_flatrate_rates = flat_rate_rates_layout.cells("b").attachForm(str_frm_settings_flatrate_rates);
    form_flatrate_rates.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdClose")
        {
            popupwin_spo.hide();
            popupwin_spo.setModal(false);
        } else if (name == "cmdBack")
        {

            tabSpo.setTabActive("flat_rate_commission");
            toggleSPOTabsEnable("flat_rate_commission");

        } else if (name == "cmdSave")
        {
            saveSPO_flatrates();
        }
    });


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
    var grid_childpolicy_own_age = null;

    initialiseChildPolicySharingGrid();
    initialiseChildPolicySingleGrid();


    //=====================================================================

    //SINGLE PARENT POLICY
    var singleparent_layout = tabRoomViews.cells("single_parent").attachLayout("1C");
    singleparent_layout.cells('a').hideHeader();

    var grid_singleparentpolicy_age = null;

    initialiseSingleParentPolicyGrid();



    //===========================================================
    jQuery(function ($) {
        $("[name='booking_before_date_from']").mask("99-99-9999");
        $("[name='booking_before_date_to']").mask("99-99-9999");
    });


    //=====================================================================================

    applyrights();
    loadHotelSPOs("");


    function loadSpoCombos()
    {
        cboSpoType.addOption([{value: "contractual", text: "Contractual", img_src: "images/solution.png"}]);
        cboSpoType.addOption([{value: "tactical", text: "Tactical", img_src: "images/solution.png"}]);

        var dsTemplates = new dhtmlXDataStore();
        dsTemplates.load("php/api/hotelspecialoffers/templategrid.php?t=" + encodeURIComponent(global_token), "json", function () {
            for (var i = 0; i < dsTemplates.dataCount(); i++)
            {
                var item = dsTemplates.item(dsTemplates.idByIndex(i));
                var value = item.template_code;
                var text = item.description;
                var arr_tabs = item.tabs.split(",");
                arr_templates.push({value: value, text: text, tabs: arr_tabs});
            }

            for (var i = 0; i < arr_templates.length; i++)
            {
                var value = arr_templates[i].value;
                var txt = arr_templates[i].text;
                cboTemplate.addOption([{value: value, text: txt, img_src: "images/solution.png"}]);
            }
        });
    }

    function toggleSPOTabsVisible(template)
    {
        var alltabids = tabSpo.getAllTabs();

        for (var i = 0; i < arr_templates.length; i++)
        {
            var tabs = arr_templates[i].tabs;
            var txt = arr_templates[i].value;
            if (template == txt)
            {
                for (var j = 0; j < tabs.length; j++)
                {
                    var tabid = tabs[j];
                    tabSpo.showTab(tabid);
                    alltabids.splice(alltabids.indexOf(tabid), 1);
                }
            }
        }

        //hide the rest of tabs that are not needed
        for (var i = 0; i < alltabids.length; i++)
        {
            var tabid = alltabids[i];
            tabSpo.hideTab(tabid);
        }
    }


    function toggleSPOTabsEnable(tabid)
    {
        var alltabids = tabSpo.getAllTabs();

        //hide the rest of tabs that are not needed
        for (var i = 0; i < alltabids.length; i++)
        {
            if (tabid == alltabids[i])
            {
                tabSpo.enableTab(tabid);
            } else
            {
                tabSpo.disableTab(alltabids[i]);
            }
        }
    }

    function loadPopupDs()
    {
        _dsMealPlans.load("php/api/hotelspecialoffers/loadmealplans.php?t=" + global_token, "json", function () {});
        _dsDatePeriods.load("php/api/hotelspecialoffers/loadseasons.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {
            loadPeriodGridSeasonCombo();
        });
        _dsRooms.load("php/api/hotelspecialoffers/hotelroomsgrid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});
        _dsChildPolicy.load("php/api/hotelspecialoffers/childrenagegrid.php?t=" + global_token, "json", function () {});
        _dsRatesCombo.load("php/api/hotelspecialoffers/rates_combo.php?t=" + global_token, "json", function () {
            for (var i = 0; i < _dsRatesCombo.dataCount(); i++)
            {
                var item = _dsRatesCombo.item(_dsRatesCombo.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                cboRates.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
                cboRates.setComboValue(value);
            }
            cboRates.setComboValue("B");
            cboRates.readonly(true);
        });

        _dsCurrencies.load("php/api/hotelspecialoffers/currencygrid.php?t=" + global_token + "&hid=" + global_hotel_id, "json", function () {});
        _dsTaxCommiItems.load("php/api/hotelspecialoffers/taxcommi_items.php?t=" + global_token, "json", function () {});
    }
    //==============================================================================

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
        popupwin_spo.setModal(false);
        popupwin.setModal(true);
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
        var countries_ids = form_name.getItemValue("market_countries_ids");
        _dsTOs.load("php/api/hotelspecialoffers/touroperatorgrid.php?t=" + encodeURIComponent(global_token) + "&countries_ids=" + encodeURIComponent(countries_ids), "json", function () {
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
        popupwin_spo.setModal(false);
        popupwin.setModal(true);
    }


    function showPopUpCountries(form, caller, inputdisplay, inputid)
    {
        var dim = popupwin_spo.getDimension();
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

                //clear tour operators
                form.setItemValue("tour_operators_display", "");
                form.setItemValue("tour_operators_ids", "");

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
        pop_grid.loadXML("php/api/hotelspecialoffers/marketgridxml.php?t=" + encodeURIComponent(global_token), function () {
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
        popupwin_spo.setModal(false);
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


    function nextStepName()
    {

        //validate first
        if (!form_name.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });


            return;
        }

        var template = form_name.getItemValue("template");
        var template_name = cboTemplate.getComboText();

        popupwin_spo.setText("Special Offer Details: <b>" + template_name + "</b>");


        toggleSPOTabsVisible(template);

        tabSpo.setTabActive("periods");
        toggleSPOTabsEnable("periods");
    }

    function nextStepPeriods()
    {
        //validate first
        if (!form_periods.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }

        //====================================
        var dtfrom = form_periods.getItemValue("booking_before_date_from", true);
        var dtto = form_periods.getItemValue("booking_before_date_to", true);


        if (utils_isDate(dtfrom) && utils_isDate(dtto))
        {
            if (!utils_validateDateOrder(dtfrom, dtto))
            {
                dhtmlx.alert({
                    text: "Invalid Booking Date Period Order!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_periods.setItemFocus("booking_before_date_to");
                    }
                });

                return;
            }
        }
        //====================================

        var daysfrom = utils_trim(form_periods.getItemValue("booking_before_days_from"), " ");
        var daysto = utils_trim(form_periods.getItemValue("booking_before_days_to"), " ");


        if (isNaN(daysfrom))
        {
            dhtmlx.alert({
                text: "Please enter numeric value for Days From!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_periods.setItemFocus("booking_before_days_from");
                }
            });

            return;
        }

        if (isNaN(daysto))
        {
            dhtmlx.alert({
                text: "Please enter numeric value for Days To!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_periods.setItemFocus("booking_before_days_to");
                }
            });

            return;
        }

        if (daysfrom != "" && daysto != "")
        {
            daysfrom = parseInt(daysfrom, 10);
            daysto = parseInt(daysto, 10);

            if (daysfrom > daysto)
            {
                dhtmlx.alert({
                    text: "Invalid Booking Days Period Order!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_periods.setItemFocus("booking_before_days_to");
                    }
                });

                return;
            }
        }


        //check if validity periods selected
        if (grid_period.getRowsNum() == 0)
        {
            dhtmlx.alert({
                text: "Please specify at least one Validity Period!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });

            return;
        }

        //check if there are rows without seasons or dates
        for (var i = 0; i < grid_period.getRowsNum(); i++) {

            var rwid = grid_period.getRowId(i);

            var date_dtfrom = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue(), " ");
            var date_dtto = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue(), " ");
            var season = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("season")).getValue(), " ");

            if (date_dtfrom == "" ||
                    date_dtto == "")
            {
                dhtmlx.alert({
                    text: "Dates cannot be blank!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_period.selectRowById(rwid, false, true, false);
                    }
                });
                return;
            }
        }

        tabSpo.setTabActive("conditions");
        toggleSPOTabsEnable("conditions");

    }


    function loadPeriods()
    {
        grid_loadperiods_seasons.clearAll();

        for (var i = 0; i < _dsDatePeriods.dataCount(); i++)
        {
            var item = _dsDatePeriods.item(_dsDatePeriods.idByIndex(i));
            var id = item.id;
            var checkin = item.checkin_dmy;
            var checkout = item.checkout_dmy;
            var season = item.season;
            var scode = item.scode;
            var caption = "<b>" + scode + "</b> : " + season;
            grid_loadperiods_seasons.addRow(id, [0, caption, checkin, checkout]);
        }

        popupwin_spo.setModal(false);
        popupwin_loadperiods.setModal(true);
        popupwin_loadperiods.center();
        popupwin_loadperiods.show();
    }


    function applyLoadPeriods()
    {
        var periodids = grid_loadperiods_seasons.getCheckedRows(grid_loadperiods_seasons.getColIndexById("X"));

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

        //======================================================

        var overlapcount = 0;

        var arr_period_ids = periodids.split(",");


        for (var j = 0; j < arr_period_ids.length; j++)
        {
            var periodid = arr_period_ids[j];

            if (periodid != "")
            {
                var item = _dsDatePeriods.item(periodid);

                var checkin_dmy = item.checkin_dmy;
                var checkout_dmy = item.checkout_dmy;

                var checkin = item.checkin;
                var checkout = item.checkout;

                var seasonid = item.seasonfk;

                var outcome = validateSpecificPeriodDate(checkin_dmy, checkout_dmy, "-1");

                if (outcome == true)
                {
                    _period_id--;
                    grid_period.addRow(_period_id, [seasonid, checkin, checkout]);
                    grid_period.setRowTextStyle(_period_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                } else if (outcome == "OVL")
                {
                    overlapcount++;
                }
            }
        }

        grid_period.groupBy(grid_period.getColIndexById("season"));
        grid_period.sortRows(grid_period.getColIndexById("valid_from"), "date", "asc");


        popupwin_loadperiods.setModal(false);
        popupwin_spo.setModal(true);
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

        return;
    }

    function validateSpecificPeriodDate(capacity_from, capacity_to, id_skip)
    {

        //capacity.from <= capacity.to
        //no overlapping with existing capacity.from and capacity.to

        if (utils_isDate(capacity_from) && utils_isDate(capacity_to))
        {
            if (!utils_validateDateOrder(capacity_from, capacity_to))
            {

                return "IDO";
            }
        }

        for (var i = 0; i < grid_period.getRowsNum(); i++) {

            var rwid = grid_period.getRowId(i);

            var date_dtfrom = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue(), "DD-MM-YYYY");
            var date_dtto = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue(), "DD-MM-YYYY");


            var chk1 = utils_validateDateOrder(capacity_from, date_dtto);
            var chk2 = utils_validateDateOrder(date_dtfrom, capacity_to);

            if (chk1 && chk2 && id_skip != rwid)
            {
                return "OVL";
            }
        }

        return true;
    }

    function onGridSpoLinkCheck(rId, cInd, state)
    {
        onGridSpoLinkEdit(2, rId, cInd, +state, "XXX");
    }
    function onGridSpoLinkEdit(stage, rid, cInd, nValue, oValue)
    {
        var colid = grid_spo_link.getColumnId(cInd);
        var spoid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("spoid")).getValue();
        var linkid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("linkid")).getValue();
        var linklineid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("linklineid")).getValue();

        if (stage == 2)
        {
            if (nValue != oValue)
            {
                var params = "t=" + encodeURIComponent(global_token) +
                        "&nValue=" + encodeURIComponent(nValue) +
                        "&colid=" + colid +
                        "&spoid=" + spoid +
                        "&linkid=" + linkid +
                        "&linklineid=" + linklineid;

                spolayout_link.cells("a").progressOn();

                dhtmlxAjax.post("php/api/hotelspecialoffers/updatelink.php", params, function (loader) {
                    spolayout_link.cells("a").progressOff();

                    if (loader)
                    {
                        if (loader.xmlDoc.responseURL == "")
                        {
                            dhtmlx.alert({
                                text: "Connection Lost!",
                                type: "alert-warning",
                                title: "UPDATE",
                                callback: function () {
                                    grid_spo_link.doUndo();
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
                                    grid_spo_link.doUndo();
                                }
                            });
                            return false;
                        }

                        if (json_obj.OUTCOME == "OK")
                        {


                        } else
                        {
                            dhtmlx.alert({
                                text: json_obj.OUTCOME,
                                type: "alert-warning",
                                title: "UPDATE",
                                callback: function () {
                                    grid_spo_link.doUndo();
                                }
                            });
                        }
                    }
                });
            }
        }

        return true;
    }

    function onGridPeriodEdit(stage, rid, cInd, nValue, oValue)
    {

        if (stage == 2 && nValue != oValue)
        {
            var dtfrom = utils_formatDate(grid_period.cells(rid, grid_period.getColIndexById("valid_from")).getValue(), "DD-MM-YYYY");
            var dtto = utils_formatDate(grid_period.cells(rid, grid_period.getColIndexById("valid_to")).getValue(), "DD-MM-YYYY");

            if (dtfrom != "" && dtto != "")
            {
                var outcome = validateSpecificPeriodDate(dtfrom, dtto, rid);
                if (outcome == "OVL")
                {
                    dhtmlx.alert({
                        text: "Overlapping Detected!",
                        type: "alert-warning",
                        title: "Load Periods",
                        callback: function () {
                            grid_period.doUndo();
                        }
                    });
                    return false;
                } else if (outcome == "IDO")
                {
                    dhtmlx.alert({
                        text: "Invalid Date Order!",
                        type: "alert-warning",
                        title: "Load Periods",
                        callback: function () {
                            grid_period.doUndo();
                        }
                    });
                    return false;
                }
            }

            grid_period.groupBy(grid_period.getColIndexById("season"));
            grid_period.sortRows(grid_period.getColIndexById("valid_from"), "date", "asc");
            grid_period.selectRowById(rid, false, true, false);

        }


        return true;
    }


    function nextStepConditions()
    {
        if (!form_conditions.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });

            return;
        }

        //validate min stay

        var min_stay_from = utils_trim(form_conditions.getItemValue("min_stay_from"), " ");
        var min_stay_to = utils_trim(form_conditions.getItemValue("min_stay_to"), " ");
        var min_stay_priority = form_conditions.getItemValue("min_stay_priority");

        if (min_stay_priority != "NONE" && min_stay_from == "" && min_stay_to == "")
        {
            dhtmlx.alert({
                text: "Please enter at least a value for Minimum Stay if Priority is not NONE",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("min_stay_from");
                }
            });

            return;
        }

        if (min_stay_from != "" && isNaN(min_stay_from))
        {
            dhtmlx.alert({
                text: "Invalid Minimum Stay From numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("min_stay_from");
                }
            });

            return;
        }

        if (min_stay_to != "" && isNaN(min_stay_to))
        {
            dhtmlx.alert({
                text: "Invalid Minimum Stay To numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("min_stay_to");
                }
            });

            return;
        }

        if (min_stay_from != "" && min_stay_to != "")
        {
            min_stay_from = parseInt(min_stay_from, 10);
            min_stay_to = parseInt(min_stay_to, 10);

            if (min_stay_to < min_stay_from)
            {
                dhtmlx.alert({
                    text: "Invalid Minimum Stay ordering!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_conditions.setItemFocus("min_stay_to");
                    }
                });
                return;
            }
        }

        var adult_min = utils_trim(form_conditions.getItemValue("adult_min"), " ");
        var adult_max = utils_trim(form_conditions.getItemValue("adult_max"), " ");


        if (adult_min != "" && isNaN(adult_min))
        {
            dhtmlx.alert({
                text: "Invalid Adult Minimum numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("adult_min");
                }
            });

            return;
        }

        if (adult_max != "" && isNaN(adult_max))
        {
            dhtmlx.alert({
                text: "Invalid Adult Maximum numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("adult_max");
                }
            });

            return;
        }

        if (adult_min != "" && adult_max != "")
        {
            adult_min = parseInt(adult_min, 10);
            adult_max = parseInt(adult_max, 10);

            if (adult_max < adult_min)
            {
                dhtmlx.alert({
                    text: "Invalid Adult Ordering!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_conditions.setItemFocus("adult_min");
                    }
                });
                return;
            }
        }

        var children_min = utils_trim(form_conditions.getItemValue("children_min"), " ");
        var children_max = utils_trim(form_conditions.getItemValue("children_max"), " ");

        if (children_min != "" && isNaN(children_max))
        {
            dhtmlx.alert({
                text: "Invalid Children Minimum numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("children_min");
                }
            });

            return;
        }

        if (children_max != "" && isNaN(children_max))
        {
            dhtmlx.alert({
                text: "Invalid Children Maximum numeric value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_conditions.setItemFocus("children_max");
                }
            });

            return;
        }

        if (children_min != "" && children_max != "")
        {
            children_min = parseInt(children_min, 10);
            children_max = parseInt(children_max, 10);

            if (children_max < children_min)
            {
                dhtmlx.alert({
                    text: "Invalid Children Ordering!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_conditions.setItemFocus("children_min");
                    }
                });
                return;
            }
        }


        //============================

        var template = form_name.getItemValue("template");

        if (template == "flat_rate")
        {
            tabSpo.setTabActive("flat_rate_periods");
            toggleSPOTabsEnable("flat_rate_periods");
            populate_flat_rate_validity_periods();

        } else if (template == "discount" || template == "early_booking" ||
                template == "long_stay" || template == "honeymoon" ||
                template == "free_nights" || template == "free_upgrade" ||
                template == "wedding_anniversary" || template == "family_offer" ||
                template == "wedding_party" || template == "senior_offer" ||
                template == "meals_upgrade")
        {

            tabSpo.setTabActive("applicable");
            toggleSPOTabsEnable("applicable");
        }

    }
    function saveSPO_discounts()
    {
        if (!form_discounts.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields with correct values!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });

            return;
        }

        var discount_value = form_discounts.getItemValue("discount_value");
        if (isNaN(discount_value))
        {
            dhtmlx.alert({
                text: "Please enter numeric value for Discount Value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_discounts.setItemFocus("discount_value");
                }
            });

            return;
        }


        saveSPO();
    }


    function saveSPO_wedding_anniversary_discounts()
    {
        //  

        //=========================================================================
        var wed_date_before_value = form_wedding_anniversary_discounts.getItemValue("wedding_date_before_value");
        var wed_date_before_basis = form_wedding_anniversary_discounts.getItemValue("wedding_date_before_basis");

        if (wed_date_before_value != "")
        {
            if (isNaN(wed_date_before_value))
            {
                dhtmlx.alert({
                    text: "Please enter numeric value for Wedding Date Before!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_date_before_value");
                    }
                });

                return;
            }

            if (wed_date_before_basis == "")
            {
                dhtmlx.alert({
                    text: "Please select a Basis for Wedding Date Before!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_date_before_basis");
                    }
                });

                return;
            }
        }


        //=========================================================================
        var wed_date_after_value = form_wedding_anniversary_discounts.getItemValue("wedding_date_after_value");
        var wed_date_after_basis = form_wedding_anniversary_discounts.getItemValue("wedding_date_after_basis");

        if (wed_date_after_value != "")
        {
            if (isNaN(wed_date_after_value))
            {
                dhtmlx.alert({
                    text: "Please enter numeric value for Wedding Date Before!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_date_after_value");
                    }
                });

                return;
            }

            if (wed_date_after_basis == "")
            {
                dhtmlx.alert({
                    text: "Please select a Basis for Wedding After Before!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_date_after_basis");
                    }
                });

                return;
            }
        }

        //=========================================================================
        var wed_date_app_value = form_wedding_anniversary_discounts.getItemValue("wedding_anniversary_applicable_value");
        var wed_date_app_basis = form_wedding_anniversary_discounts.getItemValue("wedding_anniversary_applicable_basis");

        if (wed_date_app_value != "")
        {
            if (isNaN(wed_date_app_value))
            {
                dhtmlx.alert({
                    text: "Please enter numeric value for Wedding Applicable!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_anniversary_applicable_value");
                    }
                });

                return;
            }

            if (wed_date_app_basis == "")
            {
                dhtmlx.alert({
                    text: "Please select a Basis for Wedding Applicable Basis!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_anniversary_discounts.setItemFocus("wedding_anniversary_applicable_basis");
                    }
                });

                return;
            }
        }



        //==================================================
        var both = form_wedding_anniversary_discounts.isItemChecked("wedding_apply_discount_both");
        var groom = form_wedding_anniversary_discounts.isItemChecked("wedding_apply_discount_groom");
        var bride = form_wedding_anniversary_discounts.isItemChecked("wedding_apply_discount_bride");

        if (!both && !groom && !bride)
        {
            dhtmlx.alert({
                text: "Please specify Discount Mode",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }


        if (both && (groom || bride))
        {
            dhtmlx.alert({
                text: "Cannot apply Groom or Bride individual discounts if '<b>Apply Discount for Both</b>' is already selected!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }

        if (both)
        {
            if (!validate_wedding_discount_basis(form_wedding_anniversary_discounts,
                    "wedding_apply_discount_both_basis",
                    "wedding_apply_discount_both_sngl_dbl",
                    "wedding_apply_discount_both_value"))
            {
                return;
            }
        }

        if (groom)
        {
            if (!validate_wedding_discount_basis(form_wedding_anniversary_discounts,
                    "wedding_apply_discount_groom_basis",
                    "wedding_apply_discount_groom_sngl_dbl",
                    "wedding_apply_discount_groom_value"))
            {
                return;
            }
        }

        if (bride)
        {
            if (!validate_wedding_discount_basis(form_wedding_anniversary_discounts,
                    "wedding_apply_discount_bride_basis",
                    "wedding_apply_discount_bride_sngl_dbl",
                    "wedding_apply_discount_bride_value"))
            {
                return;
            }
        }






        saveSPO();

    }

    function saveSPO_wedding_discounts()
    {
        if (!form_wedding_discounts.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields with correct values!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });

            return;
        }

        var limit = form_wedding_discounts.getItemValue("wedding_certificate_exceed_limit_value");
        if (isNaN(limit))
        {
            dhtmlx.alert({
                text: "Please enter numeric value for Wedding Certificate Limit!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_wedding_discounts.setItemFocus("wedding_certificate_exceed_limit_value");
                }
            });

            return;
        }



        var both = form_wedding_discounts.isItemChecked("wedding_apply_discount_both");
        var groom = form_wedding_discounts.isItemChecked("wedding_apply_discount_groom");
        var bride = form_wedding_discounts.isItemChecked("wedding_apply_discount_bride");

        if (!both && !groom && !bride)
        {
            dhtmlx.alert({
                text: "Please specify Discount Mode",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }


        if (both && (groom || bride))
        {
            dhtmlx.alert({
                text: "Cannot apply Groom or Bride individual discounts if '<b>Apply Discount for Both</b>' is already selected!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }

        if (both)
        {
            if (!validate_wedding_discount_basis(form_wedding_discounts,
                    "wedding_apply_discount_both_basis",
                    "wedding_apply_discount_both_sngl_dbl",
                    "wedding_apply_discount_both_value"))
            {
                return;
            }
        }

        if (groom)
        {
            if (!validate_wedding_discount_basis(form_wedding_discounts,
                    "wedding_apply_discount_groom_basis",
                    "wedding_apply_discount_groom_sngl_dbl",
                    "wedding_apply_discount_groom_value"))
            {
                return;
            }
        }

        if (bride)
        {
            if (!validate_wedding_discount_basis(form_wedding_discounts,
                    "wedding_apply_discount_bride_basis",
                    "wedding_apply_discount_bride_sngl_dbl",
                    "wedding_apply_discount_bride_value"))
            {
                return;
            }
        }


        saveSPO();

    }

    function validate_wedding_discount_basis(form, field1, field2, field3)
    {
        var f1 = form.getItemValue(field1);
        var f2 = form.getItemValue(field2);
        var f3 = form.getItemValue(field3);

        if (f1 == "" || !f1)
        {
            dhtmlx.alert({
                text: "Missing Discount Basis!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form.setItemFocus(field1);
                }
            });
            return false;
        }

        if (f2 == "" || !f2)
        {
            dhtmlx.alert({
                text: "Missing Discount Single or Double Mode!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form.setItemFocus(field2);
                }
            });
            return false;
        }

        if (f3 == "" || isNaN(f3))
        {
            dhtmlx.alert({
                text: "Invalid Numeric Discount Value!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form.setItemFocus(field3);
                }
            });
            return false;
        }

        return true;
    }


    function loadFlatRateGroupValidity(arr)
    {
        grid_flat_rate_validity.clearAll();

        for (var i = 0; i < arr.length; i++) {

            var dtfrom = utils_formatDate(arr[i].dt_from, "DD-MM-YYYY");
            var dtto = utils_formatDate(arr[i].dt_to, "DD-MM-YYYY");
            var groupno = arr[i].groupno;
            var child_age_ids = arr[i].child_age_ids;
            var child_age_display = arr[i].child_age_display;
            var rid = arr[i].id;

            grid_flat_rate_validity.addRow(rid, "");
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("X")).setValue(1);
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("dt_from")).setValue(dtfrom);
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("dt_to")).setValue(dtto);
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(child_age_display);
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(child_age_ids);
            grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("group_no")).setValue(groupno);

            grid_flat_rate_validity.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


        }
        grid_flat_rate_validity.groupBy(grid_flat_rate_validity.getColIndexById("group_no"));
    }

    function populate_flat_rate_validity_periods()
    {
        //recall previously selected validity dates and children ages

        var arr_dates = [];
        var checkedids = grid_flat_rate_validity.getCheckedRows(grid_flat_rate_validity.getColIndexById("X"));
        var arrchecked = checkedids.split(",");
        for (var i = 0; i < arrchecked.length; i++)
        {
            var rid = arrchecked[i];
            if (rid != "")
            {
                var dtfrom = grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("dt_from")).getValue();
                var dtto = grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("dt_to")).getValue();
                var children_ages = grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("children_ages_display")).getValue();
                var children_ages_ids = grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).getValue();
                var groupno = grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("group_no")).getValue();

                arr_dates.push([dtfrom, dtto, groupno, children_ages, children_ages_ids]);
            }
        }

        //get all validity periods from periods tab
        grid_flat_rate_validity.clearAll();

        for (var i = 0; i < grid_period.getRowsNum(); i++) {
            var rwid = grid_period.getRowId(i);

            var dtfrom = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue(), "DD-MM-YYYY");
            var dtto = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue(), "DD-MM-YYYY");

            _flat_rate_validity_period_id--;
            grid_flat_rate_validity.addRow(_flat_rate_validity_period_id, [0, dtfrom, dtto]);
            grid_flat_rate_validity.setRowTextStyle(_flat_rate_validity_period_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        }

        //CHECK DATES ALREADY SELECTED
        //ALSO UPDATE CHILDREN 
        for (var x = 0; x < arr_dates.length; x++)
        {
            var item = arr_dates[x];
            var _dt_from = item[0];
            var _dt_to = item[1];
            var _grpno = item[2];
            var _ages = item[3];
            var _ages_ids = item[4];

            for (var i = 0; i < grid_flat_rate_validity.getRowsNum(); i++) {
                var rwid = grid_flat_rate_validity.getRowId(i);

                var dtfrom = grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("dt_from")).getValue();
                var dtto = grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("dt_to")).getValue();

                if (_dt_from == dtfrom && dtto == _dt_to)
                {
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("X")).setValue(1);
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("group_no")).setValue(_grpno);
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(_ages);
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(_ages_ids);
                }
            }

            grid_flat_rate_validity.groupBy(grid_flat_rate_validity.getColIndexById("group_no"));
        }
    }

    function populate_free_nights_validity_periods()
    {

        //recall previously selected validity dates

        var arr_dates = [];
        var checkedids = grid_free_nights_validity.getCheckedRows(grid_free_nights_validity.getColIndexById("X"));
        var arrchecked = checkedids.split(",");
        for (var i = 0; i < arrchecked.length; i++)
        {
            var rid = arrchecked[i];
            if (rid != "")
            {
                var dtfrom = grid_free_nights_validity.cells(rid, grid_free_nights_validity.getColIndexById("valid_from")).getValue();
                var dtto = grid_free_nights_validity.cells(rid, grid_free_nights_validity.getColIndexById("valid_to")).getValue();

                arr_dates.push(dtfrom + "," + dtto);

            }
        }


        //get all validity periods from periods tab
        grid_free_nights_validity.clearAll();

        for (var i = 0; i < grid_period.getRowsNum(); i++) {
            var rwid = grid_period.getRowId(i);

            var season = grid_period.cells(rwid, grid_period.getColIndexById("season")).getValue();
            var dtfrom = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue(), "DD-MM-YYYY");
            var dtto = utils_formatDate(grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue(), "DD-MM-YYYY");


            _free_nights_period_id--;
            grid_free_nights_validity.addRow(_free_nights_period_id, [0, season, dtfrom, dtto]);
            grid_free_nights_validity.setRowTextStyle(_free_nights_period_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        }

        //CHECK DATES ALREADY SELECTED
        for (var x = 0; x < arr_dates.length; x++)
        {
            var val = arr_dates[x];

            for (var i = 0; i < grid_free_nights_validity.getRowsNum(); i++) {
                var rwid = grid_free_nights_validity.getRowId(i);

                var dtfrom = grid_free_nights_validity.cells(rwid, grid_free_nights_validity.getColIndexById("valid_from")).getValue();
                var dtto = grid_free_nights_validity.cells(rwid, grid_free_nights_validity.getColIndexById("valid_to")).getValue();

                if (val == (dtfrom + "," + dtto))
                {
                    grid_free_nights_validity.cells(rwid, grid_free_nights_validity.getColIndexById("X")).setValue(1);
                }
            }
        }

    }


    function saveSPO_free_nights()
    {
        if (!form_free_nights.validate()) {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });


            return;
        }

        //== quick fix : no need for validity period in freenights

        /*
         var checkedids = grid_free_nights_validity.getCheckedRows(grid_free_nights_validity.getColIndexById("X"));
         if (utils_trim(checkedids, " ") == "")
         {
         dhtmlx.alert({
         text: "Please select a Validity Period!",
         type: "alert-warning",
         title: "Special Offer",
         callback: function () {
         }
         });
         
         return;
         }
         */

        if (grid_free_nights.getRowsNum() == 0)
        {
            dhtmlx.alert({
                text: "Please specify at least one Free Night!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });

            return;
        }


        for (var i = 0; i < grid_free_nights.getRowsNum(); i++)
        {
            var rwid = grid_free_nights.getRowId(i);

            var pay_nights = grid_free_nights.cells(rwid, grid_free_nights.getColIndexById("pay_nights")).getValue();
            var stay_nights = grid_free_nights.cells(rwid, grid_free_nights.getColIndexById("stay_nights")).getValue();


            if (pay_nights == "" || stay_nights == "")
            {
                dhtmlx.alert({
                    text: "Please specify Pay Nights and Stay Nights!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_free_nights.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }

            pay_nights = parseInt(pay_nights, 10);
            stay_nights = parseInt(stay_nights, 10);

            if (pay_nights >= stay_nights)
            {
                dhtmlx.alert({
                    text: "Pay Nights cannnot be greater or equal to Stay Nights!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_free_nights.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }


        }

        //=============

        saveSPO();



    }

    function onGridFlatRateValidityChanged(rId, cInd, nValue)
    {
        grid_flat_rate_validity.groupBy(grid_flat_rate_validity.getColIndexById("group_no"));
    }
    function onGridFlatRateValidityEdit(stage, rid, cInd, nValue, oValue)
    {
        if (grid_flat_rate_validity.getColIndexById("group_no") == cInd)
        {
            if (stage == 0)
            {
                if (grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("X")).getValue() == 0)
                {
                    //must be checked first
                    return false;
                }
            } else if (stage == 2)
            {
                nValue = utils_trim(nValue, " ");
                if (nValue != "" && isNaN(nValue))
                {
                    return false;
                }
            }
        }


        return true;
    }

    function onGridFamilyDiscountEdit(stage, rid, cInd, nValue, oValue)
    {
        if (stage == 2)
        {
            var colid = grid_family_discount_childrenage.getColumnId(cInd);
            //child_age_from,child_age_to,discount_percentage_value,discount_value

            if (colid == "child_age_from" || colid == "child_age_to" ||
                    colid == "discount_value")
            {
                if (nValue != "" && isNaN(nValue))
                {
                    return false;
                }
            }
        }
        return true;
    }

    function onGridFreeNightsEdit(stage, rid, cInd, nValue, oValue)
    {
        if (stage == 2)
        {
            if (nValue != "")
            {
                if (isNaN(nValue))
                {
                    return false;
                }
            }

            var paynights = parseInt(grid_free_nights.cells(rid, grid_free_nights.getColIndexById("pay_nights")).getValue(), 10);
            var staynights = parseInt(grid_free_nights.cells(rid, grid_free_nights.getColIndexById("stay_nights")).getValue(), 10);
            var free_nights = staynights - paynights;

            if (free_nights <= 0)
            {
                dhtmlx.alert({
                    text: "Pay Nights cannot be greater or equal to Stay Nights",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_free_nights.selectRowById(rid, false, true, false);
                    }
                });
                return false;
            }

            grid_free_nights.cells(rid, grid_free_nights.getColIndexById("free_nights")).setValue(free_nights);

        }

        return true;
    }


    function _validateOverlappingAges(arr_ages)
    {
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
                        return false;
                    }
                }
            }

        }

        return true;
    }

    function validate_children_ages_overlap_grid(grid, rwid, displaycol_id, idcol_id)
    {

        var child_ages_ids = grid.cells(rwid, grid.getColIndexById(idcol_id)).getValue();
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


        return _validateOverlappingAges(arr_ages);
    }

    function validate_children_ages_overlap(form, inputid)
    {
        var child_ages_ids = form.getItemValue(inputid);
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


        return _validateOverlappingAges(arr_ages);
    }

    function childCallBack(form, inputdisplay, inputid)
    {

        //validate children ages
        if (!validate_children_ages_overlap(form, inputid))
        {

            dhtmlx.alert({
                text: "Overlapping Children Ages Detected!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    showPopUp(form, "Children Ages", inputdisplay, inputid, _dsChildPolicy, "MULTIPLE", childCallBack);
                }
            });
            return;
        }
    }


    function saveSPO_meal_upgrade()
    {
        for (var i = 0; i < grid_meal_upgrade.getRowsNum(); i++) {

            var rwid = grid_meal_upgrade.getRowId(i);

            var meal_from = grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_from_fk")).getValue();
            var meal_to = grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_to_fk")).getValue();

            if (meal_from == "" || meal_to == "")
            {
                dhtmlx.alert({
                    text: "Please Select a Meal From and To!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_meal_upgrade.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            } else if (meal_from == meal_to)
            {
                dhtmlx.alert({
                    text: "Meal Upgrade From and To cannot be the same!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_meal_upgrade.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }
        }

        saveSPO();
    }

    function saveSPO_upgrade()
    {
        //validate room upgrade

        for (var i = 0; i < grid_upgrade.getRowsNum(); i++) {

            var rwid = grid_upgrade.getRowId(i);

            var room_from = grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_from_fk")).getValue();
            var room_to = grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_to_fk")).getValue();

            if (room_from == "" || room_to == "")
            {
                dhtmlx.alert({
                    text: "Please Select a Room From and To!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_upgrade.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            } else if (room_from == room_to)
            {
                dhtmlx.alert({
                    text: "Room Upgrade From and To cannot be the same!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_upgrade.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }
        }

        saveSPO();

        return;
    }

    function loadUpgradeGridCombos()
    {

        var cbo1 = grid_upgrade.getColumnCombo(grid_upgrade.getColIndexById("room_from_fk"));
        var cbo2 = grid_upgrade.getColumnCombo(grid_upgrade.getColIndexById("room_to_fk"));

        cbo1.clearAll();
        cbo2.clearAll();

        var rooms_ids = form_name.getItemValue("rooms_ids");
        var arrids = rooms_ids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var roomid = utils_trim(arrids[i], " ");
            if (roomid != "")
            {
                var item = _dsRooms.item(roomid);
                var id = item.id;
                var txt = item.value;

                cbo1.addOption([{value: id, text: txt}]);
                cbo2.addOption([{value: id, text: txt}]);
            }
        }

        cbo1.readonly(true);
        cbo2.readonly(true);


        //now check if the existing rows in grid_upgrade have values that are in the room combo
        var arrids_to_delete = [];

        for (var i = 0; i < grid_upgrade.getRowsNum(); i++) {
            var rwid = grid_upgrade.getRowId(i);

            var room_from_fk = grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_from_fk")).getValue();
            var room_to_fk = grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_to_fk")).getValue();

            //if not then delete the row!
            if (room_from_fk != "" && !arrids.includes(room_from_fk))
            {
                arrids_to_delete.push(rwid);
            }
            if (room_to_fk != "" && !arrids.includes(room_to_fk))
            {
                arrids_to_delete.push(rwid);
            }

            grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_from_fk")).setValue(room_from_fk);
            grid_upgrade.cells(rwid, grid_upgrade.getColIndexById("room_to_fk")).setValue(room_to_fk);

        }

        for (var i = 0; i < arrids_to_delete.length; i++) {
            var rwid = arrids_to_delete[i];
            if (rwid != "")
            {
                grid_upgrade.deleteRow(rwid);
            }
        }
    }


    function loadFamilyDiscountGridCombos()
    {
        var cbo = grid_family_discount_childrenage.getColumnCombo(grid_family_discount_childrenage.getColIndexById("discount_percentage_value"));
        cbo.addOption([{value: "%ROOM", text: "Percentage Room"}]);
        cbo.addOption([{value: "%ALL", text: "Percentage All"}]);
        cbo.addOption([{value: "FLAT_PNI", text: "Flat PNI"}]);
        cbo.addOption([{value: "FLAT_PPPN", text: "Flat PPPN"}]);

        cbo.readonly(true);
    }


    function nextStepApplicable()
    {
        var template = form_name.getItemValue("template");


        if (template == "early_booking")
        {
            tabSpo.setTabActive("discounts");
            toggleSPOTabsEnable("discounts");

        } else if (template == "long_stay")
        {
            tabSpo.setTabActive("discounts");
            toggleSPOTabsEnable("discounts");

        } else if (template == "honeymoon")
        {
            tabSpo.setTabActive("wedding_discounts");
            toggleSPOTabsEnable("wedding_discounts");
            onFormWeddingDiscountsChanged("wedding_apply_discount_both_basis");
            onFormWeddingDiscountsChanged("wedding_apply_discount_groom_basis");
            onFormWeddingDiscountsChanged("wedding_apply_discount_bride_basis");

        } else if (template == "free_nights")
        {
            tabSpo.setTabActive("free_nights");
            toggleSPOTabsEnable("free_nights");
            populate_free_nights_validity_periods();

        } else if (template == "free_upgrade")
        {
            tabSpo.setTabActive("upgrade");
            toggleSPOTabsEnable("upgrade");
            loadUpgradeGridCombos();

        } else if (template == "wedding_anniversary")
        {
            tabSpo.setTabActive("wedding_anniversary");
            toggleSPOTabsEnable("wedding_anniversary");
            onFormWeddingAnniversaryChanged("wedding_apply_discount_both_basis");
            onFormWeddingAnniversaryChanged("wedding_apply_discount_groom_basis");
            onFormWeddingAnniversaryChanged("wedding_apply_discount_bride_basis");

        } else if (template == "family_offer")
        {
            tabSpo.setTabActive("family_discount");
            toggleSPOTabsEnable("family_discount");

        } else if (template == "wedding_party")
        {
            tabSpo.setTabActive("wedding_party");
            toggleSPOTabsEnable("wedding_party");
            onFormWeddingPartyChanged("wedding_apply_discount_both_basis");
            onFormWeddingPartyChanged("wedding_apply_discount_groom_basis");
            onFormWeddingPartyChanged("wedding_apply_discount_bride_basis");

        } else if (template == "senior_offer")
        {
            tabSpo.setTabActive("senior");
            toggleSPOTabsEnable("senior");

        } else if (template == "meals_upgrade")
        {
            tabSpo.setTabActive("meals_upgrade");
            toggleSPOTabsEnable("meals_upgrade");
            loadMealUpgradeGridCombos();

        } else if (template == "discount")
        {
            tabSpo.setTabActive("discounts");
            toggleSPOTabsEnable("discounts");
        }
    }


    function saveSPO_family_discounts()
    {
        if (!form_familydiscounts.validate())
        {
            dhtmlx.alert({
                text: "Please enter appropriate values for highlighted fields!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {}
            });

            return;
        }



        //validate each childage discount
        for (var i = 0; i < grid_family_discount_childrenage.getRowsNum(); i++)
        {
            var rwid = grid_family_discount_childrenage.getRowId(i);


            var child_age_from = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("child_age_from")).getValue();
            var child_age_to = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("child_age_to")).getValue();
            var discount_percentage_value = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("discount_percentage_value")).getValue();
            var discount_value = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("discount_value")).getValue();


            if (child_age_from == "" || child_age_to == "" ||
                    discount_percentage_value == "" || discount_value == "")
            {
                dhtmlx.alert({
                    text: "Please fill in Missing cell values!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_family_discount_childrenage.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }

            child_age_from = parseInt(child_age_from, 10);
            child_age_to = parseInt(child_age_to, 10);

            if (child_age_from > child_age_to)
            {
                dhtmlx.alert({
                    text: "Invalid Child Age order!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_family_discount_childrenage.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }
        }

        //validate age overlappings
        //TODO
        for (var i = 0; i < grid_family_discount_childrenage.getRowsNum(); i++)
        {
            var rwid = grid_family_discount_childrenage.getRowId(i);
            var child_age_from = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("child_age_from")).getValue();
            var child_age_to = grid_family_discount_childrenage.cells(rwid, grid_family_discount_childrenage.getColIndexById("child_age_to")).getValue();

            if (!validate_family_discounts_age_overlapping(child_age_from, child_age_to, rwid))
            {
                dhtmlx.alert({
                    text: "Overlapping Age Ranges Detected!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        grid_family_discount_childrenage.selectRowById(rwid, false, true, false);
                    }
                });

                return;
            }
        }


        saveSPO();

    }

    function validate_family_discounts_age_overlapping(agfrom, agto, rwid_skip)
    {
        for (var i = 0; i < grid_family_discount_childrenage.getRowsNum(); i++)
        {
            var rwid_inner = grid_family_discount_childrenage.getRowId(i);
            var agfrom_inner = grid_family_discount_childrenage.cells(rwid_inner, grid_family_discount_childrenage.getColIndexById("child_age_from")).getValue();
            var agto_inner = grid_family_discount_childrenage.cells(rwid_inner, grid_family_discount_childrenage.getColIndexById("child_age_to")).getValue();

            if (agfrom <= agto_inner && agfrom_inner <= agto && rwid_skip != rwid_inner)
            {
                return false;
            }

        }

        return true;

    }

    function saveSPO_wedding_party()
    {

        var wedding_min_guests = utils_trim(form_wedding_party_discounts.getItemValue("wedding_min_guests"), " ");
        var wedding_max_guests = utils_trim(form_wedding_party_discounts.getItemValue("wedding_max_guests"), " ");

        if (wedding_min_guests != "" && isNaN(wedding_min_guests))
        {
            dhtmlx.alert({
                text: "Please enter numeric Minimum Guests!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_wedding_party_discounts.setItemFocus("wedding_min_guests")
                }
            });

            return;
        }

        if (wedding_max_guests != "" && isNaN(wedding_max_guests))
        {
            dhtmlx.alert({
                text: "Please enter numeric Maximum Guests!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    form_wedding_party_discounts.setItemFocus("wedding_max_guests")
                }
            });

            return;
        }

        if (wedding_min_guests != "" && wedding_max_guests != "")
        {
            wedding_min_guests = parseInt(wedding_min_guests, 10);
            wedding_max_guests = parseInt(wedding_max_guests, 10);

            if (wedding_min_guests > wedding_max_guests)
            {
                dhtmlx.alert({
                    text: "Invalid Wedding Guests Order!",
                    type: "alert-warning",
                    title: "Special Offer",
                    callback: function () {
                        form_wedding_party_discounts.setItemFocus("wedding_min_guests")
                    }
                });

                return;
            }

        }



        var both = form_wedding_party_discounts.isItemChecked("wedding_apply_discount_both");
        var groom = form_wedding_party_discounts.isItemChecked("wedding_apply_discount_groom");
        var bride = form_wedding_party_discounts.isItemChecked("wedding_apply_discount_bride");

        if (!both && !groom && !bride)
        {
            dhtmlx.alert({
                text: "Please specify Discount Mode",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }


        if (both && (groom || bride))
        {
            dhtmlx.alert({
                text: "Cannot apply Groom or Bride individual discounts if '<b>Apply Discount for Both</b>' is already selected!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }

        if (both)
        {
            if (!validate_wedding_discount_basis(form_wedding_party_discounts,
                    "wedding_apply_discount_both_basis",
                    "wedding_apply_discount_both_sngl_dbl",
                    "wedding_apply_discount_both_value"))
            {
                return;
            }
        }

        if (groom)
        {
            if (!validate_wedding_discount_basis(form_wedding_party_discounts,
                    "wedding_apply_discount_groom_basis",
                    "wedding_apply_discount_groom_sngl_dbl",
                    "wedding_apply_discount_groom_value"))
            {
                return;
            }
        }

        if (bride)
        {
            if (!validate_wedding_discount_basis(form_wedding_party_discounts,
                    "wedding_apply_discount_bride_basis",
                    "wedding_apply_discount_bride_sngl_dbl",
                    "wedding_apply_discount_bride_value"))
            {
                return;
            }
        }

        saveSPO();
    }


    function saveSPO_senior_offer()
    {
        if (!form_senior_discounts.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields with proper values!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }


        saveSPO();
    }


    function loadMealUpgradeGridCombos()
    {
        var cbo1 = grid_meal_upgrade.getColumnCombo(grid_meal_upgrade.getColIndexById("meal_from_fk"));
        var cbo2 = grid_meal_upgrade.getColumnCombo(grid_meal_upgrade.getColIndexById("meal_to_fk"));

        cbo1.clearAll();
        cbo2.clearAll();

        var mealsids = form_conditions.getItemValue("meals_ids");
        var arrids = mealsids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var mealid = utils_trim(arrids[i], " ");
            if (mealid != "")
            {
                var item = _dsMealPlans.item(mealid);
                var id = item.id;
                var txt = item.value;

                cbo1.addOption([{value: id, text: txt}]);
                cbo2.addOption([{value: id, text: txt}]);
            }
        }

        cbo1.readonly(true);
        cbo2.readonly(true);


        //now check if the existing rows in grid_meal_upgrade have values that are in the meal combo
        var arrids_to_delete = [];
        for (var i = 0; i < grid_meal_upgrade.getRowsNum(); i++) {
            var rwid = grid_meal_upgrade.getRowId(i);

            var meal_from_fk = grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_from_fk")).getValue();
            var meal_to_fk = grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_to_fk")).getValue();

            //if not then delete the row!
            if (meal_from_fk != "" && !arrids.includes(meal_from_fk))
            {
                arrids_to_delete.push(rwid);
            }
            if (meal_to_fk != "" && !arrids.includes(meal_to_fk))
            {
                arrids_to_delete.push(rwid);
            }

            grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_from_fk")).setValue(meal_from_fk);
            grid_meal_upgrade.cells(rwid, grid_meal_upgrade.getColIndexById("meal_to_fk")).setValue(meal_to_fk);
        }

        for (var i = 0; i < arrids_to_delete.length; i++) {
            var rwid = arrids_to_delete[i];
            if (rwid != "")
            {
                grid_meal_upgrade.deleteRow(rwid);
            }
        }
    }

    function onGridFlatRateValidityCheck(rwid, cInd, state)
    {
        if (!state)
        {
            //clear children ages
            grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue("");
            grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue("");
            grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("group_no")).setValue("");

        } else
        {
            //lookup for children ages from other rows and apply it to this one

            for (var i = 0; i < grid_flat_rate_validity.getRowsNum(); i++) {

                var _rwid = grid_flat_rate_validity.getRowId(i);

                var children_ages_display = grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).getValue();
                var children_ages_ids = grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).getValue();

                if (children_ages_display != "" && children_ages_ids != "")
                {
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(children_ages_display);
                    grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(children_ages_ids);
                    break;
                }
            }

        }

        grid_flat_rate_validity.groupBy(grid_flat_rate_validity.getColIndexById("group_no"));

    }





    function onGridFlatRateValiditySelect(rid, cid)
    {

        if (cid == grid_flat_rate_validity.getColIndexById("children_ages_display"))
        {
            if (grid_flat_rate_validity.cells(rid, grid_flat_rate_validity.getColIndexById("X")).getValue() == 1)
            {
                //show popup for children ages
                showPopUpGrid(grid_flat_rate_validity, "Children Ages", rid, "children_ages_display", "children_ages_ids", _dsChildPolicy, childGridCallBack);
            }
        }

    }


    function showPopUpGrid(grid, caller, rwid, displaycol_id, idcol_id, ds, callback)
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

                grid.cells(rwid, grid.getColIndexById(displaycol_id)).setValue(strvalues);
                grid.cells(rwid, grid.getColIndexById(idcol_id)).setValue(checkedids);

                popupwin.close();

                if (callback)
                {
                    callback(grid, caller, rwid, displaycol_id, idcol_id);
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


        var selectedids = utils_trim(grid.cells(rwid, grid.getColIndexById(idcol_id)).getValue(), " ");
        var selectedvalues = utils_trim(grid.cells(rwid, grid.getColIndexById(displaycol_id)).getValue(), " ");

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
        popupwin_spo.setModal(false);
        popupwin.setModal(true);
    }


    function childGridCallBack(grid, caller, rwid, displaycol_id, idcol_id)
    {
        //validate children ages
        if (!validate_children_ages_overlap_grid(grid, rwid, displaycol_id, idcol_id))
        {

            dhtmlx.alert({
                text: "Overlapping Children Ages Detected!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                    showPopUpGrid(grid, caller, rwid, displaycol_id, idcol_id, _dsChildPolicy, childGridCallBack);
                }
            });

            //revert back to old age ranges
            grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(_last_popup_display_values);
            grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(_last_popup_ids);

            return;
        }


        var newids = utils_trim(grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).getValue(), " ");
        var newdisplays = utils_trim(grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).getValue(), " ");

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

                        //copy paste to all other rows
                        for (var i = 0; i < grid_flat_rate_validity.getRowsNum(); i++) {

                            var _rwid = grid_flat_rate_validity.getRowId(i);
                            var checked = grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("X")).getValue();
                            if (checked == 1)
                            {
                                grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(newdisplays);
                                grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(newids);
                            }
                        }

                        //=========== CLEAN UPS ================
                        flat_rate_cleanJsonCapacityFromRoomsAndAges();

                        //============ DRASTIC! ================
                        for (var i = 0; i < _json_capacity.length; i++)
                        {
                            if (_json_capacity[i].room_action != "DELETE")
                            {
                                var room_dates = _json_capacity[i].room_dates;

                                for (var d = 0; d < room_dates.length; d++)
                                {
                                    var date_action = room_dates[d].date_action;
                                    var date_child_rules = room_dates[d].date_childpolicies_rules;
                                    var date_single_rules = room_dates[d].date_singleparentpolicies_rules;

                                    if (date_action != "DELETE")
                                    {
                                        for (var ad = 0; ad < date_single_rules.length; ad++)
                                        {
                                            date_single_rules[ad].rule_action = "DELETE";

                                        }

                                        for (var ad = 0; ad < date_child_rules.length; ad++)
                                        {
                                            date_child_rules[ad].rule_action = "DELETE";

                                        }

                                    }
                                }

                            }
                        }
                    } else
                    {
                        //revert back to old age ranges
                        grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_display")).setValue(_last_popup_display_values);
                        grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).setValue(_last_popup_ids);

                    }
                }});
        }

    }


    function flat_rate_cleanJsonAdults()
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

                                //clean up the values of adults if currency no longer in use
                                if (date_adultpolicies_rules[ad].rule_action != "DELETE")
                                {
                                    cleanAdultOrChildValuesCurrency(date_adultpolicies_rules[ad].rule_policy);
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

                                //clean up the values of adults if currency no longer in use
                                if (date_adultpolicies_rules[ad].rule_action != "DELETE")
                                {
                                    cleanAdultOrChildValuesCurrency(date_adultpolicies_rules[ad].rule_policy);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function flat_rate_cleanJsonChildren(sharing_single)
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
                    var date_rwid = room_dates[d].date_rwid;
                    var date_action = room_dates[d].date_action;
                    var date_dtfrom = room_dates[d].date_dtfrom;
                    var date_dtto = room_dates[d].date_dtto;
                    var date_childpolicies_rules = room_dates[d].date_childpolicies_rules;

                    if (date_action != "DELETE")
                    {
                        if (room_variants == "PERSONS")
                        {
                            cleanJsonChildren_by_date_persons(sharing_single, room_id, date_rwid, date_dtfrom, date_dtto, date_childpolicies_rules, room_variants);
                        } else if (room_variants == "UNITS" && sharing_single == "sharing")
                        {
                            cleanJsonChildren_Units_by_date(room_id, date_rwid, date_childpolicies_rules);
                        }
                    }
                }
            }
        }
    }

    function cleanJsonChildren_by_date_persons(sharing_single, room_id, date_rwid, date_dtfrom, date_dtto, date_childpolicies_rules, room_variants)
    {
        //get array group by rule_ageranges
        //get array group by rule_ageranges
        var arr_ruleranges = getChildrenSharingOwnRuleRanges(sharing_single, room_id, date_rwid, date_childpolicies_rules);

        //now for each ruleranges, assess if they are outside or within scope
        for (var i = 0; i < arr_ruleranges.length; i++)
        {
            cleanChildSharingOwnRuleRange(sharing_single, arr_ruleranges[i].rule_ageranges,
                    arr_ruleranges[i].room_id,
                    arr_ruleranges[i].date_rwid, date_childpolicies_rules);

            decideDeleteSharingOwnChildrenRuleRange(sharing_single, arr_ruleranges[i].rule_ageranges, room_id, date_rwid);
        }
    }


    function getChildrenSharingOwnRuleRanges(sharing_single, room_id, date_rwid, date_childpolicies_rules)
    {
        var arr = [];

        for (var i = 0; i < date_childpolicies_rules.length; i++)
        {

            if (date_childpolicies_rules[i].rule_action != "DELETE" &&
                    date_childpolicies_rules[i].rule_sharing_single == sharing_single.toUpperCase())
            {
                var rule_ageranges = date_childpolicies_rules[i].rule_ageranges;
                if (!is_rule_agerange_in_array(room_id, date_rwid, rule_ageranges, arr))
                {
                    arr.push({room_id: room_id, date_rwid: date_rwid, rule_ageranges: rule_ageranges});
                }
            }
        }

        return arr;
    }

    function is_rule_agerange_in_array(room_id, date_rwid, rule_ageranges, arr)
    {
        for (var i = 0; i < arr.length; i++)
        {
            if (arr[i].room_id == room_id &&
                    arr[i].date_rwid == date_rwid &&
                    arr[i].rule_ageranges == rule_ageranges)
            {
                return true;
            }
        }

        return false;
    }


    function enforceMaxPaxSharingOwnChildren(arr_rules, ag_from, ag_to, max_pax)
    {
        for (var i = 0; i < arr_rules.length; i++)
        {
            var rule_category = arr_rules[i].rule_category;
            var arr_rule_policy = arr_rules[i].rule_policy;

            cleanAdultOrChildValuesCurrency(arr_rule_policy);

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


    function decideDeleteSharingOwnChildrenRuleRange(sharing_single, rule_ageranges, roomid, date_rwid)
    {

        var arr_rules = getChildSharingOwnRulesByRuleRange(sharing_single, rule_ageranges, roomid, date_rwid);

        for (var i = 0; i < arr_rules.length; i++)
        {
            var flg_delete = true;

            var arr_rule_policy = arr_rules[i].rule_policy;
            for (var j = 0; j < arr_rule_policy.length; j++)
            {
                var agfrom = arr_rule_policy[j].policy_units_additional_child_agefrom;
                var agto = arr_rule_policy[j].policy_units_additional_child_ageto;
                var policy_action = arr_rule_policy[j].policy_action;

                if (policy_action != "DELETE")
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


    function deleteAllChildrenRates(sharing_single, date_childpolicies_rules, rule_agerange_filter)
    {
        //CHANGES:: CREATED FUNCTION

        //if rule_agerange_filter is not null, then delete only records for that rule_agerange

        for (var i = 0; i < date_childpolicies_rules.length; i++)
        {
            if (date_childpolicies_rules[i].rule_sharing_single == sharing_single.toUpperCase())
            {
                if (!rule_agerange_filter)
                {
                    //rule_agerange_filter is null, delete all
                    date_childpolicies_rules[i].rule_action = "DELETE";
                } else if (date_childpolicies_rules[i].rule_ageranges == rule_agerange_filter)
                {
                    //rule_agerange_filter is not null, delete only selected one
                    date_childpolicies_rules[i].rule_action = "DELETE";
                }
            }
        }
    }

    function sharingOwnChildrenAgesInCategory(children_ages, rule_ageranges)
    {
        var copy_children_ages = utils_deepCopy(children_ages);

        //rule_ageranges example: ; 0_1:0^2 ; 2_3:1^3 ;
        //it means: age range 0-1 with capacity 0-2
        //          age range 2-3 with capacity 1-3
        //explode rule_ageranges and check if each of the ages are in children_ages

        var arr_age_ranges = rule_ageranges.split(";");
        for (var i = 0; i < arr_age_ranges.length; i++)
        {
            var _the_range = utils_trim(arr_age_ranges[i], " ");

            if (_the_range != "")
            {
                var _the_ages = _the_range.split(":");
                var age_value = _the_ages[0];
                var minmax_values = _the_ages[1];
                
                if (utils_trim(age_value, " ") != "")
                {
                    var arr_age_from_to = age_value.split("_");
                    var age_from = arr_age_from_to[0];
                    var age_to = arr_age_from_to[1];
                    
                    var arr_min_max = minmax_values.split("^");
                    var _min = arr_min_max[0];
                    var _max = arr_min_max[1];
                    
                    var found = false;

                    //now for this age range, search into copy_children_ages
                    var j = copy_children_ages.length;

                    while (j--) {

                        if (copy_children_ages[j].capacity_child_agefrom == age_from &&
                                copy_children_ages[j].capacity_child_ageto == age_to &&
                                copy_children_ages[j].capacity_maxpax == _max && 
                                copy_children_ages[j].capacity_minpax == _min)
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

    function cleanChildSharingOwnRuleRange(sharing_single, rule_ageranges, roomid, date_rwid, date_childpolicies_rules)
    {
        //get all rule lines for that _rulerange
        var arr_rules = getChildSharingOwnRulesByRuleRange(sharing_single, rule_ageranges, roomid, date_rwid);

        if (arr_rules.length == 0)
        {
            return; //nothing to do since there are no rates defined for that rule_range
        }

        var return_arr = [];

        if (sharing_single == "sharing")
        {
            return_arr = childrenSharingChildRanges(roomid, date_rwid);
        } else
        {
            return_arr = ownRoomGetChildRanges(roomid, date_rwid);
        }

        var arr_result = return_arr.RESULT;


        //===================================================================\

        if (arr_result.length == 0)
        {
            //clear all children rates for that roomid and date_rwid
            deleteAllChildrenRates(sharing_single, date_childpolicies_rules, null);
        } else
        {
            //for each result
            var flg_found_ruleage_range_incapacity = false;

            for (var r = 0; r < arr_result.length; r++)
            {
                if (sharingOwnChildrenAgesInCategory(arr_result[r].children_ages, rule_ageranges))
                {
                    flg_found_ruleage_range_incapacity = true;

                    for (var i = 0; i < arr_result[r].children_ages.length; i++)
                    {
                        var ag_from = arr_result[r].children_ages[i].capacity_child_agefrom;
                        var ag_to = arr_result[r].children_ages[i].capacity_child_ageto;
                        var max_pax = arr_result[r].children_ages[i].capacity_maxpax;

                        enforceMaxPaxSharingOwnChildren(arr_rules, ag_from, ag_to, max_pax);
                    }
                }
            }

            //if rule_age range is no longer in capacity
            if (!flg_found_ruleage_range_incapacity)
            {
                //delete all rates record for that rule_ageranges
                deleteAllChildrenRates(sharing_single, date_childpolicies_rules, rule_ageranges);
            }
        }
    }


    function getChildSharingOwnRulesByRuleRange(rule_sharing_single, rulerange, roomid, date_rwid)
    {
        var arr = [];

        var dateobj = lookupCapacityRoomDateObj(roomid, date_rwid);
        var date_rules = dateobj.date_childpolicies_rules;

        for (var ad = 0; ad < date_rules.length; ad++)
        {
            if (date_rules[ad].rule_action != "DELETE" &&
                    date_rules[ad].rule_sharing_single == rule_sharing_single.toUpperCase())
            {
                var rule_ageranges = date_rules[ad].rule_ageranges;
                if (rulerange == rule_ageranges)
                {
                    arr.push(date_rules[ad]);
                }
            }
        }

        return arr;
    }


    function cleanChildSharingUnitsRuleRange(rule_ageranges, roomid, date_rwid, date_childpolicies_rules)
    {

        //get all rule lines for that _rulerange from the children_policy
        var arr_rules = getChildSharingOwnRulesByRuleRange("sharing", rule_ageranges, roomid, date_rwid);

        if (arr_rules.length == 0)
        {
            return; //nothing to do since there are no rates defined for that rule_range
        }

        //get the array of rule ranges from the capacity
        var return_arr = childrenUnitsChildRanges(roomid, date_rwid);

        var arr_result = return_arr.RESULT;


        if (arr_result.length == 0)
        {
            //clear all children rates for that roomid and date_rwid
            deleteAllChildrenRates("sharing", date_childpolicies_rules, null);
        } else
        {
            //for each result
            var flg_found_ruleage_range_incapacity = false;

            for (var r = 0; r < arr_result.length; r++)
            {
                var ch_ages = arr_result[r].children_ages;
                if (sharingOwnChildrenAgesInCategory(ch_ages, rule_ageranges))
                {
                    flg_found_ruleage_range_incapacity = true;

                    for (var i = 0; i < arr_result[r].children_ages.length; i++)
                    {
                        var ag_from = arr_result[r].children_ages[i].capacity_child_agefrom;
                        var ag_to = arr_result[r].children_ages[i].capacity_child_ageto;
                        var max_pax = arr_result[r].children_ages[i].capacity_maxpax;

                        enforceMaxPaxSharingOwnChildren(arr_rules, ag_from, ag_to, max_pax);
                    }
                }
            }

            //if rule_age range is no longer in capacity
            if (!flg_found_ruleage_range_incapacity)
            {
                //delete all rates record for that rule_ageranges
                deleteAllChildrenRates("sharing", date_childpolicies_rules, rule_ageranges);
            }
        }
    }


    function cleanJsonChildren_Units_by_date(room_id, date_rwid, date_childpolicies_rules)
    {
        //get array group by rule_ageranges found in children policies

        var arr_ruleranges = getChildrenSharingOwnRuleRanges("sharing", room_id, date_rwid, date_childpolicies_rules);

        //now for each ruleranges, assess if they are outside or within scope of the existing capacity
        for (var i = 0; i < arr_ruleranges.length; i++)
        {
            var myrulerange = arr_ruleranges[i].rule_ageranges;
            var myroom_id = arr_ruleranges[i].room_id;
            var mydate_rwid = arr_ruleranges[i].date_rwid;

            cleanChildSharingUnitsRuleRange(myrulerange, myroom_id, mydate_rwid, date_childpolicies_rules);

            decideDeleteSharingOwnChildrenRuleRange("sharing", arr_ruleranges[i].rule_ageranges, room_id, date_rwid);
        }
    }


    function rule_capacity_has_units_extra_children(arrrule_capacity)
    {
        //returns true if in this rule_capacity array, there is at least one extra child
        //returns false other wise

        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE")
            {
                if (capacityobj.capacity_category == "CH")
                {
                    if (capacityobj.capacity_maxpax > 0)
                    {
                        return true;
                    }
                }
            }
        }

        return false;

    }

    function childrenUnitsChildRanges(roomid, date_rwid)
    {

        //for each capacity rule, check if at least one child extra child
        //      push child_ages applicable to rule + min_max child
        //next rule


        var arr_result = [];
        var arr_main_childages = [];

        var dateobj = lookupCapacityRoomDateObj(roomid, date_rwid);
        var arrrulecounter = dateobj.date_capacity_rules;

        //===========================================================

        for (var i = 0; i < dateobj.date_capacity_rules.length; i++)
        {
            var ruleobj = arrrulecounter[i];
            if (ruleobj.rule_action != "DELETE")
            {
                var arrrule_capacity = ruleobj.rule_capacity;

                //got a potential parent obj
                //check if there is at least one extra child in the object
                if (rule_capacity_has_units_extra_children(arrrule_capacity))
                {
                    var xobj = pushUnitsCapacityChilrenObj(ruleobj);
                    arr_result.push(xobj);
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

    function pushUnitsCapacityChilrenObj(ruleobj)
    {
        var xobj = {children_ages: []};
        var arrrule_capacity = ruleobj.rule_capacity;
        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE" &&
                    capacityobj.capacity_category == "CH")
            {

                var capacity_maxpax = utils_parseInt(capacityobj.capacity_maxpax);
                var capacity_minpax = utils_parseInt(capacityobj.capacity_minpax);

                if (capacity_maxpax > 0)
                {
                    xobj.children_ages.push(capacityobj);
                }
            }
        }

        return xobj;
    }



    function  flat_rate_cleanJsonSingleParent()
    {
        //get array group by rule_ageranges
        var arr_ruleranges = getSingleParentRuleRanges();


        //now for each ruleranges, assess if they are outside or within scope
        for (var i = 0; i < arr_ruleranges.length; i++)
        {

            var myrulerange = arr_ruleranges[i].rule_ageranges;
            var myroom_id = arr_ruleranges[i].room_id;
            var mydate_rwid = arr_ruleranges[i].date_rwid;

            cleanSingleParentRuleRange(myrulerange, myroom_id, mydate_rwid);  

            decideDeleteSingleParentRuleRange(myrulerange, myroom_id, mydate_rwid); 
        }
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

                                    if (!is_rule_agerange_in_array(room_id, date_rwid, rule_ageranges, arr))
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

    function cleanSingleParentRuleRange(rule_ageranges, roomid, date_rwid)
    {

        //get all rule lines for that _rulerange
        var arr_rules = getSingleParentRulesByRuleRange(rule_ageranges,roomid,date_rwid);

        if (arr_rules.length == 0)
        {
            return; //nothing to do since there are no rates defined for that rule_range
        }

        var return_arr = singleParentGetChildRanges(roomid, date_rwid);
        var arr_result = return_arr.RESULT;
        //var arr_main_childages = return_arr.MAIN_CHILD_AGES;

        if (arr_result.length == 0)
        {
            //clear all children rates for that roomid and date_rwid
            deleteAllSingleParentChildrenRates(roomid, date_rwid, null);
        } else
        {

            //for each result
            var flg_found_ruleage_range_incapacity = false;

            for (var r = 0; r < arr_result.length; r++)
            {
                if (singleParentChildrenAgesInCategory(arr_result[r].children_ages, rule_ageranges))
                {
                    flg_found_ruleage_range_incapacity = true;

                    for (var i = 0; i < arr_result[r].children_ages.length; i++)
                    {
                        var ag_from = arr_result[r].children_ages[i].capacity_child_agefrom;
                        var ag_to = arr_result[r].children_ages[i].capacity_child_ageto;
                        var max_pax = arr_result[r].children_ages[i].capacity_maxpax;

                        enforceMaxPaxChildrenSingleParent(arr_rules, ag_from, ag_to, max_pax);
                    }
                }
            }

            //if rule_age range is no longer in capacity
            if (!flg_found_ruleage_range_incapacity)
            {
                //delete all rates record for that rule_ageranges
                deleteAllSingleParentChildrenRates(roomid, date_rwid, rule_ageranges);
            }
        }

    }

    function enforceMaxPaxChildrenSingleParent(arr_rules, ag_from, ag_to, max_pax)
    {
        for (var i = 0; i < arr_rules.length; i++)
        {
            //clean up values of the wrong currency


            var rule_category = arr_rules[i].rule_category;
            var arr_rule_policy = arr_rules[i].rule_policy;

            cleanAdultOrChildValuesCurrency(arr_rule_policy);

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


    function decideDeleteSingleParentRuleRange(rule_ageranges, myroom_id, mydate_rwid)
    {
        var arr_rules = getSingleParentRulesByRuleRange(rule_ageranges, myroom_id, mydate_rwid);

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

    function getSingleParentRulesByRuleRange(rulerange, _roomid, _date_rw_id)
    {
        var arr = [];
        
        
        var dateobj = lookupCapacityRoomDateObj(_roomid, _date_rw_id);
        var date_single_rules = dateobj.date_singleparentpolicies_rules;
        
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
        
        return arr;
    }

    function flat_rate_cleanJsonCapacityFromRoomsAndAges()
    {
        var roomids = form_name.getItemValue("rooms_ids");
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


    function validate_flat_rate_supp()
    {
        for (var i = 0; i < grid_flatrate_supp.getRowsNum(); i++) {
            var rwid = grid_flatrate_supp.getRowId(i);

            var meal_fk = grid_flatrate_supp.cells(rwid, grid_flatrate_supp.getColIndexById("mealplanfk")).getValue();
            var date_periods = grid_flatrate_supp.cells(rwid, grid_flatrate_supp.getColIndexById("dateperiods")).getValue();
            var adults = grid_flatrate_supp.cells(rwid, grid_flatrate_supp.getColIndexById("adult")).getValue();

            if (meal_fk == "")
            {
                dhtmlx.alert({
                    text: "Please select a Meal Plan",
                    type: "alert-warning",
                    title: "Supplements",
                    callback: function () {
                        grid_flatrate_supp.selectRowById(rwid, false, true, false);
                    }
                });
            }

            if (date_periods == "")
            {
                dhtmlx.alert({
                    text: "Please select a Date Period",
                    type: "alert-warning",
                    title: "Supplements",
                    callback: function () {
                        grid_flatrate_supp.selectRowById(rwid, false, true, false);
                    }
                });
            }

            if (adults == "")
            {
                dhtmlx.alert({
                    text: "Please enter an Adult count",
                    type: "alert-warning",
                    title: "Supplements",
                    callback: function () {
                        grid_flatrate_supp.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }
        }

        return true;
    }


    function validate_flat_rate_cancellation()
    {
        for (var i = 0; i < grid_flatrate_cancellation.getRowsNum(); i++) {
            var rwid = grid_flatrate_cancellation.getRowId(i);


            var cancellation_type = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("cancellation_type")).getValue();
            var charge_basis = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("charge_basis")).getValue();
            var charge_value = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("charge_value")).getValue();
            var days_before_arrival_from = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("days_before_arrival_from")).getValue();
            var days_before_arrival_to = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("days_before_arrival_to")).getValue();
            var date_before_arrival_from = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("date_before_arrival_from")).getValue();
            var date_before_arrival_to = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("date_before_arrival_to")).getValue();
            var rooms_ids = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("rooms_ids")).getValue();
            var dateperiods = grid_flatrate_cancellation.cells(rwid, grid_flatrate_cancellation.getColIndexById("dateperiods")).getValue();

            date_before_arrival_from = date_before_arrival_from.replace(/\//g, "-");
            date_before_arrival_to = date_before_arrival_to.replace(/\//g, "-");

            if (cancellation_type == "")
            {
                dhtmlx.alert({
                    text: "Please select Cancellation Type",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (charge_basis == "")
            {
                dhtmlx.alert({
                    text: "Please select Charge Basis",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (charge_value == "")
            {
                dhtmlx.alert({
                    text: "Please select Charge Value",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }



            if (isNaN(days_before_arrival_from)) {
                days_before_arrival_from = 0;
            }
            if (isNaN(days_before_arrival_to)) {
                days_before_arrival_to = 0;
            }

            days_before_arrival_from = parseInt(days_before_arrival_from, 10);
            days_before_arrival_to = parseInt(days_before_arrival_to, 10);

            if (days_before_arrival_from > days_before_arrival_to)
            {
                dhtmlx.alert({
                    text: "Invalid Days Before Arrival Order!",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }


            if (utils_isDate(date_before_arrival_from) && utils_isDate(date_before_arrival_to))
            {
                if (!utils_validateDateOrder(date_before_arrival_from, date_before_arrival_to))
                {
                    dhtmlx.alert({
                        text: "Invalid Arrival From Date Order!",
                        type: "alert-warning",
                        title: "Cancellation",
                        callback: function () {
                            grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                        }
                    });

                    return false;
                }
            }

            if (rooms_ids == "")
            {
                dhtmlx.alert({
                    text: "Please select a Room",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });

                return false;
            }

            if (dateperiods == "")
            {
                dhtmlx.alert({
                    text: "Please select a Date Period",
                    type: "alert-warning",
                    title: "Cancellation",
                    callback: function () {
                        grid_flatrate_cancellation.selectRowById(rwid, false, true, false);
                    }
                });

                return false
            }

        }

        return true;
    }

    function validate_flat_rate_checkinout()
    {
        for (var i = 0; i < grid_flatrate_checkinout.getRowsNum(); i++) {
            var rwid = grid_flatrate_checkinout.getRowId(i);

            var checkinout_type = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("checkinout_type")).getValue();
            var time_before_after = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("time_before_after")).getValue();
            var time_checkinout = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("time_checkinout")).getValue();
            var charge_basis = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("charge_basis")).getValue();
            var charge_value = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("charge_value")).getValue();
            var rooms_ids = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("rooms_ids")).getValue();
            var dateperiods = grid_flatrate_checkinout.cells(rwid, grid_flatrate_checkinout.getColIndexById("dateperiods")).getValue();


            if (checkinout_type == "")
            {
                dhtmlx.alert({
                    text: "Please select a Check In Out Type",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });

                return false;
            }

            if (time_before_after == "")
            {
                dhtmlx.alert({
                    text: "Please select Time Before or After",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (time_checkinout == "")
            {
                dhtmlx.alert({
                    text: "Please enter Time",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (charge_basis == "")
            {
                dhtmlx.alert({
                    text: "Please select a Charge Basis",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (charge_value == "")
            {
                dhtmlx.alert({
                    text: "Please enter a Charge Value",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (rooms_ids == "")
            {
                dhtmlx.alert({
                    text: "Please select a Room",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

            if (dateperiods == "")
            {
                dhtmlx.alert({
                    text: "Please select a Date Period",
                    type: "alert-warning",
                    title: "Check In Out",
                    callback: function () {
                        grid_flatrate_checkinout.selectRowById(rwid, false, true, false);
                    }
                });
                return false;
            }

        }
        return true;
    }


    function gotoFlatRateRates()
    {
        //validate commission
        if (!validateFlatRateTaxCommiSetting())
        {
            return;
        }

        //all clear to proceed
        tabSpo.setTabActive("flat_rate_rates");
        toggleSPOTabsEnable("flat_rate_rates");


        populateRoomsTree();
        populateCboRoomsFilter();

    }

    function gotoFlatRateCommission()
    {
        //validate currency
        if (!validateFlatRateCurrency())
        {
            return;
        }

        if (!validateFlatRateCurrencyMapping())
        {
            return;
        }


        //all clear to proceed
        tabSpo.setTabActive("flat_rate_commission");
        toggleSPOTabsEnable("flat_rate_commission");

        populateFlatRateTaxCommi();
    }



    function gotoFlatRateCurrency()
    {
        //validate first
        //in supplement
        tab_flatrate_policies.setTabActive("supplements");
        if (!validate_flat_rate_supp())
        {
            return;
        }

        tab_flatrate_policies.setTabActive("checkinout");
        if (!validate_flat_rate_checkinout())
        {
            return;
        }

        tab_flatrate_policies.setTabActive("cancellation");
        if (!validate_flat_rate_cancellation())
        {
            return;
        }

        //all clear to proceed
        tabSpo.setTabActive("flat_rate_currency");
        toggleSPOTabsEnable("flat_rate_currency");

    }

    function gotoFlatRatePolicies()
    {
        //validate that all periods are selected
        var checkedids = grid_flat_rate_validity.getCheckedRows(grid_flat_rate_validity.getColIndexById("X"));
        checkedids = utils_trim(checkedids, " ");

        if (checkedids == "")
        {
            dhtmlx.alert({
                text: "Please select all Validity Periods by Group!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {}
            });
            return;
        }

        var arr_ids = checkedids.split(",");

        if (arr_ids.length != grid_flat_rate_validity.getRowsNum())
        {
            dhtmlx.alert({
                text: "Please select all Validity Periods by Group!",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {}
            });
            return;
        }

        //make sure that all records have a group

        for (var i = 0; i < arr_ids.length; i++)
        {
            var rwid = utils_trim(arr_ids[i], " ");
            if (rwid != "")
            {
                var gno = grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("group_no")).getValue();
                gno = utils_trim(gno, " ");
                if (gno == "")
                {
                    dhtmlx.alert({
                        text: "Please specify Grouping Number for checked period!",
                        type: "alert-warning",
                        title: "Special Offer",
                        callback: function () {
                            grid_flat_rate_validity.selectRowById(rwid, false, true, false);
                        }
                    });
                    return;
                }
            }
        }


        tabSpo.setTabActive("flat_rate_policies");
        toggleSPOTabsEnable("flat_rate_policies");

        tab_flatrate_policies.setTabActive("supplements");
        createFlatRateMealGridStucture();

        cleanUpPeriodCellGrid(grid_flatrate_checkinout, "dateperiods");
        cleanUpPeriodCellGrid(grid_flatrate_cancellation, "dateperiods");

        cleanUpRoomCellGrid(grid_flatrate_checkinout, "rooms", "rooms_ids");
        cleanUpRoomCellGrid(grid_flatrate_cancellation, "rooms", "rooms_ids");

    }


    function saveSPO()
    {
        try
        {
            var params = "token=" + encodeURIComponent(global_token);

            //name
            var name = form_name.getFormData();
            params += "&name=" + encodeURIComponent(JSON.stringify(name));


            //periods
            var periods = form_periods.getFormData();
            params += "&periods=" + encodeURIComponent(JSON.stringify(periods));


            //period validity
            var period_validity = utils_dhxSerializeGridToJson(grid_period);
            params += "&period_validity=" + encodeURIComponent(period_validity);

            //conditions
            var conditions = form_conditions.getFormData();
            params += "&conditions=" + encodeURIComponent(JSON.stringify(conditions));

            //applicable
            var applicable = form_applicable.getFormData();
            params += "&applicable=" + encodeURIComponent(JSON.stringify(applicable));

            //discounts 
            var discounts = form_discounts.getFormData();
            params += "&discounts=" + encodeURIComponent(JSON.stringify(discounts));

            //wedding discounts
            var wedding_discounts = form_wedding_discounts.getFormData();
            params += "&wedding_discounts=" + encodeURIComponent(JSON.stringify(wedding_discounts));

            //free nights
            var free_nights = form_free_nights.getFormData();
            params += "&free_nights=" + encodeURIComponent(JSON.stringify(free_nights));

            //free nights validity period
            var free_nights_validity = utils_dhxSerializeGridToJson(grid_free_nights_validity);
            params += "&free_nights_validity=" + encodeURIComponent(free_nights_validity);

            //free nights grid
            var free_nights_grid = utils_dhxSerializeGridToJson(grid_free_nights);
            params += "&free_nights_grid=" + encodeURIComponent(free_nights_grid);

            //room upgrade
            var room_upgrade = utils_dhxSerializeGridToJson(grid_upgrade);
            params += "&room_upgrade=" + encodeURIComponent(room_upgrade);

            //wedding anniversary
            var wedding_anniversary = form_wedding_anniversary_discounts.getFormData();
            params += "&wedding_anniversary=" + encodeURIComponent(JSON.stringify(wedding_anniversary));


            //family offer
            var family_offer = form_familydiscounts.getFormData();
            params += "&family_offer=" + encodeURIComponent(JSON.stringify(family_offer));

            var family_offer_children = utils_dhxSerializeGridToJson(grid_family_discount_childrenage);
            params += "&family_offer_children=" + encodeURIComponent(family_offer_children);

            //wedding party
            var wedding_party = form_wedding_party_discounts.getFormData();
            params += "&wedding_party=" + encodeURIComponent(JSON.stringify(wedding_party));

            var senior_offer = form_senior_discounts.getFormData();
            params += "&senior_offer=" + encodeURIComponent(JSON.stringify(senior_offer));

            //meal upgrade
            var meal_upgrade_grid = utils_dhxSerializeGridToJson(grid_meal_upgrade);
            params += "&meal_upgrade_grid=" + encodeURIComponent(meal_upgrade_grid);

            //flat rate
            var flat_rate_validity_group_grid = utils_dhxSerializeGridToJson(grid_flat_rate_validity);
            params += "&flat_rate_validity_group_grid=" + encodeURIComponent(flat_rate_validity_group_grid);

            var flat_rate_supplement_grid = utils_dhxSerializeGridToJson(grid_flatrate_supp);
            params += "&flat_rate_supplement_grid=" + encodeURIComponent(flat_rate_supplement_grid);

            var flat_rate_checkinout_grid = utils_dhxSerializeGridToJson(grid_flatrate_checkinout);
            params += "&flat_rate_checkinout_grid=" + encodeURIComponent(flat_rate_checkinout_grid);

            var flat_rate_cancellation_grid = utils_dhxSerializeGridToJson(grid_flatrate_cancellation);
            params += "&flat_rate_cancellation_grid=" + encodeURIComponent(flat_rate_cancellation_grid);

            var flat_rate_currency_details = form_flat_rate_currency.getFormData();
            params += "&flat_rate_currency_details=" + encodeURIComponent(JSON.stringify(flat_rate_currency_details));

            var flat_rate_exchrates = utils_dhxSerializeGridToJson(grid_flat_rate_exchrates);
            params += "&flat_rate_exchrates=" + encodeURIComponent(flat_rate_exchrates);

            var flat_rate_currencymap = utils_dhxSerializeGridToJson(grid_flat_rate_currencymap);
            params += "&flat_rate_currencymap=" + encodeURIComponent(flat_rate_currencymap);

            params += "&flat_rate_taxcomm=" + encodeURIComponent(JSON.stringify(_flat_rate_tax_commi_obj));

            params += "&flat_rate_capacity=" + encodeURIComponent(JSON.stringify(_json_capacity));

            //temp

            console.log(_json_capacity);

            winspo_layout.progressOn();

            dhtmlxAjax.post("php/api/hotelspecialoffers/savespo.php", params, function (loader) {
                winspo_layout.progressOff();

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

                        form_name.setItemValue("id", json_obj.ID);
                        loadHotelSPOs(json_obj.ID);
                        modifySPO(json_obj.ID, false);

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
            winspo_layout.progressOff();
            console.log(err.message);
            dhtmlx.alert({
                text: err.message,
                type: "alert-warning",
                title: "Save SPO",
                callback: function () {
                }
            });
        }

    }
    function clearSPO()
    {
        form_name.clear();
        form_applicable.clear();
        form_conditions.clear();
        form_discounts.clear();
        form_familydiscounts.clear();
        form_flatrate_supp.clear();
        form_free_nights.clear();
        form_meal_upgrade.clear();
        form_periods.clear();
        form_senior_discounts.clear();
        form_upgrade.clear();
        form_wedding_anniversary_discounts.clear();
        form_wedding_discounts.clear();
        form_wedding_party_discounts.clear();

        grid_family_discount_childrenage.clearAll();
        grid_flat_rate_validity.clearAll();
        grid_flatrate_cancellation.clearAll();
        grid_flatrate_checkinout.clearAll();
        grid_flatrate_supp.clearAll();
        grid_free_nights.clearAll();
        grid_free_nights_validity.clearAll();
        grid_loadperiods_seasons.clearAll();
        grid_meal_upgrade.clearAll();
        grid_period.clearAll();
        grid_upgrade.clearAll();

    }


    function loadPeriodsGrid(json_arr) {
        grid_period.clearAll();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var season = json_arr[i].season;
            var seasonid = json_arr[i].season_fk;
            var valid_from = json_arr[i].valid_from;
            var valid_to = json_arr[i].valid_to;

            grid_period.addRow(id, [seasonid, valid_from, valid_to]);
            grid_period.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }


        grid_period.groupBy(grid_period.getColIndexById("season"));
        grid_period.sortRows(grid_period.getColIndexById("valid_from"), "date", "asc");
    }



    function loadFreeNightsGrid(json_arr)
    {
        grid_free_nights.clearAll();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var pay_nights = parseInt(json_arr[i].pay_nights, 10);
            var stay_nights = parseInt(json_arr[i].stay_nights, 10);
            var free_nights = stay_nights - pay_nights;

            if (isNaN(free_nights))
            {
                free_nights = 0;
            }

            grid_free_nights.addRow(id, "");
            grid_free_nights.cells(id, grid_free_nights.getColIndexById("pay_nights")).setValue(pay_nights);
            grid_free_nights.cells(id, grid_free_nights.getColIndexById("stay_nights")).setValue(stay_nights);
            grid_free_nights.cells(id, grid_free_nights.getColIndexById("free_nights")).setValue(free_nights);
            grid_free_nights.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }
    }

    function loadRoomUpgradeGrid(json_arr)
    {
        grid_upgrade.clearAll();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var room_from_fk = json_arr[i].room_from_fk;
            var room_to_fk = json_arr[i].room_to_fk;
            grid_upgrade.addRow(id, [room_from_fk, room_to_fk]);
            grid_upgrade.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }
    }

    function loadFamilyOfferChildAgeGrid(json_arr)
    {
        grid_family_discount_childrenage.clearAll();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var child_age_from = json_arr[i].child_age_from;
            var child_age_to = json_arr[i].child_age_to;
            var discount_percentage_value = json_arr[i].discount_percentage_value;
            var discount_value = json_arr[i].discount_value;

            grid_family_discount_childrenage.addRow(id, [child_age_from, child_age_to, discount_percentage_value, discount_value]);
            grid_family_discount_childrenage.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }
    }

    function loadMealUpgradeGrid(json_arr)
    {
        grid_meal_upgrade.clearAll();
        loadMealUpgradeGridCombos();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var meal_from_fk = json_arr[i].meal_from_fk;
            var meal_to_fk = json_arr[i].meal_to_fk;
            grid_meal_upgrade.addRow(id, [meal_from_fk, meal_to_fk]);
            grid_meal_upgrade.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }
    }

    function loadFreeNightsValidityGrid(json_arr)
    {
        grid_free_nights_validity.clearAll();
        for (var i = 0; i < json_arr.length; i++)
        {
            var id = json_arr[i].id;
            var valid_from = json_arr[i].valid_from;
            var valid_to = json_arr[i].valid_to;
            grid_free_nights_validity.addRow(id, [1, "", valid_from, valid_to]);
            grid_free_nights_validity.setRowTextStyle(id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }

    }

    function createFlatRateMealGridStucture()
    {
        //recall previous values first
        var arr_recall = JSON.parse(utils_dhxSerializeGridToJson(grid_flatrate_supp));

        grid_flatrate_supp.clearAll();
        grid_flatrate_supp = null;

        var header_txt = "Meal Plan,Is Main,Date Periods,Adult";
        var col_ids = "mealplanfk,ismain,dateperiods,adult";
        var col_types = "combo,ch,ro,edn";
        var col_initwidths = "150,60,200,70";
        var col_align = "center,center,center,center";
        var col_sorting = "str,int,date,int";


        //get the children ages from validity by periods groups
        for (var i = 0; i < grid_flat_rate_validity.getRowsNum(); i++) {

            var _rwid = grid_flat_rate_validity.getRowId(i);

            var children_ages_ids = grid_flat_rate_validity.cells(_rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).getValue();

            if (children_ages_ids != "")
            {

                var arr_ids = children_ages_ids.split(",");

                for (var i = 0; i < arr_ids.length; i++)
                {
                    var id = arr_ids[i];
                    if (id != "")
                    {
                        var item = _dsChildPolicy.item(id);
                        var agefrom = parseInt(item.agefrom, 10);
                        var ageto = parseInt(item.ageto, 10);

                        header_txt += ",Child " + agefrom + " - " + ageto;
                        col_ids += "," + agefrom + "_" + ageto;
                        col_types += ",edn";
                        col_initwidths += ",80";
                        col_align += ",center";
                        col_sorting += ",int";
                    }
                }

                break;
            }
        }


        grid_flatrate_supp = tab_flatrate_policies.cells("supplements").attachGrid();
        grid_flatrate_supp.setIconsPath('libraries/dhtmlx/imgs/');
        grid_flatrate_supp.setHeader(header_txt);
        grid_flatrate_supp.setColumnIds(col_ids);
        grid_flatrate_supp.setColTypes(col_types);
        grid_flatrate_supp.setInitWidths(col_initwidths);
        grid_flatrate_supp.setColAlign(col_align);
        grid_flatrate_supp.setColSorting(col_sorting);
        grid_flatrate_supp.enableAlterCss("", "");
        grid_flatrate_supp.enableEditTabOnly(true);
        grid_flatrate_supp.enableEditEvents(true, true, true);
        grid_flatrate_supp.attachEvent("onRowSelect", onGridFlatRateSuppSelect);
        grid_flatrate_supp.attachEvent("onCheck", onGridFlatRateSuppCheck);
        grid_flatrate_supp.enableMultiline(true);
        grid_flatrate_supp.init();

        load_flat_rate_meal_combo();
        populate_grid_flatrate_supp(arr_recall);
        load_flat_rate_meal_combo();


    }


    function loadFlatRateCurrency(buy, sell, general)
    {
        form_flat_rate_currency.setItemValue("mycostprice_currencyfk", general.flatrate_mycostprice_currencyfk);
        form_flat_rate_currency.setItemValue("selected_currency_buy_display", buy.currency_display);
        form_flat_rate_currency.setItemValue("selected_currency_buy_ids", buy.currency_ids);
        form_flat_rate_currency.setItemValue("selected_currency_sell_display", sell.currency_display);
        form_flat_rate_currency.setItemValue("selected_currency_sell_ids", sell.currency_ids);

        updateFlatRateExchangeRatesJson();

    }

    function loadFlatRateMapping(arr)
    {

        grid_flat_rate_currencymap.clearAll();

        for (var i = 0; i < arr.length; i++) {
            var currency_code_buy = arr[i].currency_code_buy;
            var currency_code_sell = arr[i].currency_code_sell;
            var currencysell_fk = arr[i].currencysell_fk;
            var currencybuy_fk = arr[i].currencybuy_fk;
            var rid = arr[i].id;

            grid_flat_rate_currencymap.addRow(rid, "");
            grid_flat_rate_currencymap.cells(rid, grid_flat_rate_currencymap.getColIndexById("currency_sell")).setValue(currency_code_sell);
            grid_flat_rate_currencymap.cells(rid, grid_flat_rate_currencymap.getColIndexById("currency_buy")).setValue(currencybuy_fk);
            grid_flat_rate_currencymap.cells(rid, grid_flat_rate_currencymap.getColIndexById("action")).setValue("");
            grid_flat_rate_currencymap.cells(rid, grid_flat_rate_currencymap.getColIndexById("currency_id_sell")).setValue(currencysell_fk);

            grid_flat_rate_currencymap.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }


    }

    function loadFlatRateExgRates(arr)
    {
        grid_flat_rate_exchrates.clearAll();

        for (var i = 0; i < arr.length; i++) {
            var currency_code_from = arr[i].currency_code_from;
            var currency_code_to = arr[i].currency_code_to;
            var exchange_rate = arr[i].exchange_rate;
            var from_currencyfk = arr[i].from_currencyfk;
            var to_currencyfk = arr[i].to_currencyfk;
            var rid = arr[i].id;

            grid_flat_rate_exchrates.addRow(rid, "");
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("currency_from")).setValue(currency_code_from);
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("currency_to")).setValue(currency_code_to);
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("rates_exchange_rate")).setValue(exchange_rate);
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("action")).setValue("");
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("currency_id_from")).setValue(from_currencyfk);
            grid_flat_rate_exchrates.cells(rid, grid_flat_rate_exchrates.getColIndexById("currency_id_to")).setValue(to_currencyfk);

            grid_flat_rate_exchrates.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }

    }

    function loadFlatRateCancellation(arr)
    {
        grid_flatrate_cancellation.clearAll();

        for (var i = 0; i < arr.length; i++) {
            var cancellation_type = arr[i].cancellation_type;
            var charge_basis = arr[i].charge_basis;
            var charge_value = arr[i].charge_value;
            var date_periods = arr[i].date_periods;
            var rid = arr[i].id;
            var date_before_arrival_from = utils_formatDate(arr[i].dates_before_arrival_from, "DD/MM/YYYY");
            var date_before_arrival_to = utils_formatDate(arr[i].dates_before_arrival_to, "DD/MM/YYYY");
            var days_before_arrival_from = arr[i].days_before_arrival_from;
            var days_before_arrival_to = arr[i].days_before_arrival_to;
            var room_ids = arr[i].room_ids;
            var room_names = arr[i].room_names;

            grid_flatrate_cancellation.addRow(rid, "");
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("cancellation_type")).setValue(cancellation_type);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("charge_basis")).setValue(charge_basis);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("charge_value")).setValue(charge_value);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("days_before_arrival_from")).setValue(days_before_arrival_from);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("days_before_arrival_to")).setValue(days_before_arrival_to);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("date_before_arrival_from")).setValue(date_before_arrival_from);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("date_before_arrival_to")).setValue(date_before_arrival_to);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("rooms")).setValue(room_names);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("rooms_ids")).setValue(room_ids);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("dateperiods")).setValue(date_periods);
            grid_flatrate_cancellation.cells(rid, grid_flatrate_cancellation.getColIndexById("rooms")).setValue(date_periods);

            grid_flatrate_cancellation.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        }
    }

    function loadFlatRateCheckInOut(arr)
    {
        grid_flatrate_checkinout.clearAll();

        for (var i = 0; i < arr.length; i++) {
            var charge_basis = arr[i].charge_basis;
            var charge_value = arr[i].charge_value;
            var checkinout_type = arr[i].checkinout_type;
            var date_periods = arr[i].date_periods;
            var rid = arr[i].id;
            var room_ids = arr[i].room_ids;
            var room_names = arr[i].room_names;
            var time_before_after = arr[i].time_before_after;
            var time_checkinout = arr[i].time_checkinout;

            grid_flatrate_checkinout.addRow(rid, "");
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("checkinout_type")).setValue(checkinout_type);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("time_before_after")).setValue(time_before_after);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("time_checkinout")).setValue(time_checkinout);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("charge_basis")).setValue(charge_basis);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("charge_value")).setValue(charge_value);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("rooms")).setValue(room_names);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("rooms_ids")).setValue(room_ids);
            grid_flatrate_checkinout.cells(rid, grid_flatrate_checkinout.getColIndexById("dateperiods")).setValue(date_periods);

            grid_flatrate_checkinout.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
        }


    }

    function loadFlatRateSupplements(arr)
    {
        createFlatRateMealGridStucture();

        for (var i = 0; i < arr.length; i++) {
            var mealplanfk = arr[i].mealplanfk;
            var ismain = arr[i].ismain;
            var date_periods = arr[i].date_periods;
            var adult = arr[i].adult;
            var rid = arr[i].id;
            var arr_child_count = arr[i].children_count;

            grid_flatrate_supp.addRow(rid, "");
            grid_flatrate_supp.cells(rid, grid_flatrate_supp.getColIndexById("mealplanfk")).setValue(mealplanfk);
            grid_flatrate_supp.cells(rid, grid_flatrate_supp.getColIndexById("ismain")).setValue(ismain);
            grid_flatrate_supp.cells(rid, grid_flatrate_supp.getColIndexById("dateperiods")).setValue(date_periods);
            grid_flatrate_supp.cells(rid, grid_flatrate_supp.getColIndexById("adult")).setValue(adult);

            for (var j = 0; j < arr_child_count.length; j++)
            {
                var colid = arr_child_count[j].child_age_from + "_" + arr_child_count[j].child_age_to;
                var count = arr_child_count[j].child_count;

                if (typeof grid_flatrate_supp.getColIndexById(colid) !== 'undefined')
                {
                    grid_flatrate_supp.cells(rid, grid_flatrate_supp.getColIndexById(colid)).setValue(count);
                }
            }

            grid_flatrate_supp.setRowTextStyle(rid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


        }

    }

    function populate_grid_flatrate_supp(arr_recall)
    {
        for (var i = 0; i < arr_recall.length; i++)
        {
            var rw = arr_recall[i];
            var rwid = rw.rwid;

            grid_flatrate_supp.addRow(rwid, "");
            grid_flatrate_supp.setRowTextStyle(rwid, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

            for (var attri in rw.cells) {
                if (typeof grid_flatrate_supp.getColIndexById(attri) !== 'undefined')
                {
                    grid_flatrate_supp.cells(rwid, grid_flatrate_supp.getColIndexById(attri)).setValue(rw.cells[attri]);
                }
            }

            cleanUpPeriodCellGrid(grid_flatrate_supp, "dateperiods");
        }
    }


    function cleanUpRoomCellGrid(grid, roomname_colid, roomid_colid)
    {
        var final_roomnames = "";
        var final_roomids = "";
        var first = true;


        //get the array of room ids from spo
        var form_roomids = form_name.getItemValue("rooms_ids");
        var arr_form_roomids = form_roomids.split(",");

        for (var i = 0; i < grid.getRowsNum(); i++) {
            var rwid = grid.getRowId(i);

            //clean up period dates that are no longer in period grid
            var roomids = grid.cells(rwid, grid.getColIndexById(roomid_colid)).getValue();

            var arrids = roomids.split(",");
            for (var j = 0; j < arrids.length; j++)
            {
                var roomid = utils_trim(arrids[j], " ");
                if (roomid != "" && arr_form_roomids.includes(roomid))
                {
                    if (!first)
                    {
                        final_roomnames += " , ";
                        final_roomids += ",";
                    }

                    var item = _dsRooms.item(roomid);
                    var roomname = item.value;
                    first = false;

                    final_roomnames += roomname;
                    final_roomids += roomid;
                }
            }

            grid.cells(rwid, grid.getColIndexById(roomname_colid)).setValue(final_roomnames);
            grid.cells(rwid, grid.getColIndexById(roomid_colid)).setValue(final_roomids);
        }
    }
    function cleanUpPeriodCellGrid(grid, dateperiods_colid)
    {
        for (var i = 0; i < grid.getRowsNum(); i++) {
            var rwid = grid.getRowId(i);

            //clean up period dates that are no longer in period grid
            var selected_periods = grid.cells(rwid, grid.getColIndexById(dateperiods_colid)).getValue();
            var arrperiods = selected_periods.split("<br>");
            var str_final = "";
            var str_first = true;

            for (var i = 0; i < arrperiods.length; i++)
            {
                var dates = arrperiods[i];
                if (dates != "")
                {
                    var arrdates = dates.split(" - ");
                    var dtfrom = arrdates[0];
                    var dtto = arrdates[1];

                    //now search if the date is in grid_period
                    if (isDatePeriodInGridPeriod(dtfrom, dtto))
                    {
                        if (!str_first)
                        {
                            str_final += "<br>";
                        }
                        str_first = false;
                        str_final += dtfrom + " - " + dtto;
                    }

                }
            }

            grid.cells(rwid, grid.getColIndexById(dateperiods_colid)).setValue(str_final);
        }

    }


    function isDatePeriodInGridPeriod(dtfrom, dtto)
    {
        //dtfrom,dtto in dd-mm-yyyy format

        for (var i = 0; i < grid_period.getRowsNum(); i++) {
            var rwid = grid_period.getRowId(i);

            var valid_from = grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue();
            var valid_to = grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue();

            valid_from = utils_formatDate(valid_from, "DD-MM-YYYY");
            valid_to = utils_formatDate(valid_to, "DD-MM-YYYY");

            if (dtfrom == valid_from && dtto == valid_to)
            {
                return true;
            }

        }
        return false;
    }


    function load_flat_rate_cancellation_combo()
    {

        var cbo1 = grid_flatrate_cancellation.getColumnCombo(grid_flatrate_cancellation.getColIndexById("cancellation_type"));
        cbo1.addOption([{value: "NS", text: "No Show"}]);
        cbo1.addOption([{value: "ED", text: "Early Departure"}]);
        cbo1.addOption([{value: "CC", text: "Cancellation"}]);
        cbo1.addOption([{value: "AA", text: "After Arrival Date"}]);
        cbo1.readonly(true);

        var cbo2 = grid_flatrate_cancellation.getColumnCombo(grid_flatrate_cancellation.getColIndexById("charge_basis"));
        cbo2.addOption([{value: "%D", text: "% Discount"}]);
        cbo2.addOption([{value: "%C", text: "% Charge"}]);
        cbo2.addOption([{value: "FLAT", text: "Flat"}]);
        cbo2.addOption([{value: "NIGHTS", text: "Nights"}]);
        cbo2.addOption([{value: "REMAINING NIGHTS", text: "Remaining Nights"}]);
        cbo2.readonly(true);
    }

    function load_flat_rate_checkinout_combo()
    {
        var cbo1 = grid_flatrate_checkinout.getColumnCombo(grid_flatrate_checkinout.getColIndexById("checkinout_type"));
        cbo1.addOption([{value: "ECI", text: "Early Check-In"}]);
        cbo1.addOption([{value: "LCO", text: "Late Check-Out"}]);
        cbo1.readonly(true);

        var cbo2 = grid_flatrate_checkinout.getColumnCombo(grid_flatrate_checkinout.getColIndexById("time_before_after"));
        cbo2.addOption([{value: "BEFORE", text: "Before"}]);
        cbo2.addOption([{value: "AFTER", text: "After"}]);
        cbo2.readonly(true);

        var cbo3 = grid_flatrate_checkinout.getColumnCombo(grid_flatrate_checkinout.getColIndexById("charge_basis"));
        cbo3.addOption([{value: "%D", text: "% Discount"}]);
        cbo3.addOption([{value: "%C", text: "% Charge"}]);
        cbo3.addOption([{value: "FLAT", text: "Flat"}]);
        cbo3.readonly(true);

        return;
    }

    function load_flat_rate_meal_combo()
    {
        var cbo = grid_flatrate_supp.getColumnCombo(grid_flatrate_supp.getColIndexById("mealplanfk"));

        cbo.clearAll();

        var mealsids = form_conditions.getItemValue("meals_ids");
        var arrids = mealsids.split(",");

        for (var i = 0; i < arrids.length; i++)
        {
            var mealid = utils_trim(arrids[i], " ");
            if (mealid != "")
            {
                var item = _dsMealPlans.item(mealid);
                var id = item.id;
                var txt = item.value;

                cbo.addOption([{value: id, text: txt}]);
            }
        }

        cbo.readonly(true);


        //now check if the existing rows in grid have values that are in the meal combo
        var arrids_to_delete = [];
        for (var i = 0; i < grid_flatrate_supp.getRowsNum(); i++) {
            var rwid = grid_flatrate_supp.getRowId(i);

            var meal_fk = grid_flatrate_supp.cells(rwid, grid_flatrate_supp.getColIndexById("mealplanfk")).getValue();

            //if not then delete the row!
            if (meal_fk != "" && !arrids.includes(meal_fk))
            {
                arrids_to_delete.push(rwid);
            }
        }

        for (var i = 0; i < arrids_to_delete.length; i++) {
            var rwid = arrids_to_delete[i];
            if (rwid != "")
            {
                grid_flatrate_supp.deleteRow(rwid);
            }
        }

    }



    function onGridFlatRateCancellationEdit(stage, rId, cInd, nValue, oValue)
    {
        var colid = grid_flatrate_cancellation.getColumnId(cInd);

        if (stage == 1)
        {
            if (grid_flatrate_cancellation.editor && grid_flatrate_cancellation.editor.obj)
            {
                grid_flatrate_cancellation.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                if (colid == "charge_value" ||
                        colid == "days_before_arrival_from" ||
                        colid == "days_before_arrival_to")
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
            }

        }

        return true;
    }

    function onGridFlatRateCancellationKeyPress(code, cFlag, sFlag)
    {

        var rwid = grid_flatrate_cancellation.getSelectedRowId();
        var ind = grid_flatrate_cancellation.getSelectedCellIndex();
        if (rwid && ind)
        {
            if (code == 46) //delete key
            {
                var colid = grid_flatrate_cancellation.getColumnId(ind);
                if (colid == "date_before_arrival_from")
                {
                    grid_flatrate_cancellation.cells(rwid, ind).setValue("");
                } else if (colid == "date_before_arrival_to")
                {
                    grid_flatrate_cancellation.cells(rwid, ind).setValue("");
                }
            }
        }

        return true;
    }


    function onGridFlatRateCancellationSelect(rid, cid)
    {
        if (cid == grid_flatrate_cancellation.getColIndexById("dateperiods"))
        {
            showPeriodPopUpGrid(grid_flatrate_cancellation, "Validity Period", rid, "dateperiods");
        } else if (cid == grid_flatrate_cancellation.getColIndexById("rooms"))
        {
            showRoomsPopUpGrid(grid_flatrate_cancellation, "Select Rooms", rid, "rooms", "rooms_ids", null);
        }
    }


    function onGridFlatRateCheckinoutEdit(stage, rId, cInd, nValue, oValue)
    {
        var colid = grid_flatrate_checkinout.getColumnId(cInd);

        if (stage == 0)
        {
            if (colid == "time_before_after")
            {
                return false; //do not allow editing
            }
            return true;
        } else if (stage == 1)
        {
            if (grid_flatrate_checkinout.editor && grid_flatrate_checkinout.editor.obj)
            {
                grid_flatrate_checkinout.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                if (colid == "charge_value")
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
                } else if (colid == "time_checkinout")
                {
                    if (nValue != "")
                    {
                        if (!utils_isValidTime(nValue))
                        {
                            return false;
                        }
                    }

                } else if (colid == "checkinout_type")
                {
                    if (nValue == "ECI")
                    {
                        //set beforeafter to before
                        grid_flatrate_checkinout.cells(rId, grid_flatrate_checkinout.getColIndexById("time_before_after")).setValue("BEFORE");
                    } else if (nValue == "LCO")
                    {
                        //set beforeafter to before
                        grid_flatrate_checkinout.cells(rId, grid_flatrate_checkinout.getColIndexById("time_before_after")).setValue("AFTER");
                    }
                }
            }
        }
        return true;
    }

    function onGridFlatRateCheckinoutSelect(rid, cid)
    {
        if (cid == grid_flatrate_checkinout.getColIndexById("dateperiods"))
        {
            showPeriodPopUpGrid(grid_flatrate_checkinout, "Validity Period", rid, "dateperiods");
        } else if (cid == grid_flatrate_checkinout.getColIndexById("rooms"))
        {
            showRoomsPopUpGrid(grid_flatrate_checkinout, "Select Rooms", rid, "rooms", "rooms_ids", null);
        }
    }


    function onGridFlatRateSuppCheck(rId, cInd, state)
    {
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
            for (var i = 0; i < grid_flatrate_supp.getRowsNum(); i++)
            {
                var rowid = grid_flatrate_supp.getRowId(i);
                if (rowid != rId)
                {
                    grid_flatrate_supp.cells(rowid, cInd).setValue("0");
                }
            }
        }

        return true;
    }

    function onGridFlatRateSuppSelect(rid, cid)
    {
        if (cid == grid_flatrate_supp.getColIndexById("dateperiods"))
        {
            showPeriodPopUpGrid(grid_flatrate_supp, "Validity Period", rid, "dateperiods");
        }
    }


    function showRoomsPopUpGrid(grid, title, rwid, displaycol_id, idcol_id, callback)
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
            {type: "button", name: "cmdApply", tooltip: "Select Checked Rooms", value: "Select Checked Rooms", width: "200", height: "40"}

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

                grid.cells(rwid, grid.getColIndexById(displaycol_id)).setValue(strvalues);
                grid.cells(rwid, grid.getColIndexById(idcol_id)).setValue(checkedids);

                popupwin.close();

                if (callback)
                {
                    callback(grid, title, rwid, displaycol_id, idcol_id);
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

        //load rooms selected
        var roomids = form_name.getItemValue("rooms_ids");
        var arr_roomids = roomids.split(",");

        for (var i = 0; i < arr_roomids.length; i++) {

            var roomid = arr_roomids[i];
            if (roomid != "")
            {
                var item = _dsRooms.item(roomid);
                var _id = item.id;
                var _cell = item.value;
                pop_grid.addRow(_id, [0, _cell]);
            }
        }

        var selectedids = utils_trim(grid.cells(rwid, grid.getColIndexById(idcol_id)).getValue(), " ");
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
        popupwin.setText(title);
        popupwin_spo.setModal(false);
        popupwin.setModal(true);
    }

    function showPeriodPopUpGrid(grid, title, rwid, date_colid, callback)
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
            {type: "button", name: "cmdApply", tooltip: "Select Checked Dates", value: "Select Checked Dates", width: "200", height: "40"}

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

                        var value = pop_grid.cells(id, pop_grid.getColIndexById("period_date")).getValue();
                        if (!first)
                        {
                            strvalues += "<br>";
                        }
                        strvalues += value;

                        first = false;
                    }
                }

                grid.cells(rwid, grid.getColIndexById(date_colid)).setValue(strvalues);

                popupwin.close();

                if (callback)
                {
                    callback(grid, title, rwid, date_colid);
                }
            }
        });

        pop_grid = null;
        pop_grid = pop_layout.cells("a").attachGrid(300, 200);
        pop_grid.setIconsPath('libraries/dhtmlx/imgs/');
        pop_grid.setHeader(",Period Date");
        pop_grid.setColumnIds("X,period_date");
        pop_grid.setColTypes("ch,ro");
        pop_grid.setInitWidths("40,300");
        pop_grid.setColAlign("center,center");
        pop_grid.setColSorting('int,date');
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


        //populate validity periods
        for (var i = 0; i < grid_period.getRowsNum(); i++) {

            var _rwid = grid_period.getRowId(i);

            var valid_from = utils_formatDate(grid_period.cells(_rwid, grid_period.getColIndexById("valid_from")).getValue(), "DD-MM-YYYY");
            var valid_to = utils_formatDate(grid_period.cells(_rwid, grid_period.getColIndexById("valid_to")).getValue(), "DD-MM-YYYY");

            var _id = valid_from + " - " + valid_to;
            var _cell = valid_from + " - " + valid_to;
            pop_grid.addRow(_id, [0, _cell]);
        }


        var selecteddates = utils_trim(grid.cells(rwid, grid.getColIndexById(date_colid)).getValue(), " ");
        var arr_dates = selecteddates.split("<br>");
        for (var i = 0; i < arr_dates.length; i++)
        {

            var dt = arr_dates[i];
            if (dt != "" && pop_grid.getRowIndex(dt) != "-1")
            {
                pop_grid.cells(dt, pop_grid.getColIndexById("X")).setValue(1);
            }
        }
        pop_grid.sortRows(1, "int", "asc");
        pop_grid.sortRows(0, "int", "des");

        popupwin.show();
        popupwin.center();
        popupwin.setText(title);
        popupwin_spo.setModal(false);
        popupwin.setModal(true);
    }



    function validateFlatRateCurrency()
    {
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();
        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");


        if (costprice_currencyid == "" || !costprice_currencyid || costprice_currencyid == "-1")
        {
            dhtmlx.alert({
                text: "Missing <b>Cost Price</b> Currency",
                type: "alert-warning",
                title: "Validate Currency",
                callback: function () {
                }
            });


            return false;
        }

        if (selected_currency_buy_ids == "")
        {
            dhtmlx.alert({
                text: "Missing <b>Buying</b> Currency",
                type: "alert-warning",
                title: "Validate Currency",
                callback: function () {
                }
            });

            return false;

        }


        if (selected_currency_sell_ids == "")
        {

            dhtmlx.alert({
                text: "Missing <b>Selling</b> Currency",
                type: "alert-warning",
                title: "Validate Currency",
                callback: function () {
                }
            });

            return false;
        }

        return true;
    }


    function validateFlatRateCurrencyMapping()
    {

        //make sure that each currency sell has a currency buy mapped to it

        for (var i = 0; i < grid_flat_rate_currencymap.getRowsNum(); i++) {
            var rwid = grid_flat_rate_currencymap.getRowId(i);
            grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("action")).setValue("");

            var itemCurrency = _dsCurrencies.item(grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_id_sell")).getValue());
            var currencysell_code = itemCurrency.value;

            var currencybuyid = grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_buy")).getValue();
            if (currencybuyid == "")
            {

                dhtmlx.alert({
                    text: "Missing Mapping Currency Buy for <b>" + currencysell_code + "</b>",
                    type: "alert-warning",
                    title: "Validate Currency Mapping",
                    callback: function () {
                        grid_flat_rate_currencymap.selectRowById(rwid, false, true, false);
                    }
                });

                return false;

            } else
            {
                //make sure that currency buy is in the list of selected currency buys
                var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
                var arr_selected_buyids = selected_currency_buy_ids.split(",");
                if (arr_selected_buyids.indexOf(currencybuyid) == -1)
                {

                    dhtmlx.alert({
                        text: "Missing Mapping Currency Buy for <b>" + currencysell_code + "</b>",
                        type: "alert-warning",
                        title: "Validate Currency Mapping",
                        callback: function () {
                            grid_flat_rate_currencymap.selectRowById(rwid, false, true, false);
                        }
                    });

                    return false;

                }
            }
        }


        return true;
    }


    function updateFlatRateExchangeRatesJson()
    {

        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();
        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");

        if (costprice_currencyid == "" || !costprice_currencyid || costprice_currencyid == "-1")
        {
            return;
        }

        //================= exchange_rates first ===============================

        for (var i = 0; i < grid_flat_rate_exchrates.getRowsNum(); i++) {
            var rwid = grid_flat_rate_exchrates.getRowId(i);
            grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("action")).setValue("");
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
                    //check if combii costprice_currencyid --> currencyid exists in grid
                    //if not, then insert into grid

                    var item = _dsCurrencies.item(costprice_currencyid);
                    var currenyfrom = item.value;

                    item = _dsCurrencies.item(currencyid);
                    var currenyto = item.value;


                    var rwid = getFlatRateCurrencyExgRateFromGrid(costprice_currencyid, currencyid);
                    if (!rwid)
                    {
                        _flat_rate_exchange_rate_id--;

                        grid_flat_rate_exchrates.addRow(_flat_rate_exchange_rate_id, [currenyfrom, currenyto, "0.000", "UPDATE", costprice_currencyid, currencyid]);
                        grid_flat_rate_exchrates.setRowTextStyle(_flat_rate_exchange_rate_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    } else
                    {
                        grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("action")).setValue("UPDATE");
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
                    var item = _dsCurrencies.item(costprice_currencyid);
                    var currenyfrom = item.value;

                    item = _dsCurrencies.item(currencyid);
                    var currenyto = item.value;


                    //check if combii costprice_currencyid->currencyid exists
                    //if not, then insert
                    var rwid = getFlatRateCurrencyExgRateFromGrid(costprice_currencyid, currencyid);
                    if (!rwid)
                    {
                        _flat_rate_exchange_rate_id--;
                        grid_flat_rate_exchrates.addRow(_flat_rate_exchange_rate_id, [currenyfrom, currenyto, "0.000", "UPDATE", costprice_currencyid, currencyid]);
                        grid_flat_rate_exchrates.setRowTextStyle(_flat_rate_exchange_rate_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    } else
                    {
                        grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("action")).setValue("UPDATE");
                    }
                }
            }
        }

        //=========
        //delete the rest
        var arr_delete = [];
        for (var i = 0; i < grid_flat_rate_exchrates.getRowsNum(); i++) {
            var rwid = grid_flat_rate_exchrates.getRowId(i);
            if (grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("action")).getValue() == "")
            {
                arr_delete.push(rwid);
            }
        }


        for (var i = 0; i < arr_delete.length; i++)
        {
            var rwid = arr_delete[i];
            grid_flat_rate_exchrates.deleteRow(rwid);
        }

        //======================================================================
        //================= currency_mapping after =============================

        for (var i = 0; i < grid_flat_rate_currencymap.getRowsNum(); i++) {
            var rwid = grid_flat_rate_currencymap.getRowId(i);
            grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("action")).setValue("");
        }



        var cbo = grid_flat_rate_currencymap.getColumnCombo(grid_flat_rate_currencymap.getColIndexById("currency_buy"));
        cbo.clearAll();
        cbo.readonly(true);

        for (var i = 0; i < arr_selected_buyids.length; i++)
        {
            var currbuyid = arr_selected_buyids[i];
            if (currbuyid != "")
            {
                var item = _dsCurrencies.item(currbuyid);
                var currenybuy_code = item.value;
                cbo.addOption([{value: currbuyid, text: currenybuy_code}]);
            }
        }


        //currency_sell,currency_buy,action,currency_id_sell

        for (var i = 0; i < arr_selected_sellids.length; i++)
        {
            if (arr_selected_sellids[i] != "")
            {
                currencyid = arr_selected_sellids[i];

                //check if currency sell exists in currency_mapping
                //if not, then insert

                var item = _dsCurrencies.item(currencyid);
                var currenysell_code = item.value;

                var rwid = getFlatRateCurrencyMappingFromGrid(currencyid);
                if (!rwid)
                {
                    _flat_rate_currency_map_id--;
                    grid_flat_rate_currencymap.addRow(_flat_rate_currency_map_id, [currenysell_code, "", "UPDATE", currencyid]);
                    grid_flat_rate_currencymap.setRowTextStyle(_flat_rate_currency_map_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                } else
                {
                    //check if the currency buy to which it is mapped is in array arr_selected_buyids

                    grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("action")).setValue("UPDATE");
                    if (arr_selected_buyids.indexOf(grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_buy")).getValue()) == -1)
                    {
                        //currency buy has been removed from the initial list                        
                        grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_buy")).setValue("")//so clear it
                    }
                }
            }
        }

        //delete the rest
        arr_delete = [];
        for (var i = 0; i < grid_flat_rate_currencymap.getRowsNum(); i++) {
            var rwid = grid_flat_rate_currencymap.getRowId(i);
            if (grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("action")).getValue() == "")
            {
                arr_delete.push(rwid);
            }
        }


        for (var i = 0; i < arr_delete.length; i++)
        {
            var rwid = arr_delete[i];
            grid_flat_rate_currencymap.deleteRow(rwid);
        }



        return;
    }


    function getFlatRateCurrencyExgRateFromGrid(currency_from, currency_to)
    {
        for (var i = 0; i < grid_flat_rate_exchrates.getRowsNum(); i++) {
            var rwid = grid_flat_rate_exchrates.getRowId(i);
            var curfrom = grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("currency_id_from")).getValue();
            var curto = grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("currency_id_to")).getValue();
            if (curfrom == currency_from && currency_to == curto)
            {
                return rwid;
            }
        }

        return null;

    }

    function getFlatRateCurrencyMappingFromGrid(currencysell)
    {
        for (var i = 0; i < grid_flat_rate_currencymap.getRowsNum(); i++) {
            var rwid = grid_flat_rate_currencymap.getRowId(i);
            var currency_sell = grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_id_sell")).getValue();
            if (currency_sell == currencysell)
            {
                return rwid;
            }
        }

        return null;

    }



    function populateFlatRateTaxCommi()
    {
        grid_flat_rate_taxcomm_buy.clearAll();
        grid_flat_rate_taxcomm_sell.clearAll();

        toolbar_flat_rate_taxcommi_buy.showListOption("opts", "additem");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "deleteitem");

        toolbar_flat_rate_taxcommi_sell.showListOption("opts", "additem");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "deleteitem");

        loadInitialRowsTaxCommi();
        loadFlatRateGridTaxCommXML({
            selectrowid_buy: "",
            selectrowid_sell: ""
        });
    }

    function loadInitialRowsTaxCommi()
    {
        //if settings are blank, then place default rows
        var buying_settings = _flat_rate_tax_commi_obj.buying_settings;
        var selling_settings = _flat_rate_tax_commi_obj.selling_settings;

        if (buying_settings.length == 0)
        {
            //add item cost price
            var cp_item = lookupFlatRateTaxCommiItem("CP");
            if (cp_item)
            {
                insertFlatRateTaxCommiJsonNode("buying_settings",
                        "BUYING",
                        cp_item.id,
                        cp_item.item_name,
                        cp_item.abbrv,
                        cp_item.code,
                        cp_item.core_addon,
                        "", "", "ROUNDUP");
            }


            //add item markup
            var markup_item = lookupFlatRateTaxCommiItem("MKUP");
            if (markup_item)
            {
                insertFlatRateTaxCommiJsonNode("buying_settings",
                        "BUYING",
                        markup_item.id,
                        markup_item.item_name,
                        markup_item.abbrv,
                        markup_item.code,
                        markup_item.core_addon,
                        "", "A", "ROUNDUP");
            }


            //add item transitional row
            var transrow_item = lookupFlatRateTaxCommiItem("TRSRW");
            if (transrow_item)
            {
                insertFlatRateTaxCommiJsonNode("buying_settings",
                        "BUYING",
                        transrow_item.id,
                        transrow_item.item_name,
                        transrow_item.abbrv,
                        transrow_item.code,
                        transrow_item.core_addon,
                        "", "A + B", "ROUNDUP");
            }


            //========================================

            resetFlatRateTaxCommiRowIndex();
        }

        //===================================================
        //===================================================

        if (selling_settings.length == 0)
        {
            //add item converted sellprice
            //add item final sellprice

            var convertedsp_item = lookupFlatRateTaxCommiItem("CVSP");
            if (convertedsp_item)
            {
                insertFlatRateTaxCommiJsonNode("selling_settings",
                        "SELLING",
                        convertedsp_item.id,
                        convertedsp_item.item_name,
                        convertedsp_item.abbrv,
                        convertedsp_item.code,
                        convertedsp_item.core_addon,
                        "", "C", "ROUNDUP");
            }


            //add item commission
            var commi_item = lookupFlatRateTaxCommiItem("COMMI");
            if (commi_item)
            {
                insertFlatRateTaxCommiJsonNode("selling_settings",
                        "SELLING",
                        commi_item.id,
                        commi_item.item_name,
                        commi_item.abbrv,
                        commi_item.code,
                        commi_item.core_addon,
                        "", "D", "NOROUND");
            }


            var finalsp_item = lookupFlatRateTaxCommiItem("FINALSP");
            if (finalsp_item)
            {
                insertFlatRateTaxCommiJsonNode("selling_settings",
                        "SELLING",
                        finalsp_item.id,
                        finalsp_item.item_name,
                        finalsp_item.abbrv,
                        finalsp_item.code,
                        finalsp_item.core_addon,
                        "", "D + E", "ROUNDUP");
            }

            resetFlatRateTaxCommiRowIndex();
        }
    }

    function insertFlatRateTaxCommiJsonNode(arrbuysell,
            setting_buying_selling,
            setting_item_fk,
            setting_item_name,
            setting_item_abbrv,
            setting_item_code,
            setting_core_addon,
            setting_basis,
            setting_applyon_formula, setting_rounding)
    {

        var arr = _flat_rate_tax_commi_obj[arrbuysell];

        _flat_rate_taxcommi_settings_id--;

        var obj = {
            setting_rwid: _flat_rate_taxcommi_settings_id,
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


    function lookupFlatRateTaxCommiItem(itemcode)
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

    function resetFlatRateTaxCommiRowIndex()
    {
        var idx = 0;

        var buying_settings = _flat_rate_tax_commi_obj.buying_settings;
        var selling_settings = _flat_rate_tax_commi_obj.selling_settings;

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


    function lookupFlatRateTaxCommiItem(itemcode)
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

    function loadFlatRateGridTaxCommXML(obj_select)
    {

        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "deleteitem");

        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "deleteitem");


        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");


        //get current taxcomm data from json object

        var arr_buying_settings = [];
        var arr_selling_settings = [];
        arr_buying_settings = utils_deepCopy(_flat_rate_tax_commi_obj.buying_settings);
        arr_selling_settings = utils_deepCopy(_flat_rate_tax_commi_obj.selling_settings);

        grid_flat_rate_taxcomm_buy.clearAll(true);
        grid_flat_rate_taxcomm_buy = null;
        grid_flat_rate_taxcomm_buy = accord_flat_rate_taxcommi.cells("buying").attachGrid();
        grid_flat_rate_taxcomm_buy.setIconsPath('libraries/dhtmlx/imgs/');
        grid_flat_rate_taxcomm_buy.enableAlterCss("", "");
        grid_flat_rate_taxcomm_buy.enableEditTabOnly(true);
        grid_flat_rate_taxcomm_buy.enableEditEvents(true, true, true);
        grid_flat_rate_taxcomm_buy.attachEvent("onRowSelect", onGridTaxCommBuySelect);
        grid_flat_rate_taxcomm_buy.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
            return onGridFlatRateTaxCommBuySellEdit("BUYING", stage, rId, cInd, nValue, oValue);
        });


        grid_flat_rate_taxcomm_sell.clearAll(true);
        grid_flat_rate_taxcomm_sell = null;
        grid_flat_rate_taxcomm_sell = accord_flat_rate_taxcommi.cells("selling").attachGrid();
        grid_flat_rate_taxcomm_sell.setIconsPath('libraries/dhtmlx/imgs/');
        grid_flat_rate_taxcomm_sell.enableAlterCss("", "");
        grid_flat_rate_taxcomm_sell.enableEditTabOnly(true);
        grid_flat_rate_taxcomm_sell.enableEditEvents(true, true, true);
        grid_flat_rate_taxcomm_sell.attachEvent("onRowSelect", onGridTaxCommSellSelect);
        grid_flat_rate_taxcomm_sell.attachEvent("onEditCell", function (stage, rId, cInd, nValue, oValue) {
            return onGridFlatRateTaxCommBuySellEdit("SELLING", stage, rId, cInd, nValue, oValue);
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
        grid_flat_rate_taxcomm_buy.loadXML(url, function () {


            if (grid_flat_rate_taxcomm_buy.getRowsNum() > 0)
            {
                if (selectrowid_buy == "")
                {
                    //select first record
                    grid_flat_rate_taxcomm_buy.selectRowById(grid_flat_rate_taxcomm_buy.getRowId(0), false, true, true);
                } else
                {
                    //select specific record
                    grid_flat_rate_taxcomm_buy.selectRowById(selectrowid_buy, false, true, true);
                }
            }
        });


        //=============

        var url = "php/api/hotelcontracts/grid_taxcomm_sell_xml.php?t=" +
                encodeURIComponent(global_token) +
                "&arr_settings_data=" + encodeURIComponent(JSON.stringify(arr_selling_settings)) +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids;
        grid_flat_rate_taxcomm_sell.loadXML(url, function () {
            if (grid_flat_rate_taxcomm_sell.getRowsNum() > 0)
            {
                if (selectrowid_sell == "")
                {
                    //select first record
                    grid_flat_rate_taxcomm_sell.selectRowById(grid_flat_rate_taxcomm_sell.getRowId(0), false, true, true);
                } else
                {
                    grid_flat_rate_taxcomm_sell.selectRowById(selectrowid_sell, false, true, true);
                }
            }
        });
    }


    function onGridTaxCommBuySelect(rid, cid)
    {
        var rwindex = grid_flat_rate_taxcomm_buy.getRowIndex(rid);
        var rwcount = grid_flat_rate_taxcomm_buy.getRowsNum();

        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_buy.hideListOption("opts", "deleteitem");

        var rwobj = lookupFlatRateTaxCommiBuySellItem("buying_settings", rid);

        if (!rwobj)
        {
            return;
        }


        if (rwobj.setting_core_addon == "ADDON")
        {
            toolbar_flat_rate_taxcommi_buy.showListOption("opts", "deleteitem");

            if (rwindex < rwcount - 1) //still room to move down
            {
                toolbar_flat_rate_taxcommi_buy.showListOption("opts", "movedown");
            }

            if (rwindex - 1 > 0) //an addon row cannot be at the top
            {
                toolbar_flat_rate_taxcommi_buy.showListOption("opts", "moveup");
            }
        } else if (rwobj.setting_core_addon == "CORE")
        {
            if (rwobj.setting_item_code != "CP")
            {
                //cannot delete Cost Price
                toolbar_flat_rate_taxcommi_buy.showListOption("opts", "deleteitem");
            }

            if (rwindex != 0) //cannot delete first core row
            {
                toolbar_flat_rate_taxcommi_buy.showListOption("opts", "moveup");
            }
            if (rwindex < rwcount - 1)
            {
                toolbar_flat_rate_taxcommi_buy.showListOption("opts", "movedown");
            }
        }
    }

    function onGridTaxCommSellSelect(rid, cid)
    {
        var rwindex = grid_flat_rate_taxcomm_sell.getRowIndex(rid);
        var rwcount = grid_flat_rate_taxcomm_sell.getRowsNum();

        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "moveup");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "movedown");
        toolbar_flat_rate_taxcommi_sell.hideListOption("opts", "deleteitem");

        var rwobj = lookupFlatRateTaxCommiBuySellItem("selling_settings", rid);

        if (!rwobj)
        {
            return;
        }


        if (rwobj.setting_core_addon == "ADDON")
        {
            toolbar_flat_rate_taxcommi_sell.showListOption("opts", "deleteitem");

            if (rwindex < rwcount - 2) //still room to move down but not to last row
            {
                toolbar_flat_rate_taxcommi_sell.showListOption("opts", "movedown");
            }

            if (rwindex - 1 > 0) //an addon row cannot be at the top
            {
                toolbar_flat_rate_taxcommi_sell.showListOption("opts", "moveup");
            }
        } else if (rwobj.setting_core_addon == "CORE")
        {
            if (rwobj.setting_item_code != "CVSP" && rwobj.setting_item_code != "FINALSP")
            {
                toolbar_flat_rate_taxcommi_sell.showListOption("opts", "deleteitem");

                //cannot come to the first row
                if (rwindex - 1 > 0) //an addon row cannot be at the top
                {
                    toolbar_flat_rate_taxcommi_sell.showListOption("opts", "moveup");
                }

                //cannot come to the last row
                if (rwindex < rwcount - 2) //still room to move down but not to last row
                {
                    toolbar_flat_rate_taxcommi_sell.showListOption("opts", "movedown");
                }
            }
        }
    }

    function lookupFlatRateTaxCommiBuySellItem(buysel, itemrwid)
    {
        var arr = _flat_rate_tax_commi_obj[buysel];
        for (var i = 0; i < arr.length; i++)
        {
            if (arr[i].setting_rwid == itemrwid)
            {
                return arr[i];
            }
        }


        return null;
    }

    function onGridFlatRateTaxCommBuySellEdit(buying_selling, stage, rId, cInd, nValue, oValue)
    {
        var grid = null;
        var buy_sell = "";

        if (buying_selling == "BUYING")
        {
            grid = grid_flat_rate_taxcomm_buy;
            buy_sell = "buying_settings";
        } else {
            grid = grid_flat_rate_taxcomm_sell;
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

                return updateFlatRateJsonTaxCommi(nValue, context, currencyid, rId, buy_sell);
            }
        }

        return true;

    }

    function updateFlatRateJsonTaxCommi(nValue, context, currencyid, itemrwid, buysel)
    {
        var itemobj = lookupFlatRateTaxCommiBuySellItem(buysel, itemrwid);
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
            _flat_rate_taxcommi_settings_value_id--;
            var obj = {
                value_rwid: _flat_rate_taxcommi_settings_value_id,
                value_currency_fk: currencyid,
                value_value: nValue,
                value_currency_code: currencycode,
                value_action: "INSERT"};

            arrvalues.push(obj);
        }

        return true;
    }

    function showFlatRateTaxCommItems(buying_selling)
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
                            insertFlatRateTaxCommiJsonNode("buying_settings",
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

                            var arr_temp = [];
                            var arrsell_dispensable = utils_deepCopy(_flat_rate_tax_commi_obj.selling_settings);

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
                            _flat_rate_tax_commi_obj.selling_settings = arr_temp;

                            insertFlatRateTaxCommiJsonNode("selling_settings",
                                    "SELLING",
                                    item.id,
                                    item.item_name,
                                    item.abbrv,
                                    item.code,
                                    item.core_addon,
                                    "", "", "ROUNDUP");


                            //now insert the last item: finalsp   
                            var finalsp_obj = arrsell_dispensable[arrsell_dispensable.length - 1];
                            _flat_rate_tax_commi_obj.selling_settings.push(finalsp_obj);
                        }
                    }

                    //apply default values in case commi or markup selected
                    resetFlatRateTaxCommiRowIndex();

                    loadFlatRateGridTaxCommXML({
                        selectrowid_buy: "",
                        selectrowid_sell: ""
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
        popupwin_spo.setModal(false);
        popupwin.setModal(true);

    }


    function deleteFlatRateTaxCommiItem(grid_caller, buying_selling)
    {
        var itemrwid = grid_caller.getSelectedRowId();

        var rwobj = null;

        if (buying_selling == "BUYING")
        {
            rwobj = lookupFlatRateTaxCommiBuySellItem("buying_settings", itemrwid);
        } else if (buying_selling == "SELLING")
        {
            rwobj = lookupFlatRateTaxCommiBuySellItem("selling_settings", itemrwid);
        }

        if (!rwobj)
        {
            alert("Item not found!");
            return;
        }

        rwobj.setting_action = "DELETE";

        resetFlatRateTaxCommiRowIndex();

        loadFlatRateGridTaxCommXML({
            selectrowid_buy: "",
            selectrowid_sell: ""
        });

        return;
    }


    function moveFlatRateTaxCommiItem(grid, buysel, up_down)
    {
        var rid = grid.getSelectedRowId();

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
        for (var i = 0; i < grid_flat_rate_taxcomm_buy.getRowsNum(); i++)
        {
            var obj = lookupFlatRateTaxCommiBuySellItem("buying_settings", grid_flat_rate_taxcomm_buy.getRowId(i));
            if (obj)
            {
                obj.setting_row_index = idx;
            }
            idx++;
        }

        for (var i = 0; i < grid_flat_rate_taxcomm_sell.getRowsNum(); i++)
        {
            var obj = lookupFlatRateTaxCommiBuySellItem("selling_settings", grid_flat_rate_taxcomm_sell.getRowId(i));
            if (obj)
            {
                obj.setting_row_index = idx;
            }
            idx++;
        }
        //=============================================================


        //sort the array of buy or sell as per setting_row_index
        var arr = _flat_rate_tax_commi_obj[buysel];
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
        loadFlatRateGridTaxCommXML({
            selectrowid_buy: selectrowid_buy,
            selectrowid_sell: selectrowid_sell
        });

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


    function testTaxCommiSettings()
    {

        if (!validateFlatRateTaxCommiSetting())
        {
            return;
        }

        popupwin_spo.setModal(false);
        popupwin_flat_rate_testtaxcomm.show();
        popupwin_flat_rate_testtaxcomm.center();
        popupwin_flat_rate_testtaxcomm.setModal(true);

        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");

        var arr_buying = _flat_rate_tax_commi_obj.buying_settings;
        var arr_selling = _flat_rate_tax_commi_obj.selling_settings;

        var arr_currency_buy = selected_currency_buy_ids.split(",");
        var arr_currency_sell = selected_currency_sell_ids.split(",");

        var arr_currency_obj = intersectCurrencyArrays(arr_currency_buy, arr_currency_sell);
        arr_currency_buy = arr_currency_obj.buy;
        arr_currency_sell = arr_currency_obj.sell;



        grid_flat_rate_test_taxcomm.clearAll();
        grid_flat_rate_test_taxcomm = null;

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

        grid_flat_rate_test_taxcomm = test_taxcomm_layout.cells("a").attachGrid();
        grid_flat_rate_test_taxcomm.setIconsPath('libraries/dhtmlx/imgs/');
        grid_flat_rate_test_taxcomm.setHeader(header_str);
        grid_flat_rate_test_taxcomm.setColumnIds(col_ids);
        grid_flat_rate_test_taxcomm.setColTypes(col_types);
        grid_flat_rate_test_taxcomm.setInitWidths(init_widths);
        grid_flat_rate_test_taxcomm.setColAlign(col_align);
        grid_flat_rate_test_taxcomm.setColSorting(col_sorting);
        grid_flat_rate_test_taxcomm.enableAlterCss("", "");
        grid_flat_rate_test_taxcomm.enableColSpan(true);
        grid_flat_rate_test_taxcomm.enableRowspan(true);
        grid_flat_rate_test_taxcomm.attachEvent("onEditCell", onGridFlatRateTestTaxCommEdit);
        grid_flat_rate_test_taxcomm.enableEditTabOnly(true);
        grid_flat_rate_test_taxcomm.enableEditEvents(true, true, true);
        grid_flat_rate_test_taxcomm.init();

        var arr_title = ["Buying Settings"];
        for (var j = 0; j < arr_currency_buy.length; j++)
        {
            var itemCurrency = _dsCurrencies.item(arr_currency_buy[j]);
            var currencycode = itemCurrency.value;
            arr_title.push(currencycode);
        }

        grid_flat_rate_test_taxcomm.addRow("buy_title", arr_title);
        grid_flat_rate_test_taxcomm.setRowTextStyle("buy_title", "background-color:#D2F6FB;font-weight:bold;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

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
                    grid_flat_rate_test_taxcomm.addRow(rwid, [item.setting_item_name]);
                    grid_flat_rate_test_taxcomm.setRowTextStyle(rwid, "color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
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
                    grid_flat_rate_test_taxcomm.addRow(rwid, arr_vals);
                    grid_flat_rate_test_taxcomm.setRowTextStyle(rwid, "color:green;border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                    var rwid_2 = "buy_" + item.setting_item_code + "_" + rwidx + "_r2";
                    grid_flat_rate_test_taxcomm.addRow(rwid_2, [""]);
                    grid_flat_rate_test_taxcomm.setRowTextStyle(rwid_2, "color:green;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                    grid_flat_rate_test_taxcomm.setRowspan(rwid, 0, 2);
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
        grid_flat_rate_test_taxcomm.addRow("sell_title", arr_title);
        grid_flat_rate_test_taxcomm.setRowTextStyle("sell_title", "background-color:#D2F6FB;font-weight:bold;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:2px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        for (var i = 0; i < arr_selling.length; i++)
        {
            var item = arr_selling[i];
            if (item.setting_action != "DELETE")
            {
                if (item.setting_core_addon == "CORE")
                {
                    var rwid = "sel_" + item.setting_item_code + "_" + rwidx;
                    grid_flat_rate_test_taxcomm.addRow(rwid, [item.setting_item_name]);

                    if (item.setting_item_code == "FINALSP")
                    {
                        grid_flat_rate_test_taxcomm.setRowTextStyle(rwid, "background-color:#FBF9D2; font-weight:bold; color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
                    } else
                    {
                        grid_flat_rate_test_taxcomm.setRowTextStyle(rwid, "color:blue;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");
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
                    grid_flat_rate_test_taxcomm.addRow(rwid, arr_vals);
                    grid_flat_rate_test_taxcomm.setRowTextStyle(rwid, "color:green;border-left:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                    var rwid_2 = "sel_" + item.setting_item_code + "_" + rwidx + "_r2";
                    grid_flat_rate_test_taxcomm.addRow(rwid_2, []);
                    grid_flat_rate_test_taxcomm.setRowTextStyle(rwid_2, "color:green;border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");


                    grid_flat_rate_test_taxcomm.setRowspan(rwid, 0, 2);
                }

                rwidx++;
            }
        }

        return;
    }

    function validateFlatRateTaxCommiSetting()
    {
        //rules buying:
        //first item must be core
        //only forumula of first item can be blank

        //selling rules
        //formula cannot be blank

        var addon_basis = "";
        var first = true;
        for (var i = 0; i < _flat_rate_tax_commi_obj.buying_settings.length; i++)
        {
            var item = _flat_rate_tax_commi_obj.buying_settings[i];
            if (item.setting_action != "DELETE")
            {
                var coreaddon = item.setting_core_addon;
                if (coreaddon != "CORE" && first)
                {
                    dhtmlx.alert({
                        text: "Initial Item in Buying Setting should be a <b>CORE</b> item!",
                        type: "alert-warning",
                        title: "Validate Tax Commission",
                        callback: function () {
                            accord_flat_rate_taxcommi.cells("buying").open();
                            accord_flat_rate_taxcommi.cells("selling").close();
                        }
                    });
                    return false;
                }

                if (!first)
                {
                    //all non first rows cannot have blank formula
                    var formula = utils_trim(item.setting_applyon_formula, " ");
                    if (formula == "")
                    {
                        dhtmlx.alert({
                            text: "<b>Buying Setting</b>: Formula cell cannot be Blank for Non-Initial Items!",
                            type: "alert-warning",
                            title: "Validate Tax Commission",
                            callback: function () {
                                accord_flat_rate_taxcommi.cells("buying").open();
                                accord_flat_rate_taxcommi.cells("selling").close();
                            }
                        });
                        return false;
                    }
                }

                if (coreaddon == "ADDON")
                {
                    //check basis PPPN or PN
                    var _basis = utils_trim(item.setting_basis, " ");
                    if (_basis == "")
                    {
                        dhtmlx.alert({
                            text: "<b>Buying Setting</b>: Basis cannot be Blank for Add-On Items!",
                            type: "alert-warning",
                            title: "Validate Tax Commission",
                            callback: function () {
                                accord_flat_rate_taxcommi.cells("buying").open();
                                accord_flat_rate_taxcommi.cells("selling").close();
                            }
                        });
                        return false;
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

                        dhtmlx.alert({
                            text: "<b>Buying Setting</b>: <b>Buying Setting</b>: Basis cannot be both <b>PPPN</b> and <b>PNI</b> in the same Room!",
                            type: "alert-warning",
                            title: "Validate Tax Commission",
                            callback: function () {
                                accord_flat_rate_taxcommi.cells("buying").open();
                                accord_flat_rate_taxcommi.cells("selling").close();
                            }
                        });
                        return false;
                    }

                    //===================================
                    if (item.setting_item_code == "COMMI" ||
                            item.setting_item_code == "CCCOMMI" ||
                            item.setting_item_code == "SREPCOMMI")
                    {
                        //cannot allow flat values for commission
                        if (item.setting_basis.includes("FLAT"))
                        {
                            dhtmlx.alert({
                                text: "<b>Buying Setting</b>: <b>Buying Setting</b>: Commission can only be % and not be FLAT",
                                type: "alert-warning",
                                title: "Validate Tax Commission",
                                callback: function () {
                                    accord_flat_rate_taxcommi.cells("buying").open();
                                    accord_flat_rate_taxcommi.cells("selling").close();
                                }
                            });
                            return false;
                        }
                    }
                    //===================================

                }

                first = false;
            }
        }


        //=========================================
        for (var i = 0; i < _flat_rate_tax_commi_obj.selling_settings.length; i++)
        {
            var item = _flat_rate_tax_commi_obj.selling_settings[i];
            if (item.setting_action != "DELETE")
            {
                var coreaddon = item.setting_core_addon;
                var formula = utils_trim(item.setting_applyon_formula, " ");
                if (formula == "")
                {
                    dhtmlx.alert({
                        text: "<b>Selling Setting</b>: Formula cell cannot be Blank for Selling Items!",
                        type: "alert-warning",
                        title: "Validate Tax Commission",
                        callback: function () {
                            accord_flat_rate_taxcommi.cells("selling").open();
                            accord_flat_rate_taxcommi.cells("buying").close();
                        }
                    });
                    return false;
                }


                if (coreaddon == "ADDON")
                {
                    //check basis PPPN or PN
                    var _basis = utils_trim(item.setting_basis, " ");
                    if (_basis == "")
                    {
                        dhtmlx.alert({
                            text: "<b>Selling Setting</b>: Basis cannot be Blank for Add-On Items!",
                            type: "alert-warning",
                            title: "Validate Tax Commission",
                            callback: function () {
                                accord_flat_rate_taxcommi.cells("selling").open();
                                accord_flat_rate_taxcommi.cells("buying").close();
                            }
                        });
                        return false;
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
                        dhtmlx.alert({
                            text: "<b>Selling Setting</b>: Basis cannot be both <b>PPPN</b> and <b>PNI</b> in the same Room!",
                            type: "alert-warning",
                            title: "Validate Tax Commission",
                            callback: function () {
                                accord_flat_rate_taxcommi.cells("selling").open();
                                accord_flat_rate_taxcommi.cells("buying").close();
                            }
                        });
                        return false;
                    }
                    //===================================

                    if (item.setting_item_code == "COMMI" ||
                            item.setting_item_code == "CCCOMMI" ||
                            item.setting_item_code == "SREPCOMMI")
                    {
                        //cannot allow flat values for commission
                        if (item.setting_basis.includes("FLAT"))
                        {
                            dhtmlx.alert({
                                text: "<b>Selling Setting</b>: Commission can only be % and not be FLAT!",
                                type: "alert-warning",
                                title: "Validate Tax Commission",
                                callback: function () {
                                    accord_flat_rate_taxcommi.cells("selling").open();
                                    accord_flat_rate_taxcommi.cells("buying").close();
                                }
                            });
                            return false;
                        }
                    }

                }
            }
        }

        return true;
    }


    function onGridFlatRateTestTaxCommEdit(stage, rId, cInd, nValue, oValue)
    {
        if (stage == 0)
        {
            if (grid_flat_rate_test_taxcomm.getRowIndex(rId) != 1)
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


                var currencycode = grid_flat_rate_test_taxcomm.cells("buy_title", cInd).getValue();
                var currencyid = "-1";
                for (var i = 0; i < _dsCurrencies.dataCount(); i++)
                {
                    var item = _dsCurrencies.item(_dsCurrencies.idByIndex(i));
                    if (item.value == currencycode)
                    {
                        currencyid = item.id;
                    }
                }

                var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
                var arr_currency_sell = selected_currency_sell_ids.split(",");
                for (var i = 0; i < arr_currency_sell.length; i++)
                {
                    var sell_currency_id = arr_currency_sell[i];
                    calculateFlatRateSellingPrice(nValue, currencyid, sell_currency_id, placeTestGridValues, [rId, cInd, sell_currency_id]);
                }
            }
        }

        return true;
    }


    function placeTestGridValues(arr_arguements)
    {
        var colidx = arr_arguements[1];
        var sellcurrencyid = arr_arguements[2];
        var obj_calc = arr_arguements[3];

        //get the colindex for the currency
        var colidx = 0;
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
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


            grid_flat_rate_test_taxcomm.cells(rwid, colidx).setValue(value);
        }
    }

    function calculateFlatRateSellingPrice(value_input, currencyid_input, currencyid_sell, callback, arr_arguements)
    {
        var arr_buy = _flat_rate_tax_commi_obj.buying_settings;
        var arr_sell = _flat_rate_tax_commi_obj.selling_settings;

        var _json_exchangerates = configure_flat_rates_json_exchange_rates();

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


    function configure_flat_rates_json_exchange_rates()
    {
        var exchange_rates = {currency_mapping: [], exchange_rates: []};

        for (var i = 0; i < grid_flat_rate_currencymap.getRowsNum(); i++) {
            var rwid = grid_flat_rate_currencymap.getRowId(i);

            var obj = {mapping_action: "UPDATE",
                mapping_buy_currencyfk: grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_buy")).getValue(),
                mapping_id: rwid,
                mapping_sell_currencyfk: grid_flat_rate_currencymap.cells(rwid, grid_flat_rate_currencymap.getColIndexById("currency_id_sell")).getValue()};
            exchange_rates.currency_mapping.push(obj);
        }


        for (var i = 0; i < grid_flat_rate_exchrates.getRowsNum(); i++) {
            var rwid = grid_flat_rate_exchrates.getRowId(i);

            var obj = {rates_action: "UPDATE",
                rates_exchange_rate: grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("rates_exchange_rate")).getValue(),
                rates_from_currencyfk: grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("currency_id_from")).getValue(),
                rates_id: rwid,
                rates_to_currencyfk: grid_flat_rate_exchrates.cells(rwid, grid_flat_rate_exchrates.getColIndexById("currency_id_to")).getValue()};

            exchange_rates.exchange_rates.push(obj);
        }


        return exchange_rates;
    }


    function saveSPO_flatrates()
    {
        //validate
        if (!validate_room_capacity(""))
        {
            return;
        }

        flat_rate_cleanJsonCapacityFromRoomsAndAges();
        flat_rate_cleanJsonAdults();
        flat_rate_cleanJsonChildren("sharing");
        flat_rate_cleanJsonChildren("single");
        flat_rate_cleanJsonSingleParent();
        flat_rate_cleanJsonTaxCommi();

        saveSPO();

    }

    function flat_rate_cleanJsonTaxCommi()
    {
        var buying_settings = _flat_rate_tax_commi_obj.buying_settings;
        var selling_settings = _flat_rate_tax_commi_obj.selling_settings;


        flat_rate_cleanJsonTaxCommi_buy_sell(buying_settings);
        flat_rate_cleanJsonTaxCommi_buy_sell(selling_settings);

    }

    function flat_rate_cleanJsonTaxCommi_buy_sell(buy_sell_settings)
    {
        for (var i = 0; i < buy_sell_settings.length; i++)
        {
            var setting_values = buy_sell_settings[i].setting_values;
            for (var j = 0; j < setting_values.length; j++)
            {
                var selected_currency_buy_ids = utils_trim(form_flat_rate_currency.getItemValue("selected_currency_buy_ids"), " ");
                var value_currency_fk = setting_values[j].value_currency_fk;
                if (value_currency_fk != "" && value_currency_fk != selected_currency_buy_ids)
                {
                    setting_values[j].value_action = "DELETE";
                }
            }
        }
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


    function is_age_in_mix(child_age_from, child_age_to)
    {
        //returns true if age is in mix ranges
        var child_ages_ids = getChildrenAgeString()

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

        if (context == "date_capacity_rules")
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

    function onRoomChoicesSelect(rid, cid)
    {


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

                        //delete all room capacity rules for each date
                        var arrdates = _json_capacity[i].room_dates;
                        for (var j = 0; j < arrdates.length; j++)
                        {
                            if (arrdates[j].date_action != "DELETE")
                            {
                                var arr_date_capacity_rules = arrdates[j].date_capacity_rules;
                                for (var k = 0; k < arr_date_capacity_rules.length; k++)
                                {
                                    arr_date_capacity_rules[k].rule_action = "DELETE"
                                }
                            }
                        }

                        return;
                    }
                }
            }
        }
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
        grid_childpolicy_own_age = null;
        grid_childpolicy_own_age = accord_child.cells("single").attachGrid();
        grid_childpolicy_own_age.setIconsPath('libraries/dhtmlx/imgs/');
    }

    function initialiseAdultPolicyGrid()
    {
        grid_adultpolicy_age = adult_layout.cells("a").attachGrid();
        grid_adultpolicy_age.setIconsPath('libraries/dhtmlx/imgs/');
    }


    function populateRoomsTree()
    {
        var roomid_filter = comboRoomFilter.getSelectedValue();


        comboVariant.show(false);

        tree_roomdates = null;
        tree_roomdates = flat_rate_roomslayout.cells("a").attachTree();
        tree_roomdates.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_roomdates.setIconSize('20px', '20px');
        tree_roomdates.attachEvent("onSelect", onRoomTreeNodeSelect);
        tree_roomdates.attachEvent("onCheck", onRoomTreeNodeChecked);
        tree_roomdates.enableCheckBoxes(true, false);
        tree_roomdates.enableThreeStateCheckboxes(true);


        var arr_seasons = groupSeasons(); //get an array of seasons for that spo


        //fill in the tree:

        //get all the rooms selected in main tab
        var roomids = form_name.getItemValue("rooms_ids");
        var first_date_node_selected = "";

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

                updateRoomDatePeriods(room_id); //insert or update all date periods of the SPO for each room

                //refresh the room obj
                obj = lookupRoomObj(room_id);


                //append the room node to the tree
                appendTreeRoom(room_id, room_name, room_numbedrooms, obj.room_rwid, obj.room_variants)

                var arrdates = obj.room_dates;
                var arr_dates_ids_added = []; //will record a list of date ids recorded 


                for (var s = 0; s < arr_seasons.length; s++)
                {
                    var season = arr_seasons[s].season;
                    var seasonid = arr_seasons[s].seasonid;

                    //for that season, get all date nodes that fall within it
                    var arr_dateperiods = filterDatePeriodsBySeason(seasonid, arrdates);

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


                for (var r = 0; r < arrdates.length; r++)
                {
                    var rwid = arrdates[r].date_rwid;
                    if (!arr_dates_ids_added.includes(rwid))
                    {
                        arrdates[r].date_action = "DELETE";
                    }
                }

                //delete the other dates not used for that room
            }

        }

        //select the first date node
        tree_roomdates.selectItem(first_date_node_selected, true, false);
        grid_room_choices.selectRowById("capacity", false, true, true);
    }


    function updateRoomDatePeriods(room_id)
    {
        //get all the periods decided for that SPO
        //for each date, check if it is in the room.
        //if no, then add it

        var arr_needed_date_ids = [];

        for (var i = 0; i < grid_period.getRowsNum(); i++) {

            var rwid = grid_period.getRowId(i);

            var date_dtfrom = grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue();
            var date_dtto = grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue();

            var obj = lookupRoomDateObj(room_id, date_dtfrom, date_dtto);
            if (!obj)
            {
                //this date not been recorded for that room
                //push it for the room
                _capacity_room_date_id--;
                insertJsonDate(_capacity_room_date_id, room_id, date_dtfrom, date_dtto);
                arr_needed_date_ids.push(_capacity_room_date_id);
            } else
            {
                arr_needed_date_ids.push(obj.date_rwid);
            }
        }

        //for any room dates that are attached to the room but are not needed, then mark as deleted
        var room_obj = lookupRoomObj(room_id);
        var arr_dates = room_obj.room_dates;
        for (var i = 0; i < arr_dates.length; i++)
        {
            if (arr_dates[i].date_action != "DELETE")
            {
                var date_rwid = arr_dates[i].date_rwid;
                if (!arr_needed_date_ids.includes(date_rwid))
                {
                    arr_dates[i].date_action = "DELETE";
                }
            }
        }

        return;

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
                    date_capacity_rules: [],
                    date_adultpolicies_rules: [],
                    date_childpolicies_rules: [],
                    date_singleparentpolicies_rules: []};

                _json_capacity[i].room_dates.push(obj);
                return;
            }
        }
        return;
    }

    function decideNodeDateCaption(date_dtfrom, date_dtto)
    {


        var display_from = utils_formatDate(date_dtfrom, "DATE MON YY");
        var display_to = utils_formatDate(date_dtto, "DATE MON YY");

        var display_caption = display_from + " - " + display_to;

        return display_caption;
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



    function filterDatePeriodsBySeason(seasonid, arr_dateperiods)
    {
        var arr = [];

        //for each date in arr_dateperiods,
        //check if datefrom and dateto in grid_periods and season is same for that date
        //if yes, then push the date into the array

        for (var j = 0; j < arr_dateperiods.length; j++)
        {
            var date_dtfrom = arr_dateperiods[j].date_dtfrom;
            var date_dtto = arr_dateperiods[j].date_dtto;
            var date_action = arr_dateperiods[j].date_action;

            if (date_action != "DELETE")
            {
                //get the season of that date range from grid_periods
                var _seasonid = getSeasonFromDateValidityByDates(date_dtfrom, date_dtto);
                if (_seasonid == seasonid)
                {
                    arr.push(arr_dateperiods[j]);
                }
            }
        }

        return arr;
    }

    function getSeasonFromDateValidityByDates(date_dtfrom, date_dtto)
    {
        //get the season id from grid_period for that datefrom and dateto

        for (var i = 0; i < grid_period.getRowsNum(); i++) {

            var rwid = grid_period.getRowId(i);

            var _date_dtfrom = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("valid_from")).getValue(), " ");
            var _date_dtto = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("valid_to")).getValue(), " ");
            var season = utils_trim(grid_period.cells(rwid, grid_period.getColIndexById("season")).getValue(), " ");

            if (date_dtfrom == _date_dtfrom && date_dtto == _date_dtto)
            {
                return season;
            }
        }

        return null;
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

        //push CUSTOM season
        var obj = {season: "CUSTOM", seasonid: "", arr_season_dates: []};
        arr.push(obj)


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

    function lookupRoomDateObj(room_id, dtfrom, dtto)
    {
        //dtfrom and dtto in yyyy-mm-dd format

        for (var i = 0; i < _json_capacity.length; i++)
        {
            if (_json_capacity[i].room_id == room_id)
            {
                var arr_dates = _json_capacity[i].room_dates;
                for (var j = 0; j < arr_dates.length; j++)
                {
                    if (arr_dates[j].date_dtfrom == dtfrom &&
                            arr_dates[j].date_dtto == dtto &&
                            arr_dates[j].date_action != "DELETE")
                    {
                        return arr_dates[j];
                    }
                }
            }
        }

        return null; //this room date range has not been saved yet
    }

    function onRoomTreeNodeSelect(id)
    {
        var tabid = grid_room_choices.getSelectedRowId();
        toggleRoomViews(id, tabid);
    }

    function toggleRoomViews(nodeid, tabid)
    {
        comboVariant.show(false);

        toolbar_capacity_dates.hideItem("new");
        toolbar_capacity_dates.hideItem("modify");
        toolbar_capacity_dates.hideItem("delete");
        toolbar_capacity_rules.hideItem("new");


        toolbar_capacity_rules.hideItem("delete");
        grid_capacity_age.clearAll();

        grid_adultpolicy_age.clearAll();
        grid_childpolicy_sharing_age.clearAll();
        grid_childpolicy_own_age.clearAll();
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
        if (tabid == "capacity") {

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


                loadGridChildPolicySharing(roomid, date_rwid, dtfrom, dtto, variant, nodeid);
                if (variant == "PERSONS")
                {
                    //load single room only for PERSONS rooms
                    loadGridChildPolicyOwnRoom(roomid, date_rwid, dtfrom, dtto, variant, nodeid);
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


    function childrenSharingChildRanges(roomid, date_rwid)
    {
        //for each capacity rule, check if max_adult >= 2 and there is at least one child
        //      push child_ages applicable to rule + min_max child
        //next rule


        var arr_result = [];
        var arr_main_childages = [];

        var dateobj = lookupCapacityRoomDateObj(roomid, date_rwid);
        var arrrulecounter = dateobj.date_capacity_rules;

        //===========================================================

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
                            if (capacityobj.capacity_maxpax >= 2)
                            {
                                //got a potential sharing parent obj
                                //check if there is at least one child in the object
                                if (rule_capacity_has_children(arrrule_capacity))
                                {
                                    var xobj = pushCapacityChilrenObj(ruleobj);
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

    function rule_capacity_has_children(arrrule_capacity)
    {
        //returns true if in this rule_capacity array, there is at least one child
        //returns false other wise

        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE")
            {
                if (capacityobj.capacity_category == "CHILD")
                {
                    if (capacityobj.capacity_maxpax > 0)
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function pushCapacityChilrenObj(ruleobj)
    {
        var xobj = {children_ages: []};
        var arrrule_capacity = ruleobj.rule_capacity;
        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE" &&
                    capacityobj.capacity_category == "CHILD")
            {

                var capacity_maxpax = utils_parseInt(capacityobj.capacity_maxpax);
                var capacity_minpax = utils_parseInt(capacityobj.capacity_minpax);

                if (capacity_maxpax > 0)
                {
                    xobj.children_ages.push(capacityobj);
                }
            }
        }

        return xobj;
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

    function loadGridSingleParentPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
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
                                var xobj = pushCapacityChilrenObj(ruleobj);
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
                                    var xobj = pushCapacityChilrenObj(ruleobj);
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


                //combination must be based on spo age ranges
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



        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
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


    function explode_ageranges(agefrom, ageto)
    {
        //explode the age range in the ranges defined in main
        //eg: main = 0-1, 2-11, 12-17 and here range is 0-17
        //return array 0-1, 2-11, 12-17

        var arr = [];

        agefrom = parseInt(agefrom, 10);
        ageto = parseInt(ageto, 10);

        var child_ages_ids = getChildrenAgeString();
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

    function calculateSingleParentPolicySalesPrice(rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        //call the function for each selling currency
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateFlatRateSellingPrice(newvalue, currencyinputid, sell_currency_id, callbackDisplaySingleParentPolicySalesPrice, [rwid, cindx, sell_currency_id]);
        }

        return;
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
                var colid = grid_singleparentpolicy_age.getColumnId(c.cell.cellIndex);
                if (colid.indexOf("adult_") != -1)
                {
                    //duplicateSingleParentAdultValuesDisplay(rwid, colid, finalsp, null, null);
                }
            }
        });

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
                if (colid.indexOf("adult_") != -1)
                {
                    //duplicateSingleParentAdultValuesDisplay(rId, colid, nValue, roomid, daterwid);
                }

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

    function getRuleAdultNode(ruleobj)
    {
        //get the adult node of that rule
        //return null if not found

        var arrrule_capacity = ruleobj.rule_capacity;
        for (var k = 0; k < arrrule_capacity.length; k++)
        {
            var capacityobj = arrrule_capacity[k];
            if (capacityobj.capacity_action != "DELETE")
            {
                if (capacityobj.capacity_category == "ADULT")
                {
                    return capacityobj;
                }
            }
        }

        return null;
    }


    function ownRoomGetChildRanges(roomid, date_rwid)
    {
        //CHANGES:: ADDED FUNCTION

        //for each capacity rule, check if min_adult = 0 and max_adult = 0
        //      push child_ages applicable to rule + min_max child
        //next rule

        //if no rules found from min_adult = 0 and max_adult = 0 then
        //check for rules where min_adult = 0
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
                //get the adult node of that rule
                var adult_node = getRuleAdultNode(ruleobj);
                var flg_add_node = false;

                if (!adult_node)
                {
                    flg_add_node = true;
                } else
                {
                    if (utils_parseInt(adult_node.capacity_minpax) == 0 &&
                            utils_parseInt(adult_node.capacity_maxpax) == 0)
                    {
                        flg_add_node = true;
                    }
                }


                //got a rule without parents
                if (flg_add_node)
                {
                    var xobj = pushCapacityChilrenObj(ruleobj);
                    arr_result.push(xobj);
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


                //combination must be based on contract.main.ages
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

    function loadGridChildPolicyOwnRoom(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {


        var return_arr = ownRoomGetChildRanges(roomid, date_rwid);
        var arr_result = return_arr.RESULT;
        var arr_main_childages = return_arr.MAIN_CHILD_AGES;

        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();


        grid_childpolicy_own_age.clearAll(true);
        grid_childpolicy_own_age = null;
        grid_childpolicy_own_age = accord_child.cells("single").attachGrid();
        grid_childpolicy_own_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_childpolicy_own_age.enableAlterCss("", "");
        grid_childpolicy_own_age.enableEditTabOnly(true);
        grid_childpolicy_own_age.enableEditEvents(true, true, true);
        grid_childpolicy_own_age.attachEvent("onEditCell", onGridChildOwnPolicyEdit);
        grid_childpolicy_own_age.enableColSpan(true);



        var url = "php/api/hotelcontracts/grid_childpolicy_own_xml.php?" +
                "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid +
                "&arr_main_childages=" + encodeURIComponent(JSON.stringify(arr_main_childages)) +
                "&arr_result=" + encodeURIComponent(JSON.stringify(arr_result)) +
                "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                "&costprice_currencyid=" + costprice_currencyid;

        grid_childpolicy_own_age.loadXML(url, function () {
            //fill in values
            fillChildOwnPolicyGridValues(roomid, date_rwid, grid_childpolicy_own_age);

            grid_childpolicy_own_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_childpolicy_own_age.setEditable(true);
            }

        });

    }

    function fillChildOwnPolicyGridValues(roomid, date_rwid)
    {
        //CHANGES:: CREATED FUNCTION
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        grid_childpolicy_own_age.forEachRow(function (rwid) {
            grid_childpolicy_own_age.forEachCell(rwid, function (c) {

                var context = c.getAttribute("context");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");
                var child_index = c.getAttribute("child_index");
                var rule_ageranges = c.getAttribute("rule_ageranges");

                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {
                    var ruleobj = lookupChildOwnPoliciesDateRuleObject(capacity_date_obj, child_index, rule_ageranges);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupChildPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto);

                        if (policyrulecell)
                        {
                            var valuecell = lookupChildPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateChildOwnPolicySalesPrice(grid_childpolicy_own_age, rwid,
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

    function calculateChildOwnPolicySalesPrice(grid, rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");

        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateFlatRateSellingPrice(newvalue, currencyinputid, sell_currency_id, callbackDisplayChildOwnPolicySalesPrice, [rwid, cindx, sell_currency_id]);
        }
    }

    function callbackDisplayChildOwnPolicySalesPrice(arr_arguements)
    {
        //[rwid, cindx, sellcurrencyid]

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

        var cellObj = grid_childpolicy_own_age.cells(rwid, cellidx);

        var context = cellObj.getAttribute("context");
        var agefrom = cellObj.getAttribute("agefrom");
        var ageto = cellObj.getAttribute("ageto");
        var number = cellObj.getAttribute("number");
        var rule_ageranges = cellObj.getAttribute("rule_ageranges");

        grid_childpolicy_own_age.forEachCell(rwid, function (c) {

            if (c.getAttribute("context") == context &&
                    c.getAttribute("currencyid") == sp_currencyid &&
                    c.getAttribute("agefrom") == agefrom &&
                    c.getAttribute("ageto") == ageto &&
                    c.getAttribute("number") == number &&
                    c.getAttribute("rule_ageranges") == rule_ageranges &&
                    c.getAttribute("buy_sell") == "sell")
            {
                c.setValue(finalsp);
            }
        });
    }

    function lookupChildOwnPoliciesDateRuleObject(dateobj, number, rule_ageranges)
    {
        //returns adult policy room object from a capacity date object

        var arrrules = dateobj.date_childpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == number &&
                    arrrules[i].rule_sharing_single == "SINGLE" &&
                    arrrules[i].rule_ageranges == rule_ageranges &&
                    arrrules[i].rule_action != "DELETE")
            {

                return arrrules[i];


            }
        }

        return null;
    }

    function onGridChildOwnPolicyEdit(stage, rId, cInd, nValue, oValue)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");

        if (node != "DATE")
        {
            return false;
        }


        var roomid = tree_roomdates.getUserData(nodeid, "DATE_ROOMID");
        var daterwid = tree_roomdates.getUserData(nodeid, "DATE_RWID");



        if (stage == 1)
        {
            if (grid_childpolicy_own_age.editor && grid_childpolicy_own_age.editor.obj)
            {
                grid_childpolicy_own_age.editor.obj.select(); /* grid.editor.obj is the input object*/
            }
        } else if (stage == 2)
        {
            if (nValue != oValue)
            {
                var c = grid_childpolicy_own_age.cells(rId, cInd);

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
                    calculateChildOwnPolicySalesPrice(grid_childpolicy_own_age, rId, cInd, nValue, currencyinputid);
                }

                updateJsonChildOwnPoliciesValues(cInd, roomid, daterwid, rId, nValue);
                updateAllCheckedDatesNode("date_childpolicies_rules");
            }
        }

        return true;
    }

    function updateJsonChildOwnPoliciesValues(cInd, roomid, date_rwid, rId, nValue)
    {
        var c = grid_childpolicy_own_age.cells(rId, cInd);
        var context = c.getAttribute("context");
        var number = c.getAttribute("child_index");
        var currencyid = c.getAttribute("currencyid");
        var agefrom = c.getAttribute("agefrom");
        var ageto = c.getAttribute("ageto");
        var rule_ageranges = c.getAttribute("rule_ageranges"); //++

        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var ruleobj = lookupChildOwnPoliciesDateRuleObject(capacity_date_obj, number, rule_ageranges);
        if (!ruleobj)
        {
            //insert rule object into json_capacity
            _childpolicy_room_date_rule_id--;
            ruleobj = {rule_rwid: _childpolicy_room_date_rule_id,
                rule_category: number,
                rule_rulecounter: 0,
                rule_sharing_single: "SINGLE",
                rule_action: "INSERT",
                rule_ageranges: rule_ageranges,
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

    function calculateChildPolicySalesPrice(grid, rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateFlatRateSellingPrice(newvalue, currencyinputid, sell_currency_id, callbackDisplayChildPolicySalesPrice, [rwid, cindx, sell_currency_id]);
        }

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

    function loadGridChildPolicySharing(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {

        var child_ages_ids = getChildrenAgeString();

        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
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



        //======================================
        var url = "";
        if (variant == "PERSONS")
        {
            var return_arr = childrenSharingChildRanges(roomid, date_rwid);
            var arr_result = return_arr.RESULT;
            var arr_main_childages = return_arr.MAIN_CHILD_AGES;

            url = "php/api/hotelcontracts/grid_childpolicy_sharing_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&roomid=" + roomid +
                    "&arr_result=" + encodeURIComponent(JSON.stringify(arr_result)) +
                    "&arr_main_childages=" + encodeURIComponent(JSON.stringify(arr_main_childages)) +
                    "&child_mode=sharing" +
                    "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                    "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                    "&costprice_currencyid=" + costprice_currencyid;

        } else if (variant == "UNITS")
        {
            var return_arr = childrenUnitsChildRanges(roomid, date_rwid);
            var arr_result = return_arr.RESULT;
            var arr_main_childages = return_arr.MAIN_CHILD_AGES;

            url = "php/api/hotelcontracts/grid_childpolicy_units_xml.php?" +
                    "t=" + encodeURIComponent(global_token) +
                    "&roomid=" + roomid +
                    "&arr_result=" + encodeURIComponent(JSON.stringify(arr_result)) +
                    "&arr_main_childages=" + encodeURIComponent(JSON.stringify(arr_main_childages)) +
                    "&child_mode=sharing" +
                    "&selected_currency_buy_ids=" + selected_currency_buy_ids +
                    "&selected_currency_sell_ids=" + selected_currency_sell_ids +
                    "&costprice_currencyid=" + costprice_currencyid;
        }

        grid_childpolicy_sharing_age.loadXML(url, function () {

            //fill in values
            if (variant == "UNITS")
            {
                fillUnitsChildPolicyGridValues(roomid, date_rwid);
            } else if (variant == "PERSONS")
            {
                fillPersonsChildSharingPolicyGridValues(roomid, date_rwid);
            }

            grid_childpolicy_sharing_age.setEditable(false);
            if (tree_roomdates.isItemChecked(nodeid))
            {
                grid_childpolicy_sharing_age.setEditable(true);
            }
        });
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
                    calculateChildSharingPolicySalesPrice(rId, cInd, nValue, currencyinputid);
                }

                updateJsonChildSharingPoliciesValues(cInd, roomid, daterwid, rId, nValue);
                updateAllCheckedDatesNode("date_childpolicies_rules");
            }
        }

        return true;
    }


    function updateJsonChildSharingPoliciesValues(cInd, roomid, date_rwid, rId, nValue)
    {
        var c = grid_childpolicy_sharing_age.cells(rId, cInd);
        var context = c.getAttribute("context");
        var number = c.getAttribute("number");
        var currencyid = c.getAttribute("currencyid");
        var agefrom = c.getAttribute("agefrom");
        var ageto = c.getAttribute("ageto");
        var rule_ageranges = c.getAttribute("rule_ageranges"); //++


        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        var ruleobj = lookupChildSharingPoliciesDateRuleObject(capacity_date_obj, number, rule_ageranges);
        if (!ruleobj)
        {
            //insert rule object into json_capacity
            _childpolicy_room_date_rule_id--;
            ruleobj = {rule_rwid: _childpolicy_room_date_rule_id,
                rule_category: number,
                rule_rulecounter: 0,
                rule_sharing_single: "SHARING",
                rule_action: "INSERT",
                rule_ageranges: rule_ageranges,
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


    function fillPersonsChildSharingPolicyGridValues(roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        grid_childpolicy_sharing_age.forEachRow(function (rwid) {
            grid_childpolicy_sharing_age.forEachCell(rwid, function (c) {

                var child_index = c.getAttribute("number");
                var context = c.getAttribute("context");
                var rule_ageranges = c.getAttribute("rule_ageranges");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");

                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {
                    var ruleobj = lookupChildSharingPoliciesDateRuleObject(capacity_date_obj, child_index, rule_ageranges);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupChildSharingPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto);

                        if (policyrulecell)
                        {
                            var valuecell = lookupChildSharingPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateChildSharingPolicySalesPrice(rwid, c.cell.cellIndex, valuecell.value_value, currencyid);
                                }
                            }
                        }
                    }
                }
            });
        });
    }

    function calculateChildSharingPolicySalesPrice(rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");

        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateFlatRateSellingPrice(newvalue, currencyinputid, sell_currency_id, callbackDisplayChildSharingPolicySalesPrice, [rwid, cindx]);
        }
    }

    function callbackDisplayChildSharingPolicySalesPrice(arr_arguements)
    {

        ///[rwid, cindx, calc_obj]

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

        var cellObj = grid_childpolicy_sharing_age.cells(rwid, cellidx);

        var context = cellObj.getAttribute("context");
        var agefrom = cellObj.getAttribute("agefrom");
        var ageto = cellObj.getAttribute("ageto");
        var number = cellObj.getAttribute("number");
        var rule_ageranges = cellObj.getAttribute("rule_ageranges");

        grid_childpolicy_sharing_age.forEachCell(rwid, function (c) {

            if (c.getAttribute("context") == context &&
                    c.getAttribute("currencyid") == sp_currencyid &&
                    c.getAttribute("agefrom") == agefrom &&
                    c.getAttribute("ageto") == ageto &&
                    c.getAttribute("number") == number &&
                    c.getAttribute("rule_ageranges") == rule_ageranges &&
                    c.getAttribute("buy_sell") == "sell")
            {
                c.setValue(finalsp);
                return;
            }
        });

    }


    function lookupChildSharingPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid)
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



    function lookupChildSharingPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto, adult_child)
    {
        var arrrule_policy = ruleobj.rule_policy;
        for (var i = 0; i < arrrule_policy.length; i++)
        {
            var policyrulecell = arrrule_policy[i];

            if (policyrulecell.policy_category == context &&
                    policyrulecell.policy_basis == child_index &&
                    policyrulecell.policy_units_additional_child_agefrom == agefrom &&
                    policyrulecell.policy_units_additional_child_ageto == ageto)
            {
                return policyrulecell;
            }
        }

        return null;
    }

    function lookupChildSharingPoliciesDateRuleObject(dateobj, number, rule_ageranges)
    {
        //returns child sharing policy room object from a capacity date object

        var arrrules = dateobj.date_childpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == number &&
                    arrrules[i].rule_sharing_single == "SHARING" &&
                    arrrules[i].rule_ageranges == rule_ageranges &&
                    arrrules[i].rule_action != "DELETE")
            {
                //in case is sharing, there is an additional check

                return arrrules[i];

            }
        }

        return null;
    }



    function fillUnitsChildPolicyGridValues(roomid, date_rwid)
    {
        var capacity_date_obj = lookupCapacityRoomDateObj(roomid, date_rwid);
        if (!capacity_date_obj)
        {
            return;
        }

        grid_childpolicy_sharing_age.forEachRow(function (rwid) {
            grid_childpolicy_sharing_age.forEachCell(rwid, function (c) {

                var child_index = c.getAttribute("number");
                var context = c.getAttribute("context");
                var rule_ageranges = c.getAttribute("rule_ageranges");
                var currencyid = c.getAttribute("currencyid");
                var type = c.getAttribute("type");
                var agefrom = c.getAttribute("agefrom");
                var ageto = c.getAttribute("ageto");
                var buy_sell = c.getAttribute("buy_sell");

                if (type != "ro" && (buy_sell == "" || buy_sell == "buy"))
                {
                    var ruleobj = lookupChildSharingPoliciesDateRuleObject(capacity_date_obj, child_index, rule_ageranges);
                    if (ruleobj)
                    {
                        var policyrulecell = lookupChildSharingPoliciesDateRuleCellObject(ruleobj, context, child_index, agefrom, ageto);

                        if (policyrulecell)
                        {
                            var valuecell = lookupChildSharingPoliciesDateRuleCellValueObject(policyrulecell, context, currencyid);
                            if (valuecell)
                            {
                                c.setValue(valuecell.value_value);

                                //calculate SP where necessary
                                if (type == "edn" && buy_sell == "buy" && currencyid != "")
                                {
                                    calculateChildSharingPolicySalesPrice(rwid, c.cell.cellIndex, valuecell.value_value, currencyid);
                                }
                            }
                        }
                    }
                }
            });
        });
    }

    function lookupChildUnitsPoliciesDateRuleObject(dateobj, number)
    {
        //returns adult policy room object from a capacity date object

        var arrrules = dateobj.date_childpolicies_rules;

        for (var i = 0; i < arrrules.length; i++)
        {
            if (arrrules[i].rule_category == number &&
                    arrrules[i].rule_sharing_single == "SHARING" &&
                    arrrules[i].rule_action != "DELETE")
            {

                return arrrules[i];


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

    function loadGridAdultPolicy(roomid, date_rwid, dtfrom, dtto, variant, nodeid)
    {



        var child_ages_ids = getChildrenAgeString();
        var selected_currency_buy_ids = form_flat_rate_currency.getItemValue("selected_currency_buy_ids");
        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
        var costprice_currencyid = cboCostPriceCurrency.getSelectedValue();

        //if variant == persons then show category and basis, value depending on  room
        //add rows for max adult count for that room

        //if variant == units then show Normal Adult,Additional Adult, Additional Child X Child Ages


        grid_adultpolicy_age.clearAll(true);
        grid_adultpolicy_age = null;
        grid_adultpolicy_age = adult_layout.cells("a").attachGrid();
        grid_adultpolicy_age.setIconsPath('libraries/dhtmlx/imgs/');
        grid_adultpolicy_age.enableAlterCss("", "");
        grid_adultpolicy_age.enableEditTabOnly(true);
        grid_adultpolicy_age.enableEditEvents(true, true, true);
        grid_adultpolicy_age.attachEvent("onEditCell", onGridAdultPolicyEdit);
        grid_adultpolicy_age.enableColSpan(true);

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


        //if (variant == "PERSONS")
        //{
        //context = category,basis,value
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


    function calculateAdultPolicySalesPrice(rwid, cindx, newvalue, currencyinputid)
    {
        var nodeid = tree_roomdates.getSelectedItemId();
        var node = tree_roomdates.getUserData(nodeid, "ROOM_SEASON_DATE");
        if (node != "DATE")
        {
            return;
        }

        var selected_currency_sell_ids = form_flat_rate_currency.getItemValue("selected_currency_sell_ids");
        var arr_currency_sell = selected_currency_sell_ids.split(",");
        for (var i = 0; i < arr_currency_sell.length; i++)
        {
            var sell_currency_id = arr_currency_sell[i];
            calculateFlatRateSellingPrice(newvalue, currencyinputid, sell_currency_id, callbackDisplayAdultPolicySalesPrice, [rwid, cindx]);
        }
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
            header_str = "Std Occup (Ad + Ch),#cspan,Xtra Adults,#cspan";
            header_attach = "Min,Max,Min,Max";
            column_ids = "standardoccupation_Mi_0,standardoccupation_Mx_0,additionalpersons_Mi_0,additionalpersons_Mx_0";
            column_types = "edn,edn,edn,edn";
            init_widths = "35,35,35,35";
            col_align = "center,center,center,center";
            col_sorting = "na,na,na,na";


            var child_ages_ids = getChildrenAgeString();
            var arr_ids = child_ages_ids.split(",");
            for (var i = 0; i < arr_ids.length; i++)
            {
                var id = arr_ids[i];
                if (id != "")
                {
                    var item = _dsChildPolicy.item(id);
                    var agefrom = item.agefrom;
                    var ageto = item.ageto;

                    header_str += ",Xtra Ch <br>(" + agefrom + "-" + ageto + "),#cspan";
                    header_attach += ",Mi,Mx";
                    column_ids += ",Ch_Mi_" + agefrom + ",Ch_Mx_" + ageto;
                    column_types += ",edn,edn";
                    init_widths += "," + (_agecolwidth) + "," + (_agecolwidth);
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
                                header_str += ",Xtra Ch Mix " + agefrom_1 + "-" + ageto_2 + ",#cspan";
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

            var child_ages_ids = getChildrenAgeString();
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

            //if (variant == "PERSONS")
            //{
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
                    var placevalue = "";
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


    function getChildrenAgeString()
    {
        var child_ages_ids = "";
        if (grid_flat_rate_validity.getRowsNum() >= 0)
        {
            var rwid = grid_flat_rate_validity.getRowId(0);
            child_ages_ids = grid_flat_rate_validity.cells(rwid, grid_flat_rate_validity.getColIndexById("children_ages_ids")).getValue();
        }

        return child_ages_ids;
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

        if (!validate_room_capacity(roomid))
        {
            return;
        }

        var params = "t=" + encodeURIComponent(global_token) +
                "&roomid=" + roomid + "&dateid=" + dateid +
                "&json_capacity=" + encodeURIComponent(JSON.stringify(_json_capacity));


        popupwin_capacitycombinations.center();
        popupwin_capacitycombinations.show();
        popupwin_spo.setModal(false);
        popupwin_capacitycombinations.setModal(true);

        layout_flatrate_capacitycombii.cells("a").progressOn();


        dhtmlxAjax.post("php/api/hotelspecialoffers/generateroomcombinations.php", params, function (loader) {

            if (loader)
            {
                layout_flatrate_capacitycombii.cells("a").progressOff();

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

    function displayCombinations(json_combii)
    {
        var combii = "<div style='width:100%;height:100%;overflow:auto;'>" +
                "<style>p.big {line-height: 2;}</style><p class='big'>";


        var roomname = json_combii.room_name;
        var variant = json_combii.room_variants;

        popupwin_capacitycombinations.setText("<b>COMBINATIONS FOR " + roomname + " (" + variant + ")</b>");


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

        layout_flatrate_capacitycombii.cells("a").attachHTMLString(combii);
    }

    function validate_room_capacity_overlapping(category, rule_capacity, min, max, age_from, age_to)
    {
        age_from = parseInt(age_from, 10);
        age_to = parseInt(age_to, 10);

        if (max == 0 || (category != "CHILD" && category != "CH"))
        {
            return true; //no need to worry
        }

        for (var l = 0; l < rule_capacity.length; l++)
        {
            if (rule_capacity[l].capacity_action != "DELETE" &&
                    (rule_capacity[l].capacity_category == "CHILD" || rule_capacity[l].capacity_category == "CH"))
            {
                var _min = rule_capacity[l].capacity_minpax;
                var _max = rule_capacity[l].capacity_maxpax;

                var _age_from = parseInt(rule_capacity[l].capacity_child_agefrom, 10);
                var _age_to = parseInt(rule_capacity[l].capacity_child_ageto, 10);

                if (_min == "") {
                    _min = 0;
                }
                if (_max == "") {
                    _max = 0;
                }

                _max = parseInt(_max, 10);

                if (_max > 0)
                {
                    if (_age_from != age_from || _age_to != age_to)
                    {
                        if (_age_from <= age_to && age_from <= _age_to)
                        {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    function validate_room_capacity(roomid)
    {
        //if roomid is blank, means validate all rooms, else validate for specific room

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
                                        var category = rule_capacity[l].capacity_category;

                                        var min = rule_capacity[l].capacity_minpax;
                                        var max = rule_capacity[l].capacity_maxpax;

                                        var age_from = rule_capacity[l].capacity_child_agefrom;
                                        var age_to = rule_capacity[l].capacity_child_ageto;

                                        if (min == "") {
                                            min = 0;
                                            rule_capacity[l].capacity_minpax = 0;

                                        }
                                        if (max == "") {
                                            max = 0;
                                            rule_capacity[l].capacity_maxpax = 0;
                                        }

                                        //check for overlapping with other age ranges
                                        if (!validate_room_capacity_overlapping(category, rule_capacity, min, max, age_from, age_to))
                                        {
                                            dhtmlx.alert({
                                                text: "<b>Overlapping</b> with other date ranges. Please recheck selected entries..",
                                                type: "alert-warning",
                                                title: "Validate Capacity",
                                                callback: function () {
                                                    var nodeid = "DATE_" + err_room_date_rwid;
                                                    tree_roomdates.selectItem(nodeid, true, false);
                                                    grid_capacity_age.selectRowById(err_room_date_rule_rwid, false, true, false);
                                                }
                                            });

                                            return false;

                                        } else if (min > max)
                                        {
                                            dhtmlx.alert({
                                                text: "<b>Minimum</b> Values cannot be greater than <b>Maximum</b> Values. Please recheck selected entries..",
                                                type: "alert-warning",
                                                title: "Validate Capacity",
                                                callback: function () {
                                                    var nodeid = "DATE_" + err_room_date_rwid;
                                                    tree_roomdates.selectItem(nodeid, true, false);
                                                    grid_capacity_age.selectRowById(err_room_date_rule_rwid, false, true, false);
                                                }
                                            });

                                            return false;

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    function displayCombinationsColumn(combinations_array, from, to)
    {
        var aico = "<img src='images/adult_24.png' />";
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

    function is_age_in_main(agefrom, ageto)
    {
        //returns true if this age combination is selected in SPO

        var child_ages_ids = getChildrenAgeString()
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

    function linkSPO()
    {
        popupwin_link.center();
        popupwin_link.setModal(true);
        popupwin_link.show();

        loadSpoLinkGrid(null, null);
    }

    function loadSpoLinkGrid(callback, id)
    {
        grid_spo_link.clearAll();

        spolayout_link.cells("a").progressOn();

        grid_spo_link.loadXML("php/api/hotelspecialoffers/spolink_grid_xml.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, function () {

            spolayout_link.cells("a").progressOff();
            if (callback)
            {
                callback(id);
            }
        });
    }


    function attachSPO(linkid)
    {

        spoattachlayout_link.cells("a").progressOn();
        grid_attach_spo_link.clearAll();

        grid_attach_spo_link.loadXML("php/api/hotelspecialoffers/specialoffer_link_grid_xml.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id + "&linkid=" + linkid, function () {

            spoattachlayout_link.cells("a").progressOff();
            grid_attach_spo_link.forEachRow(function (rwid) {
                grid_attach_spo_link.forEachCell(rwid, function (c, ind) {
                    var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                    grid_attach_spo_link.setCellTextStyle(rwid, ind, cellstyle);
                });
            });
        });

        popupwin_link.setModal(false);

        popupwin_loadslinkspo.center();
        popupwin_loadslinkspo.setModal(true);
        popupwin_loadslinkspo.show();
    }

    function newSPOLink()
    {
        popupwin_link.setModal(false);

        form_spo_link.clear();
        popupwin_newspolink.center();
        popupwin_newspolink.setModal(true);
        popupwin_newspolink.show();
    }

    function saveLink()
    {
        if (!form_spo_link.validate())
        {
            dhtmlx.alert({
                text: "Please enter missing highligted fields!",
                type: "alert-warning",
                title: "Save Link",
                callback: function () {
                    t
                }
            });

            return;
        }


        var params = "t=" + encodeURIComponent(global_token) + "&hid=" + global_hotel_id;
        var data = form_spo_link.getFormData();
        params += "&data=" + encodeURIComponent(JSON.stringify(data));

        dhtmlxAjax.post("php/api/hotelspecialoffers/savelink.php", params, function (loader) {
            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "SAVE LINK",
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
                        title: "SAVE LINK",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {

                    /*
                     dhtmlx.alert({
                     text: "Save Successful",
                     type: "alert",
                     title: "SAVE",
                     callback: function () {
                     }
                     });
                     */

                    popupwin_newspolink.hide();
                    popupwin_newspolink.setModal(false);
                    popupwin_link.setModal(true);

                    var linkid = json_obj.ID;
                    loadSpoLinkGrid(selectSPOLink_link, linkid);

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "SAVE LINK",
                        callback: function () {
                        }
                    });
                }
            }
        });


    }

    function selectSPOLink_link(_linkid)
    {
        grid_spo_link.forEachRow(function (rid) {
            var linkid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("linkid")).getValue();
            if (linkid == _linkid)
            {
                grid_spo_link.selectRowById(rid, false, true, true);
                return;
            }
        });

        return;
    }

    function selectSPOLink_linkline(_linklineid)
    {
        grid_spo_link.forEachRow(function (rid) {
            var linklineid = grid_spo_link.cells(rid, grid_spo_link.getColIndexById("linklineid")).getValue();

            if (linklineid == _linklineid)
            {
                grid_spo_link.selectRowById(rid, false, true, true);
                return;
            }
        });

        return;
    }

    function attachSpoToLink()
    {
        var cid = grid_spo_link.getSelectedRowId();
        var linkid = grid_spo_link.cells(cid, grid_spo_link.getColIndexById("linkid")).getValue();

        var spoids = grid_attach_spo_link.getCheckedRows(grid_attach_spo_link.getColIndexById("X"));

        if (spoids == "")
        {
            dhtmlx.alert({
                text: "Please Select at Least SPO!",
                type: "alert-warning",
                title: "Attach SPO",
                callback: function () {
                }
            });

            return;
        }


        spoattachlayout_link.progressOn();

        var params = "linkid=" + linkid + "&spoids=" + spoids + "&t=" + encodeURIComponent(global_token);

        dhtmlxAjax.post("php/api/hotelspecialoffers/attachspo.php", params, function (loader) {
            spoattachlayout_link.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "ATTACH SPO",
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
                        title: "ATTACH SPO",
                        callback: function () {
                        }
                    });
                    return false;
                }

                if (json_obj.OUTCOME == "OK")
                {
                    loadHotelSPOs("");
                    loadSpoLinkGrid(selectSPOLink_link, linkid);
                    popupwin_loadslinkspo.hide();
                    popupwin_loadslinkspo.setModal(false);
                    popupwin_link.setModal(true);


                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "ATTACH SPO",
                        callback: function () {
                        }
                    });
                }
            }
        });
    }

    function populateCboRoomsFilter()
    {
        comboRoomFilter.clearAll(true);
        comboRoomFilter.addOption([{value: "-1", text: "--No Filter--", img_src: "images/room_32.png"}]);


        var roomids = form_name.getItemValue("rooms_ids");

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

    function resetIds()
    {
        form_name.setItemValue("id", "-1");
        form_name.setItemValue("sponame", form_name.getItemValue("sponame") + " - COPY");
        form_name.setItemValue("spocode", form_name.getItemValue("spocode") + " - COPY");


        //============================================
        resetGridIds(grid_period);


        //============================================

        var template = form_name.getItemValue("template");

        if (template == "free_nights")
        {
            resetGridIds(grid_free_nights_validity);
            resetGridIds(grid_free_nights);
        } else if (template == "free_upgrade")
        {
            resetGridIds(grid_upgrade);
        } else if (template == "meals_upgrade")
        {
            resetGridIds(grid_meal_upgrade);
        } else if (template == "family_offer")
        {
            resetGridIds(grid_family_discount_childrenage);
        } else if (template == "flat_rate")
        {

            //===========================================================================
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

            //============================================

            var buying_settings = _flat_rate_tax_commi_obj.buying_settings;
            var selling_settings = _flat_rate_tax_commi_obj.selling_settings;

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
            //===========================================

            resetGridIds(grid_flat_rate_validity);
            resetGridIds(grid_flatrate_supp);
            resetGridIds(grid_flatrate_cancellation);
            resetGridIds(grid_flatrate_checkinout);
            resetGridIds(grid_flat_rate_exchrates);
            resetGridIds(grid_flat_rate_currencymap);
        }

        return;
    }

    function resetGridIds(grid)
    {
        for (var i = 0; i < grid.getRowsNum(); i++) {
            var rwid = parseInt(grid.getRowId(i), 10);
            grid.setRowId(i, (rwid * -1));
        }
    }

    function testFreeNights()
    {
        var stays = utils_trim(toolbar_free_nights.getValue("txtstays"), " ");
        if (isNaN(stays) || stays == "")
        {
            stays = 0;
        }
        stays = parseInt(stays, 10);

        if (stays <= 0)
        {
            dhtmlx.alert({
                text: "Number of Nights Stayed must be NUMERIC and GREATER than ZERO",
                type: "alert-warning",
                title: "Special Offer",
                callback: function () {
                }
            });
            return;
        }

        //go on test:

        //get array of stays and pays

        var arr_free_nights = [];
        for (var i = 0; i < grid_free_nights.getRowsNum(); i++)
        {
            var rwid = grid_free_nights.getRowId(i);

            var pay_nights = grid_free_nights.cells(rwid, grid_free_nights.getColIndexById("pay_nights")).getValue();
            var stay_nights = grid_free_nights.cells(rwid, grid_free_nights.getColIndexById("stay_nights")).getValue();
            arr_free_nights.push({"STAYS": stay_nights, "PAYS": pay_nights});
        }

        var cumulative = form_free_nights.isItemChecked("free_nights_cumulative")
        cumulative = (cumulative) ? 1 : 0;

        var params = "t=" + encodeURIComponent(global_token) +
                "&arr_free_nights=" + encodeURIComponent(JSON.stringify(arr_free_nights)) +
                "&stays=" + stays +
                "&cumulative=" + cumulative;

        freenights_layout.progressOn();
        dhtmlxAjax.post("php/api/hotelspecialoffers/test_free_nights.php", params, function (loader) {
            freenights_layout.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "TEST FREE NIGHTS",
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
                        title: "TEST FREE NIGHTS",
                        callback: function () {
                        }
                    });
                    return false;
                }

                if (json_obj.OUTCOME == "OK")
                {
                    var free = json_obj.FREE;
                    var stay = json_obj.STAY;
                    var pay = json_obj.PAY;

                    dhtmlx.alert({
                        text: "Stays:" + stay + " - Pays:" + pay + " = Free: <b>" + free + "</b> night(s)",
                        type: "alert",
                        title: "TEST FREE NIGHTS",
                        callback: function () {
                        }
                    });

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "TEST FREE NIGHTS",
                        callback: function () {
                        }
                    });
                }
            }
        });
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
                                var xobj = pushCapacityChilrenObj(ruleobj);
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
                                    var xobj = pushCapacityChilrenObj(ruleobj);
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



    function deleteAllSingleParentChildrenRates(roomid, date_rwid, rule_agerange_filter)
    {
        var dateobj = lookupCapacityRoomDateObj(roomid, date_rwid);
        var arr_sngprnt_rules = dateobj.date_singleparentpolicies_rules;

        for (var i = 0; i < arr_sngprnt_rules.length; i++)
        {
            if (!rule_agerange_filter)
            {
                //rule_agerange_filter is null, delete all
                arr_sngprnt_rules[i].rule_action = "DELETE";

            } else if (arr_sngprnt_rules[i].rule_ageranges == rule_agerange_filter)
            {
                //rule_agerange_filter is not null, delete only selected one
                arr_sngprnt_rules[i].rule_action = "DELETE";
            }

        }
    }

    function singleParentChildrenAgesInCategory(children_ages, rule_ageranges)
    {
        var copy_children_ages = utils_deepCopy(children_ages);

        //rule_ageranges example: ; 0_1:0^2 ; 2_3:1^3 ;
        //it means: age range 0-1 with capacity 0-2
        //          age range 2-3 with capacity 1-3
        //explode rule_ageranges and check if each of the ages are in children_ages

        var arr_age_ranges = rule_ageranges.split(";");
        for (var i = 0; i < arr_age_ranges.length; i++)
        {
            var _the_range = utils_trim(arr_age_ranges[i], " ");

            if (_the_range != "")
            {
                var _the_ages = _the_range.split(":");
                var age_value = _the_ages[0];
                var minmax_values = _the_ages[1];
                
                if (utils_trim(age_value, " ") != "")
                {
                    var arr_age_from_to = age_value.split("_");
                    var age_from = arr_age_from_to[0];
                    var age_to = arr_age_from_to[1];
                    
                    var arr_min_max = minmax_values.split("^");
                    var _min = arr_min_max[0];
                    var _max = arr_min_max[1];
                    
                    var found = false;

                    //now for this age range, search into copy_children_ages
                    var j = copy_children_ages.length;

                    while (j--) {

                        if (copy_children_ages[j].capacity_child_agefrom == age_from &&
                                copy_children_ages[j].capacity_child_ageto == age_to &&
                                copy_children_ages[j].capacity_maxpax == _max && 
                                copy_children_ages[j].capacity_minpax == _min)
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

    function triggerConditionsChange(id, value)
    {
        if (id == "min_stay_priority")
        {
            var priority = form_conditions.getItemValue("min_stay_priority");
            if (priority == "NONE")
            {
                form_conditions.setItemValue("min_stay_from", "");
                form_conditions.setItemValue("min_stay_to", "");

                form_conditions.disableItem("min_stay_from");
                form_conditions.disableItem("min_stay_to");
            } else
            {
                form_conditions.enableItem("min_stay_from");
                form_conditions.enableItem("min_stay_to");
            }
        }

        return;
    }

    function cleanAdultOrChildValuesCurrency(arr_rule_policy)
    {
        for (var i = 0; i < arr_rule_policy.length; i++)
        {
            var policy_action = arr_rule_policy[i].arr_rule_policy;
            if (policy_action != "DELETE")
            {
                var arr_policy_values = arr_rule_policy[i].policy_values;

                for (var j = 0; j < arr_policy_values.length; j++)
                {
                    var value_currencyfk = arr_policy_values[j].value_currencyfk;
                    var value_action = arr_policy_values[j].value_action;
                    if (value_action != "DELETE")
                    {
                        var selected_currency_buy_ids = utils_trim(form_flat_rate_currency.getItemValue("selected_currency_buy_ids"), " ");
                        if (value_currencyfk != "" && value_currencyfk != selected_currency_buy_ids)
                        {
                            arr_policy_values[j].value_action = "DELETE";
                        }
                    }
                }
            }
        }
    }

    function loadPeriodGridSeasonCombo()
    {

        var cbo = grid_period.getColumnCombo(grid_period.getColIndexById("season"));
        cbo.clearAll();
        cbo.readonly(true);

        for (var i = 0; i < _dsDatePeriods.dataCount(); i++)
        {
            var item = _dsDatePeriods.item(_dsDatePeriods.idByIndex(i));
            var seasonid = item.seasonfk;
            var season = item.season;

            if (!utils_isIdInCombo(cbo, seasonid))
            {
                cbo.addOption([{value: seasonid, text: season}]);
            }
        }

        cbo.addOption([{value: "", text: "CUSTOM"}]);
    }

    function onFormWeddingAnniversaryChanged(id, value)
    {
        if (id == "wedding_apply_discount_both_basis")
        {
            if (form_wedding_anniversary_discounts.getItemValue("wedding_apply_discount_both_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_anniversary_discounts.disableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_anniversary_discounts.hideItem("wedding_apply_discount_both_sngl_dbl");
            } else
            {
                form_wedding_anniversary_discounts.enableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_anniversary_discounts.showItem("wedding_apply_discount_both_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_groom_basis")
        {
            if (form_wedding_anniversary_discounts.getItemValue("wedding_apply_discount_groom_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_anniversary_discounts.disableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_anniversary_discounts.hideItem("wedding_apply_discount_groom_sngl_dbl");
            } else
            {
                form_wedding_anniversary_discounts.enableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_anniversary_discounts.showItem("wedding_apply_discount_groom_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_bride_basis")
        {
            if (form_wedding_anniversary_discounts.getItemValue("wedding_apply_discount_bride_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_anniversary_discounts.disableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_anniversary_discounts.hideItem("wedding_apply_discount_bride_sngl_dbl");
            } else
            {
                form_wedding_anniversary_discounts.enableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_anniversary_discounts.showItem("wedding_apply_discount_bride_sngl_dbl");
            }
        }
    }

    function onFormWeddingPartyChanged(id, value)
    {
        if (id == "wedding_apply_discount_both_basis")
        {
            if (form_wedding_party_discounts.getItemValue("wedding_apply_discount_both_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_party_discounts.disableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_party_discounts.hideItem("wedding_apply_discount_both_sngl_dbl");
            } else
            {
                form_wedding_party_discounts.enableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_party_discounts.showItem("wedding_apply_discount_both_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_groom_basis")
        {
            if (form_wedding_party_discounts.getItemValue("wedding_apply_discount_groom_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_party_discounts.disableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_party_discounts.hideItem("wedding_apply_discount_groom_sngl_dbl");
            } else
            {
                form_wedding_party_discounts.enableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_party_discounts.showItem("wedding_apply_discount_groom_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_bride_basis")
        {
            if (form_wedding_party_discounts.getItemValue("wedding_apply_discount_bride_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_party_discounts.disableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_party_discounts.hideItem("wedding_apply_discount_bride_sngl_dbl");
            } else
            {
                form_wedding_party_discounts.enableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_party_discounts.showItem("wedding_apply_discount_bride_sngl_dbl");
            }
        }
    }

    function onFormWeddingDiscountsChanged(id, value)
    {


        if (id == "wedding_apply_discount_both_basis")
        {
            if (form_wedding_discounts.getItemValue("wedding_apply_discount_both_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_discounts.disableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_discounts.hideItem("wedding_apply_discount_both_sngl_dbl");
            } else
            {
                form_wedding_discounts.enableItem("wedding_apply_discount_both_sngl_dbl");
                form_wedding_discounts.showItem("wedding_apply_discount_both_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_groom_basis")
        {
            if (form_wedding_discounts.getItemValue("wedding_apply_discount_groom_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_discounts.disableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_discounts.hideItem("wedding_apply_discount_groom_sngl_dbl");
            } else
            {
                form_wedding_discounts.enableItem("wedding_apply_discount_groom_sngl_dbl");
                form_wedding_discounts.showItem("wedding_apply_discount_groom_sngl_dbl");
            }
        } else if (id == "wedding_apply_discount_bride_basis")
        {
            if (form_wedding_discounts.getItemValue("wedding_apply_discount_bride_basis") == "FLAT_RATE_PPPN")
            {
                form_wedding_discounts.disableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_discounts.hideItem("wedding_apply_discount_bride_sngl_dbl");
            } else
            {
                form_wedding_discounts.enableItem("wedding_apply_discount_bride_sngl_dbl");
                form_wedding_discounts.showItem("wedding_apply_discount_bride_sngl_dbl");
            }
        }

    }


    //=======================================================
    popupwin_spo.hide();
    popupwin_link.hide();
    popupwin_loadperiods.hide();
    popupwin_capacitycombinations.hide();
    popupwin_flat_rate_testtaxcomm.hide();
    popupwin_newspolink.hide();
    popupwin_loadslinkspo.hide();
    loadPopupDs();
}