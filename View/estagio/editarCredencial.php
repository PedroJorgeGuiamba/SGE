<?php
if (!isset($_GET['numero'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/credencial/listar' : '/estagio/formando'));
exit();
}

$id = $_GET['numero'];
    
require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia(); 

$sql = "SELECT c.*
        FROM credencial_estagio c
        WHERE id_credencial = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/credencial/listar' : '/estagio/formando'));
    exit();
}

$pedido = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

<main class="container mt-4">
    <h2 class="mb-4">Editar Pedido de Credencial de Estágio</h2>

    <form id="formEditarPedido" action="/estagio/credencial/atualizar" method="POST">
        <?php echo CSRFProtection::getTokenField(); ?>
        <input type="hidden" name="id_credencial" value="<?php echo htmlspecialchars($pedido['id_credencial']); ?>">

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
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['email'])); ?>" required>
                <span class="error_form text-danger small" id="email_error_message"></span>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/credencial/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
        </div>
    </form>
</main>

<!-- Bootstrap JS -->
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/editarCredencial.js"></script>
</body>

</html>