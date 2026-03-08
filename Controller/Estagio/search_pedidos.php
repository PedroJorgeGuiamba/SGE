<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conexao = new Conector();
$conn = $conexao->getConexao();

$termo = $_GET['termo'] ?? '';
$termo = trim($termo);

// Se estiver vazio, retorna todos os pedidos
if ($termo === '') {
    $sql    = "SELECT p.*, q.descricao AS qualificacao_descricao, t.nome AS turma
                FROM pedido_carta p
                LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
                LEFT JOIN turma t ON t.codigo_qualificacao = q.id_qualificacao
                ORDER BY p.id_pedido_carta DESC";
    $result = $conn->query($sql);
} else {
    $sql    = "SELECT p.*, q.descricao AS qualificacao_descricao, t.nome AS turma
                FROM pedido_carta p
                LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
                LEFT JOIN turma t ON  p.qualificacao = t.codigo_qualificacao
                WHERE p.empresa LIKE ? OR p.nome LIKE ? OR p.apelido LIKE ? OR p.email LIKE ?
                ORDER BY p.id_pedido_carta DESC";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($pedidos);

$conn->close();
?>