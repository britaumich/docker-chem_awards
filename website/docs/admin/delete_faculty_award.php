<?php
require_once('../awards-config.php');
$award_id = $purifier->purify($_REQUEST['award_id']);
$table = $purifier->purify($_REQUEST['table']);


if (isset($_REQUEST['dataid']) && is_numeric($_REQUEST['dataid'])) {
   
    $dataid = $purifier->purify($_REQUEST['dataid']);
    $result = mysqli_query($conn, "DELETE FROM $table WHERE id =$dataid")  or die(mysqli_error($conn));
    header("Location: faculty_awards_status.php?award_id=$award_id");

}

?>
