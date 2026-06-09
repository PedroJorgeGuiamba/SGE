<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../Controller/Formador/Home.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../Conexao/conector.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();
$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';

require_once __DIR__ . '/../Helpers/BaseURL.php';
$baseURL = new BaseURL();
$baseUrl = $baseURL->getBaseUrl();
?>
<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal dos Formadores do ITC — conheça o nosso corpo docente qualificado e as áreas de especialização.">
    <title>Portal de Formadores | ITC</title>
    <link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/formadores.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/header.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/notifications.css">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <script src="/estagio/Assets/JS/SessionManager.js"
        data-session-config='{"timeoutMinutes":30,"heartbeatInterval":60}'>
    </script>
</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="ITC Logo" style="height: 45px;">
                </a>
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarFormador"
                        aria-controls="navbarFormador" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarFormador">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://www.instagram.com/itc.ac" aria-label="Instagram">
                                    <i class="fa-brands fa-square-instagram"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://pt-br.facebook.com/itc.transcom" aria-label="Facebook">
                                    <i class="fa-brands fa-facebook"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://plus.google.com/share?url=https://simplesharebuttons.com" aria-label="Google">
                                    <i class="fab fa-google"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                            </li>
                            <!-- Botão Tema -->
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 shadow-sm"
                                    aria-label="Alternar tema">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>
                            <!-- Notificações -->
                            <?php include __DIR__ . '/notification-widget.php'; ?>
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="<?= $baseUrl ?>/perfil" title="Perfil">
                                    <i class="fa-solid fa-user"></i>
                                </a>
                            </li>
                            <!-- Logout -->
                            <li class="nav-item ms-lg-2">
                                <a href="<?= $baseUrl ?>/logout" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>

        <!-- Nav Secundária -->
        <nav class="bg-white shadow-sm border-bottom">
            <ul class="nav justify-content-center py-2">
                <li class="nav-item mx-2">
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="<?= $baseUrl ?>/formador">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="<?= $baseUrl ?>/formador/modulos">
                        <i class="fas fa-cubes me-1 text-primary"></i> Módulos
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="far fa-calendar-alt me-1 text-primary"></i> Horário
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="fas fa-money-bill-wave me-1 text-primary"></i> Pagamentos
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="#">
                        <i class="fas fa-briefcase me-1 text-primary"></i> Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../Notas/lancarNotas.php">
                        <i class="fas fa-clipboard-list me-1 text-primary"></i> Notas
                    </a>
                </li>
            </ul>
        </nav>
    </header>