<?php
if (!$_GET['codigo']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/turma/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['codigo'];

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

$stmt_curs = $conn->prepare("SELECT codigo, nome FROM curso ORDER BY nome");
$stmt_curs->execute();
$cursos = $stmt_curs->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_curs->close();

$sql = "SELECT
            t.*, q.id_qualificacao AS qID, q.descricao AS qualificacao, c.codigo AS cID, c.nome AS curso
        FROM turma t
        LEFT JOIN qualificacao q ON t.codigo_qualificacao = q.id_qualificacao
        LEFT JOIN curso c ON t.codigo_curso = c.codigo
        WHERE t.codigo = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/turma/listar' : '/estagio/formando'));
    exit();
}

$turma = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
    <main class="container mt-4">
        <h2 class="mb-4" style="margin-top: 100px;">Editar Turma</h2>

        <form id="formEditarSupervisor" action="/estagio/turma/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($turma['codigo']); ?>" readonly required>
                    <span class="error_form text-danger small" id="area_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($turma['nome']); ?>" required>
                    <span class="error_form text-danger small" id="nome_error_message"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo_qualificacao" class="form-label">Qualificação</label>
                        <select name="codigo_qualificacao" id="codigo_qualificacao" class="form-select">
                            <option value="">-- Selecione uma Qualificação --</option>
                            <?php foreach ($qualificacoes as $qual): ?>
                                <option 
                                    value="<?= htmlspecialchars($qual['id_qualificacao']) ?>"
                                    <?= (int)$qual['id_qualificacao'] === (int)$turma['qID'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($qual['descricao']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="codigo_curso" class="form-label">Curso</label>
                        <select name="codigo_curso" id="codigo_curso" class="form-select">
                            <option value="">-- Selecione um Curso --</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option 
                                    value="<?= htmlspecialchars($curso['codigo']) ?>"
                                    <?= (int)$curso['codigo'] === (int)$turma['cID'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($curso['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <span class="error_form text-danger small" id="qualificacao_error_message"></span>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/turma/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
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
                            window.location.href = '<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/turma/listar' : '/estagio/formando'; ?>';
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