<?php
require_once __DIR__ . '/../../Conexao/conector.php';
session_start();

$conexao = new Conector();
$conn    = $conexao->getConexao();

$termo = trim($_GET['termo'] ?? '');

$filtroBase = "p.data_de_levantamento IS NULL";

// --- Filtro por qualificação do supervisor ---
$filtroAdicional = "";
// $qualificacaoId     = null;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'supervisor' && isset($_SESSION['usuario_id'])) {
    $userId = (int) $_SESSION['usuario_id'];

    $stmtSup = $conn->prepare("
        SELECT id_qualificacao 
        FROM supervisor 
        WHERE usuario_id = ?
    ");
    $stmtSup->bind_param("i", $userId);
    $stmtSup->execute();
    $resSup = $stmtSup->get_result();

    $qualificacaoIds = [];
    while ($row = $resSup->fetch_assoc()) {
        if ($row['id_qualificacao']) {
            $qualificacaoIds[] = (int) $row['id_qualificacao'];
        }
    }
    $stmtSup->close();

    if (!empty($qualificacaoIds)) {
        $placeholders        = implode(',', $qualificacaoIds);
        $filtroQualificacao  = "AND p.qualificacao IN ($placeholders)";
    }
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'formando' && isset($_SESSION['usuario_id'])) {
    $userId = (int) $_SESSION['usuario_id'];
    $codigoFormando = (int) $_SESSION['codigo_formando'];

    $filtroAdicional = "AND codigo_formando = $codigoFormando";
    
}

if ($termo === '') {
    $sql = "
        SELECT p.*, q.descricao AS qualificacao_descricao, t.nome AS turma
        FROM pedido_carta p
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON t.codigo_qualificacao = q.id_qualificacao
        WHERE $filtroBase $filtroAdicional
        ORDER BY p.id_pedido_carta DESC
    ";
    $result = $conn->query($sql);

} else {
    $sql = "
        SELECT p.*, q.descricao AS qualificacao_descricao, t.nome AS turma
        FROM pedido_carta p
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON p.qualificacao = t.codigo_qualificacao
        WHERE $filtroBase $filtroAdicional
          AND (p.empresa LIKE ? OR p.nome LIKE ? OR p.apelido LIKE ? OR p.email LIKE ?)
        ORDER BY p.id_pedido_carta DESC
    ";
    $stmt       = $conn->prepare($sql);
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