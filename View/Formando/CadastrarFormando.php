<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Cadastrar Formando</h3>
                        <p class="text-muted small">Registe os dados pessoais e documentais do novo estudante</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="/estagio/formando/salvar" method="post" id="formularioFormando">
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
                                    <label for="codigoformando" class="form-label text-muted fw-bold small">Código do Formando</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="codigoformando" class="form-control border-start-0 ps-0" id="codigoformando" placeholder="123456" required>
                                    </div>
                                    <span class="error_form text-danger small" id="codigoformando_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="nomeformando" class="form-label text-muted fw-bold small">Nome</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                                        <input type="text" name="nomeformando" class="form-control border-start-0 ps-0" id="nomeformando" placeholder="Mário" required>
                                    </div>
                                    <span class="error_form text-danger small" id="nomeformando_error_message"></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="apelidoformando" class="form-label text-muted fw-bold small">Apelido</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-signature text-muted"></i></span>
                                        <input type="text" name="apelidoformando" class="form-control border-start-0 ps-0" id="apelidoformando" placeholder="Da Silva" required>
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
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-user-plus me-1"></i> Cadastrar Formando</button>
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
        // Selects com valores fornecidos da BD
        $(document).ready(function() {
            carregarDados();
        });

        $.validator.addMethod('telefone_mz', function(value, element) {
            if (this.optional(element)) return true;
            return /^(\+258)?[ -]?[8][2-7][0-9]{7}$/.test(value);
        }, 'Número inválido. Ex: +258 84xxxxxxx ou 84xxxxxxx');

        // Validação do formulário
        $("#formularioFormando").validate({
            rules: {
                codigoformando: {
                    required: true,
                    digits: true,
                    minlength: 5
                },
                nomeformando: {
                    required: true,
                    minlength: 2
                },
                apelidoformando: {
                    required: true,
                    minlength: 2
                },
                dataNascimento: {
                    required: true,
                    date: true
                },
                naturalidade: {
                    required: true,
                    minlength: 2
                },
                tipoDeDocumento: {
                    required: true,
                    minlength: 2
                },
                numeroDeDocumento: {
                    required: true,
                    minlength: 5
                },
                localEmitido: {
                    required: true,
                    minlength: 2
                },
                nuit: {
                    required: true,
                    digits: true,
                    minlength: 9
                },
                telefone: {
                    required: true,
                    telefone_mz: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                codigoFormando: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos.",
                    minlength: "O código deve ter pelo menos 5 digitos."
                },
                nomeformando: {
                    required: "Informe o nome do formando.",
                    minlength: "O nome deve ter pelo menos 2 caracteres."
                },
                apelidoformando: {
                    required: "Informe o apelido do formando.",
                    minlength: "O apelido deve ter pelo menos 2 caracteres."
                },
                dataNascimento: {
                    required: "Informe a data de nascimento.",
                    date: "Formato inválido."
                },
                naturalidade: {
                    required: "Informe a Naturalidade.",
                    minlength: "A naturalidade deve ter pelo menos 2 caracteres."
                },
                tipoDeDocumento: {
                    required: "Informe o O Tipo do Documento.",
                    minlength: "O Tipo de Documento deve ter pelo menos 2 caracteres."
                },
                numeroDeDocumento: {
                    required: "Informe o Número do Documento.",
                    minlength: "O Número do Documento deve ter pelo menos 5 caracteres."
                },
                localEmitido: {
                    required: "Informe o Local De Emissão do Documento.",
                    minlength: "O Local De Emissão do Documento deve ter pelo menos 2 caracteres."
                },
                nuit: {
                    required: "Informe o nome da empresa.",
                    digits: "Apenas números são permitidos.",
                    minlength: "O NUIT deve ter 9 digitos exactos."
                },
                telefone: {
                    required: "Campo obrigatório.",
                    telefone_mz: "Número inválido. Ex: +258 84xxxxxxx"
                },
                email: {
                    required: "Informe o e-mail.",
                    email: "Endereço de e-mail inválido."
                }
            },
            errorClass: "is-invalid",
            validClass: "is-valid",
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });
    </script>
</body>

</html>