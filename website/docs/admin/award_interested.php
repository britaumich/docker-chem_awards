<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
$award_id = $purifier->purify($_REQUEST['award_id']);
$error = $purifier->purify($_REQUEST['error']);
if($error != ''){
      echo "<table><TR><TD align=center><span style=color:red><b>ERRORS!</b></span><TR><TD><span style=color:red>$error</span></table>";
  }
		


$sql = "SELECT DISTINCT(award_id), Award_Name, Link_to_Website FROM faculty_awards JOIN awards_descr ON faculty_awards.award_id = awards_descr.id WHERE status = '1'";
$result = mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
while ( $data = mysqli_fetch_array($result, MYSQLI_BOTH) ) {
  
  $award_id =  $data['award_id'];
	$sqlu = "SELECT faculty_awards.id as dataid, faculty_awards.faculty_id, faculty_awards.year, faculty_awards.uniqname, Name FROM faculty_awards JOIN faculty ON faculty_awards.uniqname = faculty.uniqname WHERE status = '1' AND award_id = '$award_id'";
	$resultu = mysqli_query($conn, $sqlu) or die("There was an error: ".mysqli_error($conn));

$total=mysqli_num_rows($resultu);

    echo '<br><img src="../images/linecalendarpopup500.jpg"><br><br>';
        echo"<b><a href='award-one.php?award_id=$award_id' target='_blank'>$data[Award_Name]</b></a> (open in a new tab)<br><br>";
echo (" <table><th>Faculty</th><th>Academic Year</th> <th>Nominate</th><th>Not nominate</th> </tr> ");
while ( $fadata = mysqli_fetch_array($resultu, MYSQLI_BOTH) ) {
      echo "<td>";
      $uniqname = $fadata['uniqname'];
      echo $uniqname;
      echo "<td>";
      $year = $fadata['year'];
      echo $year;
      echo "<td>";
      echo "<form name='form' action='to_in_process.php' method='post'>";
           echo '<input type="hidden" name="uniqname" value="' . $uniqname . '">';
           echo '<input type="hidden" name="faculty_id" value="' . $fadata['faculty_id'] . '">';
           echo '<input type="hidden" name="award_id" value="' . $award_id . '">';
           echo '<input type="hidden" name="dataid" value="' . $fadata['dataid'] . '">';
           echo '<input type="hidden" name="year" value="' . $fadata['year'] . '">';
           echo '<input type="hidden" name="prog_name" value="award_interested.php">';
           echo "<input type='submit' name='submit' value='in process' />";
           echo('</form>');
      echo "<td>";
      echo "<form name='form1' action='clear_name.php' method='post'>";
           echo '<input type="hidden" name="uniqname" value="' . $uniqname . '">';
           echo '<input type="hidden" name="faculty_id" value="' . $fadata['faculty_id'] . '">';
           echo '<input type="hidden" name="award_id" value="' . $award_id . '">';
           echo '<input type="hidden" name="dataid" value="' . $fadata['dataid'] . '">';
           echo '<input type="hidden" name="prog_name" value="award_interested.php">';
           echo "<input type='submit' name='submit' value='clear' onclick=\"return confirm('Are you sure to remove this award?')\"/>";
           echo('</form>');
    echo ("</tr>");


} //while
echo "</table>";
}

?>


<br><br>


</div>   
</body>
</html>
