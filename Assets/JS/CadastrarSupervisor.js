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
