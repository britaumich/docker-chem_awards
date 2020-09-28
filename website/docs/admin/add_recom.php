<?php
require_once('../awards-config.php');
if (isset($_REQUEST['uniqname'])) {
   $uniqname = $purifier->purify($_REQUEST['uniqname']);
   $rec_name = $purifier->purify($_REQUEST['rec_name']);
   $rec_email = $purifier->purify($_REQUEST['rec_email']);
   if ($rec_name == '' or $rec_email == '') {
       // generate error message
       $error = 'ERROR: name or email is empty!';
    // if either field is blank, display the form again
     header("Location: recommenders.php?error=$error&uniqname=$uniqname");
 }
 elseif (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $rec_email)) {
// elseif (!eregi("^[A-Z0-9._%+-]++@[A-Z0-9.-]++\.[A-Z]{2,}+$", $rec_email)) {
  $error.="The e-mail you entered was not in the proper format!";
     header("Location: recommenders.php?error=$error&uniqname=$uniqname");
 }
 else {
    $rec_name = $purifier->purify($_REQUEST['rec_name']);
   $rec_email = $purifier->purify($_REQUEST['rec_email']);
   $sql = "INSERT INTO recommenders(uniqname, rec_name, rec_email) VALUES ('$uniqname', '$rec_name', '$rec_email')";
    $result = mysqli_query($conn, $sql);
    if (!($result)) {
      $error = urlencode(mysqli_error($conn));
      header("Location: recommenders.php?error=$error&uniqname=$uniqname");  
    }
    else {
        // once saved, redirect back to the view page
      header("Location: recommenders.php?uniqname=$uniqname");  
    }
 }
}










?>
