$(document).ready(function () {
    $('#formEditarQualificacao').submit(function (e) {
        e.preventDefault();
        console.log('Dados enviados:', $(this).serialize()); // Log dos dados enviados
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '/estagio/formando/listar';
                } else {
                    alert(response.message);
                }
            },
            error: function (xhr, status, error) {
                console.log('Erro AJAX:', xhr.status, status, error, xhr.responseText); // Log detalhado do erro
                alert('Erro ao processar a requisição: ' + (xhr.responseText || 'Verifique o console para mais detalhes.'));
            }
        });
    });
});

$("#formEditarQualificacao").validate({
    rules: {
        formando: {
            required: true,
            digits: true
        },
        descricao: {
            required: true,
            minlength: 2
        },
        nivel: {
            required: true,
            minlength: 1
        }
    },
    messages: {
        formando: {
            required: "Campo obrigatório.",
            digits: "Apenas números são permitidos."
        },
        descricao: {
            required: "Informe o nome",
            minlength: "A descrição deve ter pelo menos 2 caracteres."
        },
        nivel: {
            required: "Informe o apelido",
            minlength: "O nível deve ter pelo menos 1 caracteres."
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