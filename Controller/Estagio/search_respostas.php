<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conexao = new Conector();
$conn = $conexao->getConexao();

$pesquisa = $_GET['numero'] ?? '';

// Se estiver vazio, retorna todas as respostas
if (empty($pesquisa)) {
    $sql = "SELECT * FROM resposta_carta";
    $result = $conn->query($sql);
} else {
    // Corrigido: usar LIKE apenas para strings, para números usar =
    $sql = "SELECT * FROM resposta_carta WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pesquisa);
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