var companies_obj = new companies();

function companies()
{


    document.getElementById("aTitle").innerHTML = "COMPANIES";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Companies");

    var grid_companies = main_layout.cells("a").attachGrid();
    grid_companies.setIconsPath('libraries/dhtmlx/imgs/');
    grid_companies.setHeader("Name,Address,Country,Currency,Email,BRN,VATNo,Website,Phone,Fax,Description");
    grid_companies.setColumnIds("companyname,address,countrycode_3,currency_code,email,brn,vatno,website,phone,fax,description");
    grid_companies.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");
    grid_companies.setInitWidths("200,200,60,60,120,100,100,100,100,100,500");
    grid_companies.setColAlign("left,left,left,left,left,left,left,left,left,left,left");
    grid_companies.setColSorting('str,str,str,str,str,str,str,str,str,str,str');
    grid_companies.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.addButton("export", 4, "Export Excel", "excel.png");
    toolbar.addButton("bank", 5, "Accounts", "bankacc.png", "bankacc.png");
    toolbar.setIconSize(32);




    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_companies.clear();
            form_companies.setItemValue("id", "-1");
            
            cboCurrency.setComboValue(null);
            cboCurrency.setComboText("");
            
            if (default_country_id != "-1")
            {
                cboCountry.setComboValue(default_country_id);
            }

            popupwin_companies.setModal(true);
            popupwin_companies.center();
            popupwin_companies.show();


        } else if (id == "export")
        {
            grid_companies.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_companies.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsCompanies.item(uid);
            form_companies.setFormData(data);

            popupwin_companies.setModal(true);
            popupwin_companies.center();
            popupwin_companies.show();


        } else if (id == "delete")
        {
            var gid = grid_companies.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Company",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/companies/deletecompany.php", params, function (loader) {

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
                                    grid_companies.deleteRow(gid);
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
        } else if (id == "bank")
        {

            var gid = grid_companies.getSelectedRowId();
            if (!gid)
            {
                dhtmlx.message({
                    text: "<b><font color='green'>Please select a Company!</font></b>",
                    type: "alert-info"
                });

                return;
            }

            //load bank accounts window
            var companyname = dsCompanies.item(gid).companyname;

            accountlayout.cells("a").progressOn();

            dsAccounts = null;
            dsAccounts = new dhtmlXDataStore();
            dsAccounts.load("php/api/companies/accountgrid.php?t=" + encodeURIComponent(global_token) + "&cid=" + gid, "json", function () {
                accountlayout.cells("a").progressOff();
                grid_accounts.sync(dsAccounts);

            });

            popupwin_bankaccounts.setText(companyname);
            popupwin_bankaccounts.setModal(true);
            popupwin_bankaccounts.center();
            popupwin_bankaccounts.show();
        }
    });


    var dsCompanies = new dhtmlXDataStore();
    var dsAccounts = new dhtmlXDataStore();
    dsCompanies.load("php/api/companies/companygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_companies.sync(dsCompanies);

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
    
    var popupwin_companies = dhxWins.createWindow("popupwin_companies", 50, 50, 680, 500);
    popupwin_companies.setText("Company Details:");
    popupwin_companies.denyResize();
    popupwin_companies.denyPark();



    var popupwin_bankaccounts = dhxWins.createWindow("popupwin_bankaccounts", 50, 50, 680, 440);
    popupwin_bankaccounts.setText("Accounts:");
    popupwin_bankaccounts.denyResize();
    popupwin_bankaccounts.denyPark();


    var popupwin_bankaccounts_addmod = dhxWins.createWindow("popupwin_bankaccounts_addmod", 50, 50, 680, 250);
    popupwin_bankaccounts_addmod.setText("Accounts Details");
    popupwin_bankaccounts_addmod.denyResize();
    popupwin_bankaccounts_addmod.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window

        if (win.getId() == "popupwin_bankaccounts_addmod")
        {
            popupwin_bankaccounts_addmod.setModal(false);
            popupwin_bankaccounts_addmod.hide();
            popupwin_bankaccounts.setModal(true);
        } else
        {
            win.setModal(false);
            win.hide();
        }
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_companies"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "companyname", label: "Name:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "address", label: "Address:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "combo", name: "countryfk", label: "Country:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "defaultcurrencyfk", label: "Default Currency:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "input", name: "email", label: "Email:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            validate: "ValidEmail"
        },
        {type: "input", name: "brn", label: "BRN:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "vatno", label: "VAT No:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "website", label: "Website:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "phone", label: "Phone:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "input", name: "fax", label: "Fax:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "checkbox", name: "active", label: "Active:", labelWidth: "130",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"

        },
        {type: "editor", name: "description", label: "Description:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "190", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var companylayout = popupwin_companies.attachLayout("1C");

    companylayout.cells("a").hideHeader();

    var form_companies = companylayout.cells("a").attachForm(str_frm_ug);



    form_companies.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_companies.setModal(false);
            popupwin_companies.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_companies.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Company",
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
                    title: "Save Company",
                    callback: function () {
                        cboCountry.openSelect();
                    }
                });
                return;
            }



            companylayout.cells("a").progressOn();

            form_companies.setItemValue("token", global_token);

            form_companies.send("php/api/companies/savecompany.php", "post", function (loader)
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
                        companylayout.cells("a").progressOff();
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
                        companylayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsCompanies.clearAll();
                        grid_companies.clearAll();

                        dsCompanies.load("php/api/companies/companygrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_companies.sync(dsCompanies);
                            popupwin_companies.setModal(false);
                            popupwin_companies.hide();
                            companylayout.cells("a").progressOff();

                            grid_companies.selectRowById(json_obj.ID, false, true, false);
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
                        companylayout.cells("a").progressOff();
                    }
                }
            });
        }
    });






    var accountlayout = popupwin_bankaccounts.attachLayout("1C");
    accountlayout.cells("a").setText("Bank Accounts");

    var grid_accounts = accountlayout.cells("a").attachGrid();
    grid_accounts.setIconsPath('libraries/dhtmlx/imgs/');
    grid_accounts.setHeader("Account No,Bank,Currency,IBAN");
    grid_accounts.setColumnIds("accountno,bankname,currency_code,iban");
    grid_accounts.setColTypes("ro,ro,ro,ro");
    grid_accounts.setInitWidths("200,200,60,*");
    grid_accounts.setColAlign("left,left,left,left");
    grid_accounts.setColSorting('str,str,str,str');
    grid_accounts.init();


    var toolbar_accounts = accountlayout.cells("a").attachToolbar();
    toolbar_accounts.setIconsPath("images/");
    toolbar_accounts.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar_accounts.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar_accounts.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar_accounts.setIconSize(32);

    toolbar_accounts.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_accounts.clear();
            form_accounts.setItemValue("id", "-1");
            form_accounts.setItemValue("companyfk", grid_companies.getSelectedRowId());
            
            cboAccCurrency.setComboValue(null);
            cboAccCurrency.setComboText("");
            
            popupwin_bankaccounts.setModal(false);

            popupwin_bankaccounts_addmod.setModal(true);
            popupwin_bankaccounts_addmod.center();
            popupwin_bankaccounts_addmod.show();
            popupwin_bankaccounts_addmod.bringToTop();
        } else if (id == "modify")
        {
            var uid = grid_accounts.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsAccounts.item(uid);
            form_accounts.setFormData(data);

            popupwin_bankaccounts.setModal(false);

            popupwin_bankaccounts_addmod.setModal(true);
            popupwin_bankaccounts_addmod.center();
            popupwin_bankaccounts_addmod.show();
            popupwin_bankaccounts_addmod.bringToTop();


        } else if (id == "delete")
        {
            var gid = grid_accounts.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Account",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/companies/deleteaccount.php", params, function (loader) {

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
                                    grid_accounts.deleteRow(gid);
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



    var str_frm_acc = [
        {type: "settings", position: "label-left", id: "form_accounts"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "companyfk", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "accountno", label: "Account No:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "iban", label: "IBAN:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "combo", name: "bankfk", label: "Bank:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "combo", name: "currencyfk", label: "Currency:", labelWidth: "130",
            labelHeight: "22", inputWidth: "300", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true,
            comboType: "image",
            comboImagePath: "../../images/"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var accountaddmodlayout = popupwin_bankaccounts_addmod.attachLayout("1C");

    accountaddmodlayout.cells("a").hideHeader();

    var form_accounts = accountaddmodlayout.cells("a").attachForm(str_frm_acc);



    form_accounts.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_bankaccounts_addmod.setModal(false);
            popupwin_bankaccounts_addmod.hide();

            popupwin_bankaccounts.setModal(true);
        }
        if (name == "cmdSave")
        {
            if (!form_accounts.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Account",
                    callback: function () {
                    }
                });
                return;
            }


            accountaddmodlayout.cells("a").progressOn();

            form_accounts.setItemValue("token", global_token);

            form_accounts.send("php/api/companies/saveaccount.php", "post", function (loader)
            {
                accountaddmodlayout.cells("a").progressOff();
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
                        companylayout.cells("a").progressOff();
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
                        companylayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsAccounts.clearAll();
                        grid_accounts.clearAll();

                        dsAccounts.load("php/api/companies/accountgrid.php?t=" + encodeURIComponent(global_token) + "&cid=" + grid_companies.getSelectedRowId(), "json", function () {
                            grid_accounts.sync(dsAccounts);
                            popupwin_bankaccounts_addmod.setModal(false);
                            popupwin_bankaccounts_addmod.hide();
                            popupwin_bankaccounts.setModal(true);

                            grid_accounts.selectRowById(json_obj.ID, false, true, false);
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
                        companylayout.cells("a").progressOff();
                    }
                }
            });
        }
    });



    popupwin_companies.hide();
    popupwin_bankaccounts.hide();
    popupwin_bankaccounts_addmod.hide();


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
            } else if (json_rights[i].PROCESSNAME == "BANK" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("bank");
                toolbar.setItemToolTip("bank", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "ADD ACCOUNT" && json_rights[i].ALLOWED == "N")
            {
                toolbar_accounts.disableItem("new");
                toolbar_accounts.setItemToolTip("new", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "MODIFY ACCOUNT" && json_rights[i].ALLOWED == "N")
            {
                toolbar_accounts.disableItem("modify");
                toolbar_accounts.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE ACCOUNT" && json_rights[i].ALLOWED == "N")
            {
                toolbar_accounts.disableItem("delete");
                toolbar_accounts.setItemToolTip("delete", "Not Allowed");
            }
        }
    }


    applyrights();

    var cboCountry = form_companies.getCombo("countryfk");
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


    var cboBank = form_accounts.getCombo("bankfk");
    var dsBank = new dhtmlXDataStore();
    dsBank.load("php/api/combos/bank_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsBank.dataCount(); i++)
        {
            var item = dsBank.item(dsBank.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboBank.addOption([{value: value, text: txt, img_src: "images/bank_32.png"}]);
        }

        cboBank.readonly(true);
    });



    var cboCurrency = form_companies.getCombo("defaultcurrencyfk");
    var cboAccCurrency = form_accounts.getCombo("currencyfk");

    var dsCurrency = new dhtmlXDataStore();
    dsCurrency.load("php/api/combos/currency_combo.php?t=" + encodeURIComponent(global_token), "json", function () {

        for (var i = 0; i < dsCurrency.dataCount(); i++)
        {
            var item = dsCurrency.item(dsCurrency.idByIndex(i));
            var value = item.value;
            var txt = item.text;
            cboCurrency.addOption([{value: value, text: txt, img_src: "images/currency.png"}]);
            cboAccCurrency.addOption([{value: value, text: txt, img_src: "images/currency.png"}]);
        }

        cboCurrency.readonly(true);
        cboAccCurrency.readonly(true);
    });


}