<?php
include '../../Controller/Turmas/CadastrarTurma.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Turmas/CadastrarTurma.php" method="post">
                <div class="row">
                
                    <div class="form-group col-md-4">
                        <label for="codigoTurma" class="form-label">Código da Turma</label>
                        <input type="text" name="codigoTurma" class="form-control" id="codigoTurma" placeholder="123">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nomeTurma" class="form-label">Nome da Turma</label>
                        <input type="text" name="nomeTurma" class="form-control" id="nomeTurma" placeholder="TPW3">
                    </div>
                
                    <div class="form-group col-md-4">
                        <label for="qualificacao" class="form-label">Qualificação</label>
                        <select class="form-select" name="qualificacao" id="qualificacao" aria-label="Default select example">
                            <option selected>Open this select menu</option>
                        </select>
                    </div>
                

                    <div class="form-group col-md-4">
                        <label for="curso" class="form-label">Curso</label>
                        <select class="form-select" name="curso" id="curso" aria-label="Default select example">
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