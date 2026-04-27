<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer = null;
    private $enabled = false;

    public function __construct()
    {
        // SMTP not configured — skip silently
        if (empty($_ENV['SMTP_HOST'])) {
            return;
        }

        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['SMTP_USERNAME'] ?? '';
        $this->mailer->Password = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['SMTP_PORT'] ?? 587;

        $fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? '';
        $fromName = $_ENV['SMTP_FROM_NAME'] ?? '';

        if (!empty($fromEmail)) {
            $this->mailer->setFrom($fromEmail, $fromName);
            $this->enabled = true;
        }
    }

    private function sendOrFail($email, $name, $subject, $htmlBody, $textBody)
    {
        if (!$this->enabled) {
            return false;
        }
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            $this->mailer->addAddress($email, $name);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody;
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    public function sendConfirmationEmail($email, $name, $token)
    {
        $confirmUrl = BASE_URL . 'confirm-email?token=' . $token;

        $html = '
            <html><head><style>
                body{font-family:Arial,sans-serif;line-height:1.6}
                .container{max-width:600px;margin:0 auto;padding:20px}
                .button{display:inline-block;padding:10px 20px;background-color:#007bff;color:#fff;text-decoration:none;border-radius:5px}
            </style></head><body>
            <div class="container">
                <h2>Welcome to NanoBid!</h2>
                <p>Hello ' . htmlspecialchars($name) . ',</p>
                <p>Thank you for registering. Please confirm your email address by clicking the button below:</p>
                <p><a href="' . $confirmUrl . '" class="button">Confirm Email</a></p>
                <p>Or copy and paste: ' . $confirmUrl . '</p>
                <p>This link will expire in 24 hours.</p>
                <p>Regards,<br>The NanoBid Team</p>
            </div></body></html>';

        $text = "Hello $name,\n\nThank you for registering. Confirm your email: $confirmUrl\n\nRegards,\nThe NanoBid Team";

        return $this->sendOrFail($email, $name, 'Confirm Your Email Address', $html, $text);
    }

    public function sendPasswordResetEmail($email, $name, $token)
    {
        $resetUrl = BASE_URL . 'reset-password?token=' . $token;

        $html = '
            <html><head><style>
                body{font-family:Arial,sans-serif;line-height:1.6}
                .container{max-width:600px;margin:0 auto;padding:20px}
                .button{display:inline-block;padding:10px 20px;background-color:#007bff;color:#fff;text-decoration:none;border-radius:5px}
            </style></head><body>
            <div class="container">
                <h2>Password Reset Request</h2>
                <p>Hello ' . htmlspecialchars($name) . ',</p>
                <p>Click below to reset your password:</p>
                <p><a href="' . $resetUrl . '" class="button">Reset Password</a></p>
                <p>Or copy and paste: ' . $resetUrl . '</p>
                <p>This link will expire in 24 hours.</p>
                <p>Regards,<br>The NanoBid Team</p>
            </div></body></html>';

        $text = "Hello $name,\n\nReset your password: $resetUrl\n\nRegards,\nThe NanoBid Team";

        return $this->sendOrFail($email, $name, 'Reset Your Password', $html, $text);
    }

    public function sendOutbidNotification($email, $name, $lotTitle, $lotId)
    {
        $lotUrl = BASE_URL . 'lots/view?id=' . $lotId;

        $html = '
            <html><head><style>
                body{font-family:Arial,sans-serif;line-height:1.6}
                .container{max-width:600px;margin:0 auto;padding:20px}
                .button{display:inline-block;padding:10px 20px;background-color:#007bff;color:#fff;text-decoration:none;border-radius:5px}
            </style></head><body>
            <div class="container">
                <h2>You\'ve Been Outbid!</h2>
                <p>Hello ' . htmlspecialchars($name) . ',</p>
                <p>Someone placed a higher bid on <strong>' . htmlspecialchars($lotTitle) . '</strong>.</p>
                <p><a href="' . $lotUrl . '" class="button">View Item</a></p>
                <p>Place a new bid to stay in the game!</p>
                <p>Regards,<br>The NanoBid Team</p>
            </div></body></html>';

        $text = "Hello $name,\n\nSomeone outbid you on \"$lotTitle\". View: $lotUrl\n\nRegards,\nThe NanoBid Team";

        return $this->sendOrFail($email, $name, 'You have been outbid on ' . $lotTitle, $html, $text);
    }

    public function sendWinningNotification($email, $name, $lotTitle, $lotId, $winningAmount)
    {
        $lotUrl = BASE_URL . 'lots/view?id=' . $lotId;

        $html = '
            <html><head><style>
                body{font-family:Arial,sans-serif;line-height:1.6}
                .container{max-width:600px;margin:0 auto;padding:20px}
                .button{display:inline-block;padding:10px 20px;background-color:#007bff;color:#fff;text-decoration:none;border-radius:5px}
            </style></head><body>
            <div class="container">
                <h2>Congratulations!</h2>
                <p>Hello ' . htmlspecialchars($name) . ',</p>
                <p>You won <strong>' . htmlspecialchars($lotTitle) . '</strong> with bid of ' . htmlspecialchars($winningAmount) . '€.</p>
                <p><a href="' . $lotUrl . '" class="button">View Item</a></p>
                <p>We\'ll contact you with payment/shipping details.</p>
                <p>Regards,<br>The NanoBid Team</p>
            </div></body></html>';

        $text = "Hello $name,\n\nCongratulations! You won \"$lotTitle\" with $winningAmount€. View: $lotUrl\n\nRegards,\nThe NanoBid Team";

        return $this->sendOrFail($email, $name, 'Congratulations! You won ' . $lotTitle, $html, $text);
    }
}
