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
                    <h3 class="fw-bold text-primary"><i class="fas fa-map-marked-alt me-2"></i>Agendar Visita de Estágio</h3>
                    <p class="text-muted small">Registe os detalhes do local e do supervisor para que a visita de avaliação possa decorrer sem constrangimentos</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/visita/salvar" method="post" id="formularioEstagio">
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
</body>

</html>