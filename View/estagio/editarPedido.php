<?php
if (!isset($_GET['numero'])) {
    header('Location: portalDeEstudante.php');
    exit();
}

$id = $_GET['numero'];

require_once __DIR__ . '/../../Conexao/conector.php';
$conexao = new Conector();
$conn = $conexao->getConexao();

$sql = "SELECT * FROM pedido_carta WHERE numero = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: portalDeEstudante.php');
    exit();
}

$pedido = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">
        <h2 class="mb-4">Editar Pedido de Estágio</h2>

        <form id="formEditarPedido" action="../../Controller/Estagio/editarPedido.php" method="POST">
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
                </div>
                <div class="col-md-6">
                    <label for="qualificacao" class="form-label">Qualificação</label>
                    <input type="text" class="form-control" id="qualificacao" name="qualificacao" value="<?php echo htmlspecialchars($pedido['qualificacao']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo_turma" class="form-label">Turma</label>
                    <input type="text" class="form-control" id="codigo_turma" name="codigo_turma" value="<?php echo htmlspecialchars($pedido['codigo_turma']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="empresa" class="form-label">Empresa</label>
                    <input type="text" class="form-control" id="empresa" name="empresa" value="<?php echo htmlspecialchars($pedido['empresa']); ?>" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contactoPrincipal" class="form-label">Contacto Principal</label>
                    <input type="text" class="form-control" id="contactoPrincipal" name="contactoPrincipal" value="<?php echo htmlspecialchars($pedido['contactoPrincipal']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="contactoSecundario" class="form-label">Contacto Secundário</label>
                    <input type="text" class="form-control" id="contactoSecundario" name="contactoSecundario" value="<?php echo htmlspecialchars($pedido['contactoSecundario']); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($pedido['email']); ?>" required>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="listaDePedidos.php" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM. DIREITOS RESERVADOS. DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../Assets/JS/tema.js"></script>
    <script>
        $(document).ready(function () {
            $('#formEditarPedido').submit(function (e) {
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
                            window.location.href = 'listaDePedidos.php';
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
    </script>
</body>
</html>