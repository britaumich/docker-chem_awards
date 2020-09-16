<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<title>Chemistry Award  - University of Michigan</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META content="" name=KEYWORDS>
<META content="" name=description>
<link rel="stylesheet" href="../eebstyle.css">
<link rel="shortcut icon" href="favicon.ico">
</head>
<body>
<?php  
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.inc');
require_once('nav.php');
$errorid = $purifier->purify($_REQUEST['errorid']);


// if the recomtext field is empty 
if(isset($_POST['recomtext']) && $_REQUEST['recomtext'] != ""){
// let the spammer think that they got their message through
$recomtext = $purifier->purify($_REQUEST['recomtext']);

echo $recomtext;
   echo "<h1>Thanks</h1>";
exit;
}
if(isset($_POST[submit])) {

      $replacefile = $purifier->purify($_REQUEST['replacefile']);
      $uniqname = $purifier->purify($_REQUEST['uniqname']);
      $lettertype = "cv";


// store the file information to variables for easier access
   $tmp_name = $purifier->purify($_FILES['recfilename']['tmp_name']);
   $type = $purifier->purify($_FILES['recfilename']['type']);
   $name = $purifier->purify($_FILES['recfilename']['name']);
   $size = $purifier->purify($_FILES['recfilename']['size']);
   $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));


  if($name =='')  {
     $error.="Please select a PDF file!<br />";
  }
     elseif ($type != "application/pdf"){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (!((preg_match('/Firefox/i', $user_agent)) and ($file_extension == 'pdf'))) {
         $error.="Your file is not a PDF. Please select a PDF file!<br />";
        } //if
     }
  else {
    $pdf = 1;
  }

  if($error != ''){
     if($pdf == 1) {
           $error.="Please select a pdf file again! (for security reasons the script can't remember a file name)<br />";
     }
//     echo "<table><TR><TD align=center><span style=color:red><b>ERRORS!</b></span><TR><TD><span style=color:red>$error</span></table>";
     echo "<table><TR><TD><span style=color:red>$error</span></table>";
  }
  else {
  if ($replacefile == "yes") {
    // delete the old file
    $sql = "DELETE FROM faculty_letters WHERE uniqname = '$uniqname' AND type = '$lettertype'";
//echo $sql;
    $res = mysqli_query($conn, $sql) or die("There was an error replacing file in faculty_letters: ".mysqli_connect_error());
  }
          // rename and upload the file
     if ($_FILES['recfilename']['error'] === UPLOAD_ERR_OK) {
        // upload ok
//       $uploaddir = '/home/appspchem/upload/awards-files/';
        $upload_date = date("m-d-Y");
        $filename = $lettertype . "_" . $recname . $uniqname . "_" . time() . ".pdf";
        $uploadfile = $uploaddir . $filename;
        $sql = "INSERT faculty_letters (uniqname, rec_id, link, type, upload_date) VALUES('$uniqname', 0, '$filename', '$lettertype', '$upload_date')";
//echo $sql;
        $res = mysqli_query($conn, $sql) or die("There was an error updating faculty_letters: ".mysqli_connect_error());

        if (move_uploaded_file($tmp_name, $uploadfile)) {
           chmod($uploadfile,0644);
               echo "The file has been uploaded.";
               $again = "yes";
           }
        else {
          echo "error uploading file";
          exit;
       }    
  }

}

?>
<input type="hidden" name="uniqname" value="<?php echo $uniqname; ?>" />

<?php $ip = getenv("REMOTE_ADDR"); 
if ($reclastname == "") { $reclastname = $purifier->purify($_REQUEST['reclastname']); }
if ($recfirstname == "") { $recfirstname = $purifier->purify($_REQUEST['recfirstname']); }
if ($errorid == 0) {
?>
<input type="hidden" name="id" value="<?php echo $id; ?>" />
<?php
}
?>
<input type="hidden" name="errorid" value="<?php echo $errorid; ?>" />
<?php
}
//$uniqname = $_SERVER["REMOTE_USER"];
$uniqname = $_SERVER["REDIRECT_REMOTE_USER"];
?>
<div align="center"><h2>Upload a CV <br><br><h2>
</div></h2>
<strong>Faculty Uniqname: </strong><?php echo $uniqname; ?>
<form method="post" action="letter.php" enctype="multipart/form-data">

<?php
   $lettertype = "cv";

?>
<br><br>
<img src="../images/box650top.jpg"><div class="box650mid"><div class="pad15and10">
<h3>Upload File</h3>
<Br>
Must be <strong>ONE file</strong> and be in <strong>PDF format</strong>. Maximum file size is 20 MB.
<br><br>
<b>File:</b> <input type="file" name="recfilename"><br>
</div></div>
<img src="../images/box650btm.jpg">

<br>
<br>
<input type="checkbox" name="replacefile" value="yes"> Check to replace the file<br><br>
<input type="hidden" name="uniqname" value="<?php echo $uniqname; ?>" />
<input type="submit" name="submit" value="Submit Form" />

<br>
<br>
<bR><div align="center"><img src="../images/linecalendarpopup500.jpg"></div>
</form>

</body> 
</html>
