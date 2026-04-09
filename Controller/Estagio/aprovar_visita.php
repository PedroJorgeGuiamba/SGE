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
        UPDATE visita_estagio
        SET status = 'Aprovado' 
        WHERE id_visita = ?
    ");
    $stmt->bind_param('i', $idPedidoVisita);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new RuntimeException("Visita não encontrada");
    }
    $stmt->close();

    // Notifica o formando
    // $stmtFormando = $conn->prepare("
    //     SELECT f.usuario_id 
    //     FROM pedido_carta p
    //     JOIN formando f ON f.codigo = p.codigo_formando
    //     WHERE p.id_visita = ?
    // ");
    // $stmtFormando->bind_param('i', $idPedidoVisita);
    // $stmtFormando->execute();
    // $formando = $stmtFormando->get_result()->fetch_assoc();
    // $stmtFormando->close();

    // if ($formando && $formando['usuario_id']) {
    //     $mensagem = "O seu pedido de visita de estágio foi aprovado.";
    //     $stmtNotif = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
    //     $stmtNotif->bind_param('is', $formando['usuario_id'], $mensagem);
    //     $stmtNotif->execute();
    //     $stmtNotif->close();
    // }

    if (isset($_SESSION['sessao_id'])) {
        registrarAtividade($_SESSION['sessao_id'], "Visita de estágio aprovada (pedido #$idPedidoVisita)", "ACTUALIZACAO");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Visita aprovada com sucesso']);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
