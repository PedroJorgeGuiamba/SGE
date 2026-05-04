<?php
if (!$_GET['id_formando']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/formando/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['id_formando'];

require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

$sql = "SELECT
            f.*
        FROM formando f
        WHERE id_formando = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/formando/listar' : '/estagio/formando'));
    exit();
}

$formando = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
<main class="container mt-4">
    <h2 class="mb-4">Editar Formando</h2>

    <form id="formEditarFormando" action="/estagio/formando/atualizar" method="POST">
        <?php echo CSRFProtection::getTokenField(); ?>
        <input type="hidden" name="id_formando" value="<?php echo htmlspecialchars($formando['id_formando']); ?>">
        <input type="hidden" name="userID" value="<?php echo htmlspecialchars($formando['usuario_id']); ?>">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($formando['nome']); ?>" required>
                <span class="error_form text-danger small" id="nome_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="apelido" class="form-label">Apelido</label>
                <input type="text" class="form-control" id="apelido" name="apelido" value="<?php echo htmlspecialchars($formando['apelido']); ?>" required>
                <span class="error_form text-danger small" id="apelido_error_message"></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="codigo" class="form-label">Código</label>
                <input type="number" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($formando['codigo']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="dataDeNascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="dataDeNascimento" name="dataDeNascimento" value="<?php echo htmlspecialchars($formando['dataDeNascimento']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="naturalidade" class="form-label">Naturilidade</label>
                <input type="text" class="form-control" id="naturalidade" name="naturalidade" value="<?php echo htmlspecialchars($formando['naturalidade']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="tipoDeDocumento" class="form-label">Tipo de Documento</label>
                <input type="text" class="form-control" id="tipoDeDocumento" name="tipoDeDocumento" value="<?php echo htmlspecialchars($formando['tipoDeDocumento']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="numeroDeDocumento" class="form-label">Nº de Documento</label>
                <input type="text" class="form-control" id="numeroDeDocumento" name="numeroDeDocumento" value="<?php echo htmlspecialchars($criptografia->descriptografar($formando['numeroDeDocumento'])); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="localEmitido" class="form-label">Local Emitido</label>
                <input type="text" class="form-control" id="localEmitido" name="localEmitido" value="<?php echo htmlspecialchars($formando['localEmitido']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="dataDeEmissao" class="form-label">Data de Emissão</label>
                <input type="date" class="form-control" id="dataDeEmissao" name="dataDeEmissao" value="<?php echo htmlspecialchars($formando['dataDeEmissao']); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="NUIT" class="form-label">NUIT</label>
                <input type="number" class="form-control" id="NUIT" name="NUIT" value="<?php echo htmlspecialchars($criptografia->descriptografar($formando['NUIT'])); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="number" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($criptografia->descriptografar($formando['telefone'])); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($criptografia->descriptografar($formando['email'])); ?>" required>
                <span class="error_form text-danger small" id="nivel_error_message"></span>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/formando/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
        </div>
    </form>
</main>

<!-- Bootstrap JS -->
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/editarFormando.js"></script>
</body>

</html>