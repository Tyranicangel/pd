<?php
// include "AWSSDKforPHP/sdk.class.php";

// function sendEmail($to, $subject, $message)
// {
//     $amazonSes = new AmazonSES(array(
//         'key' => 'AKIAJNUPZWIDUPZS7XQQ',
//         'secret' => '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',
//             'Date'=> 'Wed, 29 APR 2015 08:08:31 GMT'
        
//     ));
//   //  $amazonSes->verify_email_address('yogeshk96@gmail.com');
//    $response = $amazonSes->send_email('yogeshk96@gmail.com',
//         array('ToAddresses' => array($to)),
//         array(
//             'Subject.Data' => $subject,
//             'Body.Html.Data' => $message
//         )
//     );
//     if (!$response->isOK())
//     {
//         echo "<pre>";
//         print_r($response);
//         echo "</pre>";
//     }

// }

// sendEmail("yogeshk96@gmail.com", "test", "test mesg");
// echo "sdsdf";
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
$mail->addReplyTo('yogeshk96@gmail.com', 'First Last');
$mail->addAddress('yogeshk96@gmail.com', 'Pradyumna');
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
?>