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

class ConfirmacaoFormandoController
{
    private $conn;
    private Criptografia $criptografia;
    private $error;
    private $maxAttempts = 3;
    private $lockoutTime = 300; // 5 minutos em segundos

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
            $this->error = "Método inválido.";
            return $this->error;
        }

        // Verificar se o usuário está logado e é um formando
        if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'formando') {
            $this->error = "Acesso não autorizado.";
            return $this->error;
        }

        $codigo_formando = trim($_POST['codigo_formando'] ?? '');
        if (!preg_match('/^\d+$/', $codigo_formando) || strlen($codigo_formando) > 10) {
            $this->error = "Código de formando inválido.";
            return $this->error;
        }

        $codigo_formando = intval($codigo_formando);

        $usuario_id = intval($_SESSION['usuario_id']);

        // Verificar tentativas de confirmação usando sessão
        $attempts = $_SESSION['confirmation_attempts'] ?? 0;
        $lastAttemptTime = $_SESSION['confirmation_last_attempt_time'] ?? 0;

        if ($attempts >= $this->maxAttempts) {
            $timeDiff = time() - $lastAttemptTime;

            if ($timeDiff < $this->lockoutTime) {
                $remainingTime = $this->lockoutTime - $timeDiff;
                $minutes = floor($remainingTime / 60);
                $seconds = $remainingTime % 60;
                $this->error = "Muitas tentativas. Tente novamente em {$minutes} minutos e {$seconds} segundos.";
                return $this->error;
            } else {
                // Resetar tentativas após o tempo de bloqueio
                $_SESSION['confirmation_attempts'] = 0;
                unset($_SESSION['confirmation_last_attempt_time']);
                $attempts = 0;
            }
        }

        // Verificar se o código do formando existe na tabela formando
        if (!$this->verificarFormando($codigo_formando)) {
            $_SESSION['confirmation_attempts'] = $attempts + 1;
            $_SESSION['confirmation_last_attempt_time'] = time();
            $remainingAttempts = $this->maxAttempts - ($attempts + 1);
            $this->error = "Código de formando não encontrado. Tentativas restantes: {$remainingAttempts}";
            return $this->error;
        }

        // Limpar tentativas de confirmação
        unset($_SESSION['confirmation_attempts']);
        unset($_SESSION['confirmation_last_attempt_time']);

        // Adicionar codigo_formando à sessão
        $_SESSION['codigo_formando'] = $codigo_formando;

        // Registrar atividade
        $sessaoId = iniciarSessao($usuario_id);
        registrarAtividade($sessaoId, "Confirmação de formando realizada com sucesso - Código: " . $this->criptografia->criptografar($codigo_formando), "CONFIRMACAO_FORMANDO");

        // Redirecionar para o portal do estudante
        header("Location: /estagio/formando");
        exit();
    }

    private function verificarFormando($codigo_formando)
    {
        $sql = "SELECT id_formando FROM formando WHERE codigo = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Erro prepare verificarFormando: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $codigo_formando);

        if (!$stmt->execute()) {
            error_log("Erro execute verificarFormando: " . $stmt->error);
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }



    public function getError()
    {
        return $this->error;
    }

    public function getRemainingAttempts()
    {
        $attempts = $_SESSION['confirmation_attempts'] ?? 0;
        $lastAttemptTime = $_SESSION['confirmation_last_attempt_time'] ?? 0;

        if ($attempts >= $this->maxAttempts) {
            $timeDiff = time() - $lastAttemptTime;

            if ($timeDiff < $this->lockoutTime) {
                return 0; // Bloqueado
            } else {
                return $this->maxAttempts; // Resetado
            }
        }

        return $this->maxAttempts - $attempts;
    }

    public function getLockoutRemainingTime()
    {
        $attempts = $_SESSION['confirmation_attempts'] ?? 0;

        if ($attempts < $this->maxAttempts) {
            return 0;
        }

        $lastAttemptTime = $_SESSION['confirmation_last_attempt_time'] ?? 0;
        $timeDiff = time() - $lastAttemptTime;

        if ($timeDiff >= $this->lockoutTime) {
            return 0;
        }

        return $this->lockoutTime - $timeDiff;
    }
}
