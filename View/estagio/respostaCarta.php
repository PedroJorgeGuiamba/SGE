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
                        <th>Data do Inicio do Estágio</th>
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

    <?php require_once __DIR__ . '/../../Includes/footer.php'?>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="/estagio/Assets/JS/respostaCarta.js"></script>
</body>
</html>
