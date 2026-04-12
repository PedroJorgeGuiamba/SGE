<?php
include '../../Controller/Turmas/CadastrarTurma.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main class="container mb-5" style="margin-top: 140px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-users-class me-2"></i>Cadastrar Turma</h3>
                        <p class="text-muted small">Preencha os campos abaixo para adicionar uma nova turma ao sistema</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../Controller/Turmas/CadastrarTurma.php" method="post">
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="codigoTurma" class="form-label text-muted fw-bold small">Código da Turma</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-hashtag text-muted"></i></span>
                                        <input type="text" name="codigoTurma" class="form-control border-start-0 ps-0" id="codigoTurma" placeholder="Ex: TDA10A" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="nomeTurma" class="form-label text-muted fw-bold small">Nome da Turma</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-id-card text-muted"></i></span>
                                        <input type="text" name="nomeTurma" class="form-control border-start-0 ps-0" id="nomeTurma" placeholder="Ex: TPW3" required>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Qualificação</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" name="qualificacao" id="qualificacao" required>
                                            <option value="" selected disabled>A carregar qualificações...</option>
                                        </select>
                                    </div>
                                </div>
                
                                <div class="col-md-6">
                                    <label for="curso" class="form-label text-muted fw-bold small">Curso Relacionado</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-graduation-cap text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" name="curso" id="curso" required>
                                            <option value="" selected disabled>A carregar cursos...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-save me-1"></i> Registar Turma</button>
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
        $(document).ready(function() {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Qualificacao/getQualificacoes.php',
                method: 'GET',
                success: function(resposta) {
                    $('#qualificacao').html(resposta);
                },
                error: function() {
                    $('#qualificacao').html('<option>Erro ao carregar</option>');
                }
            });

            $.ajax({
                url: '../../Controller/Cursos/getCursos.php',
                method: 'GET',
                success: function(resposta) {
                    $('#curso').html(resposta);
                },
                error: function() {
                    $('#curso').html('<option>Erro ao carregar</option>');
                }
            });
        }
    </script>
</body>

</html>