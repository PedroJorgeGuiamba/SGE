<?php
session_start();
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

SecurityHeaders::setFull();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
    } else {
        try {
            CSRFProtection::validateToken($_POST['csrf_token'] ?? '');
        } catch (RuntimeException $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }

        $_SESSION['preview_pedido'] = [
            'codigoFormando'     => (int) ($_POST['codigoFormando'] ?? 0),
            'qualificacao'       => (int) ($_POST['qualificacao'] ?? 0),
            'turma'              => (int) ($_POST['turma'] ?? 0),
            'empresa'            => strtoupper(trim($_POST['empresa'] ?? '')),
            'contactoPrincipal'  => trim($_POST['contactoPrincipal'] ?? ''),
            'contactoSecundario' => trim($_POST['contactoSecundario'] ?? ''),
            'email'              => trim($_POST['email'] ?? ''),
        ];

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

if (empty($_SESSION['preview_pedido'])) {
    header("Location: formularioDeCartaDeEstagio.php");
    exit();
}

$dados = $_SESSION['preview_pedido'];

$conector = new Conector();
$conn     = $conector->getConexao();

$codigoFormando     = $dados['codigoFormando'];
$codigoQualificacao = $dados['qualificacao'];
$codigoTurma        = $dados['turma'];

$dadosFormando = null;
$stmtF = $conn->prepare("SELECT nome, apelido FROM formando WHERE codigo = ?");
$stmtF->bind_param("i", $codigoFormando);
$stmtF->execute();
$dadosFormando = $stmtF->get_result()->fetch_assoc();
$stmtF->close();

$nomeQualificacao = '—';
$stmtQ = $conn->prepare("SELECT descricao FROM qualificacao WHERE id_qualificacao = ?");
$stmtQ->bind_param("i", $codigoQualificacao);
$stmtQ->execute();
$rowQ = $stmtQ->get_result()->fetch_assoc();
$stmtQ->close();
if ($rowQ) $nomeQualificacao = $rowQ['descricao'];

$nomeTurma = '—';
$stmtT = $conn->prepare("
    SELECT t.nome FROM turma t
    LEFT JOIN qualificacao q ON t.codigo_qualificacao = q.id_qualificacao
    WHERE t.codigo = ? AND q.id_qualificacao = ?
");
$stmtT->bind_param("ii", $codigoTurma, $codigoQualificacao);
$stmtT->execute();
$rowT = $stmtT->get_result()->fetch_assoc();
$stmtT->close();
if ($rowT) $nomeTurma = $rowT['nome'];

// Notificações
$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$unreadCount   = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

// Flash error vindo do CSRF ou de outro redirecionamento
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <title>Revisão do Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="../../Assets/CSS/global.css"> -->
    <link rel="stylesheet" href="../../Assets/CSS/notifications.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Verdana, Tahoma, Helvetica;
        }

        html, body {
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 0;
    }

    header {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        background-color: var(--primary-color);
        box-shadow: var(--shadow-medium);
    }

    .preview-card {
    flex: 1;
    padding-top: 80px; /* Espaço para o header fixo */
    padding-bottom: 20px;
}


        body { background: var(--bs-tertiary-bg); }

        .preview-card {
            max-width: 860px;
            margin: 2.5rem auto;
            background: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 12px;
            overflow: hidden;
        }

        .preview-header {
            background: var(--bs-primary-bg-subtle);
            border-bottom: 1px solid var(--bs-border-color);
            padding: 1.25rem 1.5rem;
        }
        .preview-header h5 { margin: 0; font-size: 15px; font-weight: 500; color: var(--bs-primary); }
        .preview-header small { font-size: 12px; color: var(--bs-secondary-color); }

        .preview-body { padding: 1.5rem; }

        .section-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 500;
            color: var(--bs-secondary-color);
            letter-spacing: .05em;
            text-transform: uppercase;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--bs-border-color);
            margin-bottom: 12px;
        }

        .field-card {
            background: var(--bs-secondary-bg);
            border-radius: 8px;
            padding: 10px 14px;
        }
        .field-card .field-label {
            font-size: 11px;
            color: var(--bs-tertiary-color);
            margin: 0 0 2px;
        }
        .field-card .field-value {
            font-size: 15px;
            font-weight: 500;
            color: var(--bs-body-color);
            margin: 0;
            word-break: break-word;
        }

        .preview-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--bs-border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
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
    <div class="preview-card">
        <?php if ($flashError): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i>
                <?php echo htmlspecialchars($flashError); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="preview-header">
            <h5><i class="fas fa-file-alt me-2"></i>Revisão do pedido de carta de estágio</h5>
            <small>Verifique todos os dados antes de confirmar o envio</small>
        </div>

        <div class="preview-body">

            <?php if (!$dadosFormando): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    Formando com código <strong class="ms-1 me-1"><?php echo htmlspecialchars($codigoFormando); ?></strong>
                    não encontrado. Verifique o código e tente novamente.
                </div>

            <?php else: ?>

            <!-- Secção: Formando -->
            <div class="mb-4">
                <div class="section-label">
                    <i class="fas fa-user" style="font-size:12px"></i> Dados do formando
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Código</p>
                            <p class="field-value"><?php echo htmlspecialchars($dados['codigoFormando']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Nome</p>
                            <p class="field-value"><?php echo htmlspecialchars($dadosFormando['nome']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Apelido</p>
                            <p class="field-value"><?php echo htmlspecialchars($dadosFormando['apelido']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secção: Académico -->
            <div class="mb-4">
                <div class="section-label">
                    <i class="fas fa-graduation-cap" style="font-size:12px"></i> Dados académicos
                </div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="field-card">
                            <p class="field-label">Qualificação</p>
                            <p class="field-value"><?php echo htmlspecialchars($nomeQualificacao); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="field-card">
                            <p class="field-label">Turma</p>
                            <p class="field-value"><?php echo htmlspecialchars($nomeTurma); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secção: Pedido -->
            <div class="mb-4">
                <div class="section-label">
                    <i class="fas fa-briefcase" style="font-size:12px"></i> Dados do pedido
                </div>
                <div class="row g-2">
                    <div class="col-12">
                        <div class="field-card">
                            <p class="field-label">Empresa</p>
                            <p class="field-value"><?php echo htmlspecialchars($dados['empresa']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Contacto principal</p>
                            <p class="field-value"><?php echo htmlspecialchars($dados['contactoPrincipal']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Contacto secundário</p>
                            <p class="field-value"><?php echo htmlspecialchars($dados['contactoSecundario']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="field-card">
                            <p class="field-label">Email pessoal</p>
                            <p class="field-value"><?php echo htmlspecialchars($dados['email']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aviso -->
            <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-0">
                <i class="fas fa-info-circle" style="font-size:13px"></i>
                <small>Após confirmação, o pedido ficará <strong>pendente</strong> de aprovação por um administrador.</small>
            </div>

            <?php endif; ?>
        </div>

        <div class="preview-footer">
            <a href="formularioDeCartaDeEstagio.php" class="btn btn-secondary shadow-sm px-5 py-2 fw-bold text-white">
                <i class="fas fa-arrow-left me-1"></i> Voltar e corrigir
            </a>

            <?php if ($dadosFormando): ?>
            <form action="../../Controller/Estagio/FormularioDeCartaDeEstagio.php" method="POST">
                <?php echo CSRFProtection::getTokenField(); ?>
                <input type="hidden" name="fromPreview" value="1">
                <button type="submit" class="btn btn-success shadow-sm px-5 py-2 fw-bold text-white">
                    <i class="fas fa-check me-1"></i> Confirmar e enviar
                </button>
            </form>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>