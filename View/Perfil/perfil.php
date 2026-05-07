<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$desc = new Criptografia();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /estagio/login');
    exit;
}

$userId = $_SESSION['usuario_id'];
SecurityHeaders::setFull();

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);
NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

// Handle password update if form submitted
$updateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPass = $_POST['current_password'] ?? '';
    $newPass = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    if (strlen($newPass) < 6 || $newPass !== $confirmPass) {
        $updateMessage = '<div class="alert alert-danger">As senhas não coincidem ou são muito curtas.</div>';
    } else {
        // Fetch current hashed password
        $sql = "SELECT password FROM usuarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($currentPass, $user['password'])) {
            // Update with new hash
            $newHash = password_hash($newPass, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newHash, $userId);
            if ($stmt->execute()) {
                $updateMessage = '<div class="alert alert-success">Senha atualizada com sucesso!</div>';
            } else {
                $updateMessage = '<div class="alert alert-danger">Erro ao atualizar senha.</div>';
            }
            $stmt->close();
        } else {
            $updateMessage = '<div class="alert alert-danger">Senha atual incorreta.</div>';
        }
    }
}

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


// Fetch empresa if applicable
$formando = null;
if ($user && $user['role'] === 'formando') {
    $sql = "SELECT * FROM formando WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $formando = $result->fetch_assoc();
    $stmt->close();
}
$formador = null;
if ($user && $user['role'] === 'formador') {
    $sql = "SELECT * FROM formador WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $formador = $result->fetch_assoc();
    $stmt->close();
}
$supervisor = null;
if ($user && $user['role'] === 'supervisor') {
    $sql = "SELECT s.*, f.*
            FROM supervisor s 
            LEFT JOIN formador f ON f.usuario_id = s.usuario_id
            WHERE s.usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $supervisor = $result->fetch_assoc();
    $stmt->close();
}

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo htmlspecialchars($themeValue, ENT_QUOTES, 'UTF-8') ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Supervisor</title>
    <link rel="icon" href="https://www.itc.ac.mz/wp-content/uploads/2020/03/cropped-logobackgsite_ITC-2-32x32.png" sizes="32x32">
    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="/estagio/Assets/CSS/notifications.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/chart.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/header.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/global.css">
    <link rel="stylesheet" href="/estagio/Assets/CSS/formando.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/estagio/Assets/JS/SessionManager.js"
        data-session-config='{"timeoutMinutes":30,"heartbeatInterval":60}'>
    </script>
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
                            <li class="nav-item">
                                <a class="nav-link fs-5" href="/estagio/perfil" title="Perfil">
                                    <i class="fa-solid fa-user"></i>
                                </a>
                            </li>
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
                    <a class="nav-link fw-semibold text-dark active" href="/estagio/<?php echo $_SESSION['role'] === 'admin'
                                                                                        ? 'admin'
                                                                                        : ($_SESSION['role'] === 'supervisor'
                                                                                            ? 'supervisor'
                                                                                            : 'formando'); ?>">
                        <i class="fas fa-home fa-fw me-1 text-primary"></i> Home
                    </a>
                </li>
                <li class="nav-item mx-1 dropdown">
                    <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="pedidosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle fa-fw me-1 text-success"></i> Fazer Pedidos
                    </a>
                    <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="pedidosDropdown">
                        <li><a class="dropdown-item" href="/estagio/estagio/criar"><i class="fas fa-envelope-open-text fa-fw me-2 text-secondary"></i> Pedido de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/credencial/criar"><i class="fas fa-id-badge fa-fw me-2 text-secondary"></i> Credencial de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/visita/criar"><i class="fas fa-map-marked-alt fa-fw me-2 text-secondary"></i> Visita de Estágio</a></li>
                        <li><a class="dropdown-item" href="/estagio/avaliacao-estagio/criar"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Avaliações De Estágio</a></li>
                    </ul>
                </li>
                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor'): ?>
                    <li class="nav-item mx-1 dropdown">
                        <a class="nav-link fw-semibold text-dark dropdown-toggle" href="#" id="listasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-list-ul fa-fw me-1 text-info"></i> Listas
                        </a>
                        <ul class="dropdown-menu shadow border-0 mt-2" aria-labelledby="listasDropdown">
                            <li><a class="dropdown-item" href="/estagio/estagio/listar"><i class="fas fa-file-alt fa-fw me-2 text-secondary"></i> Pedidos de Estágio</a></li>
                            <li><a class="dropdown-item" href="/estagio/credencial/listar"><i class="fas fa-id-card-clip fa-fw me-2 text-secondary"></i> Pedidos de Credencial</a></li>
                            <li><a class="dropdown-item" href="/estagio/visita/listar"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Pedidos de Visita</a></li>
                            <li><a class="dropdown-item" href="/estagio/avaliacao-estagio/listar"><i class="fas fa-route fa-fw me-2 text-secondary"></i> Avaliações De Estágio</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mx-1">
                        <a class="nav-link fw-semibold text-dark" href="/estagio/relatorio">
                            <i class="fas fa-file-pdf fa-fw me-1 text-danger"></i> Gerar Relatórios
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container" id="infoContainer">
        <div class="section-header">
            <div class="icon-badge">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h2>Informações Recuperadas</h2>
                <p>Consulte e gerencie todas as suas Informações no Sistema</p>
            </div>
        </div>
        <?php echo $updateMessage; // Display update message if any ?>
        <?php if (!$user): ?>
            <div class="alert alert-warning">Nenhum utilizador encontrado.</div>
        <?php else: ?>
            <!-- Dados do Utilizador -->
            <div class="card mb-4">
                <div class="card-header table-card-header"><strong>Dados do Utilizador</strong></div>
                <div class="card-body" id="pedidosTable">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($desc->descriptografar($user['Email'])); ?></p>
                    <p><strong>Perfil:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updatePasswordModal">Atualizar Senha</button>
                </div>
            </div>

            <?php if ($formando): ?>
                <!-- Dados da Empresa -->
                <div class="card mb-4">
                    <div class="card-header table-card-header"><strong>Dados do Formando</strong></div>
                    <div class="card-body" id="pedidosTable">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($formando['nome']); ?></p>
                        <p><strong>Apelido:</strong> <?php echo htmlspecialchars($formando['apelido']); ?></p>
                        <p><strong>Código:</strong> <?php echo htmlspecialchars($formando['codigo']); ?></p>
                        <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($formando['dataDeNascimento']); ?></p>
                        <p><strong>Naturalidade:</strong> <?php echo htmlspecialchars($formando['naturalidade']); ?></p>
                        <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($formando['tipoDeDocumento']); ?></p>
                        <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($desc->descriptografar($formando['numeroDeDocumento'])); ?></p>
                        <p><strong>Local Emitido:</strong> <?php echo htmlspecialchars($formando['localEmitido']); ?></p>
                        <p><strong>Data de Emissao:</strong> <?php echo htmlspecialchars($formando['dataDeEmissao']); ?></p>
                        <p><strong>NUIT:</strong> <?php echo htmlspecialchars($desc->descriptografar($formando['NUIT'])); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($desc->descriptografar($formando['telefone'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($formador): ?>
                <!-- Dados da Empresa -->
                <div class="card mb-4">
                    <div class="card-header table-card-header"><strong>Dados do Formador</strong></div>
                    <div class="card-body" id="pedidosTable">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($formador['nome']); ?></p>
                        <p><strong>Apelido:</strong> <?php echo htmlspecialchars($formador['apelido']); ?></p>
                        <p><strong>Código:</strong> <?php echo htmlspecialchars($formador['codigo']); ?></p>
                        <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($formador['dataDeNascimento']); ?></p>
                        <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($formador['tipoDeDocumento']); ?></p>
                        <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($desc->descriptografar($formador['numeroDeDocumento'])); ?></p>
                        <p><strong>Local Emitido:</strong> <?php echo htmlspecialchars($formador['localEmitido']); ?></p>
                        <p><strong>Data de Emissao:</strong> <?php echo htmlspecialchars($formador['dataDeEmissao']); ?></p>
                        <p><strong>NUIT:</strong> <?php echo htmlspecialchars($desc->descriptografar($formador['NUIT'])); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($desc->descriptografar($formador['telefone'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($supervisor): ?>
                <!-- Dados da Empresa -->
                <div class="card mb-4">
                    <div class="card-header"><strong>Dados do Supervisor</strong></div>
                    <div class="card-body" id="pedidosTable">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($supervisor['nome']); ?></p>
                        <p><strong>Apelido:</strong> <?php echo htmlspecialchars($supervisor['apelido']); ?></p>
                        <p><strong>Código:</strong> <?php echo htmlspecialchars($supervisor['codigo']); ?></p>
                        <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($supervisor['dataDeNascimento']); ?></p>
                        <p><strong>Tipo de Documento:</strong> <?php echo htmlspecialchars($supervisor['tipoDeDocumento']); ?></p>
                        <p><strong>Número de Documento:</strong> <?php echo htmlspecialchars($desc->descriptografar($supervisor['numeroDeDocumento'])); ?></p>
                        <p><strong>Local Emitido:</strong> <?php echo htmlspecialchars($supervisor['localEmitido']); ?></p>
                        <p><strong>Data de Emissao:</strong> <?php echo htmlspecialchars($supervisor['dataDeEmissao']); ?></p>
                        <p><strong>NUIT:</strong> <?php echo htmlspecialchars($desc->descriptografar($supervisor['NUIT'])); ?></p>
                        <p><strong>Telefone:</strong> <?php echo htmlspecialchars($desc->descriptografar($supervisor['telefone'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <!-- Modal para Atualizar Senha -->
    <div class="modal fade" id="updatePasswordModal" tabindex="-1" aria-labelledby="updatePasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePasswordLabel">Atualizar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="updatePasswordForm">
                        <input type="hidden" name="update_password" value="1">
                        <div class="form-group">
                            <label for="currentPassword">Senha Atual</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">Nova Senha</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirmação da Nova Senha</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required minlength="6">
                        </div>
                        <div id="passwordError" class="text-danger" style="display:none;">As senhas não coincidem ou são muito curtas.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>