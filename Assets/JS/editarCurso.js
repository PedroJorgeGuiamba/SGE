$(document).ready(function () {
    $("#formEditarCurso").submit(function (e) {
        e.preventDefault();
        console.log("Dados enviados:", $(this).serialize());
        $.ajax({
        url: $(this).attr("action"),
        method: "POST",
        data: $(this).serialize(),
        dataType: "json",
        success: function (response) {
            if (response.success) {
            alert(response.message);
            window.location.href = "/estagio/curso/listar";
            } else {
            alert(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.log("Erro AJAX:", xhr.status, status, error, xhr.responseText);
            alert(
            "Erro ao processar a requisição: " +
                (xhr.responseText || "Verifique o console para mais detalhes."),
            );
        },
        });
    });

    $("#formEditarCurso").validate({
        rules: {
        nome: {
            required: true,
            minlength: 2,
        },
        area: {
            required: true,
            minlength: 2,
        },
        qualificacao: {
            required: true,
        },
        },
        messages: {
        nome: {
            required: "Campo obrigatório.",
            minlength: "O nome deve ter pelo menos 2 caracteres.",
        },
        area: {
            required: "Informe o nome",
            minlength: "A área deve ter pelo menos 2 caracteres.",
        },
        qualificacao: {
            required: "Informe o apelido",
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
        },
    });
});
