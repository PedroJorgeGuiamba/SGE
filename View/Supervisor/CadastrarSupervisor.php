<?php
include '../../Controller/Supervisor/CadastrarSupervisor.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Supervisor/CadastrarSupervisor.php" method="post">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="nomeSupervisor" class="form-label">Nome da Supervisor:</label>
                        <input type="text" name="nomeSupervisor" class="form-control" id="nomeSupervisor">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="area" class="form-label">Area:</label>
                        <input type="text" name="area" class="form-control" id="area">
                    </div>
                
                    <div class="form-group col-md-4">
                        <label for="qualificacao" class="form-label">Qualificação</label>
                        <select class="form-select" name="qualificacao" id="qualificacao" aria-label="Default select example">
                            <option value="" selected>Open this select menu</option>
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="user" class="form-label">Utilizador</label>
                        <select class="form-select" name="user" id="user" aria-label="Default select example">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success form-control">Register</button>
                        </div>
                    </div>
                </div>
            </form>
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