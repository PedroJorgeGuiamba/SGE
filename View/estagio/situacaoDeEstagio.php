<?php
session_start();
include '../../Controller/Admin/Home.php';
include '../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();

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
    INNER JOIN pedido_carta p ON r.numero_carta = p.numero
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

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situação de Estagio</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <link rel="stylesheet" href="../../Assets/CSS/chart.css">
    <!-- Custom CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
            --border-radius: 15px;
        }
        body {
            /* background: linear-gradient(to bottom, #f8f9fa, #e9ecef); */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        footer {
            background: var(--bg-gradient);
            color: white;
            padding: 1rem 0;
            margin-top: 3rem;
        }
    </style>
</head>

<body>

    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="https://www.instagram.com/itc.ac">Instagram</a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="https://pt-br.facebook.com/itc.transcom">Facebook</a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="https://plus.google.com/share?url=https://simplesharebuttons.com">Google</a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">Linkedin</a>
                            </li>
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3" style="z-index: 1050;">
                                    <i class="fas fa-moon"></i> <!-- ícone muda com JS -->
                                </button>
                            </li>
                            <li class="nav-item">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>

        <!-- Nav Secundária -->
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page"
                        href="../../View/Admin/portalDoAdmin.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listaDePedidos.php">Pedidos de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="respostaCarta.php">Resposta das Cartas</a>
                </li>
            </ul>
        </nav>
    </header>

    <section class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold"><i class="fas fa-chart-line me-3"></i>Resumo dos Dados de Estágios</h1>
            <p class="lead">Visão Geral dos Dados de Estágio</p>
        </div>
    </section>

    <main class="container-fluid">
        <!-- Charts Section -->
        <section class="row g-4">
            <!-- Bar Chart: Pedidos per Month -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Cartas de Estágio geradas por mês (<?= date('Y') ?>)</h4>
                    <canvas id="pedidosBarChart" height="300"></canvas>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Cartas de Estágio geradas por mês e por qualificação (<?= date('Y') ?>)</h4>
                    <canvas id="pedidosPieChart" height="300"></canvas>
                </div>
            </div>

            <!-- Pie Chart: Status Resposta -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-pie me-2"></i>Distribuição do Estado das Resposta Às Cartas</h4>
                    <canvas id="statusRespostaPie" height="300"></canvas>
                </div>
            </div>

            <!-- Pie Chart: Status Estagio -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-pie me-2"></i>Distribuição dos Estados dos Estágios</h4>
                    <canvas id="statusEstagioPie" height="300"></canvas>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-pie me-2"></i>Distribuição dos Estados dos Estágios</h4>
                    <canvas id="statusEstagioQualificacaoPie" height="300"></canvas>
                </div>
            </div>

        </section>
    </main>
    
    <script src="../../Assets/JS/tema.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="/js/scripts.js"></script>
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
                scales: { y: { beginAtZero: true } }
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
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: { legend: { position: 'bottom' } }
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
                plugins: { legend: { position: 'bottom' } }
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
                plugins: { legend: { position: 'bottom' } }
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
                plugins: { legend: { position: 'bottom' } }
            }
        });

        
    </script>
</body>

</html>