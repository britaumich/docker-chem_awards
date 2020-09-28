<?php
require_once('../awards-config.php');
$uniqname = $purifier->purify($_REQUEST['uniqname']);

if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {

   
    $id = $purifier->purify($_REQUEST['id']);
    $result = mysqli_query($conn, "DELETE FROM recommenders WHERE id =$id")  or die(mysqli_error($conn));

     header("Location: recommenders.php?uniqname=$uniqname");  
}

?>
