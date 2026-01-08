<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>


    <main>
        <div class="formulario">
            <form action="../../Controller/Estagio/AdicionarRespostaCarta.php" method="post" id="formularioResposta">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="numero" class="form-label">Numero da Carta</label>
                        <select class="form-select" id="numero" aria-label="Default select example" name="numero">
                            <option selected>Selecione uma Opção</option>
                        </select>
                        <span class="error_form" id="numero_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="status" class="form-label">Estado da Resposta</label>
                        <select class="form-select" id="status" aria-label="Default select example" name="status">
                            <option selected>Selecione uma Opção</option>
                            <option value="Pendente">Pedente</option>
                            <option value="Aceito">Aprovado</option>
                            <option value="Recusado">Reprovado</option>
                        </select>
                        <span class="error_form" id="status_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataResposta" class="form-label">Data da Resposta</label>
                        <input type="date" name="dataResposta" class="form-control" id="dataResposta">
                        <span class="error_form" id="dataResposta_error_message"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="contactoResponsavel" class="form-label">Contacto do Responsável</label>
                        <input type="tel" name="contactoResponsavel" class="form-control" id="contactoResponsavel"
                            pattern="^(\+258)?[ -]?[8][2-7][0-9]{7}$" required
                            placeholder="Ex: +258 84xxxxxxx ou 84xxxxxxx">
                        <span class="error_form" id="cPrincipal_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataInicio" class="form-label">Data do Inicio do Estágio</label>
                        <input type="date" name="dataInicio" class="form-control" id="dataInicio">
                        <span class="error_form" id="dataInicio_error_message"></span>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="dataFim" class="form-label">Data do Inicio do Estágio</label>
                        <input type="date" name="dataFim" class="form-control" id="dataFim">
                        <span class="error_form" id="dataFim_error_message"></span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="statusEstagio" class="form-label">Estado do Estágio</label>
                        <select class="form-select" id="statusEstagio" aria-label="Default select example" name="statusEstagio">
                            <option selected>Selecione uma Opção</option>
                            <option value="Nao Concluido">Não Concluído</option>
                            <option value="Concluido">Concluído</option>
                        </select>
                        <span class="error_form" id="statusEstagio_error_message"></span>
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

    <footer>
        <div class="container-footer">
            <p> &copy; <?php echo date("Y"); ?> - TRANSCOM . DIREITOS RESERVADOS . DESIGN & DEVELOPMENT <span>TRANSCOM</span></p>
        </div>
    </footer>


    <!-- Scripts do BootStrap -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script src="/pedro/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
        </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script src="../../Assets/JS/tema.js"></script>
    <script>
        //Selects com valores fornecidos da bd
        $(document).ready(function () {
            carregarDados();
        });

        function carregarDados() {
            $.ajax({
                url: '../../Controller/Estagio/getNumero.php',
                method: 'GET',
                success: function (resposta) {
                    $('#numero').html(resposta);
                },
                error: function () {
                    $('#numero').html('<option>Erro ao carregar</option>');
                }
            });

        }

        //Validation
        $("#formularioResposta").validate({
            rules: {
                numero: {
                    required: true,
                    digits: true
                },
                status: {
                    required: true
                },
                dataResposta: {
                    required: true,
                    date: true
                },
                dataInicio: {
                    required: true,
                    minlength: 2
                },
                dataFim: {
                    required: true,
                    minlength: 2
                },
                contactoResponsavel: {
                    required: true,
                    pattern: /^(\+258)?[ -]?[8][2-7][0-9]{7}$/
                },
                statusEstagio: {
                    required: true
                }
            },
            messages: {
                numero: {
                    required: "Campo obrigatório.",
                    digits: "Apenas números são permitidos."
                },
                status: {
                    required: "Selecione uma qualificação."
                },
                turma: {
                    required: "Selecione uma turma."
                },
                dataResposta: {
                    required: "Informe a data da resposta.",
                    date: "Formato inválido."
                },
                dataInicio: {
                    required: "Informe uma data de Inicio.",
                },
                dataFim: {
                    required: "Informe uma da data de Fim.",
                },
                contactoResponsavel: {
                    required: "Informe um contacto válido.",
                    pattern: "Número inválido. Ex: +258 84xxxxxxx"
                },
                statusEstagio: {
                    required: "Selecione um Status."
                },
            },
            errorClass: "is-invalid",
            validClass: "is-valid",
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            }
        });

    </script>
</body>

</html>