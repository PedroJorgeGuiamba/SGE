<?php
include '../../Controller/Supervisor/CadastrarSupervisor.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main class="container mb-5" style="margin-top: 140px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-user-shield me-2"></i>Cadastrar Supervisor</h3>
                        <p class="text-muted small">Associe um utilizador a um cargo de supervisão e sua área de actuação</p>
                    </div>
                    <div class="card-body p-5">
                        <form action="../../Controller/Supervisor/CadastrarSupervisor.php" method="post">
                            <?php if (isset($_GET['erros'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                    <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="nomeSupervisor" class="form-label text-muted fw-bold small">Nome do Supervisor</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user-tie text-muted"></i></span>
                                        <input type="text" name="nomeSupervisor" class="form-control border-start-0 ps-0" id="nomeSupervisor" placeholder="Ex: Carlos Mendes" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="area" class="form-label text-muted fw-bold small">Área de Supervisão</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-briefcase text-muted"></i></span>
                                        <input type="text" name="area" class="form-control border-start-0 ps-0" id="area" placeholder="Ex: Coordenação Pedagógica" required>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="qualificacao" class="form-label text-muted fw-bold small">Qualificação Relacionada</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-certificate text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" name="qualificacao" id="qualificacao" required>
                                            <option value="" selected disabled>A carregar qualificações...</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="user" class="form-label text-muted fw-bold small">Conta de Utilizador Associada</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-users-cog text-muted"></i></span>
                                        <select class="form-select border-start-0 ps-0" name="user" id="user" required>
                                            <option value="" selected disabled>A carregar utilizadores...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                
                            <div class="row mt-5">
                                <div class="col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary shadow-sm px-5 py-2 fw-bold text-white"><i class="fas fa-user-shield me-1"></i> Registar Supervisor</button>
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
                url: '../../Controller/Usuarios/getUsers.php',
                method: 'GET',
                success: function(resposta) {
                    $('#user').html(resposta);
                },
                error: function() {
                    $('#user').html('<option>Erro ao carregar</option>');
                }
            });
        }
    </script>
</body>

</html>