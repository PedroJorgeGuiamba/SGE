<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController {
    private $conn;
    private $error;
    private $loginAttempts = 0;
    private $criptografia;

    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->loginAttempts = $_SESSION['login_attempts'] ?? 0;
        $this->criptografia = new Criptografia();
    }

    public function verificar() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return "Método inválido.";
        }

        error_log("=== AUTH CONTROLLER SESSION DEBUG ===");
        error_log("Session ID: " . session_id());
        error_log("All Session Data: " . print_r($_SESSION, true));

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "Token de segurança inválido. Recarregue a página e tente novamente.";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            return $this->error;
        }

        // if ($this->loginAttempts >= 5) {
        //     $this->error = "Muitas tentativas. Espere 5 minutos.";
        //     $_SESSION['login_attempts'] = $this->loginAttempts;
        //     return $this->error;
        // }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $senha = trim($_POST['password'] ?? '');
        $senha = strip_tags($senha);
        $senha = htmlspecialchars($senha,
        ENT_QUOTES, 'UTF-8');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = "Email inválido.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Email inválido: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            return $this->error;
        }

        if (empty($senha)) {
            $this->error = "Senha obrigatória.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            return $this->error;
        }

        $sql = "SELECT id, email, password, role FROM usuarios";
        $result = $this->conn->query($sql);
        $userEncontrado = false;
        $row = null;

        // $stmt = $this->conn->prepare($sql);
        
        // if (!$stmt) {
        //     $this->error = "Erro interno do sistema.";
        //     error_log("Erro prepare user query: " . $this->conn->error);
        //     return $this->error;
        // }
        
        // if (!$stmt->execute()) {
        //     $this->error = "Erro ao buscar usuários.";
        //     error_log("Erro execute user query: " . $stmt->error);
        //     $stmt->close();
        //     return $this->error;
        // }

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $email_descriptografado = $this->criptografia->descriptografar($row['email']);
                if ($email_descriptografado === $email) {
                    $userEncontrado = true;
                    break;
                }
            }
        }

        if ($userEncontrado && $row) {
            if (password_verify($senha, $row['password'])) {
                $_SESSION['login_attempts'] = 0; // Reset

                $otp = random_int(100000, 999999);
                $expira = date("Y-m-d H:i:s", time() + 300);

                $sqlOtp = "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) VALUES (?, ?, ?, NOW())";
                $stmtOtp = $this->conn->prepare($sqlOtp);

                if (!$stmtOtp) {
                    $this->error = "Erro ao preparar consulta OTP.";
                    error_log("Erro prepare OTP: " . $this->conn->error);
                    return $this->error;
                }

                $stmtOtp->bind_param("iis", $row['id'], $otp, $expira);

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

                    $_SESSION['pending_user_id'] = $this->criptografia->criptografar($row['id']);
                    $_SESSION['user_email'] = $this->criptografia->criptografar($email);
                    $_SESSION['role'] = $this->criptografia->criptografar(strtolower(trim($row['role'] ?? '')));
                    if (class_exists('SecurityLogger')) {
                        SecurityLogger::logSecurityEvent('LOGIN_SUCCESS', $row['id'], [
                            'email_hash' => hash('sha256', $email)
                        ]);
                    } else {
                        error_log("LOGIN_SUCCESS - User ID: " . $row['id']);
                    }
                    header("Location: /estagio/View/Auth/ValidarUser.php");
                    exit();
                } else {
                    $this->error = "Erro ao gerar OTP.";
                    return $this->error;
                }
            } else {
                $this->error = "Senha incorreta.";
                $this->loginAttempts++;
                $_SESSION['login_attempts'] = $this->loginAttempts;
                registrarAtividade(null, "Senha errada para " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
                return $this->error;
            }
        } else {
            $this->error = "Email não encontrado.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Email não registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            return $this->error;
        }
    }

    public function getError() {
        return $this->error;
    }
}

$login = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login->verificar();
    $error = $login->getError();
}
?>