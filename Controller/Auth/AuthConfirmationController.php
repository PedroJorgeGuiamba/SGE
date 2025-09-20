<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
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

        $codigo = trim($_POST['codigo'] ?? '');
        if (!preg_match('/^\d{6}$/', $codigo)) {
            $this->error = "Código inválido. Deve ter 6 dígitos.";
            return $this->error;
        }

        // Descriptografar o pending_user_id da sessão
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

        // Verificar OTP
        $sql = "SELECT * FROM user_otps WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
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
        if ($otp['otp_code'] != $codigo) {
            $this->error = "Código inválido.";
            return $this->error;
        }

        // Buscar dados do usuário
        $sqlUser = "SELECT id, email, role FROM usuarios WHERE id = ?";
        $stmtUser = $this->conn->prepare($sqlUser);
        $stmtUser->bind_param("i", $user_id);
        $stmtUser->execute();
        $user = $stmtUser->get_result()->fetch_assoc();
        $stmtUser->close();

        if (!$user) {
            $this->error = "Usuário não encontrado.";
            return $this->error;
        }

        // Descriptografar o e-mail do banco
        $email_descriptografado = $this->criptografia->descriptografar($user['email']);
        if (!$email_descriptografado) {
            $this->error = "Erro ao descriptografar e-mail do usuário.";
            return $this->error;
        }

        // Marcar OTP como usado
        $sqlUpdate = "UPDATE user_otps SET is_used = 1 WHERE id = ?";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $otp['id']);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Configurar sessão com dados criptografados
        $_SESSION['email'] = $email_descriptografado;
        $_SESSION['role'] = $user['role'];
        $_SESSION['usuario_id'] = $user['id'];

        // Limpar pending_user_id
        unset($_SESSION['pending_user_id']);

        // Registrar atividade
        $sessaoId = iniciarSessao($user['id']);
        registrarAtividade($sessaoId, "Login do User: " . $this->criptografia->criptografar($email_descriptografado) . " Realizado com sucesso", "LOGIN");

        // Regenerar ID da sessão para maior segurança
        session_regenerate_id(true);

        // Redirecionar com base na role
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
}

$confirm = new AuthConfirmationController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = $confirm->verificar();
}
?>