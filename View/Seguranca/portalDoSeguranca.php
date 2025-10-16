<?php
session_start();
include '../../Controller/Seguranca/Home.php';
require_once __DIR__ .'/../../middleware/auth.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal do Seguranca</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
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
    </header>



    <main>
        <div class="formulario">
            <form action="../../Controller/Seguranca/RegistroDeEntrada.php" method="post" id="formularioEstagio">
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
                        <label for="dataPedido" class="form-label">Data do Pedido</label>
                        <input type="date" name="dataPedido" class="form-control" id="dataPedido">
                        <span class="error_form" id="dataPedido_error_message"></span>
                    </div>
                
                    <div class="form-group col-md-4">
                        <label for="horaPedido" class="form-label">Horário do Pedido</label>
                        <input type="time" name="horaPedido" class="form-control" id="horaPedido">
                        <span class="error_form" id="horaPedido_error_message"></span>
                    </div>
                
                    <div class="form-group col-md-4">
                        <label for="empresa" class="form-label">Empresa Onde Pretende Submeter</label>
                        <input type="text" name="empresa" class="form-control" id="empresa">
                        <span class="error_form" id="empresa_error_message"></span>
                    </div>
                </div>
                
                <div class="row">
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
        <!-- Rodapé -->
    </main>
    
    <footer>
        <div class="container-footer">
            <p>© 2019 TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>
    <!-- Scripts do BootStrap -->
    <script src="/pedro/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/ js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="/js/scripts.js"></script>
</body>

</html>