<?php
// Verificar se o parâmetro numero existe
if (!isset($_GET['numero']) || !is_numeric($_GET['numero']) || intval($_GET['numero']) <= 0) {
    $_SESSION['flash_error'] = 'ID da resposta inválido ou não fornecido.';
    header("Location: /estagio/View/estagio/respostaCarta.php");
    exit;
}
$numero = intval($_GET['numero']);
?>
<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>
    <main>
        <div class="formulario">

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="../../Controller/Estagio/avaliarEstagio.php?numero=<?php echo $numero; ?>" method="post" id="formularioAvaliacao" enctype="multipart/form-data">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="resultado" class="form-label">Resultado</label>
                        <select class="form-select" name="resultado" id="resultado" required>
                            <option value="">Selecione o resultado</option>
                            <option value="A">Aprovado</option>
                            <option value="NA">Não Aprovado</option>
                        </select>
                        <span class="error_form" id="resultado_error_message"></span>
                    </div>
                    
                    <div class="form-group col-md-4">
                        <label for="imagem_doc_path" class="form-label">Relatório de Estágio (pdf, word):</label>
                        <input type="file" name="imagem_doc_path" class="form-control" id="imagem_doc_path" accept="image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        <span class="error_form" id="imagem_error_message"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success form-control">Registrar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>

    <!-- Scripts do Bootstrap e jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        // Verificar se jQuery está carregado
        if (typeof jQuery === 'undefined') {
            console.error('jQuery não foi carregado!');
        } else {
            console.log('jQuery carregado com sucesso, versão:', jQuery.fn.jquery);
        }

        // Inicializar tooltips do Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Validação do formulário
        $("#formularioAvaliacao").validate({
            rules: {
                resultado: {
                    required: true
                }
            },
            messages: {
                resultado: {
                    required: "Informe o resultado."
                }
            },
            errorClass: "is-invalid",
            validClass: "is-valid",
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                // Usar FormData para suportar upload de arquivos
                var formData = new FormData(form);
                
                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                        alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
                    }
                });
                return false; // Previne o submit padrão
            }
        });
    </script>

</body>

</html>
<!-- <script>
$(document).ready(function () {
$('#formularioAvaliacao').submit(function (e) {
    e.preventDefault();
    console.log('Dados enviados:', $(this).serialize());
    $.ajax({
        url: '../../Controller/Estagio/avaliarEstagio.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                alert(response.message);
                window.location.href = response.redirect;
            } else {
                alert(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
            alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
        }
    });
});
});
</script> -->