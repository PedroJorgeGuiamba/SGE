<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
session_start();

class AuthConfirmationController {
    private $conn;
    private $error;

    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
    }

    public function validarOtp() {
        $codigo = $_POST['codigo'] ?? '';
        $erros = '';

        if (empty($codigo)) {
            $erros .= "Código obrigatório.<br>";
            return $erros;
        }

        if (!isset($_SESSION['pending_user_id'])) {
            $erros .= "Sessão inválida.<br>";
            header("Location: " . __DIR__ . '/../../View/Login.php');
            exit();
            return $erros;
        }

        $userId = $_SESSION['pending_user_id'];
        try {
            $sqlOtp = "SELECT * FROM user_otps WHERE user_id = ? AND otp_code = ? AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1";
            $stmtOtp = $this->conn->prepare($sqlOtp);
            $stmtOtp->bind_param("is", $userId, $codigo);
            $stmtOtp->execute();
            $resultOtp = $stmtOtp->get_result();

            if ($resultOtp->num_rows === 0) {
                $erros .= "Código inválido ou expirado.<br>";
                return $erros;
            }

            // $sqlDelete = "DELETE FROM user_otps WHERE user_id = ? AND otp_code = ?";
            // $stmtDelete = $this->conn->prepare($sqlDelete);
            // $stmtDelete->bind_param("is", $userId, $codigo);
            // $stmtDelete->execute();

            // Marcar OTP como usado
            $sqlUpdate = "UPDATE user_otps SET is_used = 1 WHERE user_id = ?";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("i", $userId);
            $stmtUpdate->execute();

            $sqlUser = "SELECT * FROM usuarios WHERE id = ?";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bind_param("i", $userId);
            $stmtUser->execute();
            $resultUser = $stmtUser->get_result();
            $user = $resultUser->fetch_assoc();

            if (!$user) {
                $erros .= "Usuário não encontrado.<br>";
                unset($_SESSION['pending_user_id']);
                header("Location: " . __DIR__ . '/../../View/Login.php');
                exit();
                return $erros;
            }

            $sessaoId = iniciarSessao($user['id']);
            registrarAtividade($sessaoId, "Login sucesso: {$user['email']}", "LOGIN");

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nome'] = $user['nome'] ?? '';
            $_SESSION['role'] = strtolower(trim($user['role'] ?? ''));

            unset($_SESSION['pending_user_id']);

            error_log("DEBUG - Confirmation: Role '{$_SESSION['role']}' para ID {$userId}");

            $this->redirecionarPorRole($_SESSION['role']);

            return '';
        } catch (Exception $e) {
            error_log("Erro OTP: " . $e->getMessage());
            $erros .= "Erro interno.<br>";
            return $erros;
        }
    }

    private function redirecionarPorRole($role) {
        $roleLower = strtolower(trim($role));
        error_log("DEBUG - Redirect role: '$roleLower'");

        switch ($roleLower) {
            case 'admin':
                header("Location: " . __DIR__ . '/../../View/Admin/PortalDoAdmin.php');
                break;
            case 'formando':
                header("Location: " . __DIR__ . '/../../View/Formando/PortalDoFormando.php');
                break;
            case 'formador':
                header("Location: " . __DIR__ . '/../../View/Formador/PortalDoFormador.php');
                break;
            case 'supervisor':
                header("Location: " . __DIR__ . '/../../View/Supervisor/PortalDoSupervisor.php');
                break;
            default:
                error_log("Role inválida: '$roleLower'");
                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Role inválida: {$roleLower}", "ERROR");
                    terminarSessao();
                }
                header("Location: " . __DIR__ . '/../../View/Login.php?error=role_invalida');
                break;
        }
        exit();
    }

    public function getError() {
        return $this->error;
    }
}

$confirmation = new AuthConfirmationController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $confirmation->validarOtp();
    if (!empty($erros)) {
        $error = $erros;
    }
}
?>