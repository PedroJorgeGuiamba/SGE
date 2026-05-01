<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Formador.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';

class EditarFormador
{
    private mysqli $conn;
    private Formador $formador;
    private Criptografia $criptografia;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->criptografia = new Criptografia();
    }
    public function editarFormador()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método de requisição inválido']);
        }
        try {
            $token = $_POST['csrf_token'] ?? '';
            try {
                CSRFProtection::validateToken($token);
            } catch (ErrorException $e) {
                echo json_encode(['success' => false, 'message' => $e]);
            }
            $this->conn->begin_transaction();

            $id_formador = isset($_POST['id_formador']) && is_numeric($_POST['id_formador']) ? (int) $_POST['id_formador'] : null;
            $nome = trim($_POST['nome']) ?? '';
            $apelido = trim($_POST['apelido']) ?? '';
            $codigo = isset($_POST['codigo']) && is_numeric($_POST['codigo']) ? (int) $_POST['codigo'] : null;
            $dataDeNascimento = isset($_POST['dataDeNascimento']) && !empty(trim($_POST['dataDeNascimento']))
                ? trim($_POST['dataDeNascimento'])
                : null;
            $naturalidade = trim($_POST['naturalidade']) ?? '';
            $tipoDeDocumento = trim($_POST['tipoDeDocumento']) ?? '';
            $numeroDeDocumento = trim($_POST['numeroDeDocumento']) ?? '';
            $localEmitido = trim($_POST['localEmitido']) ?? '';
            $dataDeEmissao = isset($_POST['dataDeEmissao']) && !empty(trim($_POST['dataDeEmissao']))
                ? trim($_POST['dataDeEmissao'])
                : null;
            $NUIT = trim($_POST['NUIT']) ?? '';
            $telefone = trim($_POST['telefone']) ?? '';
            $email = trim($_POST['email']) ?? '';
            $userID = isset($_POST['userID']) && is_numeric($_POST['userID']) ? (int) $_POST['userID'] : null;

            if (empty($id_formador) || empty($codigo) || empty($nome) || empty($apelido) || empty($dataDeNascimento) || empty($naturalidade) || empty($tipoDeDocumento) || empty($numeroDeDocumento) || empty($localEmitido) || empty($dataDeEmissao) || empty($telefone) || empty($email)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            $numeroDeDocumento_encrypted = $this->criptografia->criptografar($numeroDeDocumento);
            $NUIT_encrypted = $this->criptografia->criptografar($NUIT);
            $telefone_encrypted = $this->criptografia->criptografar($telefone);
            $email_encrypted = $this->criptografia->criptografar($email);

            $this->formador = new Formador(
                $nome,
                $apelido,
                $codigo,
                new DateTime($dataDeNascimento),
                $naturalidade,
                $tipoDeDocumento,
                $numeroDeDocumento_encrypted,
                $localEmitido,
                new DateTime($dataDeEmissao),
                $NUIT_encrypted,
                $telefone_encrypted,
                $email_encrypted,
                $userID);

            if ($this->formador->actualizar($nome, $apelido, $codigo, $dataDeNascimento, $naturalidade, $tipoDeDocumento, $numeroDeDocumento_encrypted, $localEmitido, $dataDeEmissao, $NUIT_encrypted, $telefone_encrypted, $email_encrypted, $userID, $id_formador, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Formador atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar formador: ' . $this->conn->error]);
            }

            $this->conn->commit();
            $this->conn->close();
        } catch (Exception $e) {
            if ($this->conn instanceof mysqli) {
                try {
                    $this->conn->rollback();
                } catch (Throwable $rollbackError) {
                    // No-op: rollback best effort.
                }
            }

            error_log('Erro em editarFormador.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarFormador();
$controller->editarFormador();
