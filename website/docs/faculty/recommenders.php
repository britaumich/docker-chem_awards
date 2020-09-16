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
require_once('../dbConnect.inc');
require_once('nav.php');

?>

<div align="center">
<?php
if (isset($_REQUEST['error'])) {
    echo ('<span style=color:red><b>');
    echo $_REQUEST['error'];
    echo ('</span></b><br>');
}

//$uniqname1 = $_SERVER['REMOTE_USER'];
$uniqname1 = $_SERVER['REDIRECT_REMOTE_USER'];
$uniqname = check_input($conn, $_REQUEST['uniqname']);
if ($uniqname == "") {
  $uniqname = $uniqname1;
}
//echo $uniqname;

$sqlfac = "SELECT faculty.id AS fac_id, Name FROM faculty WHERE uniqname = '$uniqname'";
$resfac = mysqli_query($conn, $sqlfac) or die("There was an error updating faculty_letters: ".mysqli_connect_error());
$fdata = mysqli_fetch_array($resfac, MYSQLI_BOTH);
$name = $fdata['Name'];
echo $name;
echo "<br>";
echo "<br><div align='center'><img src='../images/linecalendarpopup500.jpg'></div><Br>";
//$uniqname = check_input($conn, $_REQUEST['uniqname']);
//echo "List of Recommenders<br>";

     $id = check_input($conn, $_REQUEST['id']);
     $awardid = array();
     $awardid = $_REQUEST[awardid];
//echo '<pre>'; var_export($awardid); echo '</pre>';
if (isset($_REQUEST[choose]) OR ($uniqname !== "")) {
// echo "five";
//echo $uniqname;
if ($uniqname == "") {
$uniqname = check_input($conn, $_REQUEST['uniqname']);
    if ($uniqname == "") {
//         $uniqname = $_SERVER['REMOTE_USER'];
         $uniqname = $_SERVER['REDIRECT_REMOTE_USER'];
    } 
}
else {
//echo $uniqname;

$sqlsearch = "SELECT id, rec_name, rec_email FROM recommenders WHERE uniqname = '$uniqname'"; 
 
//echo $sqlsearch;

 $result = mysqli_query($conn, $sqlsearch) or die("There was an error: ".mysqli_connect_error()); 
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
echo ('<td><input type="text" size="35" name="rec_name" placeholder="-- enter full name --"value=""></td>');
echo ('<td><input type="text" size="35" name="rec_email" placeholder="-- enter email --"value=""></td>');
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
echo "<br><div align='center'><img src='../images/linecalendarpopup500.jpg'></div><Br>";
}
}
?>
</body>
</html>
