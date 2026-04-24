<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Actividade.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Método inválido'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    exit;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Sem permissão'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    exit;
}

$idPedidoVisita = isset($_POST['id_visita']) ? (int) $_POST['id_visita'] : 0;
if (!$idPedidoVisita) {
    echo htmlentities(json_encode(['success' => false, 'error' => 'ID inválido'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    exit;
}

$conexao = new Conector();
$conn = $conexao->getConexao();
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("
        UPDATE visita_estagio 
        SET status = 'Recusado' 
        WHERE id_visita = ?
    ");
    $stmt->bind_param('i', $idPedidoVisita);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new RuntimeException("Visita não encontrada");
    }
    $stmt->close();

    // Notifica o formando
    $stmtFormando = $conn->prepare("
        SELECT f.usuario_id, p.id_pedido_carta
        FROM formando f
        JOIN pedido_carta p ON p.codigo_formando = f.codigo
        JOIN visita_estagio v ON v.id_pedido_carta = p.id_pedido_carta
        WHERE v.id_visita = ?
    ");
    $stmtFormando->bind_param('i', $idPedidoVisita);
    $stmtFormando->execute();
    $formando = $stmtFormando->get_result()->fetch_assoc();
    $stmtFormando->close();

    if ($formando && $formando['usuario_id']) {
        $mensagem = "O seu pedido de visita de estágio foi recusado.";
        $stmtNotif = $conn->prepare("INSERT INTO notificacao (id_utilizador, mensagem) VALUES (?, ?)");
        $stmtNotif->bind_param('is', $formando['usuario_id'], $mensagem);
        $stmtNotif->execute();
        $stmtNotif->close();
    }

    if (!empty($_SESSION['sessao_id'])) {
        registrarAtividade($_SESSION['sessao_id'], "Visita de estágio recusada (pedido #$idPedidoVisita)", "ACTUALIZACAO");
    }

    $conn->commit();
    echo htmlentities(json_encode(['success' => true, 'message' => 'Visita recusada com sucesso'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
} catch (Throwable $e) {
    $conn->rollback();
    echo htmlentities(json_encode(['success' => false, 'error' => $e->getMessage()], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
}
$conn->close();
