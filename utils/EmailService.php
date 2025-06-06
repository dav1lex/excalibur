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
      $this->mailer->Host = 'smtp.titancode.pl'; //  SMTP server
      $this->mailer->SMTPAuth = true;
      $this->mailer->Username = 'ougur'; //  email
      $this->mailer->Password = 'Davilex12345.'; //  email password
      $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $this->mailer->Port = 587;
      
      // Sender
      $this->mailer->setFrom('info@titancode.pl', 'NanoBid');
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
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}"); //Remove in production
            return false;
        }
    }

    public function sendPasswordResetEmail($email, $name, $token) {
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
            error_log("Email could not be sent. Mailer Error: {$this->mailer->ErrorInfo}"); //Remove in production
            return false;
        }
    }
} 