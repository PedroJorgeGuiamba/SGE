$(document).ready(function () {
    carregarDados();
});

function carregarDados() {
    $.ajax({
        url: "/estagio/api/qualificacao",
        method: "GET",
        success: function (resposta) {
            $("#qualificacao").html(resposta);
        },
        error: function (xhr, status, error) {
            console.error("Erro ao carregar qualificações:", status, error);
            $("#qualificacao").html(
                "<option>Erro ao carregar qualificações</option>",
            );
        },
    });

    $.ajax({
        url: "/estagio/api/turmas",
        method: "GET",
        success: function (resposta) {
            $("#turma").html(resposta);
        },
        error: function (xhr, status, error) {
            console.error("Erro ao carregar turmas:", status, error);
            $("#turma").html("<option>Erro ao carregar turmas</option>");
        },
    });
}


// Validação do formulário
$("#formularioAvaliacao").validate({
    rules: {
        codigoFormando: {
            required: true,
            digits: true
        },
        qualificacao: {
            required: true,
            digits: true
        },
        turma: {
            required: true,
            digits: true
        },
        empresa: {
            required: true,
            minlength: 2
        },
        anoTurma: {
            required: true,
            digits: true
        },
        comentario: {
            minlength: 2
        }
    },
    messages: {
        codigoFormando: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        qualificacao: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        turma: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        empresa: {
            required: "Campo obrigatório.",
            minlength: "O nome da empresa deve ter pelo menos 2 caracteres."
        },
        anoTurma: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        comentario: {
            minlength: "O comentário deve ter pelo menos 2 caracteres."
        }
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
