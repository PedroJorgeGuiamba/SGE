document.addEventListener('DOMContentLoaded', function() {
    
    // Verificar se os dados foram carregados
    if (!window.adminData) {
        console.error('Dados não carregados. Verifique se situacaoDeEstagio.data.php foi incluído.');
        return;
    }
    
    const data = window.adminData;
    
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const borderColor = isDark ? '#333333' : '#ffffff';
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: data.user_roles_labels,
            datasets: [{
                data: data.user_roles_data,
                backgroundColor: ['#0dcaf0', '#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: borderColor,
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

    // Bar Chart: Monthly Sessions
    const ctxBar = document.getElementById('barChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: data.months,
            datasets: [{
                label: 'Number of Sessions',
                data: data.monthly_sessions,
                backgroundColor: 'rgba(13, 202, 240, 0.6)',
                borderColor: 'rgba(13, 202, 240, 1)',
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

    // Bar Chart: Formandos per Curso
    const ctxFormandos = document.getElementById('formandosChart').getContext('2d');
    new Chart(ctxFormandos, {
        type: 'bar',
        data: {
            labels: data.formandos_curso_labels,
            datasets: [{
                label: 'Number of Formandos',
                data: data.formandos_curso_data,
                backgroundColor: 'rgba(25, 135, 84, 0.6)',
                borderColor: 'rgba(25, 135, 84, 1)',
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
});
