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
if (isset($_REQUEST['id']) AND (is_numeric($_REQUEST['id']))) {
       $id = $purifier->purify($_REQUEST['id']);
   $search_id_list = array();
   $search_id_list = unserialize($_REQUEST['search_id_list']);
   $search_id_list = purica_array($conn, $search_id_list);

}
else {
$id = '';
}
//echo '<pre>all id'; var_export($search_id_list); echo '</pre>';

if ($_REQUEST['edit_record'] == "Save changes") {

  $id = (int)$purifier->purify($_REQUEST['id']);
  $type = $purifier->purify($_REQUEST['type']);
  $type1 = $purifier->purify($_REQUEST['type1']);
if ($type1 !== "") { $type = $type1; }
  $Award_Name = $purifier->purify($_REQUEST['Award_Name']);
  $due_month = $purifier->purify($_REQUEST['due_month']);
  $due_day = $purifier->purify($_REQUEST['due_day']);
  $Awarded_By = $purifier->purify($_REQUEST['Awarded_By']);
  $Link_to_Website = $purifier->purify($_REQUEST['Link_to_Website']);
  $Description = $purifier->purify($_REQUEST['Description']);
  $eligibility = $purifier->purify($_REQUEST['eligibility']);
  $who_is_eligible = $purifier->purify($_REQUEST['who_is_eligible']);
  $comments = $purifier->purify($_REQUEST['comments']);

if ($id !== 0) {
  $sql = "UPDATE awards_descr SET
      type = ?,
      Award_Name = ?,
      due_month = ?,
      due_day = ?,
      Awarded_By = ?,
      Link_to_Website = ?,
      Description = ?,
      eligibility = ?,
      who_is_eligible = ?,
      comments = ?
      WHERE id =?";
   $stmt = mysqli_prepare($conn, $sql);
   mysqli_stmt_bind_param($stmt, 'sssdssssdsd', $type, $Award_Name, $due_month, $due_day, $Awarded_By, $Link_to_Website, $Description, $eligibility, $who_is_eligible, $comments, $id); 
}
else {
  // add a new record

 $sql = "INSERT INTO awards_descr(type, Award_Name, due_month, due_day, Awarded_By, Link_to_Website, Description, eligibility, who_is_eligible, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; 
   $stmt = mysqli_prepare($conn, $sql);
   mysqli_stmt_bind_param($stmt, 'sssdssssds', $type, $Award_Name, $due_month, $due_day, $Awarded_By, $Link_to_Website, $Description, $eligibility, $who_is_eligible, $comments); 
}
  $res = mysqli_stmt_execute($stmt); 
  if ($res) { 
    if ($id == "") {
       // record was added not updated
      $id = mysqli_insert_id($conn);
   } 
//    $tag_check = array();
//    $tag_check = purica_array($conn, $_REQUEST[tag_check]);
    //echo '<pre>'; var_export($tag_check); echo '</pre>';
//    $taglist = array();
//    $taglist = purica_array($conn, $_REQUEST[taglist]);
    $cluster_check = array();
    $cluster_check = purica_array($conn, $_REQUEST['cluster_check']);
// echo '<pre>cluster'; var_export($cluster_check); echo '</pre>';
    $clusterlist = array();
    $clusterlist = purica_array($conn, $_REQUEST['clusterlist']);
// echo '<pre>'; var_export($clusterlist); echo '</pre>';
      if (!empty($cluster_check)) {
       // clusters
       $sqlcluster = "INSERT INTO award_cluster (`award_id`, `cluster_id`) VALUES";
       foreach ($cluster_check as $cluster_id) {
          $sqlcluster .= " (" . $id . ", " . $cluster_id . "),"; 
       }   
       $sqlcluster = substr($sqlcluster, 0, -1);
       $sqlcluster .= " ON DUPLICATE KEY UPDATE award_id = " . $id;
       $stmtc = mysqli_prepare($conn, $sqlcluster);
       $res = mysqli_stmt_execute($stmtc) or die("There was an error updating cluster: ".mysqli_error($conn));
      // to delete unchecked 
    if (count($cluster_check) !== (count($clusterlist))) {
      $sqldel = "DELETE FROM award_cluster WHERE";
      foreach ($clusterlist as $cluster1) {
         if (!in_array($cluster1, $cluster_check)) {
            $sqldel .= "(award_id = " . $id . " and cluster_id = " . $cluster1 . ") or ";
         }
       }
      $sqldel = substr($sqldel, 0, -4);
      $res = mysqli_query($conn, $sqldel) or die("There was an error updating cluster: ".mysqli_error($conn));
   }
     }
     else {
      $sqldel = "DELETE FROM award_cluster WHERE award_id = $id";
       $stmtd = mysqli_prepare($conn, $sqldel);
       $res = mysqli_stmt_execute($stmtd) or die("There was an error updating cluster: ".mysqli_error($conn));
     }
 //     if (!empty($tag_check)) {
       // tags
 //      $sqltags = "INSERT INTO award_tag (`award_id`, `tag_id`) VALUES";
//       foreach ($tag_check as $tag_id) {
//          $sqltags .= " (" . $id . ", " . $tag_id . "),"; 
//       }   
//       $sqltags = substr($sqltags, 0, -1);
//       $sqltags .= " ON DUPLICATE KEY UPDATE award_id = " . $id;
//       $res = mysqli_query($conn, $sqltags) or die("There was an error updating tags: ".mysqli_error($conn));
      // to delete unchecked 
//    if (count($tag_check) !== (count($taglist))) {
//      $sqldel = "DELETE FROM award_tag WHERE";
//      foreach ($taglist as $tag1) {
//         if (!in_array($tag1, $tag_check)) {
//            $sqldel .= "(award_id = " . $id . " and tag_id = " . $tag1 . ") or ";
//         }
//       }
//      $sqldel = substr($sqldel, 0, -4);
//      $res = mysqli_query($conn, $sqldel) or die("There was an error updating tags: ".mysqli_error($conn));
//    }
//    }
//     else {
//      $sqldel = "DELETE FROM award_tag WHERE award_id = $id";
//      $res = mysqli_query($conn, $sqldel) or die("There was an error updating tags: ".mysqli_error($conn));
//     }
    echo "<div align='center'>";
    echo "The record has been updated";
    echo "</div>";
  }
  else {
    die("There was an error updating a record: ".mysqli_error($conn));
  }
}

if ($id !== '') {
	//Everything is peachy, pull record.
 if (!$search_id_list) {  
     $result = mysqli_query($conn, "SELECT id FROM awards_descr ORDER BY id") or die("sqlsearch query failedb:".mysqli_error($conn));
     while ( $idata = mysqli_fetch_array($result, MYSQLI_BOTH) ) {

        // get a list of ids from $sqlsearch
         $search_id_list[] = $idata['id'];
     }
 }
// next and prev
$maxid = max(array_keys($search_id_list));
$minid = min(array_keys($search_id_list));

//echo $maxid;
//echo $minid;

$key_award_id = array_search($id, $search_id_list);
if ($key_award_id == $minid) {
    $idp = $search_id_list[$key_award_id];
    $idn = $search_id_list[$key_award_id + 1];
}
elseif ($key_award_id == $maxid) {
    $idn = $search_id_list[$key_award_id];
    $idp = $search_id_list[$key_award_id - 1];
}
else {
    $idp = $search_id_list[$key_award_id - 1];
    $idn = $search_id_list[$key_award_id + 1];
}
?>

<div class='floatright'>
        </form>
    <form name="forme" method="post" action="award.php?id=<?php echo $id; ?>">
           <input type="hidden" name="award_id" value="<?php echo $id; ?>">
<?php
     $arr = serialize($search_id_list);
     echo "<input type='hidden' name='search_id_list' value='" . $arr . "'>"  ;

?>
         <input type='submit' name='Submit' value='List'>
        </form>
</div>
<div class='floatleft'>

    <form name="formp" method="post" action="edit_award.php?id=<?php echo $idp; ?>">
           <input type="hidden" name="idp" value="<?php echo $idp; ?>">
<?php
     $arr = serialize($search_id_list);
     echo "<input type='hidden' name='search_id_list' value='" . $arr . "'>"  ;

if ($key_award_id == $minid) {
          echo "<input type='submit' name='Submit' value='Prev' disabled>";
}
else {
          echo "<input type='submit' name='Submit' value='Prev'>";
}
?>
</div>
        </form>
    <form name="formn" method="post" action="edit_award.php?id=<?php echo $idn; ?>">
           <input type="hidden" name="idn" value="<?php echo $idn; ?>">
<?php
     $arr = serialize($search_id_list);
     echo "<input type='hidden' name='search_id_list' value='" . $arr . "'>"  ;
if ($key_award_id == $maxid) {

          echo "<input type='submit' name='Submit' value='Next' disabled>";
}
else {
          echo "<input type='submit' name='Submit' value='Next'>";
}
?>
        </form>
<?php
}
	$sql = "SELECT awards_descr.id as id, `type`, `Award_Name`, `due_month`, `due_day`, `Awarded_By`, `Link_to_Website`, `Description`, `eligibility`, `who_is_eligible`, `comments`, eligibility_list.name AS who_is_eligible_name FROM  `awards_descr` JOIN eligibility_list ON who_is_eligible = eligibility_list.id WHERE  awards_descr.id = '$id'";
//echo $sql;
	//$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
	$result=mysqli_query($conn, $sql) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
	$adata = mysqli_fetch_array($result, MYSQLI_BOTH);
?>	
    <form name="form" method="post" action="edit_award.php">
<table>
<tr>
<th>Id: <td> <?php print($adata['id']) ?>
<INPUT type ='hidden' name='id' value='<?php echo $id; ?>'>
<tr><th>Type:<td>choose from the list &nbsp;&nbsp;
<?php
$type = $adata['type'];
$sqlp = "SELECT DISTINCT type FROM awards_descr";
    $resultp = mysqli_query($conn, $sqlp) or die("Query failed :".mysqli_error($conn));
    echo "<select name='type'>";

       while ($typelist = mysqli_fetch_array($resultp, MYSQLI_BOTH))
        {
           echo "<option";
           if ($typelist[type] == $type) { echo " selected"; }
           echo " value=$typelist[type]>$typelist[type]</option>";
        }
    echo "</select>";
?>
&nbsp;&nbsp;or&nbsp;&nbsp;<input type="text" name="type1" placeholder="-- enter new type --" value="" >
<tr><th>Cluster:<td> 
<?php
$sqlclusterids = "SELECT clusters.id FROM clusters INNER JOIN award_cluster ON clusters.id = award_cluster.cluster_id WHERE award_id = '$id'";
$resultcluster_list = mysqli_query($conn, $sqlclusterids) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
$clustersids = array();
while ($cluster1 = mysqli_fetch_array ($resultcluster_list, MYSQLI_BOTH)) {
   $clustersids[] = $cluster1[id];
}
$sqlcluster = "SELECT id, clusters.name FROM clusters";
$resultcluster = mysqli_query($conn, $sqlcluster) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
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
<tr><th>Award Name:<td><input type="text" name="Award_Name" size="90" value="<?php print($adata['Award_Name']) ?>"> 
<tr><th>Due Date:<td> 
<?php
$due_month = $adata['due_month'];
?>
<select name = 'due_month'>
<option value=''>--Select Month--</option>
    <option selected value='January' <?php if($due_month == 'January') { ?> selected <?php } ?>>January</option>
    <option value='February' <?php if($due_month == 'February') { ?> selected <?php } ?>>February</option>
    <option value='March' <?php if($due_month == 'March') { ?> selected <?php } ?>>March</option>
    <option value='April' <?php if($due_month == 'April') { ?> selected <?php } ?>>April</option>
    <option value='May' <?php if($due_month == 'May') { ?> selected <?php } ?>>May</option>
    <option value='June' <?php if($due_month == 'June') { ?> selected <?php } ?>>June</option>
    <option value='July' <?php if($due_month == 'July') { ?> selected <?php } ?>>July</option>
    <option value='August' <?php if($due_month == 'August') { ?> selected <?php } ?>>August</option>
    <option value='September' <?php if($due_month == 'September') { ?> selected <?php } ?>>September</option>
    <option value='October' <?php if($due_month == 'October') { ?> selected <?php } ?>>October</option>
    <option value='November' <?php if($due_month == 'November') { ?> selected <?php } ?>>November</option>
    <option value='December' <?php if($due_month == 'December') { ?> selected <?php } ?>>December</option>
    </select> 
<input type="text" name="due_day" value="<?php print($adata['due_day']); ?>"> 
<tr><th>Awarded By:<td><input type="text" name="Awarded_By" size="90" value="<?php print($adata['Awarded_By']) ?> ">
<tr><th>Link to Website:<td><input type="text" name="Link_to_Website" size="90" value="<?php print($adata['Link_to_Website']) ?>"> 
<tr><th>Description:<td><textarea name="Description" cols="90" rows="7"><?php echo $adata['Description'] ?> </textarea>
<tr><th>Eligibility:<td><textarea name="eligibility" cols="90" rows="7"><?php echo $adata['eligibility'] ?></textarea> 
<tr><th>Who is Eligible:<td> <?php
$whois = $adata['who_is_eligible'];
$sqlname = "SELECT id, name FROM eligibility_list ORDER BY name";
$resultname = mysqli_query($conn, $sqlname) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
if (mysqli_num_rows($resultname) != 0) {
     echo "<select name='who_is_eligible'>";
     while ( $names = mysqli_fetch_array($resultname, MYSQLI_BOTH) ) {
           echo "<option value='$names[id]'";
           if ($names['id'] == $adata['who_is_eligible']) { echo " selected"; } 
           echo ">$names[name]</option>";
     }
     echo "</select>";
}
/*
?> 
<tr><th>Tags:<td> 
<?php
$sqltag = "SELECT tags.id FROM `tags` inner join award_tag on tags.id = award_tag.tag_id where award_id = '$id'";
$resulttag = mysqli_query($conn, $sqltag) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
$tagids = array();
$taglist = array();
while ( $tag1 = mysqli_fetch_array ($resulttag, MYSQLI_BOTH)) {
   $tagids[] = $tag1[id]; 

}
$tagnum = count ($tagids);
$sqltag_list = "SELECT id, tags.tag FROM tags ORDER BY tag";
$resulttag_list = mysqli_query($conn, $sqltag_list) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
if (mysqli_num_rows($resulttag_list) != 0) {
     while ( $tags = mysqli_fetch_array($resulttag_list, MYSQLI_BOTH) ) {
           echo "<input type='checkbox' name='tag_check[";
           echo $tags[id];
           echo "]' ";
           echo "value='$tags[id]'";
           if (in_array($tags[id], $tagids)) {echo " checked"; }
           echo ">$tags[tag]";
           echo "<input type='hidden' name='taglist[]' value='" . $tags[id] . "'>";
     }
}
*/
?> 
<tr><th>Comments:<td><textarea name="comments" cols="90" rows="7"><?php echo $adata['comments'] ?> </textarea>
</table>
<?php
     $arr = serialize($search_id_list);
     echo "<input type='hidden' name='search_id_list' value='" . $arr . "'>"  ;

?>

			
        <br><div align="center"><INPUT type="submit" name="edit_record" value="Save changes"></div>


</div> <div class="clear"></div>
	<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br><br>


</div>   
</div> 
</body>
</html>
