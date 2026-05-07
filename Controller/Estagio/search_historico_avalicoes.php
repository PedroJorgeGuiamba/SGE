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
$filtroBase = "AND a.resultado IS NOT NULL";
$filtroAdicional = "";
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

if (isset($_SESSION['role']) && $_SESSION['role'] === 'formando' && isset($_SESSION['usuario_id'])) {
    $userId = (int) $_SESSION['usuario_id'];
    $codigoFormando = (int) $_SESSION['codigo_formando'];

    $filtroAdicional = "AND a.codigo_formando = $codigoFormando";
}

// --- Queries ---
if ($termo === '') {
    $sql = "
        SELECT a.*, p.nome as nome, p.apelido as apelido, p.qualificacao, q.descricao AS qualificacao_descricao, t.nome as turma_nome
        FROM avaliacao_estagio a
        JOIN pedido_carta p ON a.id_pedido_estagio = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON a.turma = t.codigo
        WHERE 1=1 $filtroBase $filtroQualificacao $filtroAdicional
        ORDER BY a.id_avaliacao DESC
    ";
    $result = $conn->query($sql);
} else {
    $sql = "
        SELECT a.*, p.nome as nome, p.apelido as apelido, p.qualificacao, q.descricao AS qualificacao_descricao, t.nome as turma_nome
        FROM avaliacao_estagio a
        JOIN pedido_carta p ON a.id_pedido_estagio = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON a.turma = t.codigo
        WHERE 1=1 $filtroBase $filtroQualificacao $filtroAdicional
            AND (a.empresa LIKE ? OR a.codigo_formando LIKE ? OR a.empresa LIKE ?)
        ORDER BY a.id_avaliacao DESC
    ";
    $stmt       = $conn->prepare($sql);
    $searchTerm = '%' . $termo . '%';
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

$pedidos = [];
while ($row = $result->fetch_assoc()) {
    $pedidos[] = [
        'id_avaliacao' => $row['id_avaliacao'],
        'nome' => $row['nome'],
        'apelido' => $row['apelido'],
        'codigo_formando' => $row['codigo_formando'],
        'empresa' => $row['empresa'],
        'qualificacao_descricao' => $row['qualificacao_descricao'],
        'turma' => $row['turma_nome'],
        'ano_turma' => $row['ano_turma'],
        'doc_path' => $row['doc_path'],
        'resultado' => $row['resultado'],
        'comentario' => $row['comentario']
    ];
}

header('Content-Type: application/json');
echo json_encode($pedidos);
$conn->close();
