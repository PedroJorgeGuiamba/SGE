<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$conexao = new Conector();
$conn    = $conexao->getConexao();
$criptografia = new Criptografia();

$termo = trim($_GET['termo'] ?? '');

if ($termo === '') {
    $sql = "
        SELECT s.*, q.descricao AS qualificacao_descricao
        FROM supervisor s
        LEFT JOIN qualificacao q ON s.id_qualificacao = q.id_qualificacao
        ORDER BY s.id_supervisor DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT s.*, q.descricao AS qualificacao_descricao
        FROM supervisor s
        LEFT JOIN qualificacao q ON s.qualificacao = q.id_qualificacao
        WHERE
            (s.area LIKE ?)
        ORDER BY s.id_supervisor DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$supervisor = [];
while ($row = $result->fetch_assoc()) {
    $supervisor[] = [
        'id_supervisor' => $row['id_supervisor'],
        'nome' => $row['nome_supervisor'],
        'qualificacao' => $row['qualificacao_descricao'],
        'area' => $row['area']
    ];
}

header('Content-Type: application/json');
echo json_encode($supervisor);
$conn->close();
