<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../Controller/Admin/Home.php';
include '../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

// Obter filtro de período (padrão: ano atual)
$filtro_periodo = $_GET['periodo'] ?? 'anual';
$ano_filtro = (int)($_GET['ano'] ?? date('Y'));
$mes_filtro = (int)($_GET['mes'] ?? date('m'));

$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

// ========== GRÁFICOS MENSAIS ==========
if ($filtro_periodo === 'mensal') {
    // Cartas por dia do mês
    $cartas_dia_query = mysqli_query($conn, "SELECT DAY(data_do_pedido) as dia, COUNT(*) as count FROM pedido_carta WHERE YEAR(data_do_pedido) = $ano_filtro AND MONTH(data_do_pedido) = $mes_filtro GROUP BY DAY(data_do_pedido) ORDER BY dia");
    $cartas_dia_labels = [];
    $cartas_dia_data = [];
    if ($cartas_dia_query) {
        while ($row = mysqli_fetch_assoc($cartas_dia_query)) {
            $cartas_dia_labels[] = $row['dia'];
            $cartas_dia_data[] = $row['count'];
        }
    }

    // Credenciais por dia do mês
    $credenciais_dia_query = mysqli_query($conn, "SELECT DAY(data_do_pedido) as dia, COUNT(*) as count FROM credencial_estagio WHERE YEAR(data_do_pedido) = $ano_filtro AND MONTH(data_do_pedido) = $mes_filtro GROUP BY DAY(data_do_pedido) ORDER BY dia");
    $credenciais_dia_labels = [];
    $credenciais_dia_data = [];
    if ($credenciais_dia_query) {
        while ($row = mysqli_fetch_assoc($credenciais_dia_query)) {
            $credenciais_dia_labels[] = $row['dia'];
            $credenciais_dia_data[] = $row['count'];
        }
    }

    $grafico_titulo_cartas = "Cartas de Estágio por Dia - " . $months[$mes_filtro - 1] . " de " . $ano_filtro;
    $grafico_titulo_credenciais = "Credenciais por Dia - " . $months[$mes_filtro - 1] . " de " . $ano_filtro;
    $labels = $cartas_dia_labels;
} else {
    // ========== GRÁFICOS ANUAIS ==========
    // Cartas por mês do ano
    $cartas_mes_query = mysqli_query($conn, "SELECT MONTH(data_do_pedido) as mes, COUNT(*) as count FROM pedido_carta WHERE YEAR(data_do_pedido) = $ano_filtro GROUP BY MONTH(data_do_pedido)");
    $cartas_mes_data = array_fill(0, 12, 0);
    if ($cartas_mes_query) {
        while ($row = mysqli_fetch_assoc($cartas_mes_query)) {
            $cartas_mes_data[$row['mes'] - 1] = $row['count'];
        }
    }

    // Credenciais por mês do ano
    $credenciais_mes_query = mysqli_query($conn, "SELECT MONTH(data_do_pedido) as mes, COUNT(*) as count FROM credencial_estagio WHERE YEAR(data_do_pedido) = $ano_filtro GROUP BY MONTH(data_do_pedido)");
    $credenciais_mes_data = array_fill(0, 12, 0);
    if ($credenciais_mes_query) {
        while ($row = mysqli_fetch_assoc($credenciais_mes_query)) {
            $credenciais_mes_data[$row['mes'] - 1] = $row['count'];
        }
    }

    $cartas_dia_data = $cartas_mes_data;
    $credenciais_dia_data = $credenciais_mes_data;
    $labels = $months;
    $grafico_titulo_cartas = "Cartas de Estágio por Mês - Ano " . $ano_filtro;
    $grafico_titulo_credenciais = "Credenciais por Mês - Ano " . $ano_filtro;
}

// Distribuição por qualificação (Cartas)
$cartas_qual_query = mysqli_query($conn, "SELECT q.descricao, COUNT(*) as count FROM pedido_carta p JOIN qualificacao q ON p.qualificacao = q.id_qualificacao WHERE YEAR(p.data_do_pedido) = $ano_filtro" . ($filtro_periodo === 'mensal' ? " AND MONTH(p.data_do_pedido) = $mes_filtro" : "") . " GROUP BY q.descricao");
$cartas_qual_labels = [];
$cartas_qual_data = [];
if ($cartas_qual_query) {
    while ($row = mysqli_fetch_assoc($cartas_qual_query)) {
        $cartas_qual_labels[] = $row['descricao'];
        $cartas_qual_data[] = $row['count'];
    }
}

// Distribuição por qualificação (Credenciais)
$credenciais_qual_query = mysqli_query($conn, "SELECT q.descricao, COUNT(*) as count FROM credencial_estagio c JOIN pedido_carta p ON c.id_pedido_carta = p.id_pedido_carta JOIN qualificacao q ON p.qualificacao = q.id_qualificacao WHERE YEAR(c.data_do_pedido) = $ano_filtro" . ($filtro_periodo === 'mensal' ? " AND MONTH(c.data_do_pedido) = $mes_filtro" : "") . " GROUP BY q.descricao");
$credenciais_qual_labels = [];
$credenciais_qual_data = [];
if ($credenciais_qual_query) {
    while ($row = mysqli_fetch_assoc($credenciais_qual_query)) {
        $credenciais_qual_labels[] = $row['descricao'];
        $credenciais_qual_data[] = $row['count'];
    }
}

// Totais
$total_cartas = array_sum($cartas_dia_data);
$total_credenciais = array_sum($credenciais_dia_data);
?>

<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php'?>

    <style>
        .chart-container {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0,0,0,0.05);
            height: 100%;
        }
        .chart-title {
            color: #495057;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid #f1f3f5;
            padding-bottom: 15px;
        }
        .stat-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        }
    </style>

    <main class="container-fluid px-4 mb-5" style="margin-top: 40px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-primary mb-0"><i class="fas fa-chart-pie me-2"></i>Dashboard Analítico</h3>
                <p class="text-muted small mb-0">Estatísticas detalhadas sobre emissão de Cartas e Credenciais de Estágio</p>
            </div>
        </div>

        <!-- Filtros Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="periodo" class="form-label text-muted fw-bold small">Período de Análise</label>
                        <select name="periodo" id="periodo" class="form-select bg-light border-0" onchange="this.form.submit()">
                            <option value="anual" <?php echo $filtro_periodo === 'anual' ? 'selected' : ''; ?>>Dados Anuais</option>
                            <option value="mensal" <?php echo $filtro_periodo === 'mensal' ? 'selected' : ''; ?>>Dados Mensais</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="ano" class="form-label text-muted fw-bold small">Ano</label>
                        <select name="ano" id="ano" class="form-select bg-light border-0" onchange="this.form.submit()">
                            <?php
                            $ano_atual = date('Y');
                            for ($i = $ano_atual; $i >= $ano_atual - 5; $i--) {
                                echo "<option value=\"$i\" " . ($ano_filtro === $i ? 'selected' : '') . ">$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <?php if ($filtro_periodo === 'mensal'): ?>
                        <div class="col-md-2">
                            <label for="mes" class="form-label text-muted fw-bold small">Mês</label>
                            <select name="mes" id="mes" class="form-select bg-light border-0" onchange="this.form.submit()">
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    echo "<option value=\"$i\" " . ($mes_filtro === $i ? 'selected' : '') . ">" . $months[$i - 1] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-md d-flex justify-content-end gap-2">
                        <a href="relatorio_impressao.php?periodo=<?php echo $filtro_periodo; ?>&ano=<?php echo $ano_filtro; ?>&mes=<?php echo $mes_filtro; ?>" class="btn btn-outline-secondary fw-semibold shadow-sm" target="_blank">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </a>
                        <a href="../../Controller/Estagio/GerarPdfRelatorio.php?periodo=<?= $filtro_periodo ?>&ano=<?= $ano_filtro ?><?= $filtro_periodo === 'mensal' ? '&mes=' . $mes_filtro : '' ?>" 
                           class="btn btn-primary fw-semibold shadow-sm" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i> Exportar Relatório PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cards com Totais -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card stat-card bg-gradient-primary text-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-white-50 fw-semibold text-uppercase letter-spacing-1 mb-1">Total de Cartas Expedidas</h6>
                            <h2 class="display-4 fw-bold mb-0"><?php echo $total_cartas; ?></h2>
                        </div>
                        <i class="fas fa-envelope-open-text fa-4x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card bg-gradient-success text-white h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-white-50 fw-semibold text-uppercase letter-spacing-1 mb-1">Total de Credenciais Emitidas</h6>
                            <h2 class="display-4 fw-bold mb-0"><?php echo $total_credenciais; ?></h2>
                        </div>
                        <i class="fas fa-id-badge fa-4x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4">
            <!-- Bar Chart: Cartas -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="chart-title"><i class="fas fa-chart-column text-primary me-2"></i><?php echo $grafico_titulo_cartas; ?></h5>
                    <canvas id="cartasChart" height="250"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Credenciais -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="chart-title"><i class="fas fa-chart-column text-success me-2"></i><?php echo $grafico_titulo_credenciais; ?></h5>
                    <canvas id="credenciaisChart" height="250"></canvas>
                </div>
            </div>

            <!-- Pie Chart: Cartas por Qualificação -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="chart-title"><i class="fas fa-chart-pie text-warning me-2"></i>Cartas por Qualificação</h5>
                    <canvas id="cartasQualChart" height="250"></canvas>
                </div>
            </div>

            <!-- Pie Chart: Credenciais por Qualificação -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5 class="chart-title"><i class="fas fa-chart-pie text-info me-2"></i>Credenciais por Qualificação</h5>
                    <canvas id="credenciaisQualChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>

    <script>
        // Cores
        const colors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(201, 203, 207, 0.7)'
        ];
        const borderColors = colors.map(c => c.replace('0.7', '1'));

        // Gráfico de Cartas
        const ctxCartas = document.getElementById('cartasChart').getContext('2d');
        new Chart(ctxCartas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Cartas de Estágio',
                    data: <?php echo json_encode($cartas_dia_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Credenciais
        const ctxCredenciais = document.getElementById('credenciaisChart').getContext('2d');
        new Chart(ctxCredenciais, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Credenciais',
                    data: <?php echo json_encode($credenciais_dia_data); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Pie Chart: Cartas por Qualificação
        const ctxCartasQual = document.getElementById('cartasQualChart').getContext('2d');
        new Chart(ctxCartasQual, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($cartas_qual_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($cartas_qual_data); ?>,
                    backgroundColor: colors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Pie Chart: Credenciais por Qualificação
        const ctxCredenciaisQual = document.getElementById('credenciaisQualChart').getContext('2d');
        new Chart(ctxCredenciaisQual, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($credenciais_qual_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($credenciais_qual_data); ?>,
                    backgroundColor: colors,
                    borderColor: borderColors,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>