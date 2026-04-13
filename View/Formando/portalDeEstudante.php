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
        header("Location: /estagio/View/Auth/ConfirmacaoFormando.php");
        exit();
    }
}

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

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
    <meta name="description" content="Portal do Formando ITC — gerencie os seus pedidos de estágio e credenciais.">
    <title>Portal do Formando | ITC</title>

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
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* ── Tabela premium com paleta do projecto ── */
        .portal-section {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8f9fa 100%);
            min-height: calc(100vh - 140px);
            padding: 2rem 0 3rem;
            padding-top: 140px; /* header fixo: navbar principal + navbar secundária */
        }

        @media (max-width: 767.98px) {
            .portal-section {
                padding-top: 120px;
            }
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .section-header .icon-badge {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3a4c91 0%, #3c9bff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(58, 76, 145, 0.3);
            flex-shrink: 0;
        }

        .section-header .icon-badge i {
            color: white;
            font-size: 1.1rem;
        }

        .section-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #3a4c91;
            margin: 0;
        }

        .section-header p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        /* Card da tabela */
        .table-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(58, 76, 145, 0.10);
            border: 1px solid rgba(58, 76, 145, 0.07);
            overflow: hidden;
        }

        .table-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            background: white;
        }

        /* Tabela */
        #pedidosTable {
            border-collapse: separate;
            border-spacing: 0;
            width: 100% !important;
            font-size: 0.875rem;
        }

        #pedidosTable thead tr th {
            background: linear-gradient(135deg, #3a4c91 0%, #3c6fb5 100%);
            color: white;
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border: none;
            white-space: nowrap;
        }

        #pedidosTable thead tr th:first-child {
            border-radius: 0;
        }

        #pedidosTable tbody tr {
            transition: background-color 0.18s ease;
        }

        #pedidosTable tbody tr:nth-child(even) {
            background: #f8faff;
        }

        #pedidosTable tbody tr:hover {
            background: #eef2ff !important;
        }

        #pedidosTable tbody td {
            padding: 11px 14px;
            border-bottom: 1px solid #f0f0f0;
            color: #374151;
            vertical-align: middle;
        }

        /* Badges de estado */
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Botões de acção na tabela */
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }

        .btn-action.edit {
            background: rgba(58, 76, 145, 0.1);
            color: #3a4c91;
        }

        .btn-action.edit:hover {
            background: #3a4c91;
            color: white;
            transform: translateY(-1px);
        }

        .btn-action.remove {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-action.remove:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-1px);
        }

        /* Paginação */
        .page-link {
            color: #3a4c91;
            border-color: #e9ecef;
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #3a4c91 0%, #3c9bff 100%);
            border-color: transparent;
        }

        .page-link:hover {
            color: #3a4c91;
            background: #eef2ff;
        }

        /* Barra de acções rápidas */
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .quick-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .quick-action-btn.primary {
            background: linear-gradient(135deg, #3a4c91 0%, #3c9bff 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(58, 76, 145, 0.3);
        }

        .quick-action-btn.primary:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(58, 76, 145, 0.4);
        }

        .quick-action-btn.outline {
            background: white;
            color: #3a4c91;
            border: 1.5px solid #3a4c91;
        }

        .quick-action-btn.outline:hover {
            background: #3a4c91;
            color: white;
            transform: translateY(-2px);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
    </style>
</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png"
                        alt="ITC Logo" style="height: 45px;">
                </a>
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarFormando" aria-controls="navbarFormando"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarFormando">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://www.instagram.com/itc.ac" aria-label="Instagram">
                                    <i class="fa-brands fa-square-instagram" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://pt-br.facebook.com/itc.transcom" aria-label="Facebook">
                                    <i class="fa-brands fa-facebook" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="https://plus.google.com/share?url=https://simplesharebuttons.com" aria-label="Google">
                                    <i class="fab fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com" aria-label="LinkedIn">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- Botão Tema -->
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3 shadow-sm"
                                    style="z-index: 1050; border-radius: 50%; width: 45px; height: 45px;" aria-label="Alternar tema">
                                    <i class="fas fa-moon"></i>
                                </button>
                            </li>
                            <!-- Notificações -->
                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
                            <!-- Logout -->
                            <li class="nav-item ms-lg-2">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger shadow-sm">
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
                    <a class="nav-link active fw-semibold text-dark" aria-current="page" href="#">
                        <i class="fas fa-home me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../estagio/formularioDeCartaDeEstagio.php">
                        <i class="fas fa-file-alt me-1 text-primary"></i> Pedido de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../estagio/formularioDeCredencialDeEstagio.php">
                        <i class="fas fa-id-card me-1 text-primary"></i> Credencial de Estágio
                    </a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link fw-semibold text-dark" href="../estagio/formularioDeVisita.php">
                        <i class="fas fa-calendar-check me-1 text-primary"></i> Visita de Estágio
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main class="portal-section">
        <div class="container">

            <!-- Cabeçalho da secção -->
            <div class="section-header">
                <div class="icon-badge">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2>Histórico de Pedidos</h2>
                    <p>Consulte e gerencie todos os seus pedidos de estágio</p>
                </div>
            </div>

            <!-- Acções rápidas -->
            <div class="quick-actions">
                <a href="../estagio/formularioDeCartaDeEstagio.php" class="quick-action-btn primary">
                    <i class="fas fa-plus-circle"></i> Novo Pedido de Estágio
                </a>
                <a href="../estagio/formularioDeCredencialDeEstagio.php" class="quick-action-btn outline">
                    <i class="fas fa-id-badge"></i> Solicitar Credencial
                </a>
                <a href="../estagio/formularioDeVisita.php" class="quick-action-btn outline">
                    <i class="fas fa-calendar-check"></i> Solicitar Visita
                </a>
            </div>

            <!-- Tabela de pedidos -->
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
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody id="pedidosTbody">
                            <!-- Preenchido via JS -->
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

    <!-- <?php require_once __DIR__ . '/../../Includes/footer.php' ?> -->

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

                if (pageData.length === 0) {
                    $('#pedidosTbody').append(`
                        <tr>
                            <td colspan="14">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p class="fw-semibold mt-2">Nenhum pedido encontrado</p>
                                    <small>Crie um novo pedido de estágio para começar</small>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    pageData.forEach(pedido => {
                        $('#pedidosTbody').append(`
                            <tr>
                                <td><input type="checkbox" class="form-check-input select-checkbox" value="${pedido.id_pedido_carta}"></td>
                                <td><span class="fw-semibold text-primary">${pedido.numero}</span></td>
                                <td>${pedido.nome}</td>
                                <td>${pedido.apelido}</td>
                                <td><code style="color:#3a4c91;">${pedido.codigo_formando}</code></td>
                                <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                                <td>${pedido.turma}</td>
                                <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                                <td>${pedido.hora_do_pedido}</td>
                                <td>${pedido.empresa}</td>
                                <td>${pedido.contactoPrincipal}</td>
                                <td>${pedido.contactoSecundario}</td>
                                <td>${pedido.email}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="btn-action edit editar-btn" data-id="${pedido.id_pedido_carta}" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action remove remover-btn" data-id="${pedido.id_pedido_carta}" title="Remover">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                }

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
                $.get('../../Controller/Estagio/search_historico_pedidos.php', { termo: pesquisa }, function(data) {
                    pedidosData = data;
                    currentPage = 1;
                    renderTable();
                });
            }

            buscarPedidos('');

            // Pesquisa em tempo real
            $('#searchInput').on('input', function() {
                buscarPedidos($(this).val().trim());
            });

            // Editar
            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = '../estagio/editarPedido.php?numero=' + id;
            });

            // Remover
            $(document).on('click', '.remover-btn', function() {
                var id = $(this).data('id');
                if (confirm('Tem certeza que deseja remover o pedido #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_pedido.php',
                        type: 'POST',
                        data: { id_pedido_carta: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                buscarPedidos($('#searchInput').val().trim());
                            } else {
                                alert(response.error || 'Erro ao remover o pedido');
                            }
                        },
                        error: function(xhr) {
                            alert('Erro: ' + xhr.responseText);
                        }
                    });
                }
            });

            // Select All
            $('#selectAll').on('change', function() {
                $('.select-checkbox').prop('checked', this.checked);
            });

            $(document).on('change', '.select-checkbox', function() {
                const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });
        });
    </script>
</body>

</html>