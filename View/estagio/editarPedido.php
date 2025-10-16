<?php
session_start();
require_once __DIR__ . '/../../Controller/Geral/SupervisorAdmin.php';
require_once __DIR__ . '/../../middleware/auth.php';

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

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pedido de Estágio</title>
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
            <p>© 2019 TRANSCOM. DIREITOS RESERVADOS. DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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