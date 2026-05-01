let jsonData = null;

function mostrarAlerta(msg, tipo) {
    let alerta = document.getElementById('alert');
    
    if (!alerta) {
        // Criar elemento alert se não existir
        alerta = document.createElement('div');
        alerta.id = 'alert';
        alerta.className = 'alert';
        
        // Inserir após a área de upload
        const uploadArea = document.getElementById('uploadArea');
        if (uploadArea && uploadArea.parentNode) {
            uploadArea.parentNode.insertBefore(alerta, uploadArea.nextSibling);
        } else {
            document.body.appendChild(alerta);
        }
    }

    alerta.className = `alert alert-${tipo}`;
    alerta.innerHTML = msg;
    alerta.style.display = 'block';
    setTimeout(() => alerta.style.display = 'none', 5000);
}

function setLoading(show) {
    document.getElementById('loading').style.display = show ? 'block' : 'none';
}

// Upload de arquivo JSON
document.getElementById('fileInput').addEventListener('change', function(e) {
    processarArquivo(e.target.files[0]);
});

// Drag and drop
const uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    const file = e.dataTransfer.files[0];
    if (file && file.type === 'application/json') {
        processarArquivo(file);
    } else {
        mostrarAlerta('Por favor, envie um arquivo JSON válido', 'error');
    }
});

function processarArquivo(file) {
    if (!file) return;
    
    const reader = new FileReader();
    
    reader.onload = function(event) {
        try {
            const content = event.target.result;
            const parsed = JSON.parse(content);
            
            // Verificar se é array ou objeto único
            let registros = [];
            if (Array.isArray(parsed)) {
                registros = parsed;
            } else {
                registros = [parsed];
            }
            
            console.log(`📝 Total de registros encontrados: ${registros.length}`);
            console.log('📋 Primeiro registro para exemplo:', registros[0]);
            
            // Validar cada registro
            const registrosValidos = [];
            const erros = [];
            
            registros.forEach((registro, index) => {
                // Usando os nomes EXATOS do seu JSON
                const temCodigo = registro.codigo && registro.codigo !== '';
                const temNome = registro.nome && registro.nome !== '';
                const temApelido = registro.apelido && registro.apelido !== '';
                const temNuit = registro.nuit && registro.nuit !== '';
                const temDataNascimento = registro.dataNascimento && registro.dataNascimento !== '';
                const temTipoDeDocumento = registro.tipoDeDocumento && registro.tipoDeDocumento !== ''; // ← Nome correto
                const temNumeroDeDocumento = registro.numeroDeDocumento && registro.numeroDeDocumento !== '';
                const temLocalEmitido = registro.localEmitido && registro.localEmitido !== '';
                const temDataEmissao = registro.dataEmissao && registro.dataEmissao !== '';
                const temEmail = registro.email && registro.email !== '';
                const temTelefone = registro.telefone && registro.telefone !== '';
                
                console.log(`\n🔍 Registro ${index + 1}:`);
                console.log(`   - Código: ${temCodigo ? '✅' : '❌'} (${registro.codigo})`);
                console.log(`   - Nome: ${temNome ? '✅' : '❌'} (${registro.nome})`);
                console.log(`   - Apelido: ${temApelido ? '✅' : '❌'} (${registro.apelido})`);
                console.log(`   - NUIT: ${temNuit ? '✅' : '❌'} (${registro.nuit})`);
                console.log(`   - Data Nascimento: ${temDataNascimento ? '✅' : '❌'} (${registro.dataNascimento})`);
                console.log(`   - Tipo Documento: ${temTipoDeDocumento ? '✅' : '❌'} (${registro.tipoDeDocumento})`);
                console.log(`   - Nº Documento: ${temNumeroDeDocumento ? '✅' : '❌'} (${registro.numeroDeDocumento})`);
                console.log(`   - Local Emitido: ${temLocalEmitido ? '✅' : '❌'} (${registro.localEmitido})`);
                console.log(`   - Data Emissão: ${temDataEmissao ? '✅' : '❌'} (${registro.dataEmissao})`);
                console.log(`   - Email: ${temEmail ? '✅' : '❌'} (${registro.email})`);
                console.log(`   - Telefone: ${temTelefone ? '✅' : '❌'} (${registro.telefone})`);
                
                // Verificar todos os campos obrigatórios
                if (temCodigo && temNome && temApelido && temNuit && temDataNascimento && 
                    temTipoDeDocumento && temNumeroDeDocumento && temLocalEmitido && 
                    temDataEmissao && temEmail && temTelefone) {
                    
                    // Manter os dados exatamente como estão (sem transformar nomes)
                    registrosValidos.push(registro);
                    console.log(`   ✅ Registro ${index + 1} é VÁLIDO!`);
                } else {
                    const camposFaltando = [];
                    if (!temCodigo) camposFaltando.push('codigo');
                    if (!temNome) camposFaltando.push('nome');
                    if (!temApelido) camposFaltando.push('apelido');
                    if (!temNuit) camposFaltando.push('nuit');
                    if (!temDataNascimento) camposFaltando.push('dataNascimento');
                    if (!temTipoDeDocumento) camposFaltando.push('tipoDeDocumento');
                    if (!temNumeroDeDocumento) camposFaltando.push('numeroDeDocumento');
                    if (!temLocalEmitido) camposFaltando.push('localEmitido');
                    if (!temDataEmissao) camposFaltando.push('dataEmissao');
                    if (!temEmail) camposFaltando.push('email');
                    if (!temTelefone) camposFaltando.push('telefone');
                    
                    console.log(`   ❌ Registro ${index + 1} INVÁLIDO - Faltando: ${camposFaltando.join(', ')}`);
                    erros.push(`Registro ${index + 1}: Campos faltando - ${camposFaltando.join(', ')}`);
                }
            });
            
            if (registrosValidos.length === 0) {
                mostrarAlerta('Nenhum registro válido encontrado no arquivo. Verifique o console (F12) para detalhes.', 'error');
                return;
            }
            
            jsonData = registrosValidos;
            
            // Mostrar informações
            const fileInfo = document.getElementById('fileInfo');
            if (fileInfo) {
                fileInfo.style.display = 'block';
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
                document.getElementById('recordCount').textContent = registrosValidos.length;
            }
            
            // Preview
            const jsonPreview = document.getElementById('jsonPreview');
            if (jsonPreview) {
                jsonPreview.style.display = 'block';
                document.getElementById('previewContent').textContent = JSON.stringify(registrosValidos, null, 2);
            }
            
            // Mostrar botões de ação
            const batchActions = document.getElementById('batchActions');
            if (batchActions) {
                batchActions.style.display = 'block';
            }
            
            if (erros.length > 0) {
                mostrarAlerta(`⚠️ ${erros.length} registro(s) ignorado(s). ${registrosValidos.length} registro(s) válido(s) carregado(s).`, 'warning');
            } else {
                mostrarAlerta(`✅ Arquivo carregado com sucesso! ${registrosValidos.length} registro(s) encontrado(s).`, 'success');
            }
            
        } catch (error) {
            console.error('❌ Erro detalhado:', error);
            
            mostrarAlerta('Erro ao processar JSON: ' + error.message, 'error');
        }
    };
    
    reader.readAsText(file);
}

function limparArquivo() {
    document.getElementById('fileInput').value = '';
    document.getElementById('fileInfo').style.display = 'none';
    document.getElementById('jsonPreview').style.display = 'none';
    document.getElementById('batchActions').style.display = 'none';
    document.getElementById('batchResults').style.display = 'none';
    document.getElementById('progressBar').style.display = 'none';
    jsonData = null;
}

async function uploadJSON() {
    if (!jsonData || jsonData.length === 0) {
        mostrarAlerta('Nenhum arquivo carregado', 'error');
        return;
    }
    
    const resultsDiv = document.getElementById('batchResults');
    const resultsList = document.getElementById('resultsList');
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    
    resultsList.innerHTML = '';
    resultsDiv.style.display = 'block';
    progressBar.style.display = 'block';
    
    let sucessos = 0;
    let falhas = 0;
    
    for (let i = 0; i < jsonData.length; i++) {
        const registro = jsonData[i];
        const percent = ((i + 1) / jsonData.length) * 100;
        progressFill.style.width = percent + '%';
        progressFill.textContent = Math.round(percent) + '%';
        
        try {
            const response = await fetch('/estagio/formando/salvar-uploadJSON', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(registro)
            });

            if (!response.ok) {
                falhas++;
                resultsList.innerHTML += `<div style="color:red">❌ Registro ${i+1}: HTTP ${response.status}</div>`;
                continue;
            }
            
            const result = await response.json();
            
            if (result.sucesso) {
                sucessos++;
                resultsList.innerHTML += `<div style="color: green; margin: 5px 0;">✅ Registro ${i+1}: ${registro.nome} ${registro.apelido} - ID: ${result.id}</div>`;
            } else {
                falhas++;
                resultsList.innerHTML += `<div style="color: red; margin: 5px 0;">❌ Registro ${i+1}: ${registro.nome} ${registro.apelido} - Erro: ${result.erro}</div>`;
            }
        } catch (error) {
            falhas++;
            resultsList.innerHTML += `<div style="color: red; margin: 5px 0;">❌ Registro ${i+1}: ${registro.nome} ${registro.apelido} - Erro: ${error.message}</div>`;
        }

        await new Promise(resolve => setTimeout(resolve, 100));
    }
    
    progressFill.style.backgroundColor = '#48bb78';
    progressFill.textContent = 'Concluído!';
    
    resultsList.innerHTML += `<div style="margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 4px;">
        <strong>Resumo:</strong> ✅ ${sucessos} sucessos | ❌ ${falhas} falhas
    </div>`;
    
    mostrarAlerta(`Processamento concluído! ${sucessos} registros importados com sucesso.`, 'success');
    
    // Limpar após 3 segundos
    setTimeout(() => {
        limparArquivo();
        if (document.getElementById('lista-aba').classList.contains('active')) {
            carregarLista();
        }
    }, 3000);
}

async function enviarDados(dados) {
    setLoading(true);
    try {
        const response = await fetch('/estagio/formando/salvar-uploadJSON', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(dados)
        });
        const result = await response.json();
        
        if (result.sucesso) {
            mostrarAlerta(`✅ ${result.mensagem} (ID: ${result.id})`, 'success');
            document.getElementById('formFormando').reset();
            if (document.getElementById('lista-aba').classList.contains('active')) carregarLista();
        } else {
            let msg = result.erro;
            if (result.detalhes) msg += '<br>' + result.detalhes.join('<br>');
            mostrarAlerta(`❌ ${msg}`, 'error');
        }
    } catch (error) {
        mostrarAlerta(`❌ Erro: ${error.message}`, 'error');
    } finally {
        setLoading(false);
    }
}