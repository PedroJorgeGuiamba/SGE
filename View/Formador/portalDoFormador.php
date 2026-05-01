<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../../Controller/Formador/Home.php';
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
    // header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($_SESSION['theme'], ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Formadores</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
    <link rel="stylesheet" href="../../Assets/CSS/formadores.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <a class="nav-link" href="#">Módulos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Horário</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Situação de Pagamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="situacaoDeEstagio.html">Situação de Estagio</a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Secção Hero / Introdução sobre Formadores -->
        <section class="formadores-hero bg-light py-5">
            <div class="container">
                <div class="section-title fade-in mb-5">
                    <h2>Corpo Docente Qualificado</h2>
                    <p class="lead text-muted">Conheça os formadores que impulsionam a excelência no ensino técnico-profissional</p>
                </div>
            </div>
        </section>

        <!-- Secção de Estatísticas dos Formadores -->
        <section class="formadores-stats py-4 bg-white">
            <div class="container">
                <div class="row text-center g-4">
                    <div class="col-md-3">
                        <div class="stat-box">
                            <h4 class="stat-number text-primary">50+</h4>
                            <p class="stat-label">Formadores Experientes</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <h4 class="stat-number text-primary">20+</h4>
                            <p class="stat-label">Anos de Experiência</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <h4 class="stat-number text-primary">15</h4>
                            <p class="stat-label">Áreas de Especialização</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <h4 class="stat-number text-primary">5000+</h4>
                            <p class="stat-label">Alunos Formados</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Listagem de Formadores -->
        <section class="formadores-list">
            <div class="container">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    <!-- Formador 1 -->
                    <div class="col fade-in">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/itc-p.png" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-primary mb-2">Informática</span>
                                <h5 class="card-title">João Silva</h5>
                                <p class="card-text small text-muted">Especialista em Sistemas</p>
                                <p class="card-text">Formador com mais de 15 anos de experiência em desenvolvimento de software e gestão de sistemas.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 200+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.8/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 2 -->
                    <div class="col fade-in" style="animation-delay: 0.1s;">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2024/10/WhatsApp-Image-2024-10-29-at-14.36.59-7-768x1024.jpeg" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-success mb-2">Comunicações</span>
                                <h5 class="card-title">Maria Santos</h5>
                                <p class="card-text small text-muted">Especialista em Redes</p>
                                <p class="card-text">Profissional dedicada ao ensino de infraestruturas de comunicação e redes de dados.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 180+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.9/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 3 -->
                    <div class="col fade-in" style="animation-delay: 0.2s;">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-warning text-dark mb-2">Transportes</span>
                                <h5 class="card-title">Carlos Mendes</h5>
                                <p class="card-text small text-muted">Especialista em Logística</p>
                                <p class="card-text">Formador experiente em gestão de transportes e cadeia de abastecimento.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 220+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.7/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 4 -->
                    <div class="col fade-in" style="animation-delay: 0.3s;">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-info mb-2">Gestão</span>
                                <h5 class="card-title">Ana Costa</h5>
                                <p class="card-text small text-muted">Especialista em Administração</p>
                                <p class="card-text">Especialista em gestão empresarial e administração de recursos humanos.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 190+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.8/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 5 -->
                    <div class="col fade-in" style="animation-delay: 0.4s;">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-danger mb-2">Eletrónica</span>
                                <h5 class="card-title">Pedro Oliveira</h5>
                                <p class="card-text small text-muted">Especialista em Eletrônica</p>
                                <p class="card-text">Formador com vasta experiência em instalação e manutenção de sistemas eletrônicos.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 170+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.6/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formador 6 -->
                    <div class="col fade-in" style="animation-delay: 0.5s;">
                        <div class="card formador-card h-100">
                            <div class="card-img-wrapper">
                                <img src="https://www.itc.ac.mz/wp-content/uploads/2023/10/WhatsApp-Image-2023-10-26-at-15.03.08-2.jpeg" class="card-img-top" alt="Formador">
                                <div class="card-overlay">
                                    <a href="#" class="btn btn-sm btn-primary">Ver Perfil</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2">Soft Skills</span>
                                <h5 class="card-title">Rita Nunes</h5>
                                <p class="card-text small text-muted">Especialista em Desenvolvimento</p>
                                <p class="card-text">Formadora especializada em competências transversais e desenvolvimento pessoal.</p>
                                <div class="formador-stats mt-3 pt-3 border-top">
                                    <small class="text-muted d-block"><i class="fas fa-graduation-cap me-1"></i> 250+ Alunos</small>
                                    <small class="text-muted d-block"><i class="fas fa-star me-1 text-warning"></i> 4.9/5 Avaliação</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script src="/js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous">
    </script>
</body>

</html>