<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Curso.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarCurso
{
    private mysqli $conn;
    private Curso $curso;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->curso = new Curso();
    }
    public function editarCurso()
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

            $id_curso = isset($_POST['id_curso']) && is_numeric($_POST['id_curso']) ? (int) $_POST['id_curso'] : null;
            $codigo = isset($_POST['codigo']) && is_numeric($_POST['codigo']) ? (int) $_POST['codigo'] : null;
            $nome = trim($_POST['nome']) ?? '';
            $descricao = trim($_POST['descricao']) ?? '';
            $sigla = trim($_POST['sigla']) ?? '';
            $codigo_qualificacao = isset($_POST['id_qualificacao']) && is_numeric($_POST['id_qualificacao']) ? (int) $_POST['id_qualificacao'] : null;

            if (empty($codigo_qualificacao) || empty($codigo) || empty($nome) || empty($id_curso)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if ($this->curso->actualizar($codigo, $nome, $descricao, $sigla, $codigo_qualificacao, $id_curso, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Curso atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar curso: ' . $this->conn->error]);
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

            error_log('Erro em editarCurso.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarCurso();
$controller->editarCurso();
