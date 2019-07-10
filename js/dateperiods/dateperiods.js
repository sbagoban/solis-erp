var dateperiods_obj = new dateperiods();

function dateperiods()
{

    document.getElementById("aTitle").innerHTML = "DATE PERIODS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_dateperiods = main_layout.cells("a").attachGrid();
    grid_dateperiods.setIconsPath('libraries/dhtmlx/imgs/');
    grid_dateperiods.setHeader("Check In,Check Out,Season,Active,theyear");
    grid_dateperiods.setColumnIds("checkin_disp,checkout_disp,season,active,theyear");
    grid_dateperiods.setColTypes("ro,ro,ro,ch,ro");
    grid_dateperiods.setInitWidths("120,120,200,50,0");
    grid_dateperiods.setColAlign("left,left,left,center,left");
    grid_dateperiods.setColSorting('date,date,str,int,int');
    grid_dateperiods.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,");
    grid_dateperiods.setEditable(false);
    grid_dateperiods.groupBy(grid_dateperiods.getColIndexById("theyear"));
    grid_dateperiods.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.addButton("export", 4, "Export Excel", "excel.png");
    toolbar.addSpacer("export");
    toolbar.addButton("back", 5, "Back to Hotels", "exit.png", "exit.png");
    toolbar.setIconSize(32);

    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "back")
        {
            window.location = "index.php?m=bckoffhotels&hid=" + global_hotel_id;

        }
        else if (id == "new")
        {
            form_dateperiods.clear();
            form_dateperiods.setItemValue("id", "-1");
            form_dateperiods.setItemValue("active", "1");
            form_dateperiods.hideItem("chkCreateforGroup");

            loadHotelCombo(global_hotel_id);
            
            form_dateperiods.getCalendar("checkin").clearSensitiveRange();
            form_dateperiods.getCalendar("checkout").clearSensitiveRange();
            
            
            
            popupwin_dateperiods.setModal(true);
            popupwin_dateperiods.center();
            popupwin_dateperiods.show();
            
            lookupGroupDetails();


        } else if (id == "modify")
        {
            var uid = grid_dateperiods.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsDatePeriods.item(uid);
            form_dateperiods.setFormData(data);
            form_dateperiods.hideItem("chkCreateforGroup");
            
            loadHotelCombo(data.hotelfk);
            
            form_dateperiods.getCalendar("checkin").clearSensitiveRange();
            form_dateperiods.getCalendar("checkout").clearSensitiveRange();
            checkRange("checkin", form_dateperiods.getItemValue("checkin"));
            checkRange("checkout", form_dateperiods.getItemValue("checkout"));

            popupwin_dateperiods.setModal(true);
            popupwin_dateperiods.center();
            popupwin_dateperiods.show();


        } else if (id == "delete")
        {
            var gid = grid_dateperiods.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Date Period",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/dateperiods/deletedateperiod.php", params, function (loader) {

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
                                    grid_dateperiods.deleteRow(gid);
                                    grid_dateperiods.groupBy(grid_dateperiods.getColIndexById("theyear"));
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
        else if (id == "export")
        {
            grid_dateperiods.toExcel('php/api/grid-excel-php/generate.php');
        }
    });
    
    var dsHotelName = new dhtmlXDataStore();
    dsHotelName.load("php/api/bckoffhotels/hotelgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
        document.getElementById("aTitle").innerHTML = "DATE PERIODS: <b>" + dsHotelName.item(global_hotel_id).hotelname + "</b>";
    });

    var dsDatePeriods = new dhtmlXDataStore();
    dsDatePeriods.load("php/api/dateperiods/dateperiodgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
        grid_dateperiods.sync(dsDatePeriods);
        grid_dateperiods.groupBy(grid_dateperiods.getColIndexById("theyear"));

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

    var popupwin_dateperiods = dhxWins.createWindow("popupwin_dateperiods", 50, 50, 800, 400);
    popupwin_dateperiods.setText("Date Period Details:");
    popupwin_dateperiods.denyResize();
    popupwin_dateperiods.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_dateperiods"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "groupfk"},
        {type: "hidden", name: "token"},
        {type: "block", width: 700, list: [
        {type: "combo", name: "hotelfk", label: "Hotel:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/",
        }]},
        {type: "block", width: 700, list: [
        {type: "calendar", name: "checkin", label: "Check In:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        },
        {type: "newcolumn"},
        {type: "calendar", name: "checkout", label: "Check Out:", labelWidth: "100",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            dateFormat: "%d-%m-%Y",
            note: {
                text: "Format: dd-mm-yyyy"
            }
        }]},
        {type: "block", width: 700, list: [
        {type: "combo", name: "seasonfk", label: "Season:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/",
        }]},
        {type: "block", width: 700, list: [
        {type: "input", name: "grpname", label: "Hotel Group:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly: true
        }]},
        {type: "block", width: 700, list: [
        {type: "checkbox", name: "chkCreateforGroup", label: "Copy Period for <br>Other Hotels in Group:", 
            labelWidth: "130",
            labelHeight: "42", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        }]},
        {type: "block", width: 700, list: [
        {type: "checkbox", name: "active", label: "Active:", 
            labelWidth: "130",
            labelHeight: "42", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "0"
        }]},
        {type: "block", width: 700, list: [
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "newcolumn"},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
        ]}
    ];

    var dateperiodlayout = popupwin_dateperiods.attachLayout("1C");

    dateperiodlayout.cells("a").hideHeader();

    var form_dateperiods = dateperiodlayout.cells("a").attachForm(str_frm_ug);

    form_dateperiods.getInput("grpname").style.backgroundColor = "#F3E2A9";

    jQuery(function ($) {
        $("[name='checkin']").mask("99-99-9999");
    });

    jQuery(function ($) {
        $("[name='checkout']").mask("99-99-9999");
    });

    form_dateperiods.attachEvent("onChange", function (name, value) {
        if (name == "checkin" || name == "checkout")
        {
            checkRange(name, value);
        } else if (name == "hotelfk")
        {
            lookupGroupDetails();
        }

    });
    
    var cboSeason = form_dateperiods.getCombo("seasonfk");
    var dsSeason = new dhtmlXDataStore();
    dsSeason.load("php/api/combos/season_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsSeason.dataCount(); i++)
        {
            var item = dsSeason.item(dsSeason.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboSeason.addOption([{value: value, text: txt, img_src: "images/season.png"}]);
        }

        cboSeason.readonly(false);
        cboSeason.enableFilteringMode(true);
    });



    form_dateperiods.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_dateperiods.setModal(false);
            popupwin_dateperiods.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_dateperiods.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Date Period",
                    callback: function () {
                    }
                });
                return;
            }

            if (!utils_validate_autocompletecombo(cboHotel))
            {
                dhtmlx.alert({
                    text: "Please select a valid Hotel!",
                    type: "alert-warning",
                    title: "Save Date Period",
                    callback: function () {
                        cboHotel.openSelect();
                    }
                });
                return;
            }
            
            if (!utils_validate_autocompletecombo(cboSeason))
            {
                dhtmlx.alert({
                    text: "Please select a Season!",
                    type: "alert-warning",
                    title: "Save Date Period",
                    callback: function () {
                        cboSeason.openSelect();
                    }
                });
                return;
            }


            dateperiodlayout.cells("a").progressOn();

            form_dateperiods.setItemValue("token", global_token);

            form_dateperiods.send("php/api/dateperiods/savedateperiod.php", "post", function (loader)
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
                        dateperiodlayout.cells("a").progressOff();
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
                        dateperiodlayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsDatePeriods.clearAll();
                        grid_dateperiods.clearAll();

                        dsDatePeriods.load("php/api/dateperiods/dateperiodgrid.php?t=" + encodeURIComponent(global_token) + "&hoid=" + global_hotel_id, "json", function () {
                            grid_dateperiods.sync(dsDatePeriods);
                            grid_dateperiods.groupBy(grid_dateperiods.getColIndexById("theyear"));
                            grid_dateperiods.filterByAll();
                            
                            popupwin_dateperiods.setModal(false);
                            popupwin_dateperiods.hide();
                            dateperiodlayout.cells("a").progressOff();

                            grid_dateperiods.selectRowById(json_obj.ID, false, true, false);
                            
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
                        dateperiodlayout.cells("a").progressOff();
                    }
                }
            });
        }
    });


    var dsHotel = new dhtmlXDataStore();
    var cboHotel = form_dateperiods.getCombo("hotelfk");
    
    

    function loadHotelCombo(hid)
    {
        dateperiodlayout.cells("a").progressOn();
        dsHotel = null;
        cboHotel.clearAll(true);
        dsHotel = new dhtmlXDataStore();
        dsHotel.load("php/api/dateperiods/hotel_combo.php?t=" + encodeURIComponent(global_token) + "&hid=" + hid, "json", function () {

            dateperiodlayout.cells("a").progressOff();
            
            for (var i = 0; i < dsHotel.dataCount(); i++)
            {
                var item = dsHotel.item(dsHotel.idByIndex(i));
                var value = item.value;
                var txt = item.text;
                cboHotel.addOption([{value: value, text: txt, img_src: "images/hotel.png"}]);
            }
            
            if(hid != "-1")
            {
                cboHotel.setComboValue(hid);
            }
            
            
            cboHotel.readonly(true);
        });
    }



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

    function checkRange(name, value) {
        if (name == "checkout") {
            form_dateperiods.getCalendar("checkin").setSensitiveRange(null, value);
        } else {
            form_dateperiods.getCalendar("checkout").setSensitiveRange(value, null);
        }
    }

    function lookupGroupDetails()
    {
        form_dateperiods.hideItem("chkCreateforGroup");
        
        var hotelid = cboHotel.getSelectedValue();
        if (!hotelid)
        {
            form_dateperiods.setItemValue("groupfk", "-1");
            form_dateperiods.setItemValue("grpname", "");
        } else
        {   
            for (var i = 0; i < dsHotel.dataCount(); i++)
            {
                var item = dsHotel.item(dsHotel.idByIndex(i));
                if(item.value == hotelid)
                {
                    var groupfk = item.groupfk;
                    var grpname = item.grpname;
                    
                    if(form_dateperiods.getItemValue("id") == "-1" && groupfk != "-1")
                    {
                        form_dateperiods.showItem("chkCreateforGroup");
                    }
                    
                    form_dateperiods.setItemValue("groupfk", groupfk);
                    form_dateperiods.setItemValue("grpname", grpname);
                    return;
                }
            }
        }
    }

    popupwin_dateperiods.hide();

}