document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar se os dados foram carregados
    if (!window.supervisorData) {
        console.error('Dados não carregados. Verifique se situacaoDeEstagio.data.php foi incluído.');
        return;
    }
    
    const data = window.supervisorData;
    const ctxPedidosQualificacao = document.getElementById('pedidosPieChart').getContext('2d');
    const qualifications = data.qualifications;
    const pedidos_data = data.pedidos_per_qual_json;
    const colors = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 206, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(201, 203, 207, 0.6)'
    ];
    const borderColors = colors.map(color => color.replace('0.6', '1'));
    let datasets = qualifications.map((qual, index) => {
        return {
            label: qual,
            data: pedidos_data[qual],
            backgroundColor: colors[index % colors.length],
            borderColor: borderColors[index % colors.length],
            borderWidth: 2
        };
    });
    new Chart(ctxPedidosQualificacao, {
        type: 'bar',
        data: {
            labels: data.months,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });


    const ctxPedidosAno = document.getElementById('pedidosAnoPieChart').getContext('2d');
    const years_list = data.years_list_json;
    const pedidos_year_data = data.pedidos_year_per_qual_json;

    let datasetsAno = qualifications.map((qual, index) => {
        return {
            label: qual,
            data: pedidos_year_data[qual] ?? [],
            backgroundColor: colors[index % colors.length],
            borderColor: borderColors[index % colors.length],
            borderWidth: 2
        };
    });

    new Chart(ctxPedidosAno, {
        type: 'bar',
        data: {
            labels: years_list,
            datasets: datasetsAno
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});