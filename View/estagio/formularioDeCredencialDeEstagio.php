<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
include_once __DIR__ . '/../../Controller/Geral/FormandoAdmin.php';
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

<?php require_once __DIR__ . '/../../Includes/header-form-estagio.php' ?>
<main class="container mb-5" style="margin-top: 20px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary"><i class="fas fa-id-badge me-2"></i>Pedido de Credencial de Estágio</h3>
                    <p class="text-muted small">Preencha com o seu registo, destino e contacto para a geração da credencial</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/credencial/salvar" method="post" id="formularioEstagio" enctype="multipart/form-data">
                        <?php echo CSRFProtection::getTokenField(); ?>
                        <?php if (isset($_GET['erros'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="codigoFormando" class="form-label text-muted fw-bold small">Código do Formando</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                    <input type="number" name="codigoFormando" class="form-control border-start-0 ps-0" id="codigoFormando" placeholder="Ex: 123456" required>
                                </div>
                                <span class="error_form text-danger small" id="codigoFormando_error_message"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="empresa" class="form-label text-muted fw-bold small">Empresa Alvo / Destino</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" name="empresa" class="form-control border-start-0 ps-0" id="empresa" placeholder="Ex: Banco de Moçambique" required>
                                </div>
                                <span class="error_form text-danger small" id="empresa_error_message"></span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="contactoFormando" class="form-label text-muted fw-bold small">Contacto do Formando</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                    <input type="tel" name="contactoFormando" class="form-control border-start-0 ps-0" id="contactoFormando" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" placeholder="Ex: 84xxxxxxx ou +258..." required>
                                </div>
                                <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-muted fw-bold small">Email Pessoal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="estudante@dominio.com" required>
                                </div>
                                <span class="error_form text-danger small" id="email_error_message"></span>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-4 justify-content-center">
                            <div class="col-md-6">
                                <label for="carta_path" class="form-label text-muted fw-bold small">Carta de Resposta(PDF/Word/Imagem)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-file text-muted"></i></span>
                                    <input type="file" name="carta_path" id="carta_path" accept="image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                                </div>
                                <span class="error_form text-danger small" id="carta_path_error_message"></span>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-user-check me-1"></i> Submeter Pedido</button>
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
            empresa: {
                required: true,
                minlength: 2
            },
            contactoFormando: {
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
            empresa: {
                required: "Informe o nome da empresa.",
                minlength: "O nome deve ter pelo menos 2 caracteres."
            },
            contactoFormando: {
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
</body>

</html>