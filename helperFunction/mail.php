<?php
require_once __DIR__ . '/helpers.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
 * @param string $redirectUrl
 * @return array [subject, message]
 */
function getOtpEmailForUser($conn, $name, $otp, $redirectUrl = '') {
   $appName =  getBackendSettingValue('app-name'); // Ensure this is called to set up the environment
   $baseUrl = getBaseUrl(); // Get the base URL for the app
   $redirectUrl = $baseUrl . $redirectUrl; // Ensure redirect URL is absolute 
   $sql = "SELECT subject_title, user_message FROM email_templates WHERE slug = 'customer-signup-otp';";
    $res = $conn->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        $message = renderEmailTemplate($row['user_message'], ['user_name'=>$name, 'otp'=>$otp , 'app_name'=>$appName, 'redirect_url'=>$redirectUrl]);    
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
    $autoloadPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        // Log error and fail gracefully
        file_put_contents(__DIR__ . '/../email_error_log.txt', "[" . date('Y-m-d H:i:s') . "] autoload.php missing at $autoloadPath\n", FILE_APPEND);
        return false;
    }
    require_once $autoloadPath;
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
