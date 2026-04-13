<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
include '../../Controller/Geral/FormandoAdmin.php';
require_once __DIR__ . '/../../middleware/auth.php';

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
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Estágio</title>

    <!-- BootStrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Assets/CSS/global.css">
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fallback local para jQuery -->
    <script>
        window.jQuery || document.write('<script src="../../Scripts/jquery-3.6.0.min.js"><\/script>');
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
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
                                    <i class="fas fa-moon"></i> <!-- ícone muda com JS -->
                                </button>
                            </li>
                            <?php include __DIR__ . '/../../Includes/notification-widget.php'; ?>
                            <li class="nav-item ms-3">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger shadow-sm px-4 fw-semibold rounded-pill"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
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
                    <a class="nav-link fw-semibold text-dark active" href="../../View/<?php echo $_SESSION['role'] === 'admin'
                                                                ? 'Admin/portalDoAdmin.php'
                                                                : ($_SESSION['role'] === 'supervisor'
                                                                    ? 'Supervisor/portalDoSupervisor.php'
                                                                    : 'Formando/portalDeEstudante.php'); ?>">
                        <i class="fas fa-home fa-fw me-1 text-primary"></i> Home
                    </a>
                </li>
                
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="pedidosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-fw me-1 text-success"></i> Fazer Pedidos
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="pedidosDropdown">
                        <li><a class="dropdown-item" href="formularioDeCartaDeEstagio.php"><i class="fas fa-envelope-open-text fa-fw me-2 text-secondary"></i> Pedido de Estágio</a></li>
                        <li><a class="dropdown-item" href="formularioDeCredencialDeEstagio.php"><i class="fas fa-id-badge fa-fw me-2 text-secondary"></i> Credencial de Estágio</a></li>
                        <li><a class="dropdown-item" href="formularioDeVisita.php"><i class="fas fa-map-marked-alt fa-fw me-2 text-secondary"></i> Visita de Estágio</a></li>
                    </ul>
                </li>

                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor'): ?>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="listasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list-ul fa-fw me-1 text-info"></i> Listas
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="listasDropdown">
                        <li><a class="dropdown-item" href="listaDePedidos.php"><i class="fas fa-file-alt fa-fw me-2 text-secondary"></i> Pedidos de Estágio</a></li>
                        <li><a class="dropdown-item" href="listaDePedidosCredencial.php"><i class="fas fa-id-card-clip fa-fw me-2 text-secondary"></i> Pedidos de Credencial</a></li>
                        <li><a class="dropdown-item" href="listaDePedidosVisita.php"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Pedidos de Visita</a></li>
                    </ul>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link fw-semibold text-dark" href="relatorio.php">
                        <i class="fas fa-file-pdf fa-fw me-1 text-danger"></i> Gerar Relatórios
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-file-signature me-2"></i>Pedido de Carta de Estágio</h3>
                        <p class="text-muted small">Preencha os dados abaixo com precisão para gerar e submeter o seu pedido de estágio</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../View/Estagio/previewCartaDeEstagio.php" method="post" id="formularioEstagio">
                            <?php echo CSRFProtection::getTokenField(); ?>
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <!-- SECÇÃO: IDENTIFICAÇÃO DO ALUNO -->
                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Identificação Curricular</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="codigoFormando" class="form-label text-muted fw-bold small">Código do Formando</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="codigoFormando" class="form-control border-start-0 ps-0" id="codigoFormando" placeholder="123456">
                                    </div>
                                    <span class="error_form text-danger small" id="codigoFormando_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="qualificacao" name="qualificacao">
                                            <option selected disabled>A carregar...</option>
                                        </select>
                                    </div>
                                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="turma" class="form-label text-muted fw-bold small">Turma</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-users-class text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" id="turma" name="turma">
                                            <option selected disabled>A carregar...</option>
                                        </select>
                                    </div>
                                    <span class="error_form text-danger small" id="turma_error_message"></span>
                                </div>
                            </div>

                            <!-- SECÇÃO: DESTINO E CONTACTOS -->
                            <h5 class="text-secondary fw-semibold mt-4 mb-3 border-bottom pb-2">Destino e Meios de Contacto</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="empresa" class="form-label text-muted fw-bold small">Empresa Alvo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                        <input type="text" name="empresa" class="form-control border-start-0 ps-0" id="empresa" placeholder="Onde pretende realizar...">
                                    </div>
                                    <span class="error_form text-danger small" id="empresa_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contactoPrincipal" class="form-label text-muted fw-bold small">Tel. Principal</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                        <input type="tel" name="contactoPrincipal" class="form-control border-start-0 ps-0" id="contactoPrincipal" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" placeholder="84xxxxxxx" required>
                                    </div>
                                    <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contactoSecundario" class="form-label text-muted fw-bold small">Tel. Alternativo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="tel" name="contactoSecundario" class="form-control border-start-0 ps-0" id="contactoSecundario" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" placeholder="84xxxxxxx" required>
                                    </div>
                                    <span class="error_form text-danger small" id="cSecundario_error_message"></span>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="email" class="form-label text-muted fw-bold small">Email Pessoal Académico</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="estudante@dominio.com">
                                    </div>
                                    <span class="error_form text-danger small" id="email_error_message"></span>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-paper-plane me-1"></i> Avançar Pedido</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script>
        // Selects com valores fornecidos da BD
        $(document).ready(function() {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Qualificacao/getQualificacoes.php',
                method: 'GET',
                success: function(resposta) {
                    $('#qualificacao').html(resposta);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar qualificações:', status, error);
                    $('#qualificacao').html('<option>Erro ao carregar qualificações</option>');
                }
            });

            $.ajax({
                url: '../../Controller/Turmas/getTurmas.php',
                method: 'GET',
                success: function(resposta) {
                    $('#turma').html(resposta);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar turmas:', status, error);
                    $('#turma').html('<option>Erro ao carregar turmas</option>');
                }
            });
        }

        $.validator.addMethod('telefone_mz', function(value, element) {
            if (this.optional(element)) return true;
            return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
        }, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');

        // Validação do formulário
        $("#formularioEstagio").validate({
            rules: {
                codigoFormando: {
                    required: true,
                    digits: true
                },
                qualificacao: {
                    required: true
                },
                turma: {
                    required: true
                },
                dataPedido: {
                    required: true,
                    date: true
                },
                horaPedido: {
                    required: true
                },
                empresa: {
                    required: true,
                    minlength: 2
                },
                contactoPrincipal: {
                    required: true,
                    telefone_mz: true
                },
                contactoSecundario: {
                    required: true,
                    telefone_mz: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                codigoFormando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                qualificacao: {
                    required: "Selecione uma qualificação."
                },
                turma: {
                    required: "Selecione uma turma."
                },
                dataPedido: {
                    required: "Informe a data do pedido.",
                    date: "Formato inválido."
                },
                horaPedido: {
                    required: "Informe a hora do pedido."
                },
                empresa: {
                    required: "Informe o nome da empresa.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                contactoPrincipal: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                contactoSecundario: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                email: {
                    required: "Informe o e-mail.",
                    email: "Endereço de e-mail inválido."
                }
            },
            errorClass: "is-invalid",
            validClass: "is-valid",
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#formCartaDeEstagio').submit(function(e) {
                e.preventDefault();
                console.log('Dados enviados:', $(this).serialize());
                $.ajax({
                    url: '../../Controller/Estagio/FormularioDeCartaDeEstagio.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                        alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
                    }
                });
            });
        });
    </script>
</body>

</html>