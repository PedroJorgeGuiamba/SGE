<?php
require_once __DIR__ . '/../../Conexao/conector.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conexao = new Conector();
$conn    = $conexao->getConexao();
$termo = trim($_GET['termo'] ?? '');

// --- Queries ---
if ($termo === '') {
    $sql = "
        SELECT q.*
        FROM qualificacao q
        ORDER BY q.id_qualificacao DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT q.*
        FROM qualificacao q
        AND (q.nivel LIKE ?)
        ORDER BY q.id_qualificacao DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$qualificacao = [];
while ($row = $result->fetch_assoc()) {
    $qualificacao[] = [
        'id_qualificacao' => $row['id_qualificacao'],
        'qualificacao' => $row['qualificacao'],
        'descricao' => $row['descricao'],
        'nivel' => $row['nivel']
    ];
}

header('Content-Type: application/json');
echo json_encode($qualificacao);
$conn->close();
