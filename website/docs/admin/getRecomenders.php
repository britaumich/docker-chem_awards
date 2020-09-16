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
//$q = intval($_GET['q']);
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
$q = $purifier->purify($_GET['q']);

?>
<input type="hidden" name="uniqname1" value="<?php echo $q; ?>" />
<?php
$recid = $purifier->purify($_REQUEST['recid']);

$sql = "SELECT recommenders.id as recid, recommenders.uniqname, faculty.Name, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.uniqname = '$q'";
$result = mysqli_query($conn, $sql) or die ("There was an error 6: ".mysqli_error($conn));

echo "<br><strong>Select a Recommender: </strong>";
print("<select name='recid' id='recid' onchange='showOneRecomender(this.value)'>");
        print("<option select value=''> - choose name -</option>");

        WHILE ($ldata = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($ldata[recid] == $recid) { echo " selected"; }
                echo " value='$ldata[recid]'>$ldata[rec_name]</option>";
        }

echo "</select>";

?>

</body>
</html>
