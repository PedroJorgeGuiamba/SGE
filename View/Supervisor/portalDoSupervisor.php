<?php
session_start();
include '../../Controller/Supervisor/Home.php';
include '../../Conexao/conector.php';
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
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Supervisor</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
    <link rel="stylesheet" href="../../Assets/CSS/chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
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
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/listaDePedidos.php">Pedidos de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/respostaCarta.php">Respostas Das Cartas de Estagio</a>
                </li>
            </ul>
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
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Cartas de Estágio geradas por Ano </h4>
                    <canvas id="pedidosAnoPieChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Cartas de Estágio geradas por mês (<?= date('Y') ?>)</h4>
                    <canvas id="pedidosPieChart" height="300"></canvas>
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