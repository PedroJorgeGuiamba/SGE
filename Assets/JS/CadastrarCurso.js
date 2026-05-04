$(document).ready(function() {
    carregarDados();
});

// Validação do formulário
$("#formularioCurso").validate({
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
        url: '/estagio/api/users',
        method: 'GET',
        success: function(resposta) {
            $('#user').html(resposta);
        },
        error: function() {
            $('#user').html('<option>Erro ao carregar</option>');
        }
    });
}