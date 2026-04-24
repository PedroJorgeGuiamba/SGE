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

function removerPedido($conn, $numero)
{

    $check = $conn->prepare("SELECT id_pedido_carta FROM pedido_carta WHERE id_pedido_carta = ?");
    $check->bind_param("i", $numero);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        http_response_code(404);
        echo htmlentities(json_encode(['error' => 'Pedido não encontrado'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
        $check->close();
        return false;
    }

    $res = $conn->prepare("SELECT id_resposta FROM resposta_carta WHERE numero_carta = ?");
    $res->bind_param("i", $numero);
    $res->execute();
    $result = $res->get_result();
    $resposta_ids = [];
    while ($row = $result->fetch_assoc()) {
        $resposta_ids[] = (int)$row['id_resposta'];
    }
    $res->close();

    foreach ($resposta_ids as $id_resposta) {
        // 2. Apagar estagio ligado à resposta
        $s = $conn->prepare("DELETE FROM estagio WHERE id_resposta = ?");
        $s->bind_param("i", $id_resposta);
        $s->execute();
        $s->close();

        // 3. Buscar avaliações ligadas à resposta
        $av = $conn->prepare("SELECT id_avaliacao FROM avaliacao_estagio WHERE id_resposta = ?");
        $av->bind_param("i", $id_resposta);
        $av->execute();
        $av_result = $av->get_result();
        while ($av_row = $av_result->fetch_assoc()) {
            // 4. Apagar avaliação
            $del_av = $conn->prepare("DELETE FROM avaliacao_estagio WHERE id_avaliacao = ?");
            $del_av->bind_param("i", $av_row['id_avaliacao']);
            $del_av->execute();
            $del_av->close();
        }
        $av->close();

        // 5. Apagar a resposta
        $del_res = $conn->prepare("DELETE FROM resposta_carta WHERE id_resposta = ?");
        $del_res->bind_param("i", $id_resposta);
        $del_res->execute();
        $del_res->close();
    }

    // 6. Apagar visitas ligadas ao pedido
    $vis = $conn->prepare("DELETE FROM visita_estagio WHERE id_pedido_carta = ?");
    $vis->bind_param("i", $numero);
    $vis->execute();
    $vis->close();

    // 7. Apagar credenciais ligadas ao pedido
    $cred = $conn->prepare("DELETE FROM credencial_estagio WHERE id_pedido_carta = ?");
    $cred->bind_param("i", $numero);
    $cred->execute();
    $cred->close();

    // 8. Finalmente apagar o pedido
    $del = $conn->prepare("DELETE FROM pedido_carta WHERE id_pedido_carta = ?");
    $del->bind_param("i", $numero);
    $del->execute();
    $affected = $del->affected_rows;
    $del->close();

    if ($affected > 0 && isset($_SESSION['sessao_id'])) {
        registrarAtividade($_SESSION['sessao_id'], "Pedido de estágio removido (ID: $numero)", "REMOCAO");
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
} elseif (isset($inputData['id_pedido_carta']) && (int)$inputData['id_pedido_carta'] > 0) {
    $ids = [$inputData['id_pedido_carta']];
}

$ids = array_map('intval', $ids);
$ids = array_filter($ids, function ($id) {
    return $id > 0;
});

if (empty($ids)) {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Nenhum ID de Pedido de estágio foi fornecido. Dados recebidos: ' . json_encode($inputData)], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    $conn->close();
    exit;
}

$conn->begin_transaction();

try {
    $removidos = [];
    $falhas = [];

    foreach ($ids as $id) {
        if (removerPedido($conn, $id)) {
            $removidos[] = $id;
        } else {
            $falhas[] = $id;
        }
    }

    if (!empty($removidos)) {
        $conn->commit();
        $message = count($removidos) . " Pedido de estágio(s) removida(s) com sucesso.";
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
            'error' => 'Nenhum Pedido de estágio foi removida. IDs: ' . implode(', ', $ids),
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
