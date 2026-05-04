<?php
if (!$_GET['id_formador']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/formador/listar' : '/estagio/formador'));
    exit();
}

$id = $_GET['id_formador'];

require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

$sql = "SELECT
            f.*
        FROM formador f
        WHERE id_formador = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/formador/listar' : '/estagio/formador'));
    exit();
}

$formador = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
    <main class="container mt-4">
        <h2 class="mb-4" style="margin-top: 100px;">Editar Formador</h2>

        <form id="formEditarQualificacao" action="/estagio/formador/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="id_formador" value="<?php echo htmlspecialchars($formador['id_formador']); ?>">
            <input type="hidden" name="userID" value="<?php echo htmlspecialchars($formador['usuario_id']); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($formador['nome']); ?>" required>
                    <span class="error_form text-danger small" id="nome_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="apelido" class="form-label">Apelido</label>
                    <input type="text" class="form-control" id="apelido" name="apelido" value="<?php echo htmlspecialchars($formador['apelido']); ?>" required>
                    <span class="error_form text-danger small" id="apelido_error_message"></span>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código</label>
                    <input type="number" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($formador['codigo']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="dataDeNascimento" class="form-label">Data de Nascimento</label>
                    <input type="date" class="form-control" id="dataDeNascimento" name="dataDeNascimento" value="<?php echo htmlspecialchars($formador['dataDeNascimento']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="naturalidade" class="form-label">Naturilidade</label>
                    <input type="text" class="form-control" id="naturalidade" name="naturalidade" value="<?php echo htmlspecialchars($formador['naturalidade']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="tipoDeDocumento" class="form-label">Tipo de Documento</label>
                    <input type="text" class="form-control" id="tipoDeDocumento" name="tipoDeDocumento" value="<?php echo htmlspecialchars($formador['tipoDeDocumento']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="numeroDeDocumento" class="form-label">Nº de Documento</label>
                    <input type="text" class="form-control" id="numeroDeDocumento" name="numeroDeDocumento" value="<?php echo htmlspecialchars($criptografia->descriptografar($formador['numeroDeDocumento'])); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="localEmitido" class="form-label">Local Emitido</label>
                    <input type="text" class="form-control" id="localEmitido" name="localEmitido" value="<?php echo htmlspecialchars($formador['localEmitido']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="dataDeEmissao" class="form-label">Data de Emissão</label>
                    <input type="date" class="form-control" id="dataDeEmissao" name="dataDeEmissao" value="<?php echo htmlspecialchars($formador['dataDeEmissao']); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="NUIT" class="form-label">NUIT</label>
                    <input type="number" class="form-control" id="NUIT" name="NUIT" value="<?php echo htmlspecialchars($criptografia->descriptografar($formador['NUIT'])); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="number" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($criptografia->descriptografar($formador['telefone'])); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($criptografia->descriptografar($formador['email'])); ?>" required>
                    <span class="error_form text-danger small" id="nivel_error_message"></span>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' ? '/estagio/formador/listar' : '/estagio'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script src="/estagio/Assets/JS/editarFormador.js"></script>
</body>
</html>