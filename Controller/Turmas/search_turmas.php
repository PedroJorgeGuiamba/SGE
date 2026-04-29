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
        SELECT t.*, q.descricao AS qualificacao_descricao, c.nome AS curso
        FROM turma t
        LEFT JOIN qualificacao q ON t.codigo_qualificacao = q.id_qualificacao
        LEFT JOIN curso c ON t.codigo_curso = c.codigo
        ORDER BY t.codigo DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT t.*, q.descricao AS qualificacao_descricao, c.nome AS curso
        FROM turma t
        LEFT JOIN qualificacao q ON t.codigo_qualificacao = q.id_qualificacao
        LEFT JOIN curso c ON t.codigo_curso = c.codigo
        AND (t.nome LIKE ? OR t.descricao LIKE ? OR t.sigla LIKE ?)
        ORDER BY t.codigo DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$turmas = [];
while ($row = $result->fetch_assoc()) {
    $turmas[] = [
        'codigo' => $row['codigo'],
        'nome' => $row['nome'],
        'curso' => $row['curso'],
        'qualificacao_descricao' => $row['qualificacao_descricao']
    ];
}

header('Content-Type: application/json');
echo json_encode($turmas);
$conn->close();
