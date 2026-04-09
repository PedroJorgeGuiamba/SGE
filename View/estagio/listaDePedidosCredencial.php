<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">

        <h2 class="mb-4">Lista de Todos os Pedidos de Credencial</h2>

        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por Empresa, Nome, Email ou Apelido">
        <div class="table-responsive">
            <table id="pedidosTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Numero</th>
                        <th>Nome</th>
                        <th>Apelido</th>
                        <th>Código Formando</th>
                        <th>Contacto do Formando</th>
                        <th>Email</th>
                        <th>Empresa</th>
                        <th>Data do Pedido</th>
                        <th>Id do Pedido de Estágio</th>
                        <th>Acções</th>
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
            <a href="HistoricoDePedidosCredencial.php" class="btn btn-success">Histórico de Pedidos</a>
            <button type="submit" id="printSelected" class="btn btn-secondary ms-2">Gerar todos selecionados (ZIP)</button>
            <button id="deleteSelected" class="btn btn-danger ms-2">Deletar Selecionados</button>
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
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_credencial}"></td>
                            <td>${pedido.id_credencial}</td>
                            <td>${pedido.nome}</td>
                            <td>${pedido.apelido}</td>
                            <td>${pedido.codigo_formando}</td>
                            <td>${pedido.contactoFormando}</td>
                            <td>${pedido.email}</td>
                            <td>${pedido.empresa}</td>
                            <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                            <td>${pedido.id_pedido_carta}</td>
                            <td>
                                <button class="btn btn-sm btn-primary gerar-pdf-completo-btn" data-id="${pedido.id_pedido_carta}" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
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
                $.get('../../Controller/Estagio/search_credencial.php', { termo: pesquisa }, function(data) {
                    pedidosData = data;
                    currentPage = 1;
                    renderTable();
                });
            }

            buscarPedidos('');

            $('#searchInput').on('input', function() {
                buscarPedidos($(this).val().trim());
            });

            $(document).on('click','.gerar-pdf-completo-btn', function(){
                var id = $(this).data('id');
                window.location.href = '../../Controller/Estagio/GerarPdfCredencialCompleto.php?id_pedido_carta=' + id;
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
                    selectedIds.push($(this).val());
                });

                console.log('[ZIP] IDs selecionados:', selectedIds);

                if (selectedIds.length === 0) {
                    alert('Selecione pelo menos um pedido.');
                    return;
                }

                // Monta FormData com ids[]
                const formData = new FormData();
                selectedIds.forEach(id => formData.append('ids[]', id));

                // Feedback visual
                const $btn = $(this);
                $btn.prop('disabled', true).text('A gerar ZIP...');

                // Usa fetch com blob — evita navegação para página branca
                fetch('../../Controller/Estagio/GerarPdfCompleto.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('[ZIP] Status HTTP:', response.status);
                    console.log('[ZIP] Content-Type:', response.headers.get('Content-Type'));

                    const contentType = response.headers.get('Content-Type') || '';

                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error('Servidor devolveu erro ' + response.status + ':\n' + text);
                        });
                    }

                    // Se o servidor devolveu HTML (erro PHP), mostra no console e alerta
                    if (contentType.includes('text/html')) {
                        return response.text().then(text => {
                            console.error('[ZIP] Servidor devolveu HTML (erro PHP):\n', text);
                            throw new Error('O servidor devolveu um erro PHP. Veja o console para detalhes.');
                        });
                    }

                    return response.blob();
                })
                .then(blob => {
                    console.log('[ZIP] Blob recebido:', blob.size, 'bytes | tipo:', blob.type);

                    if (blob.size === 0) {
                        throw new Error('O ficheiro ZIP recebido está vazio (0 bytes).');
                    }

                    // Dispara download do ZIP
                    const url = URL.createObjectURL(blob);
                    const a   = document.createElement('a');
                    a.href     = url;
                    a.download = 'Pacotes_Estagio.zip';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(err => {
                    console.error('[ZIP] Erro:', err);
                    alert('Erro ao gerar ZIP:\n' + err.message + '\n\nVeja o console (F12) para mais detalhes.');
                })
                .finally(() => {
                    $btn.prop('disabled', false).text('Gerar todos selecionados (ZIP)');
                });
            });
        });

    </script>
</body>
</html>