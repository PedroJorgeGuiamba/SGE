<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

<main class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary"><i class="fas fa-user-shield me-2"></i>Cadastrar Formador</h3>
                    <p class="text-muted small">Associe um utilizador a um cargo de supervisão e sua área de actuação</p>
                </div>
                <div class="card-body p-5">
                    <form action="/estagio/formador/salvar" method="post" id="formularioFormador">
                        <?= CSRFProtection::getTokenField() ?>
                        <?php if (isset($_GET['erros'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- SECÇÃO 1: DADOS BASE -->
                        <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Informação Base</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="codigoformador" class="form-label text-muted fw-bold small">Código do Formador</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                    <input type="number" name="codigoformador" class="form-control border-start-0 ps-0" id="codigoformador" placeholder="123456" required>
                                </div>
                                <span class="error_form text-danger small" id="codigoformador_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="nomeformador" class="form-label text-muted fw-bold small">Nome</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" name="nomeformador" class="form-control border-start-0 ps-0" id="nomeformador" placeholder="Mário" required>
                                </div>
                                <span class="error_form text-danger small" id="nomeformador_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="apelidoformador" class="form-label text-muted fw-bold small">Apelido</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-signature text-muted"></i></span>
                                    <input type="text" name="apelidoformador" class="form-control border-start-0 ps-0" id="apelidoformador" placeholder="Da Silva" required>
                                </div>
                                <span class="error_form text-danger small" id="apelidoformando_error_message"></span>
                            </div>
                        </div>

                        <!-- SECÇÃO 2: NASCIMENTO E ORIGEM -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="dataNascimento" class="form-label text-muted fw-bold small">Data de Nascimento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-day text-muted"></i></span>
                                    <input type="date" name="dataNascimento" class="form-control border-start-0 ps-0" id="dataNascimento" required>
                                </div>
                                <span class="error_form text-danger small" id="dataNascimento_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="naturalidade" class="form-label text-muted fw-bold small">Naturalidade</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" name="naturalidade" class="form-control border-start-0 ps-0" id="naturalidade" placeholder="Ex: Maputo" required>
                                </div>
                                <span class="error_form text-danger small" id="naturalidade_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="tipoDeDocumento" class="form-label text-muted fw-bold small">Tipo de Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-id-card-clip text-muted"></i></span>
                                    <input type="text" name="tipoDeDocumento" class="form-control border-start-0 ps-0" id="tipoDeDocumento" placeholder="Ex: BI, Passaporte" required>
                                </div>
                                <span class="error_form text-danger small" id="tipoDeDocumento_error_message"></span>
                            </div>
                        </div>

                        <!-- SECÇÃO 3: DOCUMENTAÇÃO -->
                        <h5 class="text-secondary fw-semibold mt-4 mb-3 border-bottom pb-2">Documentação & Contactos</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="numeroDeDocumento" class="form-label text-muted fw-bold small">Número de Documento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="text" name="numeroDeDocumento" class="form-control border-start-0 ps-0" id="numeroDeDocumento" required>
                                </div>
                                <span class="error_form text-danger small" id="numeroDeDocumento_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="localEmitido" class="form-label text-muted fw-bold small">Local Emitido</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-building text-muted"></i></span>
                                    <input type="text" name="localEmitido" class="form-control border-start-0 ps-0" id="localEmitido" required>
                                </div>
                                <span class="error_form text-danger small" id="localEmitido_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="dataEmissao" class="form-label text-muted fw-bold small">Data de Emissão</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-check text-muted"></i></span>
                                    <input type="date" name="dataEmissao" class="form-control border-start-0 ps-0" id="dataEmissao" required>
                                </div>
                                <span class="error_form text-danger small" id="dataEmissao_error_message"></span>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="nuit" class="form-label text-muted fw-bold small">NUIT</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-file-invoice text-muted"></i></span>
                                    <input type="number" name="nuit" class="form-control border-start-0 ps-0" id="nuit" placeholder="Ex: 999999999" required>
                                </div>
                                <span class="error_form text-danger small" id="nuit_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="telefone" class="form-label text-muted fw-bold small">Telefone</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="number" name="telefone" class="form-control border-start-0 ps-0" id="telefone" placeholder="84..." required>
                                </div>
                                <span class="error_form text-danger small" id="telefone_error_message"></span>
                            </div>
                            <div class="col-md-4">
                                <label for="email" class="form-label text-muted fw-bold small">Correio Eletrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="email@dominio.com" required>
                                </div>
                                <span class="error_form text-danger small" id="email_error_message"></span>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-user-plus me-1"></i> Cadastrar Formador</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../Includes/footer.php' ?>

<script src="/estagio/Assets/JS/CadastrarFormador.js"></script>
</body>

</html>