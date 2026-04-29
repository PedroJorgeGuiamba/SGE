<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Qualificacao.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarQualificacao
{
    private mysqli $conn;
    private Qualificacao $qualificacao;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->qualificacao = new Qualificacao();
    }
    public function editarQualificacao()
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

            $id_qualificacao = isset($_POST['id_qualificacao']) && is_numeric($_POST['id_qualificacao']) ? (int) $_POST['id_qualificacao'] : null;
            $qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $descricao = trim($_POST['descricao']) ?? '';
            $nivel = trim($_POST['nivel']) ?? '';

            if (empty($id_qualificacao) || empty($qualificacao) || empty($descricao) || empty($nivel)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if ($this->qualificacao->actualizar($qualificacao, $descricao, $nivel, $id_qualificacao, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar qualificacao: ' . $this->conn->error]);
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

            error_log('Erro em editarQualificacao.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarQualificacao();
$controller->editarQualificacao();
