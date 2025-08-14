<?php
session_start();
require_once __DIR__ . '/../../../Conexao/conector.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $conexao = new Conector();
    $conn = $conexao->getConexao();
    
    $sql = "DELETE FROM pedido_carta WHERE numero = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Pedido removido com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao remover pedido: ' . $conn->error]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida']);
}
?>