<?php
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php';

ob_clean();

$conexao = new Conector();
$conn = $conexao->getConexao();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$numero = isset($_POST['id_pedido_carta']) ? (int)$_POST['id_pedido_carta'] : null;

if (!$numero) {
    http_response_code(400);
    echo json_encode(['error' => 'Número do pedido não fornecido']);
    exit;
}
$check = $conn->prepare("SELECT id_pedido_carta FROM pedido_carta WHERE id_pedido_carta = ?");
$check->bind_param("i", $numero);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Pedido não encontrado']);
    $check->close();
    exit;
}
$check->close();

$conn->begin_transaction();

try {
    // 1. Buscar IDs das respostas ligadas a este pedido
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
    $del->close();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Pedido #' . $numero . ' e todos os registos associados foram removidos com sucesso'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'error'   => 'Erro ao remover o pedido',
        'detalhe' => $e->getMessage()
    ]);
}

$conn->close();
?>