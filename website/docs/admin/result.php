<?php
require_once('../awards-config.php');
$award_id = $purifier->purify($_REQUEST['award_id']);
$year = $purifier->purify($_REQUEST['year']);
$resultyear = $purifier->purify($_REQUEST['resultyear']);
$uniqname = $purifier->purify($_REQUEST['uniqname']);
$faculty_id = $purifier->purify($_REQUEST['faculty_id']);
if (isset($_REQUEST['submit'])) {
 $st = $purifier->purify($_REQUEST['submit']);
 if ($st == 'Received') { $status = 5; }
 if ($st == 'Declined') { $status = 3; }
 
}
if (isset($_REQUEST['award_id'])) {
     $sql = "UPDATE `faculty_awards` SET status = '$status', year = '$resultyear' WHERE faculty_id = $faculty_id AND award_id = $award_id AND year = '$year' AND status = '4'";
//echo $sql;
    $result = mysqli_query($conn, $sql);
    if (!($result)) {
      $error = urlencode(mysqli_error($conn));
     header("Location: nominations.php?error=$error");
    }
    else {
      $sql = "UPDATE award_progress SET got_result = IF($status = 5, 'received', 'declined') WHERE uniqname = '$uniqname' AND award_id = $award_id AND year = '$year' AND submitted = 'yes'";
      $result = mysqli_query($conn, $sql);
    if (!($result)) {
      $error = urlencode(mysqli_error($conn));
     header("Location: nominations.php?error=$error");
    }
        // once saved, redirect back to the view page
     header("Location: nominations.php");
    }
 }

?>
