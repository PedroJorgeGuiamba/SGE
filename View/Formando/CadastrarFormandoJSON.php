<?php
include_once __DIR__ . '/../../Helpers/CSRFProtection.php';
?>
<?php require_once __DIR__ . '/../../Includes/header-admin.php' ?>

    <main class="container mb-5" style="margin-top: 40px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 mt-3 pt-4 pb-0 text-center">
                        <h3 class="fw-bold text-primary"><i class="fas fa-user-graduate me-2"></i>Cadastrar Formando</h3>
                        <p class="text-muted small">Registe os dados pessoais e documentais do novo estudante</p>
                    </div>
                    <div class="card-body p-5">
                        <div id="json-aba" class="tab-content">
                        <h3 style="margin-bottom: 15px;">Upload de Arquivo JSON</h3>
                        
                        <!-- Área de upload -->
                        <div id="uploadArea" class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <div class="upload-icon">📁</div>
                            <p>Clique ou arraste um arquivo JSON aqui</p>
                            <small style="color: #666;">Suporta arquivos .json com um único registro ou array de registros</small>
                            <input type="file" id="fileInput" accept=".json" style="display: none;">
                        </div>
                        
                        <!-- Informações do arquivo selecionado -->
                        <div id="fileInfo" class="file-info">
                            <strong>Arquivo selecionado:</strong> <span id="fileName"></span><br>
                            <strong>Tamanho:</strong> <span id="fileSize"></span><br>
                            <strong>Registros encontrados:</strong> <span id="recordCount"></span>
                        </div>
                        
                        <!-- Preview do JSON -->
                        <div id="jsonPreview" style="display: none;">
                            <h4>Preview do JSON:</h4>
                            <pre id="previewContent" style="background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; max-height: 300px;"></pre>
                        </div>
                        
                        <!-- Botões de ação -->
                        <div class="batch-actions" id="batchActions" style="display: none;">
                            <button id="btnUpload" class="btn-secondary" onclick="uploadJSON()">📤 Enviar Todos os Registros</button>
                            <button id="btnClear" class="btn-danger" onclick="limparArquivo()">🗑️ Limpar</button>
                        </div>
                        
                        <!-- Barra de progresso -->
                        <div id="progressBar" class="progress-bar">
                            <div id="progressFill" class="progress-fill" style="width: 0%">0%</div>
                        </div>
                        
                        <!-- Resultados do processamento em lote -->
                        <div id="batchResults" style="display: none; margin-top: 20px;">
                            <h4>Resultados do Processamento:</h4>
                            <div id="resultsList"></div>
                        </div>
                    </div>
                    <div id="alert" class="alert"></div>
                    <div id="loading" class="loading"><div class="spinner"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php require_once __DIR__ . '/../../Includes/footer.php'?>
    <script src="/estagio/Assets/JS/uploadJSON.js"></script>
</body>

</html>