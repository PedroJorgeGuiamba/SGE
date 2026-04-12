<?php
include '../../Controller/Cursos/CadastrarCurso.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main class="container mb-5" style="margin-top: 140px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-chalkboard-teacher me-2"></i>Cadastrar Formador</h3>
                        <p class="text-muted small">Registe os dados pessoais e académicos do novo formador</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../Controller/Formador/CadastrarFormador.php" method="post">
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <!-- SECÇÃO 1: DADOS BASE -->
                            <h5 class="text-secondary fw-semibold mb-3 border-bottom pb-2">Informação Pessoal</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="codigoFormador" class="form-label text-muted fw-bold small">Código do Formador</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-barcode text-muted"></i></span>
                                        <input type="number" name="codigoFormador" class="form-control border-start-0 ps-0" id="codigoFormador" placeholder="123456" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="nomeFormador" class="form-label text-muted fw-bold small">Nome</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                        <input type="text" name="nomeFormador" class="form-control border-start-0 ps-0" id="nomeFormador" placeholder="João" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="apelidoFormador" class="form-label text-muted fw-bold small">Apelido</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-signature text-muted"></i></span>
                                        <input type="text" name="apelidoFormador" class="form-control border-start-0 ps-0" id="apelidoFormador" placeholder="Da Silva" required>
                                    </div>
                                </div>
                            </div>

                            <!-- SECÇÃO 2: CONTACTOS E QUALIFICAÇÃO -->
                            <h5 class="text-secondary fw-semibold mt-4 mb-3 border-bottom pb-2">Contactos e Academia</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="telefone" class="form-label text-muted fw-bold small">Telefone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone text-muted"></i></span>
                                        <input type="number" name="telefone" class="form-control border-start-0 ps-0" id="telefone" placeholder="84..." required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="email" class="form-label text-muted fw-bold small">Correio Eletrónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0" id="email" placeholder="email@dominio.com" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
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
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-user-check me-1"></i> Registar Formador</button>
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