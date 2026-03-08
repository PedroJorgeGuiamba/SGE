<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Cursos/CadastrarCurso.php" method="post">
                <?php if (isset($_GET['erros'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="codigoCurso" class="form-label">Código do Curso</label>
                        <input type="number" name="codigoCurso" class="form-control" id="codigoCurso"
                            placeholder="123456">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="nomeCurso" class="form-label">Nome</label>
                        <input type="text" name="nomeCurso" class="form-control" id="nomeCurso">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="descricaoCurso" class="form-label">Descricao do curso</label>
                        <input type="text" name="descricaoCurso" class="form-control" id="descricaoCurso">
                    </div>
                </div>
                <div  class="row">
                    <div class="form-group col-md-4">
                        <label for="siglaCurso" class="form-label">Sigla do Curso</label>
                        <input type="text" name="siglaCurso" class="form-control" id="siglaCurso">
                    </div>
                
                    <div class="form-group col-md-4">
                        <label for="qualificacao" class="form-label">Codigo da Qualificação</label>
                        <select class="form-select" name="qualificacao" id="qualificacao" aria-label="Default select example" required>
                            <option value="" selected disabled>Open this select menu</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success form-control">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
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