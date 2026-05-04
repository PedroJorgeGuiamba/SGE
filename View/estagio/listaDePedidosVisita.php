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
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

<main class="container-fluid px-4 mb-5" style="margin-top: 20px;">

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
<?php require_once __DIR__ . '/../../Includes/footer.php' ?>

<script src="/estagio/Assets/JS/listaDePedidosVisita.data.php"></script>
<script src="/estagio/Assets/JS/listaDePedidosVisita.js"></script>
</body>

</html>