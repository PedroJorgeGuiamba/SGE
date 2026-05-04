// situacaoDeEstagio.js - JavaScript puro sem inline

document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar se os dados foram carregados
    if (!window.situacaoEstagioData) {
        console.error('Dados não carregados. Verifique se situacaoDeEstagio.data.php foi incluído.');
        return;
    }
    
    const data = window.situacaoEstagioData;
    
    // 1. Gráfico de Barras - Pedidos Mensais
    const ctxPedidos = document.getElementById('pedidosBarChart')?.getContext('2d');
    if (ctxPedidos) {
        new Chart(ctxPedidos, {
            type: 'bar',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Número de Pedidos',
                    data: data.pedidos_monthly,
                    backgroundColor: 'rgba(13, 202, 240, 0.6)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
    
    // 2. Gráfico de Barras Empilhadas - Pedidos por Qualificação
    const ctxPedidosQualificacao = document.getElementById('pedidosPieChart')?.getContext('2d');
    if (ctxPedidosQualificacao && data.qualifications) {
        const colors = ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)', 'rgba(153, 102, 255, 0.6)', 'rgba(255, 159, 64, 0.6)', 'rgba(201, 203, 207, 0.6)'];
        const borderColors = colors.map(c => c.replace('0.6', '1'));
        
        const datasets = data.qualifications.map((qual, index) => ({
            label: qual,
            data: data.pedidos_per_qual[qual],
            backgroundColor: colors[index % colors.length],
            borderColor: borderColors[index % colors.length],
            borderWidth: 2
        }));
        
        new Chart(ctxPedidosQualificacao, {
            type: 'bar',
            data: { labels: data.months, datasets: datasets },
            options: {
                responsive: true,
                scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
    
    // 3. Gráfico de Barras - Empresas por Qualificação
    const ctxCredEmpresa = document.getElementById('credenciaisEmpresaChart')?.getContext('2d');
    if (ctxCredEmpresa && data.credenciais_por_empresa) {
        const qualColors = ['#4e79a7', '#f28e2b', '#e15759', '#76b7b2', '#59a14f', '#edc948', '#b07aa1', '#ff9da7', '#9c755f', '#bab0ac'];
        
        const datasets = Object.entries(data.credenciais_por_empresa).map(([qual, empresaMap], i) => ({
            label: qual,
            data: data.empresas_labels.map(emp => empresaMap[emp] || 0),
            backgroundColor: qualColors[i % qualColors.length],
            borderRadius: 4
        }));
        
        new Chart(ctxCredEmpresa, {
            type: 'bar',
            data: { labels: data.empresas_labels, datasets: datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { callbacks: { label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y} credencial(ais)` } }
                },
                scales: {
                    x: { ticks: { maxRotation: 35, minRotation: 20, font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
    
    // 4. Gráfico de Pizza - Status Resposta
    const ctxResposta = document.getElementById('statusRespostaPie')?.getContext('2d');
    if (ctxResposta && data.status_resposta_labels) {
        new Chart(ctxResposta, {
            type: 'pie',
            data: {
                labels: data.status_resposta_labels,
                datasets: [{
                    data: data.status_resposta_data,
                    backgroundColor: ['#0dcaf0', '#198754', '#ffc107'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
    
    // 5. Gráfico de Pizza - Status Estágio
    const ctxEstagio = document.getElementById('statusEstagioPie')?.getContext('2d');
    if (ctxEstagio && data.status_estagio_labels) {
        new Chart(ctxEstagio, {
            type: 'pie',
            data: {
                labels: data.status_estagio_labels,
                datasets: [{
                    data: data.status_estagio_data,
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
    
    // 6. Gráfico de Pizza - Estágios por Qualificação
    const ctxEstagioQualificacao = document.getElementById('statusEstagioQualificacaoPie')?.getContext('2d');
    if (ctxEstagioQualificacao && data.status_estagio_qualificacao_labels) {
        new Chart(ctxEstagioQualificacao, {
            type: 'pie',
            data: {
                labels: data.status_estagio_qualificacao_labels,
                datasets: [{
                    data: data.status_estagio_qualificacao_data,
                    backgroundColor: ['#198754', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
});