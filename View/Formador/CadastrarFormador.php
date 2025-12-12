<?php
session_start();
include '../../Controller/Admin/Home.php';
include '../../Controller/Cursos/CadastrarCurso.php';
require_once __DIR__ .'/../../middleware/auth.php';
require_once __DIR__ . '/../../Helpers/SecurityHeaders.php';

SecurityHeaders::setFull();
?>

<!DOCTYPE html>
<html lang="pt-pt" data-bs-theme="<?php echo $_SESSION['theme'] ?? 'light'; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Formadores</title>

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
                    <a class="nav-link active" aria-current="page" href="../Admin/portalDoAdmin.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Módulos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Horário</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Situação de Pagamento</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="situacaoDeEstagio.html">Situação de Estagio</a>
                </li>
            </ul>
        </nav>
    </header>


    <main>
        <div class="formulario">
            <form action="../../Controller/Formador/CadastrarFormador.php" method="post">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="codigoCurso" class="form-label">Código do Curso</label>
                        <input type="number" name="codigoCurso" class="form-control" id="codigoCurso"
                            placeholder="123456">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="nomeCurso" class="form-label">Nome</label>
                        <input type="text" name="nomeCurso" class="form-control" id="nomeCurso">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="descricaoCurso" class="form-label">Descricao do curso</label>
                        <input type="text" name="descricaoCurso" class="form-control" id="descricaoCurso">
                    </div>


                    <div class="form-group col-md-4">
                        <label for="siglaCurso" class="form-label">Sigla do Curso</label>
                        <input type="text" name="siglaCurso" class="form-control" id="siglaCurso">
                    </div>


                    <div class="form-group col-md-4">
                        <label for="qualificacao" class="form-label">Codigo da Qualificação</label>
                        <select class="form-select" name="qualificacao" id="qualificacao" aria-label="Default select example" required>
                            <option value="" selected disabled>Open this select menu</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success form-control">Submit</button>
                        </div>
                    </div>
                </div>

            </form>
    </main>


    <!-- Scripts do BootStrap -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script>
        $(document).ready(function () {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Qualificacao/getQualificacoes.php',
                method: 'GET',
                success: function (resposta) {
                    $('#qualificacao').html(resposta);
                },
                error: function () {
                    $('#qualificacao').html('<option>Erro ao carregar</option>');
                }
            });
        }
    </script>
</body>

</html>