<?php
session_start();
include '../../Controller/Admin/Home.php';
include '../../Controller/Formando/CadastrarFormando.php';
require_once __DIR__ .'/../../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Cadastro De Cursos</title>

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
            <form action="../../Controller/Formando/CadastrarFormando.php" method="post">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="codigoformando" class="form-label">Código do Formando</label>
                        <input type="number" name="codigoformando" class="form-control" id="codigoformando" placeholder="123456" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nomeformando" class="form-label">Nome</label>
                        <input type="text" name="nomeformando" class="form-control" id="nomeformando" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="apelidoformando" class="form-label">Apelido</label>
                        <input type="text" name="apelidoformando" class="form-control" id="apelidoformando" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" name="dataNascimento" class="form-control" id="dataNascimento" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="naturalidade" class="form-label">Naturalidade</label>
                        <input type="text" name="naturalidade" class="form-control" id="naturalidade" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipoDeDocumento" class="form-label">Tipo de Documento</label>
                        <input type="text" name="tipoDeDocumento" class="form-control" id="tipoDeDocumento" placeholder="Ex: BI, Passaporte" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="numeroDeDocumento" class="form-label">Número de Documento</label>
                        <input type="text" name="numeroDeDocumento" class="form-control" id="numeroDeDocumento" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="localEmitido" class="form-label">Local Emitido</label>
                        <input type="text" name="localEmitido" class="form-control" id="localEmitido" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dataEmissao" class="form-label">Data de Emissão</label>
                        <input type="date" name="dataEmissao" class="form-control" id="dataEmissao" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="nuit" class="form-label">NUIT</label>
                        <input type="number" name="nuit" class="form-control" id="nuit" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="number" name="telefone" class="form-control" id="telefone" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-success form-control">Cadastrar</button>
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
</body>

</html>