<?php
session_start();
include '../../Controller/Admin/Home.php';
require_once __DIR__ .'/../../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respostas Das Cartas de Estágio</title>

    <!-- BootStrap Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="../../Style/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        button{
            padding-bottom: 110px;
        }
    </style>
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
            </div>
        </nav>

        <!-- Nav Secundária -->
        <nav>
            <ul class="nav justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page"
                        href="../../View/Formando/portalDeEstudante.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../estagio/formularioDeCartaDeEstagio.php">Fazer Pedido de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="listaDePedidos.php">Pedidos de Estágio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Verificar tempo de termino de Estágio</a>
                </li>
            </ul>
        </nav>
    </header>


    <main class="container mt-4">

        <h2 class="mb-4">Lista de Todos as Respostas</h2>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por resposta">
        <div class="table-responsive">
            <table id="respostasTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Numero da Resposta</th>
                        <th>Numero do Pedido</th>
                        <th>Status da Resposta</th>
                        <th>Data da Resposta</th>
                        <th>Contacto do Responsavel</th>
                        <th>Data do Ínicio do Estágio</th>
                        <th>Data do Fim</th>
                    </tr>
                </thead>
                <tbody id="respostasTbody">
                </tbody>
            </table>
            <nav>
            <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
        </nav>

        </div>
        <div class="mt-3">
            <a href="adicionarRespostaCarta.php" class="btn btn-primary">Nova Resposta</a>
        </div>
    </main>

    <footer>
        <div class="container-footer">
            <p>© 2019 TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>

    <!-- Scripts do BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            const rowsPerPage = 4;
            let respostasData = [];

            function renderTable() {
                $('#respostasTbody').empty();
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = respostasData.slice(start, end);

                pageData.forEach(resposta => {
                    // Adicione verificações para evitar erros
                    const dataInicio = resposta.data_inicio_estagio ? 
                        resposta.data_inicio_estagio.split('-').reverse().join('/') : 'N/A';
                    const dataFim = resposta.data_fim_estagio ? 
                        resposta.data_fim_estagio.split('-').reverse().join('/') : 'N/A';
                    const dataResposta = resposta.data_resposta ? 
                        resposta.data_resposta.split('-').reverse().join('/') : 'N/A';

                    $('#respostasTbody').append(`
                        <tr>
                            <td>${resposta.id_resposta || 'N/A'}</td>
                            <td>${resposta.numero_carta || 'N/A'}</td>
                            <td>${resposta.status_resposta || 'N/A'}</td>
                            <td>${dataResposta}</td>
                            <td>${resposta.contacto_responsavel || 'N/A'}</td>
                            <td>${dataInicio}</td>
                            <td>${dataFim}</td>
                            <td>
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${resposta.id_resposta}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${resposta.id_resposta}" title="Remover">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(respostasData.length / rowsPerPage);
                $('#pagination').empty();

                for (let i = 1; i <= totalPages; i++) {
                    $('#pagination').append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#">${i}</a>
                        </li>
                    `);
                }

                $('.page-link').click(function(e) {
                    e.preventDefault();
                    currentPage = parseInt($(this).text());
                    renderTable();
                });
            }

            function buscarRespostas(pesquisa = '') {
                $.get('../../Controller/Estagio/search_respostas.php', { numero: pesquisa }, function(data) {
                    console.log('Dados recebidos:', data); // Para debug
                    respostasData = Array.isArray(data) ? data : [];
                    currentPage = 1;
                    renderTable();
                }).fail(function(xhr, status, error) {
                    console.error('Erro na requisição:', status, error);
                    respostasData = [];
                    renderTable();
                });
            }

            // Inicializar
            buscarRespostas('');

            $('#searchInput').on('input', function() {
                buscarRespostas($(this).val().trim());
            });

            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'editarResposta.php?numero=' + id;
            });
        });
    </script>
</body>
</html>