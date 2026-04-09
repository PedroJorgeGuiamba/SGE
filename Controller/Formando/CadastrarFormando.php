<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Formando.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setBasic();


class CadastrarFormando
{
    private $conn;
    private $error;
    private $criptografia;


    public function __construct() {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->error = '';
        $this->criptografia = new Criptografia();
    }

    public function cadastrarFormando()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estagio/View/Formando/CadastrarFormando.php?erros=" . "Método inválido.");
        }

        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (Exception $e) {
            $this->error = "";
            error_log("CSRF Validation Failed: " . $e->getMessage());
            header("Location: /estagio/View/Formando/CadastrarFormando.php?erros=" . "Token de segurança inválido. Recarregue a página e tente novamente.");
        }

        try{

            $codigo = isset($_POST['codigoformando']) ? (int) $_POST['codigoformando'] : null;
            $nome = trim($_POST['nomeformando'] ?? '');
            $apelido = trim($_POST['apelidoformando'] ?? '');
            $dataNascimento = isset($_POST['dataNascimento']) && !empty(trim($_POST['dataNascimento']))
                ? new DateTime(trim($_POST['dataNascimento']))
                : null;
            $naturalidade = trim($_POST['naturalidade'] ?? '');
            $tipoDeDocumento = trim($_POST['tipoDeDocumento'] ?? '');
            $numeroDeDocumento = trim($_POST['numeroDeDocumento'] ?? '' );
            $localEmitido = trim($_POST['localEmitido'] ??'');
            $dataEmissao = isset($_POST['dataEmissao']) && !empty(trim($_POST['dataEmissao']))
                ? new DateTime(trim($_POST['dataEmissao']))
                : null;
            $nuit = isset($_POST['nuit']) ? (int) $_POST['nuit'] : null;
            $telefone = isset($_POST['telefone']) ? (int) $_POST['telefone'] : null;
            $email = trim($_POST['email'] ??'');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: /estagio/View/Formando/CadastrarFormando.php?erros=" . htmlspecialchars("Por favor, insira um endereço de email válido."));
            }

            $stmt = $this->conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $stmt->close();
                header("Location: /estagio/View/Formando/CadastrarFormando.php?erros=" . htmlspecialchars("Email já registrado."));
            }
            $stmt->close();

            $email_encripted = $this->criptografia->criptografar($email);
            $hashedPassword = password_hash($apelido, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO usuarios (email, password, role) VALUES (?, ?, 'formando')");
            $stmt->bind_param("ss", $email_encripted, $hashedPassword);

            $resultUser = $stmt->execute();
            if (!$resultUser) {
                error_log("Erro na execução da query: " . $stmt->error);
            }
            $userId = $stmt->insert_id;

            $formando = new Formando(
                $nome,
                $apelido,
                $codigo,
                $dataNascimento,
                $naturalidade,
                $tipoDeDocumento,
                $numeroDeDocumento,
                $localEmitido,
                $dataEmissao,
                $nuit,
                $telefone,
                $email,
                $userId
            );

            if (!$formando->salvar()) {
                header("Location: /estagio/View/Formando/CadastrarFormando.php?erros=" . htmlspecialchars("Erro ao cadastrar formando."));
            }

            if (isset($_SESSION['sessao_id'])) {
                registrarAtividade($_SESSION['sessao_id'], "Cadastrou uma formando: " . $nome, "CRIACAO");
            }

            header("Location: /estagio/View/Admin/portalDoAdmin.php");
        }catch(Exception $e){

        }
    }
}

$erros = '';
$formando = new CadastrarFormando();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $erros = $formando->cadastrarFormando();
}

