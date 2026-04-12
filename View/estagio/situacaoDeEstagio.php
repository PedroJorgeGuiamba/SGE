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
?>

<?php require_once __DIR__ . '/../../Includes/header-estagio-situacao-admin.php' ?>

<section class="dashboard-header text-center bg-white border-bottom shadow-sm py-5 mb-4" style="margin-top: 140px;">
    <div class="container">
        <h1 class="display-5 fw-bold text-primary"><i class="fas fa-chart-pie me-3"></i>Situação de Estágios</h1>
        <p class="lead text-muted">Acompanhe as métricas globais e o status das candidaturas a estágio</p>
    </div>
</section>

<!-- Fundo cinza suave faz os cartões brancos saltarem à vista -->
<main class="container-fluid px-4 bg-light pb-5 pt-3">
    <!-- Charts Section -->
    <section class="row g-4 align-items-stretch">
        
        <!-- Bar Chart: Pedidos per Month -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-line text-primary me-2"></i>Cartas Geradas Mensalmente (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pedidosBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-layer-group text-success me-2"></i>Cartas por Qualificação (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pedidosPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Full Width for Companies -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-building text-info me-2"></i>Empresas Solicitadas por Qualificação (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative" style="height: 40vh;">
                        <canvas id="credenciaisEmpresaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Status Resposta -->
        <div class="col-lg-4 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-envelope-open-text text-warning me-2"></i>Estado das Respostas
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusRespostaPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart: Status Estagio -->
        <div class="col-lg-4 col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-briefcase text-secondary me-2"></i>Estado dos Estágios (Geral)
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusEstagioPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12 mt-4">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-graduation-cap text-danger me-2"></i>Estágios por Qualificação
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="statusEstagioQualificacaoPie"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </section>
</main>

<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script>
    const ctxPedidos = document.getElementById('pedidosBarChart').getContext('2d');
    new Chart(ctxPedidos, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Number of Requests',
                data: <?php echo json_encode($pedidos_monthly); ?>,
                backgroundColor: 'rgba(13, 202, 240, 0.6)',
                borderColor: 'rgba(13, 202, 240, 1)',
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

    const ctxPedidosQualificacao = document.getElementById('pedidosPieChart').getContext('2d');
    const qualifications = <?php echo json_encode($qualifications); ?>;
    const pedidos_data = <?php echo $pedidos_per_qual_json; ?>;
    const colors = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(201, 203, 207, 0.6)'
    ];
    const borderColors = colors.map(color => color.replace('0.6', '1'));
    let datasets = qualifications.map((qual, index) => {
        return {
            label: qual,
            data: pedidos_data[qual],
            backgroundColor: colors[index % colors.length],
            borderColor: borderColors[index % colors.length],
            borderWidth: 2
        };
    });
    new Chart(ctxPedidosQualificacao, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    const ctxResposta = document.getElementById('statusRespostaPie').getContext('2d');
    new Chart(ctxResposta, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status_resposta_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_resposta_data); ?>,
                backgroundColor: ['#0dcaf0', '#198754', '#ffc107'],
                borderWidth: 2,
                borderColor: '#fff'
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

    const ctxEstagio = document.getElementById('statusEstagioPie').getContext('2d');
    new Chart(ctxEstagio, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status_estagio_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_estagio_data); ?>,
                backgroundColor: ['#198754', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
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

    const ctxEstagioQualificacao = document.getElementById('statusEstagioQualificacaoPie').getContext('2d');
    new Chart(ctxEstagioQualificacao, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($status_estagio_qualificacao_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_estagio_qualificacao_data); ?>,
                backgroundColor: ['#198754', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
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

    // Cores para cada qualificação
    const credenciaisEmpresaLabels = <?= $empresas_labels_json ?>;
    const credenciaisEmpresaData = <?= $credenciais_por_empresa_json ?>;

    const qualColors = [
        '#4e79a7', '#f28e2b', '#e15759', '#76b7b2',
        '#59a14f', '#edc948', '#b07aa1', '#ff9da7',
        '#9c755f', '#bab0ac'
    ];

    const credenciaisEmpresaDatasets = Object.entries(credenciaisEmpresaData).map(([qual, empresaMap], i) => ({
        label: qual,
        data: credenciaisEmpresaLabels.map(emp => empresaMap[emp] ?? 0),
        backgroundColor: qualColors[i % qualColors.length],
        borderRadius: 4,
    }));

    const ctxCredEmpresa = document.getElementById('credenciaisEmpresaChart').getContext('2d');
    new Chart(ctxCredEmpresa, {
        type: 'bar',
        data: {
            labels: credenciaisEmpresaLabels,
            datasets: credenciaisEmpresaDatasets,
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} credencial(ais)`
                    }
                }
            },
            scales: {
                x: {
                    stacked: false,
                    ticks: {
                        maxRotation: 35,
                        minRotation: 20,
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    stacked: false,
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
</body>

</html>