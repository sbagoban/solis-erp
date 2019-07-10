<?php

function menu_buildmenu($con, $grpid, $selectedmenuid) {

    $_SESSION["menu_item_selected_open"] = "";

    $sql = "SELECT m.* 
            FROM tblmenu m 
            INNER JOIN tblgrpmenurights gmr on m.menuid = gmr.menufk and gmr.groupfk = :groupfk
            WHERE m.inout = 'O' AND m.parentfk = 0 
            ORDER BY ordering";

    $query = $con->prepare($sql);
    $query->execute(array(":groupfk" => $grpid));
    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        if ($row["leaf"] == "Y") {

            
            echo '<li id="' . $row["menusysid"] . '">';
            echo '<a href="index.php?m=' . $row["menusysid"] . '">';
            echo '<i class="fa ' . $row["icon"] . '" style="color:#22899b"></i> <span> ' . $row["menuname"] . '</span>';
            echo '<span class="pull-right-container">';
            echo '</span>';
            echo '</a>';
            echo '</li>';
        } else {


            echo '<li class="treeview" id="' . $row["menusysid"] . '">';
                echo '<a href="#">';
                    echo '<i class="fa ' . $row["icon"] . '" style="color:#22899b"></i>';
                    echo '<span> ' . $row["menuname"] . '</span>';
                    echo '<span class="pull-right-container">';
                        echo '<i class="fa fa-angle-left pull-right"></i>';
                    echo '</span>';
                echo '</a>';
                echo '<ul class="treeview-menu">';
                    build_menuL2($row["menuid"], $con, $grpid, $selectedmenuid);
                echo '</ul>';
            echo '</li>';
        }
    }
}

function build_menuL2($parentid, $con, $grpid, $selectedmenuid) {


    $sql = "SELECT m.* 
            FROM tblmenu m 
            INNER JOIN tblgrpmenurights gmr on m.menuid = gmr.menufk and gmr.groupfk = :groupfk
            WHERE m.inout='O' AND m.parentfk = :parentfk
            ORDER BY ordering";

    $query = $con->prepare($sql);
    $query->execute(array(":groupfk" => $grpid, ":parentfk" => $parentid));



    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

        echo '<li>';
        echo '<a href="index.php?m=' . $row["menusysid"] . '">';
        echo '<i class="fa ' . $row["icon"] . '" style="color:#22899b"></i> ' . $row["menuname"];
        echo '</a>';
        echo '</li>';
    }
}

?>