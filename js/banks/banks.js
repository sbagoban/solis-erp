var banks_obj = new banks();

function banks()
{
    

    document.getElementById("aTitle").innerHTML = "BANKS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Banks");

    var grid_banks = main_layout.cells("a").attachGrid();
    grid_banks.setIconsPath('libraries/dhtmlx/imgs/');
    grid_banks.setHeader("Name,Address,City,Country,Swift");
    grid_banks.setColumnIds("bankname,address,city,country_name,swift");
    grid_banks.setColTypes("ro,ro,ro,ro,ro");
    grid_banks.setInitWidths("*,150,150,150,100");
    grid_banks.setColAlign("left,left,left,left,left");
    grid_banks.setColSorting('str,str,str,str,str');
    grid_banks.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    grid_banks.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.setIconSize(32);
    
    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_banks.clear();
            form_banks.setItemValue("id", "-1");
            
            if(default_country_id != "-1")
            {
                cboCountry.setComboValue(default_country_id);
            }
            
            popupwin_banks.setModal(true);
            popupwin_banks.center();
            popupwin_banks.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_banks.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsBanks.item(uid);
            form_banks.setFormData(data);

            popupwin_banks.setModal(true);
            popupwin_banks.center();
            popupwin_banks.show();


        } else if (id == "delete")
        {
            var gid = grid_banks.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Bank",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/banks/deletebank.php", params, function (loader) {

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
                                    grid_banks.deleteRow(gid);
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


    var dsBanks = new dhtmlXDataStore();
    dsBanks.load("php/api/banks/bankgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_banks.sync(dsBanks);

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
    
    var popupwin_banks = dhxWins.createWindow("popupwin_banks", 50, 50, 500, 280);
    popupwin_banks.setText("Bank Details:");
    popupwin_banks.denyResize();
    popupwin_banks.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_banks"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "bankname", label: "Bank Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "address", label: "Address:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "city", label: "City:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "combo", name: "countryfk", label: "Country:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/",
        },
        {type: "input", name: "swift", label: "Swift:", labelWidth: "130",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var banklayout = popupwin_banks.attachLayout("1C");

    banklayout.cells("a").hideHeader();

    var form_banks = banklayout.cells("a").attachForm(str_frm_ug);



    form_banks.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_banks.setModal(false);
            popupwin_banks.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_banks.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Bank",
                    callback: function () {
                    }
                });
                return;
            }
            
            if(!utils_validate_autocompletecombo(cboCountry))
            {
                dhtmlx.alert({
                    text: "Please select a valid Country!",
                    type: "alert-warning",
                    title: "Save Bank",
                    callback: function () {
                        cboCountry.openSelect();
                    }
                });
                return;
            }



            banklayout.cells("a").progressOn();

            form_banks.setItemValue("token", global_token);

            form_banks.send("php/api/banks/savebank.php", "post", function (loader)
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
                        banklayout.cells("a").progressOff();
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
                        banklayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsBanks.clearAll();
                        grid_banks.clearAll();

                        dsBanks.load("php/api/banks/bankgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_banks.sync(dsBanks);
                            popupwin_banks.setModal(false);
                            popupwin_banks.hide();
                            banklayout.cells("a").progressOff();

                            grid_banks.selectRowById(json_obj.ID, false, true, false);
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
                        banklayout.cells("a").progressOff();
                    }
                }
            });
        }
    });

    var cboCountry = form_banks.getCombo("countryfk");
    var dsCountry = new dhtmlXDataStore();
    dsCountry.load("php/api/combos/country_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

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

    popupwin_banks.hide();

}