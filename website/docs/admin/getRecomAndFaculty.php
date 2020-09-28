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
require_once('../awards-config.php');
$q = $purifier->purify($_GET['q']);

?>
<input type="hidden" name="uniqname1" value="<?php echo $q; ?>" />
<?php
$files = array();
$sql = "SELECT recommenders.id as recid, recommenders.uniqname, faculty.Name, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.uniqname = '$q'";
   $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));


$sqlf = "SELECT id, link FROM faculty_letters WHERE uniqname = '$q' AND type <> 'recommendation'";
   $resultf = mysqli_query($conn, $sqlf) or die("Query failed :".mysqli_error($conn));
echo "<div class='floatright'> <strong>Select a File:</strong><br>";
while ($fdata = mysqli_fetch_array($resultf, MYSQLI_BOTH)) {
    echo "<input type='checkbox' name='files[";
    echo $fdata[id];
    echo "]' ";
    echo "value='$fdata[id]'";  
//    if (in_array($clusters[id], $clustersids)) {echo " checked"; }
    echo ">$fdata[link]<br>";
//    echo "<input type='hidden' name='clusterlist[]' value='" . $clusters[id] . "'>";

}
echo "</div>";
echo "<br><strong>Select a Recommender: </strong>";
print("<select name='recid' id='recid' onchange='showOneRecomender(this.value)'>");
        print("<option select value='none'> - choose name -</option>");

        WHILE ($ldata = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($ldata[rec_email] == $recemailnew) { echo " selected"; }
                echo " value='$ldata[recid]'>$ldata[rec_name]</option>";
        }

echo "</select>";

?>
</body>
</html>
