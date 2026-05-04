// Portal Formando - list with pagination
$(document).ready(function() {
    let currentPage = 1;
    const rowsPerPage = 4;
    let pedidosData = [];

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function renderTable() {
        $('#pedidosTbody').empty();
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = pedidosData.slice(start, end);

        if (pageData.length === 0) {
            $('#pedidosTbody').append(`
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p class="fw-semibold mt-2">Nenhum pedido encontrado</p>
                            <small>Crie um novo pedido de estágio para começar</small>
                        </div>
                    </td>
                </tr>
            `);
        } else {
            pageData.forEach(pedido => {
                $('#pedidosTbody').append(`
                    <tr>
                        <td><input type="checkbox" class="form-check-input select-checkbox" value="${pedido.id_pedido_carta}"></td>
                        <td><span class="fw-semibold text-primary">${pedido.numero}</span></td>
                        <td>${escapeHtml(pedido.nome)}</td>
                        <td>${escapeHtml(pedido.apelido)}</td>
                        <td><code style="color:#3a4c91;">${pedido.codigo_formando}</code></td>
                        <td>${pedido.qualificacao_descricao ?? pedido.qualificacao}</td>
                        <td>${pedido.turma}</td>
                        <td>${pedido.data_do_pedido.split('-').reverse().join('/')}</td>
                        <td>${pedido.hora_do_pedido}</td>
                        <td>${escapeHtml(pedido.empresa)}</td>
                        <td>${pedido.contactoPrincipal}</td>
                        <td>${pedido.contactoSecundario}</td>
                        <td>${pedido.email}</td>
                        
                    </tr>
                `);
            });
        }

        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.ceil(pedidosData.length / rowsPerPage);
        $('#pagination').empty();

        if (totalPages <= 1) return;

        // Quantas páginas mostrar de cada vez na janela
        const windowSize = 5;
        let startPage = Math.max(1, currentPage - Math.floor(windowSize / 2));
        let endPage   = Math.min(totalPages, startPage + windowSize - 1);

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
        // $.get('/estagio/api/historico-pedidos', { termo: pesquisa }, function(data) {
        //     pedidosData = data;
        //     currentPage = 1;
        //     renderTable();
        // });

        $.ajax({
            url: '/estagio/api/historico-pedidos',
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

    buscarPedidos('');

    // Pesquisa em tempo real
    $('#searchInput').on('input', function() {
        buscarPedidos($(this).val().trim());
    });

    // Editar
    $(document).on('click', '.editar-btn', function() {
        var id = $(this).data('id');
        window.location.href = '../estagio/editarPedido.php?numero=' + id;
    });

    // Remover
    $(document).on('click', '.remover-btn', function() {
        var id = $(this).data('id');
        if (confirm('Tem certeza que deseja remover o pedido #' + id + '?')) {
            $.ajax({
                url: '../../Controller/Estagio/remover_pedido.php',
                type: 'POST',
                data: {
                    id_pedido_carta: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        buscarPedidos($('#searchInput').val().trim());
                    } else {
                        alert(response.error || 'Erro ao remover o pedido');
                    }
                },
                error: function(xhr) {
                    alert('Erro: ' + xhr.responseText);
                }
            });
        }
    });

    // Select All
    $('#selectAll').on('change', function() {
        $('.select-checkbox').prop('checked', this.checked);
    });

    $(document).on('change', '.select-checkbox', function() {
        const allChecked = $('.select-checkbox').length === $('.select-checkbox:checked').length;
        $('#selectAll').prop('checked', allChecked);
    });

});
