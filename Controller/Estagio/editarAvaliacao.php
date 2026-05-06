<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Model/avaliarEstagio.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';

class EditarAvaliacao
{
    private mysqli $conn;
    private AvaliarEstagio $avaliar;

    public function __construct()
    {
        $conexao = new Conector();
        $this->conn = $conexao->getConexao();
        $this->avaliar = new AvaliarEstagio();
    }
    public function editarAvaliacao()
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

            $id_avaliacao = isset($_POST['id_avaliacao']) && is_numeric($_POST['id_avaliacao']) ? (int)$_POST['id_avaliacao'] : null;
            $codigo_formando = isset($_POST['codigo_formando']) && is_numeric($_POST['codigo_formando']) ? (int)$_POST['codigo_formando'] : null;
            $qualificacao = isset($_POST['qualificacao']) && is_numeric($_POST['qualificacao']) ? (int) $_POST['qualificacao'] : null;
            $codigo_turma = isset($_POST['codigo_turma']) && is_numeric($_POST['codigo_turma']) ? (int) $_POST['codigo_turma'] : null;
            $empresa = trim(strtoupper($_POST['empresa'])) ?? '';
            $ano_turma = trim($_POST['ano_turma']) ?? '';
            $resultado = trim($_POST['resultado']) ?? '';
            $comentario = $_POST['comentario'] ?? '';

            if (empty($id_avaliacao) || empty($codigo_formando) || empty($empresa) || empty($qualificacao) || empty($codigo_turma)) {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Exceção capturada: Por favor preencha todos os campos']);
            }

            $anoAtual = (int) date('Y');
            $sql = "
                SELECT DISTINCT COALESCE(MAX(id_pedido_carta), 0) AS ultimo_id
                FROM pedido_carta
                WHERE YEAR(data_do_pedido) = ?
                AND codigo_formando = ?
                AND empresa = ?
                FOR UPDATE
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iis", $anoAtual, $codigo_formando, $empresa);
            $stmt->execute();
            $resultadoNumero = $stmt->get_result()->fetch_assoc();

            $id_pedido_estagio = $resultadoNumero['ultimo_id'];
            
            if ($id_pedido_estagio <= 0) {
                $this->conn->rollback();
                throw new RuntimeException("Nao foi possivel obter o ID do pedido");
            }

            if ($this->avaliar->actualizar($codigo_formando, $qualificacao, $codigo_turma, $empresa, $ano_turma, $resultado, $comentario, $id_pedido_estagio, $id_avaliacao, $this->conn)) {
                echo json_encode(['success' => true, 'message' => 'Pedido atualizado com sucesso!']);
            } else {
                $this->conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar pedido: ' . $this->conn->error]);
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

            error_log('Erro em editarPedido.php: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Exceção capturada: ' . $e->getMessage()]);
        }
    }
}

$controller = new EditarAvaliacao();
$controller->editarAvaliacao();
