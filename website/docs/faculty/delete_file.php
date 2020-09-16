<?php
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.inc');
require_once('../library/HTMLPurifier.auto.php');
$purifier = new HTMLPurifier();


if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
    $id = $purifier->purify($_REQUEST['id']);
    $result = mysqli_query($conn, "DELETE FROM faculty_letters WHERE id =$id")  or die(mysqli_error($conn));

     header("Location: faculty.php");  
}

?>
