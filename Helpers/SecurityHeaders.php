<?php
class SecurityHeaders {
    
    public static function setBasic() {
        // Headers básicos para todas as páginas
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
    
    public static function setFull() {
        // Headers completos (para páginas sensíveis)
        self::setBasic();
        header("Content-Security-Policy: " . self::getCSP());
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
    }
    
    public static function setLogin() {
        // Headers específicos para páginas de login
        self::setBasic();
        header("Content-Security-Policy: " . self::getLoginCSP());
    }

    private static function getCSP() {
        return  "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.datatables.net; " .
                "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://getbootstrap.com https://cdnjs.cloudflare.com https://cdn.datatables.net; " .
                "img-src 'self' data: https:; " .
                "font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
                "connect-src 'self' http://localhost; " .
                "frame-ancestors 'none'; " .
                "base-uri 'self'; " .
                "form-action 'self'";
    }
    
    private static function getLoginCSP() {
        return "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com; " .
                "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
                "img-src 'self' data: https:; " .
                "font-src 'self' https://cdn.jsdelivr.net; " .
                "connect-src 'self' http://localhost; " .
                "frame-ancestors 'none'";
    }
    
    // private static function getCSP() {
    //     // Content Security Policy restritiva
    //     return "default-src 'self'; " .
    //             "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
    //             "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
    //             "img-src 'self' data: https:; " .
    //             "font-src 'self' https://cdn.jsdelivr.net; " .
    //             "connect-src 'self'; " .
    //             "frame-ancestors 'none'; " .
    //             "base-uri 'self'; " .
    //             "form-action 'self'";
    // }
    
    // private static function getLoginCSP() {
    //     // CSP mais flexível para login (permite CDNs)
    //     return "default-src 'self'; " .
    //             "script-src 'self' https://cdn.jsdelivr.net; " .
    //             "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
    //             "img-src 'self' data: https:; " .
    //             "font-src 'self' https://cdn.jsdelivr.net; " .
    //             "connect-src 'self'; " .
    //             "frame-ancestors 'none'";
    // }
}
?>