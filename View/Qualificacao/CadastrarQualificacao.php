<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <!-- Utilizando margin e padding elevados no topo para compensar o header Duplo -->
    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Cadastrar Qualificação</h3>
                        <p class="text-muted small">Preencha os campos abaixo para adicionar uma nova qualificação ao sistema</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="/estagio/qualificacao/salvar" method="post">
                            <?php echo CSRFProtection::getTokenField(); ?>
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Código do Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="qualificacao" class="form-control border-start-0 ps-0" id="qualificacao" placeholder="Ex: 123456" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="descricao" class="form-label text-muted fw-bold small">Descrição do Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-book text-muted"></i></span>
                                        <input type="text" name="descricao" class="form-control border-start-0 ps-0" id="descricao" placeholder="Breve descrição." required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label for="nivel" class="form-label text-muted fw-bold small">Nível do Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-align-left text-muted"></i></span>
                                        <input type="text" name="nivel" class="form-control border-start-0 ps-0" id="nivel" placeholder="CV#" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar Qualificação</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php'?>
</body>

</html>