document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Cria um gráfico a partir de um canvas que tem o atributo data-chart
     * @param {HTMLCanvasElement} canvas - O elemento canvas
     */
    function createChartFromDataAttribute(canvas) {
        if (!canvas || !canvas.dataset.chart) return null;
        
        try {
            const chartConfig = JSON.parse(canvas.dataset.chart);
            const ctx = canvas.getContext('2d');
            const chartType = chartConfig.type || determineChartType(chartConfig);
            
            // Configurações comuns para todos os gráficos
            const options = {
                responsive: true,
                maintainAspectRatio: false,
                ...getChartOptions(chartType, chartConfig)
            };
            
            return new Chart(ctx, {
                type: chartType,
                data: {
                    labels: chartConfig.labels,
                    datasets: chartConfig.datasets
                },
                options: options
            });
        } catch (error) {
            console.error('Erro ao criar gráfico:', error, canvas.id);
            return null;
        }
    }
    
    function determineChartType(chartConfig) {
        if (chartConfig.type) return chartConfig.type;
        
        if (chartConfig.stacked === true) return 'bar';

        const dataset = chartConfig.datasets?.[0];
        if (dataset && (dataset.backgroundColor?.length > 1 || chartConfig.labels?.length <= 6)) {
            return 'pie';
        }
        
        return 'bar';
    }
    
    function getChartOptions(type, chartConfig) {
        const baseOptions = {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 11 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            let value = context.raw !== undefined ? context.raw : context.parsed?.y;
                            
                            if (type === 'pie') {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                            
                            if (label) {
                                return `${label}: ${value}`;
                            }
                            return `${value}`;
                        }
                    }
                }
            }
        };
        
        if (type === 'bar') {
            return {
                ...baseOptions,
                scales: {
                    x: {
                        stacked: chartConfig.stacked === true,
                        ticks: {
                            maxRotation: 35,
                            minRotation: 20,
                            font: { size: 10 }
                        }
                    },
                    y: {
                        stacked: chartConfig.stacked === true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            };
        }
        
        if (type === 'pie') {
            return {
                ...baseOptions,
                plugins: {
                    ...baseOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 } }
                    }
                }
            };
        }
        
        return baseOptions;
    }

    const pedidosBarChart = document.getElementById('pedidosBarChart');
    if (pedidosBarChart) {
        createChartFromDataAttribute(pedidosBarChart);
    }
    
    const pedidosPieChart = document.getElementById('pedidosPieChart');
    if (pedidosPieChart) {
        createChartFromDataAttribute(pedidosPieChart);
    }
    
    const credenciaisEmpresaChart = document.getElementById('credenciaisEmpresaChart');
    if (credenciaisEmpresaChart) {
        createChartFromDataAttribute(credenciaisEmpresaChart);
    }
    
    const statusRespostaPie = document.getElementById('statusRespostaPie');
    if (statusRespostaPie) {
        createChartFromDataAttribute(statusRespostaPie);
    }

    const statusEstagioPie = document.getElementById('statusEstagioPie');
    if (statusEstagioPie) {
        createChartFromDataAttribute(statusEstagioPie);
    }
    
    const statusEstagioQualificacaoPie = document.getElementById('statusEstagioQualificacaoPie');
    if (statusEstagioQualificacaoPie) {
        createChartFromDataAttribute(statusEstagioQualificacaoPie);
    }
    
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Forçar re-renderização dos gráficos
            Chart.helpers.each(Chart.instances, function(instance) {
                if (instance && instance.resize) {
                    instance.resize();
                }
            });
        }, 250);
    });
});