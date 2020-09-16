<?php
function send_mail_with_file($from, $to, $subject, $message, $html=FALSE, &$addresses=NULL, $attfile)
{
require 'PHPMailerAutoload.php';
global $shop;
$mail = new PHPMailer();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
//$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
//$mail->Debugoutput = 'html';

$mail->IsSMTP();
$mail->SMTPAuth = false;
//$mail->Host = "edispatch.lsa.umich.edu";
$mail->Host = "mail-relay.itd.umich.edu";
$mail->Port = 25;

 

$mail->SetFrom($from, $shop);
$mail->Subject = $subject;
$mail->MsgHTML($message);
$mail->AddAddress($to, '');
    $uploadfile = tempnam(sys_get_temp_dir(), sha1($attfile['name']));
    if (move_uploaded_file($attfile['tmp_name'], $uploadfile)) {
        $name = $attfile['name'];

$mail->addAttachment($uploadfile, $name);


if($mail->send()) {
//  echo "Message sent!";
  return true;
} else {
  echo "Mailer Error: " . $mail->ErrorInfo;
  return false;
}
} else {
        echo 'Failed to move file to ' . $uploadfile;
        return false;
    }

}
?>
