var hotels_obj = new hotels();


function hotels()
{
    var popupwin_photo = null;
    var upload_form = null;
    var xhr = null;


    var popupwin_room_photo = null;
    var upload_room_form = null;


    document.getElementById("aTitle").innerHTML = "HOTELS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();


    var grid_hotels = main_layout.cells("a").attachGrid();
    grid_hotels.setIconsPath('libraries/dhtmlx/imgs/');
    grid_hotels.setHeader("Name,Group,Type,Active,Rating,Descrition");
    grid_hotels.setColumnIds("hotelname,grpname,hoteltype,active,num_stars,description");
    grid_hotels.setColTypes("ro,ro,ro,ch,ro,ro");
    grid_hotels.setInitWidths("300,260,100,60,70,*");
    grid_hotels.setColAlign("left,left,left,center,center,left");
    grid_hotels.setColSorting('str,str,str,int,int,str');
    grid_hotels.attachHeader("#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#text_filter");
    grid_hotels.setEditable(false);
    grid_hotels.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");

    toolbar.setIconSize(32);

    var opts = Array(
            Array('modify', 'obj', 'Modify Selected Hotel', 'modify.png'),
            Array('separator1', 'sep'),
            Array('rooms', 'obj', 'Manage <b>Rooms</b>', 'bed.png'),
            Array('dateperiods', 'obj', 'Manage <b>Date Periods</b>', 'gantt_chart.png'),
            Array('contracts', 'obj', 'Manage <b>Contracts</b>', 'contract.png'),
            Array('specialoffers', 'obj', 'Manage <b>Special Offers</b>', 'new_mail.png'),
            Array('inventory', 'obj', 'Manage <b>Inventory</b>', 'inventory.png'),
            Array('separator2', 'sep'),
            Array('delete', 'obj', 'Delete Selected Hotel', 'delete.png')
            );
    toolbar.addButtonSelect("opts", 2, "Operate", opts, "operate.png", "operate.png", null, true);

    toolbar.addButton("export", 3, "Export Excel", "excel.png");

    toolbar.attachEvent("onClick", function (id) {

        if (id == "new")
        {
            newHotel();

        } else if (id == "export")
        {
            grid_hotels.toExcel('php/api/grid-excel-php/generate.php');

        } else if (id == "modify")
        {
            var uid = grid_hotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            modifyHotel(uid);

        } else if (id == "contracts")
        {
            var uid = grid_hotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            hotelContracts(uid);

        } else if (id == "specialoffers")
        {
            var uid = grid_hotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            hotelSpecialOffers(uid);

        } else if (id == "inventory")
        {
            var uid = grid_hotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            hotelInventory(uid);
        } else if (id == "dateperiods")
        {
            var uid = grid_hotels.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            hotelDatePeriods(uid);

        } else if (id == "delete")
        {
            var gid = grid_hotels.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Hotel",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/bckoffhotels/deletehotel.php", params, function (loader) {

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
                                    grid_hotels.deleteRow(gid);
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
        } else if (id == "rooms")
        {

            var gid = grid_hotels.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            loadHotelRooms(gid, "-1");

            popupwin_rooms.setModal(true);
            popupwin_rooms.center();
            popupwin_rooms.show();
        }
    });


    var dsHotels = new dhtmlXDataStore();
    dsHotels.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_hotels.sync(dsHotels);

        grid_hotels.forEachRow(function (rwid) {
            grid_hotels.forEachCell(rwid, function (c, ind) {
                var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                grid_hotels.setCellTextStyle(rwid, ind, cellstyle);
            });
        });

        if (select_hotel_id != "")
        {
            grid_hotels.selectRowById(select_hotel_id, false, true, false);
        }

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

            //====================


            $("#main_body").height(y);
            $("#main_body").width(x - 20);

            main_layout.setSizes(true);

        }, 1);
    }


    var dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo(main_layout.cells("a"));

    var popupwin_hotels = dhxWins.createWindow("popupwin_hotels", 50, 50, 700, 440);
    popupwin_hotels.setText("Hotel Details:");

    var x = $("#main_body").parent().width() - 20;
    var y = $("#main_body").parent().height() - 25;
    popupwin_hotels.setDimension(x, y);

    popupwin_hotels.denyResize();
    popupwin_hotels.denyPark();
    popupwin_hotels.button("close").hide();


    //======================

    var popupwin_rooms = dhxWins.createWindow("popupwin_rooms", 50, 50, 1000, 440);
    popupwin_rooms.setText("Hotel Rooms:");

    var y = $("#main_body").parent().height() - 50;
    popupwin_rooms.setDimension(1000, y);

    popupwin_rooms.denyResize();
    popupwin_rooms.denyPark();
    popupwin_rooms.button("close").hide();



    //========================
    var popupwin_facilities = dhxWins.createWindow("popupwin_facilities", 50, 50, 1000, 440);
    popupwin_facilities.setText("Hotel Facilities:");

    var y = $("#main_body").parent().height() - 50;
    popupwin_facilities.setDimension(1000, y);

    popupwin_facilities.denyResize();
    popupwin_facilities.denyPark();
    popupwin_facilities.button("close").hide();

    //========================



    var popupwin_add_facilities = dhxWins.createWindow("popupwin_facilities", 50, 50, 600, 200);
    popupwin_add_facilities.setText("Add Hotel Facilities:");
    popupwin_add_facilities.denyResize();
    popupwin_add_facilities.denyPark();
    popupwin_add_facilities.button("close").hide();


//========================
    var popupwin_rooms_addmod = dhxWins.createWindow("popupwin_rooms_addmod", 50, 50, 800, 500);
    popupwin_rooms_addmod.setText("Room Details:");

    var y = $("#main_body").parent().height() - 50;
    popupwin_rooms.setDimension(800, y);

    popupwin_rooms_addmod.denyResize();
    popupwin_rooms_addmod.denyPark();


    //========================

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {

        if (win.getId() == "popupwin_photo")
        {
            win.setModal(false);
            popupwin_hotels.setModal(true);
            popupwin_photo.detachObject();
            upload_form.parentNode.removeChild(upload_form);
            upload_form = null;
            return true;
        } else if (win.getId() == "popupwin_room_photo")
        {
            win.setModal(false);
            popupwin_rooms.setModal(true);
            popupwin_room_photo.detachObject();
            upload_room_form.parentNode.removeChild(upload_room_form);
            upload_room_form = null;
            return true;
        } else if (win.getId() == "popupwin_rooms_addmod")
        {
            win.setModal(false);
            win.hide();
            popupwin_rooms.setModal(true);
            return false;
        } else
        {
            win.setModal(false);
            win.hide();
            return false;
        }

    });

    //================================================================

    var hotellayout = popupwin_hotels.attachLayout("2U");

    hotellayout.cells("a").setWidth(130);
    hotellayout.cells("a").hideHeader();
    hotellayout.cells("b").hideHeader();

    var toolbar_details = hotellayout.cells("b").attachToolbar();
    toolbar_details.setIconsPath("images/");
    toolbar_details.addButton("save", 1, "Save", "save.png", "save.png");
    toolbar_details.addSpacer("save");
    toolbar_details.addButton("exit", 2, "Exit", "exit.png", "exit.png");
    toolbar_details.setIconSize(32);
    toolbar_details.attachEvent("onClick", function (id) {
        if (id == "exit")
        {
            popupwin_hotels.setModal(false);
            popupwin_hotels.hide();

        } else if (id == "save")
        {
            saveHotel();

        }
    });

    var tree_menu = null;


    var tabViews = hotellayout.cells("b").attachTabbar();
    tabViews.addTab("details", "Details", "180px", '');
    tabViews.addTab("description", "Description", "180px", '');
    tabViews.addTab("currencies", "Currencies", "180px", '');
    tabViews.addTab("commission", "Commission", "180px", '');
    tabViews.addTab("contacts", "Contacts", "180px", '');
    tabViews.addTab("facilities", "Facilities", "180px", '');
    tabViews.addTab("images", "Images", "180px", '');


    hideShowTab("-1");
    hideShowTab("details");

    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_hotels"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "checkbox", name: "active", label: "Active:", labelWidth: "50",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"},

        {type: "fieldset", label: "Basic", width: 700, list: [

                {type: "input", name: "hotelname", label: "Hotel Name:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true
                },
                {type: "input", name: "property_name", label: "Property Name:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },
                {type: "input", name: "company_name", label: "Company Name:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
                },

                {type: "combo", name: "hoteltypefk", label: "Hotel Type:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "groupfk", label: "Hotel Group:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "rating", label: "Rating:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/", readonly: true
                },
                {type: "input", name: "website", label: "Website:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10"
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
                {type: "combo", name: "areafk", label: "Area:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/",
                    note: {
                        text: "Dependant on Physical Country Selected"
                    }
                },
                {type: "combo", name: "coastfk", label: "Coast:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "combo", name: "id_transfer_coast", label: "Transfer Coast:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "input", name: "lat", label: "Latitude:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidNumeric"
                },
                {type: "input", name: "lon", label: "Longitude:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    validate: "ValidNumeric"
                },
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

        {type: "fieldset", label: "Miscellaneous", width: 700, hidden: true, list: [

                {type: "combo", name: "ratecode", label: "Rate:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    comboType: "image",
                    comboImagePath: "../../images/"
                },
                {type: "newcolumn"},
                {type: "combo", name: "specialratecode", label: "Special Rate:", labelWidth: "100",
                    labelHeight: "22", inputWidth: "200", inputHeight: "28", labelLeft: "0",
                    labelTop: "10", inputLeft: "10", inputTop: "10",
                    comboType: "image",
                    comboImagePath: "../../images/"
                }]}
    ];

    var form_hotels = tabViews.cells("details").attachForm(str_frm_ug);

    form_hotels.attachEvent("onChange", function (id, value) {
        if (id == "phy_countryfk")
        {
            loadArea(value, "");
        }
    });

    form_hotels.attachEvent("onButtonClick", function (name, command) {
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

    var cboPhyCountry = form_hotels.getCombo("phy_countryfk");
    var cboMailCountry = form_hotels.getCombo("mail_countryfk");

    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/bckoffhotels/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

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
    var cboRating = form_hotels.getCombo("rating");
    var dsRating = new dhtmlXDataStore();
    dsRating.load("php/api/combos/rating_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsRating.dataCount(); i++)
        {
            var item = dsRating.item(dsRating.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboRating.addOption([{value: value, text: txt, img_src: "images/star.png"}]);
        }
        cboRating.readonly(true);
    });


    //==========================



    var cboHotelType = form_hotels.getCombo("hoteltypefk");
    var dsHotelType = new dhtmlXDataStore();
    dsHotelType.load("php/api/combos/hoteltype_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsHotelType.dataCount(); i++)
        {
            var item = dsHotelType.item(dsHotelType.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboHotelType.addOption([{value: value, text: txt, img_src: "images/hoteltype.png"}]);
        }

        cboHotelType.readonly(true);
    });

    //==========================


    var cboArea = form_hotels.getCombo("areafk");
    var dsArea = new dhtmlXDataStore();

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

            if (selectAreaId != "" && utils_isIdInCombo(cboArea, selectAreaId))
            {
                cboArea.setComboValue(selectAreaId);
            }
            cboArea.readonly(false);
            cboArea.enableFilteringMode(true);
        });

    }

    //==========================


    var cboCoast = form_hotels.getCombo("coastfk");
    var cboTransferCoast = form_hotels.getCombo("id_transfer_coast");

    var dsCoast = new dhtmlXDataStore();
    dsCoast.load("php/api/combos/coast_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCoast.dataCount(); i++)
        {
            var item = dsCoast.item(dsCoast.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboCoast.addOption([{value: value, text: txt, img_src: "images/coast.png"}]);
            cboTransferCoast.addOption([{value: value, text: txt, img_src: "images/coast.png"}]);
        }
        cboCoast.readonly(true);
        cboCoast.addOption([{value: "", text: "[SELECT]", img_src: "images/coast.png"}]);

        cboTransferCoast.readonly(true);
        cboTransferCoast.addOption([{value: "", text: "[SELECT]", img_src: "images/coast.png"}]);

    });

    //==========================

    var cboHotelGroup = form_hotels.getCombo("groupfk");
    var dsHotelGroup = new dhtmlXDataStore();

    cboHotelGroup.addOption([{value: "-", text: "None", img_src: "images/hotelgroup.png"}]);
    dsHotelGroup.load("php/api/combos/hotelgroup_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsHotelGroup.dataCount(); i++)
        {
            var item = dsHotelGroup.item(dsHotelGroup.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboHotelGroup.addOption([{value: value, text: txt, img_src: "images/hotelgroup.png"}]);
        }

        cboHotelGroup.readonly(true);
    });

    //===========================


    var cboRateCode = form_hotels.getCombo("ratecode");
    var cboSpecialRateCode = form_hotels.getCombo("specialratecode");

    cboSpecialRateCode.show(false);
    cboRateCode.show(false);

    cboRateCode.addOption([{value: "-", text: "None", img_src: "images/rate_32.png"}]);
    cboSpecialRateCode.addOption([{value: "-", text: "None", img_src: "images/rate_32.png"}]);

    var dsRateCode = new dhtmlXDataStore();

    dsRateCode.load("php/api/combos/rate_code_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsRateCode.dataCount(); i++)
        {
            var item = dsRateCode.item(dsRateCode.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboRateCode.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
            cboSpecialRateCode.addOption([{value: value, text: txt, img_src: "images/rate_32.png"}]);
        }

        cboRateCode.readonly(false);
        cboRateCode.enableFilteringMode(true);

        cboSpecialRateCode.readonly(false);
        cboSpecialRateCode.enableFilteringMode(true);


        cboRateCode.setComboValue("-");
        cboSpecialRateCode.setComboValue("-");

    });


    //===========================================

    var str_frm_notes = [
        {type: "settings", position: "label-left", id: "form_description"},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "description", label: "Description:", labelWidth: "100",
            labelHeight: "22", inputWidth: "650", rows: 10, labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", inputHeight: "150"
        }
    ];

    var form_description = tabViews.cells("description").attachForm(str_frm_notes);

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


                var currencyid = utils_trim(arr_ids[i], " ");

                if (currencyid != "")
                {
                    grid_currency_from.setRowHidden(currencyid, true);
                    grid_currency_from.uncheckAll();

                    new_currency_row_id--;
                    grid_currency_to.addRow(new_currency_row_id, [currencyid, "0", "", "0", "", "", "", "", "ADD", ""]);
                    grid_currency_to.selectRowById(new_currency_row_id, false, true, false);
                    grid_currency_to.setRowTextStyle(new_currency_row_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

                }
            }
        }
    });

    var new_currency_row_id = 0;

    var grid_currency_to = currencylayout.cells("b").attachGrid();
    grid_currency_to.setIconsPath('libraries/dhtmlx/imgs/');
    grid_currency_to.setHeader("Currency,Default,Tax,Terms Value,Terms Code,Bank,Bank Acc,Acc Name,Action,Valid");
    grid_currency_to.setColumnIds("currencyid,use_default,tax_code,terms_value,terms_code,bankfk,bankaccount,accountname,action,valid");
    grid_currency_to.setColTypes("coro,ch,coro,edn,coro,coro,ed,ed,ro,ro");
    grid_currency_to.setInitWidths("80,60,120,60,120,100,100,100,100,100");
    grid_currency_to.setColAlign("left,center,left,right,left,left,left,left");
    grid_currency_to.setColSorting('str,int,str,int,str,str,str,str,str,str');
    grid_currency_to.setEditable(true);
    grid_currency_to.enableValidation(true);
    grid_currency_to.setColValidators(",,NotEmpty,ValidNumeric,NotEmpty,,,,,");
    grid_currency_to.enableEditEvents(true, false, true);
    grid_currency_to.init();


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


    //=============================================================
    var commissionlayout = tabViews.cells("commission").attachLayout("1C");
    commissionlayout.cells("a").hideHeader();

    var new_commission_row_id = 0;

    var grid_commission = commissionlayout.cells("a").attachGrid();
    grid_commission.setIconsPath('libraries/dhtmlx/imgs/');
    grid_commission.setHeader("Date From, Date To,Tax,Tax Amt,Commission (%),Mark Up (%),Action,Valid");
    grid_commission.setColumnIds("dtfrom,dtto,taxcode_fk,taxamt,commission,markup,action,valid");
    grid_commission.setColTypes("dhxCalendar,dhxCalendar,coro,edn,edn,edn,ro,ro");
    grid_commission.setInitWidths("120,120,200,100,100,100,0,0");
    grid_commission.setColAlign("left,left,left,right,right,right,left,left");
    grid_commission.setColSorting('date,date,str,int,int,int,str,str');
    grid_commission.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
    grid_commission.setEditable(true);
    grid_commission.enableValidation(true);
    grid_commission.setColValidators("ValidDate,ValidDate,NotEmpty,ValidNumeric,ValidNumeric,ValidNumeric,,");
    grid_commission.enableEditEvents(true, false, true);
    grid_commission.init();

    var dsCommissionGrid = new dhtmlXDataStore();
    loadCommissionGridCombos(true, "-1");

    var toolbar_commission = commissionlayout.cells("a").attachToolbar();
    toolbar_commission.setIconsPath("images/");
    toolbar_commission.addButton("add", 1, "Add Commission", "add.png", "add.png");
    toolbar_commission.addButton("delete", 2, "Delete Commission", "delete.png", "delete.png");
    toolbar_commission.setIconSize(32);

    toolbar_commission.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            new_commission_row_id--;
            grid_commission.addRow(new_commission_row_id, ["", "", "", "0.00", "0.00", "0.00", "ADD", ""]);
            grid_commission.selectRowById(new_commission_row_id, false, true, false);
            grid_commission.setRowTextStyle(new_commission_row_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        } else if (id == "delete")
        {
            var rid = grid_commission.getSelectedRowId();
            if (rid)
            {
                dhtmlx.confirm({
                    title: "Delete Commission",
                    type: "confirm",
                    text: "Confirm Deletion?",
                    callback: function (tf) {
                        if (tf)
                        {
                            grid_commission.cells(rid, grid_commission.getColIndexById("action")).setValue("DELETE");
                            grid_commission.setRowHidden(rid, true);
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

    var dsImages = new dhtmlXDataStore();

    var imagelayout = tabViews.cells("images").attachLayout("1C");
    imagelayout.cells("a").hideHeader();

    var grid_images = imagelayout.cells("a").attachGrid();
    grid_images.setIconsPath('libraries/dhtmlx/imgs/');
    grid_images.setHeader("Photo,Default,Description");
    grid_images.setColumnIds("image_name_url,isdefault,image_description");
    grid_images.setColTypes("ro,ch,ed");
    grid_images.setInitWidths("206,80,*");
    grid_images.setColAlign("center,center,left");
    grid_images.setColSorting('str,int,str');
    grid_images.enableEditEvents(true, false, true);
    grid_images.attachEvent("onEditCell", onImageGridEdit);
    grid_images.attachEvent("onCheck", onImageGridCheck);
    grid_images.init();




    var toolbar_images = imagelayout.cells("a").attachToolbar();
    toolbar_images.setIconsPath("images/");
    toolbar_images.addButton("add", 1, "Add Image", "add.png", "add.png");
    toolbar_images.addButton("delete", 2, "Delete Image", "delete.png", "delete.png");
    toolbar_images.setIconSize(32);
    toolbar_images.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            imageUpload();
        } else if (id == "delete")
        {
            deleteImage();
        }
    });

    //==============================================================
    var roomlayout = popupwin_rooms.attachLayout("1C");
    roomlayout.cells("a").hideHeader();

    var dsHotelRooms = new dhtmlXDataStore();


    var grid_rooms = roomlayout.cells("a").attachGrid();
    grid_rooms.setIconsPath('libraries/dhtmlx/imgs/');
    grid_rooms.setHeader("Category,Num Bedrooms,Description");
    grid_rooms.setColumnIds("roomname,numbedrooms,description");
    grid_rooms.setColTypes("ro,ro,ro");
    grid_rooms.setInitWidths("200,100,*");
    grid_rooms.setColAlign("left,center,left");
    grid_rooms.setColSorting('str,int,str');
    grid_rooms.attachHeader("#text_filter,#select_filter,#text_filter");
    grid_rooms.init();

    var toolbar_rooms = roomlayout.cells("a").attachToolbar();
    toolbar_rooms.setIconsPath("images/");
    toolbar_rooms.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_rooms.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_rooms.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_rooms.addSpacer("delete");
    toolbar_rooms.addButton("exit", 4, "Exit", "exit.png", "exit.png");
    toolbar_rooms.setIconSize(32);

    toolbar_rooms.attachEvent("onClick", function (id) {

        if (id == "exit")
        {
            popupwin_rooms.setModal(false);
            popupwin_rooms.hide();

        } else if (id == "new")
        {
            newHotelRoom();
        } else if (id == "modify")
        {
            var roomid = grid_rooms.getSelectedRowId();
            if (!roomid)
            {
                return;
            }

            modifyHotelRoom(roomid);
        }
    });


    //==============================================================

    var roomdetails_layout = popupwin_rooms_addmod.attachLayout("2U")

    roomdetails_layout.cells("a").setWidth(130);
    roomdetails_layout.cells("a").hideHeader();
    roomdetails_layout.cells("b").hideHeader();

    var tree_rooms = null;

    var tabRooms = roomdetails_layout.cells("b").attachTabbar();
    tabRooms.addTab("details", "Details", "180px", '');
    tabRooms.addTab("images", "Images", "180px", '');

    var str_frm_rooms = [
        {type: "settings", position: "label-left", id: "form_rooms"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "hidden", name: "hotelfk"},
        {type: "input", name: "roomname", label: "Room Category:", labelWidth: "130",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "numbedrooms", label: "Num Bedrooms:", labelWidth: "130",
            labelHeight: "22", inputWidth: "400", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10",
            validate: "ValidInteger"
        },
        {type: "editor", name: "description", label: "Description:", labelWidth: "130",
            labelHeight: "22", inputWidth: "400", inputHeight: "200", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdSave", value: "Save Room Category", width: "180", offsetLeft: 0},
    ];


    var form_rooms = tabRooms.cells("details").attachForm(str_frm_rooms);
    form_rooms.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdSave")
        {
            if (!form_rooms.validate())
            {
                dhtmlx.alert({
                    text: "Please fill in highlighted fields correctly!",
                    type: "alert-warning",
                    title: "SAVE",
                    callback: function () {
                    }
                });

                return;
            }

            var numbedrooms = form_rooms.getItemValue("numbedrooms");
            if (!isNaN(numbedrooms))
            {
                numbedrooms = parseInt(numbedrooms, 10);
                if (numbedrooms < 0)
                {
                    dhtmlx.alert({
                        text: "Please enter a valid numeric field!",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            form_rooms.setItemFocus("numbedrooms");
                        }
                    });
                    return;
                }
            }



            roomdetails_layout.cells("b").progressOn();

            form_rooms.setItemValue("token", global_token);

            form_rooms.send("php/api/bckoffhotels/saveroom.php", "post", function (loader)
            {
                roomdetails_layout.cells("b").progressOff();
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

                        loadHotelRooms(grid_hotels.getSelectedRowId(), json_obj.ID);
                        modifyHotelRoom(json_obj.ID);

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


    var dsRoomImages = new dhtmlXDataStore();

    var roomimagelayout = tabRooms.cells("images").attachLayout("1C");
    roomimagelayout.cells("a").hideHeader();

    var grid_room_images = roomimagelayout.cells("a").attachGrid();
    grid_room_images.setIconsPath('libraries/dhtmlx/imgs/');
    grid_room_images.setHeader("Photo,Default,Description");
    grid_room_images.setColumnIds("image_name_url,isdefault,image_description");
    grid_room_images.setColTypes("ro,ch,ed");
    grid_room_images.setInitWidths("206,80,*");
    grid_room_images.setColAlign("center,center,left");
    grid_room_images.setColSorting('str,int,str');
    grid_room_images.enableEditEvents(true, false, true);
    grid_room_images.attachEvent("onEditCell", onRoomImageGridEdit);
    grid_room_images.attachEvent("onCheck", onRoomImageGridCheck);
    grid_room_images.init();




    var toolbar_room_images = roomimagelayout.cells("a").attachToolbar();
    toolbar_room_images.setIconsPath("images/");
    toolbar_room_images.addButton("add", 1, "Add Image", "add.png", "add.png");
    toolbar_room_images.addButton("delete", 2, "Delete Image", "delete.png", "delete.png");
    toolbar_room_images.setIconSize(32);
    toolbar_room_images.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            roomImageUpload();
        } else if (id == "delete")
        {
            deleteRoomImage();
        }
    });


    //=============================================================
    var commissionlayout = tabViews.cells("commission").attachLayout("1C");
    commissionlayout.cells("a").hideHeader();

    var new_commission_row_id = 0;

    var grid_commission = commissionlayout.cells("a").attachGrid();
    grid_commission.setIconsPath('libraries/dhtmlx/imgs/');
    grid_commission.setHeader("Date From, Date To,Tax,Tax Amt,Commission (%),Mark Up (%),Action,Valid");
    grid_commission.setColumnIds("dtfrom,dtto,taxcode_fk,taxamt,commission,markup,action,valid");
    grid_commission.setColTypes("dhxCalendar,dhxCalendar,coro,edn,edn,edn,ro,ro");
    grid_commission.setInitWidths("120,120,200,100,100,100,0,0");
    grid_commission.setColAlign("left,left,left,right,right,right,left,left");
    grid_commission.setColSorting('date,date,str,int,int,int,str,str');
    grid_commission.setDateFormat("%d-%M-%Y", "%Y-%m-%d");
    grid_commission.setEditable(true);
    grid_commission.enableValidation(true);
    grid_commission.setColValidators("ValidDate,ValidDate,NotEmpty,ValidNumeric,ValidNumeric,ValidNumeric,,");
    grid_commission.enableEditEvents(true, false, true);
    grid_commission.init();

    var dsCommissionGrid = new dhtmlXDataStore();
    loadCommissionGridCombos(true, "-1");

    var toolbar_commission = commissionlayout.cells("a").attachToolbar();
    toolbar_commission.setIconsPath("images/");
    toolbar_commission.addButton("add", 1, "Add Commission", "add.png", "add.png");
    toolbar_commission.addButton("delete", 2, "Delete Commission", "delete.png", "delete.png");
    toolbar_commission.setIconSize(32);

    toolbar_commission.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            new_commission_row_id--;
            grid_commission.addRow(new_commission_row_id, ["", "", "", "0.00", "0.00", "0.00", "ADD", ""]);
            grid_commission.selectRowById(new_commission_row_id, false, true, false);
            grid_commission.setRowTextStyle(new_commission_row_id, "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;");

        } else if (id == "delete")
        {
            var rid = grid_commission.getSelectedRowId();
            if (rid)
            {
                dhtmlx.confirm({
                    title: "Delete Commission",
                    type: "confirm",
                    text: "Confirm Deletion?",
                    callback: function (tf) {
                        if (tf)
                        {
                            grid_commission.cells(rid, grid_commission.getColIndexById("action")).setValue("DELETE");
                            grid_commission.setRowHidden(rid, true);
                        }
                    }});
            }
        }
    });


    //==============================================================

    var facilitieslayout = tabViews.cells("facilities").attachLayout("1C");
    facilitieslayout.cells("a").hideHeader();


    var grid_facilities = facilitieslayout.cells("a").attachGrid();
    grid_facilities.setIconsPath('libraries/dhtmlx/imgs/');
    grid_facilities.setHeader("Facility,Description");
    grid_facilities.setColumnIds("facility,description");
    grid_facilities.setColTypes("ro,ro");
    grid_facilities.setInitWidths("200,*");
    grid_facilities.setColAlign("left,left");
    grid_facilities.setColSorting('str,str');
    grid_facilities.init();

    var dsFacilityGrid = new dhtmlXDataStore();

    var toolbar_facility = facilitieslayout.cells("a").attachToolbar();
    toolbar_facility.setIconsPath("images/");
    toolbar_facility.addButton("add", 1, "Add/Manage Facilities", "add.png", "add.png");
    toolbar_facility.addButton("delete", 2, "Delete Facility", "delete.png", "delete.png");
    toolbar_facility.setIconSize(32);

    toolbar_facility.attachEvent("onClick", function (id) {

        if (id == "add")
        {
            loadAddFacilitiesGrid(grid_hotels.getSelectedRowId());

            popupwin_hotels.setModal(false);
            popupwin_facilities.setModal(true);
            popupwin_facilities.show();
            popupwin_facilities.center();


        } else if (id == "delete")
        {
            var fid = grid_facilities.getSelectedRowId();
            if (!fid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Facility",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "fid=" + fid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/bckoffhotels/deletehotelfacility.php", params, function (loader) {

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
                                    grid_facilities.deleteRow(fid);
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


    var addfacilitieslayout = popupwin_facilities.attachLayout("1C");
    addfacilitieslayout.cells("a").hideHeader();

    var grid_add_facilities = addfacilitieslayout.cells("a").attachGrid();
    grid_add_facilities.setIconsPath('libraries/dhtmlx/imgs/');
    grid_add_facilities.setHeader(",Facility,Description");
    grid_add_facilities.setColumnIds("X,facility,description");
    grid_add_facilities.setColTypes("ch,ro,ro");
    grid_add_facilities.setInitWidths("30,200,*");
    grid_add_facilities.setColAlign("center,left,left");
    grid_add_facilities.setColSorting('int,str,str');
    grid_add_facilities.attachHeader("#master_checkbox,#text_filter,#text_filter");
    grid_add_facilities.attachEvent("onRowDblClicked", modifyFacilityItem);
    grid_add_facilities.init();


    var dsAddFacilityGrid = new dhtmlXDataStore();

    var toolbar_add_facility = addfacilitieslayout.cells("a").attachToolbar();
    toolbar_add_facility.setIconsPath("images/");
    toolbar_add_facility.addButton("attach", 1, "Attach Facilities to Hotel", "save.png", "save.png");
    toolbar_add_facility.addButton("new", 2, "Add Item to Database", "add.png", "add.png");
    toolbar_add_facility.addButton("delete", 3, "Delete Item From Database", "delete.png", "delete.png");
    toolbar_add_facility.addSpacer("delete");
    toolbar_add_facility.addButton("exit", 4, "Back", "exit.png", "exit.png");
    toolbar_add_facility.setIconSize(32);


    toolbar_add_facility.attachEvent("onClick", function (id) {

        if (id == "attach")
        {
            var itemids = grid_add_facilities.getCheckedRows(grid_add_facilities.getColIndexById("X"));
            if (itemids == "")
            {
                dhtmlx.alert({
                    text: "Please Select at Least one Facility!",
                    type: "alert-warning",
                    title: "Attach Facilities",
                    callback: function () {
                    }
                });

                return;
            }

            var params = "itemids=" + itemids + "&t=" + encodeURIComponent(global_token) +
                    "&hotelid=" + grid_hotels.getSelectedRowId();
            dhtmlxAjax.post("php/api/bckoffhotels/attachfacility.php", params, function (loader) {

                if (loader)
                {
                    if (loader.xmlDoc.responseURL == "")
                    {
                        dhtmlx.alert({
                            text: "Connection Lost!",
                            type: "alert-warning",
                            title: "Attach Facilities",
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
                            title: "Attach Facilities",
                            callback: function () {
                            }
                        });
                        return false;
                    }
                    if (json_obj.OUTCOME == "OK")
                    {
                        popupwin_facilities.setModal(false);
                        popupwin_facilities.hide();
                        popupwin_hotels.setModal(true);
                        loadFacilitiesGrid(grid_hotels.getSelectedRowId());

                    } else
                    {
                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "Attach Facilities",
                            callback: function () {
                            }
                        });
                    }

                }
            });


        } else if (id == "new")
        {
            popupwin_add_facilities.center();
            popupwin_add_facilities.show();


        } else if (id == "exit")
        {
            popupwin_facilities.setModal(false);
            popupwin_facilities.hide();
            popupwin_hotels.setModal(true);
        } else if (id == "delete")
        {
            var fid = grid_add_facilities.getSelectedRowId();
            if (!fid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Facility",
                type: "confirm",
                text: "Confirm Deletion of Item from Facilities Database?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "fid=" + fid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/bckoffhotels/deletefacilityitem.php", params, function (loader) {

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
                                    grid_add_facilities.deleteRow(fid);
                                    loadFacilitiesGrid(grid_hotels.getSelectedRowId());

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

    var str_frm_add_item = [
        {type: "settings", position: "label-left", id: "form_add_item"},
        {type: "hidden", name: "id"},
        {type: "input", name: "facility", label: "Facility:", labelWidth: "100",
            labelHeight: "22", inputWidth: "400", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "description", label: "Description:", labelWidth: "100",
            labelHeight: "22", inputWidth: "400", rows: 3, labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "button", name: "cmdSave", value: "Save", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", offsetLeft: 0}
    ];

    var form_add_item = popupwin_add_facilities.attachForm(str_frm_add_item);

    form_add_item.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdSave")
        {
            if (!form_add_item.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Facility",
                    callback: function () {}
                });
                return false;
            }

            var data = form_add_item.getFormData();

            var params = "token=" + encodeURIComponent(global_token) +
                    "&data=" + encodeURIComponent(JSON.stringify(data)) +
                    "&hotelid=" + grid_hotels.getSelectedRowId();

            dhtmlxAjax.post("php/api/bckoffhotels/savefacility.php", params, function (loader) {
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
                        return false;
                    }

                    //console.log(loader.xmlDoc.responseText);

                    var json_obj = utils_response_extract_jsonobj(loader, false, "", "");


                    if (!json_obj)
                    {
                        dhtmlx.alert({
                            text: loader.xmlDoc.responseText,
                            type: "alert-warning",
                            title: "SAVE",
                            callback: function () {}
                        });
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.alert({
                            text: "Save Successful",
                            type: "alert",
                            title: "SAVE",
                            callback: function () {}
                        });
                        form_add_item.clear();
                        form_add_item.setItemValue("id", "-1");
                        loadAddFacilitiesGrid(grid_hotels.getSelectedRowId());


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



        } else if (name == "cmdCancel")
        {

            popupwin_add_facilities.setModal(false);
            popupwin_add_facilities.hide();
            popupwin_facilities.setModal(true);
        }
    });

    function modifyFacilityItem(rId, cInd) {
        var data = dsAddFacilityGrid.item(rId);
        form_add_item.setFormData(data);
        popupwin_add_facilities.center();
        popupwin_add_facilities.show();
    }
    //=========================================================


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
            } else if (json_rights[i].PROCESSNAME == "ADD ROOM" && json_rights[i].ALLOWED == "N")
            {

                toolbar_rooms.disableItem("new");
                toolbar_rooms.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY ROOM" && json_rights[i].ALLOWED == "N")
            {

                toolbar_rooms.disableItem("modify");
                toolbar_rooms.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE ROOM" && json_rights[i].ALLOWED == "N")
            {

                toolbar_rooms.disableItem("delete");
                toolbar_rooms.setItemToolTip("delete", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "CONTRACTS" && json_rights[i].ALLOWED == "N")
            {

                toolbar.disableListOption("opts", "contracts");
                toolbar.setListOptionToolTip("opts", "contracts", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DATE PERIODS" && json_rights[i].ALLOWED == "N")
            {

                toolbar.disableListOption("opts", "dateperiods");
                toolbar.setListOptionToolTip("opts", "dateperiods", "Not Allowed");
            }

        }
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


    function loadCommissionGridCombos(tf_load_commission_grid, hoid)
    {
        var ds = new dhtmlXDataStore();

        ds = null;
        ds = new dhtmlXDataStore();
        ds.load("php/api/bckoffhotels/taxcode_combo.php?t=" + global_token, "json", function () {
            grid_commission.getCombo(grid_commission.getColIndexById("taxcode_fk")).put("", " ");
            for (var i = 0; i < ds.dataCount(); i++)
            {
                var item = ds.item(ds.idByIndex(i));
                grid_commission.getCombo(grid_commission.getColIndexById("taxcode_fk")).put(item.value, item.text);
            }

            //finally load grid data
            if (tf_load_commission_grid)
            {
                loadCommissionGrid(hoid);
            }

        });
    }

    function loadContactGridCombos(tf_load_contact_grid, hoid)
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
                loadContactGrid(hoid);
            }

        });

    }

    function loadCurrencyGridCombos(tf_load_currency_grid, hoid)
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
            loadCurrencyFromGrid(hoid);
        }
    }

    function loadCommissionGrid(hoid)
    {
        dsCommissionGrid.clearAll();
        dsCommissionGrid = null;
        dsCommissionGrid = new dhtmlXDataStore();
        commissionlayout.cells("a").progressOn();
        grid_commission.clearAll();

        dsCommissionGrid.load("php/api/bckoffhotels/commissiongrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            commissionlayout.cells("a").progressOff();
            grid_commission.sync(dsCommissionGrid);
            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_commission, css)
        });
    }

    function loadAddFacilitiesGrid(hoid)
    {
        dsAddFacilityGrid.clearAll();
        dsAddFacilityGrid = null;
        dsAddFacilityGrid = new dhtmlXDataStore();
        addfacilitieslayout.cells("a").progressOn();
        grid_add_facilities.clearAll();

        dsAddFacilityGrid.load("php/api/bckoffhotels/addfacilitiesgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            addfacilitieslayout.cells("a").progressOff();
            grid_add_facilities.sync(dsAddFacilityGrid);
            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_add_facilities, css)
        });
    }
    function loadFacilitiesGrid(hoid)
    {
        dsFacilityGrid.clearAll();
        dsFacilityGrid = null;
        dsFacilityGrid = new dhtmlXDataStore();
        facilitieslayout.cells("a").progressOn();
        grid_facilities.clearAll();

        dsFacilityGrid.load("php/api/bckoffhotels/facilitiesgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            facilitieslayout.cells("a").progressOff();
            grid_facilities.sync(dsFacilityGrid);
            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_facilities, css)
        });
    }

    function loadContactGrid(hoid)
    {
        dsContactGrid.clearAll();
        dsContactGrid = null;
        dsContactGrid = new dhtmlXDataStore();
        contactlayout.cells("a").progressOn();
        grid_contact.clearAll();

        dsContactGrid.load("php/api/bckoffhotels/contactgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            contactlayout.cells("a").progressOff();
            grid_contact.sync(dsContactGrid);

            var css = "border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
            utils_formatGridRows(grid_contact, css)
        });
    }

    function loadCurrencyFromGrid(hoid)
    {
        dsCurrencyFromGrid.clearAll();
        dsCurrencyFromGrid = null;
        dsCurrencyFromGrid = new dhtmlXDataStore();
        currencylayout.cells("a").progressOn();
        grid_currency_from.clearAll();
        dsCurrencyFromGrid.load("php/api/bckoffhotels/currencyfromgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            currencylayout.cells("a").progressOff();
            grid_currency_from.sync(dsCurrencyFromGrid);

            loadCurrencyGrid(hoid);
        });
    }

    function loadCurrencyGrid(hoid)
    {
        dsCurrencyGrid.clearAll();
        dsCurrencyGrid = null;
        dsCurrencyGrid = new dhtmlXDataStore();

        currencylayout.cells("b").progressOn();

        grid_currency_to.clearAll();

        dsCurrencyGrid.load("php/api/bckoffhotels/currencygrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
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

            var from_val = form_hotels.getItemValue(fldname_from);

            form_hotels.setItemValue(fldname_to, from_val);
        }

        if (from == "phy")
        {
            if (utils_isIdInCombo(cboMailCountry, cboPhyCountry.getSelectedValue()))
            {
                cboMailCountry.setComboValue(cboPhyCountry.getSelectedValue());
            }

        } else
        {
            if (utils_isIdInCombo(cboPhyCountry, cboMailCountry.getSelectedValue()))
            {
                cboPhyCountry.setComboValue(cboMailCountry.getSelectedValue());
            }
        }

    }

    function modifyHotel(hoid)
    {
        tree_menu = null;
        tree_menu = hotellayout.cells("a").attachTree();
        tree_menu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_menu.setIconSize('20px', '20px');
        tree_menu.attachEvent("onSelect", function (id) {
            hideShowTab(id);
        });

        main_layout.cells("a").progressOn();
        tree_menu.loadXML("php/api/bckoffhotels/tree_menu.php?t=" + global_token + "&action=MODIFY", function () {
            main_layout.cells("a").progressOff();

            form_hotels.clear();
            form_description.clear();

            form_hotels.setItemValue("id", hoid);

            cboArea.setComboValue(null);
            cboArea.setComboText("");

            cboHotelGroup.setComboValue("-");
            cboCoast.setComboValue("");

            if (utils_isIdInCombo(cboHotelType, default_hoteltype_id))
            {
                cboHotelType.setComboValue(default_hoteltype_id);
            }


            cboPhyCountry.setComboValue(null);
            cboPhyCountry.setComboText("");

            cboMailCountry.setComboValue(null);
            cboMailCountry.setComboText("");

            cboRateCode.setComboValue("-");
            cboSpecialRateCode.setComboValue("-");


            var data = dsHotels.item(hoid);

            form_hotels.setFormData(data);
            form_description.setFormData(data);

            loadArea(data.phy_countryfk, data.areafk);

            loadCurrencyGridCombos(true, hoid);
            loadContactGridCombos(true, hoid);
            loadCommissionGridCombos(true, hoid);
            loadImageGrid(hoid);
            loadFacilitiesGrid(hoid);

            popupwin_hotels.setModal(true);
            popupwin_hotels.center();
            popupwin_hotels.show();
            popupwin_hotels.setText("Hotel Details: " + form_hotels.getItemValue("hotelname"));


            tree_menu.selectItem("currencies", true, false);
            tree_menu.selectItem("details", true, false);
        });
    }


    function newHotel()
    {
        tree_menu = null;
        tree_menu = hotellayout.cells("a").attachTree();
        tree_menu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_menu.setIconSize('20px', '20px');
        tree_menu.attachEvent("onSelect", function (id) {
            hideShowTab(id);
        });

        main_layout.cells("a").progressOn();
        tree_menu.loadXML("php/api/bckoffhotels/tree_menu.php?t=" + global_token + "&action=NEW", function () {
            main_layout.cells("a").progressOff();

            form_hotels.clear();
            form_description.clear();

            form_hotels.setItemValue("active", "1");
            form_hotels.setItemValue("id", "-1");
            form_hotels.setItemValue("commission", "0.00");
            form_hotels.setItemValue("markup", "0.00");

            form_hotels.setItemFocus("toname");

            cboArea.setComboValue(null);
            cboArea.setComboText("");

            cboHotelGroup.setComboValue("-");
            cboCoast.setComboValue("");

            if (utils_isIdInCombo(cboHotelType, default_hoteltype_id))
            {
                cboHotelType.setComboValue(default_hoteltype_id);
            }


            if (utils_isIdInCombo(cboPhyCountry, default_country_id))
            {
                cboPhyCountry.setComboValue(default_country_id);
            }

            if (utils_isIdInCombo(cboMailCountry, default_country_id))
            {
                cboMailCountry.setComboValue(default_country_id);
            }

            loadArea(default_country_id, "");

            cboRateCode.setComboValue("-");
            cboSpecialRateCode.setComboValue("-");


            loadCurrencyGridCombos(true, "-1");
            loadContactGridCombos(true, "-1");

            popupwin_hotels.setModal(true);
            popupwin_hotels.center();
            popupwin_hotels.show();
            popupwin_hotels.setText("Hotel Details:");

            tabViews.showTab("currencies");
            tabViews.setTabActive("currencies");
            tabViews.hideTab("currencies");
            tabViews.setTabActive("details");
            tree_menu.selectItem("details", true, false);

        });
    }

    function saveHotel()
    {

        var id = form_hotels.getItemValue("id");


        if (!validateDetails())
        {
            return;
        }


        //is it modification of TO?
        if (id != "-1")
        {
            if (!validateCurrencyGrid())
            {
                return;
            }

            if (!validateCommissionGrid())
            {
                return;
            }

            if (!validateContactGrid())
            {
                return;
            }
        }

        hotellayout.cells("b").progressOn();

        var data_details = form_hotels.getFormData();
        var data_notes = form_description.getFormData();
        var json_currencies = utils_dhxSerializeGridToJson(grid_currency_to);
        var json_contacts = utils_dhxSerializeGridToJson(grid_contact);
        var json_commission = utils_dhxSerializeGridToJson(grid_commission);

        var params = "token=" + encodeURIComponent(global_token) +
                "&data_details=" + encodeURIComponent(JSON.stringify(data_details)) +
                "&data_notes=" + encodeURIComponent(JSON.stringify(data_notes)) +
                "&json_currencies=" + encodeURIComponent(json_currencies) +
                "&json_contacts=" + encodeURIComponent(json_contacts) +
                "&json_commission=" + encodeURIComponent(json_commission);


        dhtmlxAjax.post("php/api/bckoffhotels/savehotel.php", params, function (loader) {
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
                    hotellayout.cells("b").progressOff();
                    return false;
                }

                //console.log(loader.xmlDoc.responseText);

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
                    hotellayout.cells("b").progressOff();
                    return false;
                }

                if (json_obj.OUTCOME == "OK")
                {
                    dhtmlx.message({
                        text: "<b><font color='green'>Save Successful!</font></b>",
                        expire: 1500
                    });


                    tree_menu.selectItem("details", true, false);

                    dsHotels.clearAll();
                    grid_hotels.clearAll();

                    dsHotels.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                        grid_hotels.sync(dsHotels);
                        grid_hotels.forEachRow(function (rwid) {
                            grid_hotels.forEachCell(rwid, function (c, ind) {
                                var cellstyle = "font-weight:normal; border-left:1px solid #A4A4A4; border-bottom:1px solid #A4A4A4; border-top:1px solid #A4A4A4; border-right:1px solid #A4A4A4;";
                                grid_hotels.setCellTextStyle(rwid, ind, cellstyle);
                            });
                        });

                        hotellayout.cells("b").progressOff();

                        grid_hotels.selectRowById(json_obj.ID, false, true, false);

                        modifyHotel(json_obj.ID);
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
                    hotellayout.cells("b").progressOff();
                }
            }
        });
    }

    function validateCommissionGrid()
    {


        for (var i = 0; i < grid_commission.getRowsNum(); i++) {

            var id = grid_commission.getRowId(i);
            var dtfrom = grid_commission.cells(id, grid_commission.getColIndexById("dtfrom")).getValue();
            var dtto = grid_commission.cells(id, grid_commission.getColIndexById("dtto")).getValue();
            var taxcode_fk = grid_commission.cells(id, grid_commission.getColIndexById("taxcode_fk")).getValue();
            var taxamt = grid_commission.cells(id, grid_commission.getColIndexById("taxamt")).getValue();
            var commission = grid_commission.cells(id, grid_commission.getColIndexById("commission")).getValue();
            var markup = grid_commission.cells(id, grid_commission.getColIndexById("markup")).getValue();
            var action = grid_commission.cells(id, grid_commission.getColIndexById("action")).getValue();


            if (action != "DELETE")
            {
                if (dtfrom == "" || dtto == "")
                {
                    dhtmlx.alert({
                        text: "Please enter a Date From and a Date To",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                //valid date order
                if (!utils_validateDateOrder(dtfrom, dtto))
                {
                    dhtmlx.alert({
                        text: "Invalid Date From and Date To Order!",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return;
                }

                if (taxcode_fk == "")
                {
                    dhtmlx.alert({
                        text: "Please select a Commission Tax Code",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (taxamt == "" || isNaN(taxamt))
                {
                    dhtmlx.alert({
                        text: "Please enter a Valid Tax Amount",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (commission == "" || isNaN(commission))
                {
                    dhtmlx.alert({
                        text: "Please enter a Valid Commission Amount",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (markup == "" || isNaN(markup))
                {
                    dhtmlx.alert({
                        text: "Please enter a Valid Markup Amount",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {

                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }

                if (!validateCommissionOverlap(id, dtfrom, dtto))
                {
                    dhtmlx.alert({
                        text: "Overlapping Dates Denied!",
                        type: "alert-warning",
                        title: "SAVE",
                        callback: function () {
                            tree_menu.selectItem("commission", true, false);
                            grid_commission.selectRowById(id, false, true, false);
                        }
                    });
                    return false;
                }
            }

        }

        return true;
    }


    function validateCommissionOverlap(id, dtfrom, dtto)
    {
        for (var i = 0; i < grid_commission.getRowsNum(); i++) {

            var inner_id = grid_commission.getRowId(i);
            var inner_dtfrom = grid_commission.cells(inner_id, grid_commission.getColIndexById("dtfrom")).getValue();
            var inner_dtto = grid_commission.cells(inner_id, grid_commission.getColIndexById("dtto")).getValue();
            var inner_action = grid_commission.cells(inner_id, grid_commission.getColIndexById("action")).getValue();

            if (inner_action != "DELETE" && inner_id != id)
            {


                if (inner_dtfrom != "" && inner_dtto != "")
                {
                    if (utils_validateDateOrder(inner_dtfrom, inner_dtto))
                    {

                        //now we can check!
                        var chk1 = utils_validateDateOrder(dtfrom, inner_dtto);
                        var chk2 = utils_validateDateOrder(inner_dtfrom, dtto);
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

    function validateCurrencyGrid()
    {
        //make sure at least one currency entered
        //make sure all records are valid
        //make sure at most one currency is default

        /*
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
         * 
         */

        var default_count = 0;

        for (var i = 0; i < grid_currency_to.getRowsNum(); i++) {


            var id = grid_currency_to.getRowId(i);
            var currencyid = grid_currency_to.cells(id, grid_currency_to.getColIndexById("currencyid")).getValue();
            var tax_code = grid_currency_to.cells(id, grid_currency_to.getColIndexById("tax_code")).getValue();
            var terms_code = grid_currency_to.cells(id, grid_currency_to.getColIndexById("terms_code")).getValue();
            var terms_value = grid_currency_to.cells(id, grid_currency_to.getColIndexById("terms_value")).getValue();
            var use_default = grid_currency_to.cells(id, grid_currency_to.getColIndexById("use_default")).getValue();
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
            }

        }


        if (grid_currency_to.getRowsNum() > 0 && (default_count == 0 || default_count > 1))
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
        if (!form_hotels.validate())
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



        if (!utils_validate_autocompletecombo(cboArea))
        {
            dhtmlx.alert({
                text: "Please select a valid Area!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboArea.openSelect();
                }
            });
            return false;
        }

        if (cboCoast.getSelectedValue() == "-")
        {
            dhtmlx.alert({
                text: "Please select a valid Coast!",
                type: "alert-warning",
                title: "Save Details",
                callback: function () {
                    tree_menu.selectItem("details", true, false);
                    cboCoast.openSelect();
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

    function imageUpload()
    {
        popupwin_hotels.setModal(false);
        popupwin_photo = dhxWins.createWindow("popupwin_photo", 50, 50, 500, 200);
        popupwin_photo.setText("Upload Hotel Photo");
        popupwin_photo.denyResize();
        popupwin_photo.denyPark();
        popupwin_photo.center();
        popupwin_photo.setModal(true);

        upload_form = document.createElement("form");
        upload_form.setAttribute('method', "POST");
        upload_form.setAttribute('action', "php/api/bckoffhotels/uploadphoto.php");
        upload_form.setAttribute('id', "file-form");



        var i = document.createElement("input");
        i.setAttribute('type', "file");
        i.setAttribute('name', "photos[]");
        i.setAttribute('id', "file-select");

        var j = document.createElement("input");
        j.setAttribute('type', "hidden");
        j.setAttribute('name', "hotelid");
        j.setAttribute('id', "hotelid");
        j.setAttribute('value', form_hotels.getItemValue("id"));

        var k = document.createElement("input");
        k.setAttribute('type', "text");
        k.setAttribute('name', "description");
        k.setAttribute('id', "description");
        k.style.width = "350px";


        var label = document.createElement("Label");
        label.htmlFor = "description";
        label.innerHTML = "Description: <font color='red'>*</font>&nbsp;&nbsp;";

        var b = document.createElement("input");
        b.setAttribute('type', "button");
        b.setAttribute('value', "Upload Photo");
        b.setAttribute('id', "upload-button");

        upload_form.appendChild(i);
        upload_form.appendChild(j);
        upload_form.appendChild(document.createElement("BR"));
        upload_form.appendChild(label);
        upload_form.appendChild(k);
        upload_form.appendChild(document.createElement("BR"));
        upload_form.appendChild(document.createElement("BR"));
        upload_form.appendChild(b);


        popupwin_photo.attachObject(upload_form);

        var fileSelect = document.getElementById('file-select');
        var uploadButton = document.getElementById('upload-button');


        document.getElementById("file-form").onkeypress = function (e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
            }
        }

        uploadButton.onclick = function (event) {

            event.preventDefault();

            if (document.getElementById("file-select").files.length == 0) {
                dhtmlx.alert({
                    text: "No Photo Selected to be Uploaded!",
                    type: "alert-warning",
                    title: "Upload Photo",
                    callback: function () {
                    }
                });
                return;
            }

            if (utils_trim(document.getElementById("description").value, " ") == "")
            {
                dhtmlx.alert({
                    text: "Please enter a Photo Description!",
                    type: "alert-warning",
                    title: "Upload Photo",
                    callback: function () {
                        document.getElementById("description").focus();
                    }
                });
                return;
            }

            // Update button text.
            uploadButton.value = 'Uploading...';
            uploadButton.disable = true;

            var files = fileSelect.files;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                // Check the file type, need image type
                if (!file.type.match('image.*')) {
                    continue;
                }

                formData.append('photos', file, file.name);
                formData.append('hotelid', form_hotels.getItemValue("id"));
                formData.append('description', document.getElementById("description").value);
                formData.append('token', global_token);
            }

            xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/api/bckoffhotels/uploadphoto.php', true);

            xhr.onload = function () {

                console.log(xhr);
                if (xhr.status === 200) {
                    if (xhr.responseText.indexOf("OK") != -1)
                    {
                        popupwin_photo.close();

                        dhtmlx.message({
                            text: "Photo Sucessfully Uploaded!",
                            expire: 1500
                        });

                        //reload the image grid
                        loadImageGrid(form_hotels.getItemValue("id"));
                    } else
                    {
                        dhtmlx.alert({
                            text: xhr.responseText,
                            type: "alert-warning",
                            title: "Upload Photo",
                            callback: function () {
                                var uploadButton = document.getElementById('upload-button');
                                uploadButton.disable = false;
                                uploadButton.value = 'Upload Photo';
                            }
                        });
                    }
                } else {
                    dhtmlx.alert({
                        text: "There has been an error while sending the photo to the server!",
                        type: "alert-warning",
                        title: "Upload Photo",
                        callback: function () {
                            var uploadButton = document.getElementById('upload-button');
                            uploadButton.disable = false;
                            uploadButton.value = 'Upload Photo';
                        }
                    });
                }


            };

            xhr.send(formData);
        };
    }

    function deleteImage()
    {
        var id = grid_images.getSelectedId();

        if (!id)
        {
            return;
        }

        dhtmlx.confirm({
            title: "Delete Image",
            type: "confirm",
            text: "Confirm Deletion?",
            callback: function (tf) {
                if (tf)
                {
                    var params = "id=" + id + "&t=" + encodeURIComponent(global_token);
                    dhtmlxAjax.post("php/api/bckoffhotels/deleteimage.php", params, function (loader) {

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
                                grid_images.deleteRow(id);
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

    //==============================================
    function loadImageGrid(hoid)
    {
        grid_images.clearAll();
        dsImages.clearAll();
        dsImages = null;
        dsImages = new dhtmlXDataStore();
        imagelayout.cells("a").progressOn();
        dsImages.load("php/api/bckoffhotels/imagegrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            imagelayout.cells("a").progressOff();
            grid_images.sync(dsImages);

        });
    }

    function onImageGridCheck(rId, cInd, state) {

        if (grid_images.getColIndexById("isdefault") == cInd)
        {
            if (state)
            {
                state = 1;
            } else
            {
                state = 0;
            }

            var params = encodeURI("id=" + rId + "&colid=isdefault&val=" + state + "&t=" + global_token);
            imagelayout.cells("a").progressOn();
            dhtmlxAjax.post("php/api/bckoffhotels/setimagedetails.php", params, function (loader) {
                imagelayout.cells("a").progressOff();

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
                        return true;
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


        return true;
    }

    function onImageGridEdit(stage, rId, cInd, nValue, oValue) {

        if (stage == 2 && oValue != nValue)
        {
            if (grid_images.getColIndexById("image_description") == cInd)
            {
                if (utils_trim(nValue, " ") == "")
                {
                    dhtmlx.alert({
                        text: "Image Description cannot be blank!",
                        type: "alert-warning",
                        title: "Upload Photo",
                        callback: function () {

                        }
                    });

                    return false;
                }

                var params = "id=" + rId + "&colid=image_description&val=" + encodeURIComponent(nValue) + "&t=" + encodeURIComponent(global_token);
                imagelayout.cells("a").progressOn();
                dhtmlxAjax.post("php/api/bckoffhotels/setimagedetails.php", params, function (loader) {
                    imagelayout.cells("a").progressOff();

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
                            return true;
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
        }

        return true;
    }

    //===========================================================================

    function loadHotelRooms(hoid, roomid)
    {
        dsHotelRooms.clearAll();
        dsHotelRooms = null;

        grid_rooms.clearAll();

        dsHotelRooms = new dhtmlXDataStore();

        roomlayout.cells("a").progressOn();
        dsHotelRooms.load("php/api/bckoffhotels/roomgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + hoid, "json", function () {
            roomlayout.cells("a").progressOff();
            grid_rooms.sync(dsHotelRooms);
            grid_rooms.selectRowById(roomid);
        });
    }

    function modifyHotelRoom(roomid) {
        tree_rooms = null;
        tree_rooms = roomdetails_layout.cells("a").attachTree();
        tree_rooms.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_rooms.setIconSize('20px', '20px');
        tree_rooms.attachEvent("onSelect", function (id) {

            var arrTabids = tabRooms.getAllTabs();

            for (var i = 0; i < arrTabids.length; i++)
            {
                if (id == arrTabids[i])
                {
                    tabRooms.showTab(id);
                    tabRooms.setTabActive(id);
                } else
                {
                    tabRooms.hideTab(arrTabids[i]);
                }
            }
        });

        roomdetails_layout.cells("a").progressOn();
        tree_rooms.loadXML("php/api/bckoffhotels/tree_rooms.php?t=" + global_token + "&action=MODIFY", function () {
            roomdetails_layout.cells("a").progressOff();

            form_rooms.clear();

            var data = dsHotelRooms.item(roomid);

            form_rooms.setFormData(data);
            form_rooms.setItemFocus("roomname");

            loadRoomImageGrid(roomid);

            popupwin_rooms.setModal(false);
            popupwin_rooms_addmod.setModal(true);
            popupwin_rooms_addmod.center();
            popupwin_rooms_addmod.show();

            tabRooms.showTab("images");
            tabRooms.setTabActive("images");
            tabRooms.hideTab("images");
            tabRooms.setTabActive("details");
            tree_rooms.selectItem("details", true, false);

        });
    }

    function newHotelRoom()
    {
        tree_rooms = null;
        tree_rooms = roomdetails_layout.cells("a").attachTree();
        tree_rooms.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_rooms.setIconSize('20px', '20px');
        tree_rooms.attachEvent("onSelect", function (id) {

            var arrTabids = tabRooms.getAllTabs();

            for (var i = 0; i < arrTabids.length; i++)
            {
                if (id == arrTabids[i])
                {
                    tabRooms.showTab(id);
                    tabRooms.setTabActive(id);
                } else
                {
                    tabRooms.hideTab(arrTabids[i]);
                }
            }
        });

        roomdetails_layout.cells("a").progressOn();
        tree_rooms.loadXML("php/api/bckoffhotels/tree_rooms.php?t=" + global_token + "&action=NEW", function () {
            roomdetails_layout.cells("a").progressOff();

            form_rooms.clear();

            form_rooms.setItemValue("hotelfk", grid_hotels.getSelectedRowId());
            form_rooms.setItemValue("id", "-1");
            form_rooms.setItemFocus("roomname");

            loadRoomImageGrid("-1");

            popupwin_rooms.setModal(false);
            popupwin_rooms_addmod.setModal(true);
            popupwin_rooms_addmod.center();
            popupwin_rooms_addmod.show();

            tabRooms.showTab("images");
            tabRooms.setTabActive("images");
            tabRooms.hideTab("images");
            tabRooms.setTabActive("details");
            tree_rooms.selectItem("details", true, false);

        });
    }

    function deleteRoomImage()
    {
        var id = grid_room_images.getSelectedId();

        if (!id)
        {
            return;
        }

        dhtmlx.confirm({
            title: "Delete Image",
            type: "confirm",
            text: "Confirm Deletion?",
            callback: function (tf) {
                if (tf)
                {
                    var params = "id=" + id + "&t=" + encodeURIComponent(global_token);
                    dhtmlxAjax.post("php/api/bckoffhotels/deleteroomimage.php", params, function (loader) {

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
                                grid_room_images.deleteRow(id);
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

    function roomImageUpload()
    {
        //var popupwin_room_photo = null;
        //var upload_room_form = null;

        popupwin_rooms.setModal(false);
        popupwin_room_photo = dhxWins.createWindow("popupwin_room_photo", 50, 50, 500, 200);
        popupwin_room_photo.setText("Upload Room Photo");
        popupwin_room_photo.denyResize();
        popupwin_room_photo.denyPark();
        popupwin_room_photo.center();
        popupwin_room_photo.setModal(true);

        upload_room_form = document.createElement("form");
        upload_room_form.setAttribute('method', "POST");
        upload_room_form.setAttribute('action', "php/api/bckoffhotels/uploadroomphoto.php");
        upload_room_form.setAttribute('id', "file-form");



        var i = document.createElement("input");
        i.setAttribute('type', "file");
        i.setAttribute('name', "photos[]");
        i.setAttribute('id', "file-select");

        var j = document.createElement("input");
        j.setAttribute('type', "hidden");
        j.setAttribute('name', "roomid");
        j.setAttribute('id', "roomid");
        j.setAttribute('value', form_rooms.getItemValue("id"));

        var k = document.createElement("input");
        k.setAttribute('type', "text");
        k.setAttribute('name', "description");
        k.setAttribute('id', "description");
        k.style.width = "350px";


        var label = document.createElement("Label");
        label.htmlFor = "description";
        label.innerHTML = "Description: <font color='red'>*</font>&nbsp;&nbsp;";

        var b = document.createElement("input");
        b.setAttribute('type', "button");
        b.setAttribute('value', "Upload Photo");
        b.setAttribute('id', "upload-button");

        upload_room_form.appendChild(i);
        upload_room_form.appendChild(j);
        upload_room_form.appendChild(document.createElement("BR"));
        upload_room_form.appendChild(label);
        upload_room_form.appendChild(k);
        upload_room_form.appendChild(document.createElement("BR"));
        upload_room_form.appendChild(document.createElement("BR"));
        upload_room_form.appendChild(b);


        popupwin_room_photo.attachObject(upload_room_form);

        var fileSelect = document.getElementById('file-select');
        var uploadButton = document.getElementById('upload-button');


        document.getElementById("file-form").onkeypress = function (e) {
            var key = e.charCode || e.keyCode || 0;
            if (key == 13) {
                e.preventDefault();
            }
        }

        uploadButton.onclick = function (event) {

            event.preventDefault();

            if (document.getElementById("file-select").files.length == 0) {
                dhtmlx.alert({
                    text: "No Photo Selected to be Uploaded!",
                    type: "alert-warning",
                    title: "Upload Photo",
                    callback: function () {
                    }
                });
                return;
            }

            if (utils_trim(document.getElementById("description").value, " ") == "")
            {
                dhtmlx.alert({
                    text: "Please enter a Photo Description!",
                    type: "alert-warning",
                    title: "Upload Photo",
                    callback: function () {
                        document.getElementById("description").focus();
                    }
                });
                return;
            }

            // Update button text.
            uploadButton.value = 'Uploading...';
            uploadButton.disable = true;

            var files = fileSelect.files;
            var formData = new FormData();
            for (var i = 0; i < files.length; i++) {
                var file = files[i];

                // Check the file type, need image type
                if (!file.type.match('image.*')) {
                    continue;
                }

                formData.append('photos', file, file.name);
                formData.append('roomid', form_rooms.getItemValue("id"));
                formData.append('description', document.getElementById("description").value);
                formData.append('token', global_token);
            }

            xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/api/bckoffhotels/uploadroomphoto.php', true);

            xhr.onload = function () {

                console.log(xhr);
                if (xhr.status === 200) {
                    if (xhr.responseText.indexOf("OK") != -1)
                    {
                        popupwin_room_photo.close();

                        dhtmlx.message({
                            text: "Photo Sucessfully Uploaded!",
                            expire: 1500
                        });

                        //reload the image grid
                        loadRoomImageGrid(form_rooms.getItemValue("id"));
                    } else
                    {
                        dhtmlx.alert({
                            text: xhr.responseText,
                            type: "alert-warning",
                            title: "Upload Photo",
                            callback: function () {
                                var uploadButton = document.getElementById('upload-button');
                                uploadButton.disable = false;
                                uploadButton.value = 'Upload Photo';
                            }
                        });
                    }
                } else {
                    dhtmlx.alert({
                        text: "There has been an error while sending the photo to the server!",
                        type: "alert-warning",
                        title: "Upload Photo",
                        callback: function () {
                            var uploadButton = document.getElementById('upload-button');
                            uploadButton.disable = false;
                            uploadButton.value = 'Upload Photo';
                        }
                    });
                }
            };

            xhr.send(formData);
        };
    }

    function loadRoomImageGrid(roomid)
    {
        grid_room_images.clearAll();
        dsRoomImages.clearAll();
        dsRoomImages = null;

        dsRoomImages = new dhtmlXDataStore();
        roomimagelayout.cells("a").progressOn();

        console.log("php/api/bckoffhotels/roomimagegrid.php?t=" + encodeURIComponent(global_token) + "&roomid=" + roomid);

        dsRoomImages.load("php/api/bckoffhotels/roomimagegrid.php?t=" + encodeURIComponent(global_token) + "&roomid=" + roomid, "json", function () {
            roomimagelayout.cells("a").progressOff();
            grid_room_images.sync(dsRoomImages);

        });
    }

    function onRoomImageGridCheck(rId, cInd, state) {

        if (grid_room_images.getColIndexById("isdefault") == cInd)
        {
            if (state)
            {
                state = 1;
            } else
            {
                state = 0;
            }

            var params = encodeURI("id=" + rId + "&colid=isdefault&val=" + state + "&t=" + global_token);
            imagelayout.cells("a").progressOn();
            dhtmlxAjax.post("php/api/bckoffhotels/setroomimagedetails.php", params, function (loader) {
                imagelayout.cells("a").progressOff();

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
                        return true;
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


        return true;
    }

    function onRoomImageGridEdit(stage, rId, cInd, nValue, oValue) {

        if (stage == 2 && oValue != nValue)
        {
            if (grid_images.getColIndexById("image_description") == cInd)
            {
                if (utils_trim(nValue, " ") == "")
                {
                    dhtmlx.alert({
                        text: "Image Description cannot be blank!",
                        type: "alert-warning",
                        title: "Upload Photo",
                        callback: function () {

                        }
                    });

                    return false;
                }

                var params = "id=" + rId + "&colid=image_description&val=" + encodeURIComponent(nValue) + "&t=" + encodeURIComponent(global_token);
                imagelayout.cells("a").progressOn();
                dhtmlxAjax.post("php/api/bckoffhotels/setroomimagedetails.php", params, function (loader) {
                    imagelayout.cells("a").progressOff();

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
                            return true;
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
        }

        return true;
    }

    function hotelInventory(hid)
    {
        window.location.href = "index.php?m=inventory&hid=" + hid;
        return;
    }

    function hotelSpecialOffers(hid)
    {
        window.location.href = "index.php?m=hotelspecialoffers&hid=" + hid;
        return;
    }

    function hotelContracts(hid)
    {
        window.location.href = "index.php?m=hotelcontracts&hid=" + hid;
        return;
    }

    function hotelDatePeriods(hid)
    {
        window.location.href = "index.php?m=dateperiods&hid=" + hid;
        return;
    }


    //==============================================

    popupwin_hotels.hide();
    popupwin_rooms.hide();
    popupwin_rooms_addmod.hide();
    popupwin_facilities.hide();
    popupwin_add_facilities.hide();
    //==============================================

    applyrights();
}