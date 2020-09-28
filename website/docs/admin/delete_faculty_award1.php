<?php
require_once('../awards-config.php');
require_once('../library/HTMLPurifier.auto.php');
$purifier = new HTMLPurifier();
$award_id = $purifier->purify($_REQUEST['award_id']);
$table = $purifier->purify($_REQUEST['table']);
$keyword_search = $purifier->purify($_REQUEST['keyword_search']);


if (isset($_REQUEST['dataid']) && is_numeric($_REQUEST['dataid'])) {
   
    $dataid = $purifier->purify($_REQUEST['dataid']);
    $result = mysqli_query($conn, "DELETE FROM $table WHERE id =$dataid")  or die(mysqli_error($conn));
    header("Location: add_nominations.php?award_id=$award_id&keyword_search=$keyword_search");

}

?>
