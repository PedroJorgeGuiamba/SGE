<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Usuario.php';
require_once __DIR__ . '/../../Helpers/Sessao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

SecurityHeaders::setBasic();
class RegisterController
{
    private $conn;
    private $error;
    private Criptografia $criptografia;
    private Usuario $usuario;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->criptografia = new Criptografia();
        $this->usuario = new Usuario();
    }

    public function register()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->error = "Método inválido.";
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "Token de segurança inválido. Recarregue a página e tente novamente.";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');
        $password = strip_tags($password);
        $password = htmlspecialchars(
            $password,
            ENT_QUOTES,
            'UTF-8'
        );
        $role = filter_var($_POST['role'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error = "Por favor, insira um endereço de email válido.";
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        if (empty($password)) {
            $this->error = "Por favor, insira uma senha.";
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        $roleLower = strtolower(trim($role));
        $validRoles = ['admin', 'formando', 'formador', 'supervisor', 'seguranca'];
        if (empty($roleLower) || !in_array($roleLower, $validRoles)) {
            $this->error = "Por favor, selecione uma função válida (Admin, Formando, Formador, Supervisor).";
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        $this->conn->begin_transaction();

        $email_hashed = hash('sha256', $email);
        $result = $this->usuario->getUsersByEmail($this->conn, $email);

        $result = $this->usuario->getUsersByEmailHashed($this->conn, $email_hashed);
        if ($result && $result->num_rows > 0) {
            $this->conn->rollback();
            registrarAtividade(null, "Email registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
            header("Location: /estagio/register?erros=" . urlencode("Email já registrado."));
            exit();
        }

        $email_encripted = $this->criptografia->criptografar($email);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->usuario->setEmail($email_encripted);
        $this->usuario->setSenha($hashedPassword);
        $this->usuario->setRole($roleLower);
        $this->usuario->setEmailHash($email_hashed);

        if (!$this->usuario->salvar($this->conn)) {
            $this->conn->rollback();
            $this->error = "Falha no registo. Tente novamente.";
            $this->conn->close();
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        $userId = $this->conn->insert_id;
        registrarAtividade(null, "Novo usuário registrado: {$email_encripted}", "REGISTRO");
        error_log("DEBUG - Registro de um novo Utilizador com a role: '$roleLower' e o ID: $userId");

        $otp = random_int(100000, 999999);
        $expira = date("Y-m-d H:i:s", time() + 300);

        $sqlOtp = "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) VALUES (?, ?, ?, NOW())";
        $stmtOtp = $this->conn->prepare($sqlOtp);

        if (!$stmtOtp) {
            $this->conn->rollback();
            $this->error = "Erro ao preparar consulta OTP.";
            error_log("Erro prepare OTP: " . $this->conn->error);
            header("Location: /estagio/register?erros=" . urlencode($this->error));
            exit();
        }

        $stmtOtp->bind_param("iis", $userId, $otp, $expira);

        if (!$stmtOtp->execute()) {
            $this->conn->rollback();
            $this->error = "Erro ao gerar OTP.";
            return $this->error;
        }

        $stmtOtp->close();

        // Envio email via Python
        $escapedEmail = escapeshellarg($email);
        $escapedOtp = escapeshellarg($otp);
        $pythonPath = escapeshellarg(__DIR__ . '/AuthMailSender.py');
        $command = "python $pythonPath $escapedEmail $escapedOtp 2>&1";
        $safe_argument = escapeshellarg($command);
        $output = shell_exec($safe_argument);
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

        $this->conn->commit();
        header("Location: /estagio/validar");
        exit();
    }

    public function getError()
    {
        return $this->error;
    }
}

$controller = new RegisterController();
$controller->register();
$error = $controller->getError();
