<?php
function renderForm($conn, $dataid, $fac_id, $name, $year, $statusid, $comment, $error)
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
 <input type="hidden" name="dataid" value="<?php echo $dataid; ?>"/>
 <input type="hidden" name="fac_id" value="<?php echo $fac_id; ?>"/>
<tr><th> ID: 
<td><?php echo $dataid; ?></tr>
<tr><th>Name: 
<td> <?php echo $name; ?></tr>
<tr><th>Year: 
<td> <input type="text" name="year" value="<?php echo $year; ?>"/></tr>
<tr><th>Status:<td> 
<?php
$sqls = "SELECT `id`, `status` FROM `award_status`";
$ress = mysqli_query($conn, $sqls) or die("Query failed :".mysqli_error($conn));
print("<select name='status'>");
        print("<option select value='error'>-choose status-</option>");

        WHILE ($sdata = mysqli_fetch_array($ress, MYSQLI_BOTH))
        {
               echo "<option";
               if ($sdata['id'] == $statusid) { echo " selected"; }
               echo " value='$sdata[id]'>$sdata[status]</option>";
        }
echo "</select>";

?>
</tr>
<tr><th>Comments: 
<td> <input type="text" name="comment" value="<?php echo $comment; ?>"/></tr>
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
$keyword_search = $purifier->purify($_REQUEST['keyword_search']);

$award_id = $purifier->purify($_REQUEST['award_id']);
if (isset($_REQUEST['submit'])) {
if (isset($_REQUEST['dataid']) && is_numeric($_REQUEST['dataid'])) {
   $dataid = $purifier->purify($_REQUEST['dataid']);
   $fac_id = $purifier->purify($_REQUEST['fac_id']);
   $status = $purifier->purify($_REQUEST['status']);
   $year = $purifier->purify($_REQUEST['year']);
   $comment = $purifier->purify($_REQUEST['comment']);

   if ($status == 'error') {
       // generate error message
       $error = 'ERROR: status is empty!';
    // if either field is blank, display the form again
      renderForm($conn, $dataid, $fac_id, $name, $year, $status, $comment, $error);
   }  // empty
   else
   {
       // save the data to the database
      mysqli_query($conn, "UPDATE faculty_awards SET status='$status', year='$year', comment='$comment'  WHERE id='$dataid'") or die(mysqli_error($conn));
             // once saved, redirect back to the view page
//     header("Location: faculty_awards_status.php?award_id=$award_id");
     header("Location: add_nominations.php?award_id=$award_id&keyword_search=$keyword_search");


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
    if (isset($_REQUEST['dataid']) && is_numeric($_REQUEST['dataid']) && $_REQUEST['dataid'] > 0) {
      // query db
   $dataid = $purifier->purify($_REQUEST['dataid']);
   $result = mysqli_query($conn, "SELECT faculty_id, award_id, faculty_awards.status AS statusid, year, comment, Name  FROM faculty_awards JOIN faculty ON faculty_id = faculty.id WHERE faculty_awards.id = $dataid") or die(mysqli_error($conn));
   $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    if($row) {
         // get data from db
          $fac_id = $row['faculty_id'];
          $name = $row['Name'];
          $year = $row['year'];
          $statusid = $row['statusid'];
          $comment = $row['comment'];
       // show form
      renderForm($conn, $dataid, $fac_id, $name, $year, $statusid, $comment, $error);

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
