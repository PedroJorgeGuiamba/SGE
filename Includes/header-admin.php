<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Controller/Admin/Home.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();
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
            /* --bg-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --border-radius: 15px;
        }

        footer {
            /* background: var(--bg-gradient); */
            background-color: #3A4C91;
            color: white;
            /* padding: 1rem 0; */
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
                    <a class="nav-link active" aria-current="page" href="../../View/Admin/portalDoAdmin.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Módulos</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdownModulos" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Cadastrar
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownModulos">
                        <li><a class="dropdown-item" href="../../View/Cursos/CadastrarCurso.php">Cursos</a></li>
                        <li><a class="dropdown-item" href="../../View/Qualificacao/CadastrarQualificacao.php">Qualificacoes</a></li>
                        <li><a class="dropdown-item" href="../../View/Turmas/CadastrarTurma.php">Turmas</a></li>
                        <li><a class="dropdown-item" href="../../View/Formando/CadastrarFormando.php">Formandos</a></li>
                        <li><a class="dropdown-item" href="../../View/Formador/CadastrarFormador.php">Formadores</a></li>
                        <li><a class="dropdown-item" href="../../View/Supervisor/CadastrarSupervisor.php">Supervisores</a></li>
                    </ul>
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