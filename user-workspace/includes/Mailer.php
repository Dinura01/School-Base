<?php
class Mailer {
    private static $instance = null;
    private $mailer;
    private $logger;
    private $templates = [];
    private $defaultTemplate = 'default';

    /**
     * Constructor
     */
    private function __construct() {
        $this->logger = Logger::getInstance();
        $this->loadTemplates();
    }

    /**
     * Get Mailer instance (Singleton)
     */
    public static function getInstance(): Mailer {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Send email
     */
    public function send(string $to, string $subject, string $template, array $data = [], array $options = []): bool {
        try {
            // Prepare email content
            $content = $this->prepareContent($template, $data);
            
            // Set headers
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . ($options['from'] ?? MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>'),
                'Reply-To: ' . ($options['reply_to'] ?? MAIL_FROM_ADDRESS),
                'X-Mailer: PHP/' . phpversion()
            ];

            // Add CC
            if (!empty($options['cc'])) {
                $headers[] = 'Cc: ' . $options['cc'];
            }

            // Add BCC
            if (!empty($options['bcc'])) {
                $headers[] = 'Bcc: ' . $options['bcc'];
            }

            // Send email
            $result = mail($to, $subject, $content, implode("\r\n", $headers));

            // Log result
            if ($result) {
                $this->logger->info("Email sent successfully to {$to}", [
                    'subject' => $subject,
                    'template' => $template
                ]);
            } else {
                $this->logger->error("Failed to send email to {$to}", [
                    'subject' => $subject,
                    'template' => $template,
                    'error' => error_get_last()
                ]);
            }

            return $result;
        } catch (Exception $e) {
            $this->logger->error("Email error: " . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'template' => $template
            ]);
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(string $to, string $token): bool {
        $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
        
        return $this->send($to, 'Password Reset Request', 'password_reset', [
            'reset_link' => $resetLink,
            'expires_in' => '1 hour'
        ]);
    }

    /**
     * Send welcome email
     */
    public function sendWelcome(string $to, string $name): bool {
        return $this->send($to, 'Welcome to ' . SCHOOL_NAME, 'welcome', [
            'name' => $name,
            'school_name' => SCHOOL_NAME
        ]);
    }

    /**
     * Send notification
     */
    public function sendNotification(string $to, string $subject, string $message): bool {
        return $this->send($to, $subject, 'notification', [
            'message' => $message
        ]);
    }

    /**
     * Send bulk emails
     */
    public function sendBulk(array $recipients, string $subject, string $template, array $data = []): array {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $to = is_array($recipient) ? $recipient['email'] : $recipient;
            $recipientData = is_array($recipient) ? array_merge($data, $recipient) : $data;
            
            $results[$to] = $this->send($to, $subject, $template, $recipientData);
        }
        
        return $results;
    }

    /**
     * Prepare email content
     */
    private function prepareContent(string $template, array $data): string {
        // Get template content
        $templateContent = $this->getTemplate($template);
        
        // Replace placeholders
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{{' . $key . '}}', $value, $templateContent);
        }
        
        // Replace any remaining placeholders
        $templateContent = preg_replace('/\{\{.*?\}\}/', '', $templateContent);
        
        return $templateContent;
    }

    /**
     * Get email template
     */
    private function getTemplate(string $template): string {
        if (isset($this->templates[$template])) {
            return $this->templates[$template];
        }
        
        return $this->templates[$this->defaultTemplate];
    }

    /**
     * Load email templates
     */
    private function loadTemplates(): void {
        // Default template
        $this->templates['default'] = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{subject}}</h2>
        </div>
        {{content}}
        <div class="footer">
            <p>&copy; {{year}} {$_SERVER['HTTP_HOST']}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Welcome template
        $this->templates['welcome'] = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{school_name}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .welcome-message { margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome to {{school_name}}</h2>
        </div>
        <div class="welcome-message">
            <p>Dear {{name}},</p>
            <p>Welcome to {{school_name}}! We're excited to have you join our community.</p>
            <p>You can now log in to your account and start using our services.</p>
        </div>
        <div class="footer">
            <p>&copy; {{year}} {{school_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Password reset template
        $this->templates['password_reset'] = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Request</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .reset-button { 
            display: inline-block; 
            padding: 10px 20px; 
            background-color: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
        }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        <p>You have requested to reset your password. Click the button below to proceed:</p>
        <p style="text-align: center;">
            <a href="{{reset_link}}" class="reset-button">Reset Password</a>
        </p>
        <p>This link will expire in {{expires_in}}.</p>
        <p>If you didn't request this password reset, please ignore this email.</p>
        <div class="footer">
            <p>&copy; {{year}} {$_SERVER['HTTP_HOST']}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Notification template
        $this->templates['notification'] = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .message { margin-bottom: 30px; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{subject}}</h2>
        </div>
        <div class="message">
            {{message}}
        </div>
        <div class="footer">
            <p>&copy; {{year}} {$_SERVER['HTTP_HOST']}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;

        // Add current year to all templates
        foreach ($this->templates as &$template) {
            $template = str_replace('{{year}}', date('Y'), $template);
        }
    }
}
