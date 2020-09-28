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
?>
<div align="center"><h2>Eligibility List<br></h3>

</div>

<br> 
</div>
<div class="center"></div>
 <div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>
<?php

if ($_REQUEST['addelig'] == "Add") {
      $new = $purifier->purify($_REQUEST['newelig']);
      $sql = "INSERT INTO eligibility_list(name) VALUES ('$new')";
      mysqli_query($conn, $sql) or die("There was an error: ".mysqli_connect_error());
}
if ($_REQUEST['edit_record'] == "Save changes")
{
$sqlrank = "SELECT id FROM rank ORDER BY id";
$resultrank = mysqli_query($conn, $sqlrank) or die("Query failed :".mysqli_connect_error());
$allranks = array();
while ($rdata = mysqli_fetch_array($resultrank, MYSQLI_BOTH))  {
    $allranks[] = $rdata['id'];
}
//echo '<pre>allranks'; var_export($allranks); echo '</pre>';
$eliglist = array();
$eliglist = purica_array($conn, $_REQUEST['eliglist']);
$elig_check = purica_array($conn, $_REQUEST['elig_check']);
//echo '<pre>eliglist'; var_export($eliglist); echo '</pre>';
  foreach($elig_check as $rank_id=>$val){ 
//echo '<pre>'; var_export($val); echo '</pre>';
          $sql = "INSERT INTO eligibility (rank_id, eligibility_id) VALUES";   
      foreach($val as $k=>$elig_id){ 
          if (is_numeric($elig_id)) {
            $sql .= " (" . $rank_id . ", " . $elig_id . "),"; 
          }
      }
       $sql = substr($sql, 0, -1);
       $sql .= " ON DUPLICATE KEY UPDATE rank_id = " . $rank_id;
       mysqli_query($conn, $sql) or die("There was an error updating eligibility: ".mysqli_connect_error());
// to delete unchecked
   if (count($val) !== (count($eliglist))) {
      $sqldel = "DELETE FROM eligibility WHERE";  
      foreach ($eliglist as $elig1) {
         if (!in_array($elig1, $val)) {
            $sqldel .= "(rank_id = " . $rank_id . " and eligibility_id = " . $elig1 . ") or ";
         }
      }      
      $sqldel = substr($sqldel, 0, -4);
      mysqli_query($conn, $sqldel) or die("There was an error deleting one elig: ".mysqli_connect_error());
    }
      // remove rank from $allranks array
      if(($key = array_search($rank_id, $allranks)) !== false) {
          unset($allranks[$key]);
      }
  } 
//echo '<pre>new allranks '; var_export($allranks); echo '</pre>';
foreach ($allranks as $rank1) {
      $sqldel = "DELETE FROM eligibility WHERE rank_id = " . $rank1;
      mysqli_query($conn, $sqldel) or die("There was an error deleting all elig: ".mysqli_connect_error());
}

/*
  if (mysqli_query($conn, $sql)) { 
    echo "The record has been updated";
  }
  else {
    die("There was an error updating a record: ".mysqli_connect_error());
  }
*/
}
//add/delete/edit a eligibility
$sqle = "SELECT * FROM eligibility_list";
$resulte=mysqli_query($conn, $sqle) or die("There was an error: ".mysqli_connect_error());
echo "<table>";
echo "<tr> <th>ID</th><th>Name</th>  </th> <th></th> <th></th></tr>";
echo "<tr>";
        echo '<td>&nbsp; </td>';
echo "<form name='form' action='edit_eligibility.php' method='post'>";
echo ('<td><input type="text" size="35" name="newelig" value=""></td>');
echo ('<td> <input type="submit" name="addelig" value="Add"></td>');
while ( $edata = mysqli_fetch_array($resulte, MYSQLI_BOTH) )
{
    $id = $edata['id'];
    echo "<tr>";
    echo '<td>' . $id . '</td>';
    echo '<td>' . $edata['name'] . '</td>';
    echo '<td><a href="edit_elig.php?id=' . $id . '">Edit</a></td>';
    echo '<td><a href="delete_elig.php?id=' . $id . '">Delete</a></td>';
    echo "</tr>";

} //while
echo "</table>";




	//Everything is peachy, pull record.


	$sqlr = "SELECT id, rank FROM rank";
	$resultr=mysqli_query($conn, $sqlr) or die("There was an error: ".mysqli_connect_error());
?>
<table>
<tr><th>Rank<th>Eligibility</tr>
<?php
while ($ranks = mysqli_fetch_array($resultr, MYSQLI_BOTH)) {
     $id = $ranks['id'];
	$sql = "SELECT rank_id, eligibility_id FROM eligibility WHERE rank_id = $id";
	$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_connect_error());
	$sqle = "SELECT id, name FROM eligibility_list";
	$resulte=mysqli_query($conn, $sqle) or die("There was an error: ".mysqli_connect_error());
?>	
  <tr><td>
<?php
echo $ranks['rank'];
echo "<td>";
$eligids = array();
while ($elig1 = mysqli_fetch_array ($result, MYSQLI_BOTH)) {
   $eligids[] = $elig1['eligibility_id'];
}
//echo '<pre>'; var_export($eligids); echo '</pre>';

if (mysqli_num_rows($resulte) != 0) {
     while ( $eligs = mysqli_fetch_array($resulte, MYSQLI_BOTH) ) {
           echo "<input type='checkbox' name='elig_check[";
           echo $id;
           echo "][";
           echo $eligs['id'];
           echo "]' ";
           echo "value='$eligs[id]'";
           if (in_array($eligs['id'], $eligids)) {echo " checked"; }
           echo ">$eligs[name]";  
           echo "<br>";
     }
}
?> 
</td></tr>
<?php
}
$resulte=mysqli_query($conn, $sqle) or die("There was an error: ".mysqli_connect_error());
while ( $eligs = mysqli_fetch_array($resulte, MYSQLI_BOTH) ) {
         echo "<input type='hidden' name='eliglist[]' value='" . $eligs['id'] . "'>";
     
}
?>
</table>
			
        <br><div align="center"><INPUT type="submit" name="edit_record" value="Save changes"></div>

 </form></td>


	<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br><br>
</div> <div class="clear"></div>



<br><br>


</div>   
</div>   <br>
</body>
</html>

