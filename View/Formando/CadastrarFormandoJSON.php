<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>
<link rel="stylesheet" href="/estagio/Assets/CSS/uploadJSON.css">

<main class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Cadastrar Formando</h3>
                    <p class="text-muted small">Registe os dados pessoais e documentais do novo estudante</p>
                </div>
                <div class="card-body p-5">
                    <div id="json-aba" class="tab-content">
                        <h3>Upload de Arquivo JSON</h3>

                        <!-- Área de upload -->
                        <div id="uploadArea" class="upload-area">
                            <div class="upload-icon">📁</div>
                            <p>Clique ou arraste um arquivo JSON aqui</p>
                            <small >Suporta arquivos .json com um único registro ou array de registros</small>
                            <input type="file" id="fileInput" accept=".json" >
                        </div>

                        <!-- Informações do arquivo selecionado -->
                        <div id="fileInfo" class="file-info">
                            <strong>Arquivo selecionado:</strong> <span id="fileName"></span><br>
                            <strong>Tamanho:</strong> <span id="fileSize"></span><br>
                            <strong>Registros encontrados:</strong> <span id="recordCount"></span>
                        </div>

                        <!-- Preview do JSON -->
                        <div id="jsonPreview">
                            <h4>Preview do JSON:</h4>
                            <pre id="previewContent"></pre>
                        </div>

                        <!-- Botões de ação -->
                        <div class="batch-actions" id="batchActions">
                            <button id="btnUpload" class="btn-cancel">📤 Enviar Todos os Registros</button>
                            <button id="btnClear" class="btn-danger">🗑️ Limpar</button>
                        </div>

                        <!-- Barra de progresso -->
                        <div id="progressBar" class="progress-bar">
                            <div id="progressFill" class="progress-fill">0%</div>
                        </div>

                        <!-- Resultados do processamento em lote -->
                        <div id="batchResults">
                            <h4>Resultados do Processamento:</h4>
                            <div id="resultsList"></div>
                        </div>
                    </div>
                    <div id="alert" class="alert"></div>
                    <div id="loading" class="loading">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<?php require_once __DIR__ . '/../../Includes/footer.php' ?>
<script src="/estagio/Assets/JS/uploadJSON.js"></script>
</body>

</html>