<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container-fluid px-4 mb-5" style="margin-top: 40px;">

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-primary"><i class="fas fa-history me-2"></i>Histórico de Todos os Pedidos de Visita</h3>
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
                    <a href="formularioDeCartaDeEstagio.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                    <a href="listaDePedidosVisita.php" class="btn btn-success shadow-sm"><i class="fas fa-list-ul me-1"></i> Lista Ativa</a>
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
                                <th>Contacto do Formando</th>
                                <th>Empresa</th>
                                <th>Endereço</th>
                                <th>Nome do Supervisor na Empresa</th>
                                <th>Contacto do Supervisor na Empresa</th>
                                <th>Data e Hora da Visita</th>
                                <th>Data do Pedido</th>
                                <th>Id do Pedido de Estágio</th>
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
                <div class="mt-3">
                    <a href="formularioDeVisita.php" class="btn btn-primary">Novo Pedido</a>
                    <a href="listaDePedidosVisita.php" class="btn btn-success">Lista de Pedidos</a>
                    <button id="deleteSelected" class="btn btn-danger ms-2">Deletar Selecionados</button>
                </div>
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
                            <td>
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.id_pedido_carta}" title="Editar" >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.id_pedido_carta}" title="Remover">
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
                $.get('../../Controller/Estagio/search_historico_visita.php', { termo: pesquisa }, function(data) {
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
        });

    </script>
</body>
</html>