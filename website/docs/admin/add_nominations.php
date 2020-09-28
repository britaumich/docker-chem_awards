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
require_once('../awards-config.php');
//require_once('../noinject.inc');
require_once('nav.php');

?>

<div align="center">
<?php
$award_id = $purifier->purify($_REQUEST['award_id']);

$keyword_search = $purifier->purify($_REQUEST['keyword_search']);
//echo "<br>award_id: ";
//echo $award_id;

$error = $purifier->purify($_REQUEST['error']);
if($error != ''){
      echo "<table><TR><TD align=center><span style=color:red><b>ERRORS!</b></span><TR><TD><span style=color:red>$error</span></table>";
  }


echo "<form name='form1' method='post' action='add_nominations.php'>";

echo "<br>";
echo "Search by Keywords (in Award Name and Awarded By) ";
$keyword_search = $purifier->purify($_REQUEST['keyword_search']);
echo '<input type="text" name="keyword_search" size = "50" placeholder="-- keywords, separated by commas --" value="' . $keyword_search . '" >';

echo "  <input type='submit' name='choose' value='Search'>";
echo "</form>";
echo "<br><div align='center'><img src='../images/linecalendarpopup500.jpg'></div><Br>";


echo "<br>";

if (isset($_REQUEST['choose']) || $keyword_search != '') {
  if ($award_id == "") {
//   $awids = array();

   $keyword_search = $purifier->purify($_REQUEST['keyword_search']); 
   $where = " WHERE 1 ";
        if ($keyword_search !== "") {
           $where .= " AND (";
         foreach (explode(",", $keyword_search) as $key) {
           $key = trim($key);
           $where .= " (Award_Name LIKE '%" . $key . "%') OR (Awarded_By LIKE '%" . $key . "%') OR";
         }
         $where = substr($where, 0, -2);
         $where .= ")";

     }
} 
else {
// award_id != ''
  $where = ' WHERE id = ' . $award_id;
}
   $sqlsearch = "SELECT id, Award_Name, Awarded_By FROM awards_descr " . $where;
      $stmtsearch = prepare($sqlsearch);
   $ressearch = $stmtsearch->execute($conn) or die($stmtsearch->error);
//echo "<br>";
//echo $sqlsearch;
//echo "<br>";
   echo "<table>";
   echo "<tr>";
      echo "<th>Award Name</a></th>";
     echo "<th>Awarded By</a></th>";
if ($award_id == '') {
      echo "<th>Considered?</th>";
}
      echo "</tr>";

   while ($adata = mysqli_fetch_array($ressearch, MYSQLI_BOTH) ) {
echo "<tr>";
           $id = $adata['id'];
                echo "<td><a href='$adata[Link_to_Website]' target='_blank'>$adata[Award_Name]</td>";
                echo "<td>$adata[Awarded_By]</td>";

if ($award_id == '') {
echo "<td>";
echo "<form name='form' method='post' action='add_nominations.php'>";
echo "<input type='hidden' name='award_id' value='" . $id . "'>";
echo '<input type="hidden" name="keyword_search" value="' . $keyword_search . '">';

echo "<input type='submit' name='award' value='Choose Award'>";
echo "</form>";
echo "</td>";
}
        echo "</tr>";
   }
   echo "</table>";
   


echo "<br><br><div align='center'><img src='../images/linecalendarpopup500.jpg'></div><Br>";

if ($award_id != '') {



//echo "Who got it<br><br>";
echo "<table>";
echo "<th>Name<th>year<th>status<th>Comments<th>&nbsp;</th><th>&nbsp;</th></tr>";

echo "<form name='form2' action='add_faculty_to_award1.php' method='post'>";
echo ('<td>');
$sqla = "SELECT id, uniqname, Name FROM faculty ORDER BY name ASC";
$resa = mysqli_query($conn, $sqla) or die("Query failed :".mysqli_error($conn));
print("<select name='faculty_id'>");
        print("<option select value='error'> - choose name -</option>");

        WHILE ($applicant_name = mysqli_fetch_array($resa, MYSQLI_BOTH))
        {
               echo "<option";
               echo " value='$applicant_name[id]'>$applicant_name[Name]</option>";
        }
echo "</select>";

echo ('<br><br><input type="text" size="25" maxsize="100" name="nonchemfaculty" value="" placeholder="-or enter a name-"></td>');

echo ('<td><input type="text" size="9" name="year" value=""></td>');
echo ('<td>');
$sqls = "SELECT `id`, `status` FROM `award_status`";
$ress = mysqli_query($conn, $sqls) or die("Query failed :".mysqli_error($conn));
print("<select name='status'>");
        print("<option select value='error'>-choose status-</option>");

        WHILE ($sdata = mysqli_fetch_array($ress, MYSQLI_BOTH))
        {
               echo "<option";
               echo " value='$sdata[id]'>$sdata[status]</option>";
        }
echo "</select>";
echo ('<td><input type="text" size="10" maxsize="200" name="comment" value=""></td>');
echo '<td><input type="hidden" name="award_id" value="' . $award_id . '">';
echo '<input type="hidden" name="keyword_search" value="' . $keyword_search . '"></td>';
echo ('<td> <input type="submit" name="submit" value="Add"></td>');
 echo('</form></td>');


$sqllist = "SELECT faculty_awards.id as dataid, faculty_awards.`uniqname` as uniqname, faculty_awards.faculty_id AS faculty_id, faculty.Name, award_status.`status`, `year`, `comment` FROM `faculty_awards`JOIN faculty ON faculty_awards.faculty_id = faculty.id, award_status WHERE faculty_awards.status = award_status.id AND award_id = $award_id ORDER BY year, award_status.status";
//echo $sqllist;

      $stmtlist = prepare($sqllist);
   $reslist = $stmtlist->execute($conn) or die($stmtlist->error);

$total=mysqli_num_rows($reslist);

while ($faward = mysqli_fetch_array($reslist, MYSQLI_BOTH) )
{
    $dataid = $faward['dataid'];
    $status = $faward['status'];
    $uniqname = $faward['uniqname'];
    $faculty_id = $faward['faculty_id'];
    $year = $faward['year'];
           echo "<tr><td>" . $faward['Name']. "</td>";
           echo "<td>" . $year. "</td>";
           echo "<td>" . $status . "</td>";
           echo "<td>" . $faward['comment']. "</td>";


    $table = "faculty_awards";
    echo '<td><a href="edit_faculty_award1.php?dataid=' . $dataid . '&award_id=' . $award_id . '&keyword_search=' . $keyword_search . '">Edit</a></td>';
    echo '<td><a href="delete_faculty_award1.php?table=' . $table . '&dataid=' . $dataid . '&award_id=' . $award_id . '&keyword_search=' . $keyword_search . '">Delete</a></td>';
}


// non chemistry
$sqlnc = "SELECT faculty_awards_notchem.`id` AS id, `name`, `award_id`, `year`, `comment`, award_status.`status` AS status, comment FROM `faculty_awards_notchem`, award_status WHERE faculty_awards_notchem.status = award_status.id AND award_id = $award_id ORDER BY status, year";
$resultnc = mysqli_query($conn, $sqlnc) or die("Query failed :".mysqli_error($conn));

$total=mysqli_num_rows($resultnc);

if ($total !== 0 )  {
echo "<tr><th>Non Chemistry Awards</th>";
    while ( $awardnc = mysqli_fetch_array($resultnc, MYSQLI_BOTH) ) {
       $dataid = $awardnc['id'];
       $status = $awardnc['status'];
       $name = $awardnc['name'];
       $year = $awardnc['year'];
       echo "<tr><td>" . $name . "</td>";
           echo "<td>" . $year. "</td>";
           echo "<td>" . $status . "</td>";
           echo "<td>" . $awardnc['comment']. "</td>";


    $table = "faculty_awards_notchem";

    echo '<td><a href="edit_faculty_award1_nonchem.php?dataid=' . $dataid . '&award_id=' . $award_id . '&keyword_search=' . $keyword_search . '">Edit</a></td>';
    echo '<td><a href="delete_faculty_award1.php?table=' . $table . '&dataid=' . $dataid . '&award_id=' . $award_id . '&keyword_search=' . $keyword_search . '">Delete</a></td>';


   }
}
echo "</table><br>";
}
}
?>

</body>
</html>
