<?php
require_once __DIR__ . '/../../Conexao/conector.php';

$conector = new Conector();
$conn = $conector->getConexao();

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

    <section class="dashboard-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold"><i class="fas fa-chart-line me-3"></i>Resumo dos Dados Geral</h1>
            <p class="lead">Dados Gerais do Sistema</p>
        </div>
    </section>

    <main class="container-fluid">
        <!-- Charts Section -->
        <section class="row g-4">
            <!-- Pie Chart: User Roles -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-pie me-2"></i>Distribuição de Perfil por Utilizador</h4>
                    <canvas id="pieChart" height="300"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Monthly Sessions -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Sessões por mês (<?= date('Y') ?>)</h4>
                    <canvas id="barChart" height="300"></canvas>
                </div>
            </div>

            <!-- Bar Chart: Formandos per Curso -->
            <div class="col-lg-6">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar me-2"></i>Formandos por Curso</h4>
                    <canvas id="formandosChart" height="300"></canvas>
                </div>
            </div>
        </section>
    </main>


    <!-- Rodapé -->
    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>

    <!-- Scripts do BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="../../Assets/JS/tema.js"></script>
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