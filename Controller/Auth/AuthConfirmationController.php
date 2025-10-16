<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");

session_start();

class AuthConfirmationController {
    private $conn;
    private $criptografia;
    private $error;

    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->error = '';
    }

    public function verificar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->error = "Método inválido.";
            return $this->error;
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "Token de segurança inválido. Recarregue a página e tente novamente.";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            return $this->error;
        }

        $codigo = trim($_POST['codigo'] ?? '');
        if (!preg_match('/^\d{6}$/', $codigo) || !ctype_digit($codigo)) {
            $this->error = "Código inválido. Deve ter exatamente 6 dígitos.";
            return $this->error;
        }

        // ✅ CORREÇÃO:
        if (!preg_match('/^\d{6}$/', $codigo) || !ctype_digit($codigo) || strlen($codigo) !== 6) {
            $this->error = "Código inválido.";
            return $this->error;
        }

        $user_id_criptografado = $_SESSION['pending_user_id'] ?? null;
        if (!$user_id_criptografado) {
            $this->error = "Sessão expirou. Faça login novamente.";
            return $this->error;
        }
        $user_id = $this->criptografia->descriptografar($user_id_criptografado);
        if (!$user_id) {
            $this->error = "Erro ao descriptografar ID do usuário. Faça login novamente.";
            return $this->error;
        }

        $user_id = intval($user_id);
        if ($user_id <= 0) {
            $this->error = "ID de usuário inválido.";
            return $this->error;
        }
        
        $sql = "SELECT * FROM user_otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->error = "Erro interno do sistema.";
            error_log("Erro prepare OTP: " . $this->conn->error);
            return $this->error;
        }

        $stmt->bind_param("i", $user_id);

        if (!$stmt->execute()) {
            $this->error = "Erro ao verificar código.";
            error_log("Erro execute OTP: " . $stmt->error);
            $stmt->close();
            return $this->error;
        }

        $otp = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$otp) {
            $this->error = "Nenhum código encontrado.";
            return $this->error;
        }
        if ($otp['is_used']) {
            $this->error = "Código já usado.";
            return $this->error;
        }
        if (strtotime($otp['expires_at']) < time()) {
            $this->error = "Código expirado.";
            return $this->error;
        }

        if (!hash_equals($otp['otp_code'], $codigo)) {
            $this->error = "Código inválido.";
            return $this->error;
        }

        $user = $this->getUserById($user_id);
        if (!$user) {
            $this->error = "Usuário não encontrado.";
            return $this->error;
        }

        $email_descriptografado = $this->criptografia->descriptografar($user['email']);
        if (!$email_descriptografado) {
            $this->error = "Erro ao descriptografar e-mail do usuário.";
            return $this->error;
        }

        $sqlUpdate = "UPDATE user_otps SET is_used = 1 WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);

        if (!$stmtUpdate) {
            $this->error = "Erro interno do sistema.";
            error_log("Erro prepare update OTP: " . $this->conn->error);
            return $this->error;
        }

        $stmtUpdate->bind_param("i", $otp['id']);
        if (!$stmtUpdate->execute()) {
            $this->error = "Erro ao processar código.";
            error_log("Erro execute update OTP: " . $stmtUpdate->error);
            $stmtUpdate->close();
            return $this->error;
        }
        $stmtUpdate->close();

        $_SESSION['email'] = $email_descriptografado;
        $_SESSION['role'] = $user['role'];
        $_SESSION['usuario_id'] = $user['id'];

        unset($_SESSION['pending_user_id']);

        $sessaoId = iniciarSessao($user['id']);
        registrarAtividade($sessaoId, "Login do User: " . $this->criptografia->criptografar($email_descriptografado) . " Realizado com sucesso", "LOGIN");

        session_regenerate_id(true);

        $role = strtolower($user['role']);
        switch ($role) {
            case 'formando':
                header("Location: /estagio/View/Formando/portalDeEstudante.php");
                break;
            case 'supervisor':
                header("Location: /estagio/View/Supervisor/portalDoSupervisor.php");
                break;
            case 'formador':
                header("Location: /estagio/View/Formador/portalDoFormador.php");
                break;
            case 'admin':
                header("Location: /estagio/View/Admin/portalDoAdmin.php");
                break;
            case 'seguranca':
                header("Location: /estagio/View/Seguranca/portalDoSeguranca.php");
                break;
            default:
                registrarAtividade($sessaoId, "Tentativa de redirecionamento com role inválida: {$role}", "ERROR");
                $this->error = "Tipo de usuário desconhecido.";
                return $this->error;
        }
        exit();
    }

    public function getError() {
        return $this->error;
    }

    private function getUserById($user_id) {
        $sql = "SELECT id, email, role FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("Erro prepare getUserById: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $user_id);
        
        if (!$stmt->execute()) {
            error_log("Erro execute getUserById: " . $stmt->error);
            $stmt->close();
            return null;
        }
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        return $user;
    }
}

$confirm = new AuthConfirmationController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $confirm->verificar();
}
?>