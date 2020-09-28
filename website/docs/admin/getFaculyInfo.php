<!DOCTYPE html>
<html>
<head>
<!-- <style>
table {
    width: 100%;
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}
</style>
-->
</head>
<body>

<?php
//$q = intval($_GET['q']);
require_once('../awards-config.php');
$year = $purifier->purify($_GET['q']);
$uniqname = $purifier->purify($_REQUEST['uniqname']);

echo '<input type="hidden" name="uniqname" value="<?php echo $uniqname; ?>" />';

//$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'faculty_information' and COLUMN_NAME LIKE '%quest%'";
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
?>
</body>
</html>
