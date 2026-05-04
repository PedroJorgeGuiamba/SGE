$(document).ready(function () {
    $('#formEditarResposta').submit(function (e) {
        e.preventDefault();
        console.log('Dados enviados:', $(this).serialize());
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = 'respostaCarta.php';
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText);
                alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
            }
        });
    });
});
