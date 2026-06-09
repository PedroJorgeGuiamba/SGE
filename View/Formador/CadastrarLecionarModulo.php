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
                    <h3 class="fw-bold text-primary"><i class="fas fa-graduation-cap me-2"></i>Lecionar Módulo da Turma</h3>
                    <p class="text-muted small">Preencha os campos abaixo para associar o formador e módulo no sistema</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/formador/salvar-lecionar" method="post">
                        <?php echo CSRFProtection::getTokenField(); ?>
                        <?php if (isset($_GET['erros'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Identificação do Formador e Módulo</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="modulo" class="form-label text-muted fw-bold small">Módulo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <select class="form-select border-start-0 ps-0" name="modulo" id="modulo" required>
                                        <option value="" selected disabled>A carregar Módulos...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="formador" class="form-label text-muted fw-bold small">Formador</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <select class="form-select border-start-0 ps-0" name="formador" id="formador" required>
                                        <option value="" selected disabled>A carregar tipo...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="modulo_turma" class="form-label text-muted fw-bold small">Turma - Módulo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <select class="form-select border-start-0 ps-0" name="modulo_turma" id="modulo_turma" required>
                                        <option value="" selected disabled>A carregar Módulos...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="data_inicio" class="form-label text-muted fw-bold small">Data de Inicio</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <input type="date" class="form-control border-start-0 ps-0" name="data_inicio" id="data_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="data_fim" class="form-label text-muted fw-bold small">Data De Termino</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <input type="date" class="form-control border-start-0 ps-0" name="data_fim" id="data_fim" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="carga_horaria" class="form-label text-muted fw-bold small">Carga Horária Por Semana</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                    <input type="number" class="form-control border-start-0 ps-0" name="carga_horaria" id="carga_horaria" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar Lecionado</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/CadastrarLecionarModulo.js"></script>
</body>

</html>

