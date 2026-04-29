<?php
if (!$_GET['id_supervisor']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/supervisor/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['id_supervisor'];

require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

$stmt_quals = $conn->prepare("SELECT id_qualificacao, descricao FROM qualificacao ORDER BY descricao");
$stmt_quals->execute();
$qualificacoes = $stmt_quals->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_quals->close();

$sql = "SELECT
            s.*, q.id_qualificacao AS qID, q.descricao AS qualificacao
        FROM supervisor s
        LEFT JOIN qualificacao q ON s.id_qualificacao = q.id_qualificacao
        WHERE id_supervisor = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/supervisor/listar' : '/estagio/formando'));
    exit();
}

$supervisor = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
    <main class="container mt-4">
        <h2 class="mb-4" style="margin-top: 100px;">Editar Supervisor</h2>

        <form id="formEditarSupervisor" action="/estagio/supervisor/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="id_supervisor" value="<?php echo htmlspecialchars($supervisor['id_supervisor']); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($supervisor['nome_supervisor']); ?>" required>
                    <span class="error_form text-danger small" id="nome_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="area" class="form-label">Área</label>
                    <input type="text" class="form-control" id="area" name="area" value="<?php echo htmlspecialchars($supervisor['area']); ?>" required>
                    <span class="error_form text-danger small" id="area_error_message"></span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="id_qualificacao" class="form-label">Qualificação</label>
                        <select name="id_qualificacao" id="id_qualificacao" class="form-select">
                            <option value="">-- Selecione uma Qualificação --</option>
                            <?php foreach ($qualificacoes as $qual): ?>
                                <option 
                                    value="<?= htmlspecialchars($qual['id_qualificacao']) ?>"
                                    <?= (int)$qual['id_qualificacao'] === (int)$supervisor['qID'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($qual['descricao']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/supervisor/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script>
        $(document).ready(function () {
            $('#formEditarSupervisor').submit(function (e) {
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
                            window.location.href = '<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/supervisor/listar' : '/estagio/formando'; ?>';
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

        $("#formEditarSupervisor").validate({
            rules: {
                nome: {
                    required: true,
                    minlength: 2
                },
                area: {
                    required: true,
                    minlength: 2
                },
                qualificacao: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: "Campo obrigatório.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                area: {
                    required: "Informe o nome",
                    minlength: "A área deve ter pelo menos 2 caracteres."
                },
                qualificacao: {
                    required: "Informe o apelido"
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