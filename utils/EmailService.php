<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.example.com'; //  SMTP server
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'your-email@example.com'; //  email
        $this->mailer->Password = 'your-password'; //  email password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        
        // Sender
        $this->mailer->setFrom('noreply@auction-platform.com', 'Auction Platform');
    }
    
    public function sendConfirmationEmail($email, $name, $token) {
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
                        <h2>Welcome to Auction Platform!</h2>
                        <p>Hello ' . htmlspecialchars($name) . ',</p>
                        <p>Thank you for registering. Please confirm your email address by clicking the button below:</p>
                        <p><a href="' . $confirmUrl . '" class="button">Confirm Email</a></p>
                        <p>Or copy and paste this link into your browser:</p>
                        <p>' . $confirmUrl . '</p>
                        <p>This link will expire in 24 hours.</p>
                        <p>If you did not create an account, no further action is required.</p>
                        <p>Regards,<br>The Auction Platform Team</p>
                    </div>
                </body>
                </html>
            ';
            
            $this->mailer->AltBody = 'Hello ' . $name . ', 
                Thank you for registering. Please confirm your email address by clicking this link: ' . $confirmUrl . '
                This link will expire in 24 hours.
                If you did not create an account, no further action is required.
                Regards,
                The Auction Platform Team';
            
            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
} 