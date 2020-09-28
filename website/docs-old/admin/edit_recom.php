<?php
function renderForm($conn, $id, $uniqname, $rec_name, $rec_email, $error)
 {
 ?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
 <html>
 <head>
<title>Chemistry Awards System - University of Michigan</title>
<link rel="stylesheet" href="../eebstyle.css">
 </head>
<body>
<?php
 if ($error != '')
 {
 echo '<div style="padding:4px; border:1px solid red; color:red;">'.$error.'</div>';
 }

 ?>
<div align="center">
<table>
 <form action="" method="post">
 <input type="hidden" name="id" value="<?php echo $id; ?>"/>
 <input type="hidden" name="uniqname" id="uniqname" value="<?php echo $uniqname; ?>"/>
<tr><th> ID: 
<td><?php echo $id; ?></tr>
<tr><th>Name: 
<td> <input type="text" name="rec_name" value="<?php echo $rec_name; ?>"/></tr>
<tr><th>Email: 
<td> <input type="text" name="rec_email" value="<?php echo $rec_email; ?>"/></tr>
</table>
 <input type="submit" name="submit" value="Submit">
 </div>
<div align='center'><img src='../images/linecalendarpopup500.jpg'></div><br>

 </form>
 </body>
 </html>
 <?php
 }  // function
ob_start();
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
require_once('nav.php');

$uniqname = $_REQUEST['uniqname'];
if (isset($_REQUEST['submit'])) {
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
   $id = $purifier->purify($_REQUEST['id']);
   $rec_name = $purifier->purify($_REQUEST['rec_name']);
   $rec_email = $purifier->purify($_REQUEST['rec_email']);
   if ($rec_name == '' or $rec_email == '') {
       // generate error message
       $error = 'ERROR: name or email is empty!';
    // if either field is blank, display the form again
      renderForm($conn, $id, $uniqname, $rec_name, $rec_email, $error);
   }  // empty
   elseif (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $rec_email)) {
   $error.="The e-mail you entered was not in the proper format!";
      renderForm($conn, $id, $uniqname, $rec_name, $rec_email, $error);
 }

   else
   {
       // save the data to the database
      mysqli_query($conn, "UPDATE recommenders SET rec_name='$rec_name', rec_email='$rec_email' WHERE id='$id'") or die(mysqli_error($conn));
             // once saved, redirect back to the view page
      header("Location: recommenders.php?uniqname=$uniqname");
ob_end_flush();
   }

 } //    isset $id
 else
 {
 // if the 'id' isn't valid, display an error
 echo 'Error!';
 }


}  // isset($_POST['submit']
else {
// if the form hasn't been submitted, get the data from the db and display the form
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0) {
      // query db
   $id = $purifier->purify($_REQUEST['id']);
   $result = mysqli_query($conn, "SELECT * FROM recommenders WHERE id=$id") or die(mysqli_error($conn));
   $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    if($row) {
         // get data from db
          $uniqname = $row['uniqname'];
          $rec_name = $row['rec_name'];
          $rec_email = $row['rec_email'];
       // show form
        renderForm($conn, $id, $uniqname, $rec_name, $rec_email, $error);

    }   // if $row
    else   {
       // if no match, display result
      echo "No results!";
    }
  }   // isse $id
  else  {
     // if the 'id' in the URL isn't valid, or if there is no 'id' value, display an error
    echo 'Error!';
   }
 }

?>
