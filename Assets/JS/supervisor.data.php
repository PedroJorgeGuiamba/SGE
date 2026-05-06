<?php
session_start();
header('Content-Type: application/javascript');

require_once __DIR__ . '/../../Conexao/conector.php';
$conector = new Conector();
$conn = $conector->getConexao();

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

$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

$pedidos_monthly_query_per_qualification = mysqli_query($conn, "
    SELECT MONTH(p.data_do_pedido) as month,
                q.descricao as qualificacao_desc,
                COUNT(*) as count
                FROM pedido_carta p
                JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
                WHERE YEAR(p.data_do_pedido) = YEAR(CURDATE()) $filtroQualificacao GROUP BY MONTH(p.data_do_pedido), q.descricao");
$pedidos_monthly_per_qualification = [];
if ($pedidos_monthly_query_per_qualification) {
    while ($row = mysqli_fetch_assoc($pedidos_monthly_query_per_qualification)) {
        $qual = $row['qualificacao_desc'];
        if (!isset($pedidos_monthly_per_qualification[$qual])) {
            $pedidos_monthly_per_qualification[$qual] = array_fill(0, 12, 0);
        }
        $pedidos_monthly_per_qualification[$qual][$row['month'] - 1] = $row['count'];
    }
}
$qualifications = array_keys($pedidos_monthly_per_qualification);
$pedidos_per_qual_json = $pedidos_monthly_per_qualification;

$pedidos_year_query_per_qualification = mysqli_query($conn, "
    SELECT YEAR(p.data_do_pedido) as year,
           q.descricao as qualificacao_desc,
           COUNT(*) as count
    FROM pedido_carta p
    JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    WHERE YEAR(p.data_do_pedido) BETWEEN YEAR(CURDATE()) - 4 AND YEAR(CURDATE())
    $filtroQualificacao
    GROUP BY YEAR(p.data_do_pedido), q.descricao
    ORDER BY year ASC
");

$pedidos_year_per_qualification = [];
$years_list = [];

if ($pedidos_year_query_per_qualification) {
    while ($row = mysqli_fetch_assoc($pedidos_year_query_per_qualification)) {
        $qual = $row['qualificacao_desc'];
        $year = $row['year'];

        if (!in_array($year, $years_list)) {
            $years_list[] = $year;
        }
        if (!isset($pedidos_year_per_qualification[$qual])) {
            $pedidos_year_per_qualification[$qual] = [];
        }
        $pedidos_year_per_qualification[$qual][$year] = $row['count'];
    }

    foreach ($pedidos_year_per_qualification as $qual => &$yearData) {
        $filled = [];
        foreach ($years_list as $y) {
            $filled[] = $yearData[$y] ?? 0;
        }
        $yearData = $filled;
    }
    unset($yearData);
}

$pedidos_year_per_qual_json = $pedidos_year_per_qualification;
$years_list_json = array_values($years_list);

echo "// Dados gerados pelo PHP para os gráficos\n";
echo "window.supervisorData = " . json_encode([
    'qualifications' => $qualifications,
    'pedidos_per_qual_json' => $pedidos_per_qual_json,
    'months' => $months,
    'pedidos_year_per_qual_json' => $pedidos_year_per_qual_json,
    'years_list_json' => $years_list_json,
]) . ";\n";

echo "console.log('Dados carregados');\n";