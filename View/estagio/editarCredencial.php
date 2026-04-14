<?php
if (!isset($_GET['numero'])) {
    header('Location: portalDeEstudante.php');
    exit();
}

$id = $_GET['numero'];

require_once __DIR__ . '/../../Conexao/conector.php';
$conexao = new Conector();
$conn = $conexao->getConexao();

$sql = "SELECT c.*
        FROM credencial_estagio c
        WHERE id_credencial = ?

";
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
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">
        <h2 class="mb-4">Editar Pedido de Credencial de Estágio</h2>

        <form id="formEditarPedido" action="../../Controller/Estagio/editarCredencial.php" method="POST">
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
                    <input type="text" class="form-control" id="contactoFormando" name="contactoFormando" value="<?php echo htmlspecialchars($pedido['contactoFormando']); ?>" required>
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
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($pedido['email']); ?>" required>
                    <span class="error_form text-danger small" id="email_error_message"></span>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor' ? 'listaDePedidosCredencial.php' : '../../View/Formando/portalDeEstudante.php'; ?>" class="btn btn-secondary me-md-2">Cancelar</a>
                <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
            </div>
        </form>
    </main>

    <!-- Bootstrap JS -->
    <?php require_once __DIR__ . '/../../Includes/footer.php' ?>
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

        $.validator.addMethod('telefone_mz', function(value, element) {
            if (this.optional(element)) return true;
            return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
        }, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');


        $("#formEditarPedido").validate({
            rules: {
                codigo_formando: {
                    required: true,
                    digits: true
                },
                nome: {
                    required: true,
                    minlength: 2
                },
                apelido: {
                    required: true,
                    minlength: 2
                },
                empresa: {
                    required: true,
                    minlength: 2
                },
                contactoPrincipal: {
                    required: true,
                    telefone_mz: true
                },
                contactoSecundario: {
                    required: true,
                    telefone_mz: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                codigo_formando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                nome: {
                    required: "Informe o nome",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                apelido: {
                    required: "Informe o apelido",
                    minlength: "O apelido deve ter pelo menos 2 caracteres."
                },
                empresa: {
                    required: "Informe o nome da empresa.",
                    minlength: "O nome da empresa deve ter pelo menos 2 caracteres."
                },
                contactoPrincipal: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                contactoSecundario: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                email: {
                    required: "Informe o e-mail.",
                    email: "Endereço de e-mail inválido."
                }
            },
            errorClass: "is-invalid",
            validClass: "is-valid",
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });
    </script>
</body>
</html>