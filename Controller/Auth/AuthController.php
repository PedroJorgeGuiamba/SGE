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

    private function verifyRecaptcha($token, $siteKey, $projectId, $apiKey)
    {
        if (empty($token)) {
            return ['success' => false, 'error' => 'Token não fornecido'];
        }

        $url = "https://recaptchaenterprise.googleapis.com/v1/projects/{$projectId}/assessments?key={$apiKey}";
        
        $data = [
            'event' => [
                'token' => $token,
                'siteKey' => $siteKey,
                'expectedAction' => 'LOGIN'
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? 'Erro desconhecido';
            return ['success' => false, 'error' => $errorMsg];
        }

        $responseData = json_decode($response, true);

        if (!isset($responseData['tokenProperties']['valid']) || !$responseData['tokenProperties']['valid']) {
            return ['success' => false, 'error' => 'Token inválido ou expirado'];
        }

        if (($responseData['tokenProperties']['action'] ?? '') !== 'LOGIN') {
            return ['success' => false, 'error' => 'Ação de segurança inválida'];
        }

        $score = $responseData['riskAnalysis']['score'] ?? 0.0;
        if ($score < 0.5) {
            return ['success' => false, 'error' => "Score muito baixo: {$score}"];
        }

        return ['success' => true, 'score' => $score];
    }

    public function verificar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
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

        $env = parse_ini_file(__DIR__ . '/../../config/.env');

        foreach ($env as $key => $value) {
            putenv("$key=$value");
        }

        $projectId = getenv("GOOGLE_CLOUD_PROJECT_ID");
        $apiKey = getenv("GOOGLE_RECAPTCHA_API_KEY");
        $siteKey = getenv("GOOGLE_DATA_SITE_KEY");
        $recaptchaToken = $_POST['g-recaptcha-response'] ?? '';

        $recaptchaResult = $this->verifyRecaptcha($recaptchaToken, $siteKey, $projectId, $apiKey);

        if (!$recaptchaResult['success']) {
            $this->error = "Falha na verificação de segurança: " . $recaptchaResult['error'];
            $this->loginAttempts++;
            $_SESSION['login_attempts'] = $this->loginAttempts;
            registrarAtividade(null, "Falha no reCAPTCHA", "LOGIN_FAILED");
            header("Location: /estagio/login?erros=" . urlencode($this->error));
            exit();
        }

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

        $email_hashed = hash('sha256', strtolower(trim($email)));

        $tentativas = $_SESSION['login_falhas'][$email_hashed] ?? ['tentativas' => 0, 'bloqueado_ate' => null];

        if (!empty($tentativas['bloqueado_ate'])) {
            $restante = $tentativas['bloqueado_ate'] - time();

            if ($restante > 0) {
                $minutos = ceil($restante / 60);
                header("Location: /estagio/login?erros=" . urlencode("Conta bloqueada. Tente novamente em {$minutos} minuto(s)."));
                exit();
            }

            $_SESSION['login_falhas'][$email_hashed] = ['tentativas' => 0, 'bloqueado_ate' => null];
            $tentativas = $_SESSION['login_falhas'][$email_hashed];
        }

        if ($tentativas['tentativas'] >= 5) {
            $_SESSION['login_falhas'][$email_hashed]['bloqueado_ate'] = time() + 300;
            header("Location: /estagio/login?erros=" . urlencode("Muitas tentativas falhadas. Conta bloqueada por 5 minutos."));
            exit();
        }
        
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
