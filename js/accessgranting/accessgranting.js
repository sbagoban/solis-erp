var accessgranting_obj = new accessgranting();

function accessgranting() {
    
    var sublayout = null;
    
    document.getElementById("aTitle").innerHTML = "ACCESS GRANTING";

    var main_layout = new dhtmlXLayoutObject("main_body", "1C");

    var grid_groups = null;

    var dsGroups = new dhtmlXDataStore();
    var dsProcess = new dhtmlXDataStore();
    var dsUGProcess = new dhtmlXDataStore();

    sublayout = main_layout.cells("a").attachLayout('3W');
    
    sublayout.cells("a").setText("User Group");
    sublayout.cells("b").setText("Full System Menu");
    sublayout.cells("c").hideHeader();
    sublayout.cells("a").setWidth(230);
    sublayout.cells("b").setWidth(250);

    var grid_groups = sublayout.cells("a").attachGrid();
    grid_groups.setIconsPath('libraries/dhtmlx/imgs/');
    grid_groups.setHeader(",User Group");
    grid_groups.setColumnIds("tmpimg,ugroup");
    grid_groups.setColTypes("ro,ro");
    grid_groups.setInitWidths("40,150");
    grid_groups.setColAlign("center,left");
    grid_groups.setColSorting('str,str');
    grid_groups.attachEvent("onRowSelect", onGrpSelect);
    grid_groups.init();


    var treeMenu = sublayout.cells("b").attachTree();
    treeMenu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
    treeMenu.enableCheckBoxes(true, false);
    treeMenu.enableThreeStateCheckboxes(1);

    var tb_menu = sublayout.cells("b").attachToolbar();
    tb_menu.setIconsPath("images/");
    tb_menu.addButton("give", 1, "Give Menu", "add.png", "add.png");
    tb_menu.setIconSize(32);
    tb_menu.attachEvent("onClick", function (id) {
        if (id == "give")
        {
            giveMenu();
        }
    });


    var tabParams = sublayout.cells("c").attachTabbar();
    tabParams.addTab("menu", "<b>Menu Access</b>", "150px", '');
    tabParams.addTab("process", "<b>Process Access</b>", "150px", '');
    tabParams.setTabActive("menu");

    var layoutUGrpTree = tabParams.cells("menu").attachLayout("1C");
    layoutUGrpTree.cells("a").hideHeader();
    var tree_ugrp = layoutUGrpTree.cells("a").attachTree();
    tree_ugrp.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
    tree_ugrp.enableThreeStateCheckboxes(1);
    
    var tb_ugrpmenu = layoutUGrpTree.cells("a").attachToolbar();
    tb_ugrpmenu.setIconsPath("images/");
    tb_ugrpmenu.setIconSize(32);
    tb_ugrpmenu.addButton("delete", 1, "Remove Menu", "delete.png", "delete.png");
    tb_ugrpmenu.attachEvent("onClick", function (id) {
        if (id == "delete")
        {
            deleteMenu();
        }
    });

    //====================== PROCESSES ============================
    var layoutUGrpPrcss = tabParams.cells("process").attachLayout("2E");
    layoutUGrpPrcss.cells("a").setText("Full Processes");
    layoutUGrpPrcss.cells("b").setText("User Group Processes");

    var grid_process = layoutUGrpPrcss.cells("a").attachGrid();
    grid_process.setIconsPath('libraries/dhtmlx/imgs/');
    grid_process.setHeader(",Process,Description");
    grid_process.setColumnIds("X,processname,processdescription");
    grid_process.setColTypes("ch,ro,ro");
    grid_process.setInitWidths("50,150,300");
    grid_process.setColAlign("center,left,left");
    grid_process.setColSorting('int,str,str');
    grid_process.init();

    var tb_prcs = layoutUGrpPrcss.cells("a").attachToolbar();
    tb_prcs.setIconsPath("images/");
    tb_prcs.setIconSize(32);
    tb_prcs.addButton("all", 1, "Select All", "selectall.png", "selectall.png");
    tb_prcs.addButton("share", 2, "Share Process", "add.png", "add.png");
    
    tb_prcs.attachEvent("onClick", function (id) {
        if (id == "share")
        {
            shareProcess();
        }
        else if (id == "all")
        {
            grid_process.checkAll(true);
        }
    });


    var grid_ugprocess = layoutUGrpPrcss.cells("b").attachGrid();
    grid_ugprocess.setIconsPath('libraries/dhtmlx/imgs/');
    grid_ugprocess.setHeader(",Process,Description");
    grid_ugprocess.setColumnIds("X,processname,processdescription");
    grid_ugprocess.setColTypes("ch,ro,ro");
    grid_ugprocess.setInitWidths("50,150,300");
    grid_ugprocess.setColAlign("center,left,left");
    grid_ugprocess.setColSorting('int,str,str');
    grid_ugprocess.init();

    var tb_ugrpprcs = layoutUGrpPrcss.cells("b").attachToolbar();
    tb_ugrpprcs.setIconsPath("images/");
    tb_ugrpprcs.setIconSize(32);
    tb_ugrpprcs.addButton("all", 1, "Select All", "selectall.png", "selectall.png");
    tb_ugrpprcs.addButton("delete", 2, "Deny Process", "delete.png", "delete.png");
    
    tb_ugrpprcs.attachEvent("onClick", function (id) {
        if (id == "delete")
        {
            deleteProcess();
        }
        else if (id == "all")
        {
            grid_ugprocess.checkAll(true);
        }
    });

    //== loadings...
    sublayout.cells("a").progressOn();
    dsGroups.load("php/api/accessgranting/groupgrid.php?t=" + encodeURIComponent(global_token), "json", function () {
        sublayout.cells("a").progressOff();
        grid_groups.sync(dsGroups);
    });

    loadFullTree();
    
    applyrights();
    
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
            
            //====================

            $("#main_body").height(y - 25);
            $("#main_body").width(x - 20);

            main_layout.setSizes(true);

        }, 1);
    }
    

    this.destroy = function () {

    };

    function onGrpSelect(rid, cid)
    {
        grid_ugprocess.clearAll();
        dsUGProcess.clearAll();

        dsProcess.clearAll();
        grid_process.clearAll();

        //destroy the group menu tree
        //clear the group process grid
        tree_ugrp.destructor();
        tree_ugrp = layoutUGrpTree.cells("a").attachTree();
        tree_ugrp.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_ugrp.enableCheckBoxes(true, false);
        tree_ugrp.enableThreeStateCheckboxes(1);

        //get the menu of the group in question
        layoutUGrpTree.cells("a").progressOn();
        tree_ugrp.loadXML("php/api/accessgranting/grpmenutree.php?ugrpid=" + rid + "&t=" + encodeURIComponent(global_token), function () {
            layoutUGrpTree.cells("a").progressOff();
        });

        //load any process for the selected tree node and selected group
        var itemid = treeMenu.getSelectedItemId();
        if (itemid != "")
        {
            layoutUGrpPrcss.cells("a").progressOn();
            dsProcess.load("php/api/accessgranting/processgrid.php?menuid=" + itemid + "&grpid=" + rid + "&t=" + encodeURIComponent(global_token), "json", function () {
                layoutUGrpPrcss.cells("a").progressOff();
                grid_process.sync(dsProcess);
            });

            layoutUGrpPrcss.cells("b").progressOn();
            dsUGProcess.load("php/api/accessgranting/grpprocessgrid.php?menuid=" + itemid + "&gprid=" + rid + "&t=" + encodeURIComponent(global_token), "json", function () {
                layoutUGrpPrcss.cells("b").progressOff();
                grid_ugprocess.sync(dsUGProcess);
            });
        }
    }



    function giveMenu()
    {
        //make sure usergroup selected
        //make sure at least one node checked from full menu tree
        var grpid = grid_groups.getSelectedRowId();
        if (!grpid)
        {
            dhtmlx.alert({
                text: "Please Select a User Group!",
                type: "alert-warning",
                title: "Give Menu",
                callback: function () {
                }
            });
            return;
        }

        var id = treeMenu.getAllCheckedBranches();

        if (id == "")
        {
            dhtmlx.alert({
                text: "Please Select at least one Menu From the Tree!",
                type: "alert-warning",
                title: "Give Menu",
                callback: function () {
                }
            });
            return;
        }

        var params = "ugrpid=" + grpid + "&menuid=" + id + "&t=" + encodeURIComponent(global_token);
        sublayout.progressOn();
        dhtmlxAjax.post("php/api/accessgranting/givemenu.php", params, function (loader) {
            sublayout.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "SHARE",
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
                        title: "SHARE",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    tree_ugrp.destructor();
                    tree_ugrp = layoutUGrpTree.cells("a").attachTree();
                    tree_ugrp.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
                    tree_ugrp.enableCheckBoxes(true, false);
                    tree_ugrp.enableThreeStateCheckboxes(1);


                    //get the menu of the group in question
                    layoutUGrpTree.cells("a").progressOn();
                    tree_ugrp.loadXML("php/api/accessgranting/grpmenutree.php?ugrpid=" + grpid + "&t=" + encodeURIComponent(global_token), function () {
                        layoutUGrpTree.cells("a").progressOff();
                    });

                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "SHARE",
                        callback: function () {
                        }
                    });
                }

            }


        });
    }



    function deleteMenu()
    {
        var grpid = grid_groups.getSelectedRowId();
        if (!grpid)
        {
            dhtmlx.alert({
                text: "Please Select a User Group!",
                type: "alert-warning",
                title: "Give Menu",
                callback: function () {
                }
            });
            return;
        }
        
        
        //cannot remove menu from admin group
        var item = dsGroups.item(grpid);
        if(item.ugroup == "ADMIN")
        {
            dhtmlx.alert({
                text: "Cannot deny ADMIN group MENU",
                type: "alert-warning",
                title: "Remove Menu",
                callback: function () {
                }
            });
            
            return;
        }
        

        var id = tree_ugrp.getAllChecked();

        if (id == "")
        {
            dhtmlx.alert({
                text: "Please Select at least one Menu From the Tree!",
                type: "alert-warning",
                title: "Remove Menu",
                callback: function () {
                }
            });
            return;
        }

        var params = "ugrpid=" + grpid + "&menuid=" + id + "&t=" + encodeURIComponent(global_token);
        sublayout.progressOn();
        dhtmlxAjax.post("php/api/accessgranting/deletemenu.php", params, function (loader) {
            sublayout.progressOff();

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
                    tree_ugrp.destructor();
                    tree_ugrp = layoutUGrpTree.cells("a").attachTree();
                    tree_ugrp.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
                    tree_ugrp.enableCheckBoxes(true, false);
                    tree_ugrp.enableThreeStateCheckboxes(1);

                    //get the menu of the group in question                    
                    layoutUGrpTree.cells("a").progressOn();
                    tree_ugrp.loadXML("php/api/accessgranting/grpmenutree.php?ugrpid=" + grpid + "&t=" + encodeURIComponent(global_token), function () {
                        layoutUGrpTree.cells("a").progressOff();
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
                }

            }

        });
    }

    function loadFullTree()
    {
        treeMenu.destructor();
        treeMenu = sublayout.cells("b").attachTree();
        treeMenu.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        treeMenu.enableCheckBoxes(true, false);
        treeMenu.enableThreeStateCheckboxes(1);
        treeMenu.attachEvent("onClick", onTreeMenuSelect);

        //get the menu of the group in question
        sublayout.cells("b").progressOn();
        treeMenu.loadXML("php/api/accessgranting/menutree.php?t=" + encodeURIComponent(global_token), function () {
            sublayout.cells("b").progressOff();
        });

        tree_ugrp.destructor();
        tree_ugrp = layoutUGrpTree.cells("a").attachTree();
        tree_ugrp.setImagePath("libraries/dhtmlx/imgs/csh_vista/");
        tree_ugrp.enableCheckBoxes(true, false);
        tree_ugrp.enableThreeStateCheckboxes(1);
    }

    function onTreeMenuSelect(id)
    {
        var grpid = grid_groups.getSelectedRowId();
        if (!grpid)
        {
            grpid = "-1"; //no selected ugroup yet
        }

        dsProcess.clearAll();
        grid_process.clearAll();

        dsUGProcess.clearAll();
        grid_ugprocess.clearAll();

        //load the process of the node if any
        layoutUGrpPrcss.cells("a").progressOn();

        dsProcess.load("php/api/accessgranting/processgrid.php?menuid=" + id + "&grpid=" + grpid + "&t=" + encodeURIComponent(global_token), "json", function () {
            layoutUGrpPrcss.cells("a").progressOff();
            grid_process.sync(dsProcess);
        });

        //load the process of group for the selected node
        layoutUGrpPrcss.cells("b").progressOn();
        dsUGProcess.load("php/api/accessgranting/grpprocessgrid.php?menuid=" + id + "&gprid=" + grpid + "&t=" + encodeURIComponent(global_token), "json", function () {
            layoutUGrpPrcss.cells("b").progressOff();
            grid_ugprocess.sync(dsUGProcess);
        });
    }

    function shareProcess()
    {
        var gid = grid_groups.getSelectedRowId();
        if (!gid)
        {
            dhtmlx.alert({
                text: "Please select a User Group!",
                type: "alert-warning",
                title: "Share Process",
                callback: function () {
                }
            });

            return;
        }

        var selectedprcsids = grid_process.getCheckedRows(0);
        if (!selectedprcsids)
        {
            dhtmlx.alert({
                text: "Please select at least one Process!",
                type: "alert-warning",
                title: "Share Process",
                callback: function () {
                }
            });

            return;
        }

        var params = "grpid=" + gid + "&prcsids=" + selectedprcsids + "&t=" + encodeURIComponent(global_token);
        sublayout.progressOn();
        dhtmlxAjax.post("php/api/accessgranting/giveprocess.php", params, function (loader) {
            sublayout.progressOff();

            if (loader)
            {
                if (loader.xmlDoc.responseURL == "")
                {
                    dhtmlx.alert({
                        text: "Connection Lost!",
                        type: "alert-warning",
                        title: "SHARE",
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
                        title: "SHARE",
                        callback: function () {
                        }
                    });
                    return false;
                }
                if (json_obj.OUTCOME == "OK")
                {
                    dsProcess.clearAll();
                    grid_process.clearAll();

                    dsUGProcess.clearAll();
                    grid_ugprocess.clearAll();

                    var itemid = treeMenu.getSelectedItemId();

                    //load the process of the node if any
                    layoutUGrpPrcss.cells("a").progressOn();
                    dsProcess.load("php/api/accessgranting/processgrid.php?menuid=" + itemid + "&grpid=" + gid + "&t=" + encodeURIComponent(global_token), "json", function () {
                        layoutUGrpPrcss.cells("a").progressOff();
                        grid_process.sync(dsProcess);
                    });

                    //load the process of group for the selected node

                    layoutUGrpPrcss.cells("b").progressOn();
                    dsUGProcess.load("php/api/accessgranting/grpprocessgrid.php?menuid=" + itemid + "&gprid=" + gid + "&t=" + encodeURIComponent(global_token), "json", function () {
                        layoutUGrpPrcss.cells("b").progressOff();
                        grid_ugprocess.sync(dsUGProcess);
                    });


                } else
                {
                    dhtmlx.alert({
                        text: json_obj.OUTCOME,
                        type: "alert-warning",
                        title: "SHARE",
                        callback: function () {
                        }
                    });
                }

            }


        });
    }

    function deleteProcess()
    {
        var gid = grid_groups.getSelectedRowId();
        
        if (!gid)
        {
            dhtmlx.alert({
                text: "Please Select a User Group!",
                type: "alert-warning",
                title: "Delete Process",
                callback: function () {
                }
            });
            return;
        }
        
        //cannot remove menu from admin group
        var item = dsGroups.item(gid);
        if(item.ugroup == "ADMIN")
        {
            dhtmlx.alert({
                text: "Cannot deny ADMIN group PROCESSES",
                type: "alert-warning",
                title: "Remove Process",
                callback: function () {
                }
            });
            
            return;
        }
        
        var selectedprcsids = grid_ugprocess.getCheckedRows(0);
        if (!selectedprcsids)
        {
            dhtmlx.alert({
                text: "Please select at least one Assigned Process!",
                type: "alert-warning",
                title: "Delete Process",
                callback: function () {
                }
            });

            return;
        }

        var params = "grpid=" + gid + "&prcsids=" + selectedprcsids + "&t=" + encodeURIComponent(global_token);
        sublayout.progressOn();
        dhtmlxAjax.post("php/api/accessgranting/deleteprocess.php", params, function (loader) {
            sublayout.progressOff();

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
                    dsProcess.clearAll();
                    grid_process.clearAll();

                    dsUGProcess.clearAll();
                    grid_ugprocess.clearAll();

                    var itemid = treeMenu.getSelectedItemId();

                    //load the process of the node if any
                    layoutUGrpPrcss.cells("a").progressOn();
                    dsProcess.load("php/api/accessgranting/processgrid.php?menuid=" + itemid + "&grpid=" + gid + "&t=" + encodeURIComponent(global_token), "json", function () {
                        layoutUGrpPrcss.cells("a").progressOff();
                        grid_process.sync(dsProcess);
                    });

                    //load the process of group for the selected node

                    layoutUGrpPrcss.cells("b").progressOn();
                    dsUGProcess.load("php/api/accessgranting/grpprocessgrid.php?menuid=" + itemid + "&gprid=" + gid + "&t=" + encodeURIComponent(global_token), "json", function () {
                        layoutUGrpPrcss.cells("b").progressOff();
                        grid_ugprocess.sync(dsUGProcess);
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
                }

            }

        });
    }
    
    function applyrights()
    {       
        for(var i = 0; i < json_rights.length; i++)
        {
            if(json_rights[i].PROCESSNAME == "GIVE MENU" && json_rights[i].ALLOWED == "N")
            {
                tb_menu.disableItem("give");
                tb_menu.setItemToolTip("give", "Not Allowed");
            }
            else if(json_rights[i].PROCESSNAME == "DELETE MENU" && json_rights[i].ALLOWED == "N")
            {
                tb_ugrpmenu.disableItem("delete");
                tb_ugrpmenu.setItemToolTip("delete", "Not Allowed");
            }
            else if(json_rights[i].PROCESSNAME == "GIVE PROCESS" && json_rights[i].ALLOWED == "N")
            {
                tb_prcs.disableItem("share");
                tb_prcs.setItemToolTip("share", "Not Allowed");
            }
            else if(json_rights[i].PROCESSNAME == "DELETE PROCESS" && json_rights[i].ALLOWED == "N")
            {
                tb_ugrpprcs.disableItem("delete");
                tb_ugrpprcs.setItemToolTip("delete", "Not Allowed");
            }
        }
    }

}