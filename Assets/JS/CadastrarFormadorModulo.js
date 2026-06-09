$(document).ready(function() {
    carregarDados();
});

function carregarDados() {
    $.ajax({
        url: '/estagio/api/modulo',
        method: 'GET',
        success: function(resposta) {
            $('#modulo').html(resposta);
        },
        error: function() {
            $('#modulo').html('<option>Erro ao carregar</option>');
        }
    });

    $.ajax({
        url: '/estagio/api/formador',
        method: 'GET',
        success: function(resposta) {
            $('#formador').html(resposta);
        },
        error: function() {
            $('#formador').html('<option>Erro ao carregar</option>');
        }
    });
}