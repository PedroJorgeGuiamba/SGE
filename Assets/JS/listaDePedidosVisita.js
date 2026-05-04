$(document).ready(function() {
    let currentPage = 1;
    const rowsPerPage = 10;
    let pedidosData = [];
    let currentStatus = 'todos';
    let searchTerm = '';

    // Configuração de cores por status
    const statusConfig = {
        'Pendente': {
            class: 'pendente',
            icon: 'fa-clock'
        },
        'Aprovado': {
            class: 'aprovado',
            icon: 'fa-check-circle'
        },
        'Recusado': {
            class: 'recusado',
            icon: 'fa-times-circle'
        }
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
            const isAdmin = window.userPermissions?.isAdmin === true;
            const userRole = window.userPermissions?.role || 'guest';
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
            data: {
                termo: pesquisa
            },
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
                data: {
                    id_visita: id
                },
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
                data: {
                    id_visita: id
                },
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
                data: {
                    id_visita: id
                }, // ← Campo correto para ID único
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
                contentType: 'application/json', // ← Importante!
                data: JSON.stringify({
                    ids: selectedIds
                }), // ← Enviar como JSON string
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