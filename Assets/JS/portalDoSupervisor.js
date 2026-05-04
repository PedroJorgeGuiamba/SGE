// Dashboard charts for Supervisor
const ctxPedidosQualificacao = document.getElementById('pedidosPieChart')?.getContext('2d');
if (ctxPedidosQualificacao) {
    const qualifications = {};
    const pedidos_data = {};
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
    // Chart initialization happens in PHP
}
