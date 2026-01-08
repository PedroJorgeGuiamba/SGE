<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main class="container mt-4">
        <h2 class="mb-4">Lista de Todos as Respostas</h2>
        <input type="text" id="searchInput" class="form-control mb-3" placeholder="Pesquisar por resposta">
        <div class="table-responsive">
            <table id="respostasTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Numero da Resposta</th>
                        <th>Numero do Pedido</th>
                        <th>Estado da Resposta</th>
                        <th>Data da Resposta</th>
                        <th>Contacto da Empresa</th>
                        <th>Data do Ínicio do Estágio</th>
                        <th>Data do Fim</th>
                        <th>Estado do Estagio</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody id="respostasTbody">
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center mt-3" id="pagination"></ul>
            </nav>
        </div>
        <div class="mt-3">
            <a href="adicionarRespostaCarta.php" class="btn btn-primary">Nova Resposta</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="../../Assets/JS/tema.js"></script>
    <script>
        $(document).ready(function() {
            $('#respostasTable').DataTable();
            let currentPage = 1;
            const rowsPerPage = 4;
            let respostasData = [];

            function renderTable() {
                $('#respostasTbody').empty();
                const start = (currentPage - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                const pageData = respostasData.slice(start, end);
                
                pageData.forEach(resposta => {
                    const dataInicio = resposta.data_inicio_estagio ?
                    resposta.data_inicio_estagio.split('-').reverse().join('/') : 'N/A';
                    const dataFim = resposta.data_fim_estagio ?
                        resposta.data_fim_estagio.split('-').reverse().join('/') : 'N/A';
                        const dataResposta = resposta.data_resposta ?
                        resposta.data_resposta.split('-').reverse().join('/') : 'N/A';
                        const avaliarButton = resposta.status_estagio === 'Concluido' && resposta.status_resposta === 'Aceito'?
                                `<button class="btn btn-sm btn-primary avaliar-btn" data-id="${resposta.id_resposta}" title="Avaliar Estágio">
                                    <i class="fas fa-star"></i>
                                </button>` : '';
                        
                        $('#respostasTbody').append(`
                        <tr>
                            <td><input type="checkbox" class="select-checkbox" data-id="${resposta.id_resposta}"></td>
                            <td>${resposta.id_resposta || 'N/A'}</td>
                            <td>${resposta.numero_carta || 'N/A'}</td>
                            <td>${resposta.status_resposta || 'N/A'}</td>
                            <td>${dataResposta}</td>
                            <td>${resposta.contato_responsavel || 'N/A'}</td>
                            <td>${dataInicio}</td>
                            <td>${dataFim}</td>
                            <td>${resposta.status_estagio || 'N/A'}</td>
                            <td>
                                ${avaliarButton}
                                <button class="btn btn-sm btn-warning editar-btn" data-id="${resposta.id_resposta}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger remover-btn" data-id="${resposta.id_resposta}" title="Remover">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });

                renderPagination();
            }

            function renderPagination() {
                const totalPages = Math.ceil(respostasData.length / rowsPerPage);
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

            function buscarRespostas(pesquisa = '') {
                $.get('../../Controller/Estagio/search_respostas.php', { numero: pesquisa }, function(data) {
                    console.log('Dados recebidos:', data);
                    respostasData = Array.isArray(data) ? data : [];
                    currentPage = 1;
                    renderTable();
                }).fail(function(xhr, status, error) {
                    console.error('Erro na requisição:', status, error);
                    respostasData = [];
                    renderTable();
                });
            }

            buscarRespostas('');

            $('#searchInput').on('input', function() {
                buscarRespostas($(this).val().trim());
            });

            $('#selectAll').on('change', function() {
                var checked = this.checked;
                $('#respostasTbody .select-checkbox').prop('checked', checked);
            });
            $(document).on('change', '.select-checkbox', function() {
                var allChecked = $('#respostasTbody .select-checkbox').length === $('#respostasTbody .select-checkbox:checked').length;
                $('#selectAll').prop('checked', allChecked);
            });
            $('#deleteSelected').on('click', function() {
                var selectedIds = [];
                $('#respostasTbody .select-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });
                if (selectedIds.length === 0) {
                    alert('Nenhuma resposta selecionada.');
                    return;
                }
                if (confirm('Tem certeza que deseja remover as respostas selecionadas?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_resposta.php', // Nota: Pode precisar ajustar o backend para aceitar múltiplos IDs (ex: { numeros: selectedIds })
                        type: 'POST',
                        data: { numeros: selectedIds }, // Enviando como array; ajuste o PHP para lidar com isso
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                buscarRespostas($('#searchInput').val().trim());
                            } else {
                                alert(response.error || 'Erro ao remover as respostas');
                            }
                        },
                        error: function() {
                            alert('Erro ao comunicar com o servidor');
                        }
                    });
                }
            });

            $(document).on('click', '.editar-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'editarResposta.php?id_resposta=' + id;
            });

            $(document).on('click', '.avaliar-btn', function() {
                var id = $(this).data('id');
                window.location.href = 'avaliarEstagio.php?numero=' + id;
            });

            $(document).on('click', '.remover-btn', function() {
                var id = $(this).data('id');
                
                // Confirmação antes de remover
                if (confirm('Tem certeza que deseja remover a resposta nº ' + id + '?')) {
                    $.ajax({
                        url: '../../Controller/Estagio/remover_resposta.php',
                        type: 'POST',
                        data: { numero: id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                // Atualiza a tabela após remoção
                                buscarRespostas($('#searchInput').val().trim());
                            } else {
                                alert(response.error || 'Erro ao remover a resposta');
                            }
                        },
                        error: function() {
                            alert('Erro ao comunicar com o servidor');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>