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
$year = $purifier->purify($_REQUEST['year']);
if ($year == '') {
   $year = $report_year;
}
   
		
$id = $purifier->purify($_REQUEST['id']);
if ($id == "") {
     $uniqname = $purifier->purify($_REQUEST[uniqname]);
     $sql = "SELECT faculty.`id`, `uniqname`, `Name`, faculty.`Rank`, rank.rank as rank, `Year_PhD`, `birth_year`, `Appt_Start`  FROM `faculty`, rank  WHERE rank.id = faculty.Rank AND faculty.uniqname = '$uniqname'";
}
else {
#show Faculty  record
	$sql = "SELECT faculty.`id`, `uniqname`, `Name`, faculty.`Rank`, rank.rank as rank, `Year_PhD`, `birth_year`, `Appt_Start`  FROM `faculty`, rank  WHERE rank.id = faculty.Rank AND faculty.id = '$id'";
}
//echo $sql;
$result=mysqli_query($conn, $sql) or die("There was an error: ".mysqli_error($conn));
	$adata = mysqli_fetch_array($result, MYSQLI_BOTH);
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
    <form name="forme" method="post" action="edit_faculty.php?id=<?php echo $id; ?>">
           <input type="hidden" name="award_id" value="<?php echo $award_id; ?>">
         <input type='submit' name='Submit' value='Edit'>
        </form>
<br>&nbsp;&nbsp;
</div>

<div class='floatleft'>

    <form name="formp" method="post" action="faculty.php?id=<?php echo $idp; ?>">
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
    <form name="formn" method="post" action="faculty.php?id=<?php echo $idn; ?>">
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
	//Everything is peachy, pull record.
$uniqname = $adata['uniqname'];
?>	

<table>
<tr>
        <th>uniqname<br> (click to edit)</th>
        <th>Name</a></th>
        <th>Rank</a></th>
        <th>Year Phd</th>
        <th>Birth Year</th>
        <th>Appt Start</th>
        <th>Clusters</th>

<tr>
<td><a href='edit_faculty.php?id=<?php echo $adata['id']; ?>'><?php echo $uniqname; ?></a></td>  
<td> <?php print($adata['Name']) ?> 
<td> <?php print($adata['rank']) ?> 
<td> <?php print($adata['Year_PhD']) ?> 
<td> <?php print($adata['birth_year']) ?> 
<td> <?php print($adata['Appt_Start']) ?> 
<td>
<?php
$sqlcluster = "SELECT clusters.name FROM clusters INNER JOIN faculty_cluster ON clusters.id = faculty_cluster.cluster_id WHERE faculty_id = '$id'";
//echo $sqlcluster;
$resultcluster = mysqli_query($conn, $sqlcluster) or header('Location: ERROR.php?error="Unable to select applicant\'s information for editing."');
if (mysqli_num_rows($resultcluster) != 0) {
     while ( $clusters = mysqli_fetch_array($resultcluster, MYSQLI_BOTH) ) {
           echo $clusters['name'];
//           echo "&nbsp;";
           echo "<br>";
     }

}
?>
</table>
		<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>
			
<form name='form2' action='faculty.php' method='post'>
<input type="hidden" name="uniqname" id="uniqname" value="<?php echo $uniqname; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<?php
// faculty information
//$sql = "SELECT DISTINCT(year) FROM faculty_information ORDER BY year";
$sql = "SELECT DISTINCT(year) FROM faculty_data ORDER BY year";
$result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
//    echo "<select name='year' id='info' onchange='getinfo(this.value)'>";
//    echo "<select name='year' id='info' onchange='self.location=self.location+'?year='+this.options[this.selectedIndex].value+'&uniqname='+document.getElementById('uniqname').value'>";
    echo "<select name='year' id='info' onchange='this.form.submit()'>";
    echo "<option select value='error'> - choose year -</option>";

       while ($data = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
           echo "<option";
           if ($data['year'] == $year) { echo " selected"; }
           echo " value='$data[year]'>$data[year]</option>";
        }
    echo "</select><br><br>";
echo "<div id='txtHint'></div>";
$sql = "SELECT question, answer FROM faculty_data WHERE uniqname = '$uniqname' AND year = '$year'";
$result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
echo "<table>";
while ($qdata = mysqli_fetch_array($result, MYSQLI_BOTH))  {

echo "<tr><th>";
echo $qdata['question'];
echo "<td>";
   echo $qdata['answer'];
}
echo "</table><br><br>";

echo "<table>";

$sql1 = "SELECT * FROM faculty_letters WHERE uniqname = '$uniqname' ORDER BY type";
//echo $sql1;
$result1 = mysqli_query($conn, $sql1) or die ("Query failed : " . mysqli_error($conn));
WHILE ($recUpload = mysqli_fetch_array($result1, MYSQLI_BOTH))
       { $link = $uploaddir . $recUpload['link'];
?>
              <tr><td> <? print("$recUpload[type]") ?> :</td><td>
                 <? print("<a href=". $link . " target=\"_blank\"> $recUpload[link]</a>") ?><br>
              <td> <? print("$recUpload[upload_date]") ?></td>

                <?php
        }//while
?>
</table>
		<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>
<?php
$sqlf = "SELECT faculty_awards.award_id as award_id, faculty_awards.faculty_id AS faculty_id, faculty.Name, award_status.`status`, `year`, `comment`, awards_descr.Award_Name FROM `faculty_awards`JOIN faculty ON faculty_awards.faculty_id = faculty.id JOIN awards_descr ON faculty_awards.award_id = awards_descr.id, award_status WHERE faculty_awards.status = award_status.id AND faculty_id = $id ORDER BY year, award_status.status";
//echo $sqlf;
$resultf = mysqli_query($conn, $sqlf) or die("Query failed :".mysqli_error($conn));

if (mysqli_num_rows($resultf) != 0) {
echo "<table>";
echo "<th>Id<th>Award Name<th>year<th>status<th>Comments</tr>";
     while ( $faward = mysqli_fetch_array($resultf, MYSQLI_BOTH) ) {
         $status = $faward['status'];
         $uniqname = $faward['uniqname'];
         $dataid = $faward['dataid'];
         $faculty_id = $faward['faculty_id'];
         $year = $faward['year'];
//           echo"<tr><td><a href='award-one.php?id=$faward[award_id]'>$faward[Award_Name]</a></td>";
           echo"<tr><td>$faward[award_id]</td>";
           echo"<td>$faward[Award_Name]</td>";
           echo "<td>" . $year. "</td>";
           echo "<td>" . $status . "</td>";
           echo "<td>" . $faward['comment']. "</td>";
          echo "</td>";
     }

}


?>
<table>
<form>
		<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><br>

</div>   
</div> 
</script>
<script type="text/javascript">
function getinfo(str) {
console.log(str);

    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        var uniqname = document.getElementById('uniqname');
        uniqname = [uniqname.value];
console.log(uniqname);
        xmlhttp.open("GET","getFaculyInfo.php?q="+str+"&uniqname="+uniqname,true);
        xmlhttp.send();
    }
}

</script>
</body>
</html>

