<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Formador.php';
require_once __DIR__ . '/../../Model/Usuario.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();

class CadastrarFormador
{
    private $conn;
    private Criptografia $criptografia;
    private Usuario $usuario;
    private Notificacao $notificacao;
    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
        $this->usuario = new Usuario();
        $this->notificacao = new Notificacao();
    }

    public function cadastrarFormador()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/formador/criar?erros=" . urlencode("Método inválido."));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/formador/criar?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
            exit();
        }

        try {

            $codigo = isset($_POST['codigoformador']) ? (int) $_POST['codigoformador'] : null;
            $nome = trim($_POST['nomeformador'] ?? '');
            $apelido = trim($_POST['apelidoformador'] ?? '');
            $dataNascimento = isset($_POST['dataNascimento']) && !empty(trim($_POST['dataNascimento']))
                ? new DateTime(trim($_POST['dataNascimento']))
                : null;
            $naturalidade = trim($_POST['naturalidade'] ?? '');
            $tipoDeDocumento = trim(strtoupper($_POST['tipoDeDocumento']) ?? '');
            $numeroDeDocumento = trim(strtoupper($_POST['numeroDeDocumento']) ?? '');

            if (strlen($numeroDeDocumento) < 5) {
                header("Location: /estagio/formador/criar?erros=" . htmlspecialchars("O Número de documento deve ter no mínimo 5 digitos."));
                exit();
            }

            $localEmitido = trim($_POST['localEmitido'] ?? '');
            $dataEmissao = isset($_POST['dataEmissao']) && !empty(trim($_POST['dataEmissao']))
                ? new DateTime(trim($_POST['dataEmissao']))
                : null;
            $nuit = isset($_POST['nuit']) ? (int) $_POST['nuit'] : null;

            if ($nuit <= 9) {
                header("Location: /estagio/formador/criar?erros=" . htmlspecialchars("O NUIT deve ter no mínimo 9 digitos."));
                exit();
            }

            $telefone = isset($_POST['telefone']) ? (int) $_POST['telefone'] : null;
            $email = trim($_POST['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/formador/criar?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
                exit();
            }

            $this->conn->begin_transaction();

            $email_hashed = hash('sha256', $email);

            $result = $this->usuario->getUsersByEmailHashed($this->conn, $email_hashed);
            if ($result  && $result->num_rows > 0) {
                $this->conn->rollback();
                registrarAtividade(null, "Email registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
                header("Location: /estagio/formador/criar?erros=" . urlencode("Email já registrado."));
                exit();
            }

            $email_encripted = $this->criptografia->criptografar($email);
            $hashedPassword = password_hash($apelido, PASSWORD_DEFAULT);

            $nuit_encripted = $this->criptografia->criptografar($nuit);
            $numeroDeDocumento_encripted = $this->criptografia->criptografar($numeroDeDocumento);
            $telefone_encripted = $this->criptografia->criptografar($telefone);

            $this->usuario->setEmail($email_encripted);
            $this->usuario->setEmailHash(hash('sha256', $email));
            $this->usuario->setSenha($hashedPassword);
            $this->usuario->setRole('formador');

            $resultUser = $this->usuario->salvar($this->conn);
            if (!$resultUser) {
                $this->conn->rollback();
                error_log("Erro na execução da query: " . $this->conn->error);
            }
            $userId = $this->conn->insert_id;

            $formador = new Formador(
                $nome,
                $apelido,
                $codigo,
                $dataNascimento,
                $naturalidade,
                $tipoDeDocumento,
                $numeroDeDocumento_encripted,
                $localEmitido,
                $dataEmissao,
                $nuit_encripted,
                $telefone_encripted,
                $email_encripted,
                $userId
            );

            if (!$formador->salvar($this->conn)) {
                $this->conn->rollback();
                header("Location: /estagio/formador/criar?erros=" . htmlspecialchars("Erro ao cadastrar formador."));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formador: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O formador $nome foi cadastrado com sucesso.";

                $this->notificacao->setId_Utilizador($_SESSION['usuario_id']);
                $this->notificacao->setMensagem($mensagem);
                $this->notificacao->salvar($this->conn);
            }

            $this->conn->commit();

            header("Location: /estagio/admin");
            exit();
        } catch (Throwable $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            header("LOCATION: /estagio/formador/criar?erros=" . urlencode("Erro no sistema." . $e->getMessage()));
            exit();
        }
    }
}

$formador = new CadastrarFormador();
$formador->cadastrarFormador();
