<?php
function send_mail($from, $to, $bcc, $subject, $message, $uploadfile, $filename, $html=FALSE, &$addresses=NULL)
{
require_once 'PHPMailerAutoload.php';
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
$mail->Host = "mail-relay.itd.umich.edu";
$mail->Port = 25;

$mail->SetFrom($from);
$mail->Subject = $subject;
$mail->MsgHTML($message);
$mail->AddAddress($to, '');
$mail->addBCC($bcc, '');

if ($uploadfile) {

$mail->addAttachment($uploadfile, $filename);
}

if($mail->send()) {
//  echo "Message sent!";
  return true;
} else {
  echo "Mailer Error: " . $mail->ErrorInfo;
  return false;
}

}
?>
