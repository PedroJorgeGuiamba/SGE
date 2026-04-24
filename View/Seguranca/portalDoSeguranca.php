<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../Controller/Seguranca/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Conexao/conector.php';

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

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Segurança</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- JQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
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
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
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
                            <li class="nav-item ms-3">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 shadow-sm" style="z-index: 1050; border-radius: 50%; width: 50px; height: 50px;">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>
                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
                            <li class="nav-item ms-lg-3">
                                <a href="/estagio/logout" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
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
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="portalDoSeguranca.php">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-shield-alt me-2"></i>Controlo de Acesso</h3>
                        <p class="text-muted small">Registe os detalhes do acesso para monitorização diária e segurança das instalações</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../Controller/Seguranca/RegistroDeEntrada.php" method="post" id="formularioEstagio">

                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Identificação Principal</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="codigoFormando" class="form-label text-muted fw-bold small">Código do Formando</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                        <input type="number" name="codigoFormando" class="form-control border-start-0 ps-0" id="codigoFormando" placeholder="123456">
                                    </div>
                                    <span class="error_form text-danger small" id="codigoFormando_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="qualificacao" name="qualificacao">
                                            <option selected>Selecione a qualificação</option>
                                        </select>
                                    </div>
                                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="turma" class="form-label text-muted fw-bold small">Turma</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-users text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="turma" name="turma">
                                            <option selected>Selecione a turma</option>
                                        </select>
                                    </div>
                                    <span class="error_form text-danger small" id="turma_error_message"></span>
                                </div>
                            </div>

                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2 mt-4">Detalhes do Pedido</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="dataPedido" class="form-label text-muted fw-bold small">Data do Pedido</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-day text-muted"></i></span>
                                        <input type="date" name="dataPedido" class="form-control border-start-0 ps-0" id="dataPedido">
                                    </div>
                                    <span class="error_form text-danger small" id="dataPedido_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="horaPedido" class="form-label text-muted fw-bold small">Horário do Pedido</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-clock text-muted"></i></span>
                                        <input type="time" name="horaPedido" class="form-control border-start-0 ps-0" id="horaPedido">
                                    </div>
                                    <span class="error_form text-danger small" id="horaPedido_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="empresa" class="form-label text-muted fw-bold small">Empresa de Submissão</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                        <input type="text" name="empresa" class="form-control border-start-0 ps-0" id="empresa" placeholder="Empresa...">
                                    </div>
                                    <span class="error_form text-danger small" id="empresa_error_message"></span>
                                </div>
                            </div>

                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2 mt-4">Contactos</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="contactoPrincipal" class="form-label text-muted fw-bold small">Contacto Principal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="tel" name="contactoPrincipal" class="form-control border-start-0 ps-0" id="contactoPrincipal" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required placeholder="84xxxxxxx">
                                    </div>
                                    <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contactoSecundario" class="form-label text-muted fw-bold small">Contacto Secundário</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="tel" name="contactoSecundario" class="form-control border-start-0 ps-0" id="contactoSecundario" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required placeholder="84xxxxxxx">
                                    </div>
                                    <span class="error_form text-danger small" id="cSecundario_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label text-muted fw-bold small">Email Pessoal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="exemplo@email.com">
                                    </div>
                                    <span class="error_form text-danger small" id="email_error_message"></span>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>

    <!-- Bootsrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</body>

</html>