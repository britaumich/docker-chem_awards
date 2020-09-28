<?php
require_once('../awards-config.php');
require_once('../library/HTMLPurifier.auto.php');
$purifier = new HTMLPurifier();
$dataid = $purifier->purify($_REQUEST['dataid']);
$prog_name = $purifier->purify($_REQUEST['prog_name']);
        $sql = "DELETE FROM faculty_awards WHERE id = '$dataid'";
        $result = mysqli_query($conn, $sql);
        if (!($result)) {
                  $error = urlencode(mysqli_error($conn));
            $back = $prog_name . "?&error=" . $error;
            header("Location: $back");  

        }
        else {
            $back = $prog_name;
            header("Location: $back");  

        }

?>
