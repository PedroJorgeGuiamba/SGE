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
    <title>Formulário de Visita</title>

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
                                    <i class="fas fa-moon"></i>
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

                <?php if ($_SESSION['role'] === 'admin'): ?>
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
                    <a class="nav-link fw-semibold text-dark" href="respostaCarta.php">
                        <i class="fas fa-reply-all fa-fw me-1 text-warning"></i> Resposta das Cartas
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container mb-5" style="margin-top: 140px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-map-marked-alt me-2"></i>Agendar Visita de Estágio</h3>
                        <p class="text-muted small">Registe os detalhes do local e do supervisor para que a visita de avaliação possa decorrer sem constrangimentos</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../Controller/Estagio/FormularioDeVisita.php" method="post" id="formularioEstagio">
                            <?php echo CSRFProtection::getTokenField(); ?>
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <!-- SECÇÃO: DADOS DO FORMANDO E EMPRESA -->
                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Identificação & Localização</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="codigoFormando" class="form-label text-muted fw-bold small">Código do Formando</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="codigoFormando" class="form-control border-start-0 ps-0" id="codigoFormando" placeholder="Ex: 123456" required>
                                    </div>
                                    <span class="error_form text-danger small" id="codigoFormando_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contactoFormando" class="form-label text-muted fw-bold small">Contacto do Formando</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-mobile-alt text-muted"></i></span>
                                        <input type="tel" name="contactoFormando" class="form-control border-start-0 ps-0" id="contactoFormando" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" placeholder="84xxxxxxx" required>
                                    </div>
                                    <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="empresa" class="form-label text-muted fw-bold small">Nome da Empresa</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                        <input type="text" name="empresa" class="form-control border-start-0 ps-0" id="empresa" placeholder="Empresa..." required>
                                    </div>
                                    <span class="error_form text-danger small" id="empresa_error_message"></span>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label for="endereco" class="form-label text-muted fw-bold small">Endereço Físico Exato da Empresa</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                        <input type="text" name="endereco" class="form-control border-start-0 ps-0" id="endereco" placeholder="Avenida, Rua, Bairro..." required>
                                    </div>
                                    <span class="error_form text-danger small" id="endereco_error_message"></span>
                                </div>
                            </div>

                            <!-- SECÇÃO: SUPERVISÃO E AGENDAMENTO -->
                            <h5 class="text-secondary fw-semibold mt-4 mb-3 border-bottom pb-2">Supervisão Interna & Encontro</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="nome_supervisor" class="form-label text-muted fw-bold small">Nome do Supervisor</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                        <input type="text" name="nome_supervisor" class="form-control border-start-0 ps-0" id="nome_supervisor" placeholder="Supervisor da empresa..." required>
                                    </div>
                                    <span class="error_form text-danger small" id="nome_supervisor_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="contacto_supervisor" class="form-label text-muted fw-bold small">Contacto Directo Supervisor</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="text" name="contacto_supervisor" class="form-control border-start-0 ps-0" id="contacto_supervisor" placeholder="84xxxxxxx" required>
                                    </div>
                                    <span class="error_form text-danger small" id="contacto_supervisor_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="datahora" class="form-label text-muted fw-bold small">Data e Hora Acordada</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                                        <input type="datetime-local" name="datahora" class="form-control border-start-0 ps-0" id="datahora" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                    </div>
                                    <span class="error_form text-danger small" id="datahora_error_message"></span>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-calendar-check me-1"></i> Agendar Visita</button>
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

        $.validator.addMethod('telefone_mz', function(value, element) {
            if (this.optional(element)) return true;
            return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
        }, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');

        // Validação do formulário
        $("#formularioEstagio").validate({
            rules: {
                codigoFormando: {
                    required: true,
                    digits: true,
                    minlength: 5
                },
                contactoFormando: {
                    required: true,
                    telefone_mz: true
                },
                empresa: {
                    required: true,
                    minlength: 2
                },
                endereco: {
                    required: true,
                    minlength: 2
                },
                nome_supervisor: {
                    required: true,
                    minlength: 2
                },
                contacto_supervisor: {
                    required: true,
                    telefone_mz: true
                },
                datahora: {
                    required: true,
                }
            },
            messages: {
                codigoFormando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos.",
                    minlength: "O Código deve ter pelo menos 5 caracteres."
                },
                contactoFormando: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                empresa: {
                    required: "Informe o nome da empresa.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                endereco: {
                    required: "Informe o endereço da empresa.",
                    minlength: "O endereço deve ter pelo menos 2 caracteres."
                },
                nome_supervisor: {
                    required: "Informe o nome do supervisor.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                contacto_supervisor: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                datahora: {
                    required: "Informe a data do pedido.",
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
                    url: '../../Controller/Estagio/FormularioDeVisita.php',
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