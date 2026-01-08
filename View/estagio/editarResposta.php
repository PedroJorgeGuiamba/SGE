<?php
require_once __DIR__ . '/../../Conexao/conector.php';
// Verifica se o ID foi passado na URL
if (!isset($_GET['id_resposta'])) {
    header('Location: repostaCarta.php');
    exit();
}

$id_resposta = intval($_GET['id_resposta']);

$conector = new Conector();
$conn = $conector->getConexao();

$sql = "
    SELECT 
    rc.id_resposta,
    rc.numero_carta,                    -- este é o id_pedido_carta
    rc.status_resposta,
    rc.data_resposta,
    rc.contato_responsavel,
    rc.data_inicio_estagio,
    rc.data_fim_estagio,
    rc.status_estagio,
    
    pc.nome,
    pc.apelido,
    pc.codigo_formando,
    pc.codigo_turma,
    pc.qualificacao,
    pc.empresa,
    pc.contactoPrincipal,
    pc.contactoSecundario,
    pc.email,
    pc.numero AS numero_sequencial,     -- número do tipo 2025/003
    pc.data_do_pedido,
    pc.hora_do_pedido

FROM resposta_carta rc
JOIN pedido_carta pc ON rc.numero_carta = pc.id_pedido_carta
WHERE rc.id_resposta = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_resposta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header('Location: portalDoAdmin.php');
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">
        <h2 class="mb-4">Editar Resposta do Pedido de Estágio</h2>

        <form id="formEditarResposta" action="../../Controller/Estagio/editarResposta.php" method="POST">
            <input type="hidden" name="id_resposta" value="<?php echo htmlspecialchars($record['id_resposta']); ?>">
            <input type="hidden" name="numero_carta" value="<?php echo htmlspecialchars($record['numero_carta']); ?>">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status_resposta" class="form-label">Status da Resposta</label>
                    <select class="form-select" id="status_resposta" name="status_resposta" required>
                        <option value="Pendente" <?php echo ($record['status_resposta'] ?? '') === 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="Aceito" <?php echo ($record['status_resposta'] ?? '') === 'Aceito' ? 'selected' : ''; ?>>Aceito</option>
                        <option value="Recusado" <?php echo ($record['status_resposta'] ?? '') === 'Recusado' ? 'selected' : ''; ?>>Recusado</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="data_resposta" class="form-label">Data da Resposta</label>
                    <input type="date" class="form-control" id="data_resposta" name="data_resposta" value="<?php echo htmlspecialchars($record['data_resposta'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="contato_responsavel" class="form-label">Contato do Responsável</label>
                    <input type="text" class="form-control" id="contato_responsavel" name="contato_responsavel" value="<?php echo htmlspecialchars($record['contato_responsavel'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="data_inicio_estagio" class="form-label">Data de Início do Estágio</label>
                    <input type="date" class="form-control" id="data_inicio_estagio" name="data_inicio_estagio" value="<?php echo htmlspecialchars($record['data_inicio_estagio'] ?? ''); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="data_fim_estagio" class="form-label">Data de Fim do Estágio</label>
                    <input type="date" class="form-control" id="data_fim_estagio" name="data_fim_estagio" value="<?php echo htmlspecialchars($record['data_fim_estagio'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="status_estagio" class="form-label">Status do Estágio</label>
                    <select class="form-select" id="status_estagio" name="status_estagio">
                        <option value="" <?php echo empty($record['status_estagio']) ? 'selected' : ''; ?>>Selecione</option>
                        <option value="Concluido" <?php echo ($record['status_estagio'] ?? '') === 'Concluido' ? 'selected' : ''; ?>>Concluído</option>
                        <option value="Nao Concluido" <?php echo ($record['status_estagio'] ?? '') === 'Nao Concluido' ? 'selected' : ''; ?>>Não Concluído</option>
                    </select>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="respostaCarta.php" class="btn btn-secondary me-md-2">Cancelar</a>
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
    <script>
        $(document).ready(function () {
            $('#formEditarResposta').submit(function (e) {
                e.preventDefault();
                console.log('Dados enviados:', $(this).serialize());
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = 'respostaCarta.php';
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
    <script src="../../Assets/JS/tema.js"></script>
</body>
</html>