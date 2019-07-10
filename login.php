<?php
session_start();
if (isset($_SESSION["solis_userid"])) {
    //user already logged in 
    header("Location: index.php");
    exit();
}
?>

<head>
    <title>SOLIS CONNECT</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="icon" href="images/logo_solis_connect.png" type="image/x-icon"/>
    <link rel="shortcut icon" href="images/logo_solis_connect.png" type="image/x-icon"/>

    <link href="libraries/bootstrap4/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="css/login.css?<?php echo time() ?>" rel="stylesheet" id="login-css">
    <script src="libraries/bootstrap4/js/bootstrap.min.js"></script>
    <script src="libraries/jquery/jquery-1.11.1.min.js"></script>
    <script src="js/login/login.js?<?php echo time() ?>"></script>
    <script src="js/utils/utils.js?<?php echo time() ?>"></script>
    <script src="js/utils/md5.min.js?<?php echo time() ?>"></script>

    <script type="text/javascript" src="libraries/dhtmlx/dhtmlx.js"></script>
    <link rel="stylesheet" type="text/css" href="libraries/dhtmlx/dhtmlx.css">

</head>
<body>


    <div class="container">
        <div class="login-container">
            <div id="output"></div>
            <div class="avatar"></div>
            <div class="form-box">
                <form action="" method="">
                    <input name="uname" type="text" placeholder="email" value="karveshg@gmail.com">
                    <input name="pwd" type="password" placeholder="password" value="ACTIMOTO3">
                    <button class="btn btn-info btn-block login" type="submit">Sign In</button>
                </form>
            </div>
        </div>

    </div>
</body>