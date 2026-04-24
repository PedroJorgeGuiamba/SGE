<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../Controller/Supervisor/Home.php';
include_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

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
$pedidos_per_qual_json = json_encode($pedidos_monthly_per_qualification);

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

$pedidos_year_per_qual_json = json_encode($pedidos_year_per_qualification);
$years_list_json = json_encode(array_values($years_list));

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Supervisor</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/notifications.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="ITC Logo">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://www.instagram.com/itc.ac" aria-label="Instagram">
                                    <i class="fa-brands fa-instagram" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://pt-br.facebook.com/itc.transcom" aria-label="Facebook">
                                    <i class="fa-brands fa-facebook" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://plus.google.com/share?url=https://simplesharebuttons.com" aria-label="Google">
                                    <i class="fa-brands fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 shadow-sm" style="z-index: 1050; border-radius: 50%; width: 45px; height: 45px;">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>
                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
                            <li class="nav-item ms-lg-3">
                                <a href="/estagio/logout" class="btn btn-danger shadow-sm"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Nav Secundária -->
        <nav class="bg-white shadow-sm border-bottom">
            <ul class="nav justify-content-center py-2">
                <li class="nav-item mx-2">
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="/estagio/supervisor">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="pedidosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-fw me-1 text-success"></i> Fazer Pedidos
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="pedidosDropdown">
                        <li><a class="dropdown-item" href="/estagio/estagio/criar"><i class="fas fa-envelope-open-text fa-fw me-2 text-secondary"></i> Pedido de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/credencial/criar"><i class="fas fa-id-badge fa-fw me-2 text-secondary"></i> Credencial de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/visita/criar"><i class="fas fa-map-marked-alt fa-fw me-2 text-secondary"></i> Visita de Estágio</a></li>
                    </ul>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="listasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list-ul fa-fw me-1 text-info"></i> Listas
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="listasDropdown">
                        <li><a class="dropdown-item" href="/estagio/estagio/listar"><i class="fas fa-file-alt fa-fw me-2 text-secondary"></i> Pedidos de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/credencial/listar"><i class="fas fa-id-card-clip fa-fw me-2 text-secondary"></i> Pedidos de Credencial</a></li>
                        <li><a class="dropdown-item" href="/estagio/visita/listar"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Pedidos de Visita</a></li>
                    </ul>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link fw-semibold text-dark" href="/estagio/relatorio">
                        <i class="fas fa-file-pdf fa-fw me-1 text-danger"></i> Gerar Relatórios
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <section class="dashboard-header text-center bg-white border-bottom shadow-sm py-5 mb-4" style="margin-top: 190px;">
        <div class="container">
            <h1 class="display-5 fw-bold"><i class="fas fa-chart-line me-3"></i>Resumo dos Dados de Estágios</h1>
            <p class="lead text-muted">Visão Geral dos Dados de Estágio sob a sua supervisão</p>
        </div>
    </section>

    <main class="container-fluid px-4 bg-light pb-5 pt-3">
        <!-- Charts Section -->
        <section class="row g-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="card-title fw-bold text-secondary mb-4">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>Cartas de Estágio geradas por Ano
                        </h5>
                        <div class="chart-container flex-grow-1 position-relative">
                            <canvas id="pedidosAnoPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100 shadow-sm border-0 rounded-4">
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="card-title fw-bold text-secondary mb-4">
                            <i class="fas fa-chart-bar text-success me-2"></i>Cartas de Estágio geradas por mês (<?= date('Y') ?>)
                        </h5>
                        <div class="chart-container flex-grow-1 position-relative">
                            <canvas id="pedidosPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script>
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


        const ctxPedidosAno = document.getElementById('pedidosAnoPieChart').getContext('2d');
        const years_list = <?php echo $years_list_json; ?>;
        const pedidos_year_data = <?php echo $pedidos_year_per_qual_json; ?>;

        let datasetsAno = qualifications.map((qual, index) => {
            return {
                label: qual,
                data: pedidos_year_data[qual] ?? [],
                backgroundColor: colors[index % colors.length],
                borderColor: borderColors[index % colors.length],
                borderWidth: 2
            };
        });

        new Chart(ctxPedidosAno, {
            type: 'bar',
            data: {
                labels: years_list,
                datasets: datasetsAno
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
    </script>
</body>

</html>