<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<head>
<title>Chemistry Awards System  - University of Michigan</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META content="" name=KEYWORDS>
<META content="" name=description>
<link rel="stylesheet" href="../eebstyle.css">
<link rel="shortcut icon" href="favicon.ico">
</head>

<body>
<?php
require_once('nav.php');
require_once($_SERVER["DOCUMENT_ROOT"] . '/../support/awards_dbConnect.php');
require_once "../php_mail.inc";
require_once "../php_mail_with_file.inc";
require_once('../ldap.inc');



$error='';//initialize $error to blank

// if the recomtext field is empty
if(isset($_POST['recomtext']) && $_REQUEST['recomtext'] != ""){
// let the spammer think that they got their message through
$recomtext = $purifier->purify($_REQUEST['recomtext']);
echo $recomtext;
   echo "<h1>Thanks</h1>";
exit;
}

//$user = $_SERVER["REMOTE_USER"]; 
$user = $_SERVER["REDIRECT_REMOTE_USER"]; 
if (!isset ($_REQUEST[fromaddress])) {
    $fromaddress = $user;
} 
if(isset($_POST[addtemplate])) {
    
   $newname = $purifier->purify($_REQUEST['newname']);
   $newtext = $purifier->purify($_REQUEST['message']);
   $sql = "INSERT INTO email_templates (name, text) VALUES ('$newname', '$newtext')";
   $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
   echo "template has been added";


}
$uniqname = "error";
$recnamenew = "";
$recemailnew = "";
if(isset($_POST[submit])) {

         $recid = $purifier->purify($_REQUEST['recid']);
  if($recid =='' ){ $error.="Please select a recommender!<br />"; }
      $recnamenew = $purifier->purify($_REQUEST['recnamenew']);
      $recemailnew = $purifier->purify($_REQUEST['recemailnew']);

   $uniqname = $purifier->purify($_REQUEST['uniqname1']);
   if($uniqname =='' ){ $uniqname = $purifier->purify($_REQUEST['uniqname']); }
   if($uniqname =='' ){ $error.="Please select a faculty!<br />"; }
   if($purifier->purify($_REQUEST['fromaddress'])=='' ){ $error.="Please enter your fromaddress!<br />"; }
   if($purifier->purify($_REQUEST['recemailnew'])=='' ){ $error.="Please enter your toaddress!<br />"; }
   if($purifier->purify($_REQUEST['subject'])=='' ){ $error.="Please enter your subject!<br />"; }
   if($purifier->purify($_REQUEST['message'])=='' ){ $error.="Please enter your message!<br />"; }
     // check if files were selected
     $files = array();
     $files = $_REQUEST[files];
//     echo '<pre>'; var_export($files); echo '</pre>'; 
    
   if($error != ''){
     if($pdf == 1) {
           $error.="Please select a pdf file again! (for security reasons the script can't remember a file name)<br />";
     }
     echo "<table><TR><TD align=center><span style=color:red><b>ERRORS!</b></span><TR><TD><span style=color:red>$error</span></table>";
  }
  else {

     // no errors in the form; prosess data
     $from = $fromaddress . '@umich.edu';
     $from = $purifier->purify($_REQUEST['fromaddress']) . '@umich.edu';
//     $to = $purifier->purify($_REQUEST['recemailnew']);
$to = $from;
     $subject = $purifier->purify($_REQUEST['subject']);
     $message = $purifier->purify($_REQUEST['recnamenew']);
     $message .= "<br><br>";
     $message .= $purifier->purify($_REQUEST['message']);
     $message .= "<br><a href=\"https://apps-prod.chem.lsa.umich.edu/upload/upload_letter.php?id=$recid\">Upload letter here</a>";
     $message .= "<br><br>";
     $message .= nl2br(str_replace('\\r\\n', "\r\n", $purifier->purify($_REQUEST['signature'])));
     if (!empty($files)) {
        // attach files
        foreach ($files as $fileid) {
               echo "fileid";
               echo $fileid;
               $sql = "SELECT link FROM faculty_letters WHERE id = $fileid";
               $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
               $filename = mysqli_fetch_array($result, MYSQLI_BOTH)[link];
               $uploadfile = '/home/appspchem/upload/awards-files/' . $filename;
               echo $filename;
        }
    }
     $ok = send_mail($from, $to, "",  $subject, $message, $uploadfile, $filename);
       if ($ok) {
         print "Your email has been sent.";
       }
       else {
             echo "failed to send email";
    //   header('Location: ERROR.php?error="recommendation.php, error 19 - failed to send email."');
       exit;
      }
      exit;
  }

}
?>

<form method="post" action="ask_recommendation.php" enctype="multipart/form-data">
<?php
if ($fromaddress == "") { $fromaddress = $purifier->purify($_REQUEST['fromaddress']); }
if ($recemailnew == "") { $recemailnew = $purifier->purify($_REQUEST['recemailnew']); }
if ($subject == "") { $subject = $purifier->purify($_REQUEST['subject']); }
if ($message == "") { $message = $purifier->purify($_REQUEST['message']); }
?>
<div align="center"><h2>Send an email asking about a recommendation letter</h2><br></div>
<!--
<strong>From Address: <font color ="#FF0000" >*</font></strong> <input type="text" name="fromaddress" id="fromaddress" oninput=changesignature() value="<?php echo $purifier->purify($fromaddress); ?>" size="15" maxlength="200"/>@umich.edu
-->
<strong>From Address: <font color ="#FF0000" >*</font></strong> <input type="text" name="fromaddress" value="<?php echo $purifier->purify($fromaddress); ?>" size="15" maxlength="200"/>@umich.edu
<br><br>
<strong>Subject: <font color ="#FF0000" >*</font></strong> <input type="text" name="subject" autofocus value="Recommendation Letter" size="35" maxlength="200"/>
<br><br>

<strong>Select a Faculty: <font color ="#FF0000" >*</font></strong>

 <?php

        $sql = "SELECT uniqname, Name FROM faculty ORDER BY name ASC";

        $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));

        print("<select name='uniqname' onchange='showRecomenders(this.value)'>");
        print("<option select value='error'> - choose name -</option>");

        WHILE ($applicant_name = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($applicant_name[uniqname] == $uniqname) { echo " selected"; }
                echo " value='$applicant_name[uniqname]'>$applicant_name[Name]</option>";
        }
?>
</select>


<?php
if ($uniqname !== "error") {
//echo '<pre>'; var_export($files); echo '</pre>';
    $sqlf = "SELECT id, link FROM faculty_letters WHERE uniqname = '$uniqname' AND type <> 'recommendation'";
   $resultf = mysqli_query($conn, $sqlf) or die("Query failed :".mysqli_error($conn));
   echo "<div class='floatright'> <h2>Select a File</h2>";
   while ($fdata = mysqli_fetch_array($resultf, MYSQLI_BOTH)) {
    $id = $fdata[id];
    echo "<input type='checkbox' name='files[";
    echo $fdata[id];
    echo "]' ";
    echo "value='$id'";
    if ($files[$id] == $id) {echo " checked"; }
    echo ">$fdata[link]<br>";
//    echo "<input type='hidden' name='clusterlist[]' value='" . $clusters[id] . "'>";

}
echo "</div>";
    $recid = $purifier->purify($_REQUEST['recid']);

   $sql = "SELECT recommenders.id as recid, recommenders.uniqname, faculty.Name, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.uniqname = '$uniqname'";
$result = mysqli_query($conn, $sql);

   echo "<h2>Select a Recommender</h2>";
   print("<select name='recid' id='recid' onchange='showOneRecomender(this.value)'>");
        print("<option select value='none'> - choose name -</option>");

        WHILE ($ldata = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($ldata[recid] == $recid) { echo " selected"; }
                echo " value='$ldata[recid]'>$ldata[rec_name]</option>";
        }

   echo "</select>";


if (($recnamenew !== "") || ($recemailnew !== "")) {
$sql = "SELECT recommenders.id AS recid, recommenders.uniqname, faculty.Name AS facname, rec_name, rec_email FROM recommenders, faculty WHERE recommenders.uniqname = faculty.uniqname AND recommenders.id = '$recid'";
$result = mysqli_query($conn, $sql);
$ldata = mysqli_fetch_array($result, MYSQLI_BOTH);
$facname = $ldata[facname];

?>
<h2>Recommender's Name and Email</h2><br>
<input type="hidden" name="recid" value="<?php echo $ldata[recid]; ?>" />
<input type="hidden" name="recemailnew" value="<?php echo $ldata[rec_email]; ?>" />


<strong>Email: <font color ="#FF0000" >*</font></strong>  <input type="text" name="recemailnew" id="recemailnew" value="<?php echo $ldata[rec_email]; ?>" disabled size="35" maxlength="200"/><br>
<br>
<strong>To: <input type="text" name="recnamenew" autofocus value="Dear Dr. <?php echo $ldata[rec_name]; ?>" size="35" maxlength="200"/>
<br><br><strong>Message: <font color ="#FF0000" >*</font></strong>

<br><textarea name="message" id="message" cols="74" rows="6" maxlength="2000">Please send a recommendation letter for <?php echo $facname; ?></textarea>
<?php
}
}
echo "<div id='txtHint'></div>";

echo "<div id='txtHint1'></div>";

$fromname = ldap_name($fromaddress);
//$signature = $purifier->purify($_REQUEST['signature']);
$signature = nl2br(str_replace('\\r\\n', "&#13;&#10;", $purifier->purify($_REQUEST['signature'])));
if ($signature == "") {
    $signature = "Thank you, &#13;&#10;" . $fromname;
}
?>
<br><br>
<strong>Signature: <font color ="#FF0000" >*</font></strong>
<br><textarea name="signature" id="signature" cols="74" rows="3" maxlength="2000"><?php echo $signature; ?></textarea>


<p class="recomtextclass">Leave this empty: <input type="text" name="recomtext" /></p>

<bR><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><Br>
<input type="submit" name="submit" value="Submit Form" />
<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><Br>

</form>
<script type="text/javascript">
function changesignature()
{
    var fromaddress = document.getElementById('fromaddress');

    var signature = document.getElementById('signature');
    var from = [fromaddress.value];
console.log(from);
    signature.value = from;
}
function gettemplate()
{
    var template = document.getElementById('template');
    var message = document.getElementById('message');
    var text = [template.value];
    var text1 = [template.value];
    message.value = text;
}
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
        xmlhttp.open("GET","getRecomAndFaculty.php?q="+str,true);
        xmlhttp.send();
    }
}
function showOneRecomender(str) {
console.log(str);

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
        xmlhttp.open("GET","getOneToAddress.php?q="+str,true);
        xmlhttp.send();
    }
}

</script>
</body>
</html>
