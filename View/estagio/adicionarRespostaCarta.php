<?php require_once __DIR__ . '/../../Includes/header-estagio-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Estagio/AdicionarRespostaCarta.php" method="post" id="formularioResposta">
                <?php if (isset($_GET['erros'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['erros']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
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
</body>
</html>