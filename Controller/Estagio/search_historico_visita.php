<?php
require_once __DIR__ . '/../../Conexao/conector.php';
session_start();
$conexao = new Conector();
$conn    = $conexao->getConexao();
$termo = trim($_GET['termo'] ?? '');

// --- Filtro por qualificação do supervisor ---
$filtroQualificacao = "";

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

// --- Queries ---
if ($termo === '') {
    if($_SESSION['role'] && $_SESSION['role'] !== 'supervisor'){
        $sql = "
            SELECT v.*, p.qualificacao, q.descricao AS qualificacao_descricao
            FROM visita_estagio v
            JOIN pedido_carta p ON v.id_pedido_carta = p.id_pedido_carta
            LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            ORDER BY v.id_visita DESC
        ";
    }else{
        $sql = "
            SELECT v.*, p.qualificacao, q.descricao AS qualificacao_descricao
            FROM visita_estagio v
            JOIN pedido_carta p ON v.id_pedido_carta = p.id_pedido_carta
            LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            WHERE 1=1 $filtroQualificacao
            ORDER BY v.id_visita DESC
        ";
    }
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT v.*, p.qualificacao, q.descricao AS qualificacao_descricao
        FROM visita_estagio v
        JOIN pedido_carta p ON v.id_pedido_carta = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        WHERE 1=1 $filtroQualificacao
            AND (v.empresa LIKE ? OR v.nome LIKE ? OR v.apelido LIKE ?)
        ORDER BY v.id_visita DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
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