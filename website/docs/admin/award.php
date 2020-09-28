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
		
$award_id = $purifier->purify($_REQUEST['award_id']);
$search_id_list = array();
$search_id_list = unserialize($_REQUEST['search_id_list']);
$search_id_list = purica_array($conn, $search_id_list);

//echo '<pre>all id'; var_export($search_id_list); echo '</pre>';


$sql = "SELECT awards_descr.id, `type`, `Award_Name`, `due_month`, `due_day`, `Awarded_By`, `Link_to_Website`, `Description`, `eligibility`, `who_is_eligible`, `comments`, eligibility_list.name AS who_is_eligible FROM  `awards_descr` JOIN eligibility_list ON who_is_eligible = eligibility_list.id WHERE  awards_descr.id = '$award_id'";
//echo $sql;
	$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
	$adata = mysqli_fetch_array($result, MYSQLI_BOTH);
$maxid = max(array_keys($search_id_list));
$minid = min(array_keys($search_id_list));


$key_award_id = array_search($award_id, $search_id_list);
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
    <form name="forme" method="post" action="edit_award.php?id=<?php echo $award_id; ?>">
           <input type="hidden" name="award_id" value="<?php echo $award_id; ?>">
<?php
     $arr = serialize($search_id_list);
     echo "<input type='hidden' name='search_id_list' value='" . $arr . "'>"  ;
?>
         <input type='submit' name='Submit' value='Edit'>
        </form>
</div>

<div class='floatleft'>
    <form name="formp" method="post" action="award.php?award_id=<?php echo $idp; ?>">
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
        </form>
</div>
    <form name="formn" method="post" action="award.php?award_id=<?php echo $idn; ?>">
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
<table>
<tr>
<th>Id: <td><?php print($adata['id']) ?>
<tr><th>Type:<td><?php print($adata['type']) ?> 
<tr><th>ACS Cluster:<td> 
<?php
$sqlcluster = "SELECT clusters.name FROM `clusters` inner join award_cluster on clusters.id = award_cluster.cluster_id where award_id = '$award_id'";
$resultcluster = mysqli_query($conn, $sqlcluster) or die("There was an error: ".mysqli_error($conn));
if (mysqli_num_rows($resultcluster) != 0) {
     while ( $clusters = mysqli_fetch_array($resultcluster, MYSQLI_BOTH) ) {
           echo $clusters['name']; 
           echo "&nbsp;";
     }

}
?> 
<tr><th>Award Name:<td><?php print($adata['Award_Name']) ?> 
<tr><th>Due Date:<td><?php print($adata['due_month']); echo ",&nbsp;&nbsp;";  print($adata['due_day']); ?> 
<tr><th>Awarded By:<td><?php print($adata['Awarded_By']) ?> 
<tr><th>Link to Website:<td><?php print("<a href='$adata[Link_to_Website]'>$adata[Link_to_Website]</a>"); ?> 
<tr><th>Description:<td><?php print($adata['Description']) ?> 
<tr><th>Eligibility:<td><?php echo nl2br($adata['eligibility']) ?> 
<tr><th>Who is Eligible:<td><?php echo ($adata['who_is_eligible']) ?> 
<!--
<tr><th>Tags:<td> 
-->
<?php
/*
$sqltag = "SELECT tags.tag FROM `tags` inner join award_tag on tags.id = award_tag.tag_id where award_id = '$award_id'";
$resulttag = mysqli_query($conn, $sqltag) or die("There was an error: ".mysqli_error($conn));
if (mysqli_num_rows($resulttag) != 0) {
     while ( $tags = mysqli_fetch_array($resulttag, MYSQLI_BOTH) ) {
           echo $tags['tag']; 
           echo "&nbsp;";
     }

}
*/
?> 
<tr><th>Comments:<td><?php print($adata['comments']) ?> 
</table>
		<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>
			

</div> <div class="clear"></div>
<?php
// faculty list for this award


$sqlf = "SELECT faculty_awards.id as dataid, faculty_awards.`uniqname` AS uniqname, faculty_awards.faculty_id AS faculty_id, faculty.Name, award_status.`status`, `year`, `comment` FROM `faculty_awards`JOIN faculty ON faculty_awards.faculty_id = faculty.id, award_status WHERE faculty_awards.status = award_status.id AND award_id = $award_id ORDER BY year, award_status.status";
//echo $sqlf;
$resultf = mysqli_query($conn, $sqlf) or die("Query failed :".mysqli_error($conn));
if (mysqli_num_rows($resultf) != 0) {
echo "<table>";
echo "<th>Name<th>year<th>status<th>Comments</tr>";
     while ( $faward = mysqli_fetch_array($resultf, MYSQLI_BOTH) ) {
         $status = $faward['status'];
         $uniqname = $faward['uniqname'];
         $dataid = $faward['dataid'];
         $faculty_id = $faward['faculty_id'];
         $year = $faward['year'];
//           echo "<tr><td>" . $faward['Name']. "</td>";
           echo"<tr><td><a href='faculty.php?id=$faward[faculty_id]'>$faward[Name]</a></td>";
           echo "<td>" . $year. "</td>";
           echo "<td>" . $status . "</td>";
           echo "<td>" . $faward['comment']. "</td>";
         if ($status == "interested") {
           echo "<form name='form' action='to_in_process.php' method='post'>"; 
           echo '<input type="hidden" name="uniqname" value="' . $uniqname . '">';
           echo '<input type="hidden" name="faculty_id" value="' . $faculty_id . '">';
           echo '<input type="hidden" name="award_id" value="' . $award_id . '">';
           echo '<input type="hidden" name="dataid" value="' . $dataid . '">';
           echo '<input type="hidden" name="prog_name" value="award.php">';
           echo "<td><input type='submit' name='submit' value='inprocess' /></td>";
           echo('</form></td>');
         }
           echo "</td>";
     }

}
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
           echo "<td>" . $awardnc['comment'] . "</td>";
   }
}
echo "</table>";
?>
<br><br>
</div>   
</div>
</body>
</html>
