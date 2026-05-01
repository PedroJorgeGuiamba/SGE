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

// --- Filtro por qualificação do supervisor ---
$filtroBase = "AND c.data_de_levantamento IS NULL";
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
    $sql = "
        SELECT c.*, p.qualificacao, q.descricao AS qualificacao_descricao
        FROM credencial_estagio c
        JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        WHERE 1=1 $filtroBase $filtroQualificacao
        ORDER BY c.id_credencial DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT c.*, p.qualificacao, q.descricao AS qualificacao_descricao
        FROM credencial_estagio c
        JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        WHERE 1=1 $filtroBase $filtroQualificacao
            AND (c.empresa LIKE ? OR c.nome LIKE ? OR c.apelido LIKE ? OR c.email LIKE ?)
        ORDER BY c.id_credencial DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = [
        'id_credencial' => $row['id_credencial'],
        'nome' => $row['nome'],
        'apelido' => $row['apelido'],
        'codigo_formando' => $row['codigo_formando'],
        'contactoFormando' => $criptografia->descriptografar($row['contactoFormando']),
        'email' => $criptografia->descriptografar($row['email']),
        'empresa' => $row['empresa'],
        'data_do_pedido' => $row['data_do_pedido'],
        'id_pedido_carta' => $row['id_pedido_carta'],
        'carta_path'      => $row['carta_path'] ?? null
    ];
}

header('Content-Type: application/json');
echo json_encode($pedidos);
$conn->close();
