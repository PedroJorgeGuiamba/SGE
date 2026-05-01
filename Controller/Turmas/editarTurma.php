<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Turma.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarTurma
{
    private mysqli $conn;
    private Turma $turma;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->turma = new Turma();
    }
    public function editarTurma()
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

            $codigo = isset($_POST['codigo']) && is_numeric($_POST['codigo']) ? (int) $_POST['codigo'] : null;
            $nome = trim($_POST['nome']) ?? '';
            $codigo_curso = isset($_POST['codigo_curso']) && is_numeric($_POST['codigo_curso']) ? (int) $_POST['codigo_curso'] : null;
            $codigo_qualificacao = isset($_POST['codigo_qualificacao']) && is_numeric($_POST['codigo_qualificacao']) ? (int) $_POST['codigo_qualificacao'] : null;

            if (empty($codigo_qualificacao) || empty($codigo) || empty($nome)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if ($this->turma->actualizar($codigo, $nome, $codigo_curso, $codigo_qualificacao, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Turma atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar turma: ' . $this->conn->error]);
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

            error_log('Erro em editarTurma.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarTurma();
$controller->editarTurma();
