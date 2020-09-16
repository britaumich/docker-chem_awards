<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitionas//EN">
<html>
<head>
<title>Chemistry Awards System - University of Michigan</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META content="" name=KEYWORDS>
<META content="" name=description>

<link rel="stylesheet" href="../eebstyle.css">

</head>
<body>
<?php 
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
require_once('nav.php');
		
# EDIT Faculty Applicant record
if ($_REQUEST['remove_record'] == "Remove") {
  $faid = $purifier->purify($_REQUEST[faid_update]);
  $uniqname = $purifier->purify($_REQUEST[uniqname]);
  $award_id = $purifier->purify($_REQUEST[award_id]);
  $sql = "DELETE FROM award_progress WHERE faid= $faid";
  mysqli_query($conn, $sql) or die("<hr>There was an error removing a record from award_progress:".mysqli_error($conn)."<hr>\n");
  $sql = "DELETE FROM faculty_awards WHERE uniqname = '$uniqname' AND award_id = $award_id AND status = 10";
  mysqli_query($conn, $sql) or die("<hr>There was an error removing a record from faculty_awards:".mysqli_error($conn)."<hr>\n");
}
else {
if ($_REQUEST['edit_record'] == "Update") {

  $in_process = ((isset($_REQUEST[in_process])) ? "yes" : "no");
  $ask_recommenders = ((isset($_REQUEST[ask_recommenders])) ? "yes" : "no");
  $ask_letters = ((isset($_REQUEST[ask_letters])) ? "yes" : "no");
  $got_letters = ((isset($_REQUEST[got_letters])) ? "yes" : "no");
  $submitted = ((isset($_REQUEST[submitted])) ? "yes" : "no");
  $comments = $purifier->purify($_REQUEST[comments]);
  $faid = $purifier->purify($_REQUEST[faid_update]);
  $uniqname = $purifier->purify($_REQUEST[uniqname]);
  $award_id = $purifier->purify($_REQUEST[award_id]);
  $year = $purifier->purify($_REQUEST[year]);
  $sql = "UPDATE award_progress SET 
  in_process='$in_process',
  ask_recommenders='$ask_recommenders',
  ask_letters='$ask_letters',
  got_letters='$got_letters', 
  submitted='$submitted',
  comments='$comments'
  WHERE faid= $faid";
//echo $sql;
//exit;
  
mysqli_query($conn, $sql) or die("<hr>There was an error:".mysqli_error($conn)."<hr>\n");
//mysqli_query($conn, $sql) or header('Location: ERROR.php?error="Unable to update applicant\'s information."');

if ($submitted == "yes") {
     $sql = "UPDATE faculty_awards SET status = '4' WHERE uniqname = '$uniqname' AND award_id = $award_id AND year = '$year' AND status = 10";
//echo $sql;

     mysqli_query($conn, $sql) or die("<hr>There was an error:".mysqli_error($conn)."<hr>\n");

}
// print("<Br></Br><strong>Your changes have been made.</strong> <Br></Br>");
 }//if
}
$sqlu = "SELECT DISTINCT award_progress.uniqname, Name  FROM award_progress, faculty where award_progress.uniqname = faculty.uniqname";
//$sqlu = "SELECT DISTINCT uniqname FROM faculty_awards WHERE status = 1";
$resultu = mysqli_query($conn, $sqlu) or die("There was an error: ".mysqli_error($conn));
while ( $udata = mysqli_fetch_array($resultu, MYSQLI_BOTH) ) {
  $uniqname =  $udata[uniqname];
  $name =  $udata[Name];
	$sql = "SELECT `faid`, `uniqname`, `award_id`, award_progress.year, Award_Name, Link_to_Website, `in_process`, `ask_recommenders`, `ask_letters`, `got_letters`, `submitted`, `got_result`, award_progress.comments FROM `award_progress`, awards_descr  WHERE uniqname = '$uniqname' AND award_id = awards_descr.id AND submitted <> 'yes' ";
//echo $sql;
	//$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
	$result=mysqli_query($conn, $sql) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');

$total=mysqli_num_rows($result);
if ($total > 0) {
echo "<h2>";
echo "<a href='facultyone.php?uniqname=$uniqname' target='_blank'>$name</a>";
echo "; ";;
echo $uniqname;
echo " Total: ".$total;
echo "</h2>";
echo ("
<table>
<tr>
       <th>Award Name</th>
       <th>Academic Year</th>
       <th>Status</th>
       <th>comments</th>
       <th>update</th>

</tr>
");
while ( $fadata = mysqli_fetch_array($result, MYSQLI_BOTH) )
{
  echo "<form method=\"post\" action=\"award_progress.php\">";
    echo ("<tr>");
        $award_id = $fadata[award_id];
        $year = $fadata[year];
        echo "<td><a href='award-one.php?award_id=$award_id' target='_blank'>$fadata[Award_Name]</b></a>";
        echo"<td>$year</td>";
	?>
   <td>
      <input type="checkbox" name="in_process" value="yes" <?php if ( $fadata['in_process'] == "yes" ) { echo " checked";  } ?>> in process<br>
      <input type="checkbox" name="ask_recommenders" value="" <?php if ( $fadata['ask_recommenders'] == "yes" ) { echo " checked";  } ?>> recommenders? <br>
      <input type="checkbox" name="ask_letters" value="" <?php if ( $fadata['ask_letters'] == "yes" ) { echo " checked";  } ?>> letters? <br>
      <input type="checkbox" name="got_letters" value="" <?php if ( $fadata['got_letters'] == "yes" ) { echo " checked";  } ?>> letters! <br>
      <input type="checkbox" name="submitted" value="" <?php if ( $fadata['submitted'] == "yes" ) { echo " checked";  } ?>> submitted<br>
        <td><textarea name="comments" cols="50" rows="7" ><?php echo $fadata[comments]; ?></textarea></td>
         <INPUT type ='hidden' name='faid_update' value='<?php echo $fadata[faid]; ?>'>
         <INPUT type ='hidden' name='uniqname' value='<?php echo $uniqname; ?>'>
         <INPUT type ='hidden' name='award_id' value='<?php echo $award_id; ?>'>
         <INPUT type ='hidden' name='year' value='<?php echo $year; ?>'>
        <td><INPUT type="submit" name="edit_record" value="Update"><br><br>
        <INPUT type="submit" name="remove_record" value="Remove"></td>

</tr>
</FORM>
<?php 
} //while
echo "</table>";
?>
 <div align="center"><img src="../images/linecalendarpopup500.jpg"></div>
<?php
echo "<table>";

$sql1 = "SELECT * FROM faculty_letters WHERE uniqname = '$uniqname' ORDER BY type";
//echo $sql1;
$result1 = mysqli_query($conn, $sql1) or die ("Query failed : " . mysqli_error($conn));
WHILE ($recUpload = mysqli_fetch_array($result1, MYSQLI_BOTH))
        { $link = $uploaddir . $recUpload[link]; 
 ?>
              <tr><td> <? print("$recUpload[type]") ?> :<td>
                 <? print("<a href=" . $link . " target=\"_blank\"> $recUpload[link]</a>") ?><br>
                <?php
        }//while
echo "</table>";
?>
 <div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>
<?php
}
}
?>


<br><br>


</div>   
</body>
</html>
