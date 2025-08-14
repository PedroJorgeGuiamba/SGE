<?php
session_start();
include '../../Controller/formando/Home.php';
require_once __DIR__ .'/../../middleware/auth.php';

require_once __DIR__ . '/../../Conexao/conector.php';
$conexao = new Conector();
$conn = $conexao->getConexao();

$sql = "SELECT numero, nome, apelido, codigo_formando, qualificacao, codigo_turma, data_do_pedido, hora_do_pedido, empresa, contactoPrincipal, contactoSecundario, email FROM pedido_carta";
$result = $conn->query($sql);

if (!$result) {
    die("Erro ao executar a consulta: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("Nenhum pedido encontrado.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Estágio</title>

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

        <h2 class="mb-4">Lista de Todos os Pedidos</h2>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por empresa">
        <div class="table-responsive">
            <table id="pedidosTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Numero</th>
                        <th>Nome</th>
                        <th>Apelido</th>
                        <th>Código Formando</th>
                        <th>Qualificação</th>
                        <th>Turma</th>
                        <th>Data do Pedido</th>
                        <th>Hora do Pedido</th>
                        <th>Empresa</th>
                        <th>Contacto Principal</th>
                        <th>Contacto Secundário</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="pedidosTbody">
                    <?php while ($pedido = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pedido['numero'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['apelido'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['codigo_formando'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['qualificacao'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['codigo_turma'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo date("d/m/Y", strtotime($pedido['data_do_pedido'])); ?></td>
                            <td><?php echo htmlspecialchars($pedido['hora_do_pedido'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['empresa'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['contactoPrincipal'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['contactoSecundario'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary editar-btn" data-id="<?php echo $pedido['numero']; ?>">Editar</button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="<?php echo $pedido['numero']; ?>">Remover</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <a href="formularioDeCartaDeEstagio.php" class="btn btn-primary">Novo Pedido</a>
        </div>

        <?php
        $conn->close();
        ?>

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
    <script>
        $(document).ready(function() {
            buscarPedidos('');

            // Evento para botão de editar
            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                alert('Editar pedido com ID: ' + id);
                window.location.href = 'editar_pedido.php?id=' + id;
            });

        });

        // function buscarPedidos(filtro = '') {
        //     $.ajax({
        //         url: '../../Controller/Estagio/search_pedidos.php', // Endpoint que criamos
        //         method: 'GET',
        //         data: {
        //             empresa: filtro
        //         },
        //         success: function(data) {
        //             $('#pedidosTbody');
        //         },
        //         error: function() {
        //             $('#pedidosTbody').html('<tr><td colspan="13" class="text-center">Erro ao carregar dados.</td></tr>');
        //         }
        //     });
        // }

        // buscarPedidos();

        // $('#searchInput').on('input', function() {
        //     var filtro = $(this).val().trim();
        //     buscarPedidos(filtro);
        // });
    </script>

    <script>
        $(document).ready(function() {
            
            function buscarPedidos(pesquisa) {
                $.get('../../Controller/Estagio/search_pedidos.php', {
                    empresa: pesquisa
                }, function(data) {
                    $('#pedidosTbody').empty();

                    data.forEach(pedido => {
                        $('#pedidosTbody').append(`
                    <tr>
                        <td>${pedido.numero}</td>
                        <td>${pedido.nome}</td>
                        <td>${pedido.apelido}</td>
                        <td>${pedido.codigo_formando}</td>
                        <td>${pedido.qualificacao}</td>
                        <td>${pedido.codigo_turma}</td>
                        <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                        <td>${pedido.hora_do_pedido}</td>
                        <td>${pedido.empresa}</td>
                        <td>${pedido.contactoPrincipal}</td>
                        <td>${pedido.contactoSecundario}</td>
                        <td>${pedido.email}</td>
                        <td>
                            <button class="btn btn-sm btn-primary editar-btn" data-id="${pedido.numero}">Editar</button>
                            <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.numero}">Remover</button>
                        </td>
                    </tr>
                `);
                    });
                });
            }

            buscarPedidos('');

            $('#searchInput').on('input', function() {
                buscarPedidos($(this).val().trim());
            });
        });
    </script>
</body>

</html>