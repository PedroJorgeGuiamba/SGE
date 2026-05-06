<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include_once __DIR__ . '/../../Controller/Formando/Home.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';

SecurityHeaders::setFull();

if (!isset($conn) || $conn === null) {
    $conector = new Conector();
    $conn = $conector->getConexao();
}

$userId = NotificationHelper::sanitizeUserId($_SESSION['usuario_id'] ?? 0);

// Verificar se o formando já confirmou seu código
if (strtolower($_SESSION['role'] ?? '') === 'formando') {
    if (!isset($_SESSION['codigo_formando'])) {
        header("Location: /estagio/login/confirmar-user");
        exit();
    }
}

NotificationHelper::handleAction($conn, $userId, $_POST ?? []);

$unreadCount = NotificationHelper::getUnreadCount($conn, $userId);
$notifications = NotificationHelper::getNotifications($conn, $userId);

$themeValue = isset($_SESSION['theme']) ? trim($_SESSION['theme']) : 'light';
$themeValue = in_array($themeValue, ['light', 'dark', 'auto']) ? $themeValue : 'light';
?>

<?php require_once __DIR__ . '/../../Includes/header-form-estagio.php' ?>
<link rel="stylesheet" href="/estagio/Assets/CSS/formando.css">

    <main class="portal-section">
        <div class="container">
            <div class="section-header">
                <div class="icon-badge">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h2>Inserir Avaliação</h2>
                </div>
            </div>
            <div class="formulario">
                <form action="/estagio/avaliacao-estagio/salvar" method="post" id="formularioAvaliacao" enctype="multipart/form-data">
                    <?php echo CSRFProtection::getTokenField(); ?>
                    <?php if (isset($_GET['erros'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
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
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-chalkboard-teacher text-muted"></i></span>
                                <select class="form-select border-start-0 ps-0" id="turma" name="turma">
                                    <option selected disabled>A carregar...</option>
                                </select>
                            </div>
                            <span class="error_form text-danger small" id="turma_error_message"></span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="empresa" class="form-label text-muted fw-bold small">Empresa</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                <input type="text" name="empresa" class="form-control border-start-0 ps-0" id="empresa" placeholder="Onde realizou">
                            </div>
                            <span class="error_form text-danger small" id="empresa_error_message"></span>
                        </div>
                        <div class="col-md-4">
                            <label for="anoTurma" class="form-label text-muted fw-bold small">Ano da Turma</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa fa-calendar text-muted"></i></span>
                                <input type="number" name="anoTurma" class="form-control border-start-0 ps-0" id="anoTurma" required>
                            </div>
                            <span class="error_form text-danger small" id="anoTurma_error_message"></span>
                        </div>
                        <div class="col-md-4">
                            <label for="relatorio_path" class="form-label text-muted fw-bold small">Relatório de Estágio (pdf, word):</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-file-pdf text-muted"></i></span>
                                <input type="file" name="relatorio_path" class="form-control" id="relatorio_path" accept="image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            </div>
                            <span class="error_form text-danger small" id="docPath_error_message"></span>
                        </div>
                    </div>

                    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor'): ?>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="resultado" class="form-label">Resultado</label>
                                <select class="form-select" name="resultado" id="resultado" required>
                                    <option value="">Selecione o resultado</option>
                                    <option value="A">Aprovado</option>
                                    <option value="NA">Não Aprovado</option>
                                </select>
                                <span class="error_form" id="resultado_error_message"></span>
                            </div>

                            <div class="col-md-4">
                                <label for="comentario" class="form-label text-muted fw-bold small">Comentário</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-comment text-muted"></i></span>
                                    <input type="tel" name="comentario" class="form-control border-start-0 ps-0" id="comentario" required>
                                </div>
                                <span class="error_form text-danger small" id="comentario_error_message"></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success form-control">Registrar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script src="/estagio/Assets/JS/avaliarEstagio.js"></script>

</body>

</html>