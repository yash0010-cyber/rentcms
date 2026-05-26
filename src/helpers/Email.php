<?php
/**
 * Email Helper Class
 * Handles all email sending via PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private static $mail = null;

    /**
     * Initialize PHPMailer
     */
    private static function initMail() {
        if (self::$mail === null) {
            self::$mail = new PHPMailer(true);

            try {
                self::$mail->isSMTP();
                self::$mail->Host = MAIL_HOST;
                self::$mail->Port = MAIL_PORT;
                self::$mail->SMTPAuth = MAIL_USE_AUTH;
                self::$mail->SMTPSecure = MAIL_USE_TLS ? PHPMailer::ENCRYPTION_STARTTLS : '';
                self::$mail->Username = MAIL_USERNAME;
                self::$mail->Password = MAIL_PASSWORD;
                self::$mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
                self::$mail->CharSet = 'UTF-8';
            } catch (Exception $e) {
                error_log("PHPMailer initialization error: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Send email verification
     */
    public static function sendVerification($email, $name, $token) {
        if (!self::initMail()) return false;

        try {
            $verificationLink = APP_URL . '/verify-email?token=' . $token;

            self::$mail->clearAddresses();
            self::$mail->addAddress($email, $name);
            self::$mail->Subject = 'Email Verification - ' . APP_NAME;

            $htmlBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { 
                            display: inline-block; 
                            padding: 10px 20px; 
                            background-color: #007bff; 
                            color: white; 
                            text-decoration: none; 
                            border-radius: 5px;
                            margin: 10px 0;
                        }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>" . APP_NAME . "</h1>
                        </div>
                        <div class='content'>
                            <p>Hello $name,</p>
                            <p>Thank you for registering with " . APP_NAME . "!</p>
                            <p>Please click the button below to verify your email address:</p>
                            <p>
                                <a href='$verificationLink' class='button'>Verify Email Address</a>
                            </p>
                            <p>Or copy and paste this link in your browser:</p>
                            <p><a href='$verificationLink'>$verificationLink</a></p>
                            <p>This link will expire in 24 hours.</p>
                            <p>If you did not create this account, please ignore this email.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            self::$mail->isHTML(true);
            self::$mail->Body = $htmlBody;
            self::$mail->AltBody = strip_tags($htmlBody);

            if (self::$mail->send()) {
                self::logEmail($email, 'Email Verification - ' . APP_NAME, 'verification', 'sent');
                return true;
            } else {
                self::logEmail($email, 'Email Verification - ' . APP_NAME, 'verification', 'failed', self::$mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordReset($email, $name, $token) {
        if (!self::initMail()) return false;

        try {
            $resetLink = APP_URL . '/reset-password?token=' . $token;

            self::$mail->clearAddresses();
            self::$mail->addAddress($email, $name);
            self::$mail->Subject = 'Password Reset Request - ' . APP_NAME;

            $htmlBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .button { 
                            display: inline-block; 
                            padding: 10px 20px; 
                            background-color: #dc3545; 
                            color: white; 
                            text-decoration: none; 
                            border-radius: 5px;
                            margin: 10px 0;
                        }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                        .warning { color: #dc3545; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Password Reset Request</h1>
                        </div>
                        <div class='content'>
                            <p>Hello $name,</p>
                            <p>We received a request to reset your password. Click the button below to proceed:</p>
                            <p>
                                <a href='$resetLink' class='button'>Reset Password</a>
                            </p>
                            <p>Or copy and paste this link in your browser:</p>
                            <p><a href='$resetLink'>$resetLink</a></p>
                            <p class='warning'>This link will expire in 1 hour.</p>
                            <p>If you did not request this password reset, please ignore this email or contact support.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            self::$mail->isHTML(true);
            self::$mail->Body = $htmlBody;
            self::$mail->AltBody = strip_tags($htmlBody);

            if (self::$mail->send()) {
                self::logEmail($email, 'Password Reset Request - ' . APP_NAME, 'password_reset', 'sent');
                return true;
            } else {
                self::logEmail($email, 'Password Reset Request - ' . APP_NAME, 'password_reset', 'failed', self::$mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking confirmation email
     */
    public static function sendBookingConfirmation($email, $name, $propertyName, $checkInDate, $checkOutDate, $totalPrice) {
        if (!self::initMail()) return false;

        try {
            self::$mail->clearAddresses();
            self::$mail->addAddress($email, $name);
            self::$mail->Subject = 'Booking Confirmation - ' . APP_NAME;

            $checkInFormatted = date('F j, Y', strtotime($checkInDate));
            $checkOutFormatted = date('F j, Y', strtotime($checkOutDate));

            $htmlBody = "
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #28a745; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .booking-details { background-color: white; border: 1px solid #ddd; padding: 15px; margin: 15px 0; }
                        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
                        .detail-label { font-weight: bold; }
                        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>✓ Booking Confirmed!</h1>
                        </div>
                        <div class='content'>
                            <p>Hello $name,</p>
                            <p>Your booking has been confirmed. Here are the details:</p>
                            <div class='booking-details'>
                                <div class='detail-row'>
                                    <span class='detail-label'>Property:</span>
                                    <span>$propertyName</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Check-in:</span>
                                    <span>$checkInFormatted</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Check-out:</span>
                                    <span>$checkOutFormatted</span>
                                </div>
                                <div class='detail-row'>
                                    <span class='detail-label'>Total Price:</span>
                                    <span>\$$totalPrice</span>
                                </div>
                            </div>
                            <p>We look forward to hosting you! If you have any questions, please don't hesitate to contact us.</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            self::$mail->isHTML(true);
            self::$mail->Body = $htmlBody;
            self::$mail->AltBody = strip_tags($htmlBody);

            if (self::$mail->send()) {
                self::logEmail($email, 'Booking Confirmation - ' . APP_NAME, 'booking_confirmation', 'sent');
                return true;
            } else {
                self::logEmail($email, 'Booking Confirmation - ' . APP_NAME, 'booking_confirmation', 'failed', self::$mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send generic email
     */
    public static function send($to, $subject, $htmlBody, $altBody = '') {
        if (!self::initMail()) return false;

        try {
            self::$mail->clearAddresses();
            self::$mail->addAddress($to);
            self::$mail->Subject = $subject;
            self::$mail->isHTML(true);
            self::$mail->Body = $htmlBody;
            self::$mail->AltBody = $altBody ?: strip_tags($htmlBody);

            if (self::$mail->send()) {
                self::logEmail($to, $subject, 'generic', 'sent');
                return true;
            } else {
                self::logEmail($to, $subject, 'generic', 'failed', self::$mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Log email to database
     */
    private static function logEmail($email, $subject, $type, $status, $error = '') {
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                INSERT INTO email_logs (recipient_email, subject, email_type, status, error_message)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$email, $subject, $type, $status, $error]);
        } catch (Exception $e) {
            error_log("Email log error: " . $e->getMessage());
        }
    }
}

?>
