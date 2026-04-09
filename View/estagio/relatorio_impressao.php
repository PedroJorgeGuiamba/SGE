<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';


$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

// ==================== RECEBER FILTROS ====================
$filtro_periodo = $_GET['periodo'] ?? 'anual';
$ano_filtro = (int)($_GET['ano'] ?? date('Y'));
$mes_filtro = (int)($_GET['mes'] ?? date('m'));

$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$meses_pt = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
    7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// ==================== CONSULTAS (mesmo código do dashboard) ====================
if ($filtro_periodo === 'mensal') {
    // Cartas por dia
    $cartas_query = mysqli_query($conn, "SELECT DAY(data_do_pedido) as dia, COUNT(*) as count 
        FROM pedido_carta 
        WHERE YEAR(data_do_pedido) = $ano_filtro AND MONTH(data_do_pedido) = $mes_filtro 
        GROUP BY DAY(data_do_pedido) ORDER BY dia");
    
    // Credenciais por dia
    $cred_query = mysqli_query($conn, "SELECT DAY(data_do_pedido) as dia, COUNT(*) as count 
        FROM credencial_estagio 
        WHERE YEAR(data_do_pedido) = $ano_filtro AND MONTH(data_do_pedido) = $mes_filtro 
        GROUP BY DAY(data_do_pedido) ORDER BY dia");

    $titulo_periodo = $meses_pt[$mes_filtro] . " de " . $ano_filtro;
} else {
    // Cartas por mês
    $cartas_query = mysqli_query($conn, "SELECT MONTH(data_do_pedido) as mes, COUNT(*) as count 
        FROM pedido_carta WHERE YEAR(data_do_pedido) = $ano_filtro GROUP BY MONTH(data_do_pedido)");
    
    // Credenciais por mês
    $cred_query = mysqli_query($conn, "SELECT MONTH(data_do_pedido) as mes, COUNT(*) as count 
        FROM credencial_estagio WHERE YEAR(data_do_pedido) = $ano_filtro GROUP BY MONTH(data_do_pedido)");

    $titulo_periodo = "Ano " . $ano_filtro;
}

// Processar dados para gráficos e totais
$cartas_data = $filtro_periodo === 'mensal' ? [] : array_fill(0, 12, 0);
$cred_data   = $filtro_periodo === 'mensal' ? [] : array_fill(0, 12, 0);
$labels = $filtro_periodo === 'mensal' ? [] : $months;

$total_cartas = 0;
$total_credenciais = 0;

while ($row = mysqli_fetch_assoc($cartas_query)) {
    if ($filtro_periodo === 'mensal') {
        $labels[] = $row['dia'];
        $cartas_data[] = $row['count'];
    } else {
        $cartas_data[$row['mes'] - 1] = $row['count'];
    }
    $total_cartas += $row['count'];
}

while ($row = mysqli_fetch_assoc($cred_query)) {
    if ($filtro_periodo === 'mensal') {
        $cred_data[] = $row['count'];
    } else {
        $cred_data[$row['mes'] - 1] = $row['count'];
    }
    $total_credenciais += $row['count'];
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="utf-8">
    <title>Relatório de Pedidos e Credenciais - <?= $titulo_periodo ?></title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm; }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }
        header {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 3px solid #003366;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        header img { width: 90px; }
        h1, h2, h3 { color: #003366; }
        .center { text-align: center; }
        .summary {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            text-align: center;
        }
        .summary-box {
            border: 2px solid #003366;
            padding: 15px 25px;
            border-radius: 8px;
            min-width: 220px;
        }
        .summary-box h2 { margin: 5px 0; font-size: 2.2em; color: #003366; }
        canvas { max-width: 100%; height: auto !important; margin: 20px 0; }
        .chart-container {
            page-break-inside: avoid;
            margin-bottom: 40px;
        }
        footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #555;
        }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
        <div>
            <h1>INSTITUTO DE TRANSPORTES E COMUNICAÇÕES</h1>
            <h3>Relatório de Pedidos de Cartas de Estágio e Credenciais</h3>
            <p><strong>Período:</strong> <?= $titulo_periodo ?></p>
        </div>
    </header>

    <!-- Resumo -->
    <div class="summary">
        <div class="summary-box">
            <h4>Total de Cartas Solicitadas</h4>
            <h2><?= $total_cartas ?></h2>
        </div>
        <div class="summary-box">
            <h4>Total de Credenciais Emitidas</h4>
            <h2><?= $total_credenciais ?></h2>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="chart-container">
        <h2 class="center">Evolução de Cartas de Estágio - <?= $titulo_periodo ?></h2>
        <canvas id="cartasChart" height="110"></canvas>
    </div>

    <div class="chart-container">
        <h2 class="center">Evolução de Credenciais - <?= $titulo_periodo ?></h2>
        <canvas id="credenciaisChart" height="110"></canvas>
    </div>

    <script>
        const labels = <?= json_encode($labels) ?>;
        const cartasData = <?= json_encode($cartas_data) ?>;
        const credData = <?= json_encode($cred_data) ?>;

        // Gráfico Cartas
        new Chart(document.getElementById('cartasChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cartas de Estágio',
                    data: cartasData,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Gráfico Credenciais
        new Chart(document.getElementById('credenciaisChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Credenciais',
                    data: credData,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>

    <footer>
        Relatório gerado em <?= date('d/m/Y \à\s H:i') ?> | Instituto de Transportes e Comunicações
    </footer>
</body>
</html>