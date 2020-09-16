<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
require_once('../library/HTMLPurifier.auto.php');
$purifier = new HTMLPurifier();
$award_id = $purifier->purify($_REQUEST['award_id']);
$fac_id = $purifier->purify($_REQUEST['faculty_id']);
$nonchemfaculty = $purifier->purify($_REQUEST['nonchemfaculty']);
$status = $purifier->purify($_REQUEST['status']);
$year = $purifier->purify($_REQUEST['year']);
$comment = $purifier->purify($_REQUEST['comment']);
$keyword_search = $purifier->purify($_REQUEST['keyword_search']);

if (isset($_REQUEST['award_id'])) {
   if (($status == 'error') OR (!($nonchemfaculty == '' XOR $fac_id == 'error'))) {
       // generate error message
       $error = 'ERROR: status or faculty name is empty!';
    // if either field is blank, display the form again
     header("Location: add_nominations.php?error=$error&award_id=$award_id&keyword_search=$keyword_search");
 }
 else {
   if ($fac_id !== 'error') {
     $sql = "INSERT INTO `faculty_awards`(`faculty_id`, `award_id`, `status`, `year`, `comment`) VALUES ($fac_id, $award_id, '$status', '$year', '$comment')";
   }
   else {
     $sql = "INSERT INTO `faculty_awards_notchem`(`name`, `award_id`, `status`, `year`, `comment`) VALUES ('$nonchemfaculty', $award_id, '$status', '$year', '$comment')";
   }
//echo $sql;
//exit;
    $result = mysqli_query($conn, $sql);
    if (!($result)) {
      $error = urlencode(mysqli_error($conn));
     header("Location: add_nominations.php?error=$error&award_id=$award_id&keyword_search=$keyword_search");
    }
    else {
        // once saved, redirect back to the view page
     header("Location: add_nominations.php?award_id=$award_id&keyword_search=$keyword_search");
    }
 }
}










?>
