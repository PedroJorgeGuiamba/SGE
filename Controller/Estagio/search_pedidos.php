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
        LEFT JOIN turma t ON p.codigo_turma = t.codigo
        WHERE $filtroBase $filtroAdicional
        ORDER BY p.id_pedido_carta DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT p.*, q.descricao AS qualificacao_descricao, t.nome AS turma
        FROM pedido_carta p
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON p.codigo_turma = t.codigo
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
    $pedidos[] = [
        'id_pedido_carta' => $row['id_pedido_carta'],
        'numero' => $row['numero'],
        'nome' => $row['nome'],
        'apelido' => $row['apelido'],
        'codigo_formando' => $row['codigo_formando'],
        'qualificacao_descricao' => $row['qualificacao_descricao'],
        'qualificacao' => $row['qualificacao'],
        'turma' => $row['turma'],
        'data_do_pedido' => $row['data_do_pedido'],
        'hora_do_pedido' => $row['hora_do_pedido'],
        'empresa' => $row['empresa'],
        'contactoPrincipal' => $criptografia->descriptografar($row['contactoPrincipal']),
        'contactoSecundario' => $criptografia->descriptografar($row['contactoSecundario']),
        'email' => $criptografia->descriptografar($row['email'])
    ];
}

header('Content-Type: application/json');
echo json_encode($pedidos);
$conn->close();
