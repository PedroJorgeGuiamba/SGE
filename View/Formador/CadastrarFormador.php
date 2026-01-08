<?php
include '../../Controller/Cursos/CadastrarCurso.php';
?>

<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Formador/CadastrarFormador.php" method="post">
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

    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>


    <!-- Scripts do BootStrap -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
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