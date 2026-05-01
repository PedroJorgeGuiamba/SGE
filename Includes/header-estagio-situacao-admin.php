<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Controller/Geral/SupervisorAdmin.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../Conexao/conector.php';

SecurityHeaders::setFull();

if (!isset($conn) || $conn === null) {
    $conector = new Conector();
    $conn = $conector->getConexao();
}

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>
<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITC</title><link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/chart.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/notifications.css">
    <!-- Custom CSS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" style="height: 40px; margin-right: 15px;">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://www.instagram.com/itc.ac" target="_blank" title="Instagram">
                                    <i class="fa-brands fa-instagram" style="color: #E1306C;"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://pt-br.facebook.com/itc.transcom" target="_blank" title="Facebook">
                                    <i class="fa-brands fa-facebook" style="color: #1877F2;"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://plus.google.com/share?url=https://simplesharebuttons.com" target="_blank" title="Google Plus">
                                    <i class="fa-brands fa-google-plus-g" style="color: #db4a39;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com" target="_blank" title="LinkedIn">
                                    <i class="fa-brands fa-linkedin" style="color: #0A66C2;"></i>
                                </a>
                            </li>

                            <li class="nav-item ms-3">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 rounded-circle shadow" style="z-index: 1050; width: 50px; height: 50px;">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>

                            <?php include __DIR__ . '/notification-widget.php'; ?>

                            <li class="nav-item ms-3">
                                <a href="/estagio/logout" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Nav Secundária -->
        <nav class="bg-white shadow-sm border-bottom">
            <ul class="nav justify-content-center py-2">
                <li class="nav-item mx-1">
                    <a class="nav-link fw-semibold text-dark active" aria-current="page" href="/estagio/admin">
                        <i class="fas fa-home fa-fw me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="pedidosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-fw me-1 text-success"></i> Fazer Pedidos
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="pedidosDropdown">
                        <li><a class="dropdown-item" href="/estagio/estagio/criar"><i class="fas fa-envelope-open-text fa-fw me-2 text-secondary"></i> Pedido de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/credencial/criar"><i class="fas fa-id-badge fa-fw me-2 text-secondary"></i> Credencial de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/visitas/criar"><i class="fas fa-map-marked-alt fa-fw me-2 text-secondary"></i> Visita de Estágio</a></li>
                    </ul>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="listasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list-ul fa-fw me-1 text-info"></i> Listas
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="listasDropdown">
                        <li><a class="dropdown-item" href="/estagio/estagio/listar"><i class="fas fa-file-alt fa-fw me-2 text-secondary"></i> Pedidos de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/credencial/listar"><i class="fas fa-id-card-clip fa-fw me-2 text-secondary"></i> Pedidos de Credencial</a></li>
                        <li><a class="dropdown-item" href="/estagio/visitas/listar"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Pedidos de Visita</a></li>
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