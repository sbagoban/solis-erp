var seasons_obj = new seasons();

function seasons()
{

    document.getElementById("aTitle").innerHTML = "SEASONS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_seasons = main_layout.cells("a").attachGrid();
    grid_seasons.setIconsPath('libraries/dhtmlx/imgs/');
    grid_seasons.setHeader("Code,Season");
    grid_seasons.setColumnIds("scode,season");
    grid_seasons.setColTypes("ro,ro");
    grid_seasons.setInitWidths("150,400");
    grid_seasons.setColAlign("left,left");
    grid_seasons.setColSorting('str,str');
    grid_seasons.attachHeader("#text_filter,#text_filter");
    grid_seasons.init();


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
            form_seasons.clear();
            form_seasons.setItemValue("id", "-1");
            popupwin_seasons.setModal(true);
            popupwin_seasons.center();
            popupwin_seasons.show();


        } else if (id == "export")
        {
            grid_seasons.toExcel('php/api/grid-excel-php/generate.php');
        } else if (id == "modify")
        {
            var uid = grid_seasons.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsSeasons.item(uid);
            form_seasons.setFormData(data);

            popupwin_seasons.setModal(true);
            popupwin_seasons.center();
            popupwin_seasons.show();


        } else if (id == "delete")
        {
            var gid = grid_seasons.getSelectedRowId();
            if (!gid)
            {
                return;
            }


            dhtmlx.confirm({
                title: "Delete Service Type",
                type: "confirm",
                text: "Confirm Deletion?",
                callback: function (tf) {
                    if (tf)
                    {
                        var params = "gid=" + gid + "&t=" + encodeURIComponent(global_token);
                        dhtmlxAjax.post("php/api/seasons/deleteseason.php", params, function (loader) {

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
                                    grid_seasons.deleteRow(gid);
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


    var dsSeasons = new dhtmlXDataStore();
    dsSeasons.load("php/api/seasons/seasongrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_seasons.sync(dsSeasons);

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

    var popupwin_seasons = dhxWins.createWindow("popupwin_seasons", 50, 50, 700, 180);
    popupwin_seasons.setText("ServiceType Details:");
    popupwin_seasons.denyResize();
    popupwin_seasons.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_seasons"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "scode", label: "Code:", labelWidth: "80",
            labelHeight: "22", inputWidth: "100", inputHeight: "28", labelLeft: "0",
            maxLength:4,
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "season", label: "Season:", labelWidth: "80",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "block", blockOffset: 0, list: [
                {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
                {type: "newcolumn"},
                {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
            ]}
    ];

    var seasonlayout = popupwin_seasons.attachLayout("1C");

    seasonlayout.cells("a").hideHeader();

    var form_seasons = seasonlayout.cells("a").attachForm(str_frm_ug);

    form_seasons.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_seasons.setModal(false);
            popupwin_seasons.hide();
        } else if (name == "cmdSave")
        {
            if (!form_seasons.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Season",
                    callback: function () {
                    }
                });
                return;
            }

            seasonlayout.cells("a").progressOn();

            form_seasons.setItemValue("token", global_token);

            form_seasons.send("php/api/seasons/saveseason.php", "post", function (loader)
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
                        seasonlayout.cells("a").progressOff();
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
                        seasonlayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {

                        dsSeasons.clearAll();
                        grid_seasons.clearAll();

                        dsSeasons = null;
                        dsSeasons = new dhtmlXDataStore();

                        dsSeasons.load("php/api/seasons/seasongrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_seasons.sync(dsSeasons);
                            popupwin_seasons.setModal(false);
                            popupwin_seasons.hide();
                            seasonlayout.cells("a").progressOff();

                            grid_seasons.selectRowById(json_obj.ID, false, true, false);
                        });

                    } else
                    {
                        dhtmlx.alert({
                            text: json_obj.OUTCOME,
                            type: "alert-warning",
                            title: "DELETE",
                            callback: function () {
                            }
                        });
                        seasonlayout.cells("a").progressOff();
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

    popupwin_seasons.hide();

}