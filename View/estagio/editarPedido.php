<?php
if (!$_GET['numero']) {
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

$sql = "SELECT p.*,
            q.descricao AS qualificacao_descricao,
            q.id_qualificacao AS nivel,
            t.nome AS nomeT,
            t.codigo AS codigoT
        FROM pedido_carta p
        JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        JOIN turma t ON p.codigo_turma = t.codigo
        WHERE numero = ?

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
        <h2 class="mb-4">Editar Pedido de Estágio</h2>

        <form id="formEditarPedido" action="/estagio/estagio/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="numero" value="<?php echo htmlspecialchars($pedido['numero']); ?>">
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
                    <label for="qualificacao" class="form-label">Qualificação</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($pedido['qualificacao_descricao']); ?>" readonly>
                    
                    <input type="hidden" name="qualificacao" id="qualificacao" value="<?php echo htmlspecialchars($pedido['nivel']); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo_turma" class="form-label">Turma</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($pedido['nomeT']); ?>" readonly>
                    
                    <input type="hidden" name="codigo_turma" id="codigo_turma" value="<?php echo htmlspecialchars($pedido['codigoT']); ?>">
                </div>
                <div class="col-md-6">
                    <label for="empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($pedido['empresa']); ?>" required>
                    <span class="error_form text-danger small" id="empresa_error_message"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contactoPrincipal" class="form-label">Contacto Principal</label>
                    <input type="text" class="form-control" id="contactoPrincipal" name="contactoPrincipal" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['contactoPrincipal'])); ?>" required>
                    <span class="error_form text-danger small" id="cPrincipal_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="contactoSecundario" class="form-label">Contacto Secundário</label>
                    <input type="text" class="form-control" id="contactoSecundario" name="contactoSecundario" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['contactoSecundario'])); ?>">
                    <span class="error_form text-danger small" id="cSecundario_error_message"></span>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $criptografia->descriptografar(htmlspecialchars($pedido['email'])); ?>" required>
                <span class="error_form text-danger small" id="email_error_message"></span>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/estagio/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script src="/estagio/Assets/JS/editarPedido.js"></script>
</body>
</html>