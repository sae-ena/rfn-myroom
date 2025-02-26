<?php
$to = "awalsujal99@gmail.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: kidssujal@gmail.com" . "\r\n" .
"CC: awalsujal99@gmail.com";

try {
    $sms = mail($to, $subject, $txt, $headers);
    var_dump($sms);  // Outputs success or failure
 // Log the details of the sent email for debugging
 file_put_contents('email_log.txt', "Email sent to $to with subject '$subject' at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
} catch(Exception $e) {
    echo $e->getMessage();
}
