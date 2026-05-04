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
    <h2 class="mb-4">Editar Qualificacao</h2>

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
<script src="/estagio/Assets/JS/editarQualificacao.js"></script>
</body>

</html>