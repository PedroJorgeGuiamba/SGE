<?php
require_once __DIR__ . '/../Helpers/Sessao.php';
require_once __DIR__ . '/../Conexao/conector.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';
SecurityHeaders::setFull();

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

class AuthMiddleware
{
    public function verificarAutenticacao()
    {
        if (!isset($_COOKIE["token_sessao"], $_SESSION['usuario_id'], $_SESSION['email'], $_SESSION['role'])) {
            $this->redirectToLogin();
        }


        $token = $_COOKIE["token_sessao"];
        $sessao = selecionarSessao($token, 1);

        if (!$sessao) {
            return false;
        }

        $usuario_id = intval($_SESSION['usuario_id']);
        $email = filter_var($_SESSION['email'], FILTER_SANITIZE_EMAIL);
        $role = $_SESSION['role'];

        if ($usuario_id <= 0 || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($role)) {
            $this->redirectToLogin();
        }

        if (isset($_SESSION['token'])) {
            $token = $_SESSION['token'];
            
            // Validar formato do token
            if (strlen($token) !== 64 || !ctype_xdigit($token)) {
                $this->redirectToLogin();
            }
            
            $sessao = selecionarSessao($token, 1);
            if (!$sessao) {
                $this->redirectToLogin();
            }
        }

        return true;
    }

    private function redirectToLogin() {
        // Limpar sessão corrompida
        if (isset($_SESSION)) {
            session_unset();
            session_destroy();
        }
        
        header("Location: /estagio/View/Login.php");
        exit();
    }


}
?>