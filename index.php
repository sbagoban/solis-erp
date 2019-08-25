<?php
session_start();
if (!isset($_SESSION["solis_userid"])) {
    //user already logged in 
    header("Location: login.php");
    exit();
}

require_once("./php/utils/utilities.php");
require_once("./php/api/index/menu.php");
require_once("./php/connector/pdo_connect_main.php");

$con = pdo_con();

$relative_server_path = utils_getsysparams($con, "USER", "PHOTO", "RELATIVE_PATH");


if (isset($_GET["m"])) {
    $menu = $_GET["m"];

    if ($menu != "norights") {
        if (!utils_firewall_menu_rights($con, $menu, $_SESSION["solis_grpid"])) {
            header("Location: index.php?m=norights");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <link rel="icon" href="images/logo_solis_connect.png" type="image/x-icon"/>
        <link rel="shortcut icon" href="images/logo_solis_connect.png" type="image/x-icon"/>


        <meta name="description" content="SOLIS CONNECT">
        <meta name="author" content="SOLIS">
        <title>SOLIS CONNECT</title>   

        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
        <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
        <link rel="stylesheet" href="dist/css/AdminLTE.css?<?php echo time(); ?>">

        <!-- Sandeep Start -->
        <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min.css">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="css/bookingEngine.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.10.6/jquery.typeahead.min.css">
        <link rel="stylesheet" href="bower_components/bootstrap-duration-picker/dist/bootstrap-duration-picker.css">
        <link rel="stylesheet" href="css/editor.css">
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/css/dataTables.bootstrap.min.css'>
        <?php
        $menu = "";
        if (isset($_GET["m"])) {
            $menu = $_GET["m"];
        }
        if ($menu == "backoff_excursions") {
            //load "css/gridStyle.css" only for "backoff_excursions" interface
            echo '<link rel="stylesheet" href="css/gridStyle.css">';
        }
        ?>

        <!-- Sandeep End -->

        <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

        <script src="js/utils/utils.js?<?php echo time(); ?>"></script>
        <script src="js/utils/global_vars.js?<?php echo time(); ?>"></script>
        <script src="js/utils/md5.min.js?<?php echo time(); ?>"></script>
        <script src="js/logout/logout.js?<?php echo time(); ?>"></script>
        <script src="js/index/index.js?<?php echo time(); ?>"></script>

        <script type="text/javascript" src="libraries/dhtmlx/dhtmlx.js"></script>
        <link rel="stylesheet" type="text/css" href="libraries/dhtmlx/dhtmlx.css?<?php echo time(); ?>">

        <script type="text/javascript" src="libraries/dhtmlx/dhtmlxdataprocessor.js"></script>
        <script type="text/javascript" src="php/connector/connector.js"></script>
        <script type="text/javascript" src="libraries/dhtmlx/dhtmlxgrid_export.js"></script>
        <script type="text/javascript" src="libraries/dhtmlx/dhtmlxform_item_upload.js"></script>
        <script type="text/javascript" src="libraries/dhtmlx/dhtmlxform_item_colorpicker.js"></script>
        <script type="text/javascript" src="libraries/dhtmlx/dhtmlxgrid_post.js"></script>



        <script>
            dhtmlx.image_path = "libraries/dhtmlx/imgs/";
        </script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Google Font -->
        <link rel="stylesheet"
              href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    </head>
    <body class="hold-transition <?php echo $_SESSION["solis_skin"]; ?> sidebar-mini">
        <div class="wrapper">

            <header class="main-header">

                <!-- Logo -->
                <a href="index.php" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini"><b>S</b>olis</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg"><b>Solis</b> Connect</span>
                </a>

                <!-- Header Navbar: style can be found in header.less -->
                <nav class="navbar navbar-static-top">
                    <!-- Sidebar toggle button-->
                    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>
                    <!-- Navbar Right Menu -->
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <!-- Messages: style can be found in dropdown.less-->
                            <li class="dropdown messages-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-envelope-o"></i>
                                    <span class="label label-success">4</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 4 messages</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- start message -->
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="<?php echo $relative_server_path . $_SESSION["solis_userimage"]; ?>" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Support Team
                                                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                    </h4>
                                                    <p>Feedback on dossier SIFI3456</p>
                                                </a>
                                            </li>
                                            <!-- end message -->
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Stephane
                                                        <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                    </h4>
                                                    <p>Query about Excursion on rates</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Kevin
                                                        <small><i class="fa fa-clock-o"></i> Today</small>
                                                    </h4>
                                                    <p>Need implement Import Template</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Marketing Department
                                                        <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                                    </h4>
                                                    <p>API ready?</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <div class="pull-left">
                                                        <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                                                    </div>
                                                    <h4>
                                                        Tour Operators
                                                        <small><i class="fa fa-clock-o"></i> 2 days</small>
                                                    </h4>
                                                    <p>Nearly linked with APIs?</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">See All Messages</a></li>
                                </ul>
                            </li>
                            <!-- Notifications: style can be found in dropdown.less -->
                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-bell-o"></i>
                                    <span class="label label-warning">10</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 10 notifications</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-users text-aqua"></i> Dossier SIFI8900 confirmed
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-warning text-yellow"></i> Transfer Interhotel cancelled on 3rd Septempted
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-users text-red"></i> Priscille added to SIFI2356 following
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-shopping-cart text-green"></i> 20 External Users added by Tour Operators
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#">
                                                    <i class="fa fa-user text-red"></i> You changed your username
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="#">View all</a></li>
                                </ul>
                            </li>
                            <!-- Tasks: style can be found in dropdown.less -->
                            <li class="dropdown tasks-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-flag-o"></i>
                                    <span class="label label-danger">9</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header">You have 9 tasks</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Implement APIs
                                                        <small class="pull-right">20%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                                                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">20% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Implement Hotel Contracts
                                                        <small class="pull-right">40%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar"
                                                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">40% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Personal Task: Create system tables
                                                        <small class="pull-right">60%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar"
                                                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">60% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                            <li><!-- Task item -->
                                                <a href="#">
                                                    <h3>
                                                        Create documentation
                                                        <small class="pull-right">80%</small>
                                                    </h3>
                                                    <div class="progress xs">
                                                        <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar"
                                                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                            <span class="sr-only">80% Complete</span>
                                                        </div>
                                                    </div>
                                                </a>
                                            </li>
                                            <!-- end task item -->
                                        </ul>
                                    </li>
                                    <li class="footer">
                                        <a href="#">View all tasks</a>
                                    </li>
                                </ul>
                            </li>
                            <!-- User Account: style can be found in dropdown.less -->
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?php echo $relative_server_path . $_SESSION["solis_userimage"]; ?>" class="user-image" alt="User Image">
                                    <span class="hidden-xs"><?php echo $_SESSION["solis_username"]; ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <!-- User image -->
                                    <li class="user-header">
                                        <img src="<?php echo $relative_server_path . $_SESSION["solis_userimage"]; ?>" class="img-circle" alt="User Image">

                                        <p>
                                            <?php echo $_SESSION["solis_username"]; ?> - <?php echo $_SESSION["solis_grpname"]; ?>
                                            <small>Created on <?php echo date_format(date_create($_SESSION["solis_usercreated"]), "M Y"); ?></small>
                                        </p>
                                    </li>
                                    <!-- Menu Body -->

                                    <!-- Menu Footer-->
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="index.php?m=userprofile" class="btn btn-default btn-flat">Profile</a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="#" class="btn btn-default btn-flat" onclick="logout_logout();">Sign out</a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- Control Sidebar Toggle Button -->
                            <li>
                                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                            </li>
                        </ul>
                    </div>

                </nav>
            </header>
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar-collapse">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="<?php echo $relative_server_path . $_SESSION["solis_userimage"]; ?>" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <p><?php echo $_SESSION["solis_username"]; ?></p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search...">
                            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu" data-widget="tree">
                        <?php
                        $menu = "";
                        if (isset($_GET["m"])) {
                            $menu = $_GET["m"];
                        }

                        $userid = $_SESSION["solis_userid"];
                        $grpid = $_SESSION["solis_grpid"];


                        menu_buildmenu($con, $grpid, $menu);
                        ?>
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" id="divsection">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        <div id="aTitle">
                            Newsroom
                        </div>
                    </h1>

                </section>

                <!-- Main content -->
                <section class="content">

                    <?php
                    $menu = "";
                    if (isset($_GET["m"])) {
                        $menu = $_GET["m"];
                    }

                    if ($menu == "newsroom" || $menu == "") {
                        include 'php/template/newsroom.php';
                    } else if ($menu == "userprofile") {
                        include 'php/template/userprofile.php';
                    } else if ($menu == "norights") {
                        include 'php/template/norights.php';
                    } else if ($menu == "managebookings") {
                        include 'php/template/booking_engine.php';
                    } else if ($menu == "backoff_excursions") {
                        include 'php/template/addexcursions.php';
                    } else {
                        echo '<div id="main_body" style="position:relative; top:0px; left:0px; width: 1000px; height: 550px; "></div>';
                    }
                    ?>
                </section>
                <!-- /.content -->


            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 2.0
                </div>
                <strong>Copyright &copy; 2019 <a href="http://www.solis-io.com/">Solis Indian Ocean</a>.</strong> All rights
                reserved.
            </footer>


            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Create the tabs -->
                <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                    <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
                    <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane" id="control-sidebar-home-tab">
                        <h3 class="control-sidebar-heading">Recent Activity</h3>
                        <ul class="control-sidebar-menu">
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                                        <p>Will be 23 on April 24th</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-user bg-yellow"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                                        <p>New phone +1(800)555-1234</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                                        <p>nora@example.com</p>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="menu-icon fa fa-file-code-o bg-green"></i>

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                                        <p>Execution time 5 seconds</p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <!-- /.control-sidebar-menu -->

                        <h3 class="control-sidebar-heading">Tasks Progress</h3>
                        <ul class="control-sidebar-menu">
                            <li>
                                <a href="javascript:void(0)">
                                    <h4 class="control-sidebar-subheading">
                                        Custom Template Design
                                        <span class="label label-danger pull-right">70%</span>
                                    </h4>

                                    <div class="progress progress-xxs">
                                        <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <h4 class="control-sidebar-subheading">
                                        Update Resume
                                        <span class="label label-success pull-right">95%</span>
                                    </h4>

                                    <div class="progress progress-xxs">
                                        <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <h4 class="control-sidebar-subheading">
                                        Laravel Integration
                                        <span class="label label-warning pull-right">50%</span>
                                    </h4>

                                    <div class="progress progress-xxs">
                                        <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0)">
                                    <h4 class="control-sidebar-subheading">
                                        Back End Framework
                                        <span class="label label-primary pull-right">68%</span>
                                    </h4>

                                    <div class="progress progress-xxs">
                                        <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <!-- /.control-sidebar-menu -->

                    </div>
                    <!-- /.tab-pane -->

                    <!-- Settings tab content -->
                    <div class="tab-pane" id="control-sidebar-settings-tab">
                        <form method="post">
                            <h3 class="control-sidebar-heading">General Settings</h3>

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Report panel usage
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                <p>
                                    Some information about this general settings option
                                </p>
                            </div>
                            <!-- /.form-group -->

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Allow mail redirect
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                <p>
                                    Other sets of options are available
                                </p>
                            </div>
                            <!-- /.form-group -->

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Expose author name in posts
                                    <input type="checkbox" class="pull-right" checked>
                                </label>

                                <p>
                                    Allow the user to show his name in blog posts
                                </p>
                            </div>
                            <!-- /.form-group -->

                            <h3 class="control-sidebar-heading">Chat Settings</h3>

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Show me as online
                                    <input type="checkbox" class="pull-right" checked>
                                </label>
                            </div>
                            <!-- /.form-group -->

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Turn off notifications
                                    <input type="checkbox" class="pull-right">
                                </label>
                            </div>
                            <!-- /.form-group -->

                            <div class="form-group">
                                <label class="control-sidebar-subheading">
                                    Delete chat history
                                    <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                                </label>
                            </div>
                            <!-- /.form-group -->
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
            </aside>
            <!-- /.control-sidebar -->
            <!-- Add the sidebar's background. This div must be placed
                 immediately after the control sidebar -->
            <div class="control-sidebar-bg"></div>

        </div>
        <!-- ./wrapper -->

        <!-- jQuery 3 -->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script type="text/javascript" src="libraries/jquery/jquery.maskedinput.min.js"></script>

        <!-- Sandeep Start -->
        <script src="bower_components/jquery-ui/jquery-ui.min.js"></script>
        <script src="bower_components/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script src="bower_components/bootstrap-duration-picker/dist/bootstrap-duration-picker.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.min.js"></script>
        <script src="https://code.jquery.com/jquery-migrate-3.0.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.10.6/jquery.typeahead.min.js"></script>
        <script src "https://cdn.datatables.net/plug-ins/1.10.15/sorting/stringMonthYear.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/dataTables.bootstrap.min.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.flash.min.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>
        <!-- Sandeep End -->

        <!-- Bootstrap 3.3.7 -->
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- FastClick -->
        <script src="bower_components/fastclick/lib/fastclick.js"></script>
        <!-- AdminLTE App -->
        <script src="dist/js/adminlte.min.js"></script>
        <!-- Sparkline -->
        <script src="bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
        <!-- jvectormap  -->
        <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
        <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
        <!-- SlimScroll -->
        <script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <!-- ChartJS -->
        <script src="bower_components/chart.js/Chart.js"></script>

        <!-- AdminLTE for demo purposes -->
        <script src="dist/js/demo.js?<?php echo time(); ?>"></script>

        <script>
                                                $('[data-toggle="push-menu"]').pushMenu('toggle');
        </script>

        <?php
        if (isset($_SESSION["menu_item_selected_open"])) {
            echo '<script>' . $_SESSION["menu_item_selected_open"] . '</script>';
        }

//generate token
        $token = openssl_random_pseudo_bytes(16);
        $token = bin2hex($token);
        $_SESSION["token"] = $token;
        echo '<script>var global_token="' . $token . '"</script>';

        $menu = "";

        if (isset($_GET["m"])) {
            $menu = $_GET["m"];
        }

        echo '<script>var json_rights=' . utils_loadRights($menu, $con, $_SESSION["solis_grpid"]) . '</script>';

        if ($menu == "usergroups") {
            echo '<script type="text/javascript" src="js/usergroups/usergroups.js?' . time() . '"></script>';
        } else if ($menu == "users") {
            echo '<script type="text/javascript" src="js/users/users.js?' . time() . '"></script>';
        } else if ($menu == "accessgranting") {
            echo '<script type="text/javascript" src="js/accessgranting/accessgranting.js?' . time() . '"></script>';
        } else if ($menu == "countries") {
            echo '<script type="text/javascript" src="js/countries/countries.js?' . time() . '"></script>';
        } else if ($menu == "areas") {
            echo '<script>var default_country_id = "' . utils_getDefaultCountry($con) . '";</script>';
            echo '<script type="text/javascript" src="js/areas/areas.js?' . time() . '"></script>';
        } else if ($menu == "company_type") {
            echo '<script type="text/javascript" src="js/companytype/companytype.js?' . time() . '"></script>';
        } else if ($menu == "ratings") {
            echo '<script type="text/javascript" src="js/ratings/ratings.js?' . time() . '"></script>';
        } else if ($menu == "coasts") {
            echo '<script type="text/javascript" src="js/coasts/coasts.js?' . time() . '"></script>';
        } else if ($menu == "grphotels") {
            echo '<script type="text/javascript" src="js/grphotels/grphotels.js?' . time() . '"></script>';
        } else if ($menu == "hoteltype") {
            echo '<script type="text/javascript" src="js/hoteltype/hoteltype.js?' . time() . '"></script>';
        } else if ($menu == "mealplans") {
            echo '<script type="text/javascript" src="js/mealplans/mealplans.js?' . time() . '"></script>';
        } else if ($menu == "optservices") {
            echo '<script type="text/javascript" src="js/optionalservices/optionalservices.js?' . time() . '"></script>';
        } else if ($menu == "airports") {
            echo '<script>var default_country_id = "' . utils_getDefaultCountry($con) . '";</script>';
            echo '<script type="text/javascript" src="js/airports/airports.js?' . time() . '"></script>';
        } else if ($menu == "childrenages") {
            echo '<script type="text/javascript" src="js/childrenages/childrenages.js?' . time() . '"></script>';
        } else if ($menu == "bankdetails") {
            echo '<script>var default_country_id = "' . utils_getDefaultCountry($con) . '";</script>';
            echo '<script type="text/javascript" src="js/banks/banks.js?' . time() . '"></script>';
        } else if ($menu == "exgrates") {
            echo '<script type="text/javascript" src="js/exchangerates/exchangerates.js?' . time() . '"></script>';
        } else if ($menu == "companies") {
            echo '<script>var default_country_id = "' . utils_getDefaultCountry($con) . '";</script>';
            echo '<script type="text/javascript" src="js/companies/companies.js?' . time() . '"></script>';
        } else if ($menu == "to") {
            echo '<script type="text/javascript" src="js/touroperators/touroperators.js?' . time() . '"></script>';
        } else if ($menu == "categovehicles") {
            echo '<script type="text/javascript" src="js/vehicletypes/vehicletypes.js?' . time() . '"></script>';
        } else if ($menu == "servicetype") {
            echo '<script type="text/javascript" src="js/servicetypes/servicetypes.js?' . time() . '"></script>';
        } else if ($menu == "bckoffhotels") {
            echo '<script>'
            . 'var default_country_id = "' . utils_getDefaultCountry($con) . '";'
            . 'var default_hoteltype_id = "' . utils_getDefaultHotelType($con) . '";'
            . 'var select_hotel_id = ""; ';

            if (isset($_GET["hid"])) {
                echo 'select_hotel_id = "' . $_GET["hid"] . '"';
            }
            echo '</script>';

            echo '<script type="text/javascript" src="js/bckoffhotels/bckoffhotels.js?' . time() . '"></script>';
        } else if ($menu == "markets") {
            echo '<script type="text/javascript" src="js/markets/markets.js?' . time() . '"></script>';
        } else if ($menu == "" || $menu == "newsroom") {
            echo '<script src="dist/js/pages/dashboard2.js?' . time() . '"></script>';
        } else if ($menu == "userprofile") {
            echo '<script src="js/userprofile/userprofile.js?' . time() . '"></script>';
        } else if ($menu == "seasons") {
            echo '<script src="js/seasons/seasons.js?' . time() . '"></script>';
        } else if ($menu == "hotelcontracts") {
            if (!isset($_GET["hid"])) {
                header("Location: index.php?m=norights");
                exit();
            }
            echo '<script>var global_hotel_id = "' . $_GET["hid"] . '";</script>';

            echo '<script src="js/hotelcontracts/hotelcontracts.js?' . time() . '"></script>';
        } else if ($menu == "hotelspecialoffers") {
            if (!isset($_GET["hid"])) {
                header("Location: index.php?m=norights");
                exit();
            }
            echo '<script>var global_hotel_id = "' . $_GET["hid"] . '";</script>';

            echo '<script src="js/hotelspecialoffers/hotelspecialoffers.js?' . time() . '"></script>';
        } else if ($menu == "inventory") {
            if (!isset($_GET["hid"])) {
                header("Location: index.php?m=norights");
                exit();
            }
            echo '<script>var global_hotel_id = "' . $_GET["hid"] . '";</script>';

            echo '<script src="js/hotelinventory/hotelinventory.js?' . time() . '"></script>';
        } else if ($menu == "dateperiods") {

            if (!isset($_GET["hid"])) {
                header("Location: index.php?m=norights");
                exit();
            }
            echo '<script>var global_hotel_id = "' . $_GET["hid"] . '";</script>';

            echo '<script type="text/javascript" src="js/dateperiods/dateperiods.js?' . time() . '"></script>';
        } else if ($menu == "sysparams") {
            echo '<script src="js/sysparams/sysparams.js?' . time() . '"></script>';
        } else if ($menu == "ratescalc") {
            echo '<script src="js/ratescalculator/ratescalculator.js?' . time() . '"></script>';
        } else if ($menu == "managebookings") {
            echo '<script src="js/booking_engine/control/accomodationsCtrl.js"></script>
        <script src="js/booking_engine/control/excursionsCtrl.js"></script>
        <script src="js/booking_engine/control/transfersCtrl.js"></script>
        <script src="js/booking_engine/utils/rangeSlider.js"></script>
        <script src="js/booking_engine/script.js"></script>
        <script src="js/booking_engine/models/excursionsModel.js"></script>';
        } else if ($menu == "backoff_excursions") {
            echo '<script src="js/boexcursions/models/boexcursionsQuoteDetailsModel.js"></script>
        <script src="js/boexcursions/models/boexcursionsModel.js"></script>
        <script src="js/boexcursions/utils/editor.js"></script>
        <script src="js/boexcursions/utils/generateOptionCode.js"></script>
        <script src="js/boexcursions/ctrlBackOffExcursions.js"></script>
        <script src="js/boexcursions/scriptExcursionTableGrid.js"></script>
        <script src="js/boexcursions/models/boexcursionsUpdatedetailsModel.js"></script>
        <script src="js/boexcursions/models/boExcursionsSearchModels.js"></script>
        <script src="js/boexcursions/scriptBackOffExcursions.js"></script>';
        }
        ?>
        <div id = "alert_placeholder"></div>
    </body>
</html>
