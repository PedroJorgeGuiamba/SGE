<?php
if (!isset($_GET['id_visita'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/visita/listar' : '/estagio/formando'));
    exit();
    }
    
$id = $_GET['id_visita'];

require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
$conexao = new Conector();
$criptografia = new Criptografia();
$conn = $conexao->getConexao();

$sql = "SELECT v.*
        FROM visita_estagio v
        WHERE id_visita = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/visita/listar' : '/estagio/formando'));
    exit();
}

$pedido = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">
        <h2 class="mb-4">Editar Pedido de Visita de Estágio</h2>

        <form id="formEditarPedido" action="/estagio/visita/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="id_visita" value="<?php echo htmlspecialchars($pedido['id_visita']); ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($pedido['nome']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="apelido" class="form-label">Apelido</label>
                    <input type="text" class="form-control" id="apelido" name="apelido" value="<?php echo htmlspecialchars($pedido['apelido']); ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo_formando" class="form-label">Código do Formando</label>
                    <input type="text" class="form-control" id="codigo_formando" name="codigo_formando" value="<?php echo htmlspecialchars($pedido['codigo_formando']); ?>" required>
                    <span class="error_form text-danger small" id="codigoFormando_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="contactoFormando" class="form-label">Contacto do Formando</label>
                    <input type="text" class="form-control" id="contactoFormando" name="contactoFormando" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['contactoFormando'])); ?>" required>
                    <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($pedido['empresa']); ?>" required>
                    <span class="error_form text-danger small" id="empresa_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="endereco" class="form-label">Endereço da Empresa</label>
                    <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo htmlspecialchars($pedido['endereco']); ?>" required>
                    <span class="error_form text-danger small" id="endereco_error_message"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nomeSupervisor" class="form-label">Nome do Supervisor</label>
                    <input type="text" class="form-control" id="nomeSupervisor" name="nomeSupervisor" value="<?php echo htmlspecialchars($pedido['nomeSupervisor']); ?>" required>
                    <span class="error_form text-danger small" id="nomeSupervisor_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="contactoSupervisor" class="form-label">Contacto do Supervisor</label>
                    <input type="text" class="form-control" id="contactoSupervisor" name="contactoSupervisor" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['contactoSupervisor'])); ?>">
                    <span class="error_form text-danger small" id="cSupervisor_error_message"></span>
                </div>
            </div>

            <div class="mb-3">
                <label for="dataHoraDaVisita" class="form-label">Data Da Visita</label>
                <input type="datetime-local" class="form-control" id="dataHoraDaVisita" name="dataHoraDaVisita" value="<?php echo htmlspecialchars($pedido['dataHoraDaVisita']); ?>" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                <span class="error_form text-danger small" id="dataDaVisita_error_message"></span>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/visita/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script>
        $(document).ready(function () {
            $('#formEditarPedido').submit(function (e) {
                e.preventDefault();
                console.log('Dados enviados:', $(this).serialize());
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = '<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/visita/listar' : '/estagio/formando'; ?>';
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                        alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
                    }
                });
            });
        });

        $.validator.addMethod('telefone_mz', function(value, element) {
            if (this.optional(element)) return true;
            return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
        }, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');


        $("#formEditarPedido").validate({
            rules: {
                codigo_formando: {
                    required: true,
                    digits: true
                },
                nome: {
                    required: true,
                    minlength: 2
                },
                apelido: {
                    required: true,
                    minlength: 2
                },
                empresa: {
                    required: true,
                    minlength: 2
                },
                contactoPrincipal: {
                    required: true,
                    telefone_mz: true
                },
                endereco: {
                    required: true,
                    minlength: 2
                },
                nomeSupervisor: {
                    required: true,
                    minlength: 2
                },
                contactoSupervisor: {
                    required: true,
                    telefone_mz: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                codigo_formando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                nome: {
                    required: "Informe o nome",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                apelido: {
                    required: "Informe o apelido",
                    minlength: "O apelido deve ter pelo menos 2 caracteres."
                },
                empresa: {
                    required: "Informe o nome da empresa.",
                    minlength: "O nome da empresa deve ter pelo menos 2 caracteres."
                },
                contactoPrincipal: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                contactoSupervisor: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                nomeSupervisor: {
                    required: "Informe o nome do supervisor",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                endereco: {
                    required: "Informe o endereço da empresa.",
                    minlength: "O Endereço da deve ter mais de 2 letras."
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