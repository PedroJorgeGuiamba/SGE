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

function removerVisita($conn, $idPedidoVisita)
{

    $checkStmt = $conn->prepare("SELECT id_visita FROM visita_estagio WHERE id_visita = ?");
    $checkStmt->bind_param('i', $idPedidoVisita);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $existe = $checkResult->num_rows > 0;
    $checkStmt->close();

    if (!$existe) {
        error_log("Visita ID $idPedidoVisita não encontrada");
        return false;
    }

    $stmt = $conn->prepare("DELETE FROM visita_estagio WHERE id_visita = ?");
    $stmt->bind_param('i', $idPedidoVisita);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    error_log("DELETE affected_rows para ID $idPedidoVisita: " . $affected);

    if ($affected > 0 && isset($_SESSION['sessao_id'])) {
        registrarAtividade($_SESSION['sessao_id'], "Visita de estágio removida (ID: $idPedidoVisita)", "REMOCAO");
    }

    return $affected > 0;
}

$conexao = new Conector();
$conn = $conexao->getConexao();

$inputData = $_POST;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);
    if ($jsonData !== null) {
        $inputData = $jsonData;
        error_log("Dados JSON decodificados: " . json_encode($inputData));
    }
}

$ids = [];

if (isset($inputData['ids']) && is_array($inputData['ids']) && !empty($inputData['ids'])) {
    $ids = $inputData['ids'];
} elseif (isset($inputData['ids']) && is_string($inputData['ids']) && !empty($inputData['ids'])) {
    $ids = [$inputData['ids']];
} elseif (isset($inputData['id_visita']) && (int)$inputData['id_visita'] > 0) {
    $ids = [$inputData['id_visita']];
}

$ids = array_map('intval', $ids);
$ids = array_filter($ids, function ($id) {
    return $id > 0;
});

if (empty($ids)) {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Nenhum ID de visita foi fornecido. Dados recebidos: ' . json_encode($inputData)], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    $conn->close();
    exit;
}

$conn->begin_transaction();

try {
    $removidos = [];
    $falhas = [];

    foreach ($ids as $id) {
        if (removerVisita($conn, $id)) {
            $removidos[] = $id;
        } else {
            $falhas[] = $id;
        }
    }

    if (!empty($removidos)) {
        $conn->commit();
        $message = count($removidos) . " visita(s) removida(s) com sucesso.";
        if (!empty($falhas)) {
            $message .= " Falha nos IDs: " . implode(', ', $falhas);
        }

        echo htmlentities(json_encode([
            'success' => true,
            'message' => $message,
            'debug' => [
                'removidos' => $removidos,
                'falhas' => $falhas,
                'total_enviados' => count($ids)
            ]
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    } else {
        $conn->rollback();
        echo htmlentities(json_encode([
            'success' => false,
            'error' => 'Nenhuma visita foi removida. IDs: ' . implode(', ', $ids),
            'debug' => [
                'removidos' => $removidos,
                'falhas' => $falhas,
                'total_enviados' => count($ids)
            ]
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    }
} catch (Exception $e) {
    $conn->rollback();
    echo htmlentities(json_encode([
        'success' => false,
        'error' => 'Erro interno: ' . $e->getMessage()
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
}

$conn->close();
exit;
