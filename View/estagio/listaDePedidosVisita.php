<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container-fluid px-4 mb-5" style="margin-top: 40px;">

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-primary"><i class="fas fa-list-alt me-2"></i>Lista de Todos os Pedidos de Visita</h3>
                </div>
                <div class="w-25">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 bg-light" placeholder="Pesquisar por Empresa, Nome, Email ou Apelido">
                    </div>
                </div>
            </div>

            <div class="card-body p-4 pt-0">
                <div class="mb-3 d-flex gap-2">
                    <a href="formularioDeVisita.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                    <a href="HistoricoDePedidosVisita.php" class="btn btn-success shadow-sm"><i class="fas fa-history me-1"></i> Histórico</a>
                    <button id="deleteSelected" class="btn btn-danger shadow-sm"><i class="fas fa-trash-alt me-1"></i> Deletar Selecionados</button>
                </div>
            
                <div class="table-responsive rounded-3 border">
                    <table id="pedidosTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Numero</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>Código Formando</th>
                                <th>C. do Formando</th>
                                <th>Empresa</th>
                                <th>Endereço</th>
                                <th>N. Sup. na Empresa</th>
                                <th>C. do Supervisor</th>
                                <th>Data e Hora da Visita</th>
                                <th>Data do Pedido</th>
                                <th>Id do Pedido</th>
                                <th>E. Pedido</th>
                                <th>Acções</th>
                            </tr>
                        </thead>
                        <tbody id="pedidosTbody">
                        </tbody>
                    </table>
                </div>
                <nav>
                    <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </main>

    <!-- Scripts do BootStrap -->
    <?php require_once __DIR__ . '/../../Includes/footer.php'?>

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
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_visita}"></td>
                            <td>${pedido.id_visita}</td>
                            <td>${pedido.nome}</td>
                            <td>${pedido.apelido}</td>
                            <td>${pedido.codigo_formando}</td>
                            <td>${pedido.contactoFormando}</td>
                            <td>${pedido.empresa}</td>
                            <td>${pedido.endereco}</td>
                            <td>${pedido.nomeSupervisor}</td>
                            <td>${pedido.contactoSupervisor}</td>
                            <td>${pedido.dataHoraDaVisita}</td>
                            <td>${pedido.data_do_pedido ? pedido.data_do_pedido.split('-').reverse().join('/') : 'N/A'}</td>
                            <td>${pedido.id_pedido_carta}</td>
                            <td>${pedido.status}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor'):?>
                                        <button class="btn btn-sm btn-outline-primary aprovar-btn" data-id="${pedido.id_visita}" title="Aprovar Pedido">
                                            <i class="fas fa-check-circle" style="color: green;"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger recusar-btn" data-id="${pedido.id_visita}" title="Recusar Pedido">
                                            <i class="fas fa-ban" style="color:red;"></i>
                                        </button>
                                    <?php endif?>
                                    <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.id_visita}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.id_visita}" title="Remover">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
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
                $.get('../../Controller/Estagio/search_visita.php', { termo: pesquisa }, function(data) {
                    pedidosData = data;
                    currentPage = 1;
                    renderTable();
                });
            }

            buscarPedidos('');

            $('#searchInput').on('input', function() {
                buscarPedidos($(this).val().trim());
            });

            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'editarVisita.php?numero=' + id;

                $.ajax({
                    url: '../../Controller/Estagio/aprovar_visita.php',
                    type: 'POST',
                    data: { id_visita: id },
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
            });

            // Aprovar
            $(document).on('click', '.aprovar-btn', function() {
                var id = $(this).data('id');
                if (confirm('Aprovar a visita #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/aprovar_visita.php',
                        type: 'POST',
                        data: { id_visita: id },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.success ? response.message : response.error);
                            buscarPedidos($('#searchInput').val().trim());
                        },
                        error: function() { alert('Erro ao comunicar com o servidor'); }
                    });
                }
            });

            // Recusar
            $(document).on('click', '.recusar-btn', function() {
                var id = $(this).data('id');
                if (confirm('Recusar a visita #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/recusar_visita.php',
                        type: 'POST',
                        data: { id_visita: id },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.success ? response.message : response.error);
                            buscarPedidos($('#searchInput').val().trim());
                        },
                        error: function() { alert('Erro ao comunicar com o servidor'); }
                    });
                }
            });

            // Remover
            $(document).on('click', '.remover-btn', function() {
                var id = $(this).data('id');
                if (confirm('Remover a visita #' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_visita.php',
                        type: 'POST',
                        data: { id_visita: id },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.success ? response.message : response.error);
                            buscarPedidos($('#searchInput').val().trim());
                        },
                        error: function() { alert('Erro ao comunicar com o servidor'); }
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
        });

    </script>
</body>
</html>