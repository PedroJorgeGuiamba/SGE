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
        SELECT c.*, q.descricao AS qualificacao_descricao
        FROM curso c
        LEFT JOIN qualificacao q ON c.codigo_qualificacao = q.id_qualificacao
        ORDER BY c.id_curso DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT c.*, q.descricao AS qualificacao_descricao
        FROM curso c
        LEFT JOIN qualificacao q ON c.codigo_qualificacao = q.id_qualificacao
        AND (c.nome LIKE ? OR c.descricao LIKE ? OR c.sigla LIKE ?)
        ORDER BY c.id_curso DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$cursos = [];
while ($row = $result->fetch_assoc()) {
    $cursos[] = [
        'id_curso' => $row['id_curso'],
        'codigo' => $row['codigo'],
        'nome' => $row['nome'],
        'descricao' => $row['descricao'],
        'sigla' => $row['sigla'],
        'qualificacao_descricao' => $row['qualificacao_descricao']
    ];
}

header('Content-Type: application/json');
echo json_encode($cursos);
$conn->close();
