<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Formando.php';
require_once __DIR__ . '/../../Model/Usuario.php';
require_once __DIR__ . '/../../Model/Notificacao.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();


class CadastrarFormando
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

    public function cadastrarFormando()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/formando/criar?erros=" . urlencode("Método inválido."));
            exit();
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/formando/criar?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
            exit();
        }

        try {

            $codigo = isset($_POST['codigoformando']) ? (int) $_POST['codigoformando'] : null;
            $nome = trim($_POST['nomeformando'] ?? '');
            $apelido = trim($_POST['apelidoformando'] ?? '');
            $dataNascimento = isset($_POST['dataNascimento']) && !empty(trim($_POST['dataNascimento']))
                ? new DateTime(trim($_POST['dataNascimento']))
                : null;
            $naturalidade = trim($_POST['naturalidade'] ?? '');
            $tipoDeDocumento = trim(strtoupper($_POST['tipoDeDocumento']) ?? '');
            $numeroDeDocumento = trim(strtoupper($_POST['numeroDeDocumento']) ?? '');

            if (strlen($numeroDeDocumento) < 5) {
                header("Location: /estagio/formando/criar?erros=" . htmlspecialchars("O Número de documento deve ter no mínimo 5 digitos."));
                exit();
            }

            $localEmitido = trim($_POST['localEmitido'] ?? '');
            $dataEmissao = isset($_POST['dataEmissao']) && !empty(trim($_POST['dataEmissao']))
                ? new DateTime(trim($_POST['dataEmissao']))
                : null;
            $nuit = isset($_POST['nuit']) ? (int) $_POST['nuit'] : null;

            if ($nuit <= 9) {
                header("Location: /estagio/formando/criar?erros=" . htmlspecialchars("O NUIT deve ter no mínimo 9 digitos."));
                exit();
            }

            $telefone = isset($_POST['telefone']) ? (int) $_POST['telefone'] : null;
            $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/formando/criar?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
                exit();
            }

            $this->conn->begin_transaction();

            $email_hashed = hash('sha256', $email);

            $result = $this->usuario->getUsersByEmailHashed($this->conn, $email_hashed);
            if ($result  && $result->num_rows > 0) {
                $this->conn->rollback();
                registrarAtividade(null, "Email registrado: " . $this->criptografia->criptografar($email), "LOGIN_FAILED");
                header("Location: /estagio/formando/criar?erros=" . urlencode("Email já registrado."));
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
            $this->usuario->setRole('formando');

            $resultUser = $this->usuario->salvar($this->conn);
            if (!$resultUser) {
                $this->conn->rollback();
                error_log("Erro na execução da query: " . $this->conn->error);
            }
            $userId = $this->conn->insert_id;

            $formando = new Formando(
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

            if (!$formando->salvar($this->conn)) {
                $this->conn->rollback();
                header("Location: /estagio/formando/criar?erros=" . htmlspecialchars("Erro ao cadastrar formando."));
                exit();
            }

            if (!empty($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formando: " . $nome, "CRIACAO");
            }

            if (!empty($_SESSION['usuario_id'])) {
                $mensagem = "O formando $nome foi cadastrado com sucesso.";

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

                }
            }

            header("LOCATION: /estagio/formando/criar?erros=" . urlencode("Erro no sistema." . $e->getMessage()));
            exit();
        }
    }
}

$formando = new CadastrarFormando();
$formando->cadastrarFormando();
