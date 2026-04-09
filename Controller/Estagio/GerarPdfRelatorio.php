<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Conexao/conector.php';

// ── Segurança básica ──────────────────────────────────────────────────────────
if (empty($_SESSION['usuario_id'])) {
    http_response_code(403);
    exit('Acesso não autorizado.');
}

// ── Parâmetros de filtro ──────────────────────────────────────────────────────
$filtro_periodo = $_GET['periodo'] ?? 'anual';
$ano_filtro     = (int)($_GET['ano'] ?? date('Y'));
$mes_filtro     = (int)($_GET['mes'] ?? date('m'));

$months   = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
$meses_pt = [
    1=>'Janeiro', 2=>'Fevereiro', 3=>'Março',    4=>'Abril',
    5=>'Maio',    6=>'Junho',     7=>'Julho',     8=>'Agosto',
    9=>'Setembro',10=>'Outubro', 11=>'Novembro', 12=>'Dezembro',
];

$titulo_periodo = $filtro_periodo === 'mensal'
    ? $meses_pt[$mes_filtro] . ' de ' . $ano_filtro
    : 'Ano ' . $ano_filtro;

// ── Ligação à BD ──────────────────────────────────────────────────────────────
$conector = new Conector();
$conn     = $conector->getConexao();

// ── Filtros SQL corrigidos ───────────────────────────────────────────────────
$filtro_ano_cartas = "YEAR(p.data_do_pedido) = $ano_filtro";
$filtro_ano_cred   = "YEAR(c.data_do_pedido) = $ano_filtro";

$filtro_mes_sql_cartas = '';
$filtro_mes_sql_cred   = '';

if ($filtro_periodo === 'mensal') {
    $filtro_mes_sql_cartas = " AND MONTH(p.data_do_pedido) = $mes_filtro";
    $filtro_mes_sql_cred   = " AND MONTH(c.data_do_pedido) = $mes_filtro";
}

// ── Consultas de totais (por dia ou por mês) ─────────────────────────────────
if ($filtro_periodo === 'mensal') {
    // Por dia
    $cartas_q = mysqli_query($conn,
        "SELECT DAY(data_do_pedido) AS key_val, COUNT(*) AS count
         FROM pedido_carta
         WHERE YEAR(data_do_pedido) = $ano_filtro 
           AND MONTH(data_do_pedido) = $mes_filtro
         GROUP BY DAY(data_do_pedido) ORDER BY key_val");

    $cred_q = mysqli_query($conn,
        "SELECT DAY(data_do_pedido) AS key_val, COUNT(*) AS count
         FROM credencial_estagio
         WHERE YEAR(data_do_pedido) = $ano_filtro 
           AND MONTH(data_do_pedido) = $mes_filtro
         GROUP BY DAY(data_do_pedido) ORDER BY key_val");
} else {
    // Por mês
    $cartas_q = mysqli_query($conn,
        "SELECT MONTH(data_do_pedido) AS key_val, COUNT(*) AS count
         FROM pedido_carta
         WHERE YEAR(data_do_pedido) = $ano_filtro
         GROUP BY MONTH(data_do_pedido) ORDER BY key_val");

    $cred_q = mysqli_query($conn,
        "SELECT MONTH(data_do_pedido) AS key_val, COUNT(*) AS count
         FROM credencial_estagio
         WHERE YEAR(data_do_pedido) = $ano_filtro
         GROUP BY MONTH(data_do_pedido) ORDER BY key_val");
}

// Construir arrays indexados
if ($filtro_periodo === 'mensal') {
    $labels      = [];
    $cartas_data = [];
    $cred_data   = [];

    $temp_cartas = [];
    while ($row = mysqli_fetch_assoc($cartas_q)) {
        $temp_cartas[(int)$row['key_val']] = (int)$row['count'];
    }
    $temp_cred = [];
    while ($row = mysqli_fetch_assoc($cred_q)) {
        $temp_cred[(int)$row['key_val']] = (int)$row['count'];
    }

    $dias_no_mes = cal_days_in_month(CAL_GREGORIAN, $mes_filtro, $ano_filtro);
    for ($d = 1; $d <= $dias_no_mes; $d++) {
        $labels[]      = $d;
        $cartas_data[] = $temp_cartas[$d] ?? 0;
        $cred_data[]   = $temp_cred[$d]   ?? 0;
    }
} else {
    $labels      = $months;
    $cartas_data = array_fill(0, 12, 0);
    $cred_data   = array_fill(0, 12, 0);

    while ($row = mysqli_fetch_assoc($cartas_q)) {
        $cartas_data[(int)$row['key_val'] - 1] = (int)$row['count'];
    }
    while ($row = mysqli_fetch_assoc($cred_q)) {
        $cred_data[(int)$row['key_val'] - 1] = (int)$row['count'];
    }
}

$total_cartas      = array_sum($cartas_data);
$total_credenciais = array_sum($cred_data);

// Distribuição por qualificação — Cartas
$qual_c_q = mysqli_query($conn,
    "SELECT q.descricao, COUNT(*) AS count
     FROM pedido_carta p
     JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
     WHERE $filtro_ano_cartas $filtro_mes_sql_cartas
     GROUP BY q.descricao ORDER BY count DESC");

// Distribuição por qualificação — Credenciais
$qual_cr_q = mysqli_query($conn,
    "SELECT q.descricao, COUNT(*) AS count
     FROM credencial_estagio c
     JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
     JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
     WHERE $filtro_ano_cred $filtro_mes_sql_cred
     GROUP BY q.descricao ORDER BY count DESC");

// Processar resultados
$cartas_qual_labels = []; $cartas_qual_data = [];
if ($qual_c_q) {
    while ($row = mysqli_fetch_assoc($qual_c_q)) {
        $cartas_qual_labels[] = $row['descricao'];
        $cartas_qual_data[]   = (int)$row['count'];
    }
}

$credenciais_qual_labels = []; $credenciais_qual_data = [];
if ($qual_cr_q) {
    while ($row = mysqli_fetch_assoc($qual_cr_q)) {
        $credenciais_qual_labels[] = $row['descricao'];
        $credenciais_qual_data[]   = (int)$row['count'];
    }
}

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
    'credenciais_qual_labels'=> $credenciais_qual_labels,
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

// No Windows/XAMPP use python (não python3)
$python_bin = 'python';   // ou 'C:\\Python312\\python.exe' se necessário

$cmd = escapeshellcmd($python_bin) . ' ' 
     . escapeshellarg($python_script) 
     . ' < ' . escapeshellarg($json_file) 
     . ' 2>&1';

$output_cmd = shell_exec($cmd);

// Limpeza
@unlink($json_file);

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