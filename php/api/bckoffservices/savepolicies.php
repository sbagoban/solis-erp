<?php

    try {

        //=-================== CATCH ALL WARNINGS INTO ERROR TRAP =======================
        set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
            // error was suppressed with the @-operator
            if (0 === error_reporting()) {
                return false;
            }
            throw new Exception($errstr . " " . $errno);
        });
        //================================================================================


        session_start();
        
        if (!isset($_SESSION["solis_userid"])) {
            throw new Exception("NO LOG IN!");
        }
        
        if (!isset($_GET["t"])) {
            throw new Exception("INVALID TOKEN1");
        }
        if ($_GET["t"] != $_SESSION["token"]) {
            throw new Exception("INVALID TOKEN2");
        }
        
        if (!isset($_POST["id"])) {
            throw new Exception("INVALID ID");
        }
        // get id service to edit
        
        require_once("../../connector/pdo_connect_main.php");

        $id = $_POST["id"];
        // set service new values for cost details
        $settingapplyto_policies = trim($_POST["settingapplyto_policies"]);
        $pickoffdropoff_policies = trim($_POST["pickoffdropoff_policies"]);
        $crossseasonsrates_policies = trim($_POST["crossseasonsrates_policies"]);
        $infantmin_policies = trim($_POST["infantmin_policies"]);
        $infantmax_policies = trim($_POST["infantmax_policies"]);
        $childmin_policies = trim($_POST["childmin_policies"]);
        $childmax_policies = trim($_POST["childmax_policies"]);
        $teenmin_policies = trim($_POST["teenmin_policies"]);
        $teenmax_policies = trim($_POST["teenmax_policies"]);
        $adultmin_policies = trim($_POST["adultmin_policies"]);
        $adultmax_policies = trim($_POST["adultmax_policies"]);
        $starton_monday_policies = trim($_POST["starton_monday_policies"]);
        $starton_tuesday_policies = trim($_POST["starton_tuesday_policies"]);
        $starton_wednesday_policies = trim($_POST["starton_wednesday_policies"]);
        $starton_thursday_policies = trim($_POST["starton_thursday_policies"]);
        $starton_friday_policies = trim($_POST["starton_friday_policies"]);
        $starton_saturday_policies = trim($_POST["starton_saturday_policies"]);
        $starton_sunday_policies = trim($_POST["starton_sunday_policies"]);
        $mustinclude_monday_policies = trim($_POST["mustinclude_monday_policies"]);
        $mustinclude_tuesday_policies = trim($_POST["mustinclude_tuesday_policies"]);
        $mustinclude_wednesday_policies = trim($_POST["mustinclude_wednesday_policies"]);
        $mustinclude_thursday_policies = trim($_POST["mustinclude_thursday_policies"]);
        $mustinclude_friday_policies = trim($_POST["mustinclude_friday_policies"]);
        $mustinclude_saturday_policies = trim($_POST["mustinclude_saturday_policies"]);
        $mustinclude_sunday_policies = trim($_POST["mustinclude_sunday_policies"]);

        $con = pdo_con();
        $sql = "UPDATE tblexcursion_services SET 
                        settingapplyto_policies=:settingapplyto_policies,
                        pickoffdropoff_policies=:pickoffdropoff_policies,
                        crossseasonsrates_policies=:crossseasonsrates_policies,
                        infantmin_policies=:infantmin_policies,
                        infantmax_policies=:infantmax_policies,
                        childmin_policies=:childmin_policies,
                        childmax_policies=:childmax_policies,
                        teenmin_policies=:teenmin_policies,
                        teenmax_policies=:teenmax_policies,
                        adultmin_policies=:adultmin_policies,
                        adultmax_policies=:adultmax_policies,
                        starton_monday_policies=:starton_monday_policies,
                        starton_tuesday_policies=:starton_tuesday_policies,
                        starton_wednesday_policies=:starton_wednesday_policies,
                        starton_thursday_policies=:starton_thursday_policies,
                        starton_friday_policies=:starton_friday_policies,
                        starton_saturday_policies=:starton_saturday_policies,
                        starton_sunday_policies=:starton_sunday_policies,
                        mustinclude_monday_policies=:mustinclude_monday_policies,
                        mustinclude_tuesday_policies=:mustinclude_tuesday_policies,
                        mustinclude_wednesday_policies=:mustinclude_wednesday_policies,
                        mustinclude_thursday_policies=:mustinclude_thursday_policies,
                        mustinclude_friday_policies=:mustinclude_friday_policies,
                        mustinclude_saturday_policies=:mustinclude_saturday_policies,
                        mustinclude_sunday_policies=:mustinclude_sunday_policies
                        WHERE id=:id";

        $stmt = $con->prepare($sql);                        
        $stmt->execute(array(
                ":id" => $id,
                ":settingapplyto_policies" => $settingapplyto_policies,
                ":pickoffdropoff_policies" => $pickoffdropoff_policies,
                ":crossseasonsrates_policies" => $crossseasonsrates_policies,
                ":infantmin_policies" => $infantmin_policies,
                ":infantmax_policies" => $infantmax_policies,
                ":childmin_policies" => $childmin_policies,
                ":childmax_policies" => $childmax_policies,
                ":teenmin_policies" => $teenmin_policies,
                ":teenmax_policies" => $teenmax_policies,
                ":adultmin_policies" => $adultmin_policies,
                ":adultmax_policies" => $adultmax_policies,
                ":starton_monday_policies" => $starton_monday_policies,
                ":starton_tuesday_policies" => $starton_tuesday_policies,
                ":starton_wednesday_policies" => $starton_wednesday_policies,
                ":starton_thursday_policies" => $starton_thursday_policies,
                ":starton_friday_policies" => $starton_friday_policies,
                ":starton_saturday_policies" => $starton_saturday_policies,
                ":starton_sunday_policies" => $starton_sunday_policies,
                ":mustinclude_monday_policies" => $mustinclude_monday_policies,
                ":mustinclude_tuesday_policies" => $mustinclude_tuesday_policies,
                ":mustinclude_wednesday_policies" => $mustinclude_wednesday_policies,
                "mustinclude_thursday_policies" => $mustinclude_thursday_policies,
                ":mustinclude_friday_policies" => $mustinclude_friday_policies,
                ":mustinclude_saturday_policies" => $mustinclude_saturday_policies,
                ":mustinclude_sunday_policies" => $mustinclude_sunday_policies));
    }
    catch (Exception $ex) {
        die(json_encode(array("OUTCOME" => "ERROR: " . $ex->getMessage())));
    }

    echo json_encode(array("OUTCOME" => "OK"));
?>
