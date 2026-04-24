<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../Controller/Geral/SupervisorAdmin.php';
include_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

$userId   = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
$userRole = $_SESSION['role'] ?? '';

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
$unreadCount   = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

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

// Label de escopo para mostrar na UI (só para supervisor)
$escopo_label = '';
if ($is_supervisor) {
    if (!empty($qualificacoes_ids)) {
        // Busca os nomes das qualificações para exibir
        $pids = implode(',', $qualificacoes_ids);
        $q_nomes = mysqli_query($conn, "SELECT descricao FROM qualificacao WHERE id_qualificacao IN ($pids)");
        $nomes = [];
        if ($q_nomes) {
            while ($r = mysqli_fetch_assoc($q_nomes)) {
                $nomes[] = $r['descricao'];
            }
        }
        $escopo_label = implode(' · ', $nomes);
    } else {
        $escopo_label = 'Nenhuma qualificação atribuída';
    }
}
?>

<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>
<link rel="stylesheet" href="/estagio/Assets/CSS/relatorio.css">

<!--<style>
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
        .scope-badge {
            background: rgba(13, 110, 253, 0.08);
            border: 1px solid rgba(13, 110, 253, 0.2);
            color: #0d6efd;
            border-radius: 50px;
            padding: 4px 14px;
            font-size: 0.82rem;
            font-weight: 600;
        }
        .scope-badge.no-qual {
            background: rgba(220, 53, 69, 0.08);
            border-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
    </style> -->

<main class="container-fluid px-4 mb-5" style="margin-top: 40px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-primary mb-0">
                <i class="fas fa-chart-pie me-2"></i>Dashboard Analítico
            </h3>
            <p class="text-muted small mb-0">
                Estatísticas detalhadas sobre emissão de Cartas e Credenciais de Estágio
            </p>
            <?php if ($is_supervisor && $escopo_label): ?>
                <div class="mt-2">
                    <i class="fas fa-filter text-muted me-1" style="font-size:.8rem;"></i>
                    <span class="scope-badge <?= empty($qualificacoes_ids) ? 'no-qual' : '' ?>">
                        <i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($escopo_label) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($is_supervisor && empty($qualificacoes_ids)): ?>
        <!-- Aviso: supervisor sem qualificações associadas -->
        <div class="alert alert-warning rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
            <div>
                <strong>Sem qualificações atribuídas.</strong>
                Não existem qualificações associadas à sua conta. Contacte o administrador do sistema.
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtros Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="periodo" class="form-label text-muted fw-bold small">Período de Análise</label>
                    <select name="periodo" id="periodo" class="form-select bg-light border-0" onchange="this.form.submit()">
                        <option value="anual" <?= $filtro_periodo === 'anual'  ? 'selected' : '' ?>>Dados Anuais</option>
                        <option value="mensal" <?= $filtro_periodo === 'mensal' ? 'selected' : '' ?>>Dados Mensais</option>
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
                    <a href="/estagio/relatorio/gerarPDF?periodo=<?= htmlspecialchars($filtro_periodo, ENT_QUOTES, 'UTF-8') ?>&ano=<?= htmlspecialchars($ano_filtro, ENT_QUOTES, 'UTF-8') ?><?= htmlspecialchars($filtro_periodo, ENT_QUOTES, 'UTF-8') === 'mensal' ? '&mes=' . htmlspecialchars($mes_filtro, ENT_QUOTES, 'UTF-8') : '' ?>"
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
                        <h6 class="text-white-50 fw-semibold text-uppercase letter-spacing-1 mb-1">Total de Cartas Solicitadas</h6>
                        <h2 class="display-4 fw-bold mb-0"><?= htmlspecialchars($total_cartas, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <i class="fas fa-envelope-open-text fa-4x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card bg-gradient-success text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <h6 class="text-white-50 fw-semibold text-uppercase letter-spacing-1 mb-1">Total de Credenciais Solicitadas</h6>
                        <h2 class="display-4 fw-bold mb-0"><?= htmlspecialchars($total_credenciais, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <i class="fas fa-id-badge fa-4x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="chart-container">
                <h5 class="chart-title"><i class="fas fa-chart-column text-primary me-2"></i><?= htmlspecialchars($grafico_titulo_cartas) ?></h5>
                <canvas id="cartasChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-container">
                <h5 class="chart-title"><i class="fas fa-chart-column text-success me-2"></i><?= htmlspecialchars($grafico_titulo_credenciais) ?></h5>
                <canvas id="credenciaisChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-container">
                <h5 class="chart-title"><i class="fas fa-chart-pie text-warning me-2"></i>Cartas por Qualificação</h5>
                <canvas id="cartasQualChart" height="250"></canvas>
            </div>
        </div>
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
    const colors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(201, 203, 207, 0.7)'
    ];
    const borderColors = colors.map(c => c.replace('0.7', '1'));

    new Chart(document.getElementById('cartasChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Cartas de Estágio',
                data: <?= json_encode($cartas_dia_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
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

    new Chart(document.getElementById('credenciaisChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Credenciais',
                data: <?= json_encode($credenciais_dia_data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
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

    new Chart(document.getElementById('cartasQualChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($cartas_qual_labels) ?>,
            datasets: [{
                data: <?= json_encode($cartas_qual_data) ?>,
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

    new Chart(document.getElementById('credenciaisQualChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($credenciais_qual_labels) ?>,
            datasets: [{
                data: <?= json_encode($credenciais_qual_data) ?>,
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