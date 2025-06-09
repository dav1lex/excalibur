<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['SMTP_HOST']; //  SMTP 
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['SMTP_USERNAME']; //  email
        $this->mailer->Password = $_ENV['SMTP_PASSWORD']; 
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['SMTP_PORT'];

        // Sender
        $this->mailer->setFrom(
            $_ENV['SMTP_FROM_EMAIL'], 
            $_ENV['SMTP_FROM_NAME']
        );
    }

    public function sendConfirmationEmail($email, $name, $token)
    {
        try {
            // Recipients
            $this->mailer->addAddress($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Confirm Your Email Address';

            $confirmUrl = BASE_URL . 'confirm-email?token=' . $token;

            $this->mailer->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Welcome to NanoBid!</h2>
                        <p>Hello ' . htmlspecialchars($name) . ',</p>
                        <p>Thank you for registering. Please confirm your email address by clicking the button below:</p>
                        <p><a href="' . $confirmUrl . '" class="button">Confirm Email</a></p>
                        <p>Or copy and paste this link into your browser:</p>
                        <p>' . $confirmUrl . '</p>
                        <p>This link will expire in 24 hours.</p>
                        <p>If you did not create an account, no further action is required.</p>
                        <p>Regards,<br>The NanoBid Team</p>
                    </div>
                </body>
                </html>
            ';

            $this->mailer->AltBody = 'Hello ' . $name . ', 
                Thank you for registering. Please confirm your email address by clicking this link: ' . $confirmUrl . '
                This link will expire in 24 hours.
                If you did not create an account, no further action is required.
                Regards,
                The NanoBid Team';

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    public function sendPasswordResetEmail($email, $name, $token)
    {
        try {
            // Recipients
            $this->mailer->addAddress($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Reset Your Password';

            $resetUrl = BASE_URL . 'reset-password?token=' . $token;

            $this->mailer->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Password Reset Request</h2>
                        <p>Hello ' . htmlspecialchars($name) . ',</p>
                        <p>You requested to reset your password. Click the button below to reset it:</p>
                        <p><a href="' . $resetUrl . '" class="button">Reset Password</a></p>
                        <p>Or copy and paste this link into your browser:</p>
                        <p>' . $resetUrl . '</p>
                        <p>This link will expire in 24 hours.</p>
                        <p>If you did not request a password reset, please ignore this email.</p>
                        <p>Regards,<br>The NanoBid Team</p>
                    </div>
                </body>
                </html>
            ';

            $this->mailer->AltBody = 'Hello ' . $name . ', 
                You requested to reset your password. Please click this link to reset it: ' . $resetUrl . '
                This link will expire in 24 hours.
                If you did not request a password reset, please ignore this email.
                Regards,
                The NanoBid Team';

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    public function sendOutbidNotification($email, $name, $lotTitle, $lotId)
    {
        try {
            // Clear all addresses and attachments for reuse
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->addAddress($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'You have been outbid on ' . $lotTitle;

            $lotUrl = BASE_URL . 'lots/view?id=' . $lotId;

            $this->mailer->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>You\'ve Been Outbid!</h2>
                        <p>Hello ' . htmlspecialchars($name) . ',</p>
                        <p>Someone has placed a higher bid on item <strong>' . htmlspecialchars($lotTitle) . '</strong> that you were bidding on.</p>
                        <p><a href="' . $lotUrl . '" class="button">View Item</a></p>
                        <p>Don\'t miss out! Place a new bid now to stay in the game.</p>
                        <p>Regards,<br>The NanoBid Team</p>
                    </div>
                </body>
                </html>
            ';

            $this->mailer->AltBody = 'Hello ' . $name . ', 
                Someone has placed a higher bid on item "' . $lotTitle . '" that you were bidding on.
                You can view the item and place a new bid here: ' . $lotUrl . '
                Don\'t miss out!
                Regards,
                The NanoBid Team';
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send outbid notification: " . $e->getMessage());
            return false;
        }
    }

    public function sendWinningNotification($email, $name, $lotTitle, $lotId, $winningAmount)
    {
        try {
            // Clear all addresses and attachments for reuse
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Recipients
            $this->mailer->addAddress($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Congratulations! You won ' . $lotTitle;

            $lotUrl = BASE_URL . 'lots/view?id=' . $lotId;

            $this->mailer->Body = '
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Congratulations!</h2>
                        <p>Hello ' . htmlspecialchars($name) . ',</p>
                        <p>You are the winning bidder for <strong>' . htmlspecialchars($lotTitle) . '</strong> with a bid of ' . htmlspecialchars($winningAmount) . '€.</p>
                        <p><a href="' . $lotUrl . '" class="button">View Item</a></p>
                        <p>Our team will be in contact shortly with payment and shipping details.</p>
                        <p>Thank you for participating in our auction!</p>
                        <p>Regards,<br>The NanoBid Team</p>
                    </div>
                </body>
                </html>
            ';

            $this->mailer->AltBody = 'Hello ' . $name . ', 
                Congratulations! You are the winning bidder for "' . $lotTitle . '" with a bid of ' . $winningAmount . '€.
                You can view the item details here: ' . $lotUrl . '
                Thank you for participating in our auction!
                Regards,
                The NanoBid Team';
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send winning notification: " . $e->getMessage());
            return false;
        }
    }
}