<?php
session_start();
include '../../Controller/Formando/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

$conexao = new Conector();
$conn = $conexao->getConexao();
$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

// Verificar se o formando já confirmou seu código
if (strtolower($_SESSION['role'] ?? '') === 'formando') {
    if (!isset($_SESSION['codigo_formando'])) {
        // Formando não confirmou código, redirecionar para confirmação
        header("Location: /estagio/View/Auth/ConfirmacaoFormando.php");
        exit();
    }
}

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

// Se efetuou ação via POST, redireciona e evita re-envio de formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Estudante</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">

    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
    <!-- CSS -->
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
    <style>
        /* ====== VARIÁVEIS E CONFIGURAÇÕES GLOBAIS ====== */
        :root {
            --primary-color: #3a4c91;
            --secondary-color: #3c9bff;
            --accent-color: #0d6efd;
            --text-light: #ffffff;
            --text-dark: #000000;
            --shadow-light: 0 3px 6px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 8px 20px rgba(0, 0, 0, 0.15);
            --shadow-heavy: 0 12px 25px rgba(0, 0, 0, 0.15);
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        /* ====== ESTRUTURA PRINCIPAL ====== */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background-color: var(--primary-color);
            box-shadow: var(--shadow-medium);
        }

        html,
        body {
            height: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
            padding: 0;
        }

        main {
            flex: 1;
            padding-top: 80px;
            /* Espaço para o header fixo */
            padding-bottom: 20px;
        }

        footer {
            background-color: var(--primary-color);
            color: var(--text-light);
            text-align: center;
            padding: 15px 0;
            width: 100%;
        }

        .container-footer p {
            color: var(--text-light);
            font-size: clamp(12px, 3vw, 16px);
            margin: 0;
        }

        .container-footer p span {
            color: var(--secondary-color);
        }

        /* ====== NAVEGAÇÃO ====== */
        .nav-link {
            color: var(--text-light) !important;
            transition: var(--transition);
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
        }

        .dropdown-menu {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
        }

        /* ====== HERO SECTION ====== */
        .hero-section {
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
            z-index: 1;
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        /* ====== MOTOR DE BUSCA ====== */
        .search-box {
            background: var(--text-light);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-heavy);
            padding: 2rem;
            margin: 20px auto;
        }

        .search-box h3 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 12px;
            font-size: 16px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 12px 24px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-2px);
        }

        /* ====== CARDS E SEÇÕES ====== */
        .destaques,
        .dicas-noticias {
            padding: 3rem 0;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .card-img-top {
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .card-text {
            color: #6c757d;
            line-height: 1.6;
        }

        /* ====== RESPONSIVIDADE ====== */
        @media (min-width: 768px) {
            main {
                padding-top: 100px;
            }

            .search-box {
                margin: 40px auto;
            }

            .hero-section {
                padding: 100px 0;
            }
        }

        @media (max-width: 767.98px) {
            main {
                padding-top: 70px;
            }

            .search-box {
                padding: 1.5rem;
                margin: 15px;
            }

            .card-img-top {
                height: 150px;
            }

            .destaques,
            .dicas-noticias {
                padding: 2rem 0;
            }
        }

        @media (max-width: 480px) {
            main {
                padding-top: 60px;
            }

            .search-box {
                padding: 1rem;
                margin: 10px;
            }

            .card-body {
                padding: 1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                width: 100%;
                margin-top: 0.5rem;
            }
        }

        @media (max-width: 320px) {
            .search-box {
                padding: 0.75rem;
                margin: 5px;
            }

            #resultadosBusca .card img {
                height: 120px;
            }
        }

        /* Para tablets em modo retrato */
        @media (min-width: 768px) and (max-width: 1024px) and (orientation: portrait) {
            .search-box {
                padding: 1.5rem;
            }

            .card-img-top {
                height: 180px;
            }
        }

        /* Animações suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Melhorias de acessibilidade */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Focus visível para acessibilidade */
        .btn:focus,
        .form-control:focus,
        .form-select:focus {
            outline: 2px solid var(--accent-color);
            outline-offset: 2px;
        }
    </style>
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>

    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                                <a class="nav-link" aria-current="page" href="https://www.instagram.com/itc.ac" class="me-3 text-white fs-4">
                                    <i class="fa-brands fa-square-instagram" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://pt-br.facebook.com/itc.transcom">
                                    <i class="fa-brands fa-facebook" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link" href="https://plus.google.com/share?url=https://simplesharebuttons.com">
                                    <i class="fab fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
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
                    <a class="nav-link" href="../estagio/formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/formularioDeCredencialDeEstagio.php">Solicitar Credencial de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/formularioDeVisita.php">Solicitar Visita de Estágio</a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container mt-4">
        <h2 class="mb-4" style="padding-top: 30px;">Histórico de Pedidos</h2>

        <div class="table-responsive">
            <table id="pedidosTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Numero</th>
                        <th>Nome</th>
                        <th>Apelido</th>
                        <th>Código Formando</th>
                        <th>Qualificação</th>
                        <th>Turma</th>
                        <th>Data do Pedido</th>
                        <th>Hora do Pedido</th>
                        <th>Empresa</th>
                        <th>Contacto Principal</th>
                        <th>Contacto Secundário</th>
                        <th>Email</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="pedidosTbody">
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
            </nav>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            const rowsPerPage = 4;
            let pedidosData = [];

            function renderTable() {
                $('#pedidosTbody').empty();
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = pedidosData.slice(start, end);

                pageData.forEach(pedido => {
                    $('#pedidosTbody').append(`
                        <tr>
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_pedido_carta}"></td>
                            <td>${pedido.numero}</td>
                            <td>${pedido.nome}</td>
                            <td>${pedido.apelido}</td>
                            <td>${pedido.codigo_formando}</td>
                            <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                            <td>${pedido.turma}</td>
                            <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                            <td>${pedido.hora_do_pedido}</td>
                            <td>${pedido.empresa}</td>
                            <td>${pedido.contactoPrincipal}</td>
                            <td>${pedido.contactoSecundario}</td>
                            <td>${pedido.email}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.id_pedido_carta}" title="Editar" >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.id_pedido_carta}" title="Remover">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(pedidosData.length / rowsPerPage);
                $('#pagination').empty();

                for (let i = 1; i <= totalPages; i++) {
                    $('#pagination').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#">${i}</a>
                        </li>
                    `);
                }

                $('.page-link').click(function(e) {
                    e.preventDefault();
                    currentPage = parseInt($(this).text());
                    renderTable();
                });
            }

            function buscarPedidos(pesquisa = '') {
                $.get('../../Controller/Estagio/search_pedidos.php', {
                    termo: pesquisa
                }, function(data) {
                    pedidosData = data;
                    currentPage = 1;
                    renderTable();
                });
            }

            buscarPedidos('');

            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = '../estagio/editarPedido.php?numero=' + id;
            });

            $(document).on('click', '.remover-btn', function() {
                var id = $(this).data('id');

                // Confirmação antes de remover
                if (confirm('Tem certeza que deseja remover o pedido #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_pedido.php',
                        type: 'POST',
                        data: {
                            id_pedido_carta: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                buscarPedidos($('#searchInput').val().trim());
                            } else {
                                alert(response.error || 'Erro ao remover o pedido');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('Status:', xhr.status);
                            console.log('Resposta:', xhr.responseText); // ← mostra o que o PHP devolveu
                            alert('Erro: ' + xhr.responseText);
                        }
                        // error: function() {
                        //     alert('Erro ao comunicar com o servidor');
                        // }
                    });
                }
            });

            $('#selectAll').on('change', function() {
                $('.select-checkbox').prop('checked', this.checked);
            });

            // Atualiza o "Select All" quando desmarcar um checkbox
            $(document).on('change', '.select-checkbox', function() {
                const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });

        });
    </script>
</body>

</html>