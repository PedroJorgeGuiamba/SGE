<?php
class SecurityLogger {
    public static function logSecurityEvent($event, $userId = null, $context = []) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

        $sanitizedContext = [];
        foreach ($context as $key => $value) {
            if (in_array($key, ['email', 'password', 'token'])) {
                $sanitizedContext[$key] = '***REDACTED***';
            } else {
                $sanitizedContext[$key] = $value;
            }
        }
        
        $logData = [
            'timestamp' => date('c'),
            'event' => $event,
            'user_id' => $userId,
            'ip_hash' => hash('sha256', $ip . 'salt'),
            'user_agent_hash' => hash('sha256', $userAgent),
            'context' => $sanitizedContext
        ];
        
        error_log("SECURITY: " . json_encode($logData));

        file_put_contents(
            __DIR__ . '/../logs/security.log',
            json_encode($logData) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}