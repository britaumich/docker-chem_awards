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
if (isset($_REQUEST['id']) AND (is_numeric($_REQUEST['id']))) {
       $id = $purifier->purify($_REQUEST['id']);
}
else {
$id = '';
}
if ($_REQUEST['edit_record'] == "Save changes") {

  $id = $purifier->purify($_REQUEST['id']);
  $uniqname = $purifier->purify($_REQUEST['uniqname']);
  $Name = $purifier->purify($_REQUEST['Name']);
  $Rank = $purifier->purify($_REQUEST['Rank']);
  $rank1 = $purifier->purify($_REQUEST['rank1']);
  if ($rank1 !== "") { 
     $sqlr = "INSERT INTO `rank`(rank) VALUES ('$rank1')"; 
     $resr = mysqli_query($conn, $sqlr) or die("There was an error updating rank: ".mysqli_error($conn));
     $Rank = mysqli_insert_id($conn); 
  }

  $Year_PhD = $purifier->purify($_REQUEST['Year_PhD']);
  $birth_year = $purifier->purify($_REQUEST['birth_year']);
  $Appt_Start = $purifier->purify($_REQUEST['Appt_Start']);

if ($id !== "") {
  $sql = "UPDATE faculty SET
      uniqname = '$uniqname',
      Name = '$Name',
      Rank = '$Rank',
      Year_PhD = '$Year_PhD',
      birth_year = '$birth_year',
      Appt_Start = '$Appt_Start'
      WHERE id ='$id'";
}
else {
  // add a new record
 $sql = "INSERT INTO `faculty`(`uniqname`, `Name`, `Rank`, `Year_PhD`, `birth_year`, `Appt_Start`) VALUES ('$uniqname', '$Name', '$Rank', '$Year_PhD', '$birth_year', '$Appt_Start')";
}
//echo ($sql);
  if (mysqli_query($conn, $sql)) { 
     if ($id == "") {
       // record was added not updated
      $id = mysqli_insert_id($conn);
     }
// add clusters
         $cluster_check = array();
    $cluster_check = purica_array($conn, $_REQUEST['cluster_check']);
// echo '<pre>'; var_export($cluster_check); echo '</pre>';
    $clusterlist = array();
    $clusterlist = purica_array($conn, $_REQUEST['clusterlist']);
// echo '<pre>'; var_export($clusterlist); echo '</pre>';
      if (!empty($cluster_check)) {
       // clusters
       $sqlcluster = "INSERT INTO faculty_cluster (`faculty_id`, `cluster_id`) VALUES";
       foreach ($cluster_check as $cluster_id) {
          $sqlcluster .= " (" . $id . ", " . $cluster_id . "),";
       }
       $sqlcluster = substr($sqlcluster, 0, -1);
       $sqlcluster .= " ON DUPLICATE KEY UPDATE faculty_id = " . $id;
       $res = mysqli_query($conn, $sqlcluster) or die("There was an error 2 updating cluster: ".mysqli_error($conn));
      // to delete unchecked
    if (count($cluster_check) !== (count($clusterlist))) {
      $sqldel = "DELETE FROM faculty_cluster WHERE";
      foreach ($clusterlist as $cluster1) {
         if (!in_array($cluster1, $cluster_check)) {
            $sqldel .= "(faculty_id = " . $id . " and cluster_id = " . $cluster1 . ") or ";
         }
       }
      $sqldel = substr($sqldel, 0, -4);
      $res = mysqli_query($conn, $sqldel) or die("There was an error deleting cluster: ".mysqli_error($conn));
    }
     }
     else {
      $sqldel = "DELETE FROM faculty_cluster WHERE faculty_id = $id";
      $res = mysqli_query($conn, $sqldel) or die("There was an error 1 updating cluster: ".mysqli_error($conn));
     }
// end add clusters
    echo "<div align='center'>";
    echo "The record has been updated";
    echo "</div>";
  }
  else {
    die("There was an error 4 updating a record: ".mysqli_error($conn));
  }
}

if ($id !== '') {

	//Everything is peachy, pull record.
// next and prev
$maxid = mysqli_fetch_array(mysqli_query($conn, "SELECT MAX(id) FROM faculty"), MYSQLI_NUM)[0];
$minid = mysqli_fetch_array(mysqli_query($conn, "SELECT MIN(id) FROM faculty"), MYSQLI_NUM)[0];

$sqlrec = "SELECT id FROM faculty WHERE (id = IFNULL((SELECT MIN(id) FROM faculty WHERE id > $id),0) OR id = IFNULL((SELECT MAX(id) FROM faculty WHERE id < $id),0))";
//echo $sqlrec;
$res=mysqli_query($conn, $sqlrec) or die("There was an error: ".mysqli_error($conn));
        $id1 = mysqli_fetch_array($res, MYSQLI_BOTH)['id'];
        $id2 = mysqli_fetch_array($res, MYSQLI_BOTH)['id'];

if ($id == $minid) {
    $idp = $id;
    $idn = $id1;
}
elseif ($id == $maxid) {
    $idp = $id1;
    $idn = $id;
}
else {
     $idp = $id1;
     $idn = $id2;
}
?>
<div class='floatright'>
        </form>
    <form name="forme" method="post" action="faculty.php?id=<?php echo $id; ?>">
           <input type="hidden" name="id" value="<?php echo $id; ?>">
         <input type='submit' name='Submit' value='List'>
        </form>
<br>&nbsp;&nbsp;
</div>


<div class='floatleft'>

    <form name="formp" method="post" action="edit_faculty.php?id=<?php echo $idp; ?>">
           <input type="hidden" name="idp" value="<?php echo $idp; ?>">
<?php
if ($id == $minid) {
          echo "<input type='submit' name='Submit' value='Prev' disabled>";
}
else {
          echo "<input type='submit' name='Submit' value='Prev'>";
}
?>
</div>
        </form>
    <form name="formn" method="post" action="edit_faculty.php?id=<?php echo $idn; ?>">
           <input type="hidden" name="idn" value="<?php echo $idn; ?>">
<?php
if ($id == $maxid) {
          echo "<input type='submit' name='Submit' value='Next' disabled>";
}
else {
          echo "<input type='submit' name='Submit' value='Next'>";
}
?>
        </form>
<?php
}
$sql = "SELECT faculty.`id`, `uniqname`, `Name`, faculty.`Rank`, rank.rank as rank, `Year_PhD`, `birth_year`, `Appt_Start`, `Num_papers`, `Num_UG_courses_taught`, `Num_of_times`, `Q1_avg`, `Q2_avg`, `teaching_summary` FROM `faculty`, rank  WHERE rank.id = faculty.Rank AND faculty.id = '$id'";
//echo $sql;
	//$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
	$result=mysqli_query($conn, $sql) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
	$adata = mysqli_fetch_array($result, MYSQLI_BOTH);
?>	
    <form name="form" method="post" action="edit_faculty.php">
<table>
<tr>
<th>id: <td> <?php print($adata['id']) ?>
<INPUT type ='hidden' name='id' value='<?php echo $id; ?>'>
<tr><th>Uniqname:<td><input type="text" name="uniqname" value="<?php print($adata['uniqname']) ?>">
<tr><th>Name:<td><input type="text" name="Name" value="<?php print($adata['Name']) ?>">
<tr><th>Rank:<td>choose from the list &nbsp;&nbsp; 
<?php
$rank = $adata['rank'];
$sqlrank = "SELECT id, rank FROM rank";
$resultrank = mysqli_query($conn, $sqlrank) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
echo "<select name='Rank'>";
if (mysqli_num_rows($resultrank) != 0) {
     while ( $ranks = mysqli_fetch_array($resultrank, MYSQLI_BOTH) ) {
           echo "<option";
           if ($ranks['rank'] == $rank) { echo " selected"; }
           echo " value=$ranks[id]>$ranks[rank]</option>";
     }
     echo "</select>";
}
?> 
&nbsp;&nbsp;or&nbsp;&nbsp;<input type="text" name="rank1" placeholder="-- enter new rank --" value="" >

<tr><th>Year_PhD:<td><input type="text" name="Year_PhD" value="<?php print($adata['Year_PhD']) ?>">
<tr><th>Birth Year:<td><input type="text" name="birth_year" value="<?php print($adata['birth_year']) ?>">
<tr><th>Appt_Start:<td><input type="text" name="Appt_Start" value="<?php print($adata['Appt_Start']) ?>">
<tr><th>cluster:<td>
<?php
$sqlclusterids = "SELECT clusters.id FROM clusters INNER JOIN faculty_cluster ON clusters.id = faculty_cluster.cluster_id WHERE faculty_id = '$id'";
$resultcluster_list = mysqli_query($conn, $sqlclusterids) or header('Location: ERROR.php?error="Unable to select clusters."');
$clustersids = array();
while ($cluster1 = mysqli_fetch_array ($resultcluster_list, MYSQLI_BOTH)) {
   $clustersids[] = $cluster1['id'];
}
$sqlcluster = "SELECT id, clusters.name FROM clusters";
$resultcluster = mysqli_query($conn, $sqlcluster) or header('Location: ERROR.php?error="Unable to select clusters."');
if (mysqli_num_rows($resultcluster) != 0) {
     while ( $clusters = mysqli_fetch_array($resultcluster, MYSQLI_BOTH) ) {
           echo "<input type='checkbox' name='cluster_check[";
           echo $clusters['id'];
           echo "]' ";
           echo "value='$clusters[id]'";
           if (in_array($clusters['id'], $clustersids)) {echo " checked"; }
           echo ">$clusters[name]";
           echo "<input type='hidden' name='clusterlist[]' value='" . $clusters['id'] . "'>";
     }
}
?>
</table>
			
        <br><div align="center"><INPUT type="submit" name="edit_record" value="Save changes"></div>


</div> <div class="clear"></div>
	<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br><br>


</div>   
</div>   <br>
</body>
</html>

