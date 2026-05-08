$(document).ready(function () {
    carregarQualificacoes();
});

function carregarQualificacoes() {
    $.ajax({
        url: "/estagio/api/qualificacao",
        method: "GET",
        success: function (resposta) {
            $("#qualificacao").html(resposta);
        },
        error: function (xhr, status, error) {
            console.error("Erro ao carregar qualificações:", status, error);
            $("#qualificacao").html("<option>Erro ao carregar qualificações</option>");
        }
    });
}

