<?php
require_once "../dbConnect.inc";

$uniqname = check_input($conn, $_REQUEST['uniqname']);

if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {

   
    $id = check_input($conn, $_REQUEST['id']);
    $result = mysqli_query($conn, "DELETE FROM recommenders WHERE id =$id")  or die(mysqli_error($conn));

     header("Location: recommenders.php?uniqname=$uniqname");  
}

?>
