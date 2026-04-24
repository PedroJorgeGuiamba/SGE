<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <!-- Utilizando margin e padding elevados no topo para compensar o header Duplo -->
    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Cadastrar Curso</h3>
                        <p class="text-muted small">Preencha os campos abaixo para adicionar um novo curso ao sistema</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="/estagio/cursos/salvar" method="post">
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="codigoCurso" class="form-label text-muted fw-bold small">Código do Curso</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="codigoCurso" class="form-control border-start-0 ps-0" id="codigoCurso" placeholder="Ex: 123456" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="nomeCurso" class="form-label text-muted fw-bold small">Nome do Curso</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-book text-muted"></i></span>
                                        <input type="text" name="nomeCurso" class="form-control border-start-0 ps-0" id="nomeCurso" placeholder="Ex: Engenharia Informática" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label for="descricaoCurso" class="form-label text-muted fw-bold small">Descrição do Curso</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-align-left text-muted"></i></span>
                                        <input type="text" name="descricaoCurso" class="form-control border-start-0 ps-0" id="descricaoCurso" placeholder="Breve descrição dos objectivos curriculares..." required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="siglaCurso" class="form-label text-muted fw-bold small">Sigla do Curso</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-tag text-muted"></i></span>
                                        <input type="text" name="siglaCurso" class="form-control border-start-0 ps-0" id="siglaCurso" placeholder="Ex: EI" required>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Código da Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" name="qualificacao" id="qualificacao" required>
                                            <option value="" selected disabled>A carregar qualificações...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar Curso</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php'?>
    <script>
        $(document).ready(function () {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '/estagio/api/qualificacao',
                method: 'GET',
                success: function (resposta) {
                    $('#qualificacao').html(resposta);
                },
                error: function () {
                    $('#qualificacao').html('<option>Erro ao carregar</option>');
                }
            });
        }
    </script>
</body>

</html>