<?php
class Utility {
    /**
     * Format date to specified format
     */
    public static function formatDate($date, string $format = 'Y-m-d'): string {
        if (!$date) return '';
        return date($format, is_numeric($date) ? $date : strtotime($date));
    }

    /**
     * Format datetime
     */
    public static function formatDateTime($date): string {
        return self::formatDate($date, 'Y-m-d H:i:s');
    }

    /**
     * Format time
     */
    public static function formatTime($date): string {
        return self::formatDate($date, 'H:i:s');
    }

    /**
     * Format currency
     */
    public static function formatCurrency(float $amount, string $currency = 'USD'): string {
        return number_format($amount, 2) . ' ' . $currency;
    }

    /**
     * Format file size
     */
    public static function formatFileSize(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Generate random string
     */
    public static function generateRandomString(int $length = 10): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Sanitize string
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Slugify string
     */
    public static function slugify(string $text): string {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);
        return $text;
    }

    /**
     * Get file extension
     */
    public static function getFileExtension(string $filename): string {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Check if file is image
     */
    public static function isImage(string $filename): bool {
        $extension = self::getFileExtension($filename);
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Generate thumbnail
     */
    public static function generateThumbnail(string $source, string $destination, int $width, int $height): bool {
        list($sourceWidth, $sourceHeight, $type) = getimagesize($source);
        
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($source);
                break;
            default:
                return false;
        }

        $thumbnail = imagecreatetruecolor($width, $height);
        imagecopyresampled(
            $thumbnail, $sourceImage,
            0, 0, 0, 0,
            $width, $height,
            $sourceWidth, $sourceHeight
        );

        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagejpeg($thumbnail, $destination, 90);
            case IMAGETYPE_PNG:
                return imagepng($thumbnail, $destination, 9);
            case IMAGETYPE_GIF:
                return imagegif($thumbnail, $destination);
        }

        return false;
    }

    /**
     * Get gravatar URL
     */
    public static function getGravatar(string $email, int $size = 80): string {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp";
    }

    /**
     * Calculate age from date
     */
    public static function calculateAge($birthDate): int {
        return date_diff(date_create($birthDate), date_create('today'))->y;
    }

    /**
     * Get time ago
     */
    public static function timeAgo($datetime): string {
        $time = is_numeric($datetime) ? $datetime : strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) {
            return 'Just now';
        }
        
        $intervals = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute'
        ];
        
        foreach ($intervals as $seconds => $label) {
            $interval = floor($diff / $seconds);
            if ($interval >= 1) {
                return $interval . ' ' . $label . ($interval > 1 ? 's' : '') . ' ago';
            }
        }
    }

    /**
     * Truncate text
     */
    public static function truncate(string $text, int $length = 100, string $ending = '...'): string {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length - strlen($ending)) . $ending;
    }

    /**
     * Convert bytes to human readable size
     */
    public static function bytesToHuman(int $bytes): string {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Generate CSV
     */
    public static function generateCSV(array $data, string $filename): void {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
        }
        
        // Add data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        
        fclose($output);
    }

    /**
     * Send email
     */
    public static function sendEmail(string $to, string $subject, string $message, array $options = []): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . ($options['from'] ?? MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>')
        ];

        if (!empty($options['cc'])) {
            $headers[] = 'Cc: ' . $options['cc'];
        }

        if (!empty($options['bcc'])) {
            $headers[] = 'Bcc: ' . $options['bcc'];
        }

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Generate PDF
     */
    public static function generatePDF(string $html, string $filename): void {
        require_once BASE_PATH . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 20,
            'margin_top' => 20,
            'margin_bottom' => 20
        ]);
        
        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, 'D');
    }

    /**
     * Clean filename
     */
    public static function cleanFilename(string $filename): string {
        // Remove any path information
        $filename = basename($filename);
        
        // Replace spaces
        $filename = str_replace(' ', '-', $filename);
        
        // Remove any non-alphanumeric characters except dots and dashes
        $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '', $filename);
        
        // Remove multiple dashes
        $filename = preg_replace('/-+/', '-', $filename);
        
        return $filename;
    }

    /**
     * Get mime type
     */
    public static function getMimeType(string $filename): string {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimeType;
    }

    /**
     * Validate date
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): bool {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}
