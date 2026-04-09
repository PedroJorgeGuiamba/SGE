<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    echo json_encode(['success' => false, 'error' => 'Sem permissão']);
    exit;
}

$idPedidoVisita = isset($_POST['id_visita']) ? (int) $_POST['id_visita'] : 0;
if (!$idPedidoVisita) {
    echo json_encode(['success' => false, 'error' => 'ID inválido']);
    exit;
}

$conexao = new Conector();
$conn = $conexao->getConexao();
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        DELETE FROM visita_estagio 
        WHERE id_visita = ?
    ");
    $stmt->bind_param('i', $idPedidoVisita);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new RuntimeException("Visita não encontrada");
    }
    $stmt->close();

    if (isset($_SESSION['sessao_id'])) {
        registrarAtividade($_SESSION['sessao_id'], "Visita de estágio removida (pedido #$idPedidoVisita)", "REMOCAO");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Visita removida com sucesso']);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>