<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/Supervisor.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarSupervisor
{
    private mysqli $conn;
    private Supervisor $supervisor;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->supervisor = new Supervisor();
    }
    public function editarSupervisor()
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

            $id_supervisor = isset($_POST['id_supervisor']) && is_numeric($_POST['id_supervisor']) ? (int) $_POST['id_supervisor'] : null;
            $id_qualificacao = isset($_POST['id_qualificacao']) && is_numeric($_POST['id_qualificacao']) ? (int) $_POST['id_qualificacao'] : null;
            $area = trim($_POST['area']) ?? '';
            $nome = trim($_POST['nome']) ?? '';

            if (empty($id_qualificacao) || empty($area) || empty($nome) || empty($id_supervisor)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            if ($this->supervisor->actualizar($nome, $area, $id_qualificacao, $id_supervisor, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar supervisor: ' . $this->conn->error]);
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

            error_log('Erro em editarSupervisor.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarSupervisor();
$controller->editarSupervisor();
