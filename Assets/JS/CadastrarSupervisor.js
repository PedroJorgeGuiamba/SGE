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
        error: function () {
        $("#qualificacao").html("<option>Erro ao carregar</option>");
        },
    });

    $.ajax({
        url: "/estagio/api/users",
        method: "GET",
        success: function (resposta) {
        $("#user").html(resposta);
        },
        error: function () {
        $("#user").html("<option>Erro ao carregar</option>");
        },
    });
}

$("#formularioSupervisor").validate({
    rules: {
        nomeSupervisor: {
            required: true,
            minlength: 2
        },
        area: {
            required: true,
            minlength: 2
        },
        qualificacao: {
            required: true
        },
        user: {
            required: true
        }
    },
    messages: {
        nomeSupervisor: {
            required: "Informe o nome do supervisor.",
            minlength: "O nome deve ter pelo menos 2 digitos."
        },
        area: {
            required: "Informe a área.",
            minlength: "O nome deve ter pelo menos 2 caracteres."
        },
        qualificacao: {
            required: "Informe o qualificação do supervisor."
        },
        user: {
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