<?php
date_default_timezone_set('Asia/Kolkata');
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
// var_dump($mail);
$mail->isSMTP();
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPSecure = 'tls';
$mail->SMTPAuth = true;
$mail->Username = "drigger.acp@gmail.com";
$mail->Password = "noobtard123";
$mail->setFrom('drigger.acp@gmail.com', 'First Last');
$mail->addReplyTo('drigger.acp@gmail.com', 'First Last');
$mail->addAddress('drigger.acp@gmail.com', 'Pradyumna');
$mail->Subject = 'PHPMailer GMail SMTP test';

// // //Read an HTML message body from an external file, convert referenced images to embedded,
// // //convert HTML into a basic plain-text alternative body
// // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));

// //Replace the plain text body with one created manually
$mail->Body="Test Body";
$mail->AltBody = 'This is a plain-text message body';

// //send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}