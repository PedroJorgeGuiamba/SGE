<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>


    <main class="container mt-4">

        <h2 class="mb-4">Lista de Todos os Pedidos</h2>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por empresa">
        <div class="table-responsive">
            <table id="pedidosTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
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
                </tbody>
            </table>
            <nav>
            <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
        </nav>

        </div>
        <div class="mt-3">
            <a href="formularioDeCartaDeEstagio.php" class="btn btn-primary">Novo Pedido</a>
            <button type="submit" id="printSelected" class="btn btn-secondary ms-2">Gerar todos selecionados (ZIP)</button>
            <button id="deleteSelected" class="btn btn-danger ms-2">Deletar Selecionados</button>
        </div>
    </main>

    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
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
    <script src="../../Assets/JS/tema.js"></script>

    <script>
        $(document).ready(function() {
            let currentPage = 1;
            const rowsPerPage = 4;
            let pedidosData = [];

            function renderTable() {
                $('#pedidosTbody').empty();
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = pedidosData.slice(start, end);

                pageData.forEach(pedido => {
                    $('#pedidosTbody').append(`
                        <tr>
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_pedido_carta}"></td>
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
                                <button class="btn btn-sm btn-primary gerar-pdf-btn" data-id="${pedido.id_pedido_carta}" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-danger gerar-pdf-completo-btn" data-id="${pedido.id_pedido_carta}" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.numero}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.numero}" title="Remover">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(pedidosData.length / rowsPerPage);
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

            function buscarPedidos(pesquisa = '') {
                $.get('../../Controller/Estagio/search_pedidos.php', { empresa: pesquisa }, function(data) {
                    pedidosData = data;
                    currentPage = 1;
                    renderTable();
                });
            }

            buscarPedidos('');

            $('#searchInput').on('input', function() {
                buscarPedidos($(this).val().trim());
            });

            $(document).on('click', '.gerar-pdf-btn', function() {
                var id = $(this).data('id');
                window.location.href = '../../Controller/Estagio/GerarPdfCarta.php?numero=' + id;
            });

            $(document).on('click','.gerar-pdf-completo-btn', function(){
                var id = $(this).data('id');
                window.location.href = '../../Controller/Estagio/GerarPdfCompleto.php?id_pedido_carta=' + id;
            } )

            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'editarPedido.php?numero=' + id;
            });

            $(document).on('click', '.remover-btn', function() {
                var id = $(this).data('id');
                
                // Confirmação antes de remover
                if (confirm('Tem certeza que deseja remover o pedido #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_pedido.php',
                        type: 'POST',
                        data: { id_pedido_carta: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // Atualiza a tabela após remoção
                                buscarPedidos($('#searchInput').val().trim());
                            } else {
                                alert(response.error || 'Erro ao remover o pedido');
                            }
                        },
                        error: function() {
                            alert('Erro ao comunicar com o servidor');
                        }
                    });
                }
            });

            $('#selectAll').on('change', function() {
                $('.select-checkbox').prop('checked', this.checked);
            });

            // Atualiza o "Select All" quando desmarcar um checkbox
            $(document).on('change', '.select-checkbox', function() {
                const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });

            // Botão GERAR TODOS SELECIONADOS (ZIP)
            $('#printSelected').on('click', function(e) {
                e.preventDefault();

                const selectedIds = [];
                $('.select-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('id')); // ou .val() se puseres value=
                });

                if (selectedIds.length === 0) {
                    alert('Selecione pelo menos um pedido.');
                    return;
                }

                const $form = $('<form>', {
                    method: 'POST',
                    action: '../../Controller/Estagio/GerarPdfCompleto.php'
                });

                selectedIds.forEach(id => {
                    $form.append($('<input>', {
                        type: 'hidden',
                        name: 'ids[]',
                        value: id
                    }));
                });

                $('body').append($form);
                $form.submit(); // <--- ESTAVA A FALTAR ISTO!!
            });
        });

    </script>
</body>
</html>