<style>
    .user-row {
        margin-bottom: 14px;
    }

    .user-row:last-child {
        margin-bottom: 0;
    }

    .dropdown-user {
        margin: 13px 0;
        padding: 5px;
        height: 100%;
    }

    .dropdown-user:hover {
        cursor: pointer;
    }

    .table-user-information > tbody > tr {
        border-top: 1px solid rgb(221, 221, 221);
    }

    .table-user-information > tbody > tr:first-child {
        border-top: 0;
    }


    .table-user-information > tbody > tr > td {
        border-top: 0;
    }
    .toppad
    {
        margin-top:20px;
    }
</style>

<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad" >


    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $_SESSION["solis_userfullname"]; ?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="<?php
                    $relative_server_path = utils_getsysparams($con, "USER", "PHOTO", "RELATIVE_PATH");
                    echo $relative_server_path . $_SESSION["solis_userimage"];
                    ?>" class="img-circle img-responsive"> 
                    <br>
                    <a href="#" onclick="change_userphoto();">Change</a> &nbsp;&nbsp;
                    <a href="#" onclick="reset_userphoto()">Reset</a>
                </div>

                <div class=" col-md-9 col-lg-9 "> 
                    <table class="table table-user-information">
                        <tbody>
                            <tr>
                                <td><b>User Name:</b></td>
                                <td><?php echo $_SESSION["solis_username"]; ?></td>
                            </tr>
                            <tr>
                                <td><b>Email:</b></td>
                                <td><?php echo $_SESSION["solis_useremail"]; ?></td>
                            </tr>
                            <tr>
                                <td><b>User Group:</b></td>
                                <td><?php echo $_SESSION["solis_grpname"]; ?></td>
                            </tr>
                            <tr>
                                <td><b>Department:</b></td>
                                <td><?php
                                    $sql = "select d.deptname, d.deptcode
                                            from tbluserdepts ud inner join tbldepartments d on 
                                            ud.deptfk = d.id
                                            WHERE ud.userfk = :userfk
                                            order by d.deptcode asc";
                                    $stmt = $con->prepare($sql);
                                    $stmt->execute(array(":userfk" => $_SESSION["solis_userid"]));
                                    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo $rw["deptcode"] . " - " . $rw["deptname"] . "<br>";
                                    }
                                    ?></td>
                            </tr>
                            <tr>
                                <td><b>Created On:</b></td>
                                <td><?php echo date_format(date_create($_SESSION["solis_usercreated"]), "M Y"); ?></td>
                            </tr>
                            <tr>
                                <td><b>Activated On:</b></td>
                                <td><?php echo date_format(date_create($_SESSION["solis_useractivated"]), "M Y"); ?></td>
                            </tr>

                            <?php
                            if ($_SESSION["solis_usertoid"] != "") {
                                $sql = "SELECT * FROM tbltouroperator WHERE id=:id";
                                $stmt = $con->prepare($sql);
                                $stmt->execute(array(":id" => $_SESSION["solis_usertoid"]));
                                if ($rw = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<tr>';
                                    echo '<td><b>Company:</b></td>';
                                    echo '<td>' . $rw["toname"] . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Upload Photo</h4>
            </div>

            <!-- Modal body -->
            <label class="custom-file">
                <input type="file" id="file-select" class="custom-file-input">
                <span class="custom-file-control"></span>
            </label>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick='uploadPhoto();' id='upload-button'>Upload</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>