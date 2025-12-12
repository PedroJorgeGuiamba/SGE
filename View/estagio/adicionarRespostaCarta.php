<?php
session_start();
require_once __DIR__ . '/../../Controller/Geral/SupervisorAdmin.php';
require_once __DIR__ .'/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Resposta</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
                                    href="https://plus.google.com/share?url=https://simplesharebuttons.com">Google</a>
                            </li>
                            <!-- LinkedIn -->
                            <li class="nav-item">
                                <a class="nav-link"
                                    href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://simplesharebuttons.com">Linkedin</a>
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
                    <a class="nav-link" href="../../View/<?php echo $_SESSION['role'] === 'admin' ? 'Admin/portalDoAdmin.php' : 'Supervisor/portalDoSupervisor.php'; ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listaDePedidos.php">Pedidos de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="respostaCarta.php">Respostas às Cartas</a>
                </li>
            </ul>
        </nav>
    </header>


    <main>
        <div class="formulario">
            <form action="../../Controller/Estagio/AdicionarRespostaCarta.php" method="post" id="formularioResposta">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="numero" class="form-label">Numero da Carta</label>
                        <!-- <input type="number" name="numero" class="form-control" id="numero"> -->
                        <select class="form-select" id="numero" aria-label="Default select example" name="numero">
                            <option selected>Open this select menu</option>
                        </select>
                        <span class="error_form" id="numero_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="status" class="form-label">Status da Resposta</label>
                        <select class="form-select" id="status" aria-label="Default select example" name="status">
                            <option selected>Open this select menu</option>
                            <option value="Pendente">Pedente</option>
                            <option value="Aceito">Aprovado</option>
                            <option value="Recusado">Reprovado</option>
                        </select>
                        <span class="error_form" id="status_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataResposta" class="form-label">Data da Resposta</label>
                        <input type="date" name="dataResposta" class="form-control" id="dataResposta">
                        <span class="error_form" id="dataResposta_error_message"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="contactoResponsavel" class="form-label">Contacto do Responsável</label>
                        <input type="tel" name="contactoResponsavel" class="form-control" id="contactoResponsavel"
                            pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required
                            placeholder="Ex: +258 84xxxxxxx ou 84xxxxxxx">
                        <span class="error_form" id="cPrincipal_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataInicio" class="form-label">Data do Inicio do Estágio</label>
                        <input type="date" name="dataInicio" class="form-control" id="dataInicio">
                        <span class="error_form" id="dataInicio_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataFim" class="form-label">Data do Inicio do Estágio</label>
                        <input type="date" name="dataFim" class="form-control" id="dataFim">
                        <span class="error_form" id="dataFim_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="statusEstagio" class="form-label">Status do Estágio</label>
                        <select class="form-select" id="statusEstagio" aria-label="Default select example" name="statusEstagio">
                            <option selected>Open this select menu</option>
                            <option value="Nao Concluido">Não Concluído</option>
                            <option value="Concluido">Concluído</option>
                        </select>
                        <span class="error_form" id="statusEstagio_error_message"></span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success form-control">Register</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>


    <!-- Scripts do BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script src="/pedro/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="../../Assets/JS/tema.js"></script>
    <script>
        //Selects com valores fornecidos da bd
        $(document).ready(function () {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Estagio/getNumero.php',
                method: 'GET',
                success: function (resposta) {
                    $('#numero').html(resposta);
                },
                error: function () {
                    $('#numero').html('<option>Erro ao carregar</option>');
                }
            });

        }

        //Validation
        $("#formularioResposta").validate({
            rules: {
                numero: {
                    required: true,
                    digits: true
                },
                status: {
                    required: true
                },
                dataResposta: {
                    required: true,
                    date: true
                },
                dataInicio: {
                    required: true,
                    minlength: 2
                },
                dataFim: {
                    required: true,
                    minlength: 2
                },
                contactoResponsavel: {
                    required: true,
                    pattern: /^(\+258)?[ -]?[8][2-7][0-9]{7}$/
                },
                statusEstagio: {
                    required: true
                }
            },
            messages: {
                numero: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                status: {
                    required: "Selecione uma qualificação."
                },
                turma: {
                    required: "Selecione uma turma."
                },
                dataResposta: {
                    required: "Informe a data da resposta.",
                    date: "Formato inválido."
                },
                dataInicio: {
                    required: "Informe uma data de Inicio.",
                },
                dataFim: {
                    required: "Informe uma da data de Fim.",
                },
                contactoResponsavel: {
                    required: "Informe um contacto válido.",
                    pattern: "Número inválido. Ex: +258 84xxxxxxx"
                },
                statusEstagio: {
                    required: "Selecione um Status."
                },
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
            }
        });

    </script>
</body>

</html>