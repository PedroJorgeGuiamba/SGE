<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

<style>
    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .filter-btn {
        min-width: 100px;
        transition: all 0.2s ease;
    }
    .filter-btn.active {
        transform: scale(1.02);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .filter-btn.pendente.active {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
    .filter-btn.aprovado.active {
        background-color: #198754;
        border-color: #198754;
        color: #fff;
    }
    .filter-btn.recusado.active {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    .filter-btn.todos.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }
    .status-badge.pendente {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
    }
    .status-badge.aprovado {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #198754;
    }
    .status-badge.recusado {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #dc3545;
    }
</style>

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
                    <a href="/estagio/visita/criar" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                    <button id="deleteSelected" class="btn btn-danger shadow-sm"><i class="fas fa-trash-alt me-1"></i> Deletar Selecionados</button>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold small mb-2">Filtrar por Status</label>
                    <div class="filter-buttons">
                        <button class="btn btn-outline-primary filter-btn todos active" data-status="todos">
                            <i class="fas fa-list me-1"></i> Todos <span id="countTodos" class="ms-1 badge bg-primary rounded-pill">0</span>
                        </button>
                        <button class="btn btn-outline-warning filter-btn pendente" data-status="Pendente">
                            <i class="fas fa-clock me-1"></i> Pendentes <span id="countPendente" class="ms-1 badge bg-warning rounded-pill">0</span>
                        </button>
                        <button class="btn btn-outline-success filter-btn aprovado" data-status="Aprovado">
                            <i class="fas fa-check-circle me-1"></i> Aprovados <span id="countAprovado" class="ms-1 badge bg-success rounded-pill">0</span>
                        </button>
                        <button class="btn btn-outline-danger filter-btn recusado" data-status="Recusado">
                            <i class="fas fa-times-circle me-1"></i> Recusados <span id="countRecusado" class="ms-1 badge bg-danger rounded-pill">0</span>
                        </button>
                    </div>
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
            const rowsPerPage = 10;
            let pedidosData = [];
            let currentStatus = 'todos';
            let searchTerm = '';

            // Configuração de cores por status
            const statusConfig = {
                'Pendente': { class: 'pendente', icon: 'fa-clock' },
                'Aprovado': { class: 'aprovado', icon: 'fa-check-circle' },
                'Recusado': { class: 'recusado', icon: 'fa-times-circle' }
            };

            function renderTable() {
                const tbody = $('#pedidosTbody');
                tbody.empty();
                
                // Filtrar por status
                let filteredData = pedidosData;
                if (currentStatus !== 'todos') {
                    filteredData = pedidosData.filter(pedido => pedido.status === currentStatus);
                }
                
                // Filtrar por busca
                if (searchTerm) {
                    filteredData = filteredData.filter(pedido => 
                        (pedido.nome && pedido.nome.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (pedido.apelido && pedido.apelido.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (pedido.empresa && pedido.empresa.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (pedido.email && pedido.email.toLowerCase().includes(searchTerm.toLowerCase())) ||
                        (pedido.codigo_formando && pedido.codigo_formando.toString().includes(searchTerm))
                    );
                }
                
                // Verificar se há dados
                if (filteredData.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="15" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p class="mb-0">Nenhum pedido encontrado</p>
                                    <small class="text-muted">Tente ajustar os filtros ou criar um novo pedido</small>
                                </div>
                            </td>
                        </tr>
                    `);
                    renderPagination(0);
                    return;
                }
                
                // Paginação
                const totalPages = Math.ceil(filteredData.length / rowsPerPage);
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = filteredData.slice(start, end);
                
                // Renderizar linhas
                pageData.forEach(pedido => {
                    const statusClass = statusConfig[pedido.status]?.class || 'secondary';
                    const statusIcon = statusConfig[pedido.status]?.icon || 'fa-tag';
                    const isAdmin = <?= json_encode($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'supervisor') ?>;
                    const isPendente = pedido.status === 'Pendente';
                    
                    tbody.append(`
                        <tr class="status-row-${statusClass}">
                            <td><input type="checkbox" class="select-checkbox" value="${pedido.id_visita}" data-id="${pedido.id_visita}"></td>
                            <td><span class="fw-semibold">${pedido.id_visita || 'N/A'}</span></td>
                            <td>${escapeHtml(pedido.nome || '-')}</td>
                            <td>${escapeHtml(pedido.apelido || '-')}</td>
                            <td>${pedido.codigo_formando || '-'}</td>
                            <td>${pedido.contactoFormando || '-'}</td>
                            <td>${escapeHtml(pedido.empresa || '-')}</td>
                            <td>${escapeHtml(pedido.endereco || '-')}</td>
                            <td>${escapeHtml(pedido.nomeSupervisor || '-')}</td>
                            <td>${pedido.contactoSupervisor || '-'}</td>
                            <td>${formatDateTime(pedido.dataHoraDaVisita)}</td>
                            <td>${formatDate(pedido.data_do_pedido)}</td>
                            <td>${pedido.id_pedido_carta || '-'}</td>
                            <td>
                                <span class="status-badge ${statusClass}">
                                    <i class="fas ${statusIcon} me-1"></i> ${pedido.status || 'Desconhecido'}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    ${isAdmin && isPendente ? `
                                        <button class="btn btn-sm btn-outline-success aprovar-btn" data-id="${pedido.id_visita}" title="Aprovar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger recusar-btn" data-id="${pedido.id_visita}" title="Recusar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    ` : ''}
                                    <button class="btn btn-sm btn-outline-warning editar-btn" data-id="${pedido.id_visita}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger remover-btn" data-id="${pedido.id_visita}" title="Remover">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
                });
                
                renderPagination(filteredData.length);
                updateStatusCounters();
            }
            
            function updateStatusCounters() {
                const total = pedidosData.length;
                const pendentes = pedidosData.filter(p => p.status === 'Pendente').length;
                const aprovados = pedidosData.filter(p => p.status === 'Aprovado').length;
                const recusados = pedidosData.filter(p => p.status === 'Recusado').length;
                
                $('#countTodos').text(total);
                $('#countPendente').text(pendentes);
                $('#countAprovado').text(aprovados);
                $('#countRecusado').text(recusados);
            }

            function renderPagination(totalItems) {
                const totalPages = Math.ceil(totalItems / rowsPerPage);
                const pagination = $('#pagination');
                pagination.empty();
                
                if (totalPages <= 1) return;
                
                // Botão Anterior
                if (currentPage > 1) {
                    pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${currentPage - 1}">« Anterior</a>
                        </li>
                    `);
                }
                
                // Números das páginas
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, currentPage + 2);
                
                for (let i = startPage; i <= endPage; i++) {
                    pagination.append(`
                        <li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `);
                }
                
                // Botão Próximo
                if (currentPage < totalPages) {
                    pagination.append(`
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${currentPage + 1}">Próximo »</a>
                        </li>
                    `);
                }
                
                $('.page-link').click(function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    if (page) {
                        currentPage = parseInt(page);
                        renderTable();
                    }
                });
            }

            function buscarPedidos(pesquisa = '') {
                $.ajax({
                    url: '/estagio/api/visitas',
                    method: 'GET',
                    data: { termo: pesquisa },
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            $('#pedidosTbody').html(`
                                <tr><td colspan="15" class="text-center text-danger py-4">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>
                                    ${data.error}
                                </td></tr>
                            `);
                            return;
                        }
                        pedidosData = Array.isArray(data) ? data : [];
                        currentPage = 1;
                        renderTable();
                    },
                    error: function(xhr) {
                        console.error('Erro:', xhr.responseText);
                        $('#pedidosTbody').html(`
                            <tr><td colspan="15" class="text-center text-danger py-4">
                                <i class="fas fa-database fa-2x mb-2 d-block"></i>
                                Erro ao carregar dados do servidor
                            </td></tr>
                        `);
                    }
                });
            }

            function formatDate(dateString) {
                if (!dateString) return 'N/A';
                if (dateString.includes('-')) {
                    return dateString.split('-').reverse().join('/');
                }
                return dateString;
            }
            
            function formatDateTime(dateTimeString) {
                if (!dateTimeString) return 'N/A';
                return dateTimeString.replace(' ', '<br><small class="text-muted">');
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // Eventos de Filtro
            $('.filter-btn').click(function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentStatus = $(this).data('status');
                currentPage = 1;
                renderTable();
            });

            // Busca em tempo real
            let searchTimeout;
            $('#searchInput').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchTerm = $(this).val().trim();
                    currentPage = 1;
                    renderTable();
                }, 300);
            });

            // Aprovar
            $(document).on('click', '.aprovar-btn', function() {
                const id = $(this).data('id');
                if (confirm(`Confirmar aprovação da visita #${id}?`)) {
                    $.ajax({
                        url: '/estagio/visita/aprovar',
                        type: 'POST',
                        data: { id_visita: id },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.success ? response.message : response.error);
                            buscarPedidos($('#searchInput').val().trim());
                        },
                        error: () => alert('Erro ao comunicar com o servidor')
                    });
                }
            });

            // Recusar
            $(document).on('click', '.recusar-btn', function() {
                const id = $(this).data('id');
                if (confirm(`Confirmar recusa da visita #${id}?`)) {
                    $.ajax({
                        url: '/estagio/visita/recusar',
                        type: 'POST',
                        data: { id_visita: id },
                        dataType: 'json',
                        success: function(response) {
                            alert(response.success ? response.message : response.error);
                            buscarPedidos($('#searchInput').val().trim());
                        },
                        error: () => alert('Erro ao comunicar com o servidor')
                    });
                }
            });
            
                        // Editar
            $(document).on('click', '.editar-btn', function() {
                const id = $(this).data('id');
                window.location.href = '/estagio/visita/editar/' + id;
            });

            // Remover
            $(document).on('click', '.remover-btn', function() {
                const id = $(this).data('id');
                if (confirm(`Tem certeza que deseja remover a visita #${id}? Esta ação não pode ser desfeita.`)) {
                    $.ajax({
                        url: '/estagio/visita/remover',
                        type: 'POST',
                        data: { id_visita: id },  // ← Campo correto para ID único
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                buscarPedidos($('#searchInput').val().trim());
                            } else {
                                alert('Erro: ' + response.error);
                            }
                        },
                        error: function(xhr) {
                            console.error('Erro:', xhr.responseText);
                            alert('Erro ao comunicar com o servidor');
                        }
                    });
                }
            });

            // Selecionar todos
            $('#selectAll').on('change', function() {
                $('.select-checkbox').prop('checked', this.checked);
            });

            $(document).on('change', '.select-checkbox', function() {
                const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });
            
            $('#deleteSelected').on('click', function() {
                // Coletar IDs como números
                const selectedIds = [];
                $('.select-checkbox:checked').each(function() {
                    const id = parseInt($(this).val());
                    if (!isNaN(id) && id > 0) {
                        selectedIds.push(id);
                    }
                });
                
                console.log('IDs selecionados (números):', selectedIds);
                
                if (selectedIds.length === 0) {
                    alert('Selecione pelo menos um pedido para remover.');
                    return;
                }
                
                if (confirm(`Tem certeza que deseja remover ${selectedIds.length} pedido(s)?`)) {
                    const $btn = $(this);
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Processando...');
                    
                    // SOLUÇÃO: Enviar como JSON em vez de form-urlencoded
                    $.ajax({
                        url: '/estagio/visita/remover',
                        type: 'POST',
                        contentType: 'application/json',  // ← Importante!
                        data: JSON.stringify({ ids: selectedIds }),  // ← Enviar como JSON string
                        dataType: 'json',
                        success: function(response) {
                            console.log('Resposta:', response);
                            if (response.success) {
                                alert(response.message);
                                buscarPedidos($('#searchInput').val().trim());
                            } else {
                                alert('Erro: ' + response.error);
                            }
                        },
                        error: function(xhr) {
                            console.error('Erro:', xhr.responseText);
                            alert('Erro ao comunicar com o servidor');
                        },
                        complete: function() {
                            $btn.prop('disabled', false).html('<i class="fas fa-trash-alt me-1"></i> Deletar Selecionados');
                        }
                    });
                }
            });
            buscarPedidos('');
        });
    </script>
</body>
</html>