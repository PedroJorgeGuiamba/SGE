<?php
// Inicia sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define tema padrão se não existir
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light'; // ou 'dark' se preferires padrão escuro
}

include '../../Controller/Admin/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();

$conector = new Conector();
$conn = $conector->getConexao();

// === Aqui colocas todas as queries que precisas nesta página ===
// (vão continuar no ficheiro da página, mas podes mover para um controller se quiseres)
?>

<!DOCTYPE html>
<html lang="pt" data-bs-theme="<?php echo $_SESSION['theme']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Admin</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- CSS Personalizados -->
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
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="https://www.instagram.com/itc.ac">Instagram</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://pt-br.facebook.com/itc.transcom">Facebook</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://plus.google.com/share?url=https://simplesharebuttons.com">Google</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">Linkedin</a>
                            </li>
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 rounded-circle" 
                                        style="z-index: 1050; width: 50px; height: 50px;">
                                    <i class="fas fa-moon fa-lg"></i>
                                </button>
                            </li>
                            <li class="nav-item">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger">Logout</a>
                            </li>
                        </ul>
                    </div>
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
                        <button style="background-color: #094297ff;" class="btn btn-secondary dropdown-toggle" type="button" 
                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Cadastrar
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="../../View/Cursos/CadastrarCurso.php">Cursos</a>
                            <a class="dropdown-item" href="../../View/Qualificacao/CadastrarQualificacao.php">Qualificacoes</a>
                            <a class="dropdown-item" href="../../View/Turmas/CadastrarTurma.php">Turmas</a>
                            <a class="dropdown-item" href="../../View/Formando/CadastrarFormando.php">Formandos</a>
                            <a class="dropdown-item" href="../../View/Formador/CadastrarFormador.php">Formadores</a>
                            <a class="dropdown-item" href="../../View/Supervisor/CadastrarSupervisor.php">Supervisores</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="../Notas/visualizarNotas.php">Notas</a>
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
        <section class="row g-4"></section>