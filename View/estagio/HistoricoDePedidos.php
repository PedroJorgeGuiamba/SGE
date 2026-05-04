<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

<main class="container-fluid px-4 mb-5">
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
                <a href="/estagio/estagio/criar" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                <a href="/estagio/estagio/listar" class="btn btn-success shadow-sm"><i class="fas fa-list-ul me-1"></i> Lista Ativa</a>
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
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>

<script src="/estagio/Assets/JS/HistoricoDePedidos.js"></script>
</body>

</html>