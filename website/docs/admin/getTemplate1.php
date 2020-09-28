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
<input type="hidden" name="template" value="<?php echo $q; ?>" />
<?php

$sql = "SELECT name, text FROM email_templates WHERE name = '$q'";
$result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
$template = mysqli_fetch_array($result, MYSQLI_BOTH);
$name = $template[name];
if ($name == "Empty template") {
   echo '<br><strong>Type your message. You can save it as a new template, if you enter a name for the template and click "Add as a template" button</strong>';
}
?>
<br><strong>Message: <font color ="#FF0000" >*</font></strong>
<br><textarea name="message" id="message" cols="74" rows="6" maxlength="2000"><?php echo $template[text]; ?></textarea>
<input type="submit" name="updatetemplate" value="Update the template" />

</body>
</html>
