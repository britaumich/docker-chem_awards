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
require_once('nav.php');
require_once('../awards-config.php');
require_once "../php_mail.inc";
$errorid = $purifier->purify($_REQUEST['errorid']);

// if the recomtext field is empty 
if(isset($_POST['recomtext']) && $_REQUEST['recomtext'] != ""){
// let the spammer think that they got their message through
$recomtext = $purifier->purify($_REQUEST['recomtext']);
echo $recomtext;
   echo "<h1>Thanks</h1>";
exit;
}
$recnamenew = "";
$recemailnew = "";
if(isset($_POST['submit'])) {

      $replacefile = $purifier->purify($_REQUEST['replacefile']);
      $uniqname = $purifier->purify($_REQUEST['uniqname1']);
      $lettertype = $purifier->purify($_REQUEST['lettertype']);
      $lettertype1 = $purifier->purify($_REQUEST['lettertype1']);
  if($uniqname =='' ){ $uniqname = $purifier->purify($_REQUEST['uniqname']); }
  if($uniqname =='' ){ $error.="Please select a faculty!<br />"; }
  if($lettertype =='' ) {
      if($lettertype1 =='' ) { 
          $error.="Please select a type!<br />"; 
      }
      else {
        $lettertype = $lettertype1;
      }
   }

  if ($lettertype == "recommendation")  {
      $recid = $purifier->purify($_REQUEST['recid']);
  if($recid =='' ){ $error.="Please select a recommender!<br />"; }
      $recnamenew = $purifier->purify($_REQUEST['recnamenew']);
      $recemailnew = $purifier->purify($_REQUEST['recemailnew']);
      $recname = str_replace(' ', '_', $recnamenew);

      $recname = "-" . $recname . "-";
  }
  else {
     $recid = 0;
     $recname = "-";
  }

// store the file information to variables for easier access
//$file1 = array();
//print_r($_FILES);
//echo '<pre>'; var_export($_FILES); echo '</pre>';
//exit;
   $tmp_name = $purifier->purify($_FILES['recfilename']['tmp_name']);
   $type = $purifier->purify($_FILES['recfilename']['type']);
   $name = $purifier->purify($_FILES['recfilename']['name']);
   $size = $purifier->purify($_FILES['recfilename']['size']);
   $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));


  if($name =='')  {
     $error.="Please select a PDF or DOC file!<br />";
  }
     elseif (!($file_extension == "pdf" || $file_extension == "doc" || $file_extension == "docx")){
         $error.="Your file is not a PDF or DOC. Please select a correct file!<br />";
     } //elseif
  else {
    $pdf = 1;
  }

  if($error != ''){
     if($pdf == 1) {
           $error.="Please select a pdf file again! (for security reasons the script can't remember a file name)<br />";
     }
     echo "<table><TR><TD><span style=color:red>$error</span></table>";
  }
  else {
  if ($replacefile == "yes") {
    // delete the old file
    $sql = "DELETE FROM faculty_letters WHERE uniqname = '$uniqname' AND type = '$lettertype'";
//echo $sql;
    $res = mysqli_query($conn, $sql) or die("There was an error replacing file in faculty_letters: ".mysqli_error($conn));
  }
          // rename and upload the file
     if ($_FILES['recfilename']['error'] === UPLOAD_ERR_OK) {
        // upload ok
//       $uploaddir = '/home/appspchem/upload/awards-files/';
//        $filename = $lettertype . $recname . $uniqname . "-" . time() . ".pdf";
        $filename = $lettertype . $recname . $uniqname . "-" . time() . "." . $file_extension;
        $uploadfile = $uploaddir . $filename;
        $upload_date = date("m-d-Y");
        $sql = "INSERT faculty_letters (uniqname, rec_id, link, type, upload_date) VALUES('$uniqname', $recid, '$filename', '$lettertype', '$upload_date')";
//echo $sql;
        $res = mysqli_query($conn, $sql) or die("There was an error updating faculty_letters: ".mysqli_error($conn));

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
?>
<div align="center"><h2>Upload a Letter <br><br><h2>
</div></h2>
<form method="post" action="letter.php" enctype="multipart/form-data">
<strong>Select a Faculy: </strong> 
 <?php
$lettertype = $purifier->purify($_REQUEST['lettertype']);
$lettertype1 = $purifier->purify($_REQUEST['lettertype1']);
if ($again == "yes") {
    $uniqname = "";
    $lettertype = "";
    $lettertype1 = "";
}
        $sql = "SELECT uniqname, Name FROM faculty ORDER BY name ASC";
        $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
        print("<select name='uniqname' id='uniqname'>");
        print("<option select value=''> - choose name -</option>");
             
        WHILE ($applicant_name = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($applicant_name['uniqname'] == $uniqname) { echo " selected"; }
                echo " value='$applicant_name[uniqname]'>$applicant_name[Name]</option>";
        }
?>
</select>
<?php
    $sql = "SELECT DISTINCT (type) FROM faculty_letters";
   $result = mysqli_query($conn, $sql);

   echo "<br><br><strong>Select a Type of a Letter: </strong>";
   print("<select name='lettertype' id='type' onchange='showRecomenders(this.value)'>");
        print("<option select value=''> - choose type -</option>");

        WHILE ($ldata = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($ldata['type'] == $lettertype) { echo " selected"; }
                echo " value='$ldata[type]'>$ldata[type]</option>";
        }

    echo "</select>";


if ($lettertype == "recommendation") {
$recid = $purifier->purify($_REQUEST['recid']);

   $sql = "SELECT recommenders.id as recid, recommenders.uniqname, faculty.Name, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.uniqname = '$uniqname'";
$result = mysqli_query($conn, $sql);

   echo "<br><br><strong>Select a Recommender: </strong>";
   print("<select name='recid' id='recid' onchange='showOneRecomender(this.value)'>");
        print("<option select value='none'> - choose name -</option>");

        WHILE ($ldata = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($ldata[recid] == $recid) { echo " selected"; }
                echo " value='$ldata[recid]'>$ldata[rec_name]</option>";
        }

   echo "</select>";
}
if ((($recnamenew !== "") || ($recemailnew !== "")) && ($again !== "yes")){
?>
<br><br><strong>Recommender's Name and Email: </strong><br><br>
<strong>Name: <font color ="#FF0000" >*</font></strong> <input type="text" name="recnamenew" autofocus value="<?php echo $recnamenew; ?>" disabled size="35" maxlength="200"/>
<br><br>
<strong>Email: <font color ="#FF0000" >*</font></strong>  <input type="text" name="recemailnew" id="recemailnew" value="<?php echo $recemailnew; ?>" disabled size="35" maxlength="200"/><br><Br>
<br>
<?php
}
?>
<div id="txtHint0"></div>
<div id="txtHint"></div>
<div id="txtHint1"></div>
<br><br>
<img src="../images/box650top.jpg"><div class="box650mid"><div class="pad15and10">
<h3>Upload File</h3>
<Br>
Must be <strong>ONE file</strong> and be in <strong>PDF or DOC format</strong>. Maximum file size is 20 MB.
<br><br>
<b>File:</b> <input type="file" name="recfilename"><br>
</div></div>
<img src="../images/box650btm.jpg">

<br>
<br>
<input type="checkbox" name="replacefile" value="yes"> Check to replace the file<br><br>
<input type="submit" name="submit" value="Submit Form" />

<br>
<br>
<bR><div align="center"><img src="../images/linecalendarpopup500.jpg"></div>
</form>
<script>
function showRecomenders(str) {
console.log(str);

    if (str == "") {
        document.getElementById("txtHint").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        if (str == "recommendation" ) {
      
             var uniqname1 = document.getElementById('uniqname');
             uniqname1 = [uniqname1.value];
             xmlhttp.open("GET","getRecomenders.php?q="+uniqname1,true);
             xmlhttp.send();
        } else {
              document.getElementById("txtHint").innerHTML = "";
              return;
        } 
    }
}
function showOneRecomender(str) {

    if (str == "") {
        document.getElementById("txtHint1").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint1").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getOneRecomender.php?q="+str,true);
        xmlhttp.send();
    }
}

</script>

</body> 
</html>