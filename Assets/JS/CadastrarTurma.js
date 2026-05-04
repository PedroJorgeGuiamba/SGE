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

$("#formularioTurma").validate({
    rules: {
        codigoTurma: {
            required: true,
            digits: true,
            minlength: 2
        },
        nomeTurma: {
            required: true,
            minlength: 2
        },
        qualificacao: {
            required: true
        },
        curso: {
            required: true
        }
    },
    messages: {
        codigoTurma: {
            required: "Informe o nome da turma.",
            minlength: "O nome deve ter pelo menos 2 digitos."
        },
        nomeTurma: {
            required: "Informe a área.",
            minlength: "O nome deve ter pelo menos 2 caracteres."
        },
        qualificacao: {
            required: "Informe o qualificação da turma."
        },
        curso: {
            required: "Informe Utilizador."
        }
    },
    errorClass: "is-invalid",
    validClass: "is-valid",
    highlight: function(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
    },
    errorPlacement: function(error, element) {
        error.insertAfter(element);
    }
});