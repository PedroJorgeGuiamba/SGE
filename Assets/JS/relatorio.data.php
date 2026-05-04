<?php
session_start();
header('Content-Type: application/javascript');
include_once __DIR__ . '/../../Conexao/conector.php';

$conector = new Conector();
$conn = $conector->getConexao();
$userRole = $_SESSION['role'] ?? '';

// ============================================================
// CONTROLO DE QUALIFICAÇÕES POR ROLE
// ============================================================

$qualificacoes_ids      = [];
$qualificacoes_sql_in   = '';
$is_supervisor          = ($userRole === 'supervisor');

if ($is_supervisor) {
    $stmt_qual = $conn->prepare(
        "SELECT id_qualificacao FROM supervisor WHERE usuario_id = ?"
    );
    if ($stmt_qual) {
        $stmt_qual->bind_param('i', $userId);
        $stmt_qual->execute();
        $result_qual = $stmt_qual->get_result();
        while ($row = $result_qual->fetch_assoc()) {
            $qualificacoes_ids[] = (int)$row['id_qualificacao'];
        }
        $stmt_qual->close();
    }

    if (!empty($qualificacoes_ids)) {
        $placeholders         = implode(',', $qualificacoes_ids);
        $qualificacoes_sql_in = " AND q.id_qualificacao IN ($placeholders) ";
    } else {
        $qualificacoes_sql_in = " AND 1=0 ";
    }
}

function buildWhere(string $alias_data, string $alias_qual, int $ano, int $mes, string $filtro, string $qual_in): string
{
    $where = " WHERE YEAR($alias_data.data_do_pedido) = $ano";
    if ($filtro === 'mensal') {
        $where .= " AND MONTH($alias_data.data_do_pedido) = $mes";
    }

    $qual_in_alias = str_replace('q.id_qualificacao', "$alias_qual.id_qualificacao", $qual_in);
    $where .= $qual_in_alias;
    return $where;
}

// ============================================================
// FILTROS DE PERÍODO
// ============================================================
$filtro_periodo = $_GET['periodo'] ?? 'anual';
$ano_filtro     = (int)($_GET['ano'] ?? date('Y'));
$mes_filtro     = (int)($_GET['mes'] ?? date('m'));

$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

// ============================================================
// QUERIES PRINCIPAIS
// ============================================================
if ($filtro_periodo === 'mensal') {

    if ($is_supervisor && !empty($qualificacoes_ids)) {
        $placeholders = implode(',', $qualificacoes_ids);
        $sql_cartas_dia = "
            SELECT DAY(p.data_do_pedido) AS dia, COUNT(*) AS count
            FROM pedido_carta p
            JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            WHERE YEAR(p.data_do_pedido) = ?
              AND MONTH(p.data_do_pedido) = ?
              AND q.id_qualificacao IN ($placeholders)
            GROUP BY DAY(p.data_do_pedido)
            ORDER BY dia
        ";

        $stmt = $conn->prepare($sql_cartas_dia);

        $params = array_merge([$ano_filtro, $mes_filtro], $qualificacoes_ids);
        $types  = str_repeat('i', count($params));

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        $cartas_dia_labels = [];
        $cartas_dia_data   = [];

        while ($row = $result->fetch_assoc()) {
            $cartas_dia_labels[] = $row['dia'];
            $cartas_dia_data[]   = $row['count'];
        }

        $stmt->close();

        $sql_credenciais_dia = "
            SELECT DAY(c.data_do_pedido) AS dia, COUNT(*) AS count
            FROM credencial_estagio c
            JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
            JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            WHERE YEAR(c.data_do_pedido) = ?
              AND MONTH(c.data_do_pedido) = ?
              AND q.id_qualificacao IN ($placeholders)
            GROUP BY DAY(c.data_do_pedido)
            ORDER BY dia";

        $stmt = $conn->prepare($sql_credenciais_dia);

        $params = array_merge([$ano_filtro, $mes_filtro], $qualificacoes_ids);
        $types  = str_repeat('i', count($params));

        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        $credenciais_dia_labels = [];
        $credenciais_dia_data   = [];

        while ($row = $result->fetch_assoc()) {
            $credenciais_dia_labels[] = $row['dia'];
            $credenciais_dia_data[]   = $row['count'];
        }

        $stmt->close();
    } else {
        $sql_cartas_dia = "
            SELECT DAY(data_do_pedido) AS dia, COUNT(*) AS count
            FROM pedido_carta
            WHERE YEAR(data_do_pedido) = ?
              AND MONTH(data_do_pedido) = ?
            GROUP BY DAY(data_do_pedido)
            ORDER BY dia";

        $stmt = $conn->prepare($sql_cartas_dia);
        $stmt->bind_param('ii', $ano_filtro, $mes_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $cartas_dia_labels = [];
        $cartas_dia_data   = [];

        while ($row = $result->fetch_assoc()) {
            $cartas_dia_labels[] = $row['dia'];
            $cartas_dia_data[]   = $row['count'];
        }

        $stmt->close();

        $sql_credenciais_dia = "
            SELECT DAY(data_do_pedido) AS dia, COUNT(*) AS count
            FROM credencial_estagio
            WHERE YEAR(data_do_pedido) = ?
              AND MONTH(data_do_pedido) = ?
            GROUP BY DAY(data_do_pedido)
            ORDER BY dia";

        $stmt = $conn->prepare($sql_credenciais_dia);
        $stmt->bind_param('ii', $ano_filtro, $mes_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $credenciais_dia_labels = [];
        $credenciais_dia_data   = [];

        while ($row = $result->fetch_assoc()) {
            $credenciais_dia_labels[] = $row['dia'];
            $credenciais_dia_data[]   = $row['count'];
        }

        $stmt->close();
    }

    $labels                   = $cartas_dia_labels;
    $grafico_titulo_cartas    = "Cartas de Estágio por Dia - " . $months[$mes_filtro - 1] . " de " . $ano_filtro;
    $grafico_titulo_credenciais = "Credenciais por Dia - " . $months[$mes_filtro - 1] . " de " . $ano_filtro;
} else {
    // --- Anuais ---
    if ($is_supervisor && !empty($qualificacoes_ids)) {
        $placeholders = implode(',', $qualificacoes_ids);

        $sql_cartas_mes = "
            SELECT MONTH(p.data_do_pedido) AS mes, COUNT(*) AS count
            FROM pedido_carta p
            JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            WHERE YEAR(p.data_do_pedido) = ?
              AND q.id_qualificacao IN ($placeholders)
            GROUP BY MONTH(p.data_do_pedido)";

        $stmt = $conn->prepare($sql_cartas_mes);
        $stmt->bind_param('i', $ano_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $cartas_mes_data = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $cartas_mes_data[$row['mes'] - 1] = $row['count'];
        }

        $stmt->close();

        $sql_credenciais_mes = "
            SELECT MONTH(c.data_do_pedido) AS mes, COUNT(*) AS count
            FROM credencial_estagio c
            JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
            JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
            WHERE YEAR(c.data_do_pedido) = ?
              AND q.id_qualificacao IN ($placeholders)
            GROUP BY MONTH(c.data_do_pedido)";

        $stmt = $conn->prepare($sql_credenciais_mes);
        $stmt->bind_param('i', $ano_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $credenciais_mes_data = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            // $cartas_mes_data[$row['mes'] - 1] = $row['count'];
            $credenciais_mes_data[$row['mes'] - 1] = $row['count'];
        }

        $stmt->close();
    } else {
        $sql_cartas_mes = "
            SELECT MONTH(data_do_pedido) AS mes, COUNT(*) AS count
            FROM pedido_carta
            WHERE YEAR(data_do_pedido) = ?
            GROUP BY MONTH(data_do_pedido)";

        $stmt = $conn->prepare($sql_cartas_mes);
        $stmt->bind_param('i', $ano_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $cartas_mes_data = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $cartas_mes_data[$row['mes'] - 1] = $row['count'];
        }

        $stmt->close();

        $sql_credenciais_mes = "
            SELECT MONTH(data_do_pedido) AS mes, COUNT(*) AS count
            FROM credencial_estagio
            WHERE YEAR(data_do_pedido) = ?
            GROUP BY MONTH(data_do_pedido)";

        $stmt = $conn->prepare($sql_credenciais_mes);
        $stmt->bind_param('i', $ano_filtro);
        $stmt->execute();
        $result = $stmt->get_result();

        $credenciais_mes_data = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $credenciais_mes_data[$row['mes'] - 1] = $row['count'];
        }

        $stmt->close();
    }

    $cartas_dia_data            = $cartas_mes_data;
    $credenciais_dia_data       = $credenciais_mes_data;
    $labels                     = $months;
    $grafico_titulo_cartas      = "Cartas de Estágio por Mês - Ano " . $ano_filtro;
    $grafico_titulo_credenciais = "Credenciais por Mês - Ano " . $ano_filtro;
}

// ============================================================
// DISTRIBUIÇÃO POR QUALIFICAÇÃO
// ============================================================
$periodo_where_cartas      = " AND YEAR(p.data_do_pedido) = $ano_filtro"
    . ($filtro_periodo === 'mensal' ? " AND MONTH(p.data_do_pedido) = $mes_filtro" : "");

$periodo_where_credenciais = " AND YEAR(c.data_do_pedido) = $ano_filtro"
    . ($filtro_periodo === 'mensal' ? " AND MONTH(c.data_do_pedido) = $mes_filtro" : "");

// Filtro de qualificação para os pie charts
$qual_in_fragment = '';
if ($is_supervisor && !empty($qualificacoes_ids)) {
    $placeholders       = implode(',', $qualificacoes_ids);
    $qual_in_fragment   = " AND q.id_qualificacao IN ($placeholders) ";
} elseif ($is_supervisor && empty($qualificacoes_ids)) {
    $qual_in_fragment   = " AND 1=0 ";
}

// Pie: Cartas por Qualificação
$sql_cartas_qual = "
    SELECT q.descricao, COUNT(*) AS count
    FROM pedido_carta p
    JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    WHERE 1=1
    $periodo_where_cartas
    $qual_in_fragment
    GROUP BY q.id_qualificacao, q.descricao";

$cartas_qual_labels = [];
$cartas_qual_data   = [];
$q = mysqli_query($conn, $sql_cartas_qual);
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $cartas_qual_labels[] = $row['descricao'];
        $cartas_qual_data[]   = $row['count'];
    }
}

// Pie: Credenciais por Qualificação
$sql_credenciais_qual = "
    SELECT q.descricao, COUNT(*) AS count
    FROM credencial_estagio c
    JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
    JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    WHERE 1=1
    $periodo_where_credenciais
    $qual_in_fragment
    GROUP BY q.id_qualificacao, q.descricao";

$credenciais_qual_labels = [];
$credenciais_qual_data   = [];
$q = mysqli_query($conn, $sql_credenciais_qual);
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $credenciais_qual_labels[] = $row['descricao'];
        $credenciais_qual_data[]   = $row['count'];
    }
}

// Totais
$total_cartas      = array_sum($cartas_dia_data);
$total_credenciais = array_sum($credenciais_dia_data);

echo "// Dados gerados pelo PHP para os gráficos\n";
echo "window.relatorioData = " . json_encode([
    'months' => $months,
    'labels' => $labels,
    'cartas_dia_data' => $cartas_dia_data,
    'credenciais_dia_data' => $credenciais_dia_data,
    'cartas_qual_labels' => $cartas_qual_labels,
    'cartas_qual_data' => $cartas_qual_data,
    'credenciais_qual_labels' => $credenciais_qual_labels,
    'credenciais_qual_data' => $credenciais_qual_data,
]) . ";\n";

echo "console.log('Dados de situação de estágio carregados');\n";