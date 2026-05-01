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

<main class="container mb-5" style="margin-top: 40px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary"><i class="fas fa-file-signature me-2"></i>Pedido de Carta de Estágio</h3>
                    <p class="text-muted small">Preencha os dados abaixo com precisão para gerar e submeter o seu pedido de estágio</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/estagio/preview" method="post" id="formularioEstagio">
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
                                    <input type="tel" name="contactoPrincipal" class="form-control border-start-0 ps-0" id="contactoPrincipal" placeholder="84xxxxxxx" required>
                                </div>
                                <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="contactoSecundario" class="form-label text-muted fw-bold small">Tel. Alternativo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="tel" name="contactoSecundario" class="form-control border-start-0 ps-0" id="contactoSecundario" placeholder="84xxxxxxx" required>
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
            url: '/estagio/api/qualificacao',
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
            url: '/estagio/api/turmas',
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