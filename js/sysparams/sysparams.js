var sysparams_obj = new sysparams();

function sysparams()
{

    document.getElementById("aTitle").innerHTML = "SYSTEM PARAMETERS";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");


    main_layout.cells('a').hideHeader();

    var grid_sysparams = main_layout.cells("a").attachGrid();
    grid_sysparams.setIconsPath('libraries/dhtmlx/imgs/');
    grid_sysparams.setHeader("Parameter 1,Parameter 2,Key,Value,Description");
    grid_sysparams.setColumnIds("param1,param2,pkey,pvalue,description");
    grid_sysparams.setColTypes("ro,ro,ro,ro,ro");
    grid_sysparams.setInitWidths("150,150,200,400,*");
    grid_sysparams.setColAlign("left,left,left,left,left");
    grid_sysparams.setColSorting('str,str,str,str,str');
    grid_sysparams.attachHeader("#select_filter,#select_filter,#select_filter,#text_filter,#text_filter");
    grid_sysparams.init();


    var toolbar = main_layout.cells("a").attachToolbar();
    toolbar.setIconsPath("images/");
    toolbar.addButton("modify", 1, "Modify", "modify.png", "modify.png");
    toolbar.setIconSize(32);

    applyrights();


    toolbar.attachEvent("onClick", function (id) {
        if (id == "modify")
        {
            var uid = grid_sysparams.getSelectedRowId();
            if (!uid)
            {
                return;
            }

            var data = dsSysParams.item(uid);
            form_sysparams.setFormData(data);

            popupwin_sysparams.setModal(true);
            popupwin_sysparams.center();
            popupwin_sysparams.show();
        } 
    });


    var dsSysParams = new dhtmlXDataStore();
    dsSysParams.load("php/api/sysparams/sysparamgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        grid_sysparams.sync(dsSysParams);

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

    var popupwin_sysparams = dhxWins.createWindow("popupwin_sysparams", 50, 50, 650, 300);
    popupwin_sysparams.setText("Sysparam Details:");
    popupwin_sysparams.denyResize();
    popupwin_sysparams.denyPark();

    /*=== WINDOW ON CLOSE EVENT ===*/
    dhxWins.attachEvent("onClose", function (win) {
        //do let user close window by clicking on close icon in window header
        //so catch it in the event and return false. Simply hide the window
        win.setModal(false);
        win.hide();
    });


    var str_frm_ug = [
        {type: "settings", position: "label-left", id: "form_sysparams"},
        {type: "newcolumn", offset: 0},
        {type: "hidden", name: "id", required: true},
        {type: "hidden", name: "token"},
        {type: "input", name: "param1", label: "Parameter 1:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly:true
        },
        {type: "input", name: "param2", label: "Parameter 2:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly:true
        },
        {type: "input", name: "pkey", label: "Key:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly:true
        },
        {type: "input", name: "pvalue", label: "Value:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", required: true
        },
        {type: "input", name: "description", label: "Description:", labelWidth: "130",
            labelHeight: "22", inputWidth: "450", inputHeight: "28", labelLeft: "0",
            labelTop: "10", inputLeft: "10", inputTop: "10", readonly:true
        },
        {type: "button", name: "cmdSave", value: "Save", width: "80", offsetLeft: 0},
        {type: "button", name: "cmdCancel", value: "Cancel", width: "80", offsetLeft: 0}
    ];

    var sysparamlayout = popupwin_sysparams.attachLayout("1C");

    sysparamlayout.cells("a").hideHeader();

    var form_sysparams = sysparamlayout.cells("a").attachForm(str_frm_ug);
    form_sysparams.getInput("param1").style.backgroundColor = "#F3E2A9";
    form_sysparams.getInput("param2").style.backgroundColor = "#F3E2A9";
    form_sysparams.getInput("pkey").style.backgroundColor = "#F3E2A9";
    form_sysparams.getInput("description").style.backgroundColor = "#F3E2A9";
    
    form_sysparams.attachEvent("onButtonClick", function (name, command) {
        if (name == "cmdCancel")
        {
            popupwin_sysparams.setModal(false);
            popupwin_sysparams.hide();
        }
        if (name == "cmdSave")
        {
            if (!form_sysparams.validate())
            {
                dhtmlx.alert({
                    text: "Please fill highlighted fields correctly!",
                    type: "alert-warning",
                    title: "Save Sysparam",
                    callback: function () {
                    }
                });
                return;
            }


            sysparamlayout.cells("a").progressOn();

            form_sysparams.setItemValue("token", global_token);

            form_sysparams.send("php/api/sysparams/savesysparam.php", "post", function (loader)
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
                        sysparamlayout.cells("a").progressOff();
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
                        sysparamlayout.cells("a").progressOff();
                        return false;
                    }

                    if (json_obj.OUTCOME == "OK")
                    {
                        dhtmlx.message({
                            text: "<b><font color='green'>Save Successful!</font></b>",
                            expire: 1500
                        });

                        dsSysParams.clearAll();
                        grid_sysparams.clearAll();

                        dsSysParams.load("php/api/sysparams/sysparamgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
                            grid_sysparams.sync(dsSysParams);
                            popupwin_sysparams.setModal(false);
                            popupwin_sysparams.hide();
                            sysparamlayout.cells("a").progressOff();

                            grid_sysparams.selectRowById(json_obj.ID, false, true, false);
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
                        sysparamlayout.cells("a").progressOff();
                    }

                }
            });
        }
    });

    function applyrights()
    {
        for (var i = 0; i < json_rights.length; i++)
        {

            if (json_rights[i].PROCESSNAME == "MODIFY PARAMETER" && json_rights[i].ALLOWED == "N")
            {
                toolbar.disableItem("modify");
                toolbar.setItemToolTip("modify", "Not Allowed");
            }
        }
    }

    popupwin_sysparams.hide();

}