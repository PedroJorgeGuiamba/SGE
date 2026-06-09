<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

<!-- Utilizando margin e padding elevados no topo para compensar o header Duplo -->
<main class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Cadastrar Módulo</h3>
                    <p class="text-muted small">Preencha os campos abaixo para adicionar um novo módulo ao sistema</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/modulo/salvar" method="post">
                        <?php echo CSRFProtection::getTokenField(); ?>
                        <?php if (isset($_GET['erros'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Identificação Módulo</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="codigo_modulo" class="form-label text-muted fw-bold small">Código do Módulo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                    <input type="text" name="codigo_modulo" class="form-control border-start-0 ps-0" id="codigo_modulo" placeholder="Ex: 123456" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="descricao_modulo" class="form-label text-muted fw-bold small">Descrição do Módulo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-book text-muted"></i></span>
                                    <input type="text" name="descricao_modulo" class="form-control border-start-0 ps-0" id="descricao_modulo" placeholder="Breve descrição." required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="carga_horaria" class="form-label text-muted fw-bold small">Carga Horária</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-align-left text-muted"></i></span>
                                    <input type="number" name="carga_horaria" class="form-control border-start-0 ps-0" id="carga_horaria" placeholder="CV#" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="carga_horaria" class="form-label text-muted fw-bold small">Qualificação</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <select class="form-select border-start-0 ps-0" id="qualificacao" name="qualificacao">
                                        <option selected disabled>A carregar...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar Módulo</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/CadastrarModulo.js"></script>
</body>

</html>