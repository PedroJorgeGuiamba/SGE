<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Controller/Admin/Home.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../Conexao/conector.php';

SecurityHeaders::setFull();

// Inicializar conexão se não existir
if (!isset($conn) || $conn === null) {
    $conector = new Conector();
    $conn = $conector->getConexao();
}

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
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
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/chart.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
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
                            <?php include __DIR__ . '/notification-widget.php'; ?>
                            <li class="nav-item ms-lg-3">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>

        <nav class="bg-white shadow-sm border-bottom">
            <ul class="nav justify-content-center py-2">
                <li class="nav-item mx-2">
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="../../View/Admin/portalDoAdmin.php">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="fas fa-cubes me-1 text-primary"></i> Módulos
                    </a>
                </li>

                <li class="nav-item dropdown mx-2">
                    <a class="nav-link dropdown-toggle fw-semibold text-dark" href="#" id="dropdownModulos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1 text-primary"></i> Cadastrar
                    </a>
                    <ul class="dropdown-menu shadow-sm border-0" aria-labelledby="dropdownModulos">
                        <li><a class="dropdown-item" href="../../View/Cursos/CadastrarCurso.php"><i class="fas fa-graduation-cap fa-fw me-2 text-secondary"></i> Cursos</a></li>
                        <li><a class="dropdown-item" href="../../View/Qualificacao/CadastrarQualificacao.php"><i class="fas fa-certificate fa-fw me-2 text-secondary"></i> Qualificações</a></li>
                        <li><a class="dropdown-item" href="../../View/Turmas/CadastrarTurma.php"><i class="fas fa-users-class fa-fw me-2 text-secondary"></i> Turmas</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../../View/Formando/CadastrarFormando.php"><i class="fas fa-user-graduate fa-fw me-2 text-secondary"></i> Formandos</a></li>
                        <li><a class="dropdown-item" href="../../View/Formador/CadastrarFormador.php"><i class="fas fa-chalkboard-teacher fa-fw me-2 text-secondary"></i> Formadores</a></li>
                        <li><a class="dropdown-item" href="../../View/Supervisor/CadastrarSupervisor.php"><i class="fas fa-user-tie fa-fw me-2 text-secondary"></i> Supervisores</a></li>
                    </ul>
                </li>

                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="far fa-calendar-alt me-1 text-primary"></i> Horário
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="fas fa-money-bill-wave me-1 text-primary"></i> Situação de Pagamento
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../estagio/situacaoDeEstagio.php">
                        <i class="fas fa-briefcase me-1 text-primary"></i> Situação de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../Notas/visualizarNotas.php">
                        <i class="fas fa-clipboard-list me-1 text-primary"></i> Notas
                    </a>
                </li>
            </ul>
        </nav>
    </header>