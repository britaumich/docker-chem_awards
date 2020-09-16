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
$error = $_REQUEST[error];
if($error != ''){
      echo "<table><TR><TD align=center><span style=color:red><b>ERRORS!</b></span><TR><TD><span style=color:red>$error</span></table>";
  }


$sql = "SELECT `faid`, award_progress.`uniqname`, award_progress.`award_id`, awards_descr.Award_Name,  award_progress.year, Name, faculty.id as faculty_id FROM `award_progress`, faculty, awards_descr where award_progress.uniqname = faculty.uniqname AND award_progress.submitted = 'yes' AND got_result = 'no' AND award_progress.award_id = awards_descr.id";
//echo $sql;
$result = mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
$total=mysqli_num_rows($result);
echo (" <table><tr><th>Faculty</th><th>Award Id</th><th>Award Name</th><th>Academic year</th><th>Choose a year </th>  <th>Received</th><th>Declined</th> </tr> ");
while ( $data = mysqli_fetch_array($result, MYSQLI_BOTH) ) {
  
      echo "<tr>";
      $name = $data[Name];
      $uniqname = $data[uniqname];
      $award_id = $data[award_id];
      $award_name = $data[Award_Name];
      $year = $data[year];
      $faculty_id = $data[faculty_id];
      echo "<td>";
      echo $name;
      echo "<td>";
      echo "<a href='award-one.php?award_id=$award_id' target='_blank'>$award_id</a>";
      echo "<td>";
      echo $award_name;
      echo "<td>";
      echo $year;
      echo "<td>";
      echo "<form name='form' action='result.php' method='post'>";
      echo "<select name='resultyear'>";
$current_year = date("Y");
$current_year0 = $current_year - 1;;
$current_year1 = $current_year + 1;;
      echo "<option select value=$current_year0>$current_year0</option>";
      echo "<option select  selected value=$current_year>$current_year</option>";
      echo "<option select value=$current_year1>$current_year1</option>";

      echo "</select>";
      echo "<td>";
      
           echo '<input type="hidden" name="uniqname" value="' . $uniqname . '">';
           echo '<input type="hidden" name="faculty_id" value="' . $faculty_id . '">';
           echo '<input type="hidden" name="award_id" value="' . $award_id . '">';
           echo '<input type="hidden" name="year" value="' . $year . '">';
           echo "<input type='submit' name='submit' value='Received' />";
      echo "<td>";
           echo "<input type='submit' name='submit' value='Declined' />";
           echo('</form>');
    echo ("</tr>");


} //while
echo "</table>";
?> 


<br><br>


</div>   
</body>
</html>
