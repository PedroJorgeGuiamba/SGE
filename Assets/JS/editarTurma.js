$(document).ready(function () {
    $('#formEditarSupervisor').submit(function (e) {
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
                    window.location.href = '/estagio/turma/listar';
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

$("#formularioEditarTurma").validate({
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