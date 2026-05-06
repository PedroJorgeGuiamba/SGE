$(document).ready(function() {
    let currentPage = 1;
    const rowsPerPage = 4;
    let pedidosData = [];

    function renderTable() {
        $('#pedidosTbody').empty();
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = pedidosData.slice(start, end);

        if (pedidosData.length == 0) {
            $('#pedidosTbody').append(`
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

        pageData.forEach(pedido => {
            $('#pedidosTbody').append(`
                    <tr>
                        <td><input type="checkbox" class="select-checkbox" value="${pedido.id_avaliacao}"></td>
                        <td>${pedido.id_avaliacao}</td>
                        <td>${pedido.nome}</td>
                        <td>${pedido.apelido}</td>
                        <td>${pedido.codigo_formando}</td>
                        <td>${pedido.empresa}</td>
                        <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                        <td>${pedido.turma}</td>
                        <td>${pedido.ano_turma}</td>
                        <td>
                            ${pedido.doc_path
                                ? `<a href="${pedido.doc_path}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info" title="Visualizar Carta">
                                    <i class="fas fa-file-alt"></i>
                                </a>`
                                : `<span class="text-muted" title="Nenhuma carta anexada">
                                        <i class="fas fa-file-slash" style="font-size: 1.1rem; opacity: 0.4;"></i>
                                    </span>`
                            }
                        </td>
                        <td>${pedido.resultado}</td>
                        <td>${pedido.comentario}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${pedido.id_avaliacao}" title="Editar" >
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${pedido.id_avaliacao}" title="Remover">
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

        if (totalPages <= 1) return;

        // Quantas páginas mostrar de cada vez na janela
        const windowSize = 5;
        let startPage = Math.max(1, currentPage - Math.floor(windowSize / 2));
        let endPage = Math.min(totalPages, startPage + windowSize - 1);

        // Ajusta se estiver perto do fim
        if (endPage - startPage < windowSize - 1) {
            startPage = Math.max(1, endPage - windowSize + 1);
        }

        // Botão « Primeira
        $('#pagination').append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="1" title="Primeira">«</a>
                </li>
            `);

        // Botão ‹ Anterior
        $('#pagination').append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}" title="Anterior">‹</a>
                </li>
            `);

        // Reticências no início
        if (startPage > 1) {
            $('#pagination').append(`
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                `);
        }

        // Páginas da janela
        for (let i = startPage; i <= endPage; i++) {
            $('#pagination').append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
        }

        // Reticências no fim
        if (endPage < totalPages) {
            $('#pagination').append(`
                    <li class="page-item disabled">
                        <span class="page-link">…</span>
                    </li>
                `);
        }

        // Botão › Próxima
        $('#pagination').append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}" title="Próxima">›</a>
                </li>
            `);

        // Botão » Última
        $('#pagination').append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${totalPages}" title="Última">»</a>
                </li>
            `);
        // Evento de clique unificado
        $('#pagination .page-link').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (!isNaN(page) && page >= 1 && page <= totalPages && page !== currentPage) {
                currentPage = page;
                renderTable();
            }
        });
    }

    function buscarPedidos(pesquisa = '') {
        $.ajax({
            url: '/estagio/api/historico-avalicoes',
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

    $('#searchInput').on('input', function() {
        buscarPedidos($(this).val().trim());
    });

    $(document).on('click', '.gerar-pdf-completo-btn', function() {
        var id = $(this).data('id');
        window.location.href = '/estagio/estagio/gerarPDF/' + id;
    })

    $(document).on('click', '.editar-btn', function() {
        var id = $(this).data('id');
        window.location.href = '/estagio/avaliacao-estagio/editar/' + id;
    });

    $(document).on('click', '.remover-btn', function() {
        var id = $(this).data('id');

        // Confirmação antes de remover
        if (confirm('Tem certeza que deseja remover o pedido #' + id + '?')) {
            $.ajax({
                url: '/estagio/estagio/remover',
                type: 'POST',
                data: {
                    id_pedido_carta: id
                },
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

    $(document).on('change', '.select-checkbox', function() {
        const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
        $('#selectAll').prop('checked', allChecked);
    });

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
        fetch('/estagio/estagio/gerarPDF', {
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
                const a = document.createElement('a');
                a.href = url;
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

    $('#deleteSelected').on('click', function() {
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

            $.ajax({
                url: '/estagio/estagio/remover',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    ids: selectedIds
                }),
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