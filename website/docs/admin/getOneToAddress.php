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
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
//$q = $purifier->purify($_GET['q']);


$sql = "SELECT recommenders.id AS recid, recommenders.uniqname, faculty.Name AS facname, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.id = '$q'";
$result = mysqli_query($conn, $sql) or die("There was an error 5: ".mysqli_error($conn));
$ldata = mysqli_fetch_array($result, MYSQLI_BOTH);
$facname = $ldata[facname];

?>
<br><strong>Recommender's Name and Email: </strong><br><br>
<input type="hidden" name="recid" value="<?php echo $ldata[recid]; ?>" />
<input type="hidden" name="recemailnew" value="<?php echo $ldata[rec_email]; ?>" />


<strong>Email: <font color ="#FF0000" >*</font></strong>  <input type="text" name="recemailnew" id="recemailnew" value="<?php echo $ldata[rec_email]; ?>" disabled size="35" maxlength="200"/><br>
<br>
<strong>To: <input type="text" name="recnamenew" autofocus value="Dear Dr. <?php echo $ldata[rec_name]; ?>" size="35" maxlength="200"/>
<br><br><strong>Message: <font color ="#FF0000" >*</font></strong>

<br><textarea name="message" id="message" cols="74" rows="6" maxlength="2000">Please send a recommendation letter for <?php echo $facname; ?></textarea>
</body>
</html>
