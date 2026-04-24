<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SafeUnlink.php';

if (empty($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit('Acesso não autorizado.');
}

$filtro_periodo = $_GET['periodo'] ?? 'anual';
$ano_filtro     = (int)($_GET['ano'] ?? date('Y'));
$mes_filtro     = (int)($_GET['mes'] ?? date('m'));

$months   = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$meses_pt = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro',
];

$titulo_periodo = $filtro_periodo === 'mensal'
    ? $meses_pt[$mes_filtro] . ' de ' . $ano_filtro
    : 'Ano ' . $ano_filtro;

$conector = new Conector();
$conn     = $conector->getConexao();
$safe = new SafeUnlink();

// ── Consultas de totais (por dia ou por mês) ─────────────────────────────────
if ($filtro_periodo === 'mensal') {
    // Por dia
    $cartas_q =
        "SELECT DAY(data_do_pedido) AS key_val, COUNT(*) AS count
        FROM pedido_carta
        WHERE YEAR(data_do_pedido) = ? 
        AND MONTH(data_do_pedido) = ?
        GROUP BY DAY(data_do_pedido) ORDER BY key_val";

    $stmt = $conn->prepare($cartas_q);
    $stmt->bind_param('ii', $ano_filtro, $mes_filtro);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels      = [];
    $cartas_data = [];
    $temp_cartas = [];

    while ($row = $result->fetch_assoc()) {
        $temp_cartas[(int)$row['key_val']] = (int)$row['count'];
    }

    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes_filtro, $ano_filtro);
    for ($d = 1; $d <= $dias_no_mes; $d++) {
        $labels[]      = $d;
        $cartas_data[] = $temp_cartas[$d] ?? 0;
    }

    $stmt->close();

    $cred_q =
        "SELECT DAY(data_do_pedido) AS key_val, COUNT(*) AS count
            FROM credencial_estagio
            WHERE YEAR(data_do_pedido) = ?
            AND MONTH(data_do_pedido) = ?
            GROUP BY DAY(data_do_pedido) ORDER BY key_val";

    $stmt = $conn->prepare($cred_q);
    $stmt->bind_param('ii', $ano_filtro, $mes_filtro);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels      = [];
    $cred_data   = [];
    $temp_cred = [];

    while ($row = $result->fetch_assoc()) {
        $temp_cred[(int)$row['key_val']] = (int)$row['count'];
    }

    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes_filtro, $ano_filtro);
    for ($d = 1; $d <= $dias_no_mes; $d++) {
        $labels[]      = $d;
        $cred_data[]   = $temp_cred[$d]   ?? 0;
    }

    $stmt->close();
} else {
    // Por mês
    $cartas_q =
        "SELECT MONTH(data_do_pedido) AS key_val, COUNT(*) AS count
        FROM pedido_carta
        WHERE YEAR(data_do_pedido) = ?
        GROUP BY MONTH(data_do_pedido) ORDER BY key_val";

    $stmt = $conn->prepare($cartas_q);
    $stmt->bind_param('i', $ano_filtro);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels      = $months;
    $cartas_data = array_fill(0, 12, 0);

    while ($row = $result->fetch_assoc()) {
        $cartas_data[(int)$row['key_val'] - 1] = (int)$row['count'];
    }

    $stmt->close();

    $cred_q =
        "SELECT MONTH(data_do_pedido) AS key_val, COUNT(*) AS count
        FROM credencial_estagio
        WHERE YEAR(data_do_pedido) = ?
        GROUP BY MONTH(data_do_pedido) ORDER BY key_val";

    $stmt = $conn->prepare($cred_q);
    $stmt->bind_param('i', $ano_filtro);
    $stmt->execute();
    $result = $stmt->get_result();

    $labels      = $months;
    $cred_data   = array_fill(0, 12, 0);

    while ($row = $result->fetch_assoc()) {
        $cred_data[(int)$row['key_val'] - 1] = (int)$row['count'];
    }

    $stmt->close();
}

$total_cartas      = array_sum($cartas_data);
$total_credenciais = array_sum($cred_data);

$qual_c_q =
    "SELECT q.descricao, COUNT(*) AS count
    FROM pedido_carta p
    JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    WHERE 1=1";

$params = [];
$types = "";

// Filtros dinâmicos
if (!empty($ano_filtro)) {
    $qual_c_q .= " AND YEAR(p.data_do_pedido) = ?";
    $params[] = $ano_filtro;
    $types .= "i"; // integer
}

if (!empty($mes_filtro)) {
    $qual_c_q .= " AND MONTH(p.data_do_pedido) = ?";
    $params[] = $mes_filtro;
    $types .= "i";
}

$qual_c_q .= " GROUP BY q.descricao ORDER BY count DESC";
$stmt = $conn->prepare($qual_c_q);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
// Processar resultados
$cartas_qual_labels = [];
$cartas_qual_data = [];

while ($row = $result->fetch_assoc()) {
    $cartas_qual_labels[] = $row['descricao'];
    $cartas_qual_data[]   = (int)$row['count'];
}
$stmt->close();

// Distribuição por qualificação — Credenciais
$qual_cr_q =
    "SELECT q.descricao, COUNT(*) AS count
     FROM credencial_estagio c
     JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
     JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
     WHERE 1=1";

$params = [];
$types = "";

// Filtros dinâmicos
if (!empty($ano_filtro)) {
    $qual_cr_q .= " AND YEAR(c.data_do_pedido) = ?";
    $params[] = $ano_filtro;
    $types .= "i";
}

if (!empty($mes_filtro)) {
    $qual_cr_q .= " AND MONTH(c.data_do_pedido) = ?";
    $params[] = $mes_filtro;
    $types .= "i";
}

$qual_cr_q .= " GROUP BY q.descricao ORDER BY count DESC";

$stmt = $conn->prepare($qual_cr_q);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_all(MYSQLI_ASSOC);

$credenciais_qual_labels = [];
$credenciais_qual_data = [];
while ($row = $result->fetch_assoc()) {
    $credenciais_qual_labels[] = $row['descricao'];
    $credenciais_qual_data[]   = (int)$row['count'];
}


$stmt->close();
// ── Montar payload ───────────────────────────────────────────────────────────
$dados = [
    'periodo'                => $filtro_periodo,
    'ano'                    => $ano_filtro,
    'mes'                    => $mes_filtro,
    'titulo_periodo'         => $titulo_periodo,
    'labels'                 => $labels,
    'cartas_data'            => $cartas_data,
    'cred_data'              => $cred_data,
    'total_cartas'           => $total_cartas,
    'total_credenciais'      => $total_credenciais,
    'cartas_qual_labels'     => $cartas_qual_labels,
    'cartas_qual_data'       => $cartas_qual_data,
    'credenciais_qual_labels' => $credenciais_qual_labels,
    'credenciais_qual_data'  => $credenciais_qual_data,
];

// ── Caminhos e Execução Python (corrigido para Windows/XAMPP) ───────────────
$script_dir    = realpath(__DIR__ . '/../../Scripts');
$python_script = realpath($script_dir . '/gerar_relatorio_pdf.py');  // ajuste se necessário

if (!$python_script || !file_exists($python_script)) {
    http_response_code(500);
    exit("Erro: Script Python não encontrado em: " . ($python_script ?: 'caminho inválido'));
}

$tmp_dir    = sys_get_temp_dir();
$json_file  = $tmp_dir . '/relatorio_dados_' . session_id() . '.json';
$output_pdf = $tmp_dir . '/relatorio_' . session_id() . '.pdf';

file_put_contents($json_file, json_encode([
    'dados'  => $dados,
    'output' => $output_pdf,
]));

$env = parse_ini_file(__DIR__ . '/../../Config/.env');

foreach ($env as $key => $value) {
    putenv("$key=$value");
}

$python_bin = getenv("python_bin");

$cmd = escapeshellarg($python_bin) . ' '
    . escapeshellarg($python_script)
    . ' < ' . escapeshellarg($json_file)
    . ' 2>&1';

$output_cmd = shell_exec($cmd);

$temp_dir = __DIR__ . '/../../temp/';
$safe->safe_unlink($json_file, $temp_dir);

// Limpeza
// @unlink($json_file);

// ── Enviar PDF ───────────────────────────────────────────────────────────────
if (!file_exists($output_pdf) || filesize($output_pdf) < 100) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Erro ao gerar o PDF.\n\n";
    echo "Comando: " . htmlspecialchars($cmd) . "\n\n";
    echo "Saída: " . htmlspecialchars($output_cmd);
    exit;
}

$filename = 'Relatorio_Cartas_Credenciais_'
    . ($filtro_periodo === 'mensal' ? $mes_filtro . '_' : '')
    . $ano_filtro . '_'
    . date('Ymd_His') . '.pdf';

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($output_pdf));
header('Cache-Control: private, max-age=0, must-revalidate');

readfile($output_pdf);
@unlink($output_pdf);
exit;
