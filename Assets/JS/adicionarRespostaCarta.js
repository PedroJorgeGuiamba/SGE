// AJAX call to load data
$(document).ready(function () {
    carregarDados();
});

function carregarDados() {
    $.ajax({
        url: '../../Controller/Estagio/getNumero.php',
        method: 'GET',
        success: function (resposta) {
            $('#numero').html(resposta);
        },
        error: function () {
            $('#numero').html('<option>Erro ao carregar</option>');
        }
    });
}

// Form validation
$("#formularioResposta").validate({
    rules: {
        numero: {
            required: true,
            digits: true
        },
        status: {
            required: true
        },
        dataResposta: {
            required: true,
            date: true
        },
        dataInicio: {
            required: true,
            minlength: 2
        },
        dataFim: {
            required: true,
            minlength: 2
        },
        contactoResponsavel: {
            required: true,
            pattern: /^(\+258)?[ -]?[8][2-7][0-9]{7}$/
        },
        statusEstagio: {
            required: true
        }
    },
    messages: {
        numero: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        status: {
            required: "Selecione uma qualificação."
        },
        dataResposta: {
            required: "Informe a data da resposta.",
            date: "Formato inválido."
        },
        dataInicio: {
            required: "Informe uma data de Inicio.",
        },
        dataFim: {
            required: "Informe uma da data de Fim.",
        },
        contactoResponsavel: {
            required: "Informe um contacto válido.",
            pattern: "Número inválido. Ex: +258 84xxxxxxx"
        },
        statusEstagio: {
            required: "Selecione um Status."
        },
    },
    errorClass: "is-invalid",
    validClass: "is-valid",
    highlight: function (element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function (element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
    },
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    }
});
