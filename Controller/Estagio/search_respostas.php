<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conexao = new Conector();
$conn = $conexao->getConexao();

$pesquisa = trim($_GET['numero'] ?? '');

if ($pesquisa === '') {
    $sql = "
        SELECT
            rc.*,
            pc.numero AS numero_pedido
        FROM resposta_carta rc
        LEFT JOIN pedido_carta pc ON rc.numero_carta = pc.id_pedido_carta
        ORDER BY rc.id_resposta DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT
            rc.*,
            pc.numero AS numero_pedido
        FROM resposta_carta rc
        LEFT JOIN pedido_carta pc ON rc.numero_carta = pc.id_pedido_carta
        WHERE rc.numero_carta = ? OR pc.numero = ?
        ORDER BY rc.id_resposta DESC
    ";

    $stmt = $conn->prepare($sql);
    $pesquisaInt = (int) $pesquisa;
    $stmt->bind_param("ii", $pesquisaInt, $pesquisaInt);
    $stmt->execute();
    $result = $stmt->get_result();
}

$respostas = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $respostas[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($respostas);

$conn->close();
?>
