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

if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

class AuthConfirmationController
{
    private $conn;
    private Criptografia $criptografia;
    private ?string $error;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->error = '';
    }

    public function verificar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: /estagio/validar?erros=" . urldecode("Método inválido."));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/validar?erros=" . urldecode("Token de segurança inválido. Recarregue a página e tente novamente."));
            exit();
        }

        $codigo = trim($_POST['codigo'] ?? '');
        if (!preg_match('/^\d{6}$/', $codigo) || !ctype_digit($codigo) || strlen($codigo) !== 6) {
            header("Location: /estagio/validar?erros=" . urldecode("Código inválido. Deve ter exatamente 6 dígitos."));
            exit();
        }

        $user_id_criptografado = $_SESSION['pending_user_id'] ?? null;
        if (!$user_id_criptografado) {
            header("Location: /estagio/validar?erros=" . urldecode("Sessão expirou. Faça login novamente."));
            exit();
        }
        $user_id = $this->criptografia->descriptografar($user_id_criptografado);
        if (!$user_id) {
            header("Location: /estagio/validar?erros=" . urldecode("Erro ao descriptografar ID do usuário. Faça login novamente."));
            exit();
        }

        $user_id = intval($user_id);
        if ($user_id <= 0) {
            header("Location: /estagio/validar?erros=" . urldecode("ID de usuário inválido."));
            exit();
        }

        $this->conn->begin_transaction();

        $sql = "SELECT * FROM user_otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Erro interno"));
            exit();
        }

        $stmt->bind_param("i", $user_id);

        if (!$stmt->execute()) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Erro ao verificar código."));
            exit();
        }

        $otp = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$otp) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Nenhum código encontrado."));
            exit();
        }
        if ($otp['is_used']) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Código já usado."));
            exit();
        }
        if (strtotime($otp['expires_at']) < time()) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Código expirado."));
            exit();
        }

        if (!hash_equals($otp['otp_code'], $codigo)) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Código inválido"));
            exit();
        }

        $user = $this->getUserById($user_id);
        if (!$user) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Usuário não encontrado."));
            exit();
        }

        $email_descriptografado = $this->criptografia->descriptografar($user['email']);
        if (!$email_descriptografado) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Erro ao descriptografar e-mail do usuário."));
            exit();
        }

        $sqlUpdate = "UPDATE user_otps SET is_used = 1 WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);

        if (!$stmtUpdate) {
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Erro ao Actualizar Estado do OTP."));
            exit();
        }

        $stmtUpdate->bind_param("i", $otp['id']);
        if (!$stmtUpdate->execute()) {
            $stmtUpdate->close();
            $this->conn->rollback();
            header("Location: /estagio/validar?erros=" . urldecode("Erro ao processar código."));
            exit();
        }

        $this->conn->commit();
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
                header("Location: /estagio/login/confirmar-user");
                break;
            case 'supervisor':
                header("Location: /estagio/supervisor");
                break;
            case 'formador':
                header("Location: /estagio/formador");
                break;
            case 'admin':
                header("Location: /estagio/admin");
                break;
            case 'seguranca':
                header("Location: /estagio/seguranca");
                break;
            default:
                registrarAtividade($sessaoId, "Tentativa de redirecionamento com role inválida: {$role}", "ERROR");
                $this->error = "Tipo de usuário desconhecido.";
                return $this->error;
        }
        exit();
    }

    public function getError()
    {
        return $this->error;
    }

    private function getUserById($user_id)
    {
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
