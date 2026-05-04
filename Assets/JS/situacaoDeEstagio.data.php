<?php
session_start();
header('Content-Type: application/javascript');
include_once __DIR__ . '/../../Conexao/conector.php';

$conector = new Conector();
$conn = $conector->getConexao();
$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$pedidos_monthly_query = mysqli_query($conn, "SELECT MONTH(data_do_pedido) as month, COUNT(*) as count FROM pedido_carta WHERE YEAR(data_do_pedido) = YEAR(CURDATE()) GROUP BY MONTH(data_do_pedido)");
$pedidos_monthly = array_fill(0, 12, 0);
if ($pedidos_monthly_query) {
    while ($row = mysqli_fetch_assoc($pedidos_monthly_query)) {
        $pedidos_monthly[$row['month'] - 1] = $row['count'];
    }
}

$pedidos_monthly_query_per_qualification = mysqli_query($conn, "SELECT MONTH(p.data_do_pedido) as month, q.descricao as qualificacao_desc, COUNT(*) as count FROM pedido_carta p JOIN qualificacao q ON p.qualificacao = q.id_qualificacao WHERE YEAR(p.data_do_pedido) = YEAR(CURDATE()) GROUP BY MONTH(p.data_do_pedido), q.descricao");
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
$pedidos_per_qual_json = json_encode($pedidos_monthly_per_qualification);

$credenciais_empresa_query = mysqli_query($conn, "
    SELECT 
        c.empresa,
        q.descricao AS qualificacao_desc,
        COUNT(*) AS count
    FROM credencial_estagio c
    JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta
    JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    WHERE YEAR(c.data_do_pedido) = YEAR(CURDATE())
      AND c.empresa IS NOT NULL
      AND c.empresa != ''
    GROUP BY c.empresa, q.descricao
    ORDER BY count DESC
");

$credenciais_por_empresa = [];
$empresas_labels = [];

if ($credenciais_empresa_query) {
    while ($row = mysqli_fetch_assoc($credenciais_empresa_query)) {
        $empresa = $row['empresa'];
        $qual = $row['qualificacao_desc'];

        if (!in_array($empresa, $empresas_labels)) {
            $empresas_labels[] = $empresa;
        }

        if (!isset($credenciais_por_empresa[$qual])) {
            $credenciais_por_empresa[$qual] = [];
        }

        $credenciais_por_empresa[$qual][$empresa] = $row['count'];
    }
}

$empresas_labels_json = json_encode($empresas_labels);
$credenciais_por_empresa_json = json_encode($credenciais_por_empresa);

// Status resposta pie chart
$status_resposta_query = mysqli_query($conn, "SELECT status_resposta, COUNT(*) as count FROM resposta_carta GROUP BY status_resposta");
$status_resposta_labels = [];
$status_resposta_data = [];
if ($status_resposta_query) {
    while ($row = mysqli_fetch_assoc($status_resposta_query)) {
        $status_resposta_labels[] = $row['status_resposta'];
        $status_resposta_data[] = $row['count'];
    }
}

// Status estagio pie chart
$status_estagio_query = mysqli_query($conn, "SELECT status_estagio, COUNT(*) as count FROM resposta_carta GROUP BY status_estagio");
$status_estagio_labels = [];
$status_estagio_data = [];
if ($status_estagio_query) {
    while ($row = mysqli_fetch_assoc($status_estagio_query)) {
        $status_estagio_labels[] = $row['status_estagio'];
        $status_estagio_data[] = $row['count'];
    }
}

// Status estagio pie chart agrupado por qualificacao
$status_estagio_qualificacao_query = mysqli_query($conn, "
    SELECT
        q.descricao AS qualificacao,
        r.status_estagio,
        COUNT(*) AS total
    FROM resposta_carta r
    INNER JOIN pedido_carta p ON r.numero_carta = p.id_pedido_carta
    INNER JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
    GROUP BY q.descricao, r.status_estagio
    ORDER BY q.descricao, r.status_estagio
");
$status_estagio_qualificacao_labels = [];
$status_estagio_qualificacao_data = [];
if ($status_estagio_qualificacao_query) {
    while ($row = mysqli_fetch_assoc($status_estagio_qualificacao_query)) {
        $status_estagio_qualificacao_labels[] = $row['qualificacao'] . ' - ' . $row['status_estagio'];
        $status_estagio_qualificacao_data[] = $row['total'];
    }
}

// Avaliacao results pie chart
$avaliacao_result_query = mysqli_query($conn, "SELECT resultado, COUNT(*) as count FROM avaliacao_estagio GROUP BY resultado");
$avaliacao_result_labels = [];
$avaliacao_result_data = [];
if ($avaliacao_result_query) {
    while ($row = mysqli_fetch_assoc($avaliacao_result_query)) {
        $avaliacao_result_labels[] = $row['resultado'];
        $avaliacao_result_data[] = $row['count'];
    }
}

echo "// Dados gerados pelo PHP para os gráficos\n";
echo "window.situacaoEstagioData = " . json_encode([
    'months' => $months,
    'pedidos_monthly' => $pedidos_monthly,
    'qualifications' => $qualifications,
    'pedidos_per_qual' => $pedidos_monthly_per_qualification,
    'empresas_labels' => $empresas_labels,
    'credenciais_por_empresa' => $credenciais_por_empresa,
    'status_resposta_labels' => $status_resposta_labels,
    'status_resposta_data' => $status_resposta_data,
    'status_estagio_labels' => $status_estagio_labels,
    'status_estagio_data' => $status_estagio_data,
    'status_estagio_qualificacao_labels' => $status_estagio_qualificacao_labels,
    'status_estagio_qualificacao_data' => $status_estagio_qualificacao_data,
]) . ";\n";

echo "console.log('Dados de situação de estágio carregados');\n";
?>