
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Confirmação adicional para exclusão
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a[title="Excluir"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const sucursalName = this.closest('tr').querySelector('td:first-child').textContent;
            if (!confirm(`Tem certeza que deseja excluir a sucursal "${sucursalName.trim()}"?`)) {
                e.preventDefault();
            }
        });
    });
});