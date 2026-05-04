$(document).ready(function() {
    carregarDados();
});

function carregarDados() {
    $.ajax({
        url: '/estagio/api/qualificacao',
        method: 'GET',
        success: function(resposta) {
            $('#qualificacao').html(resposta);
        },
        error: function() {
            $('#qualificacao').html('<option>Erro ao carregar</option>');
        }
    });

    $.ajax({
        url: '/estagio/api/cursos',
        method: 'GET',
        success: function(resposta) {
            $('#curso').html(resposta);
        },
        error: function() {
            $('#curso').html('<option>Erro ao carregar</option>');
        }
    });
}