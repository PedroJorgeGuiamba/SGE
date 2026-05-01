<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php';

header('Content-Type: application/json');

$conexao = new Conector();
$conn = $conexao->getConexao();
$conn->begin_transaction();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Método inválido'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    exit;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'supervisor'])) {
    echo htmlentities(json_encode(['success' => false, 'error' => 'Sem permissão'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    exit;
}

if (isset($_POST['numeros']) && is_array($_POST['numeros'])) {
    $ids = array_filter(array_map('intval', $_POST['numeros']));
    if (empty($ids)) {
        header('HTTP/1.1 400 Bad Request');
        echo htmlentities(json_encode(['error' => 'Números das respostas não fornecidos ou inválidos'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
        exit;
    }

    $ids = array_map('intval', $ids);
    $ids = array_filter($ids);
    
    if (empty($ids)) {
        return ['success' => false, 'error' => 'IDs inválidos'];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM resposta_carta WHERE id_resposta IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    // $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    if ($stmt->execute()) {
        echo htmlentities(json_encode(['success' => true, 'message' => 'Respostas removidas com sucesso'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo htmlentities(json_encode(['error' => 'Erro ao remover as respostas'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    }
    $stmt->close();
} elseif (isset($_POST['numero'])) {
    $numero = (int)$_POST['numero'];
    if ($numero < 0) {
        header('HTTP/1.1 400 Bad Request');
        echo htmlentities(json_encode(['error' => 'Número da resposta inválido'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    }

    $sql = "DELETE FROM resposta_carta WHERE id_resposta = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $numero);
    if (!$stmt->execute()) {
        header('HTTP/1.1 500 Internal Server Error');
        echo htmlentities(json_encode(['error' => 'Erro ao remover a resposta'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    }

    $conn->commit();
    echo htmlentities(json_encode(['success' => true, 'message' => 'Resposta removida com sucesso'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
    $stmt->close();
} else {
    header('HTTP/1.1 400 Bad Request');
    echo htmlentities(json_encode(['error' => 'Número da resposta não fornecido'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT));
}

$conn->close();
