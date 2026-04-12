<?php
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
SecurityHeaders::setFull();
$conector = new Conector();
$conn = $conector->getConexao();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);


// User roles pie chart
$user_roles_query = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM usuarios GROUP BY role");
$user_roles_labels = [];
$user_roles_data = [];
if ($user_roles_query) {
    while ($row = mysqli_fetch_assoc($user_roles_query)) {
        $user_roles_labels[] = $row['role'];
        $user_roles_data[] = $row['count'];
    }
}

// Monthly sessions bar chart
$months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$monthly_sessions_query = mysqli_query($conn, "SELECT MONTH(data) as month, COUNT(*) as count FROM sessao WHERE YEAR(data) = YEAR(CURDATE()) GROUP BY MONTH(data)");
$monthly_sessions = array_fill(0, 12, 0);
if ($monthly_sessions_query) {
    while ($row = mysqli_fetch_assoc($monthly_sessions_query)) {
        $monthly_sessions[$row['month'] - 1] = $row['count'];
    }
}

// Formandos per curso bar chart
$formandos_per_curso_query = mysqli_query($conn, "SELECT c.nome, COUNT(f.id_formando) as count FROM formando f JOIN turma_formando tf ON f.codigo = tf.codigo_formando JOIN turma t ON tf.codigo_turma = t.codigo JOIN curso c ON t.codigo_curso = c.codigo GROUP BY c.nome");
$formandos_curso_labels = [];
$formandos_curso_data = [];
if ($formandos_per_curso_query) {
    while ($row = mysqli_fetch_assoc($formandos_per_curso_query)) {
        $formandos_curso_labels[] = $row['nome'];
        $formandos_curso_data[] = $row['count'];
    }
}
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

<section class="dashboard-header text-center bg-white border-bottom shadow-sm py-5 mb-4">
    <div class="container">
        <h1 class="display-5 fw-bold text-primary"><i class="fas fa-chart-line me-3"></i>Resumo dos Dados Geral</h1>
        <p class="lead text-muted">Acompanhe e analise as métricas vitais do sistema</p>
    </div>
</section>

<!-- Fundo cinza suave faz os cartões brancos saltarem à vista -->
<main class="container-fluid px-4 bg-light pb-5 pt-3">
    <!-- Charts Section -->
    <section class="row g-4 align-items-stretch">
        <!-- Pie Chart: User Roles -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Distribuição de Perfil
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Formandos per Curso -->
        <div class="col-lg-8 col-md-6">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-chart-bar text-success me-2"></i>Formandos por Curso
                    </h5>
                    <div class="chart-container flex-grow-1 position-relative">
                        <canvas id="formandosChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart: Monthly Sessions -->
        <div class="col-12 mt-4">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold text-secondary mb-4">
                        <i class="fas fa-users text-warning me-2"></i>Tráfego de Sessões Mensais (<?= date('Y') ?>)
                    </h5>
                    <div class="chart-container" style="position: relative; height:40vh; width:100%">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script>
    // Pie Chart: User Roles
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const borderColor = isDark ? '#333333' : '#ffffff';
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($user_roles_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($user_roles_data); ?>,
                backgroundColor: ['#0dcaf0', '#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: borderColor,
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

    // Bar Chart: Monthly Sessions
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Number of Sessions',
                data: <?php echo json_encode($monthly_sessions); ?>,
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

    // Bar Chart: Formandos per Curso
    const ctxFormandos = document.getElementById('formandosChart').getContext('2d');
    new Chart(ctxFormandos, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($formandos_curso_labels); ?>,
            datasets: [{
                label: 'Number of Formandos',
                data: <?php echo json_encode($formandos_curso_data); ?>,
                backgroundColor: 'rgba(25, 135, 84, 0.6)',
                borderColor: 'rgba(25, 135, 84, 1)',
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
</script>
</body>

</html>