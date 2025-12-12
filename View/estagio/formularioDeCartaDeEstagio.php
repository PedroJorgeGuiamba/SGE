<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';
include '../../Controller/Geral/FormandoAdmin.php';
require_once __DIR__ . '/../../middleware/auth.php';

SecurityHeaders::setFull();
?>

<!DOCTYPE html>
<html lang="pt-pt"  data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Estágio</title>

    <!-- BootStrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Fallback local para jQuery -->
    <script>
        window.jQuery || document.write('<script src="../../Scripts/jquery-3.6.0.min.js"><\/script>');
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/fonts/brands/boxicons-brands.min.css' rel='stylesheet'>
</head>

<body>
    <header>
        <!-- Nav principal -->
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="container-fluid">
                <img src="https://www.itc.ac.mz/wp-content/uploads/2020/07/cropped-LOGO_ITC-09.png">
                <div class="nav-modal">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText"
                        aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Instagram -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://www.instagram.com/itc.ac" class="me-3 text-white fs-4">
                                    <i class="fa-brands fa-square-instagram" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Facebook -->
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="https://pt-br.facebook.com/itc.transcom">
                                    <i class="fa-brands fa-facebook" style="color: #3a4c91"></i>
                                </a>
                            </li>
                            <!-- Google -->
                            <li class="nav-item">
                                <a class="nav-link" href="https://plus.google.com/share?url=https://simplesharebuttons.com">
                                    <i class="fab fa-google" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">
                                    <i class="fa-brands fa-linkedin-in" style="color: #3a4c91;"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <button id="themeToggle" class="btn btn-outline-secondary position-fixed bottom-0 end-0 m-3" style="z-index: 1050;">
                                    <i class="fas fa-moon"></i> <!-- ícone muda com JS -->
                                </button>
                            </li>
                            <li class="nav-item">
                                <a href="../../Controller/Auth/LogoutController.php" class="btn btn-danger">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
        </nav>


        <!-- Nav Secundária -->
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link" href="../../View/<?php echo $_SESSION['role'] === 'admin'
                                                                ? 'Admin/portalDoAdmin.php'
                                                                : ($_SESSION['role'] === 'supervisor'
                                                                    ? 'Supervisor/portalDoSupervisor.php'
                                                                    : 'Formando/portalDeEstudante.php'); ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Fazer Pedido de Estágio</a>
                </li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="listaDePedidos.php">Pedidos de Estágio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="respostaCarta.php">Resposta das Cartas</a>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>
    </header>

    <main>
        <div class="formulario">
            <form action="../../Controller/Estagio/FormularioDeCartaDeEstagio.php" method="post" id="formularioEstagio">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="codigoFormando" class="form-label">Código do Formando</label>
                        <input type="number" name="codigoFormando" class="form-control" id="codigoFormando"
                            placeholder="123456">
                        <span class="error_form" id="codigoFormando_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="qualificacao" class="form-label">Qualificação</label>
                        <select class="form-select" id="qualificacao" aria-label="Default select example" name="qualificacao">
                            <option selected>Open this select menu</option>
                        </select>
                        <span class="error_form" id="qualificacao_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="turma" class="form-label">Turma</label>
                        <select class="form-select" id="turma" aria-label="Default select example" name="turma">
                            <option selected>Open this select menu</option>
                        </select>
                        <span class="error_form" id="turma_error_message"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="empresa" class="form-label">Empresa Onde Pretende Submeter</label>
                        <input type="text" name="empresa" class="form-control" id="empresa">
                        <span class="error_form" id="empresa_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="contactoPrincipal" class="form-label">Contacto Principal</label>
                        <input type="tel" name="contactoPrincipal" class="form-control" id="contactoPrincipal"
                            pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required
                            placeholder="Ex: +258 84xxxxxxx ou 84xxxxxxx">
                        <span class="error_form" id="cPrincipal_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="contactoSecundario" class="form-label">Contacto Secundário</label>
                        <input type="tel" name="contactoSecundario" pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required
                            class="form-control" placeholder="Ex: +258 84xxxxxxx ou 84xxxxxxx" id="contactoSecundario">
                        <span class="error_form" id="cSecundario_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="email" class="form-label">Email Pessoal</label>
                        <input type="email" name="email" class="form-control" id="email"
                            placeholder="pedrojorge@guiamba.com">
                        <span class="error_form" id="email_error_message"></span>
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
    <script src="../../Assets/JS/tema.js"></script>
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

        // Selects com valores fornecidos da BD
        $(document).ready(function() {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Qualificacao/getQualificacoes.php',
                method: 'GET',
                success: function(resposta) {
                    $('#qualificacao').html(resposta);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar qualificações:', status, error);
                    $('#qualificacao').html('<option>Erro ao carregar qualificações</option>');
                }
            });

            $.ajax({
                url: '../../Controller/Turmas/getTurmas.php',
                method: 'GET',
                success: function(resposta) {
                    $('#turma').html(resposta);
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao carregar turmas:', status, error);
                    $('#turma').html('<option>Erro ao carregar turmas</option>');
                }
            });
        }

        // Validação do formulário
        $("#formularioEstagio").validate({
            rules: {
                codigoFormando: {
                    required: true,
                    digits: true
                },
                qualificacao: {
                    required: true
                },
                turma: {
                    required: true
                },
                dataPedido: {
                    required: true,
                    date: true
                },
                horaPedido: {
                    required: true
                },
                empresa: {
                    required: true,
                    minlength: 2
                },
                contactoPrincipal: {
                    required: true,
                    pattern: /^(\+258)?[ -]?[8][2-7][0-9]{7}$/
                },
                contactoSecundario: {
                    required: true,
                    pattern: /^(\+258)?[ -]?[8][2-7][0-9]{7}$/
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                codigoFormando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                qualificacao: {
                    required: "Selecione uma qualificação."
                },
                turma: {
                    required: "Selecione uma turma."
                },
                dataPedido: {
                    required: "Informe a data do pedido.",
                    date: "Formato inválido."
                },
                horaPedido: {
                    required: "Informe a hora do pedido."
                },
                empresa: {
                    required: "Informe o nome da empresa.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                contactoPrincipal: {
                    required: "Informe um contacto válido.",
                    pattern: "Número inválido. Ex: +258 84xxxxxxx"
                },
                contactoSecundario: {
                    required: "Informe o contacto secundário.",
                    pattern: "Número inválido. Ex: +258 84xxxxxxx"
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

        // $(document).ready(function () {
        //     $('#formCartaDeEstagio').submit(function (e) {
        //         e.preventDefault();
        //         console.log('Dados enviados:', $(this).serialize());
        //         $.ajax({
        //             url: $(this).attr('action'), // e.g., ../../Controller/Estagio/FormularioDeCartaDeEstagio.php
        //             method: 'POST',
        //             data: $(this).serialize(),
        //             dataType: 'json',
        //             success: function (response) {
        //                 if (response.success) {
        //                     alert(response.message);
        //                     window.location.href = response.redirect; // Redireciona para GerarPdfCarta.php
        //                 } else {
        //                     alert(response.message);
        //                 }
        //             },
        //             error: function (xhr, status, error) {
        //                 console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
        //                 alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
        //             }
        //         });
        //     });
        // });
    </script>

    <script>
        $(document).ready(function() {
            $('#formCartaDeEstagio').submit(function(e) {
                e.preventDefault();
                console.log('Dados enviados:', $(this).serialize());
                $.ajax({
                    url: '../../Controller/Estagio/FormularioDeCartaDeEstagio.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            window.location.href = response.redirect;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                        alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
                    }
                });
            });
        });
    </script>
</body>

</html>