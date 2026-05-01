<?php
if (!$_GET['id_qualificacao']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/qualificacao/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['id_qualificacao'];

require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

$sql = "SELECT
            q.*
        FROM qualificacao q
        WHERE id_qualificacao = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/qualificacao/listar' : '/estagio/formando'));
    exit();
}

$qualificacao = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
    <main class="container mt-4">
        <h2 class="mb-4" style="margin-top: 100px;">Editar Qualificacao</h2>

        <form id="formEditarQualificacao" action="/estagio/qualificacao/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="id_qualificacao" value="<?php echo htmlspecialchars($qualificacao['id_qualificacao']); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="qualificacao" class="form-label">Qualificação</label>
                    <input type="text" class="form-control" id="qualificacao" name="qualificacao" value="<?php echo htmlspecialchars($qualificacao['qualificacao']); ?>" required>
                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="descricao" class="form-label">Descrição</label>
                    <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($qualificacao['descricao']); ?>" required>
                    <span class="error_form text-danger small" id="descricao_error_message"></span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nivel" class="form-label">Nível</label>
                    <input type="text" class="form-control" id="nivel" name="nivel" value="<?php echo htmlspecialchars($qualificacao['nivel']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/qualificacao/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script>
        $(document).ready(function () {
            $('#formEditarQualificacao').submit(function (e) {
                e.preventDefault();
                console.log('Dados enviados:', $(this).serialize()); // Log dos dados enviados
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = '<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/qualificacao/listar' : '/estagio/formando'; ?>';
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText); // Log detalhado do erro
                        alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
                    }
                });
            });
        });

        $("#formEditarQualificacao").validate({
            rules: {
                qualificacao: {
                    required: true,
                    digits: true
                },
                descricao: {
                    required: true,
                    minlength: 2
                },
                nivel: {
                    required: true,
                    minlength: 1
                }
            },
            messages: {
                qualificacao: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                descricao: {
                    required: "Informe o nome",
                    minlength: "A descrição deve ter pelo menos 2 caracteres."
                },
                nivel: {
                    required: "Informe o apelido",
                    minlength: "O nível deve ter pelo menos 1 caracteres."
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