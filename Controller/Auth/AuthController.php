<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Model/Usuario.php';

SecurityHeaders::setBasic();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController
{
    private $conn;
    private string $error;
    private int $loginAttempts = 0;
    private Criptografia $criptografia;
    private Usuario $usuario;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->loginAttempts = $_SESSION['login_attempts'] ?? 0;
        $this->criptografia = new Criptografia();
        $this->usuario = new Usuario();
    }

    public function verificar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            // return "Método inválido.";
            header("Location: /estagio/login?erros=" . urldecode("Método inválido."));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "Token de segurança inválido. Recarregue a página e tente novamente.";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

        // if ($this->loginAttempts >= 5) {
        //     $_SESSION['login_block_time'] = time(); // marca quando bloqueou
        //     $_SESSION['login_attempts'] = $this->loginAttempts;

        //     header("Location: /estagio/login?erros=" . urldecode("Muitas tentativas. Espere 5 minutos."));
        //     exit();
        //     // throw new RuntimeException("Muitas tentativas. Espere 5 minutos.");
        //     // $this->error = "Muitas tentativas. Espere 5 minutos.";
        //     // return $this->error;
        // }

        // if (isset($_SESSION['login_block_time'])) {
        //     $tempoBloqueio = $_SESSION['login_block_time'];
        //     $agora = time();

        //     if (($agora - $tempoBloqueio) < 60) {
        //         $restante = 60 - ($agora - $tempoBloqueio);
        //         header("Location: /estagio/login?erros=" . urldecode("Bloqueado. Tente novamente em " . ceil($restante / 60) . " minuto(s)."));
        //         exit();
        //         // return "Bloqueado. Tente novamente em " . ceil($restante / 60) . " minuto(s).";
        //     }

        //     // desbloqueia após 5 minutos
        //     unset($_SESSION['login_block_time']);
        //     $_SESSION['login_attempts'] = 0;
        // }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $senha = trim($_POST['password'] ?? '');
        $senha = strip_tags($senha);
        $senha = htmlspecialchars(
            $senha,
            ENT_QUOTES,
            'UTF-8'
        );

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = "Email inválido.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Email inválido: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

        if (empty($senha)) {
            $this->error = "Senha obrigatória.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

        $email_hashed = hash('sha256', $email);
        $this->conn->begin_transaction();
        $result = $this->usuario->getUsersByEmailHashed($this->conn, $email_hashed);
        if (!$result || $result->num_rows === 0) {
            $this->conn->rollback();
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Email não registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            header("Location: /estagio/login?erros=" . urlencode("Email não encontrado."));
            exit();
        }

        $row = $result->fetch_assoc();

        if (!password_verify($senha, $row['password'])) {
            $this->conn->rollback();
            $this->error = "Senha incorreta.";
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Senha errada para " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

        $_SESSION['login_attempts'] = 0;

        $otp = random_int(100000, 999999);
        $expira = date("Y-m-d H:i:s", time() + 300);

        $sqlOtp = "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) VALUES (?, ?, ?, NOW())";
        $stmtOtp = $this->conn->prepare($sqlOtp);

        if (!$stmtOtp) {
            $this->conn->rollback();
            $this->error = "Erro ao preparar consulta OTP.";
            error_log("Erro prepare OTP: " . $this->conn->error);
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

        $stmtOtp->bind_param("iis", $row['id'], $otp, $expira);

        if (!$stmtOtp->execute()) {
            $stmtOtp->close();
            $this->conn->rollback();
            $this->error = "Erro ao gerar OTP.";
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }
        // Envio email via Python
        $escapedEmail = escapeshellarg($email);
        $escapedOtp = escapeshellarg($otp);
        $pythonPath = escapeshellarg(__DIR__ . '/AuthMailSender.py');
        $command = "python {$pythonPath} {$escapedEmail} {$escapedOtp} 2>&1";
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

        $this->conn->commit();
        header("Location: /estagio/validar");
        exit();
    }

    public function getError()
    {
        return $this->error;
    }
}

$login = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login->verificar();
}
