document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar se os dados foram carregados
    if (!window.relatorioData) {
        console.error('Dados não carregados. Verifique se situacaoDeEstagio.data.php foi incluído.');
        return;
    }
    
    const data = window.relatorioData;

    const colors = [
        'rgba(54, 162, 235, 0.7)',
        'rgba(255, 99, 132, 0.7)',
        'rgba(75, 192, 192, 0.7)',
        'rgba(255, 206, 86, 0.7)',
        'rgba(153, 102, 255, 0.7)',
        'rgba(201, 203, 207, 0.7)'
    ];
    const borderColors = colors.map(c => c.replace('0.7', '1'));

    new Chart(document.getElementById('cartasChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Cartas de Estágio',
                data: data.cartas_dia_data,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('credenciaisChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Credenciais',
                data: data.credenciais_dia_data,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('cartasQualChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: data.cartas_qual_labels,
            datasets: [{
                data: data.cartas_qual_data,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    new Chart(document.getElementById('credenciaisQualChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: data.credenciais_qual_labels,
            datasets: [{
                data: data.credenciais_qual_data,
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});