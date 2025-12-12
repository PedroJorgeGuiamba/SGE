<?php
class CSRFProtection {
    private static $initialized = false;
    
    private static function initialize() {
        if (self::$initialized) {
            return;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            // ✅ Configurações seguras de sessão
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            session_start();
        }
        
        self::$initialized = true;
    }
    
    public static function generateToken() {
        self::initialize();
        
        // ✅ Gerar token único
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        error_log("DEBUG: CSRF Token Generated in session: " . session_id());
        error_log("DEBUG: CSRF Token Value: " . $token);
        
        return $token;
    }
    
    public static function validateToken($token) {
        self::initialize();
        
        error_log("DEBUG CSRF Validation - Session ID: ");
        error_log("DEBUG CSRF Validation - Input Token: ");
        error_log("DEBUG CSRF Validation - Session Token: ");
        
        // ✅ Verificações rigorosas
        if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
            throw new Exception('Token CSRF não encontrado na sessão. Session ID: ' . session_id());
        }
        
        if (empty($token)) {
            throw new Exception('Token CSRF não fornecido no formulário');
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            throw new Exception(
                'Token CSRF não corresponde. ' .
                'Sessão: ' . session_id() . ' | ' .
                'Esperado: ' . $_SESSION['csrf_token'] . ' | ' .
                'Recebido: ' . $token
            );
        }
        
        // ✅ Token válido - remover
        unset($_SESSION['csrf_token']);
        
        error_log("DEBUG: CSRF Token Validated Successfully");
        
        return true;
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    // ✅ Método para debug
    public static function debugSession() {
        self::initialize();
        return [
            'session_id' => session_id(),
            'csrf_token' => $_SESSION['csrf_token'] ?? 'NOT SET',
            'session_status' => session_status()
        ];
    }
}
?>