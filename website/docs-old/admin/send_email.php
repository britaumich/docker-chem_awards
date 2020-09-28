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
if(isset($_POST[updatetemplate])) {
    
   $template = $purifier->purify($_REQUEST['template']);
   $text = $purifier->purify($_REQUEST['message']);
   $sql = "UPDATE email_templates SET text = '$text' WHERE name = '$template'";
   $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
   echo "template has been updated";
   $addupdate = "yes";
}
if(isset($_POST[addtemplate])) {
    
   $newname = $purifier->purify($_REQUEST['newname']);
   $newtext = $purifier->purify($_REQUEST['message']);
   $sql = "INSERT INTO email_templates (name, text) VALUES ('$newname', '$newtext')";
   $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
   echo "template has been added";
   $addupdate = "yes";
}
if(isset($_POST[submit])) {

   if($purifier->purify($_REQUEST['fromaddress'])=='' ){ $error.="Please enter your fromaddress!<br />"; }
   if(($purifier->purify($_REQUEST['toaddress'])=='' ) AND ($purifier->purify($_REQUEST['eligibility'])=='error')){ $error.="Please enter your toaddress!<br />"; }
   if($purifier->purify($_REQUEST['subject'])=='' ){ $error.="Please enter your subject!<br />"; }
   if($purifier->purify($_REQUEST['message'])=='' ){ $error.="Please enter your message!<br />"; }
//   $template_yes = $purifier->purify($_REQUEST['template_yes']);
    
   if($error != ''){
     if($pdf == 1) {
           $error.="Please select a pdf file again! (for security reasons the script can't remember a file name)<br />";
     }
     echo "<table><TR><span style=color:red>$error</span></table>";
  }
  else {

     // no errors in the form; prosess data
     $from = $fromaddress . '@umich.edu';
     $from = $purifier->purify($_REQUEST['fromaddress']) . '@umich.edu';
     $subject = $purifier->purify($_REQUEST['subject']);
     $message = $purifier->purify($_REQUEST['message']);
     $message = nl2br(str_replace('\\r\\n', "\r\n",$message));
     $signature = $purifier->purify($_REQUEST['signature']);
     $eligibility = $purifier->purify($_REQUEST['eligibility']);
     $between = $purifier->purify($_REQUEST['between']);
     $month = $purifier->purify($_REQUEST['month']);
     $to = $purifier->purify($_REQUEST['toaddress']);
     $bcc = $purifier->purify($_REQUEST['bccaddress']);
     if ($bcc !== "") { $bcc .= '@umich.edu'; }

     if ($to == "" ) {
        $sql = "select uniqname, Name, rank from faculty join eligibility on Rank = rank_id where eligibility_id = $eligibility";
//echo $sql;
//exit;
        $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
         // send email to all eligible
         while ($results = mysqli_fetch_array($result, MYSQLI_BOTH)) {
             $uniqname = $results[uniqname];
           if ($between == "yes") {
             $message1 = "<br><a href=\"https://apps-prod.chem.lsa.umich.edu/chem-awards/faculty/check_award.php?uniqname=$uniqname&is_eligible=$eligibility&month=$month\">Click here for a list of awards</a>";
           $message2 = $message . $message1;
          }
                 $to = $results[uniqname] . '@umich.edu';
echo $to;
echo "<br>";
// for testing
$to = $from;
     $message2 .= "<br><br>";
     $message2 .= nl2br(str_replace('\\r\\n', "\r\n", $signature));

                 $ok = send_mail($from, $to, $bcc, $subject, $message2, FALSE, FALSE);
                 if (!$ok) {
                      echo "failed to send email";
                      exit; 
                 }         
                 else { 
                    echo "Your email has been sent."; 
                 }
         }

     } 
     else {
        $uniqname = $to;
        $to .= '@umich.edu';
           if ($between == "yes") {
             $message1 = "<br><a href=\"https://apps-prod.chem.lsa.umich.edu/chem-awards/faculty/check_award.php?uniqname=$uniqname&month=$month\">Click here for a list of awards</a>";
           $message = $message . $message1;
          }
echo $to;
echo "<br>";
$to = $from;
     $message .= "<br><br>";
     $message .= nl2br(str_replace('\\r\\n', "\r\n", $signature));
        $ok = send_mail($from, $to, $bcc, $subject, $message, FALSE, FALSE);
       if ($ok) {
         print "Your email has been sent.";
       }
       else {
             echo "failed to send email";
    //   header('Location: ERROR.php?error="recommendation.php, error 19 - failed to send email."');
       exit;
      }
     }
      exit;
  }

}
?>

<form method="post" action="send_email.php" enctype="multipart/form-data">
<?php
if ($fromaddress == "") { $fromaddress = $purifier->purify($_REQUEST['fromaddress']); }
if ($toaddress == "") { $toaddress = $purifier->purify($_REQUEST['toaddress']); }
if ($bccaddress == "") { $bccaddress = $purifier->purify($_REQUEST['bccaddress']); }
if ($subject == "") { $subject = $purifier->purify($_REQUEST['subject']); }
if ($message == "") { $message = $purifier->purify($_REQUEST['message']); }
if ($eligibility == "") { $eligibility = $purifier->purify($_REQUEST['eligibility']); }
if ($template == "") { $template = $purifier->purify($_REQUEST['template']); }
if ($template == "Empty template") { $template = $purifier->purify($_REQUEST['newname']); }
$newname = $purifier->purify($_REQUEST['newname']);
if ($newname !== "") { echo "ten";$template = $newname; }
if (!$between) { $between = $purifier->purify($_REQUEST['between']); }
?>
<div align="center"><h2>Send an email</h2><br></div>
<strong>From Address: <font color ="#FF0000" >*</font></strong> <input type="text" name="fromaddress" autofocus value="<?php echo $purifier->purify($fromaddress); ?>" size="15" maxlength="200"/>@umich.edu
<br><br>
<strong>To Address: <font color ="#FF0000" >*</font></strong> <input type="text" name="toaddress" id="toaddress" autofocus value="<?php echo $purifier->purify($toaddress); ?>" size="15" maxlength="200"/>@umich.edu
&nbsp;&nbsp;OR&nbsp;&nbsp;
<?php
$sql = "SELECT uniqname, Name FROM faculty ORDER BY name ASC";
$result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
        print("<select name='touniqname' id='touniqname' onchange='gettouniqname()'>");
        print("<option select value='error'> - choose name -</option>");

        WHILE ($applicant_name = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                print("<option value='$applicant_name[uniqname]'>$applicant_name[Name]</option>");
        }
echo "</select>";
echo "&nbsp;&nbsp;OR&nbsp;&nbsp;";
$sql1 = "SELECT `id`, `name` FROM `eligibility_list` ORDER BY name ASC";
$result1 = mysqli_query($conn, $sql1) or die("Query failed :".mysqli_error($conn));
//        print("<select name='eligibility' id='eligibility' onchange='geteligibility()'>");
        print("<select name='eligibility' id='eligibility'>");
        print("<option select value='error'> - who is eligible -</option>");

        WHILE ($eligibilitys = mysqli_fetch_array($result1, MYSQLI_BOTH))
        {
                echo "<option";
                if ($eligibilitys[id] == $eligibility)  { echo " selected"; }
                echo " value=$eligibilitys[id]>$eligibilitys[name]</option>";
        }
echo "</select>";
?>
<br><br><strong>Bcc Address: <input type="text" name="bccaddress" id="bccaddress" autofocus value="<?php echo $purifier->purify($bccaddress); ?>" size="15" maxlength="200"/>@umich.edu
<br><br>
<strong>Subject: <font color ="#FF0000" >*</font></strong> <input type="text" name="subject" autofocus value="<?php echo $purifier->purify($subject); ?>" size="35" maxlength="200"/>
<input type="checkbox" name="between" value="yes" <?php if ( $between == "yes" ) { echo " checked";  } ?>> Add 
Award Due Month? 
<?php

// one month

$month = $purifier->purify($_REQUEST['month']);
if ($month == "" ) { $month = "%";}
    $sqlm ="SELECT DISTINCT due_month FROM `awards_descr` order by month(str_to_date(left(due_month, 3),'%b'))";
      $resm = mysqli_query($conn, $sqlm) or die("There was an error getting min date: ".mysqli_error($conn));
echo "<select name='month'>";
echo "<option select value='%'> - pick all  -</option>";
while ($months = mysqli_fetch_array($resm, MYSQLI_BOTH)) {
           echo "<option";
           if ($months[due_month] == $month) { echo " selected"; }
           echo " value='$months[due_month]'>$months[due_month]</option>";
}
echo "</select>";

//
echo "<br><br><strong>Select a template for your message: </strong>";
$sql = "SELECT name, text FROM email_templates ORDER BY name ASC";

$result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
        print("<select name='template' id='template' onchange='gettemplate(this.value)'>");
        print("<option select value=''> - choose template name -</option>");

        WHILE ($template_name = mysqli_fetch_array($result, MYSQLI_BOTH))
        {
                echo "<option";
                if ($template_name[name] == $template)  { echo " selected"; }
                echo " value='$template_name[name]'>$template_name[name]</option>";
        }
echo "</select>";
echo "<div id='txtHint'></div>";
//echo "<input type='text' name='template_yes' id='template_yes' value='" . $template_yes . "'>"; 
//if (($template !=="") || ($template_yes == "yes")) {
if ($template !=="")  {
    $sql = "SELECT name, text FROM email_templates WHERE name = '$template'";
    $result = mysqli_query($conn, $sql) or die("Query failed :".mysqli_error($conn));
    $template = mysqli_fetch_array($result, MYSQLI_BOTH);

?>

<br><br>
<strong>Message: <font color ="#FF0000" >*</font></strong>
<br><textarea name="message" id="message" cols="74" rows="6" maxlength="2000"><?php echo $template[text]; ?></textarea>
<input type="submit" name="updatetemplate" value="Update the template" />

<?php
}  // getting message from template
$fromname = ldap_name($fromaddress);
$signature = $purifier->purify($_REQUEST['signature']); 
$signature = nl2br(str_replace('\\r\\n', "&#13;&#10;", $signature));
if ($signature == "") {
    $signature = "Thank you, &#13;&#10;" . $fromname;
}
?>

<p class="recomtextclass">Leave this empty: <input type="text" name="recomtext" /></p>
<br><strong>Name for a new template: </strong> <input type="text" name="newname" id="newname" autofocus value="" size="35" maxlength="200"/>
<input type="submit" name="addtemplate" value="Add as a template" />
<?php
if ($addupdate == "yes") {
 echo "<br><h5>(To select a new template after Update or Add a template, please run the script again.)</h5>";
}
?> 
<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><Br>
<strong>Signature: <font color ="#FF0000" >*</font></strong> 
<br><textarea name="signature" id="signature" cols="74" rows="3" maxlength="2000"><?php echo $signature; ?></textarea>
<br><br>
<input type="submit" name="submit" value="Submit Form" />

<br><div align="center"><img src="../images/linecalendarpopup500.jpg"></div><Br>

</form>
<script type="text/javascript">
function gettouniqname()
{
    var touniqname = document.getElementById('touniqname');
    var toaddress = document.getElementById('toaddress');
    var touniq = [touniqname.value];
//    console.log(touniq);
    toaddress.value = touniq;
//    console.log(toaddress.value);
}
</script>
<script type="text/javascript">
function gettemplate(str)  {
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
//        var template_yes = document.getElementById('template_yes');
//        template_yes.value = "yes";
        xmlhttp.open("GET","getTemplate.php?q="+str,true);
        xmlhttp.send();
    }
}
</script>
<script type="text/javascript">
function geteligibility()
{
    var eligibility = document.getElementById('eligibility');
    var whoiselig = document.getElementById('whoiselig');
    var eligibility1 = [eligibility.value];
    whoiselig.value = eligibility1;
}
</script>
</body>
</html>
