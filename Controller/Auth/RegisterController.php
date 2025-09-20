<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
session_start();

class RegisterController {
    private $conn;
    private $error;
    private $criptografia;


    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->criptografia = new Criptografia();
    }

    public function register() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $role = filter_var($_POST['role'] ?? '', FILTER_SANITIZE_STRING);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->error = "Por favor, insira um endereço de email válido.";
                return false;
            }

            if (empty($password)) {
                $this->error = "Por favor, insira uma senha.";
                return false;
            }

            $roleLower = strtolower(trim($role));
            $validRoles = ['admin', 'formando', 'formador', 'supervisor'];
            if (empty($roleLower) || !in_array($roleLower, $validRoles)) {
                $this->error = "Por favor, selecione uma função válida (Admin, Formando, Formador, Supervisor).";
                return false;
            }

            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $this->error = "Email já registrado.";
                $stmt->close();
                return false;
            }
            $stmt->close();

            $email_encripted = $this->criptografia->criptografar($email);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO usuarios (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email_encripted, $hashedPassword, $roleLower);

            if ($stmt->execute()) {
                $userId = $stmt->insert_id;
                $stmt->close();

                $sessaoId = iniciarSessao($userId);

                registrarAtividade($sessaoId, "Novo usuário registrado: {$email_encripted}", "REGISTRO");

                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_nome'] = '';
                $_SESSION['role'] = $roleLower;

                error_log("DEBUG - Register role: '$roleLower' for user ID: $userId");

                $this->redirecionarPorRole($roleLower);

                return true;
            } else {
                $this->error = "Falha no registo. Tente novamente.";
                $stmt->close();
                return false;
            }
        }
        return true;
    }

    public function getError() {
        return $this->error;
    }

    private function redirecionarPorRole($role) {
        error_log("DEBUG - RedirecionarPorRole called with role: '$role'");

        switch (strtolower(trim($role))) {
            case 'admin':
                header("Location: ../../View/Admin/portalDoAdmin.php");
                break;
            case 'formando':
                header("Location: ../../View/Formando/portalDoFormando.php");
                break;
            case 'formador':
                header("Location: ../../View/Formador/portalDoFormador.php");
                break;
            case 'supervisor':
                header("Location: ../../View/Supervisor/portalDoSupervisor.php");
                break;
            default:
                error_log("DEBUG - Invalid role: '$role'. Terminating session.");
                if (isset($_SESSION['sessao_id'])) {
                    registrarAtividade($_SESSION['sessao_id'], "Tentativa de redirecionamento com role inválida: {$role}", "ERROR");
                    terminarSessao();
                }
                header("Location: ../../View/Login.php?error=role_invalida");
                break;
        }
        exit();
    }
}

$controller = new RegisterController();
$controller->register();
$error = $controller->getError();
?>