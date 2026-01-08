<?php
include '../../Controller/Formando/CadastrarFormando.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main>
        <div class="formulario">
            <form action="../../Controller/Formando/CadastrarFormando.php" method="post">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="codigoformando" class="form-label">Código do Formando</label>
                        <input type="number" name="codigoformando" class="form-control" id="codigoformando" placeholder="123456" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="nomeformando" class="form-label">Nome</label>
                        <input type="text" name="nomeformando" class="form-control" id="nomeformando" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="apelidoformando" class="form-label">Apelido</label>
                        <input type="text" name="apelidoformando" class="form-control" id="apelidoformando" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" name="dataNascimento" class="form-control" id="dataNascimento" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="naturalidade" class="form-label">Naturalidade</label>
                        <input type="text" name="naturalidade" class="form-control" id="naturalidade" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipoDeDocumento" class="form-label">Tipo de Documento</label>
                        <input type="text" name="tipoDeDocumento" class="form-control" id="tipoDeDocumento" placeholder="Ex: BI, Passaporte" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="numeroDeDocumento" class="form-label">Número de Documento</label>
                        <input type="text" name="numeroDeDocumento" class="form-control" id="numeroDeDocumento" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="localEmitido" class="form-label">Local Emitido</label>
                        <input type="text" name="localEmitido" class="form-control" id="localEmitido" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dataEmissao" class="form-label">Data de Emissão</label>
                        <input type="date" name="dataEmissao" class="form-control" id="dataEmissao" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="nuit" class="form-label">NUIT</label>
                        <input type="number" name="nuit" class="form-control" id="nuit" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="number" name="telefone" class="form-control" id="telefone" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-success form-control">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>
    </main>


    <!-- Scripts do BootStrap -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
</body>

</html>