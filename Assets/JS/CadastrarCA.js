$(document).ready(function() {
    carregarDados();
});

// Validação do formulário
$("#elemento").validate({
    rules: {
        codigoCurso: {
            required: true,
            digits: true,
            minlength: 5
        },
        nomeCurso: {
            required: true,
            minlength: 2
        },
        descricaoCurso: {
            required: true,
            minlength: 2
        },
        siglaCurso: {
            required: true,
            minlength: 2
        },
        qualificacao: {
            required: true
        }
    },
    messages: {
        codigoCurso: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos.",
            minlength: "O código deve ter pelo menos 5 digitos."
        },
        nomeCurso: {
            required: "Informe o nome do curso.",
            minlength: "O nome deve ter pelo menos 2 caracteres."
        },
        descricaoCurso: {
            required: "Informe o apelido do curso.",
            minlength: "O apelido deve ter pelo menos 2 caracteres."
        },
        siglaCurso: {
            required: "Informe a sigla do curso.",
            minlength: "A sigla deve conter no mínimo 2 caracteres."
        },
        qualificacao: {
            required: "A qualificacao deve ser preenchida."
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
        url: '/estagio/api/tipo-avaliacao',
        method: 'GET',
        success: function(resposta) {
            $('#tipo_avaliacao').html(resposta);
        },
        error: function() {
            $('#tipo_avaliacao').html('<option>Erro ao carregar</option>');
        }
    });
}