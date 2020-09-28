<!DOCTYPE html>
<html>
<head>
<style>
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
</head>
<body>

<?php
$q = intval($_GET['q']);
require_once('../awards-config.php');
//$q = $purifier->purify($_GET['q']);


$sql = "SELECT recommenders.id as recid, recommenders.uniqname, faculty.Name, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.id = '$q'";
$result = mysqli_query($conn, $sql) or die ("There was an error updating rank: ".mysqli_error($conn));
$ldata = mysqli_fetch_array($result, MYSQLI_BOTH)
?>
<br><strong>Recommender's Name and Email:</strong><br><br>
<input type="hidden" name="recid" value="<?php echo $ldata[recid]; ?>" />
<input type="hidden" name="recnamenew" value="<?php echo $ldata[rec_name]; ?>" />
<input type="hidden" name="recemailnew" value="<?php echo $ldata[rec_email]; ?>" />
<strong>Name: <font color ="#FF0000" >*</font></strong> <input type="text" name="recname" autofocus value="<?php echo $ldata[rec_name]; ?>" disabled size="35" maxlength="200"/>
<br><br>
<strong>Email: <font color ="#FF0000" >*</font></strong>  <input type="text" name="recemailnew" id="recemailnew" value="<?php echo $ldata[rec_email]; ?>" disabled size="35" maxlength="200"/><br>

</body>
</html>
