<?php
include '../../Controller/Cursos/CadastrarCurso.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main style="padding-top: 140px; padding-bottom: 60px; background: linear-gradient(135deg, #f0f4ff 0%, #f8f9fa 100%); min-height: 100vh;">
        <div class="container">

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="../../View/Admin/portalDoAdmin.php" class="text-decoration-none text-primary">
                            <i class="fas fa-home me-1"></i> Painel
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-muted">Cadastrar Formador</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">

                    <!-- Card principal -->
                    <div class="card border-0 rounded-4 overflow-hidden" style="box-shadow: 0 20px 60px rgba(58, 76, 145, 0.12);">

                        <!-- Cabeçalho do card -->
                        <div class="card-header border-0 py-4 px-5 text-center text-white"
                             style="background: linear-gradient(135deg, #3a4c91 0%, #3c9bff 100%);">
                            <div class="mb-2">
                                <i class="fas fa-chalkboard-teacher fa-2x opacity-90"></i>
                            </div>
                            <h4 class="fw-bold mb-1">Cadastrar Formador</h4>
                            <p class="mb-0 small opacity-75">Registe os dados pessoais e académicos do novo formador</p>
                        </div>

                        <div class="card-body p-5">

                            <!-- Alerta de erro -->
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    <span><?php echo htmlspecialchars($_GET['erros']); ?></span>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fechar"></button>
                                </div>
                            <?php endif; ?>

                            <form action="../../Controller/Formador/CadastrarFormador.php" method="post" id="formCadastrarFormador">

                                <!-- ── SECÇÃO 1: DADOS PESSOAIS ── -->
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <div class="rounded-3 p-2 d-flex" style="background: rgba(58, 76, 145, 0.1);">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Informação Pessoal</h6>
                                        <small class="text-muted">Identificação do formador</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-5">
                                    <!-- Código do Formador -->
                                    <div class="col-md-4">
                                        <label for="codigoFormador" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-barcode me-1"></i> Código do Formador
                                        </label>
                                        <input type="number"
                                               name="codigoFormador"
                                               class="form-control rounded-3"
                                               id="codigoFormador"
                                               placeholder="Ex: 123456"
                                               style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                               required>
                                    </div>

                                    <!-- Nome -->
                                    <div class="col-md-4">
                                        <label for="nomeFormador" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-user-tie me-1"></i> Nome
                                        </label>
                                        <input type="text"
                                               name="nomeFormador"
                                               class="form-control rounded-3"
                                               id="nomeFormador"
                                               placeholder="Ex: João"
                                               style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                               required>
                                    </div>

                                    <!-- Apelido -->
                                    <div class="col-md-4">
                                        <label for="apelidoFormador" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-signature me-1"></i> Apelido
                                        </label>
                                        <input type="text"
                                               name="apelidoFormador"
                                               class="form-control rounded-3"
                                               id="apelidoFormador"
                                               placeholder="Ex: Da Silva"
                                               style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                               required>
                                    </div>
                                </div>

                                <!-- Divisor -->
                                <hr class="my-4" style="border-color: #e9ecef;">

                                <!-- ── SECÇÃO 2: CONTACTOS E QUALIFICAÇÃO ── -->
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <div class="rounded-3 p-2 d-flex" style="background: rgba(60, 155, 255, 0.12);">
                                        <i class="fas fa-address-card text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Contactos e Academia</h6>
                                        <small class="text-muted">Dados de contacto e qualificação académica</small>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <!-- Telefone -->
                                    <div class="col-md-4">
                                        <label for="telefone" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-phone me-1"></i> Telefone
                                        </label>
                                        <input type="number"
                                               name="telefone"
                                               class="form-control rounded-3"
                                               id="telefone"
                                               placeholder="Ex: 84 000 0000"
                                               style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                               required>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-4">
                                        <label for="email" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-envelope me-1"></i> Correio Eletrónico
                                        </label>
                                        <input type="email"
                                               name="email"
                                               class="form-control rounded-3"
                                               id="email"
                                               placeholder="email@dominio.com"
                                               style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                               required>
                                    </div>

                                    <!-- Qualificação -->
                                    <div class="col-md-4">
                                        <label for="qualificacao" class="form-label fw-semibold small text-muted">
                                            <i class="fas fa-certificate me-1"></i> Qualificação
                                        </label>
                                        <select class="form-select rounded-3"
                                                name="qualificacao"
                                                id="qualificacao"
                                                style="padding: 12px 15px; background: #f8f9fa; border: 1.5px solid #e9ecef;"
                                                required>
                                            <option value="" selected disabled>A carregar qualificações...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ── ACÇÕES DO FORMULÁRIO ── -->
                                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                                    <a href="../../View/Admin/portalDoAdmin.php"
                                       class="btn btn-outline-secondary rounded-3 px-4 py-2">
                                        <i class="fas fa-arrow-left me-2"></i> Cancelar
                                    </a>
                                    <button type="submit"
                                            class="btn btn-primary shadow rounded-3 px-5 py-2 fw-bold"
                                            style="background: linear-gradient(135deg, #3a4c91 0%, #3c9bff 100%); border: none;">
                                        <i class="fas fa-user-check me-2"></i> Registar Formador
                                    </button>
                                </div>

                            </form>
                        </div><!-- /.card-body -->
                    </div><!-- /.card -->

                </div>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/../../Includes/footer.php'?>
    <script>
        $(document).ready(function () {
            carregarDados();

            // Focus effect nos inputs
            $('input, select').on('focus', function () {
                $(this).css('border-color', '#3a4c91').css('box-shadow', '0 0 0 0.2rem rgba(58, 76, 145, 0.15)').css('background', '#ffffff');
            }).on('blur', function () {
                $(this).css('border-color', '#e9ecef').css('box-shadow', 'none').css('background', '#f8f9fa');
            });
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Qualificacao/getQualificacoes.php',
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