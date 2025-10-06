<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "Método inválido.";
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "Token de segurança inválido. Recarregue a página e tente novamente.";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            return $this->error;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $password = strip_tags($password);
        $password = htmlspecialchars($password,
        ENT_QUOTES, 'UTF-8');
        $role = filter_var($_POST['role'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
            registrarAtividade(null, "Novo usuário registrado: {$email_encripted}", "REGISTRO");
            error_log("DEBUG - Register role: '$roleLower' for user ID: $userId");

            $otp = random_int(100000, 999999);
            $expira = date("Y-m-d H:i:s", time() + 300);

            $sqlOtp = "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) VALUES (?, ?, ?, NOW())";
            $stmtOtp = $this->conn->prepare($sqlOtp);

            if (!$stmtOtp) {
                $this->error = "Erro ao preparar consulta OTP.";
                error_log("Erro prepare OTP: " . $this->conn->error);
                return $this->error;
            }

            $stmtOtp->bind_param("iis", $userId, $otp, $expira);

            if ($stmtOtp->execute()) {
                $stmtOtp->close();

                // Envio email via Python
                $escapedEmail = escapeshellarg($email);
                $escapedOtp = escapeshellarg($otp);
                $pythonPath = __DIR__ . '/AuthMailSender.py';
                $command = "python $pythonPath $escapedEmail $escapedOtp 2>&1";
                $output = shell_exec($command);
                if (strpos($output ?? '', 'Erro') !== false) {
                    error_log("Falha email OTP: $output");
                }

                $_SESSION['pending_user_id'] = $this->criptografia->criptografar($userId);
                $_SESSION['user_email'] = $this->criptografia->criptografar($email);
                $_SESSION['role'] = $this->criptografia->criptografar(strtolower(trim($row['role'] ?? '')));
                if (class_exists('SecurityLogger')) {
                    SecurityLogger::logSecurityEvent('LOGIN_SUCCESS', $userId, [
                        'email_hash' => hash('sha256', $email)
                    ]);
                } else {
                    error_log("LOGIN_SUCCESS - User ID: " . $userId);
                }
                header("Location: /estagio/View/Auth/ValidarUser.php");
                exit();
            } else {
                $this->error = "Erro ao gerar OTP.";
                return $this->error;
            }
        }else {
            $this->error = "Falha no registo. Tente novamente.";
            $stmt->close();
            return false;
        }
    }

    public function getError() {
        return $this->error;
    }
}

$controller = new RegisterController();
$controller->register();
$error = $controller->getError();
?>