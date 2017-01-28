<?php

function sendEmail($to, $subject, $message)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"http://54.169.56.136/ses.php");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
                "to=$to&subject=$subject&message=$message");

    // in real life you should use something like:
    // curl_setopt($ch, CURLOPT_POSTFIELDS, 
    //          http_build_query(array('postvar1' => 'value1')));

    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);

}

?>
