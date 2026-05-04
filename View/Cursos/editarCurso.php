<?php
if (!$_GET['id_curso']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/cursos/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['id_curso'];

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
            c.*, q.id_qualificacao AS qID, q.descricao AS qualificacao
        FROM curso c
        LEFT JOIN qualificacao q ON c.codigo_qualificacao = q.id_qualificacao
        WHERE id_curso = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/cursos/listar' : '/estagio/formando'));
    exit();
}

$curso = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
<main class="container mt-4">
    <h2 class="mb-4">Editar Curso</h2>

    <form id="formEditarCurso" action="/estagio/cursos/atualizar" method="POST">
        <?php echo CSRFProtection::getTokenField(); ?>
        <input type="hidden" name="id_curso" value="<?php echo htmlspecialchars($curso['id_curso']); ?>">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="codigo" class="form-label">Código</label>
                <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($curso['codigo']); ?>" required>
                <span class="error_form text-danger small" id="area_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($curso['nome']); ?>" required>
                <span class="error_form text-danger small" id="nome_error_message"></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($curso['descricao']); ?>" required>
                <span class="error_form text-danger small" id="area_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="sigla" class="form-label">Sigla</label>
                <input type="text" class="form-control" id="sigla" name="sigla" value="<?php echo htmlspecialchars($curso['sigla']); ?>" required>
                <span class="error_form text-danger small" id="nome_error_message"></span>
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
                            <?= (int)$qual['id_qualificacao'] === (int)$curso['qID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($qual['descricao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="error_form text-danger small" id="qualificacao_error_message"></span>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/cursos/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
        </div>
    </form>
</main>

<!-- Bootstrap JS -->
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/editarCurso.js"></script>
</body>

</html>