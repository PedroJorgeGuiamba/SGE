<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

<main class="container-fluid px-4 mb-5" style="margin-top: 20px;">

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold text-primary"><i class="fas fa-list-alt me-2"></i>Lista de Todos os Pedidos de Credencial</h3>
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
                <a href="/estagio/credencial/criar" class="btn btn-primary shadow-sm"><i class="fas fa-plus-circle me-1"></i> Novo Pedido</a>
                <a href="/estagio/credencial/historico" class="btn btn-success shadow-sm"><i class="fas fa-history me-1"></i> Histórico</a>
                <button type="submit" id="printSelected" class="btn btn-secondary shadow-sm"><i class="fas fa-file-archive me-1"></i> Gerar Selecionados (ZIP)</button>
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
                            <th>Email</th>
                            <th>Empresa</th>
                            <th>Data do Pedido</th>
                            <th>Id do Pedido de Estágio</th>
                            <th>Carta Resposta</th>
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
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>

<script src="/estagio/Assets/JS/listaDePedidosCredencial.js"></script>
</body>

</html>