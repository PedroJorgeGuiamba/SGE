$(document).ready(function() {
    carregarFormador();
    carregarModulo();
    carregarModuloTurma();
});

function carregarModuloTurma(moduloId = null) {
    $.ajax({
        url: '/estagio/api/modulo-turma',
        method: 'GET',
        data: { termo: moduloId || "" },
        success: function(resposta) {
            $('#modulo_turma').html(resposta);
        },
        error: function() {
            $('#modulo_turma').html('<option>Erro ao carregar</option>');
        }
    });
}
function carregarModulo() {
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
}
function carregarFormador(moduloId = null) {
    $.ajax({
        url: '/estagio/api/formador-modulo',
        method: 'GET',
        data: { termo: moduloId || "" },
        success: function(resposta) {
            $('#formador').html(resposta);
        },
        error: function() {
            $('#formador').html('<option>Erro ao carregar</option>');
        }
    });
}

$('#modulo').on('change', function() {
    carregarModuloTurma($(this).val());
});