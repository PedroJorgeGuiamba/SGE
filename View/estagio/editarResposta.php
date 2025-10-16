<?php
session_start();
require_once __DIR__ . '/../../Controller/Geral/SupervisorAdmin.php';
require_once __DIR__ . '/../../middleware/auth.php';

// Verifica se o ID foi passado na URL
if (!isset($_GET['id_resposta'])) {
    header('Location: repostaCarta.php');
    exit();
}

$id_resposta = intval($_GET['id_resposta']);

$conector = new Conector();
$conn = $conector->getConexao();

$sql = "
    SELECT rc.id_resposta, rc.numero_carta, rc.status_resposta, rc.data_resposta, rc.contato_responsavel,
            rc.data_inicio_estagio, rc.data_fim_estagio, rc.status_estagio,
            pc.nome, pc.apelido, pc.codigo_formando, pc.qualificacao, pc.codigo_turma, pc.empresa,
            pc.contactoPrincipal, pc.contactoSecundario, pc.email
    FROM resposta_carta rc
    JOIN pedido_carta pc ON rc.numero_carta = pc.numero
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

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Resposta do Pedido de Estágio</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
</head>
<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="Logo ITC">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Nav Secundária -->
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="../../View/<?php echo $_SESSION['role'] === 'admin' ? 'Admin/portalDoAdmin.php' : 'Supervisor/portalDoSupervisor.php'; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
            </ul>
        </nav>
    </header>

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
            <p>© 2019 TRANSCOM. DIREITOS RESERVADOS. DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
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
</body>
</html>