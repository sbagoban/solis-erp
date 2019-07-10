var mealplans_obj = new mealplans();

function mealplans()
{
    

    document.getElementById("aTitle").innerHTML = "MEAL PLANS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').setText("Meal Plans");

    var grid_mealplans = main_layout.cells("a").attachGrid();
    grid_mealplans.setIconsPath('libraries/dhtmlx/imgs/');
    grid_mealplans.setHeader("Meal,Full Name,Compulsory,Search Engine,Board Basis");
    grid_mealplans.setColumnIds("meal,mealfullname,compulsory,usedinsearchengine,usedasboardbasis");
    grid_mealplans.setColTypes("ro,ro,ch,ch,ch");
    grid_mealplans.setInitWidths("250,400,80,80,80");
    grid_mealplans.setColAlign("left,left,center,center,center");
    grid_mealplans.setColSorting('str,str,int,int,int');
    grid_mealplans.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter");
    grid_mealplans.setEditable(false);
    grid_mealplans.init();
    


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("new", 1, "Add New", "add.png", "add.png");
    toolbar.addButton("modify", 2, "Modify", "modify.png", "modify.png");
    toolbar.addButton("delete", 3, "Delete", "delete.png", "delete.png");
    toolbar.addButton("export", 4, "Export Excel", "excel.png");
    toolbar.setIconSize(32);
    
    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "new")
        {
            form_mealplans.clear();
            form_mealplans.setItemValue("id", "-1");
            
            popupwin_mealplans.setModal(true);
            popupwin_mealplans.center();
            popupwin_mealplans.show();
            
            
        } else if (id == "modify")
        {
            var uid = grid_mealplans.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsMealPlans.item(uid);
            form_mealplans.setFormData(data);

            popupwin_mealplans.setModal(true);
            popupwin_mealplans.center();
            popupwin_mealplans.show();


        } else if (id == "delete")
        {
            var gid = grid_mealplans.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Meal Plan",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/mealplans/deletemealplan.php", params, function (loader) {

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
                                    grid_mealplans.deleteRow(gid);
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
            grid_mealplans.toExcel('php/api/grid-excel-php/generate.php');
        }
    });


    var dsMealPlans = new dhtmlXDataStore();
    dsMealPlans.load("php/api/mealplans/mealplangrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_mealplans.sync(dsMealPlans);

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
    
    var popupwin_mealplans = dhxWins.createWindow("popupwin_mealplans", 50, 50, 680, 470);
    popupwin_mealplans.setText("Meal Plan Details:");
    popupwin_mealplans.denyResize();
    popupwin_mealplans.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_mealplans"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "meal", label: "Meal:", labelWidth: "140",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "mealfullname", label: "Full Name:", labelWidth: "140",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "checkbox", name: "compulsory", label: "Complusory:", labelWidth: "140",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "checkbox", name: "usedinsearchengine", label: "Used in Search Engine:", 
            labelWidth: "140",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
       
        {type: "checkbox", name: "usedasboardbasis", label: "Used as Board Basis:", labelWidth: "140",
            labelHeight: "22", inputWidth: "250", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "editor", name: "description", label: "Description:", labelWidth: "140",
            labelHeight: "22", inputWidth: "450", inputHeight: "190", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10"
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var mealplanslayout = popupwin_mealplans.attachLayout("1C");

    mealplanslayout.cells("a").hideHeader();

    var form_mealplans = mealplanslayout.cells("a").attachForm(str_frm_ug);



    form_mealplans.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_mealplans.setModal(false);
            popupwin_mealplans.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_mealplans.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Meal Plan",
                    callback: function () {
                    }
                });
                return;
            }


            mealplanslayout.cells("a").progressOn();

            form_mealplans.setItemValue("token", global_token);

            form_mealplans.send("php/api/mealplans/savemealplan.php", "post", function (loader)
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
                        mealplanslayout.cells("a").progressOff();
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
                        mealplanslayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsMealPlans.clearAll();
                        grid_mealplans.clearAll();

                        dsMealPlans.load("php/api/mealplans/mealplangrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_mealplans.sync(dsMealPlans);
                            popupwin_mealplans.setModal(false);
                            popupwin_mealplans.hide();
                            mealplanslayout.cells("a").progressOff();

                            grid_mealplans.selectRowById(json_obj.ID, false, true, false);
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
                        mealplanslayout.cells("a").progressOff();
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
                toolbar.disableItem("modify");
                toolbar.setItemToolTip("modify", "Not Allowed");
            } else if (json_rights[i].PROCESSNAME == "DELETE" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("delete");
                toolbar.setItemToolTip("delete", "Not Allowed");
            }
        }
    }

    popupwin_mealplans.hide();

}