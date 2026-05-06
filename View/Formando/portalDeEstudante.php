<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../../Controller/Formando/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Conexao/conector.php';

SecurityHeaders::setFull();

if (!isset($conn) || $conn === null) {
    $conector = new Conector();
    $conn = $conector->getConexao();
}

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

// Verificar se o formando já confirmou seu código
if (strtolower($_SESSION['role'] ?? '') === 'formando') {
    if (!isset($_SESSION['codigo_formando'])) {
        header("Location: /estagio/login/confirmar-user");
        exit();
    }
}

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal do Formando ITC — gerencie os seus pedidos de estágio e credenciais.">
    <title>Portal do Formando | ITC</title>
    <link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/notifications.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/header.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/formando.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
    <script src="/estagio/Assets/JS/SessionManager.js" 
        data-session-config='{"timeoutMinutes":30,"heartbeatInterval":60}'>
    </script>
</head>

<body>
    <header>
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
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" aria-current="page" href="https://pt-br.facebook.com/itc.transcom" aria-label="Facebook">
                                    <i class="fa-brands fa-facebook"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://plus.google.com/share?url=https://simplesharebuttons.com" aria-label="Google">
                                    <i class="fa-brands fa-google"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 shadow-sm">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>
                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
                            <li class="nav-item ms-lg-3">
                                <a href="/estagio/logout" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill"><i class="fas fa-sign-out-alt me-1"></i> Logout</a>
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
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="/estagio/formando">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="/estagio/estagio/criar">
                        <i class="fas fa-file-alt me-1 text-primary"></i> Pedido de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="/estagio/credencial/criar">
                        <i class="fas fa-id-card me-1 text-primary"></i> Credencial de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="/estagio/visita/criar">
                        <i class="fas fa-calendar-check me-1 text-primary"></i> Visita de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="/estagio/avaliacao-estagio/criar">
                        <i class="fas fa-file-pdf me-1 text-primary"></i> Avaliação de Estágio
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="portal-section">
        <div class="container">
            <div class="section-header">
                <div class="icon-badge">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2>Histórico de Pedidos</h2>
                    <p>Consulte e gerencie todos os seus pedidos de estágio</p>
                </div>
            </div>

            <div class="quick-actions">
                <a href="/estagio/estagio/criar" class="quick-action-btn primary">
                    <i class="fas fa-plus-circle"></i> Novo Pedido de Estágio
                </a>
                <a href="/estagio/credencial/criar" class="quick-action-btn outline">
                    <i class="fas fa-id-badge"></i> Solicitar Credencial
                </a>
                <a href="/estagio/visita/criar" class="quick-action-btn outline">
                    <i class="fas fa-calendar-check"></i> Solicitar Visita
                </a>
            </div>

            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <span class="fw-semibold text-dark">
                            <i class="fas fa-list-alt me-2 text-primary"></i>Todos os Pedidos
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm" style="width: 220px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted" style="font-size: 0.75rem;"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0"
                                placeholder="Pesquisar..." style="font-size: 0.85rem;">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="pedidosTable" class="table mb-0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Nº</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>Cód. Formando</th>
                                <th>Qualificação</th>
                                <th>Turma</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Empresa</th>
                                <th>Contacto 1</th>
                                <th>Contacto 2</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody id="pedidosTbody">
                        </tbody>
                    </table>
                </div>

                <div class="px-3 py-3 border-top bg-white">
                    <nav>
                        <ul class="pagination justify-content-center mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </main>

    <script src="/estagio/Assets/JS/portalDeEstudante.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>