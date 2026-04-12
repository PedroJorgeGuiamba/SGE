<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container-fluid px-4 mb-5" style="margin-top: 140px;">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-primary"><i class="fas fa-history me-2"></i>Histórico de Pedidos de Estágio</h3>
                    <p class="text-muted small mb-0">Consulte o arquivo de todos os pedidos já finalizados na plataforma</p>
                </div>
                <div class="w-25">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0 bg-light" placeholder="Pesquisar histórico...">
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4 pt-0">
                <div class="mb-3 d-flex gap-2">
                    <a href="formularioDeCartaDeEstagio.php" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                    <a href="listaDePedidos.php" class="btn btn-success shadow-sm"><i class="fas fa-list-ul me-1"></i> Lista Ativa</a>
                    <button type="submit" id="printSelected" class="btn btn-secondary shadow-sm"><i class="fas fa-file-archive me-1"></i> Gerar Selecionados (ZIP)</button>
                    <button id="deleteSelected" class="btn btn-danger shadow-sm"><i class="fas fa-trash-alt me-1"></i> Deletar Selecionados</button>
                </div>

                <div class="table-responsive rounded-3 border">
                    <table id="pedidosTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                <th>Número</th>
                                <th>Nome</th>
                                <th>Apelido</th>
                                <th>Cód. Formando</th>
                                <th>Qualificação</th>
                                <th>Turma</th>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>Empresa</th>
                                <th>Ct. Princ.</th>
                                <th>Ct. Sec.</th>
                                <th>Email</th>
                                <th class="text-center">Acções</th>
                            </tr>
                        </thead>
                        <tbody id="pedidosTbody">
                        </tbody>
                    </table>
                </div>
                
                <nav>
                    <ul class="pagination justify-content-center mt-4" id="pagination"></ul>
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
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_pedido_carta}"></td>
                            <td>${pedido.numero}</td>
                            <td>${pedido.nome}</td>
                            <td>${pedido.apelido}</td>
                            <td>${pedido.codigo_formando}</td>
                            <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                            <td>${pedido.turma}</td>
                            <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                            <td>${pedido.hora_do_pedido}</td>
                            <td>${pedido.empresa}</td>
                            <td>${pedido.contactoPrincipal}</td>
                            <td>${pedido.contactoSecundario}</td>
                            <td>${pedido.email}</td>
                            <td>
                                <button class="btn btn-sm btn-primary gerar-pdf-completo-btn" data-id="${pedido.id_pedido_carta}" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.numero}" title="Editar" >
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
                $.get('../../Controller/Estagio/search_historico_pedidos.php', { termo: pesquisa }, function(data) {
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