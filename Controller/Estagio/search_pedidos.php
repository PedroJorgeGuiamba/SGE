<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conexao = new Conector();
$conn = $conexao->getConexao();

$empresa = $_GET['empresa'] ?? '';
$empresa = trim($empresa);

// Se estiver vazio, retorna todos os pedidos
if ($empresa === '') {
    $sql = "SELECT * FROM pedido_carta";
    $result = $conn->query($sql);
}
else {
    $sql = "SELECT * FROM pedido_carta WHERE empresa LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $empresa . '%';
    $stmt->bind_param("s", $searchTerm);
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