<?php
if (!$_GET['id_avaliacao']) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/avaliacao-estagio/listar' : '/estagio/formando'));
    exit();
}

$id = $_GET['id_avaliacao'];

require_once __DIR__ . '/../../Helpers/CSRFProtection.php';
require_once __DIR__ . '/../../Conexao/conector.php';
require_once __DIR__ . '/../../Helpers/Criptografia.php';
$conexao = new Conector();
$conn = $conexao->getConexao();
$criptografia = new Criptografia();

$sql = "SELECT a.*, p.nome as nome, p.apelido as apelido,
            p.qualificacao, q.descricao AS qualificacao_descricao, q.id_qualificacao as nivel, t.nome as nomeT, t.codigo as codigoT
        FROM avaliacao_estagio a
        JOIN pedido_carta p ON a.id_pedido_estagio = p.id_pedido_carta
        LEFT JOIN qualificacao q ON p.qualificacao = q.id_qualificacao
        LEFT JOIN turma t ON a.turma = t.codigo
        WHERE a.id_avaliacao = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: ' . ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/avaliacao-estagio/listar' : '/estagio/formando'));
    exit();
}

$pedido = $result->fetch_assoc();
$stmt->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>
    <main class="container mt-4">
        <h2 class="mb-4">Editar Pedido de Estágio</h2>

        <form id="formEditarPedido" action="/estagio/avaliacao-estagio/atualizar" method="POST">
            <?php echo CSRFProtection::getTokenField(); ?>
            <input type="hidden" name="id_avaliacao" value="<?php echo htmlspecialchars($pedido['id_avaliacao']); ?>">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nome" value="<?php echo htmlspecialchars($pedido['nome']); ?>"  readonly>
                </div>
                <div class="col-md-6">
                    <label for="apelido" class="form-label">Apelido</label>
                    <input type="text" class="form-control" id="apelido" value="<?php echo htmlspecialchars($pedido['apelido']); ?>" readonly>
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
                    <label for="ano_turma" class="form-label">Ano que frequentou a Turma</label>
                    <input type="text" class="form-control" id="ano_turma" name="ano_turma" value="<?php echo htmlspecialchars($pedido['ano_turma']); ?>" required>
                    <span class="error_form text-danger small" id="anoTurma_error_message"></span>
                </div>
                <div class="col-md-6">
                    <label for="resultado" class="form-label">Resultado</label>
                    <select class="form-select" name="resultado" id="resultado" required>
                        <option value="">Selecione o resultado</option>
                        <option value="A">Aprovado</option>
                        <option value="NA">Não Aprovado</option>
                    </select>
                    <span class="error_form text-danger small" id="resultado_error_message"></span>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="comentario" class="form-label">Comentário</label>
                    <input type="text" class="form-control" id="comentario" name="comentario" value="<?php echo htmlspecialchars($pedido['comentario']); ?>">
                    <span class="error_form text-danger small" id="comentario_error_message"></span>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? '/estagio/avaliacao-estagio/listar' : '/estagio/formando'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
    <script src="/estagio/Assets/JS/editarAvaliacao.js"></script>
</body>
</html>