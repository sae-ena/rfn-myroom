<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

/**
 * Render an email template with variables (e.g., {{name}}, {{otp}})
 * @param string $template The template string
 * @param array $vars Associative array of variables
 * @return string Rendered template
 */
function renderEmailTemplate($template, $vars) {
    foreach ($vars as $key => $value) {
        $template = str_replace('{{'.$key.'}}', htmlspecialchars($value), $template);
    }
    return $template;
}

/**
 * Fetch the active OTP email template and render it for a user
 * @param mysqli $conn
 * @param string $name
 * @param string $otp
 * @param int $expires (minutes)
 * @return array [subject, message]
 */
function getOtpEmailForUser($conn, $name, $otp, $expires) {
    $sql = "SELECT subject_title, user_message FROM email_templates WHERE slug = 'customer-signup-otp';";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $message = renderEmailTemplate($row['user_message'], ['user_name'=>$name, 'otp'=>$otp]);    
        return [$row['subject_title'], $message];
    }
    return ['', '']; // Return empty if no template found
    
}


/**
 * Send an email using PHPMailer (SMTP)
 * @param string $to Recipient email
 * @param string $subject
 * @param string $body HTML message
 * @return bool
 */
function sendMailPHPMailer($to, $subject, $body) {
    require_once __DIR__ . '/../../vendor/autoload.php'; // Adjust path as needed
    $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp-relay.brevo.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '881c47001@smtp-brevo.com';
            $mail->Password   = 'r8yO2NtWa3X4K1qR';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('kidssujal@gmail.com', 'Casabo Room Finder');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true; // Indicate success
        } catch (Exception $e) {
            // Log the error
            file_put_contents('email_error_log.txt', "Email to $to failed: " . $mail->ErrorInfo . "\n", FILE_APPEND);
            return false; // Indicate failure
        }
}
