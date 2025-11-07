<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../Controller/Geral/SupervisorAdmin.php';

SecurityHeaders::setFull();

// Verificar se o parâmetro numero existe
if (!isset($_GET['numero']) || !is_numeric($_GET['numero']) || intval($_GET['numero']) <= 0) {
    $_SESSION['flash_error'] = 'ID da resposta inválido ou não fornecido.';
    header("Location: /estagio/View/estagio/respostaCarta.php");
    exit;
}
$numero = intval($_GET['numero']);
?>

<!DOCTYPE html>
<html lang="pt-pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Avaliação de Estágio</title>

    <!-- BootStrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fallback local para jQuery -->
    <script>window.jQuery || document.write('<script src="../../Scripts/jquery-3.6.0.min.js"><\/script>');</script>
</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png" alt="ITC Logo">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="https://www.instagram.com/itc.ac">Instagram</a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="https://pt-br.facebook.com/itc.transcom">Facebook</a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="https://plus.google.com/share?url=https://simpleshare.buttons.com">Google</a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simpleshare.buttons.com">Linkedin</a>
                            </li>
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
                    <a class="nav-link" href="formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listaDePedidos.php">Pedidos de Estágio</a>
                </li>
            </ul>
        </nav>
    </header>

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