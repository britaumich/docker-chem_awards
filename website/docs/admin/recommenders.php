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
require_once('nav.php');

?>

<div align="center">
<?php
if (isset($_REQUEST['error'])) {
    echo ('<span style=color:red><b>');
    echo $purifier->purify($_REQUEST['error']);
    echo ('</span></b>');
}

//$uniqname1 = $_SERVER['REMOTE_USER'];
$uniqname1 = $_SERVER['REDIRECT_REMOTE_USER'];
$uniqname = $purifier->purify($_REQUEST['uniqname']);
if ($uniqname == "") {
  $uniqname = $uniqname1;
}
//echo $uniqname;
if (is_admin($uniqname1)) {
echo "<form name='form' method='post' action='recommenders.php'>";

//   echo "admin";
$sqlf = "SELECT uniqname, Name FROM faculty ORDER BY name ASC";
$resf = mysqli_query($conn, $sqlf) or die("Query failed :".mysqli_error($conn));
echo "Select a faculty: ";
print("<select name='uniqname'>");
        print("<option select value='error'> - choose name -</option>");

        WHILE ($applicant_name = mysqli_fetch_array($resf, MYSQLI_BOTH))
        {
               echo "<option";
               if ($applicant_name[uniqname] == $uniqname) { echo " selected"; }
               echo " value='$applicant_name[uniqname]'>$applicant_name[Name]</option>";
        }
echo "</select><br><br>";
echo "<input type='submit' name='choose' value='Choose'>";

echo "</form>";
}


     $id = $purifier->purify($_REQUEST['id']);
     $awardid = array();
     $awardid = $purifier->purify($_REQUEST[awardid]);
//echo '<pre>'; var_export($awardid); echo '</pre>';
if (isset($_REQUEST[choose]) OR ($uniqname !== "")) {
if ($uniqname == "") {
$uniqname = $purifier->purify($_REQUEST['uniqname']);
    if ($uniqname == "") {
//         $uniqname = $_SERVER['REMOTE_USER'];
         $uniqname = $_SERVER['REDIRECT_REMOTE_USER'];
    } 
}
else {
//echo $uniqname;

$sqlsearch = "SELECT id, rec_name, rec_email FROM recommenders WHERE uniqname = '$uniqname'"; 
 
//echo $sqlsearch;

 $result = mysqli_query($conn, $sqlsearch) or die("There was an error: ".mysqli_error($conn)); 
$total=mysqli_num_rows($result);
 
 if( $total == 0 )
 {
    echo "<br><br>There were no results found.</Br>";
 }//if

echo "<table>";
echo "<tr> <th>ID</th><th>Name</th> <th>Email Address</th> </th> <th></th> <th></th></tr>";
echo "<tr>";
        echo '<td>&nbsp; </td>';
echo "<form name='form1' action='add_recom.php' method='post'>";
echo ('<td><input type="text" size="35" name="rec_name" value="" placeholder="-- enter full name --"></td>');
echo ('<td><input type="text" size="35" name="rec_email" value="" placeholder="-- enter email --"></td>');
echo '<td><input type="hidden" name="uniqname" value="' . $uniqname . '"></td>';
echo ('<td> <input type="submit" name="submit" value="Add"></td>');
 echo('</form></td>'); 
while ( $rdata = mysqli_fetch_array($result, MYSQLI_BOTH) ) 
{
    $id = $rdata[id];
    echo "<tr>";
    echo '<td>' . $id . '</td>';
    echo '<td>' . $rdata['rec_name'] . '</td>';
    echo '<td>' . $rdata['rec_email'] . '</td>';
    echo '<td><a href="edit_recom.php?id=' . $id . '&uniqname=' . $uniqname . '">Edit</a></td>';
    echo '<td><a href="delete_recom.php?id=' . $id . '&uniqname=' . $uniqname . '">Delete</a></td>';
    echo "</tr>";

} //while
echo "</table>";

}
}
?>
<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><Br>
</body>
</html>
