<?php
session_start();
include '../../Controller/Admin/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();

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

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <link rel="stylesheet" href="../../Assets/CSS/chart.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --border-radius: 15px;
        }

        footer {
            background: var(--bg-gradient);
            color: white;
            padding: 1rem 0;
            margin-top: 3rem;
        }

        header nav ul li.nav-item ul.dropdown-menu li a.dropdown-item {
            color: black;
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
                                <a class="nav-link" aria-current="page" href="https://www.instagram.com/itc.ac">Instagram</a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://pt-br.facebook.com/itc.transcom">Facebook</a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link" href="https://plus.google.com/share?url=https://simplesharebuttons.com">Google</a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">Linkedin</a>
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
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Módulos</a>
                </li>

                <li class="nav-item">
                    <div class="dropdown">
                        <button  style="background-color: #094297ff;" class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Cadastrar
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="../../View/Cursos/CadastrarCurso.php" style="color: black">Cursos</a>
                            <a class="dropdown-item" href="../../View/Qualificacao/CadastrarQualificacao.php" style="color: black">Qualificacoes</a>
                            <a class="dropdown-item" href="../../View/Turmas/CadastrarTurma.php" style="color: black">Turmas</a>
                            <a class="dropdown-item" href="../../View/Formando/CadastrarFormando.php" style="color: black">Formandos</a>
                            <a class="dropdown-item" href="../../View/Formador/CadastrarFormador.php" style="color: black">Formadores</a>
                            <a class="dropdown-item" href="../../View/Supervisor/CadastrarSupervisor.php" style="color: black">Supervisores</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Horário</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Situação de Pagamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/situacaoDeEstagio.php">Situação de Estagio</a>
                </li>
                <li>
                    <a class="nav-link"  href="../Notas/visualizarNotas.php">Notas</a>
                </li>
            </ul>
        </nav>
    </header>

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
            <p>© 2019 TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
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